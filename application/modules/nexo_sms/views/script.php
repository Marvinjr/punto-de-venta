<?php global $Options;?>
<script type="text/javascript">
"use strict";
var NexoSMS			=	new Object;
	NexoSMS.__CustomerNumber	=	'';
	NexoSMS.__SendSMSInvoice	=	null;
	NexoSMS.__CustomerName		=	'';

/**
 * Set customer Number
**/

NexoAPI.events.addAction( 'select_customer', function( data ) {
	if( _.isObject( data ) ) {
		NexoSMS.__CustomerNumber		=	data[0].TEL;
		NexoSMS.__CustomerName			=	data[0].NOM
	}
});

/**
 * Display Toggle
**/

NexoAPI.events.addFilter( 'pay_box_footer', function( data ) {
	return 	data + '<input type="checkbox" <?php echo  store_option( 'nexo_enable_smsinvoice' )== 'yes' ? 'checked="checked"' : '';?> name="send_sms" send-sms-invoice data-toggle="toggle" data-width="150" data-height="35">';
}, 30 );

/**
 * Load Paybox
**/

NexoAPI.events.addAction( 'pay_box_loaded', function( data ) {
	$('[send-sms-invoice]').bootstrapToggle({
      on: '<?php echo _s('Activer les SMS', 'nexo_sms');?>',
      off: '<?php echo _s('Désactiver les SMS', 'nexo_sms');?>'
    });

	// Ask whether to change customer number

	$( '[send-sms-invoice]' ).bind( 'change', function(){
		if( typeof $(this).attr( 'checked' ) != 'undefined' ) {
			NexoAPI.Bootbox().prompt({
			  title: "<?php echo _s('Veuillez définir le numéro à utiliser pour la facture par SMS', 'nexo_sms');?>",
			  value: typeof NexoSMS.__CustomerNumber != 'undefined' ? NexoSMS.__CustomerNumber : '',
			  callback: function(result) {
				if (result !== null) {
				  NexoSMS.__CustomerNumber	=	result;
				}
			  }
			});
		}
	});
});

/**
 * Before Subiting order
**/

NexoAPI.events.addAction( 'submit_order', function() {
	NexoSMS.__SendSMSInvoice	=	typeof $( '[send-sms-invoice]').attr( 'checked' ) != 'undefined' ? true : false;
})

/**
 * When Cart is Reset
**/

NexoAPI.events.addAction( 'reset_cart', function( v2Checkout ) {
	NexoSMS.__CustomerNumber	=	'';
	NexoSMS.__SendSMSInvoice	=	null;
});
</script>

<?php if ( store_option( 'nexo_sms_service' ) === 'plivo'):?>
<?php include_once( dirname( __FILE__ ) . '/plivo.php' );?>
<?php endif;?>
<?php if (in_array('twilio', array_keys($this->config->item('nexo_sms_providers'))) &&  store_option( 'nexo_sms_service' ) == 'twilio'):?>
<?php include_once( dirname( __FILE__ ) . '/twilio.php' );?>
<?php elseif (in_array('bulksms', array_keys($this->config->item('nexo_sms_providers'))) &&  store_option( 'nexo_sms_service' ) == 'bulksms'):?>
<?php include_once( dirname( __FILE__ ) . '/bulk-sms.php' );?>
<?php endif;?>