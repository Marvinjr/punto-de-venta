<?php
class ApiNexoItems extends Tendoo_Api
{
    /**
     * Search items
     * @param void
     * @return json
     */
    public function physicals_and_digitals()
    {
        $this->db->or_like( 'CODEBAR', $this->post( 'search' ) );
        $this->db->or_like( 'DESIGN', $this->post( 'search' ) );
        $this->db->or_like( 'SKU', $this->post( 'search' ) );
        $query  =   $this->db->get( store_prefix() . 'nexo_articles' );
        return $this->response( $query->result_array() );
    }

    /**
     * Create Grouped Items
     * @incomplete
     * @param void
     * @return json
     */
    public function post_grouped()
    {
        $form       =   $this->post( 'form' );
        $fieldsErrors   =   [];
        foreach([ 'barcode', 'sku', 'tax_type', 'barcode_type', 'stock_enabled', 'status', 'sale_price', 'category_id' ] as $field ) {
            if ( @$form[ $field ] == null ) {
                $fieldsErrors[]     =   $field;
            }
        }

        if ( $fieldsErrors ) {
            return $this->response([
                'status'    =>  'failed',
                'message'   =>  __( 'Le formulaire contient une ou plusieurs erreurs', 'nexo' ),
                'fields'    =>  $fieldsErrors
            ], 403 );
        }

        // search if the barcode and sku is already used
        $this->db->or_where( 'CODEBAR', $form[ 'barcode' ]);
        $this->db->or_where( 'SKU', $form[ 'sku' ]);
        $search     =   $this->db->get( store_prefix() . 'nexo_articles' )->result();

        if ( $search ) {
            return $this->response([
                'status'    =>  'failed',
                'message'   =>  __( 'Le code barre est déjà en cours d\'utilisation', 'nexo' )
            ], 403 );
        }
        
        $tax        =   $this->db->where( 'ID', @$form[ 'tax_id' ])
        ->get( store_prefix() . 'nexo_taxes' )
        ->result_array();

        /**
         * if a tax is found
         */
        if ( $tax ) {
            if ( $form[ 'tax_type' ] == 'exclusive' ) {
                if ( $tax[0][ 'TYPE' ] == 'percentage' ) {
                    $percent            =   (floatval( $tax[0][ 'RATE' ] ) * floatval( $form[ 'sale_price' ])) / 100;
                    $sale_price         =   $form[ 'sale_price' ];
                    $sale_price_ttc     =   floatval( $form[ 'sale_price' ] ) + $percent;
                } else {
                    $flat               =   floatval( $tax[0][ 'FLAT' ]);
                    $sale_price         =   $form[ 'sale_price' ];
                    $sale_price_ttc     =   floatval( $form[ 'sale_price' ] ) + $flat;
                }
            } else {
                if ( $tax[0][ 'TYPE' ] == 'percentage' ) {
                    $percent            =   (floatval( $tax[0][ 'RATE' ] ) * floatval( $form[ 'sale_price' ])) / 100;
                    $sale_price         =   $form[ 'sale_price' ];
                    $sale_price_ttc     =   floatval( $form[ 'sale_price' ] ) - $percent;
                } else {
                    $flat            =   floatval( $tax[0][ 'FLAT' ] );
                    $sale_price         =   $form[ 'sale_price' ];
                    $sale_price_ttc     =   floatval( $form[ 'sale_price' ] ) - $flat;
                }
            }
        } else {
            $sale_price         =   $form[ 'sale_price' ];
            $sale_price_ttc     =   $form[ 'sale_price' ];
        }

        $item_details               =   [
            'DESIGN'                =>  $this->post( 'item_name' ),
            'REF_CATEGORIE'         =>  $form[ 'category_id' ],
            'SKU'                   =>  $form[ 'sku' ],
            'PRIX_DE_VENTE'         =>  $sale_price,
            'PRIX_DE_VENTE_TTC'     =>  $sale_price_ttc,
            'CODEBAR'               =>  $form[ 'barcode' ],
            'BARCODE_TYPE'          =>  $form[ 'barcode_type' ],
            'TAX_TYPE'              =>  @$form[ 'tax_type' ],
            'REF_TAXE'              =>  @$form[ 'tax_id' ] ? $form[ 'tax_id' ] : 0,
            'TYPE'                  =>  3, // for grouped items
            'STATUS'                =>  @$form[ 'status' ] == 'on_sale' ? 1 : 2,
            'STOCK_ENABLED'         =>  @$form[ 'stock_enabled' ]    == 'enable' ? 1 : 2  ,
            'APERCU'                =>  @$form[ 'apercu' ] ? @$form[ 'apercu' ] : '',
            'DATE_CREATION'         =>  date_now()   
        ];

        $this->db->insert( store_prefix() . 'nexo_articles', $item_details );
        $item_id  =  $this->db->insert_id();

        // create items     
        $this->db->insert( store_prefix() . 'nexo_articles_meta', [
            'KEY'                   =>  'included_items',
            'VALUE'                 =>  json_encode( $this->post( 'items' ) ),
            'REF_ARTICLE'           =>  $item_id,
            'DATE_CREATION'         =>  date_now()
        ]);

        /**
         * Hook before updating stock
         */
        get_instance()->events->do_action( 'create_grouped_products', $item_details );


        return $this->response([
            'status'    =>  'success',
            'message'   =>  __( 'Le produit a été crée.', 'nexo' )
        ]);
    }

    /**
     * Create Grouped Items
     * @incomplete
     * @param void
     * @return json
     */
    public function put_grouped( $id )
    {
        $form       =   $this->post( 'form' );

        // search if the barcode and sku is already used
        $this->db->or_where( 'CODEBAR', $form[ 'barcode' ]);
        $this->db->or_where( 'SKU', $form[ 'sku' ]);
        $search     =   $this->db->get( store_prefix() . 'nexo_articles' )->result_array();

        if ( $search ) {
            if ( $search[0][ 'ID' ] != $id ) {
                return $this->response([
                    'status'    =>  'failed',
                    'message'   =>  __( 'Le code barre est déjà en cours d\'utilisation', 'nexo' )
                ], 403 );
            }
        }
        
        $tax        =   $this->db->where( 'ID', $form[ 'tax_id' ])
        ->get( store_prefix() . 'nexo_taxes' )
        ->result_array();

        if ( $tax ) {
            if ( $form[ 'tax_type' ] == 'exclusive' ) {
                $percent            =   (floatval( $tax[0][ 'RATE' ] ) * floatval( $form[ 'sale_price' ])) / 100;
                $sale_price         =   $form[ 'sale_price' ];
                $sale_price_ttc     =   floatval( $form[ 'sale_price' ] ) + $percent;
                $sale_price_gross   =   floatval( $form[ 'sale_price' ] );
            } else {
                $percent            =   (floatval( $tax[0][ 'RATE' ] ) * floatval( $form[ 'sale_price' ])) / 100;
                $sale_price         =   $form[ 'sale_price' ];
                $sale_price_ttc     =   floatval( $form[ 'sale_price' ] );
                $sale_price_gross   =   floatval( $form[ 'sale_price' ] ) - $percent;
            }
        } else {
            $sale_price         =   $form[ 'sale_price' ];
            $sale_price_ttc     =   $form[ 'sale_price' ];
        }

        $item_details               =   [
            'DESIGN'                =>  $this->post( 'item_name' ),
            'REF_CATEGORIE'         =>  $form[ 'category_id' ],
            'SKU'                   =>  $form[ 'sku' ],
            'PRIX_DE_VENTE'         =>  $sale_price,
            'PRIX_DE_VENTE_TTC'     =>  $sale_price_ttc,
            'PRIX_DE_VENTE_BRUT'    =>  $sale_price_gross,
            'CODEBAR'               =>  $form[ 'barcode' ],
            'BARCODE_TYPE'          =>  $form[ 'barcode_type' ],
            'TAX_TYPE'              =>  $form[ 'tax_type' ],
            'REF_TAXE'              =>  $form[ 'tax_id' ],
            'TYPE'                  =>  3, // for grouped items
            'STATUS'                =>  @$form[ 'status' ] == 'on_sale' ? 1 : 2,
            'STOCK_ENABLED'         =>  @$form[ 'stock_enabled' ] == 'enable' ? 1 : 2,
            // 'APERCU'                =>  @$form[ 'apercu' ],
            'DATE_MOD'              =>  date_now()   
        ];

        if ( @$form[ 'apercu' ] ) {
            $item_details[ 'APERCU' ]   =   @$form[ 'apercu' ];
        }

        $this->db->where( 'ID', $id )->update( store_prefix() . 'nexo_articles', $item_details );

        // Update meta   
        $this->db->where( 'REF_ARTICLE', $id )
        ->where( 'KEY', 'included_items' )
        ->update( store_prefix() . 'nexo_articles_meta', [
            'KEY'                   =>  'included_items',
            'VALUE'                 =>  json_encode( $this->post( 'items' ) ),
            'DATE_MOD'               =>  date_now()
        ]);

        /**
         * Hook before updating stock
         */
        get_instance()->events->do_action( 'edit_grouped_products', $item_details );


        return $this->response([
            'status'    =>  'success',
            'message'   =>  __( 'Le produit a été mis à jour.', 'nexo' )
        ]);
    }

    /**
     * Create Supply
     * @param void
     * @return json response
     */
    public function createSupply()
    {
        $data   =   [];
        $data[ 'TITRE' ]    =   $this->post( 'title' );
        $data[ 'AUTHOR' ]   =   User::id();
        $data[ 'DATE_CREATION' ]    =   date_now();

        $this->db->insert( store_prefix() . 'nexo_arrivages', $data );

        /**
         * Hook before updating stock
         */
        get_instance()->events->do_action( 'create_stock_taking', $data );


        return $this->response([ 
            'status'    => 'success',
            'message'   => __( 'L\'approvisionnement a été correctement crée.', 'nexo' )
        ]);
    }

    public function productsDeliveries( $id )
    {
        $this->load->module_model( 'nexo', 'NexoItems', 'item_model' );
        $product    =   $this->item_model->getUsingID( $id );

        if ( $product ) {
            $deliveries     =   $this->db->select( 
                store_prefix() . 'nexo_arrivages.TITRE as TITRE,'
                . store_prefix() . 'nexo_arrivages.ID as ID'
            )
            ->from( store_prefix() . 'nexo_arrivages' )
            ->join( store_prefix() . 'nexo_articles_stock_flow', store_prefix() . 'nexo_articles_stock_flow.REF_SHIPPING =' . store_prefix() . 'nexo_arrivages.ID' )
            ->where( store_prefix() . 'nexo_articles_stock_flow.REF_ARTICLE_BARCODE', $product[ 'CODEBAR' ] )
            ->group_by( store_prefix() . 'nexo_arrivages.ID' )
            ->get()
            ->result_array();

            return $this->response( $deliveries );
        }

        return $this->response([
            'status'    =>  'failed',
            'message'   =>  __( 'Impossible de trouver le produit demandé', 'nexo' )
        ]);
    }

    public function getItems()
    {
        $this->load->module_model( 'nexo', 'NexoItems', 'item_model' );

        return $this->response( 
            $this->item_model->get()
        );
    }

    public function createItem()
    {
        $this->load->module_model( 'nexo', 'NexoItems', 'item_model' );

        return $this->response( 
            $this->item_model->createItem( $this->post() )
        );
    }

    public function updateItem( $id )
    {
        $this->load->module_model( 'nexo', 'NexoItems', 'item_model' );

        return $this->response( 
            $this->item_model->updateItem( $id, $this->post() )
        );
    }
}