<?php
class NexoProducts extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get Product from a specific table
     * @param string/int id, sku, barocde
     * @param string column
     * @param int/null 
     * @return array/boolean
     */
    public function get( $id = null, $column = 'ID', $store = null )
    {
        $table_name   =   $store !== null ? 
            'store_' . $store . '_nexo_articles' : 
            store_prefix() . 'nexo_articles';

        if( $id !== null ) {
            $this->db->where( $column, $id );
        }

        $product    =   $this->db   
            ->get( $table_name )
            ->result_array();
        
        return empty( $product ) ? ($id === null ? [] : false ) : ( $id === null ? $product : $product[0] );
    }

    /**
     * Add product stock flow
     * @param int order id
     * @param array flow config
     * @return array
     */
    public function addStockFlow( $item_id, $data ) 
    {
        $item   =   $this->get( $item_id );

        if ( intval( $item[ 'STOCK_ENABLED' ] ) === 1 ) {

            switch( $data[ 'TYPE' ] ) {
                case 'usable': 
                    $after_quantite     =   floatval( $item[ 'QUANTITE_RESTANTE' ] ) + floatval( $data[ 'QUANTITE' ] );
                break;
                default:
                    $after_quantite     =   floatval( $item[ 'QUANTITE_RESTANTE' ] ) - floatval( $data[ 'QUANTITE' ] );;
                break;
            }
    
            $data       =   array_merge( $data, [
                'BEFORE_QUANTITE'       =>  $item[ 'QUANTITE_RESTANTE' ],
                'AFTER_QUANTITE'        =>  $after_quantite,
                'REF_ARTICLE_BARCODE'   =>  $item[ 'CODEBAR' ]
            ]);

            /**
             * update remaining quantity
             */
            $this->db->where( 'ID', $item[ 'ID' ] )
                ->update( store_prefix() . 'nexo_articles', [
                    'QUANTITE_RESTANTE'     =>  $after_quantite,
                    'DATE_MOD'              =>  date_now(),
                ]);
        } else {
            /**
             * no changes should be made on the history.
             * so we just reference a sale has been made
             * nothing more.
             */
            $data       =   array_merge( $data, [
                'BEFORE_QUANTITE'       =>  $item[ 'QUANTITE_RESTANTE' ],
                'AFTER_QUANTITE'        =>  $item[ 'QUANTITE_RESTANTE' ],
                'REF_ARTICLE_BARCODE'   =>  $item[ 'CODEBAR' ]
            ]);
        }

        /**
         * update stock flow
         */
        $this->db->where( 'REF_ARTICLE_BARCODE', $item[ 'CODEBAR' ] )
            ->insert( store_prefix() . 'nexo_articles_stock_flow', $data );
            
        return [
            'status'    =>  'success',
            'message'   =>  __( 'Le stock a été mis à jour', 'nexo' )
        ];
    }

    /**
     * get categories products
     * @param int category id
     * @return array
     */
    public function getCategoriesProducts( $category_id )
    {
        return $this->db->where( 'REF_CATEGORIE', $category_id )
            ->order_by( 'ORDER', 'ASC' )
            ->get( store_prefix() . 'nexo_articles' )
            ->result_array();
    }

    /**
     * refresh a product sale price (net and all tax included)
     * @param int product id
     * @return array
     */
    public function refreshSalePrice( $id )
    {
        $this->load->module_model( 'nexo', 'NexoTaxesModel', 'tax_model' );
        $product        =   $this->get( $id );
        
        if ( ! empty( $product ) ) {

            /**
             * the default value so that
             * the price won't remain empty
             */
            $product[ 'PRIX_DE_VENTE_TTC' ]     =   $product[ 'PRIX_DE_VENTE' ];
            $product[ 'PRIX_DE_VENTE_BRUT' ]    =   $product[ 'PRIX_DE_VENTE' ];
            $tax                                =   $this->tax_model->getTax( $product[ 'REF_TAXE' ] );

            if ( ! empty( $tax ) ) {
                $taxValue   =   ( floatval( $tax[0][ 'RATE' ] ) * floatval( $product[ 'PRIX_DE_VENTE' ] ) ) / 100;

                if ( $product[ 'TAX_TYPE' ] === 'inclusive' ) {
                    $product[ 'PRIX_DE_VENTE_TTC' ]         =   $product[ 'PRIX_DE_VENTE' ];
                    $product[ 'PRIX_DE_VENTE_BRUT' ]        =   floatval( $product[ 'PRIX_DE_VENTE' ] ) - $taxValue;
                } else if ( $product[ 'TAX_TYPE' ] === 'exclusive' ) {
                    $product[ 'PRIX_DE_VENTE_TTC' ]         =   $product[ 'PRIX_DE_VENTE' ] + $taxValue;
                    $product[ 'PRIX_DE_VENTE_BRUT' ]        =   floatval( $product[ 'PRIX_DE_VENTE' ] );
                }
            }

            $this->updateProduct( $id, [
                'PRIX_DE_VENTE_TTC'         =>      $product[ 'PRIX_DE_VENTE_TTC' ],
                'PRIX_DE_VENTE_BRUT'        =>      $product[ 'PRIX_DE_VENTE_BRUT' ],
            ]);

            return [
                'status'    =>  'success',
                'message'   =>  __( 'Les prix ont été mis à jour.', 'nexo' )
            ];
        }

        return [
            'status'    =>  'failed',
            'message'   =>  __( 'Impossible de retrouver le produit.', 'nexo' )
        ];
    }

    public function updateProduct( $product_id, $data )
    {
        $this->db->where( 'ID', $product_id )
            ->update( store_prefix() . 'nexo_articles', $data );

        return [
            'status'    =>  'success',
            'message'   =>  __( 'Le produit a été mis à jour.', 'nexo' )
        ];
    }

    /**
     * Helps to create a product
     * using the provided array
     * @param array product data
     * @return array operation response
     */
    public function createProduct( $data )
    {
        $this->db->insert( store_prefix() . 'nexo_articles', $data );
        
        return [
            'status'    =>  'success',
            'message'   =>  __( 'Le produit a été crée.', 'nexo' )
        ];
    }
}