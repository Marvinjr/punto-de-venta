<?php

class OrderReceipt
{
    protected $subTotal         =   0;
    protected $totalProduct     =   0;
    private $order;
    private $items;
    private $refunds;
    private $totalRefunds;
    protected $paymentTypes;
    protected $shipping;
    private $template;

    public function __construct( $order_id )
    {
        $this->ci       =   get_instance();
        $this->ci->load->module_model( 'nexo', 'Nexo_Orders_Model', 'order_model' );
        $this->ci->load->model( 'Nexo_Misc' );
        $this->ci->load->model( 'Nexo_Checkout' );

        $this->order            =   $this->ci->order_model->getOrder( $order_id );
        $this->items            =   $this->ci->order_model->getOrderItems( $order_id );
        $this->tax              =   $this->ci->Nexo_Misc->get_taxes( $this->order[ 'REF_TAX' ] );
        $this->refunds          =   $this->ci->order_model->order_refunds( $order_id );
        $this->payments         =   $this->ci->Nexo_Misc->order_payments( $this->order[ 'CODE' ] );
        $this->paymentTypes     =   $this->ci->events->apply_filters( 'nexo_payments_types', $this->ci->config->item( 'nexo_payments_types' ) );;
        $this->shipping         =   $this->ci->db->where( 'ref_order', $order_id )->get( store_prefix() . 'nexo_commandes_shippings' )->result_array();

        $this->totalRefunds   =   array_sum( array_map( function( $refund ) {
            return floatval( $refund[ 'TOTAL' ] );
        }, $this->refunds ) );

        $this->computeReceipt();
    }

    /**
     * get current order products
     * @return array of items
     */
    public function getProducts()
    {
        return $this->items;
    }

    public function getPayments()
    {
        return array_map( function( $payment ) {
            return ( object ) [
                'amount'    =>  floatval( $payment[ 'MONTANT' ] ),
                'name'      =>  @$this->paymentTypes[ $payment[ 'PAYMENT_TYPE' ] ] ?: __( 'Paiement Inconnu', 'nexo' ),
                'type'      =>  $payment[ 'OPERATION' ]
            ];
        }, $this->payments );
    }

    /**
     * get the current order
     * @return array with current order details.
     */
    public function getOrder()
    {
        return $this->order;
    }

    public function items()
    {
        return array_map( function( $item ) {
            $newItem    =   new \stdClass;
            $newItem    =   $this->setProductName( $item, $newItem );
        }, $this->items );
    }

    /**
     * get net total for the current order
     * @return float net total
     */
    public function computeReceipt()
    {
        // $this->subTotal     =   array_sum( array_may( function( $item ) {
        //     $this->totalProduct     +=  floatval( $item[ 'QUANTITE' ] );
        //     return parseFloat( $item[ 'PRIX_TOTAL' ] );
        // }, $this->items ) );

        return floatval( $this->order[ 'NET_TOTAL' ] );
    }

    /**
     * get the shipping amount
     * for the current order
     * @return float amount
     */
    public function getShippingAmount() 
    {
        return $this->order[ 'SHPPING_AMOUNT' ];
    }

    /**
     * get the discount which apply to
     * the current order
     * @return float discount
     */
    public function getDiscount()
    {
        if ( $this->order[ 'REMISE_TYPE' ] === 'flat' ) {
            return $this->order[ 'REMISE' ];
        } else if ( $this->order[ 'REMISE_TYPE' ] === 'percentage' ) {
            return nexoCartGrossValue( $this->items ) * floatval( $this->order[ 'REMISE_PERCENT' ] );
        } 
        return 0;
    }

    /**
     * get the current amount perceived 
     * for the order
     * @return float amount
     */
    public function getCollectedAmount()
    {
        return floatval( $this->order[ 'SOMME_PERCU' ]);
    }

    /**
     * get change for the current order
     * @return float amount 
     */
    public function getChange()
    {
        return ( $this->getCollectedAmount() - $this->getTotalRefund() ) - $this->getTotal();
    }

    /**
     * get the total refund of the order
     * @return float amount
     */
    public function getTotalRefund()
    {
        return $this->totalRefunds;
    }

    /**
     * return the tax value for the
     * current order. Could be multiple or not
     * @return array tax value
     */
    public function getTaxValue()
    {
        if( store_option( 'nexo_vat_type' ) === 'fixed' ) {
            return [
                'name'      =>  __( 'Fixée', 'nexo' ),
                'rate'      =>  store_option( 'nexo_vat_percent' ),
                'value'     =>  floatval( $this->order[ 'TVA' ] ),
                'multiple'  =>  false
            ];
        } else if ( store_option( 'nexo_vat_type' ) === 'variable' ) {
            return [
                'name'  =>  $this->tax[0][ 'NAME' ],
                'rate'  =>  $this->tax[0][ 'RATE' ],
                'value' =>  $this->order[ 'TVA' ],
                'multiple'  =>  false
            ];
        } else if( store_option( 'nexo_vat_type' ) == 'item_vat' ) {
            $multiple   =   true;
            $taxes      =   $taxes = json_decode( @$this->order[ 'metas' ][ 'taxes' ], true );
            $taxes      =   array_map( function( $tax ) {
                return [
                    'name'  =>  $tax[ 'NAME' ],
                    'rate'  =>  $tax[ 'RATE' ],
                    'value' =>  $tax[ 'VALUE' ],
                ];
            }, $taxes );

            return compact( 'multiple', 'taxes' );
        }
    }

    /**
     * return total of the current order
     * @return float total 
     */
    public function getTotal()
    {
        return floatval( $this->order[ 'TOTAL' ] );
    }

    /**
     * return the group discount on
     * the current order
     * @return float discount
     */
    public function getGroupDiscount()
    {
        return floatval( $this->order[ 'GROUP_DISCOUNT' ]);
    }

    /**
     * return the product name to display
     * for the current order
     * @param array raw product
     * @param object stclass of the new product
     * @return object stclass updated
     */
    protected function getProductName( $entry, &$item )
    {
        if ( store_option( 'item_name', 'only_primary' ) === 'only_primary' ) {
            $item->name     =   $entry[ 'NAME' ];
        } else if ( store_option( 'item_name', 'only_primary' ) === 'only_secondary' ) {
            $item->name     =   $entry[ 'ALTERNATIVE_NAME' ];
        } else if ( store_option( 'item_name', 'only_primary' ) === 'use_both' ) {
            $item->name            =   $entry[ 'NAME' ];
            $item->second_name     =   $entry[ 'ALTERNATIVE_NAME' ];
        }
        return $item;
    }

    public function getSubTotal()
    {
        return floatval( $this->order[ 'NET_TOTAL' ] );
    }

    public function getTemplate()
    {
        $dateCreation                           =   new DateTime( $this->order[ 'DATE_CREATION' ] );
        $dateModification                       =   new DateTime( $this->order[ 'DATE_MOD' ] );
        $this->template                         =   new stdClass;
        $this->template->order_date		        =	$dateCreation->format( store_option( 'nexo_datetime_format', 'Y-m-d h:i:s' ) );
        $this->template->order_updated          =	$dateModification->format( store_option( 'nexo_datetime_format', 'Y-m-d h:i:s' ) );
        $this->template->order_code		        =	$this->order[ 'CODE' ];
        $this->template->order_id               =   $this->order[ 'ID' ];
        $this->template->order_status	        =	$this->ci->Nexo_Checkout->get_order_type($this->order[ 'TYPE' ]);
        $this->template->order_note             =   $this->order[ 'DESCRIPTION' ];
        $this->template->order_cashier	        =	User::pseudo( $this->order[ 'AUTHOR' ] );
        $this->template->shop_name		        =	store_option( 'site_name' );
        $this->template->shop_pobox		        =	store_option( 'nexo_shop_pobox' );
        $this->template->shop_fax		        =	store_option( 'nexo_shop_fax' );
        $this->template->shop_email             =	store_option( 'nexo_shop_email' );
        $this->template->shop_street            =	store_option( 'nexo_shop_street' );
        $this->template->shop_phone             =	store_option( 'nexo_shop_phone' );
        $this->template->customer_name          =   $this->order[ 'customer' ][ 'NOM' ];
        $this->template->customer_phone         =   $this->order[ 'customer' ][ 'TEL' ];
        $this->template->delivery_address_1     =   @$this->shipping[0][ 'address_1' ];
        $this->template->delivery_address_2     =   @$this->shipping[0][ 'address_2' ];
        $this->template->city                   =   @$this->shipping[0][ 'city' ];
        $this->template->country                =   @$this->shipping[0][ 'country' ];
        $this->template->name                   =   @$this->shipping[0][ 'name' ];
        $this->template->phone                  =   @$this->shipping[0][ 'phone' ];
        $this->template->surname                =   @$this->shipping[0][ 'surname' ];
        $this->template->state                  =   @$this->shipping[0][ 'surname' ];
        $this->template->delivery_cost          =   @$this->shipping[0][ 'price' ];
        return $this->template;
    }
}