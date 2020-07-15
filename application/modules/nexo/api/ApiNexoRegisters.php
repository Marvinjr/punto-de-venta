<?php
use Carbon\Carbon;
class ApiNexoRegisters extends Tendoo_Api
{
    /**
     * List all available registers
     * @return json of registers
     */
    public function getAll()
    {
        $registers  =   $this->db->get( store_prefix() . 'nexo_registers' )
        ->result_array();
        return $this->response( $registers );
    }

    /**
     * Idle Register
     * @return json
     */
    public function idleRegister( $id )
    {
        $this->load->model( 'Nexo_Checkout' );
        $register   =   $this->Nexo_Checkout->get_register( $id );
        
        if ( $register ) {
            $this->Nexo_Checkout->set_idle( 'idle_starts', $id );
            return $this->response([
                'status'    =>  'success',
                'message'   =>  __( 'La session a été arrêtée !', 'nexo' )
            ]);
        }

        return $this->response([
            'status'    =>  'failed',
            'message'   =>  __( 'Impossible d\'identifier la caisse enregistreuse', 'nexo' )
        ], 404 );
    }

    /**
     * Idle Register
     * @return json
     */
    public function activeRegister( $id )
    {
        $this->load->model( 'Nexo_Checkout' );
        $register   =   $this->Nexo_Checkout->get_register( $id );
        
        if ( $register ) {
            $this->Nexo_Checkout->set_idle( 'idle_ends', $id );
            return $this->response([
                'status'    =>  'success',
                'message'   =>  __( 'La session a été relancée !', 'nexo' )
            ]);
        }

        return $this->response([
            'status'    =>  'failed',
            'message'   =>  __( 'Impossible d\'identifier la caisse enregistreuse', 'nexo' )
        ], 404 );
    }

    /**
     * record a cash out operation
     * on a specific cash register
     * @param id
     */
    public function cashOut( $register_id )
    {
        /**
         * check if its allowed
         * @todo might need to make a 
         * permission check as well
         */
        if ( store_option( 'nexo_allow_cash_out' ) === 'yes' ) {
            $this->load->module_model( 'nexo', 'NexoCashRegisterModel', 'register_model' );
            
            $amount     =   $this->post( 'amount' );
            $reason     =   $this->post( 'reason' );
            $response   =   $this->register_model->addCashOut( $register_id, $amount, $reason );

            return $this->response( $response, $response[ 'status' ] === 'success' ? 200 : 401 );
        }

        return $this->response([
            'status'    =>  'failed',
            'message'   =>  __( 'La sortie d\'argent a été désactivée.', 'nexo' )
        ], 401 );
    }

    public function registerHistory( $register_id )
    {
        $pagination     =   new Pagination([
            'table'     =>  store_prefix() . 'nexo_registers_activities',
            'perPage'   =>  20
        ]);

        $results        =   $pagination->select( '*' )
            ->select([
                store_prefix() . 'nexo_registers_activities.*',
                'AUTHOR_NAME'   =>  'aauth_users.name'
            ])
            ->where([ 'REF_REGISTER' => $register_id ])
            ->join( 'aauth_users', 'AUTHOR' )
            ->get();

        return $this->response( $results );
    }

    public function registersClearHistory( $register_id )
    {
        if ( User::can( 'nexo.clear.registers-history' ) ) {
            $this->load->module_model( 'nexo', 'NexoCashRegisterModel', 'register_model' );
            $this->register_model->clearHistory( $register_id );

            return $this->response([
                'status'    =>  'success',
                'message'   =>  __( 'L\'historique a correctement été supprimé', 'nexo' )
            ]);
        }

        return $this->response([
            'status'    =>  'failed',
            'message'   =>  __( 'Vous n\'êtes pas autorisé à supprimer l\'historique d\'une caisse enregistreuse', 'nexo' )
        ]);
    }
}