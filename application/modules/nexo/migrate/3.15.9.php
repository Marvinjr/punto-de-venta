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

// foreach( $stores as $store ) {
//     $store_id           =   $store[ 'ID' ] == 0 ? null : $store[ 'ID' ];
//     $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

//     set_otion( store_prefix( $store_id ) . 'enable_customers_banking', get_option( store_prefix( $store_id ) . 'allow_negative_credit', 'no' ) );
//     delete_option( store_prefix( $store_id ) . 'allow_negative_credit' );
// }