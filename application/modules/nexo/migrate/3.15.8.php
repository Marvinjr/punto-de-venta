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

    $columns            =   list_table_fields( $store_prefix . 'nexo_fournisseurs' );
    if( ! in_array( 'STREET', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_fournisseurs` 
        ADD `STREET` varchar(200) NULL AFTER `TEL`' );
    }
    $columns            =   list_table_fields( $store_prefix . 'nexo_fournisseurs' );
    if( ! in_array( 'CITY', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_fournisseurs` 
        ADD `CITY` varchar(200) NULL AFTER `TEL`' );
    }
    $columns            =   list_table_fields( $store_prefix . 'nexo_fournisseurs' );
    if( ! in_array( 'ZIPCODE', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_fournisseurs` 
        ADD `ZIPCODE` varchar(200) NULL AFTER `TEL`' );
    }
    $columns            =   list_table_fields( $store_prefix . 'nexo_fournisseurs' );
    if( ! in_array( 'COUNTRY', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_fournisseurs` 
        ADD `COUNTRY` varchar(200) NULL AFTER `TEL`' );
    }
    $columns            =   list_table_fields( $store_prefix . 'nexo_fournisseurs' );
    if( ! in_array( 'FAX', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_fournisseurs` 
        ADD `FAX` varchar(200) NULL AFTER `TEL`' );
    }
    $columns            =   list_table_fields( $store_prefix . 'nexo_fournisseurs' );
    if( ! in_array( 'WEBSITE', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_fournisseurs` 
        ADD `WEBSITE` varchar(200) NULL AFTER `TEL`' );
    }
}