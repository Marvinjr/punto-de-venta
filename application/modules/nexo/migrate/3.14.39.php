<?php
/**
 * update permission for users
 * @since 3.13.15
 */
$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {
    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    $columns            =   $this->db->list_fields( $store_prefix . 'nexo_articles' );
    if( ! in_array( 'ORDER', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_articles` 
        ADD `ORDER` int(11) NULL AFTER `STATUS`' );
    }

    $products       =   $this->db->get( $store_prefix . 'nexo_articles' )
        ->result_array();

    foreach( $products as $product ) {
        if ( empty( $product[ 'ORDER' ] ) ) {
            $product[ 'ORDER' ]     =   0;
            $this->db->where( 'ID', $product[ 'ID' ] )
                ->update( $store_prefix . 'nexo_articles', $product );
        }
    }
}