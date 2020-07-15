<?php

use Carbon\Carbon;

class ApiNexoPremiumProductReport extends Tendoo_API
{
    public function getTaxes()
    {
        $this->load->module_model( 'nexo', 'Nexo_Orders_Model', 'orderModel' );
        $orders     =   $this->orderModel->getOrderByTimeRange( 
            $this->post( 'startDate' ),
            $this->post( 'endDate' )
        );

        $this->response( $orders, @$orders[ 'status' ] === 'failed' ? 403 : 200 );
    }
}