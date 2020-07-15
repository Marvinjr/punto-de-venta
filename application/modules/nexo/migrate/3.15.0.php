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

    $columns            =   $this->db->list_fields( $store_prefix . 'nexo_registers' );
    if( ! in_array( 'TOTAL_CASH', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_registers` 
        ADD `TOTAL_CASH` float(11) NULL AFTER `USED_BY`' );
    }

    $permissions                                        =   [];
    $permissions[ 'nexo.clear.registers-history' ] 		=	__( 'Effacer l\'historique d\'une caisse enregistreuse', 'nexo' );
    $permissions[ 'nexo.disbursing.registers' ] 		=	__( 'Décaissement d\'une caisse enregistreuse', 'nexo' );
    
    foreach( $permissions as $namespace => $perm ) {
        $this->auth->create_perm( 
            $namespace,
            $perm
        );
    }

    $permissions_keys    =   array_keys( $permissions );

    // var_dump( $this->auth->list_groups() );

    foreach([ 
        'registers',
        'registers-history',
    ] as $component ) {
        foreach([ 'clear.', 'disbursing.' ] as $action ) {

            $permission 	=	'nexo.' . $action . $component;
            if ( in_array( $permission, $permissions_keys ) ) {
                $this->auth->allow_group( 'store.manager', $permission );
                $this->auth->allow_group( 'master', $permission );
                $this->auth->allow_group( 'admin', $permission );
            }
        }
    }
}