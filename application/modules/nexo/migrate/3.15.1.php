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

    $columns            =   $this->db->list_fields( $store_prefix . 'nexo_commandes_produits' );
    if( ! in_array( 'TAX', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_commandes_produits` 
        ADD `TAX` float(11) NULL AFTER `QUANTITE`' );
    }

    $columns            =   $this->db->list_fields( $store_prefix . 'nexo_commandes_produits' );
    if( ! in_array( 'TOTAL_TAX', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_commandes_produits` 
        ADD `TOTAL_TAX` float(11) NULL AFTER `PRIX_BRUT_TOTAL`' );
    }
}