<?php
/**
 * update permission for users
 * @since 3.13.15
 */
global $store_id;
$this->load->model( 'Nexo_Stores' );
$this->load->module_model( 'nexo', 'NexoProducts', 'products_model' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {
    $store_id           =   $store[ 'ID' ] == 0 ? null : $store[ 'ID' ];
    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    // $columns            =   $this->db->list_fields( $store_prefix . 'nexo_articles' );
    // if ( in_array( 'PRIX_DE_VENTE_BRUT', $columns ) ) {
    //     $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_articles` 
    //     DROP COLUMN `PRIX_DE_VENTE_BRUT`' );
    // }
    
    $columns            =   $this->db->list_fields( $store_prefix . 'nexo_commandes' );
    if( ! in_array( 'NET_TOTAL', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_commandes` 
        ADD `NET_TOTAL` float(11) NULL AFTER `TOTAL`' );
    }

    /**
     * fill order with 
     * net total.
     */
    $orders             =   $this->db->get( $store_prefix . 'nexo_commandes' )
        ->result_array();

    foreach( $orders as $order ) {
        $this->db->where( 'ID', $order[ 'ID' ])
            ->update( $store_prefix . 'nexo_commandes', [
                'NET_TOTAL'     =>  $order[ 'TOTAL' ]
            ]);
    }

    $columns            =   $this->db->list_fields( $store_prefix . 'nexo_articles' );
    if( ! in_array( 'PRIX_DE_VENTE_BRUT', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_articles` 
        ADD `PRIX_DE_VENTE_BRUT` float(11) NULL AFTER `PRIX_DE_VENTE_TTC`' );
    }

    $products       =   $this->products_model->get();
    foreach( $products as $product ) {
        $this->products_model->refreshSalePrice( $product[ 'ID' ] );
    }
}