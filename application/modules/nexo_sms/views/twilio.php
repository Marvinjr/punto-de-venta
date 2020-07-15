<script>
NexoAPI.events.addFilter( 'test_order_type', function( data ) {
	console.log( data, NexoSMS );
	if( NexoSMS.__SendSMSInvoice == true && [ 'nexo_order_comptant' ].indexOf( data[1].order_type ) !== -1 ) {
		if( NexoSMS.__CustomerNumber != '' ) {

			var v2Checkout		=	data[0];
			var order_details	=	data[1];
			var ItemsDetails	=	v2Checkout.CartTotalItems + '<?php echo _s(': produit(s) acheté(s)', 'nexo_sms');?>';

			_.templateSettings = {
			  interpolate: /\{\{(.+?)\}\}/g
			};

			var	message			=	_.template( `<?php echo  store_option( 'nexo_sms_invoice_template' );?>` );

			var SMS_object		=	{
				'site_name'		:	`<?php echo  store_option( 'site_name' );?>`,
				'order_code'	:	order_details.order_code,
				'order_topay'	:	NexoAPI.DisplayMoney( v2Checkout.CartValue ),
				'name'			:	NexoSMS.__CustomerName
			};

			var SMS				=	message( SMS_object );

			var phones			=	[ NexoSMS.__CustomerNumber ];
			var from_number		=	'<?php echo  store_option( 'nexo_twilio_from_number' );?>';
			var	post_data		=	_.object( [ 'message', 'phones', 'from_number' ], [ SMS, phones, from_number ] );
			var twilioUrl		=	'<?php echo site_url(array( 'rest', 'twilio', 'send_sms' ));?>/';

			post_data[ 'account_sid' ] 		=	'<?php echo  store_option( 'nexo_twilio_account_sid' );?>';
			post_data[ 'account_token' ] 	=	'<?php echo  store_option( 'nexo_twilio_account_token' );?>';

			$.ajax( twilioUrl , {
				success	:	function( returned ) {
					if( _.isObject( returned ) ) {
						if( returned.status == 'success' ) {
							tendoo.notify.success( '<?php echo _s('La facture par SMS a été envoyée', 'nexo_sms');?>', '<?php echo _s('Un exemplaire de la facture a été envoyée au numéro spécifié.', 'nexo_sms');?>' );
						}
					}
				},
				error	:	function( returned ) {
					returned		=	$.parseJSON( returned.responseText );
					NexoAPI.Notify().warning( '<?php echo _s('Une erreur s\'est produite.', 'nexo_sms');?>', '<?php echo _s('Le serveur à renvoyé une erreur durant l\'envoi du SMS :', 'nexo_sms');?>' + returned.error.message );
				},
				type	:	'POST',
				data	:	post_data
			});
		} else {
			NexoAPI.Notify().warning( '<?php echo _s('Une erreur s\'est produite.', 'nexo_sms');?>', '<?php echo _s('Vous devez specifier un numéro de téléphone. La facture par SMS n\'a pas pu être envoyée.', 'nexo_sms');?>' );
		}
	}
	
	return data;
});


</script>