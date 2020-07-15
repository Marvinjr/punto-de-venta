<?php
class OrderReceiptBuilder
{
    private $order;

    public function __construct( $id )
    {
        $this->customerModel        =   new NexoCustomersModel();
        $this->orderModel           =   new Nexo_Orders_Model();
        $this->rawOrder             =   $this->orderModel->getOrderOnly( $id );

        if ( $this->rawOrder === false ) {
            throw new Exception( __( 'Impossible de retrouver la commande, en utilisant l\'identifiant fourni.', 'nexo' ) );
        }

        $this->orderProducts        =   $this->orderModel->getOrderProducts( $this->rawOrder );
        $this->orderPayments        =   $this->orderModel->getPayments( $id );
        $this->orderRefunds         =   $this->orderModel->order_refunds( $id );
        $this->order                =   new stdClass;
        
        $this->buildOrder();
        $this->buildOrderCustomer();
        $this->buildOrderProducts();
        $this->buildOrderPayments();
        $this->buildOrderRefunds();
        $this->buildOrderMetas();
        $this->buildOrderItemTaxes();
        $this->buildOrderTax();
        
        $shipping                   =   [];
        if ( $raw   =   $this->orderModel->getOrderShipping( $id ) ) {
            $shipping               =   $raw[0];
        }
        $this->order->shipping      =   ( object ) $shipping; // is already well formated

        $this->buildOrderStoreOptions();
    }

    public function buildOrderTax()
    {
        $this->order->tax               =   [];
        
        if ( $this->order->ref_tax !== '0' ) {
            $tax    =   $this->orderModel->getOrderTax( $this->order->ref_tax );
            if ( $tax ) {
                $values     =   [];
                foreach( $tax[0] as $field => $value ) {
                    $values[ strtolower( $field ) ]     =   $value;
                }
                $this->order->tax           =   $values;
            }
        }
    }

    public function buildOrderMetas()
    {
        $this->order->metas   =   collect( $this->orderModel->getOrderMetas( $this->order->id ) )
            ->map( function( $meta ) {
                $newValues      =   [];
                foreach( $meta as $field => $value ) {
                    $newValues[ strtolower( $field ) ]  =   $value;
                }
                return ( object ) $newValues;
        });
    }

    public function buildOrderItemTaxes()
    {
        $this->order->taxes     =   $this->order->metas->map( function( $meta ) {
            if ( $meta->key === 'taxes' ) {
                $taxes      =   json_decode( $meta->value, true );
                $newValues  =   [];
                
                foreach( $taxes as $value ) {
                    $newVal         =   [];
                    foreach( $value as $field => $_value ) {
                        $newVal[ strtolower( $field ) ]     =   $_value;
                    }
                    $newValues[]    =   $newVal;
                }

                return json_decode( json_encode( $newValues ) );
            }
        });
    } 

    public function get()
    {
        return $this->order;
    }

    public function buildOrderStoreOptions()
    {
        get_instance()->load->library( 'parser' );

        $option                         =   new stdClass;
        $option->shop_name              =   store_option( 'site_name' );
        $option->shop_email             =   store_option( 'nexo_shop_email' );
        $option->shop_pobox             =   store_option( 'nexo_shop_pobox' );
        $option->shop_fax               =   store_option( 'nexo_shop_fax' );
        $option->shop_details           =   store_option( 'nexo_other_details' );
        $option->shop_street            =   store_option( 'nexo_shop_street' );
        $option->shop_phone             =   store_option( 'nexo_shop_phone' );
        $option->shop_city              =   store_option( 'nexo_shop_city' );
        $option->address_1              =   store_option( 'nexo_shop_address_1' );
        $option->address_2              =   store_option( 'nexo_shop_address_2' );

        $option->order_id               =   $this->order->id;
        $option->order_note             =   $this->order->description;
        $option->order_cashier          =   $this->order->author_name;
        $option->order_code             =   $this->order->code;
        $option->order_date             =   $this->order->created_at;
        $option->order_updated          =   $this->order->updated_at;
        $option->customer_name          =   $this->order->customer->name;
        $option->customer_phone         =   $this->order->customer->phone;
        $option->delivery_cost          =   $this->order->shipping_amount;
        $option->delivery_address_1     =   $this->order->shipping->address_1;
        $option->delivery_address_2     =   $this->order->shipping->address_2;
        $option->city                   =   $this->order->shipping->city;
        $option->country                =   $this->order->shipping->country;
        $option->name                   =   $this->order->shipping->name;
        $option->surname                =   $this->order->shipping->surname;
        $option->phone                  =   $this->order->shipping->phone;
        $option->state                  =   $this->order->shipping->state;
        $option->vat_type               =   store_option( 'nexo_vat_type' );
        $option->vat_percent            =   store_option( 'nexo_vat_percent' );

        $values                         =   ( array ) $option;

        $option->receipt_column_1       =   get_instance()->parser->parse_string( store_option( 'receipt_col_1' ), $values, true );
        $option->receipt_column_2       =   get_instance()->parser->parse_string( store_option( 'receipt_col_2' ), $values, true );
        $option->receipt_footer         =   get_instance()->parser->parse_string( store_option( 'nexo_bills_notices' ), $values, true );

        $this->order->options           =   $option;
    }

    public function buildOrderRefunds()
    {
        $refunds        =   [];

        foreach( $this->orderRefunds as $refund ) {
            $localRefund    =   [];
            foreach( $refund as $field => $value ) {
                $localRefund[ strtolower( $field ) ]    =   $value;
            }
            $refunds[]      =   $localRefund;
        }

        $this->order->refunds      =   ! empty( $refunds ) ? ( object ) $refunds : $refunds;
    }

    /**
     * Build order payments
     * @return void
     */
    public function buildOrderPayments()
    {
        $payments               =   [];

        foreach( $this->orderPayments as $rawPayment ) {
            $payment            =   [];
            $replacements       =   [
                'MONTANT'           =>  'amount',
                'REF_COMMAND_CODE'  =>  'order_code'
            ];
            
            foreach( $rawPayment as $field => $value ) {
                $payment[ strtolower( @$replacements[ $field ] ?: $field ) ]        =   $value;
            }

            $payments[]     =   $payment;
        }

        $this->order->payments      =   json_decode( json_encode( $payments ) );
    }

    /**
     * build orde products
     * @return void
     */
    public function buildOrderProducts()
    {
        $products      =   collect();
        foreach( $this->orderProducts as $rawProduct ) {
            $localProduct        =   [];
            
            $replacements       =   [
                'PRIX_BRUT'         =>  'gross_price',
                'PRIX_BRUT_TOTAL'   =>  'total_gross_price',
                'PRIX'              =>  'price',
                'PRIX_TOTAL'        =>  'total_price',
                'QUANTITE'          =>  'quantity',
                'CODEBAR'           =>  'barcode'
            ];

            foreach( $rawProduct as $key => $value ) {
                $localProduct[ strtolower( @$replacements[ $key ] ?: $key ) ]      =   $value;
            }
            
            $products->push( $localProduct );
        }

        $this->order->products  =   $products;
    }

    public function buildOrder()
    {
        $replacements       =   [
            'DATE_CREATION'     =>  'created_at',
            'TVA'               =>  'vat',
            'DATE_MOD'          =>  'updated_at',
            'SOMME_PERCU'       =>  'amount_perceived',
            'TITRE'             =>  'title',
            'REMISE'            =>  'discount'
        ];

        foreach( $this->rawOrder as $key => $value ) {
            $this->order->{strtolower( @$replacements[ $key ] ?: $key )}    =   $value;
        }
    }

    /**
     * Build current order customers
     * @return void
     */
    public function buildOrderCustomer()
    {
        $rawCustomer                =   $this->customerModel->get( $this->rawOrder[ 'REF_CLIENT' ] );

        if ( empty( $rawCustomer ) ) {
            throw new Exception( __( 'Impossible de recupérer le client attaché à la commande sollicitée', 'nexo' ) );
        }

        $customer                   =   [];

        $replacements               =   [
            'TEL'                   =>      'phone',
            'POIDS'                 =>      'weight',
            'PRENOM'                =>      'surname',
            'NOM'                   =>      'name',
            'DATE_NAISSANCE'        =>      'birth_date',
            'NBR_COMMANDES'         =>      'total_purchases',
            'DATE_MOD'              =>      'updated_at',
            'DATE_CREATION'         =>      'created_at'
        ];

        foreach( $rawCustomer[0] as $key => $value ) {
            $customer[ strtolower( @$replacements[ $key ] ?: $key ) ]     =   $value;
        }

        $this->order->customer      =   json_decode( json_encode( $customer ) );
    }
}