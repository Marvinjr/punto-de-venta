<?php
/**
 * @since 3.12.13
 * @author NexoPOS Solutions
 */
class NexoProvidersModel extends Tendoo_Module
{
    /**
     * Get provider using the iD
     * @param int provider id
     * @return array | null
     */
    public function get( $id = null )
    {
        if ( $id !== null ) {
            $this->db->where( 'ID', $id );
        }

        $provider   =   $this->db
            ->get( store_prefix() . 'nexo_fournisseurs' )
            ->result_array();
        
        return $id === null ? $provider : @$provider[0];
    }

    /**
     * create a provider using the provided 
     * informations
     * @param array
     * @return array
     */
    public function create( $data ) 
    {
        $provider   =   $this->getUsingEmail( $data[ 'EMAIL' ] );

        if ( ! empty( $provider ) ) {
            return [
                'status'    =>  'failed',
                'message'   =>  __( 'Impossible de créer le fournisseur, l\'adresse email est déjà utilisée', 'nexo' )
            ];
        }

        $this->db->insert( store_prefix() . 'nexo_fournisseurs', $data );

        return [ 
            'status'    =>  'success',
            'message'   =>  __( 'Le fournisseur à été crée', 'nexo' )
        ];
    }

    public function getUsingEmail( $email )
    {
        return $provider   =   $this->db->where( 'EMAIL', $email )
            ->get( store_prefix() . 'nexo_fournisseurs' )
            ->result_array();
    }

    /**
     * update the owned amount
     * @param int provider id
     * @return void
     */
    public function refreshOwnedAmount( $provider_id )
    {
        /**
         * works only if the provider account
         * is enabled
         */
        if ( store_option( 'enable_providers_account', 'no' ) === 'yes' ) {

            $this->load->module_model( 'nexo', 'NexoStockTaking', 'stock_taking_model' );
    
            $paymentHistory     =   $this->getHistory( $provider_id, 'payment' );
            $supplies           =   $this->stock_taking_model->getByProvider( $provider_id );
    
            $amounts            =   array_sum( array_map( function( $supply ) {
                return floatval( $supply[ 'VALUE' ] );
            }, $supplies ) );
    
            $alreadyPaid       =   array_sum( array_map( function( $history ) {
                return floatval( $history[ 'AMOUNT' ] );
            }, $paymentHistory ) );
    
            /**
             * we need to count all what has yet
             * been paid to the customer
             */
            $ownedAmount    =   $amounts - $alreadyPaid;
    
            /**
             * here we'll just make sure
             * that the payable field is updated
             */
            $this->db->where( 'ID', $provider_id )
                ->update( store_prefix() . 'nexo_fournisseurs', [
                    'PAYABLE'   =>  $ownedAmount
                ]);
        }
    }

    public function getHistory( $provider_id, $type )
    {
        return $this->db->where( 'TYPE', $type )
            ->where( 'REF_PROVIDER', $provider_id )
            ->get( store_prefix() . 'nexo_fournisseurs_history' )
            ->result_array();
    }

    public function updateStockPurchaseValue( $shipping_id )
    {
        $moneyFlow  =   $this->db->where( 'REF_SUPPLY', $shipping_id )
            ->where( 'TYPE', 'stock_purchase' )
            ->get( store_prefix() . 'nexo_fournisseurs_history' )
            ->result_array();
        
        if ( ! empty( $moneyFlow ) ) {

            $this->load->module_model( 'nexo', 'NexoStockTaking', 'stocktaking_model' );
            $data   =   $this->stocktaking_model->countItemsTotal( $shipping_id );

            extract( $data );
            /**
             * -> total_amount
             * -> total_quantity
             * -> current_item
             */

            $this->db->where( 'REF_SUPPLY', $shipping_id )
                ->update( store_prefix() . 'nexo_fournisseurs_history', [
                    'AMOUNT'        =>  $total_amount,
                    'AFTER_AMOUNT'  =>  $total_amount + floatval( $moneyFlow[0][ 'BEFORE_AMOUNT' ] )
                ]);
        }
    }

    public function getRecentDeliveries( $provider_id )
    {
        return $procurements   =   $this->db->where( 'FOURNISSEUR_REF_ID', $provider_id )
            ->order_by( 'ID', 'desc' )
            ->get( store_prefix() . 'nexo_arrivages' )
            ->result_array();
    }

    public function getDeliveryProducts( $procurement_id )
    {
        return $this->db->where( 'REF_SHIPPING', $procurement_id )
            ->get( store_prefix() . 'nexo_articles_stock_flow' )
            ->result_array();
    }
}