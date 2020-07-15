<?php
class NexoExpenseModel extends Tendoo_Module 
{
    public function record( $fields )
    {
        $this->db->insert( store_prefix() . 'nexo_premium_factures', $fields );
    }

    public function updateRecord( $id, $fields )
    {
        $this->where( 'ID', $id )
            ->update( store_prefix() . 'nexo_premium_factures', $fields );
    }
}