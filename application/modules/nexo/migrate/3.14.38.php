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

    $columns            =   $this->db->list_fields( $store_prefix . 'nexo_clients' );
    if( ! in_array( 'TOTAL_CREDIT', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_clients` 
        ADD `TOTAL_CREDIT` float(20) NULL AFTER `TOTAL_SPEND`' );
    }
    if( ! in_array( 'CREDIT_LIMIT', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_clients` 
        ADD `CREDIT_LIMIT` float(20) NULL AFTER `TOTAL_SPEND`' );
    }
    if( ! in_array( 'ALLOW_CREDIT', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_clients` 
        ADD `ALLOW_CREDIT` varchar(20) NULL AFTER `TOTAL_SPEND`' );
    }

    $this->db->query('CREATE TABLE IF NOT EXISTS `'. $this->db->dbprefix . $store_prefix . 'nexo_clients_accounts` (
        `ID` int(11) NOT NULL AUTO_INCREMENT,
        `OPERATION` varchar(200) NOT NULL,
        `VALUE` float NOT NULL,
        `DATE_CREATION` datetime NOT NULL,
        `DESCRIPTION` text NULL,
        `AUTHOR` int(11) NOT NULL,
        `REF_CLIENT` int(11) NOT NULL,
        PRIMARY KEY (`ID`)
    )');

    /**
     * let's update the customer
     * and disable the credit on their 
     * account
     */
    $customers  =   $this->db->get( $store_prefix . 'nexo_clients' )
        ->result_array();
        
    foreach( $customers as $customer ) {
        $this->db->where( 'ID', $customer[ 'ID' ])
            ->update( $store_prefix . 'nexo_clients', [
                'TOTAL_CREDIT'  =>  0,
                'CREDIT_LIMIT'  =>  0,
                'ALLOW_CREDIT'  =>  'no'
            ]);
    }
}