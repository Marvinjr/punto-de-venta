<?php

class NexoProcurementsModel extends CI_Model
{
    public function getProcurements()
    {
        return $this->db->get( store_prefix() . 'nexo_arrivages' )
            ->result_array();
    }

    
}