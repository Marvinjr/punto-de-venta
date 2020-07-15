<?php
$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    $permissions                                        =   [];
    $permissions[ 'nexo.warehouse.settings' ] 		    =	__( 'Manage warehouse settings', 'stock-manager' );
    
    foreach( $permissions as $namespace => $perm ) {
        $this->auth->create_perm( 
            $namespace,
            $perm
        );
    }

    $permissions_keys    =   array_keys( $permissions );

    foreach( $permissions_keys as $action ) {
        $this->auth->allow_group( 'master', $action );
        $this->auth->allow_group( 'admin', $action );
    }
}