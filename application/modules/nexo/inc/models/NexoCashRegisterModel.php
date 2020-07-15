<?php
class NexoCashRegisterModel extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
        $this->load->module_model( 'nexo', 'NexoLogModel', 'logModel' );
        $this->load->model( 'Nexo_Misc', 'misc' );
    }


    /**
     * Register a cash out operation
     * on a specific register
     * @param int register id
     * @param int amount
     * @param string reason
     */
    public function addCashOut( $register_id, $amount, $reason )
    {
        if ( ! User::can( 'nexo.disbursing.registers' ) ) {
            /**
             * let's log the operation
             */
            $this->logModel->log(
                __( 'Tentative de décaissement', 'nexo' ),
                sprintf(
                    __( 'L\'utilisateur %s a essayé un décaissement d\'argent à hauteur de %s, depuis la caisse enregistreuse %s. Raison : %s', 'nexo' ),
                    User::pseudo(),
                    $this->misc->cmoney_format( $amount ),
                    $register[ 'NAME' ],
                    $reason
                )
            );

            return [
                'status'    =>  'failed',
                'message'   =>  __( 'Vous n\'avez pas l\'autorisation pour effectuer cette opération.', 'nexo' )
            ];
        }

        $register       =   $this->getRegister( $register_id );

        if ( floatval( $amount ) > floatval( $register[ 'TOTAL_CASH' ] ) ) {
            return [
                'status'    =>  'failed',
                'message'   =>  __( 'Impossible de faire une sortie d\'argent. Le solde est insuffisant pour le montant demandé', 'nexo' )
            ];
        }

        $this->db->insert( store_prefix() . 'nexo_registers_activities', [
            'AUTHOR'            =>  User::id(),
            'TYPE'              =>  'disbursement',
            'REF_REGISTER'      =>  $register_id,
            'BALANCE'           =>  $amount,
            'NOTE'              =>  $reason,
            'DATE_CREATION'     =>  date_now()
        ]);

        /**
         * let's update the cash register
         * total cash amount
         */
        $this->db->where( 'ID', $register_id )
            ->update( store_prefix() . 'nexo_registers', [
                'TOTAL_CASH'    =>  floatval( $register[ 'TOTAL_CASH' ] - floatval( $amount ) )
            ]);

        /**
         * let's log the operation
         */
        $this->logModel->log(
            __( 'Décaissement', 'nexo' ),
            sprintf(
                __( 'L\'utilisateur %s a effectuée un décaissement d\'argent à hauteur de %s, depuis la caisse enregistreuse %s. Raison : %s', 'nexo' ),
                User::pseudo(),
                $this->misc->cmoney_format( $amount ),
                $register[ 'NAME' ],
                $reason
            )
        );

        return [
            'status'    =>  'success',
            'message'   =>  __( 'La sortie d\'argent a été correctement effectuée.', 'nexo' )
        ];
    }

    /**
     * Save a cashin operation
     * for a specific register
     * @param int register id
     * @param int amount
     * @return mixed
     */
    public function addCashIn( $register_id, $amount, $note =  null )
    {
        $note                   =   $note ?? __( 'Encaissement Automatique', 'nexo' );
        $this->db->insert( store_prefix() . 'nexo_registers_activities', [
            'AUTHOR'            =>  User::id(),
            'TYPE'              =>  'cashing',
            'REF_REGISTER'      =>  $register_id,
            'BALANCE'           =>  $amount,
            'NOTE'              =>  $note,
            'DATE_CREATION'     =>  date_now()
        ]);

        $register               =   $this->getRegister( $register_id );

        $this->db->where( 'ID', $register_id )
            ->update( store_prefix() . 'nexo_registers', [
                'TOTAL_CASH'        =>  floatval( $register[ 'TOTAL_CASH' ] ) + $amount
            ]);

        /**
         * let's log the operation
         */
        $this->logModel->log(
            __( 'Encaissement', 'nexo' ),
            sprintf(
                __( 'L\'utilisateur %s a effectué un encaissement d\'argent à hauteur de %s dans la caisse enregistreuse %s', 'nexo' ),
                User::pseudo(),
                $this->misc->cmoney_format( $amount ),
                $register[ 'NAME' ]
            )
        );

        return [
            'status'    =>  'success',
            'message'   =>  __( 'L\'encaissement a été correctement enregistré', 'nexo' )
        ];
    }

    /**
     * get a specific cash register total
     * @param int register_id
     * @return int total cash
     */
    public function getRegisterTotalCash( $register_id )
    {
        $register       =   $this->getRegister( $id );

        if ( $register ) {
            return floatval( $register[ 'TOTAL_CACH' ] );
        }

        log_message( 'error', sprintf( 'Unable to find a register with the id : %s' ), $register_id );
        return 0;
    } 

    /**
     * get a specific cash register array
     * @param int register id
     * @return mixed
     */
    public function getRegister( $id )
    {
        $register   =   $this->db->where( 'ID', $id )
            ->get( store_prefix() . 'nexo_registers' )
            ->result_array();

        if( ! empty( $register ) ) {
            return $register[0];
        }

        return false;
    }

    /**
     * delete a cash register history
     * @param int register id
     * @return array
     */
    public function clearHistory( $register_id )
    {
        $this->db->where( 'REF_REGISTER', $register_id )
            ->delete( store_prefix() . 'nexo_registers_activities' );
        
            return [
            'status'    =>  'failed',
            'message'   =>  __( 'L\'historique à correctement été supprimé', 'nexo' )
        ];
    }
}