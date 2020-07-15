<?php 
/**
 * Load global values
 */
global  $Options, 
        $current_register, 
        $order;
if ( true == false ):?>
<script>
<?php endif;?>

v2Checkout.paymentWindow.hideSplash();
v2Checkout.paymentWindow.close();

const registerOpen          =   '<?php echo store_option( 'nexo_use_cashrawer', 'no' ) === 'yes' ? '?cash-drawer-action=open' : '';?>';
const posPrinter            =   `<?php echo store_option( 'nexo_pos_printer' );?>`;
const printServerURL        =   `<?php echo store_option( 'nexo_print_server_url' );?>`;
const registerURL           =   `<?php echo $current_register[ 'NPS_URL' ];?>`;
const registerPrinter       =   `<?php echo $current_register[ 'ASSIGNED_PRINTER' ];?>`;
const totalPrint            =   <?php echo intval( store_option( 'nexo_nps_print_copies', 1 ) );?>;
const autoPrintEnabled      =   <?php echo store_option( 'nexo_enable_autoprint', 'yes' ) === 'yes' ? 'true' : 'false';?>;
const printGateway          =   '<?php echo store_option( 'nexo_print_gateway', 'normal_print' );?>';
const printBase64           =   <?php echo store_option( 'nps_print_base64', 'no' ) === 'no' ? 'false' : 'true';?>;
const base64URL             =   `<?php echo store_option( 'nexo_print_server_url' );?>/api/print-base64${registerOpen.length > 0 ? registerOpen : '' }`;
const base64PrinterName     =   `<?php echo store_option( 'printer_takeway' );?>`;


const sendToNPS         =   ({ template, printer, registerOpen, printServerURL }) => {
    return new Promise( ( resolve, reject) => {
        $.ajax( printServerURL + '/api/print' + registerOpen, {
            type  	:	'POST',
            data 	:	{
                'content' 	:	template,
                'printer'	:	printer
            },
            dataType 	:	'json',
            success 	:	function( result ) {
                NexoAPI.Toast()( `<?php echo __( 'Tâche d\'impression soumisse', 'nexo' );?>` );
                resolve({ 
                    print: true,
                    template
                });
            },
            error		:	() => {
                NexoAPI.Notify().warning(
                    `<?php echo __( 'Impossible d\'imprimer', 'nexo' );?>`,
                    `<?php echo __( 'NexoPOS n\'a pas été en mesure de se connecter au serveur d\'impression ou ce dernier à retourner une erreur inattendue.', 'nexo' );?>`
                );
                reject({
                    print: false,
                    template
                })
            }
        });
    })
}

if( _.isObject( returned ) ) {
    // Init Message Object
    var MessageObject	=	new Object;
    var data	        =	NexoAPI.events.applyFilters( 'test_order_type', [ ( returned.order_type == 'nexo_order_comptant' ), returned ] );
    var test_order	    =	data[0];

    if( test_order == true ) {
        if( NexoAPI.events.applyFilters( 'cart_enable_print', autoPrintEnabled ) ) {

            MessageObject.title	=	`<?php echo _s('Effectué', 'nexo');?>`;
            MessageObject.msg	=	`<?php echo _s('La commande est en cours d\'impression.', 'nexo');?>`;
            MessageObject.type	=	'success';

            if ( printGateway === 'normal_print' ) {
                $( '#receipt-wrapper' ).remove();
                $( 'body' ).append( '<iframe id="receipt-wrapper" style="visibility:hidden;height:0px;width:100%;position:absolute;top:0;" src="<?php echo $this->events->apply_filters( 'normal_print_receipt_url', dashboard_url([ 'orders', 'receipt' ]) );?>/' + returned.order_id + '?refresh=true&autoprint=true"></iframe>' );
                NexoAPI.events.doAction( 'nexo_print_complete', {
                    printer: 'normal',
                    returned
                });
            } else if ( printGateway === 'nexo_print_server' ) {

                if ( ! printBase64 ) {
                    $.ajax( `<?php echo dashboard_url([ 'local-print' ]);?>` + '/' + returned.order_id, {
                        success 	:	function( template ) {
                            let run     =   async () => {
                                const results     =   [];
                                for( let index = 0; index < totalPrint; index++ ) {
                                    let result  =   await sendToNPS({
                                        template, 
                                        printer : posPrinter,
                                        registerOpen,
                                        printServerURL
                                    });
                                    results.push( result );
                                }

                                NexoAPI.events.doAction( 'nexo_print_complete', {
                                    returned,
                                    printer: posPrinter,
                                    results
                                });
                            }
                            run();
                        }
                    });
                } else {
                    const triggerPrint  =   async function() {
                        for( let index = 0; index < totalPrint; index++ ) {
                            await new Promise( ( resolve, reject ) => {
                                HttpRequest.get( `<?php echo dashboard_url([ 'orders', 'receipt' ]);?>/${returned.order_id}/base64?refresh=true&ignore_header=true&white=true` ).then( result => {
                                    $( '#print-base64' ).remove();
                                    $( 'body' ).append( `<div id="print-base64" style="width:580px">${result.data}</div>` );
                                    console.log( 'start printing' );
                                    html2canvas( document.getElementById( 'print-base64' ), {
                                        logging: true
                                    }).then(function(canvas) {
                                        const image     =   canvas.toDataURL();
                                        HttpRequest.post( base64URL, {
                                            'base64' 	:	image,
                                            'printer'	:	posPrinter
                                        }).then( results => {
                                            $( '#print-base64' ).remove();
                                            console.log( 'finished printing' );
                                            NexoAPI.events.doAction( 'nexo_print_complete', {
                                                returned,
                                                printer: posPrinter,
                                                results
                                            });
                                            resolve( true );
                                        }).catch( error => {
                                            console.log( 'failed printing' );
                                            $( '#print-base64' ).remove();
                                            reject( false );
                                        })
                                    }).catch( err => console.log( err ) );
                                });
                            })
                        }
                    }
                    triggerPrint();
                }
            } else if ( printGateway === 'register_nps' ) {
                <?php if ( empty( $current_register[ 'ASSIGNED_PRINTER' ] ) || ! filter_var( $current_register[ 'NPS_URL' ], FILTER_VALIDATE_URL ) ):?>
                    NexoAPI.Notify().warning(
                        `<?php echo __( 'Impossible d\'imprimer', 'nexo' );?>`,
                        `<?php echo __( 'Aucune imprimante n\'est assignée à la caisse enregistreuse ou l\'URL du serveur d\'impression est incorrecte.', 'nexo' );?>`
                    );
                <?php else:?>

                $.ajax( '<?php echo dashboard_url([ 'local-print' ]);?>' + '/' + returned.order_id, {
                    success 	:	( template ) => {
                        let run     =   async () => {
                            results     =   [];
                            for( let index = 0; index < totalPrint; index++ ) {
                                let result  =   await sendToNPS({
                                    template, 
                                    printer: registerPrinter,
                                    registerOpen,
                                    printServerURL: registerURL
                                });
                                results.push( result );
                            }

                            NexoAPI.events.doAction( 'nexo_print_complete', {
                                returned,
                                printer: registerPrinter
                            });
                        }
                        run();
                    }
                });

                <?php endif;?>
            } else {
                NexoAPI.events.doAction( 'nexopos_handle_print', {
                    MessageObject,
                    returned
                });
            }
        } else {
            NexoAPI.events.doAction( 'nexo_print_complete', {
                printer: 'disabled',
                returned
            });
        }
        
        // Remove filter after it's done
        NexoAPI.events.removeFilter( 'cart_enable_print' );
        MessageObject.title	=	'<?php echo _s('Effectué', 'nexo');?>';
        MessageObject.msg	=	'<?php echo _s('La commande a été enregistrée.', 'nexo');?>';
        MessageObject.type	=	'success';

    } else if ( test_order != null ) { // let the user customize the response
        if( data[1].message != undefined ) {
            MessageObject.title	=	'<?php echo _s('Une erreur s\'est produite', 'nexo');?>';
            MessageObject.msg	=	data[1].message;
            MessageObject.type	=	data[1].status === 'failed' ? 'danger' : data[1].status;
        } else {
            <?php if (@$Options[ store_prefix() . 'nexo_enable_autoprint' ] == 'yes'):?>
            MessageObject.title	=	'<?php echo _s('Effectué', 'nexo');?>';
            MessageObject.msg	=	'<?php echo _s('La commande a été enregistrée', 'nexo');?>';
            MessageObject.type	=	'info';
            <?php else:?>
            MessageObject.title	=	'<?php echo _s('Effectué', 'nexo');?>';
            MessageObject.msg	=	'<?php echo _s('La commande a été enregistrée', 'nexo');?>';
            MessageObject.type	=	'info';
            <?php endif;?>

            NexoAPI.events.doAction( 'nexo_print_complete', {
                returned
            });
        }
    }

    <?php if (@$Options[ store_prefix() . 'nexo_enable_smsinvoice' ] == 'yes'):?>
    /**
    * Send SMS
    **/
    // Do Action when order is complete and submited
    NexoAPI.events.doAction( 'is_cash_order', [ v2Checkout, returned ] );
    <?php endif;?>


    // Filter Message Callback
    // add filtred data to callback message
    var data				=	NexoAPI.events.applyFilters( 'callback_message', [ MessageObject, returned, data[0] ] );
    MessageObject		=	data[0];

    // For Success
    if( MessageObject.type == 'success' ) {
        NexoAPI.Toast()( MessageObject.msg );
    } else if( MessageObject.type == 'info' ) {
        NexoAPI.Toast()( MessageObject.msg );
    } else if ( MessageObject.type == 'danger' ) {
        NexoAPI.Notify().warning( MessageObject.title, MessageObject.msg );
    }
}
v2Checkout.resetCart();