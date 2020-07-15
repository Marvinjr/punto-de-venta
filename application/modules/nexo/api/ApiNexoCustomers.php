<?php
use Carbon\Carbon;
class ApiNexoCustomers extends Tendoo_Api
{
    public function importCSV()
    {
        $customers     =   [];

        if ( $this->post( 'empty' ) ) {
            $this->db->from( store_prefix() . 'nexo_clients' )
                ->truncate();
        }

        foreach( $this->post( 'csv' ) as $row ) {
            if ( ! empty( $row ) ) {
                $singleLine     =   [];
                foreach( $this->post( 'model' ) as $index => $model ) {
                    if ( ! empty( $model ) && count( $row ) > 1 ) {
                        // var_dump( $model, $index, @$row[ $index ] );
                        $singleLine[ $model ]   =   $row[ $index ];
                    }
                }
                
                if( ! empty( $singleLine ) ) {
                    $singleLine[ 'DATE_CREATION' ]  =   date_now();
                    $singleLine[ 'DATE_MOD' ]       =   date_now();
                    $customers[]    =   $singleLine;
                }
            }
        }

        $this->db->insert_batch( store_prefix() . 'nexo_clients', $customers );

        return $this->response([
            'status'    =>  'success',
            'message'   =>  __( 'Les clients ont été correctement importées' )
        ]);
    }

    /**
     * submit an account action
     * @return json
     */
    public function account()
    {
        $this->load->module_model( 'nexo', 'NexoCustomersModel', 'customer_model' );

        $response   =   $this->customer_model->creditAction( 
            $this->post( 'customer_id' ),
            $this->post( 'type' ),
            $this->post( 'amount' ),
            $this->post( 'description' )
        );

        return $this->response( $response, $response[ 'status' ] === 'failed' ? 403 : 200 );
    }

    /**
     * return the customer
     * account history
     * @param int customer id
     * @return json
     */
    public function accountHistory( $customer_id )
    {
        $this->load->module_model( 'nexo', 'NexoCustomersModel', 'customer_model' );
        $customer   =   $this->customer_model->get( $customer_id );

        if ( $customer ) {
            $result     =   $this->customer_model->getPaginatedHistory( $customer_id, intval( @$_GET[ 'page' ] ) );
            $this->response( $result );
        }
        
        $this->response([
            'status'    =>  'failed',
            'message'   =>  __( 'Impossible de charger l\'historique du client', 'nexo' )
        ], 403 );
    }

    public function cancelTransaction( $transaction_id )
    {
        $this->load->module_model( 'nexo', 'NexoCustomersModel', 'customer_model' );
        $transaction   =   $this->customer_model->cancelTranslaction( $transaction_id );

        /**
         * check if the transaction
         * was successful or not
         */
        if ( $transaction ) {
            return $this->response([
                'status'    =>  'success',
                'message'   =>  __( 'La transaction a été annulée', 'nexo' )
            ]);
        }

        return $this->response([
            'status'    =>  'failed',
            'message'   =>  __( 'Une erreur s\'est produite durant l\'opération', 'nexo' )
        ], 403 );
    }
}