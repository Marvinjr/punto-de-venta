<?php

class NexoCustomersModel extends Tendoo_Module 
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get Customer
     * @param int customer id (optional)
     * @return array
    **/

    public function get( $id = null, $as = 'ID' )
    {
        $this->db->select( 
            '*,' .
            store_prefix() . 'nexo_clients.ID as id, ' . 
            store_prefix() . 'nexo_clients.NOM as name, ' . 
            store_prefix() . 'nexo_clients.PRENOM as surname,' .
            store_prefix() . 'nexo_clients.EMAIL as email,' . 
            store_prefix() . 'nexo_clients.DATE_NAISSANCE as birth_date,' . 
            store_prefix() . 'nexo_clients.OVERALL_COMMANDES as overall_commandes,' .
            store_prefix() . 'nexo_clients.NBR_COMMANDES as total_orders,' . 
            store_prefix() . 'nexo_clients.TOTAL_SPEND as total_spend,' .                        
            store_prefix() . 'nexo_clients.LAST_ORDER as last_order,' .
            store_prefix() . 'nexo_clients.AVATAR as avatar,' .
            store_prefix() . 'nexo_clients.COUNTRY as country,' .
            store_prefix() . 'nexo_clients.POST_CODE as post_code,' .
            store_prefix() . 'nexo_clients.CITY as city,' .
            store_prefix() . 'nexo_clients.STATE as state,' . 
            store_prefix() . 'nexo_clients.DATE_CREATION as created_on,' . 
            store_prefix() . 'nexo_clients.DATE_MOD as edited_on,' . 
            store_prefix() . 'nexo_clients.REF_GROUP as ref_group,' . 
            store_prefix() . 'nexo_clients.AUTHOR as author,' .     
            store_prefix() . 'nexo_clients.DISCOUNT_ACTIVE as discount_active,' .     
            store_prefix() . 'nexo_clients.DESCRIPTION as description,' .        
            store_prefix() . 'nexo_clients.CREDIT_LIMIT as CREDIT_LIMIT,' .        
            store_prefix() . 'nexo_clients.ALLOW_CREDIT as ALLOW_CREDIT,' .        
            store_prefix() . 'nexo_clients.TOTAL_CREDIT as TOTAL_CREDIT,' .        
            store_prefix() . 'nexo_clients.TEL as phone'     
        );

        if( $id != null ) {
            $this->db->where( store_prefix() . 'nexo_clients.' . $as, $id );
        }

        $clients  =   $this->db->get( store_prefix() . 'nexo_clients' )
            ->result_array();

        foreach( $clients as &$client ) {
            $clients_addresses      =   $this->db->where( store_prefix() . 'nexo_clients_address.ref_client', $client[ 'id' ] )
                ->get( store_prefix() . 'nexo_clients_address' )
                ->result_array();

            if( $clients_addresses ) {
                foreach( $clients_addresses as $client_address ) {
                    foreach( $client_address as $key => $value ) {
                        if( $key != 'type') {
                            $client[ $client_address[ 'type' ] . '_' . $key ]   =   $value;
                            $client[ $client_address[ 'type' ] ][ $key ]    =   $value;
                        }
                    }
                }
            }
        }   

        return $clients;
    }

    /**
     * Get billing & shipping informations
     * @param int customer id
     * @return array
     */
    public function get_informations( $customer_id )
    {
        $address    =   $this->db->where( 'ref_client', $customer_id )
            ->get( store_prefix() . 'nexo_clients_address' )
            ->result_array();
        
        $finalAddresses         =   [];

        if ( count( $address ) ) {
            foreach( $address as $index => $_address ) {
                if ( in_array( $_address[ 'type' ], [ 'billing', 'shipping' ] ) ) {
                    $finalAddresses[ $_address[ 'type' ] ]   =   $_address;
                }
            }
        }

        return $finalAddresses;
    }

    /**
     * update a given customer
     * @param int customer id
     * @param data form to update
     * @return array
     */
    public function update( $customer_id, $data )
    {
        $this->db->where( 'ID', $customer_id )
            ->update( store_prefix() . 'nexo_clients', $data );
        return [
            'status'    =>  'success',
            'message'   =>  __( 'Le client a été mis à jour', 'nexo' )
        ];
    }

    /**
     * Create a customer
     * @return array
     */
    public function create( $data )
    {
        $emailUsed  =   false;
        // we must avoid same user with same email
        if( ! empty( $data[ 'email' ] ) ) {
            $customer  =   $this->db->where( 'EMAIL', $data[ 'email' ] )
                ->get( store_prefix() . 'nexo_clients' )
                ->result_array();

            if ( ! empty( $customer ) ) {
                return [
                    'status'    =>  'failed',
                    'message'   =>  __( 'Impossible de créer le client, l\'email est déjà en cours d\'utilisation.', 'nexo' ),
                    'customer'  =>  array_merge( $customer[0], $this->get_informations( $customer[0][ 'ID' ] ) )
                ];
            }
        }

        $customer_fields        =   $this->events->apply_filters_ref_array( 'nexo_filters_customers_post_fields', [
            [
                'NOM'               =>  @$data[ 'name' ] != null ? $data[ 'name' ] : '',
                'PRENOM'            =>  @$data[ 'surname' ] != null ? $data[ 'surname' ] : '',
                'COUNTRY'           =>  @$data[ 'country' ] != null ? $data[ 'country' ] : '',
                'CITY'              =>  @$data[ 'city' ] != null ? $data[ 'city' ] : '',
                'POST_CODE'         =>  @$data[ 'post_code' ] != null ? $data[ 'post_code' ] : '',
                'STATE'             =>  @$data[ 'state' ] != null ? $data[ 'state' ] : '',
                'DESCRIPTION'       =>  @$data[ 'description' ] != null ? $data[ 'description' ] : '',
                'DATE_NAISSANCE'    =>  @$data[ 'birth_date' ] != null ? $data[ 'birth_date' ] : '',
                'EMAIL'             =>  @$data[ 'email' ] != null ? $data[ 'email' ] : '',
                'DATE_CREATION'     =>  date_now(),
                'DATE_MOD'          =>  date_now(),
                'AUTHOR'            =>  @$data[ 'author' ] != null ? $data[ 'author' ] : User::id(),
                'TEL'               =>  @$data[ 'phone' ] != null ? $data[ 'phone' ] : '',
                'REF_GROUP'         =>  @$data[ 'ref_group' ]  != null ? $data[ 'ref_group' ]  : ''
            ], $data
        ] );

        $this->db->insert( store_prefix() . 'nexo_clients', $customer_fields );

        $insert_id      =   $this->db->insert_id();

        $meta                   =   [];
        foreach( $data as $key => $value ) {
            if( substr( $key, 0, 8 ) == 'shipping' || substr( $key, 0, 7 ) == 'billing' ) {

                if( substr( $key, 0, 8 ) == 'shipping' ) {
                    if( @$meta[ 'shipping' ] == null ) {
                        $meta[ 'shipping' ]     =   [];
                    }

                    $meta[ 'shipping' ][ substr( $key, 9 ) ]     =   $value ?: '';
                } else {
                    if( @$meta[ 'billing' ] == null ) {
                        $meta[ 'billing' ]     =   [];
                    }

                    $meta[ 'billing' ][ substr( $key, 8 ) ]     =   $value ?: '';
                }
            }
        }

        $meta[ 'billing' ][ 'ref_client' ]          =   $insert_id;
        $meta[ 'billing' ][ 'type' ]                =   'billing';
        $meta[ 'shipping' ][ 'ref_client' ]         =   $insert_id;
        $meta[ 'shipping' ][ 'type' ]               =   'shipping';

        $this->db->insert( store_prefix() . 'nexo_clients_address', $meta[ 'shipping' ] );
        $shipping_id    =   $this->db->insert_id();

        $this->db->insert( store_prefix() . 'nexo_clients_address', $meta[ 'billing' ] ); 
        $billing_id    =   $this->db->insert_id();

        $meta[ 'billing' ][ 'id' ]      =   $billing_id;
        $meta[ 'shipping' ][ 'id' ]     =   $shipping_id;

        $customer   =   $this->get( $insert_id );
        
        return [
            'status'        =>  'success',
            'message'       =>  __( 'Le client a été crée', 'nexo' ),
            'customer'      =>  array_merge( @$customer[0], [
                'shipping'      =>  $meta[ 'shipping' ],
                'billing'       =>  $meta[ 'billing' ],
            ]),            
        ];
    }

    /**
     * get a customer group
     * @param int customer id
     * @return array|false
     */
    public function getCustomerGroup( $customer_id )
    {
        $customer   =   $this->getSingle( $customer_id );
        if ( $customer ) {
            $group     =   $this->db->where( 'ID', $customer[ 'REF_GROUP' ] )
                ->get( store_prefix() . 'nexo_clients_groups' )
                ->result_array();

            if ( $group ) {
                return $group[0];
            }
        }
        return false;
    }

    /**
     * get single entry
     * @param int customer id
     * @return array
     */
    public function getSingle( $customer_id )
    {
        $customer   =   $this->get( $customer_id );
        if ( $customer ) {
            return $customer[0];
        }
        return false;
    }

    /**
     * get all customers groups
     * @return array 
     */
    public function getGroups()
    {
        return $this->db->get( store_prefix() . 'nexo_clients_groups' )
            ->result_array();
    }

    /**
     * credit action to the 
     * provided customer id
     * @param int customer id
     * @param string type {add|remove}
     * @param int amount
     * @param string description
     * @return array
     */
    public function creditAction( $customer_id, $type, $amount, $description )
    {
        $customer   =   $this->getSingle( $customer_id );

        if( $customer && in_array( $type, [ 'add', 'remove', 'payment' ] ) ) {

            /**
             * can't reduce an already negative stock
             */
            if ( floatval( $customer[ 'TOTAL_CREDIT' ] ) < 0 && $type === 'remove' ) {
                return [
                    'status'    =>  'failed',
                    'message'   =>  __( 'Impossible de retirer du crédit sur un compte débiteur. Un ajout de crédit est nécessaire pour continuer', 'nexo' )
                ];
            }

            if( $type === 'remove' && ( floatval( $customer[ 'TOTAL_CREDIT' ] ) - $amount ) < 0 ) {
                return [
                    'status'    =>  'failed',
                    'message'   =>  __( 'Impossible de continuer. A l\'issue de l\'opération, le solde du compte sera négatif. Un ajout de crédit est nécessaire pour continuer', 'nexo' )
                ];
            }
            
            $this->db->insert( store_prefix() . 'nexo_clients_accounts', [
                'REF_CLIENT'    =>  $customer_id,
                'AUTHOR'        =>  User::id(),
                'DESCRIPTION'   =>  $description,
                'OPERATION'     =>  $type,
                'DATE_CREATION' =>  date_now(),
                'VALUE'         =>  $amount
            ]);

            if ( $type === 'add' ) {
                $this->db->where( 'ID', $customer_id )
                    ->update( store_prefix() . 'nexo_clients', [
                        'TOTAL_CREDIT'  =>  floatval( $customer[ 'TOTAL_CREDIT' ] ) + floatval( $amount )
                    ]);
                return [
                    'status'    =>  'success',
                    'message'   =>  __( 'L\'opération de crédit s\'est déroulé correctement.' )
                ];
            } else {
                $this->db->where( 'ID', $customer_id )
                    ->update( store_prefix() . 'nexo_clients', [
                        'TOTAL_CREDIT'  =>  floatval( $customer[ 'TOTAL_CREDIT' ] ) - floatval( $amount )
                    ]);
                return [
                    'status'    =>  'success',
                    'message'   =>  __( 'L\'opération de débit s\'est déroulé correctement.' )
                ];
            }
        }
    }

    /**
     * Return paginated result
     * @param number customer id
     * @param number page id
     * @param number items per page
     * @return array
     */
    public function getPaginatedHistory( $customer_id, $page = 1, $perPage = 30 )
    {
        $totalRows  =   $this->db->where( 'REF_CLIENT', $customer_id )
            ->get( store_prefix() . 'nexo_clients_accounts' )
            ->num_rows();
        $results    =   $this->db->where( 'REF_CLIENT', $customer_id )
            ->limit( $perPage, ( intval( $page ) - 1 ) * $perPage )        
            ->get( store_prefix() . 'nexo_clients_accounts' )
            ->result_array();
        $totalPage  =   ceil( $totalRows / $perPage );

        return [
            'current_page'  =>  $page,
            'total_page'    =>  $totalPage,
            'prev_page'     =>  $page > 1 ? $page -1 : null,
            'next_page'     =>  $page < $totalPage ? $page + 1 : null,
            'data'          =>  $results,
            'total_items'   =>  $totalRows
        ];
    }

    /**
     * get customer transaction
     * @param number id
     * @return array
     */
    public function getTransaction( $id )
    {
        return $this->db->where( 'ID', $id )
            ->get( store_prefix() . 'nexo_clients_accounts' )
            ->result_array();
    }

    /**
     * cancel transaction
     * @param int transaction id
     * @return array
     */
    public function cancelTranslaction( $id )
    {
        $transaction    =   $this->getTransaction( $id );

        if ( ! empty( $transaction ) ) {
            $value      =   floatval( $transaction[0][ 'VALUE' ] );
            $customer   =   $this->get( $transaction[0][ 'REF_CLIENT' ] );

            $this->db->update( store_prefix() . 'nexo_clients', [
                'TOTAL_CREDIT'  => floatval( $customer[0][ 'TOTAL_CREDIT' ] ) - $value
            ]);

            $this->db->where( 'ID', $id )
                ->delete( store_prefix() . 'nexo_clients_accounts' );

            return true;
        }

        return false;
    }
    
    public function refreshExpenditures( $customer_id )
    {
        $this->load->module_model( 'nexo', 'Nexo_Orders_Model', 'order_model' );
        $customer   =   $this->getSingle( $customer_id );
        
        if( $customer ) {
            $orders             =   $this->order_model->getByCustomer( $customer_id, [ 
                'nexo_order_comptant',
                'nexo_order_refunded',
                'nexo_order_partially_refunded' 
            ]);
            $totalSpend         =   0;
            $totalOrders        =   count( $orders );

            foreach( $orders as $order ) {
                $totalSpend  +=  floatval( $order[ 'TOTAL' ] );
            }

            $this->db->where( 'ID', $customer_id )
                ->update( store_prefix() . 'nexo_clients', [
                    'OVERALL_COMMANDES'     =>      $totalOrders,
                    'TOTAL_SPEND'           =>      $totalSpend
                ]);

            return [
                'status'    =>  'success',
                'message'   =>  __( 'Les données du client ont été mis à jour', 'nexo' )
            ];
        }
        
        return [
            'status'    =>  'failed',
            'message'   =>  __( 'Impossible de retrouver le client avec l\'identifiant fourni', 'nexo' )
        ];
    }
}