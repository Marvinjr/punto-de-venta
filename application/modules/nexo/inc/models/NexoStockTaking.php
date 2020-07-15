<?php
/**
 * @since 3.12.13
 * @author NexoPOS Solutions
 */
class NexoStockTaking extends Tendoo_Module
{
    /**
     * refresh stock taking
     * @param int stock id
     * @return void
     */
    public function refresh_stock_taking( $id )
    {
        $items  =   get_instance()->db->where( 'REF_SHIPPING', $id )
            ->get( store_prefix() . 'nexo_articles_stock_flow' )
            ->result_array();

        $itemsNegative     =   array_filter( $items, function( $item ){
            return in_array( $item[ 'TYPE' ], [ 'defective', 'adjustment', 'sale', 'transfert_out' ]);
        });

        $itemsPositive     =   array_filter( $items, function( $item ){
            return in_array( $item[ 'TYPE' ], [ 'supply', 'transfert_in' ]);
        });

        /**
         * positive operations
         */
        $positiveTotal          =   0;
        $positiveTotalItems     =   0;
        foreach( $itemsPositive as $item ) {
            $positiveTotalItems     +=  floatval( $item[ 'QUANTITE' ] );
            $positiveTotal          +=  ( floatval( $item[ 'QUANTITE' ] ) * floatval( $item[ 'UNIT_PRICE' ] ) );
        }

        /**
         * negative operations
         */
        $negativeTotal          =   0;
        $negativeTotalItems     =   0;
        foreach( $itemsNegative as $item ) {
            $negativeTotalItems     +=  floatval( $item[ 'QUANTITE' ] );
            $negativeTotal          +=  ( floatval( $item[ 'QUANTITE' ] ) * floatval( $item[ 'UNIT_PRICE' ] ) );
        }

        /**
         * once ready, let's update the stock taking
         */
        get_instance()->db->where( 'ID', $id )
            ->update( store_prefix() . 'nexo_arrivages', [
                'VALUE' =>  $positiveTotal - $negativeTotal,
                'ITEMS' =>  $positiveTotalItems - $negativeTotalItems
            ]);

        return [
            'status'    =>  'success',
            'message'   =>  __( 'L\'approvisionnement a été correctement rafraichi', 'nexo' ),
            'result'    =>  [
                'outcome'               =>  $positiveTotal,
                'incoming_items'        =>  $positiveTotalItems,
                'income'                =>  $negativeTotal,
                'outgoing_items'        =>  $negativeTotalItems
            ]
        ];
    }

    public function getByProvider( $provider_id )
    {
        return $this->db->where( 'REF_PROVIDER', $provider_id )
            ->get( store_prefix() . 'nexo_arrivages' )
            ->result_array();
    }

    /**
     * compute the total of 
     * each items included on a delivery
     * @param int shipping id
     * @return array
     */
    public function countItemsTotal( $shipping_id, $excluded_id = null )
    {
        $items       =   $this->db
            ->where( 'REF_SHIPPING', $shipping_id )
            ->get( store_prefix() . 'nexo_articles_stock_flow' )
            ->result_array();

        $total_amount       =   0;
        $total_quantity     =   0;
        $current_item       =   [];
        foreach( $items as $item ) {
            if( $item[ 'ID' ] != $excluded_id ) {
                if( $item[ 'UNIT_PRICE' ] != '0' && $item[ 'TOTAL_PRICE' ] != '0' ) {
                    $total              =   floatval( $item[ 'UNIT_PRICE' ]) * floatval( $item[ 'QUANTITE' ] );
                    $total_amount       +=  $total;
                    $total_quantity     +=  floatval( $item[ 'QUANTITE' ]);
                }
            } else {
                $current_item   =   $item;
            }
        }

        return compact( 'total_amount', 'total_quantity', 'current_item' );
    }

    public function get( $shipping_id )
    {
        return $this->db->where( 'ID', $shipping_id )
            ->get( store_prefix() . 'nexo_arrivages' )
            ->result_array();
    }

    public function save_supply( $items, $provider_id, $supply_id, $delivery_name = '' )
    {
        if( is_array( $items ) ) {
            $delivery_cost                  =   [];
            $provider_amount_due            =   [];
            $items                          =   $items;

            /**
             * If the delivery name is provided. it's used in priority to create
             * a new delivery
             */
            if ( ! empty( $delivery_name ) ) {
                $this->db->insert( store_prefix() . 'nexo_arrivages', [
                    'REF_PROVIDER'      =>  $provider_id,
                    'AUTHOR'            =>  User::id(),
                    'DATE_CREATION'     =>  date_now(),
                    'TITRE'             =>  $delivery_name 
                ]);
    
                $shipping_id        =   $this->db->insert_id();
            } else {
                $shipping_id        =   $supply_id; 
            }
            
            foreach( $items as $item ) {
                // get current item stock
                $saved_item       =   $this->db->where( 'CODEBAR', $item[ 'item_barcode' ] )
                    ->get( store_prefix() . 'nexo_articles' )
                    ->result_array();
                    
                // required
                if( @$item[ 'item_barcode' ] != null && @$item[ 'item_qte' ] != null ) {

                    // Now increase the current stock of the item
                    if( in_array( $item[ 'type' ], [ 'defective', 'adjustment' ] ) ) {
                        $remaining_qte      =   intval( $saved_item[0][ 'QUANTITE_RESTANTE' ] ) - intval( $item[ 'item_qte' ] );
                    } else if( in_array( $item[ 'type' ], [ 'supply' ] )) { // 'usable' is only used by the refund feature
                        $remaining_qte      =   intval( $saved_item[0][ 'QUANTITE_RESTANTE' ] ) + intval( $item[ 'item_qte' ] );
                    }

                    if( $remaining_qte < 0 ) {
                        break;
                    }
                    
                    $this->db->insert( store_prefix() . 'nexo_articles_stock_flow', [
                        'REF_ARTICLE_BARCODE'   =>  $item[ 'item_barcode' ],
                        'BEFORE_QUANTITE'       =>  $saved_item[0][ 'QUANTITE_RESTANTE' ],
                        'AFTER_QUANTITE'        =>  $remaining_qte,
                        'QUANTITE'              =>  $item[ 'item_qte' ],
                        'DATE_CREATION'         =>  date_now(),
                        'AUTHOR'                =>  User::id(),
                        'TYPE'                  =>  $item[ 'type' ], // defective, usable, supply, adjustment
                        'UNIT_PRICE'            =>  ( float ) $item[ 'unit_price' ],
                        'TOTAL_PRICE'           =>  ( float ) $item[ 'unit_price' ] * ( float ) $item[ 'item_qte' ],
                        // 'DESCRIPTION'           =>  $this->post( 'description' ) == null ? '' : $this->post( 'description' ),
                        'REF_PROVIDER'          =>  $item[ 'ref_provider' ],
                        'REF_SHIPPING'          =>  $shipping_id,
                        'PROVIDER_TYPE'         =>  'suppliers'
                    ]);

                    $updatable_columns          =   $this->events->apply_filters( 'items_columns_updatable_after_supply', [
                        $item, [
                            'QUANTITE_RESTANTE'     =>  $remaining_qte
                        ]
                    ]);

                    // use the updatable columns
                    $this->db->where( 'CODEBAR', $item[ 'item_barcode' ] )
                        ->update( store_prefix() . 'nexo_articles', $updatable_columns[1] );

                    // Calculating the delivery Cost
                    if( @$delivery_cost[ $shipping_id ] == null ) {
                        $delivery_cost[ $shipping_id ]       =   [
                            'cost'          =>  0,
                            'items'         =>  0,
                            'ref_provider'  =>  $item[ 'ref_provider' ]
                        ];
                    }

                    $item_cost          =   floatval( $item[ 'unit_price'] ) * floatval( $item[ 'item_qte' ] ) ;

                    $delivery_cost[ $shipping_id ][ 'cost' ]        +=   $item_cost;
                    $delivery_cost[ $shipping_id ][ 'items' ]       += floatval( $item[ 'item_qte' ] );

                    // update average price
                    $supplies       =   $this->db->where( 'REF_ARTICLE_BARCODE', $item[ 'item_barcode' ] )
                        ->get( store_prefix() . 'nexo_articles_stock_flow' )
                        ->result_array();
                    
                    $totalPurchase          =   0;
                    
                    foreach( $supplies as $supply ) {
                        $totalPurchase      +=  floatval( $item[ 'unit_price' ] );
                    }
                    
                    $averagePurchase        =   $totalPurchase / count( $supplies );
                    
                    $this->db->where( 'CODEBAR', $item[ 'item_barcode' ] )->update( store_prefix() . 'nexo_articles', [
                        'QUANTITE_RESTANTE'     =>  $remaining_qte,
                        'PRIX_DACHAT'           =>  $averagePurchase
                    ]);

                    // Save item cost to the supplier
                    if( store_option( 'enable_providers_account', 'no' ) == 'yes' ) {
                        if( @$provider_amount_due[ $item[ 'ref_provider' ] ] == null ) {
                            $provider_amount_due[ $item[ 'ref_provider' ] ]     =   [
                                'cost'          =>  0,
                                'supply_id'     =>  $shipping_id
                            ];
                        }

                        $provider_amount_due[ $item[ 'ref_provider' ] ][ 'cost' ]  +=    $item_cost;
                    }

                    $this->events->do_action( 'nexo_supply_item', $item );
                }
            }

            if( store_option( 'enable_providers_account', 'no' ) == 'yes' ) {
                // update amount due
                foreach( $provider_amount_due as $provider => $data ) {
                    $currentProvider    =   $this->db->where( 'ID', $provider )
                    ->get( store_prefix() . 'nexo_fournisseurs' )
                    ->result_array();
    
                    // loop amount
                    $currentAmountDue       =   floatval( $currentProvider[0][ 'PAYABLE' ] );
                    $transactionAmount      =   $data[ 'cost' ];
                    $currentAmountDue       +=  $transactionAmount;
    
                    // Update customer payable.
                    $this->db->where( 'ID', $provider )->update( store_prefix() . 'nexo_fournisseurs', [
                        'PAYABLE'   =>  $currentAmountDue
                    ]);

                    // add it as an history
                    $this->db->insert( store_prefix() . 'nexo_fournisseurs_history', [
                        'REF_PROVIDER'      =>  $provider,
                        'REF_SUPPLY'        =>  $data[ 'supply_id' ], // @since 3.10.0
                        'TYPE'              =>  'stock_purchase',
                        'BEFORE_AMOUNT'     =>  $currentProvider[0][ 'PAYABLE' ],
                        'AMOUNT'            =>  $transactionAmount,
                        'AFTER_AMOUNT'      =>  $currentAmountDue,
                        'DATE_CREATION'     =>  date_now(),
                        'DATE_MOD'          =>  date_now(),
                        'AUTHOR'            =>  User::id()
                    ]);
                }
            }

            // Update new values
            foreach( $delivery_cost as $delivery_id => $data ) {
                $this->db->where( store_prefix() . 'nexo_arrivages.ID', $delivery_id )->update( store_prefix() . 'nexo_arrivages', [
                    'ITEMS'         =>  $data[ 'items' ],
                    'VALUE'         =>  $data[ 'cost' ]
                ]);
            }

            $this->events->do_action( 'nexo_after_stock_supplied', $shipping_id );
            
            return [
                'shipping_id'   =>  $shipping_id,
                'status'        =>  'success',
                'message'       =>  __( 'La livraison a été enregistrée.', 'nexo' )
            ];
        }

        return [
            'status'    =>  'failed',
            'message'   =>  __( 'Impossible d\'enregistrer une livraison sans produits.', 'nexo' )
        ];
    }
}