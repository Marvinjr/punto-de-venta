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

    $columns            =   $this->db->list_fields( $store_prefix . 'nexo_categories' );
    if( ! in_array( 'ENABLED', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_categories` 
        ADD `ENABLED` varchar(20) NULL AFTER `THUMB`' );
    }

    $categories     =   $this->db->get( $store_prefix . 'nexo_categories' )
        ->result_array();

    /**
     * let's update the default value for
     * the categories
     */
    foreach( $categories as $category ) {
        if ( empty( $category[ 'ORDER' ] ) ) {
            $category[ 'ORDER' ]    =   0;
            $this->db->where( 'ID', $category[ 'ID' ] )
                ->update( $store_prefix . 'nexo_categories', $category );
        }
    }

    if( ! in_array( 'ORDER', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_categories` 
        ADD `ORDER` int(11) NULL AFTER `THUMB`' );
    }
}