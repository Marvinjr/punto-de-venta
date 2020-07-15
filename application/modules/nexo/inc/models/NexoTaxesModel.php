<?php
class NexoTaxesModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * get single tax using provided id
     * @param int tax id
     * @return array of tax or empty array
     */
    public function getTax( $id )
    {
        return $this->db->where( 'ID', $id )
            ->get( store_prefix() . 'nexo_taxes' )
            ->result_array();
    }

    /**
     * get all taxes
     * @return array
     */
    public function get()
    {
        return $this->db
            ->get( store_prefix() . 'nexo_taxes' )
            ->result_array();
    }
}