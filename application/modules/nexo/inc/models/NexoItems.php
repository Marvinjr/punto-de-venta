<?php
class NexoItems extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get( $where = [] )
    {
        $this->load->module_model( 'nexo', 'NexoCategories', 'category_model' );
        if ( $where ) {
            foreach( $where as $key => $value ) {
                $this->db->where( $key, $value );
            }
        }
        
        $products   =   $this->db
            ->get( store_prefix() . 'nexo_articles' )
            ->result_array();

        foreach( $products as &$product ) {
            $url                            =   base_url() . preg_replace('#/+#','/', 'public/upload/' . store_prefix() . '/items-images/' . $product[ 'APERCU' ] );
            $product[ 'category' ]          =   $this->category_model->getSingle( $product[ 'REF_CATEGORIE' ]);
            $product[ 'preview_url' ]       =   $url;
        }

        return $products;
    }

    /**
     * get item using either a barcode
     * or a sku
     * @param string index
     * @return array or empty array
     */
    public function getUsingBarcodeAndSku( $item )
    {
        $item   =   $this->db->where( 'CODEBAR', $item )
            ->or_where( 'SKU', $item )
            ->get( store_prefix() . 'nexo_articles' )
            ->result_array();
        
        return $item ? $item[0] : [];
    }

    /**
     * get item using either a barcode
     * or a sku
     * @param int index
     * @return array or empty array
     */
    public function getUsingID( $id )
    {
        $item   =   $this->db->where( 'ID', $id )
            ->get( store_prefix() . 'nexo_articles' )
            ->result_array();
        
        return $item ? $item[0] : [];
    }

    /**
     * get item using  barcode
     * @param string barcode
     * @return mixed boolean or array
     */
    public function getUsingBarcode( $barcode )
    {
        $item   =   $this->_get( $barcode, 'CODEBAR' );
        return $item ? $item[0] : false;
    }

    private function _get( $ref, $index = 'ID' ) 
    {
        $item   =   $this->db
            ->where( $index, $ref )
            ->get( store_prefix() . 'nexo_articles' );
        
        return $item->result_array();
    }

    /**
     * Get Product using SKU
     * @param string SKU
     * @return array
     */
    public function getUsingSKU( $sku ) 
    {
        $item   =   $this->_get( $sku, 'SKU' );
        return $item ? $item[0] : false;
    }

    /**
     * Get stock flow using barcode and supply id
     * @param int id
     * @return stock flow
     */
    public function getStockFlow( $barcode )
    {
        $stockFlow  =   $this->db->where( 'ID', $barcode )
            ->get( store_prefix() . 'nexo_articles_stock_flow' )
            ->result_array();
        
        return $stockFlow ? $stockFlow[0] : false;
    }

    /**
     * Delete product using sku
     * @param string sku
     * @return json
     */
    public function deleteProductUsingSKU( $sku )
    {
        $item   =   $this->db->where( 'SKU', $sku )
            ->get( store_prefix() . 'nexo_articles' )
            ->result_array();

        if ( $item ) {
            $item_id   =   $item[0][ 'ID' ];
            $this->db->where( 'REF_ARTICLE', $item_id )->delete( store_prefix() . 'nexo_articles_meta' );
            $this->db->where( 'REF_ARTICLE', $item_id )->delete( store_prefix() . 'nexo_articles_variations' );
            $this->db->where( 'REF_ARTICLE_BARCODE', $item[0][ 'CODEBAR' ] )->delete( store_prefix() . 'nexo_articles_stock_flow' );
            $this->db->where( 'ID', $item_id )->delete( store_prefix() . 'nexo_articles' );
            return true;
        }
        return false;
    }

    /**
     * Update remaining quantity
     * @param int product id
     * @param int quantity
     */
    public function updateRemainingQuantity( $id, $remaining )
    {
        $this->db->where( 'ID', $id )->update( store_prefix() . 'nexo_articles', [
            'QUANTITE_RESTANTE'     =>  $remaining
        ]);
    }

    /**
     * proceed a stock flow record
     * on the product stock flow
     * @param string action
     * @param array config
     * @return void
     */
    public function stockFlow( $namespace, $config )
    {
        if( ! array_in( array_keys( $config ), [ 'barcode', 'price', 'order_code', 'quantity' ] ) ) {
            throw \Exception( __( 'La requête n\'inclut pas des paramètres requis sur la configuration proposée', 'nexo' ) );
        }

        /**
         * @var string barcode
         * @var float price
         * @var string order_code
         * @var float quantity
         */
        extract( $config );

        // Add history for this item on stock flow
        $total_price    =   floatval( $price ) * $quantity;
        $stock_flow     =   [
            'REF_ARTICLE_BARCODE'       =>  $barcode,
            'QUANTITE'                  =>  $quantity,
            'UNIT_PRICE'                =>  $price,
            'TOTAL_PRICE'               =>  $total_price,
            'REF_COMMAND_CODE'          =>  $order_code,
            'AUTHOR'                    =>  User::id(),
            'DATE_CREATION'             =>  date_now(),
            'TYPE'                      =>  $namespace
        ];

        $item     =   $this->getUsingBarcode( $barcode );

        // if item is a physical item, than we can consider using before and after quantity
        if( @$item[ 'TYPE' ] === '1' && @$item[ 'STOCK_ENABLED' ] === '1' ) {
            $stock_flow[ 'BEFORE_QUANTITE' ]    =   $item[ 'QUANTITE_RESTANTE' ];
            $stock_flow[ 'AFTER_QUANTITE' ]     =   floatval( $item[ 'QUANTITE_RESTANTE' ] ) - floatval( $quantity );
        } else {
            $stock_flow[ 'BEFORE_QUANTITE' ]    =   $item[ 'QUANTITE_RESTANTE' ];
            $stock_flow[ 'AFTER_QUANTITE' ]     =   $item[ 'QUANTITE_RESTANTE' ];
        }
        
        $this->db->insert( store_prefix() . 'nexo_articles_stock_flow', $stock_flow );

        /**
         * updating included item stock
         */
        if ( in_array( $namespace, [ 'sale' ] ) ) {
            $this->db->where( 'CODEBAR', $barcode )
                ->set( 'QUANTITE_RESTANTE', 'QUANTITE_RESTANTE-' . $quantity, false )
                ->set( 'QUANTITE_VENDU', 'QUANTITE_VENDU+' . $quantity, false )
                ->update( store_prefix() . 'nexo_articles' );
        }

        $this->events->do_action( 'nexo_stock_flow_updated', [
            'item'          =>      $item,
            'type'          =>      $type,
            'quantity'      =>      $quantity,
            'unit_price'    =>      $price,
            'total_price'   =>      $total_price,
            'before'        =>      $stock_flow[ 'BEFORE_QUANTITE' ],
            'after'         =>      $stock_flow[ 'AFTER_QUANTITE' ],
        ]);
    }

    public function holdQuantity( $item_id, $quantity, $previous = null )
    {
        if ( $previous === null ) {
            $item   =   $this->getUsingID( $item_id );
            $previous   =   $item[ 'HOLD_QUANTITY' ];
        }

        $this->db->where( 'ID', $item_id )
            ->update( store_prefix() . 'nexo_articles', [
                'HOLD_QUANTITY'     =>      floatval( $quantity )   +   floatval( $previous)
            ]);
    }

    public function releaseQuantity( $barcode, $quantity, $previous = null )
    {
        if ( $previous === null ) {
            $item       =   $this->getUsingBarcode( $barcode );
            $previous   =   $item[ 'HOLD_QUANTITY' ];
        }

        if ( floatval( $previous ) - floatval( $quantity ) >= 0 ) {
            $this->db->where( 'CODEBAR', $barcode )
                ->update( store_prefix() . 'nexo_articles', [
                    'HOLD_QUANTITY'     =>      floatval( $previous ) - floatval( $quantity )
                ]);
        }
    }

    public function getHavingCategory( $cat_id )
    {
        return $this->db->where( 'REF_CATEGORIE', $cat_id )
            ->get( store_prefix() . 'nexo_articles' )
            ->result_array();
    }

    public function createItem( $details )
    {
        $item_details               =   [
            'DESIGN'                =>  $details[ 'name' ],
            'REF_CATEGORIE'         =>  $details[ 'category_id' ],
            'SKU'                   =>  $details[ 'sku' ],
            'PRIX_DE_VENTE'         =>  $details[ 'sale_price' ],
            'PRIX_DE_VENTE_TTC'     =>  $details[ 'sale_price_with_tax' ],
            'PRIX_DE_VENTE_BRUT'    =>  $details[ 'gross_sale_price' ],
            'CODEBAR'               =>  $details[ 'barcode' ],
            'BARCODE_TYPE'          =>  $details[ 'barcode_type' ],
            'TAX_TYPE'              =>  $details[ 'tax_type' ],
            'REF_TAXE'              =>  $details[ 'tax_id' ],
            'TYPE'                  =>  $details[ 'item_type' ], // for grouped ite]s
            'STATUS'                =>  $details[ 'item_status' ],
            'STOCK_ENABLED'         =>  $details[ 'item_stock_status' ],
            'DATE_CREATION'         =>  date_now()   
        ];

        $this->db->insert( store_prefix() . 'nexo_articles', $items_details );

        return [
            'status'    =>  'success',
            'message'   =>  __( 'The produit a correctement été créer', 'nexo' ),
            'data'      =>  array_merge([
                'ID'    =>  $this->db->insert_id()
            ], $details )
        ];
    }

    public function updateItem( $id, $details )
    {
        $item   =   $this->db->get( $id );

        if ( ! empty( $item ) ) {

            $item_details               =   [
                'DESIGN'                =>  $details[ 'name' ],
                'REF_CATEGORIE'         =>  $details[ 'category_id' ],
                'SKU'                   =>  $details[ 'sku' ],
                'PRIX_DE_VENTE'         =>  $details[ 'sale_price' ],
                'PRIX_DE_VENTE_TTC'     =>  $details[ 'sale_price_with_tax' ],
                'PRIX_DE_VENTE_BRUT'    =>  $details[ 'gross_sale_price' ],
                'CODEBAR'               =>  $details[ 'barcode' ],
                'BARCODE_TYPE'          =>  $details[ 'barcode_type' ],
                'TAX_TYPE'              =>  $details[ 'tax_type' ],
                'REF_TAXE'              =>  $details[ 'tax_id' ],
                'TYPE'                  =>  $details[ 'item_type' ], // for grouped ite]s
                'STATUS'                =>  $details[ 'item_status' ],
                'STOCK_ENABLED'         =>  $details[ 'item_stock_status' ],
                'DATE_MOD'              =>  date_now()   
            ];
    
            $this->db->where( 'ID', $id )
                ->update( store_prefix() . 'nexo_articles', $items_details );
    
            return [
                'status'    =>  'success',
                'message'   =>  __( 'The produit a correctement été mis à jour', 'nexo' ),
                'data'      =>  array_merge([
                    'ID'    =>  $this->db->insert_id()
                ], $details )
            ];
        }

        return [
            'status'    =>  'failed',
            'message'   =>  __( 'Impossible de retrouver l\'article que vous recherchez.', 'nexo' )
        ];
    }
}