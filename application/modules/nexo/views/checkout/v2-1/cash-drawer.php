<?php
global  
    $Options, 
    $current_register, 
    $order;
?>
<script>
    const cashDrawerData  =   () => {
        data                        =   {}
        data.textDomain             =   {
            cashDrawerOpening               : `<?php echo  __( 'Ouverture du tiroir de caisse...', 'nexo' );?>`,
            cashDrawerErrorWhileOpening     : `<?php echo  __( 'Une erreur s\'est produite durant l\'ouverture de la caisse.', 'nexo' );?>`,
        }
        data.registerOpen           =   `<?php echo store_option( 'nexo_use_cashrawer', 'no' ) === 'yes' ? '?cash-drawer-action=open' : '';?>`;
        data.posPrinter             =   `<?php echo store_option( 'nexo_pos_printer' );?>`;
        data.printServerURL         =   `<?php echo store_option( 'nexo_print_server_url' );?>`;
        data.registerURL            =   `<?php echo $current_register[ 'NPS_URL' ];?>`;
        data.registerPrinter        =   `<?php echo $current_register[ 'ASSIGNED_PRINTER' ];?>`;
        data.totalPrint             =   <?php echo intval( store_option( 'nexo_nps_print_copies', 1 ) );?>;
        data.autoPrintEnabled       =   <?php echo store_option( 'nexo_enable_autoprint', 'yes' ) === 'yes' ? 'true' : 'false';?>;
        data.printGateway           =   '<?php echo store_option( 'nexo_print_gateway', 'normal_print' );?>';
        data.printBase64            =   <?php echo store_option( 'nps_print_base64', 'no' ) === 'no' ? 'false' : 'true';?>;
        data.base64URL              =   `<?php echo store_option( 'nexo_print_server_url' );?>/api/print-base64${data.registerOpen.length > 0 ? data.registerOpen : '' }`;
        data.base64PrinterName      =   `<?php echo store_option( 'printer_takeway' );?>`;
        return data;
    }
</script>
<script>
    const CashDrawerVue     =   new Vue({
        el: '#drawer-vue',
        data: { ...cashDrawerData() },
        mounted() {
        },
        methods : {
            openCashDrawer() {
                let url     =   this.printServerURL + `/api/open-cash-drawer`;
                let printer;

                if ( this.printGateway === 'nexo_print_server' ) {
                    url         =   this.printServerURL + `/api/open-cash-drawer`;
                    printer     =   this.posPrinter;
                } else if ( this.printGateway === 'register_nps' ) {
                    url         =   this.registerURL + `/api/open-cash-drawer`;
                    printer     =   this.registerPrinter;
                }

                console.log( printer, url );

                HttpRequest.post( url, { printer }).then( results => {
                    NexoAPI.Toast()( this.textDomain.cashDrawerOpening );
                }).catch( error => {
                    NexoAPI.Toast()( this.textDomain.cashDrawerErrorWhileOpening );
                })
            }
        }
    })
</script>