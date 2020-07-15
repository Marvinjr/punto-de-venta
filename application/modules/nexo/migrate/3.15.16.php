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

echo json_encode([
    'status'    =>  'success',
    'message'   =>  __( 'Démarrage du processus...', 'nexo' ),
    'data'      =>  [
        'url'       =>  site_url([ 'api', 'nexopos', 'migration' ]),
        'title'     =>  __( 'Détection de l\'index des commandes', 'nexo' ),
        'version'   =>  '3.15.16',
        'handle'    =>  'orders-indexes',
        'class'     =>  'root',
        'store'     =>  0,
        'processed' =>  null
    ]
]);

return false;