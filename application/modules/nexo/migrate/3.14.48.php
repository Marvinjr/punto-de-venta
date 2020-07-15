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

$permissions[ 'nexo.manage.transfers' ]    =    __( 'Autorisé à manipuler les transferts de stock', 'nexo' );

foreach( $permissions as $namespace => $perm ) {
    get_instance()->auth->create_perm( 
        $namespace,
        $perm
    );
}

foreach([ 'nexo.manage.transfers' ] as $transferPermission ) {
    get_instance()->auth->allow_group( 'store.manager', $transferPermission );
    get_instance()->auth->allow_group( 'master', $transferPermission );
    get_instance()->auth->allow_group( 'admin', $transferPermission );
    get_instance()->auth->allow_group( 'sub-store.manager', $transferPermission );
}


// foreach( $stores as $store ) {
//     $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';
// }

