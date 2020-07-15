<?php

use Carbon\Carbon;
/**
 * @todo : provide route to {
 *      crud products
 *      crud orders
 * }
 */
class ApiNexoSystem extends Tendoo_Api
{
    /**
     * Get Modules
     * @param void
     * @return json
     */
    public function details()
    {
        return $this->response( Modules::get() );
    }

    /**
     * get products
     * @return json
     */
    public function products()
    {
        $this->load->module_model( 'nexo', 'NexoItems' );
        return $this->response(
            $this->NexoItems->get()
        );
    }

    /**
     * get customers
     * @return json
     */
    public function customers()
    {
        $this->load->module_model( 'nexo', 'NexoCustomersModel' );
        return $this->response(
            $this->NexoCustomersModel->get()
        );
    }

    /**
     * get system orders
     * @return json
     */
    public function orders()
    {
        $this->load->module_model( 'nexo', 'Nexo_Orders_Model' );
        return $this->response(
            $this->Nexo_Orders_Model->get()
        );
    }

    /**
     * show system options
     * @return json
     */
    public function options()
    {
        global $Options;
        return $this->response( $Options );
    }

    /**
     * Set options 
     * @return json
     */
    public function setOptions()
    {
        $options    =   $this->post( 'options' );
        if ( $options ) {
            foreach( $options as $key => $value ) {
                set_option( $key, $value );
            }
        }

        return $this->response([
            'status'    =>  'success',
            'message'   =>  __( 'Les réglages ont été sauvegardées', 'nexo' )
        ]);
    }

    /**
     * Post product coming from WooCommerce
     * if a product has various categories, only the first is used. 
     * NexoPOS only support a single category
     * @return void
     */
    public function syncDownSingleProduct()
    {
        return $this->__syncDownSingleProduct( 
            $this->post( 'product' ),
            $this->post( 'categories' )
        );
    }

    /**
     * Sync Delete NexoPOS Product
     * @param string sku
     * @return json
     */
    public function syncDownDeleteSingleProduct()
    {
        $sku        =   $this->input->get( 'sku' );
        $product    =   $this->db->where( 'SKU', $sku )
            ->get( store_prefix() . 'nexo_articles' )
            ->result_array();
        
        if ( $product ) {
            $this->load->module_model( 'nexo', 'NexoItems' );
            $this->NexoItems->deleteProductUsingSKU( $sku );
            return $this->response([
                'status'    =>  'success',
                'message'   =>  sprintf( __( 'Le produit avec l\'UGS %s a été supprimé.', 'nexo' ), $sku )
            ]);
        }
        
        return $this->response([
            'status'    =>  'failed',
            'message'   =>  sprintf( __( 'Impossible de localiser le produit avec l\'UGS %s.', 'nexo' ), $sku )
        ], 404 );
    }

    /**
     * Create Single product
     * @param array products
     * @param array categories
     * @return json
     */
    private function __syncDownSingleProduct( $product, $categories )
    {
        get_instance()->load->model( 'Nexo_Products', 'productModel' );
        $productModel   =   get_instance()->productModel;

        /**
         * let's check first if the category exists
         * otherwise, let's create a category and assign the item
         * to it
         */
        $firstCategory  =   $categories[0];

        $category   =   $this->db->where( 'NOM', $firstCategory[ 'name' ])
        ->get( store_prefix() . 'nexo_categories' )
        ->result_array();

        $category_id    =   @$category[0][ 'ID' ];

        /**
         * if the category doesn't exist,
         * we should create that then.
         */
        if ( empty( $category ) ) {
            $this->db->insert( store_prefix() . 'nexo_categories', [
                'NOM'           =>  $firstCategory[ 'name' ],
                'DESCRIPTION'   =>  $firstCategory[ 'description' ]
            ]);

            $category_id  =   $this->db->insert_id();
        } 

        $this->__treatProduct( $product, [ 'ID' =>  $category_id ]);

        return $this->response([
            'status'    =>  'success',
            'message'   =>  __( 'Le produit a été crée', 'nexo' )
        ]);
    }

    /**
     * Treat product as it's send
     * @return void
     */
    private function __treatProduct( $product, $cat ) 
    {
        $status         =   [
            'error'     =>  [],
            'succcess'  =>  []
        ];

        /**
         * If a product has a different sku than the old sku, 
         * that means the sku has been modified. In order to not loose the link
         * the reference for the old item will be the old sku.
         */
        $sku    =   $product[ 'sku' ];

        if ( ! empty( $product[ 'old_sku' ] ) && $product[ 'sku' ] !== $product[ 'old_sku' ] ) {
            $sku    =   $product[ 'old_sku' ];
        }

        /**
         * We should probably check if a product with a similar sku exists
         * if it's provided
         */
        $checkProduct    =   $this->db->where( 'SKU', $sku )
            ->get( store_prefix() . 'nexo_articles' )
            ->result_array();

        if ( $checkProduct ) {

            $product[ 'quantity' ]      =   @$product[ 'quantity' ] ? $product[ 'quantity' ] : 0;

            $data   =   [
                'DESIGN'                =>  $product[ 'name' ],
                'QUANTITE_RESTANTE'     =>  $product[ 'quantity' ],
                'REF_CATEGORIE'         =>  $cat[ 'ID' ], // first cat
                'SKU'                   =>  $product[ 'sku' ],
                'PRIX_DE_VENTE'         =>  $product[ 'sale_price' ],
                'PRIX_DE_VENTE_TTC'     =>  $product[ 'sale_price' ],
                'PRIX_DE_VENTE_BRUT'    =>  $product[ 'sale_price' ],
                'PRIX_PROMOTIONEL'      =>  $product[ 'discount_price' ],
                'STOCK_ENABLED'         =>  $product[ 'stock_management' ],
                'DATE_MOD'              =>  date_now(),
                'AUTHOR'                =>  User::id(),
                'STATUS'                =>  1, // on sale
                'TYPE'                  =>  @$product[ 'quantity' ] ? 1 : 2
            ];

            if ( @$product[ 'discount_starts' ] && @$product[ 'discount_ends' ] ) {
                $data[ 'SPECIAL_PRICE_START_DATE' ]     =  $product[ 'discount_starts' ]; 
                $data[ 'SPECIAL_PRICE_END_DATE' ]       =  $product[ 'discount_ends' ]; 
            }

            $this->db->where( 'SKU', $sku )
                ->update( store_prefix() . 'nexo_articles', $data );

            /**
             * let's check if the quantity has been changed
             * if it's negative we should remove the quantity
             * if it's positive we should increase the quantity
             */
            $beforeQuantity =   0;
            $stockFlow      =   $this->db->where( 'REF_ARTICLE_BARCODE', $checkProduct[0][ 'CODEBAR' ])
                ->order_by( 'ID', 'DESC' )
                ->get( store_prefix() . 'nexo_articles_stock_flow' )
                ->result_array();

            /**
             * The before quantity here is the after quantity
             * of the last stock flow
             */
            if ( $stockFlow ) {
                $beforeQuantity     =   floatval( $stockFlow[0][ 'AFTER_QUANTITE' ] );
            }

            $newQuantity    =   floatval( $product[ 'quantity' ] ) - floatval( $checkProduct[0][ 'QUANTITE_RESTANTE' ] );

            if ( $newQuantity > 0 ) {
                $this->db->insert( store_prefix() . 'nexo_articles_stock_flow', [
                    'REF_ARTICLE_BARCODE'   =>  $checkProduct[0][ 'CODEBAR' ],
                    'BEFORE_QUANTITE'       =>  $beforeQuantity,
                    'QUANTITE'              =>  abs( $newQuantity ),
                    'AFTER_QUANTITE'        =>  $beforeQuantity + abs( $newQuantity ),
                    'DATE_CREATION'         =>  date_now(),
                    'AUTHOR'                =>  User::id(),
                    'UNIT_PRICE'            =>  $product[ 'sale_price' ],
                    'TYPE'                  =>  'supply',
                    'REF_SHIPPING'          =>  1,
                    'REF_PROVIDER'          =>  1,
                    'TOTAL_PRICE'           =>  floatval( $product[ 'sale_price' ] ) * $newQuantity,
                    'DESCRIPTION'           =>  __( 'Modification du stock effectuée depuis WooCommerce', 'nexo' )
                ]);
            } else {
                $this->db->insert( store_prefix() . 'nexo_articles_stock_flow', [
                    'REF_ARTICLE_BARCODE'   =>  $checkProduct[0][ 'CODEBAR' ],
                    'BEFORE_QUANTITE'       =>  $beforeQuantity,
                    'QUANTITE'              =>  abs( $newQuantity ),
                    'AFTER_QUANTITE'        =>  $beforeQuantity - abs( $newQuantity ),
                    'DATE_CREATION'         =>  date_now(),
                    'AUTHOR'                =>  User::id(),
                    'UNIT_PRICE'            =>  $product[ 'sale_price' ],
                    'TYPE'                  =>  'adjustment',
                    'REF_SHIPPING'          =>  1,
                    'REF_PROVIDER'          =>  1,
                    'TOTAL_PRICE'           =>  floatval( $product[ 'sale_price' ] ) * $newQuantity,
                    'DESCRIPTION'           =>  __( 'Modification du stock effectuée depuis WooCommerce', 'nexo' )
                ]);
            }

            $status[ 'success' ][]    =   $product;

        } else {
            $this->load->model( 'Nexo_Products' );

            $codebar    =   $this->Nexo_Products->generate_barcode();
            $this->Nexo_Products->create_codebar( $codebar, 'ean8' );

            $data   =   [
                'DESIGN'                        =>  $product[ 'name' ],
                'QUANTITE_RESTANTE'             =>  @$product[ 'quantity' ] ? $product[ 'quantity' ] : 0,
                'REF_CATEGORIE'                 =>  $product[ 'category_ids' ][0], // first cat
                'SKU'                           =>  empty( @$product[ 'sku' ] ) ? $codebar : @$product[ 'sku' ],
                'PRIX_DE_VENTE'                 =>  @$product[ 'sale_price' ] ?? 0,
                'PRIX_DE_VENTE_TTC'             =>  @$product[ 'sale_price' ] ?? 0,
                'PRIX_PROMOTIONEL'              =>  @$product[ 'discount_price' ] ?? 0,
                'STOCK_ENABLED'                 =>  @$product[ 'stock_management' ] ?? 0,
                'CODEBAR'                       =>  $codebar,
                'DATE_CREATION'                 =>  date_now(),
                'AUTHOR'                        =>  User::id(),
                'STATUS'                        =>  1, // on sale
                'TYPE'                          =>  @$product[ 'quantity' ] ? 1 : 2
            ];

            if ( @$product[ 'discount_starts' ] && @$product[ 'discount_ends' ] ) {
                $data[ 'SPECIAL_PRICE_START_DATE' ]     =  $product[ 'discount_starts' ]; 
                $data[ 'SPECIAL_PRICE_END_DATE' ]       =  $product[ 'discount_ends' ]; 
            }

            $this->db->insert( store_prefix() . 'nexo_articles', $data );

            /**
             * We can now get the product since it has been
             * created
             */
            $checkProduct       =   $this->db->where( 'ID', $this->db->insert_id() )
                ->get( store_prefix() . 'nexo_articles' )
                ->result_array();
            
            $newQuantity        =   @$product[ 'quantity' ];

            /**
             * If a quantity is not provided, then we'll not create 
             * a stock flow history for this item.
             */
            if ( $newQuantity ) {
                $this->db->insert( store_prefix() . 'nexo_articles_stock_flow', [
                    'REF_ARTICLE_BARCODE'   =>  $checkProduct[0][ 'CODEBAR' ],
                    'BEFORE_QUANTITE'       =>  0,
                    'QUANTITE'              =>  $newQuantity,
                    'AFTER_QUANTITE'        =>  $newQuantity,
                    'DATE_CREATION'         =>  date_now(),
                    'AUTHOR'                =>  User::id(),
                    'UNIT_PRICE'            =>  $product[ 'sale_price' ],
                    'TYPE'                  =>  'supply',
                    'REF_SHIPPING'          =>  1,
                    'REF_PROVIDER'          =>  1,
                    'TOTAL_PRICE'           =>  floatval( $product[ 'sale_price' ] ) * $newQuantity,
                    'DESCRIPTION'           =>  __( 'Modification du stock effectuée depuis WooCommerce', 'nexo' )
                ]);
            }

            $status[ 'success' ][]    =   $product;
        }

        return $status;
    }

    /**
     * Sync Down Products
     * from WooCommerce
     * @return json
     */
    public function syncDownProducts()
    {
        $this->db->truncate( store_prefix() . 'nexo_articles' );
        $this->db->truncate( store_prefix() . 'nexo_articles_stock_flow' );
        $this->db->truncate( store_prefix() . 'nexo_articles_meta' );
        
        if ( $this->post( 'products' ) ) {
            foreach( $this->post( 'products' ) as $product ) {

                /**
                 * we should get first if such category exists
                 * normally it should
                 */
                $cat    =   $this->db->where( 'NOM', $product[ 'categories' ][0][ 'name' ] )
                    ->get( store_prefix() . 'nexo_categories' );
                
                if ( ! $cat ) {
                    /**
                     * let's create the category
                     */
                    $this->db->insert( store_prefix() . 'nexo_categories', [
                        'NOM'           =>  $product[ 'categories' ][0][ 'name' ],
                        'DESCRIPTION'   =>  $product[ 'categories' ][0][ 'description' ],
                    ]);

                    $cat    =   [ 'ID'  =>  $this->db->insert_id() ];
                }

                $this->__treatProduct( $product, $cat );
            }

            /**
             * If nothing has been provided
             */
            return $this->response([
                'status'    =>  'failed',
                'message'   =>  __( 'Les produits ont été correctement synchronisé', 'nexo' )
            ]);
        }

        /**
         * If nothing has been provided
         */
        return $this->response([
            'status'    =>  'failed',
            'message'   =>  __( 'Liste de produits non fournie', 'nexo' )
        ]);
    }

    /**
     * Sync down category coming from WooCommerce
     * @return json
     */
    public function syncDownCategory()
    {

    }

    /**
     * Sync up products 
     * saved from NexoPOS
     * @return json
     */
    public function syncUpProduct()
    {

    }

    /**
     * sync up orders
     * as saved on NexoPOS
     * @return json
     */
    public function syncUpOrder()
    {

    }

    /**
     * sync up category
     * as saved on NexoPOS
     * @return json
     */
    public function syncCategory()
    {

    }

    /**
     * Sync Down Single Order
     * @return json
     */
    public function syncDownSingleOrder()
    {
        return $this->response([ 
            'status'    =>  'success',
            'message'   =>  'the order has been placed'
        ]);
    }

    /**
     * history
     * @return json of history
     */
    public function history( $page = 1 )
    {
        $this->load->library( 'pagination' );

        $allHistory     =   $this->db
            ->select( '*' )
            ->from( store_prefix() . 'nexo_historique' )
            ->count_all_results();
        
        $config                     =   [];
        $config[ 'per_page' ]       =   10;
        $config[ 'page' ]           =   floatval( $page );
        $config[ 'count_all' ]      =   $allHistory;
        $config[ 'offset' ]         =   ( floatval( $page ) - 1 ) * $config[ 'per_page' ];
        $config[ 'total_pages' ]    =   ceil( floatval( $config[ 'count_all' ] ) / floatval( $config[ 'per_page' ] ) );

        $entries        =   $this->db
            ->select( '*' )
            ->from( store_prefix() . 'nexo_historique' )
            ->limit( $config[ 'per_page' ], $config[ 'offset' ] )
            ->order_by( 'ID', 'desc' )
            ->get()
            ->result_array();
        
        $config[ 'entries' ]        =   $entries;

        return $this->response( $config );
    }

    /**
     * delete selected history
     * @return json
     */
    public function deleteSelectedHistory() {

        if ( ! User::in_group([ 'master' ]) ) {
            return $this->response([
                'status'    =>  'failed',
                'message'   =>  __( 'Vous n\'êtes pas autorisée à supprimer l\'historique', 'nexo' )
            ]);
        }

        if ( is_array( $this->post( 'selected' ) ) ) {
            $this->db->where_in( 'ID', $this->post( 'selected' ) )
                ->delete( store_prefix() . 'nexo_historique' );

            return $this->response([
                'status'    =>  'success',
                'message'   =>  __( 'Les entrées sélectionnées ont été supprimées', 'nexo' )
            ]);
        }

        return $this->response([
            'status'    =>  'failed',
            'message'   =>  __( 'Identifiants incorrectes fournies. Impossible de supprimer les entrées.', 'nexo' )
        ]);
    }

    public function productReorder()
    {
        if ( $this->post( 'products' ) ) {
            foreach( $this->post( 'products' ) as $product ) {
                $this->db->where( 'ID', $product[ 'ID' ])
                    ->update( store_prefix() . 'nexo_articles', [
                        'ORDER'     =>  $product[ 'ORDER' ]
                    ]);
            }

            return $this->response([
                'status'    =>  'success',
                'message'   =>  __( 'Les produits ont correctement été réorganisés', 'nexo' )
            ]);
        }

        return $this->response([
            'status'    =>  'failed',
            'message'   =>  __( 'Impossible de traiter la requête. Les produits n\'ont pas été fourni.', 'nexo' )
        ], 403 );
    }

    public function migration()
    {
        $version        =   $this->post( 'version' );
        $handle         =   $this->post( 'handle' );
        $store          =   $this->post( 'store' );
        $processed      =   $this->post( 'processed' );
        
        switch( $version ) {
            case '3.15.16':
                $result     =   $this->findOrderIndex( compact( 'version', 'handle', 'store', 'processed' ) );
            break;
        }

        return $this->response( $result );
    }

    private function findOrderIndex( $details )
    {
        global $store_id;

        extract( $details );
        /**
         * @param string version
         * @param string handle
         * @param string store
         * @param string processed
         */

        $this->load->model( 'Nexo_Stores' );

        $store_id           =   empty( $store ) ? null : $store;

        $this->assignOrderCodeForStore( $store_id );

        $nextStoreID        =   null;
        $stores             =   $this->Nexo_Stores->get();

        if ( $store === null ) {
            $nextStore      =   collect( $stores )->first();
            $nextStoreID    =   @$nextStore[ 'ID' ];
        } else {
            $nextStore      =   collect( $stores )->filter( function( $_store ) use ( $store ) {
                return intval( $_store[ 'ID' ] ) > intval( $store );
            })->first();

            $nextStoreID    =   @$nextStore[ 'ID' ];
        }
        
        if ( ! empty( $nextStoreID ) ) {
            $this->response([
                'status'    =>  'success',
                'message'   =>  __( 'Démarrage du processus...', 'nexo' ),
                'data'      =>  [
                    'url'       =>  site_url([ 'api', 'nexopos', 'migration' ]),
                    'title'     =>  sprintf( __( 'Détection de l\'index des commandes pour : %s', 'nexo' ), $nextStoreID ),
                    'version'   =>  '3.15.16',
                    'handle'    =>  'orders-indexes',
                    'class'     =>  'store' . $nextStoreID,
                    'store'     =>  $nextStoreID,
                    'processed' =>  null
                ]
            ]);
        } else {
            set_option( 'migration_' . 'nexo', '3.15.16', true);
            return $this->response([
                'status'    =>  'success',
                'message'   =>  __( 'Migration complète', 'nexo' )
            ]);
        }
    }

    private function assignOrderCodeForStore( $store = null )
    {
        $this->load->module_model( 'nexo', 'Nexo_Orders_Model', 'order_model' );
        $date 			    =	Carbon::parse( date_now() );
        $start              =   $date->startOfDay()->toDateTimeString();
        $end                =   $date->endOfDay()->toDateTimeString();
        $todayString 	    =	$date->toDateString();
        
        $orders             =   $this->order_model->getOrderByTimeRange( date_now(), date_now(), []);
        $store_prefix       =   $store === null ? '' : 'store_' . $store . '_'; 
        $indexReference     =   'nexo_orders_indexes_' . $store_prefix . slugify( $todayString );
        $totalOrders        =   count( $orders );
        $index              =   floatval( get_option( $indexReference, 0 ) ) + $totalOrders;
        $code               =   substr( $date->year, 2 ) . sprintf( "%02d", $date->month ) . sprintf( "%02d", $date->day ) . '-' . sprintf( "%03d", ( $index ) );
        $safeGuard          =   0;

        do {
            $safeGuard++;
            $order          =   $this->order_model->get( $code, 'CODE' );
            set_option( $indexReference, ++$index );

            if ( empty( $order ) ) {
                break;
            }

            if ( $safeGuard >= 200 ) {
                throw new Exception( __( 'Impossible d\'assigner un code avec autant de commandes déjà créées', 'nexo' ) );
            }
        }
        while( true );
    }

    public function reassignOrdersCode()
    {
        $this->assignOrderCodeForStore( get_store_id() );

        return $this->response([
            'status'    =>  'success',
            'message'   =>  __( 'Le code des commandes a été reassigné', 'nexo' )
        ]);
    }
}