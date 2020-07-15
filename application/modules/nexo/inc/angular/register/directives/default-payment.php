<script>
tendooApp.directive( 'defaultPayment', function(){

	const template 	=	`
	<h3 
    class="text-center hidden-xs hidden-sm" 
    style="margin: 15px 0px;">
    {{ defaultSelectedPaymentText }}
    <span class="status ng-hide" ng-show="editModeEnabled"></span></h3>
	<div class="input-group input-group-lg payment-field-wrapper">
		<span class="input-group-addon hidden-sm hidden-xs"><?php echo _s( 'Montant du paiement', 'nexo' );?></span>
		<input class="form-control ng-pristine ng-untouched ng-valid ng-empty" ng-model="paidAmount"
			ng-focus="bindKeyBoardEvent( $event )" placeholder="<?php echo _s( 'Définir un montant', 'nexo' );?>">
		<span class="input-group-btn paymentButtons">
			<button class="btn addPaymentButton btn-success"
				ng-click="addPayment( defaultSelectedPaymentNamespace, paidAmount )"
				ng-disabled="addPaymentDisabled">{{ defaultAddPaymentText }}</button><button class="btn btn-default ng-hide"
				ng-show="showCancelEditionButton" ng-click="cancelPaymentEdition()">
				<i class="fa fa-remove"></i>
			</button>
		</span>
	</div>
	<button class="btn btn-info fullCashButton" ng-click="fullPayment( defaultSelectedPaymentNamespace )" ng-disabled="addPaymentDisabled" style="margin-top: 20px; width: 100%; line-height: 45px; font-size: 40px;"><?php echo __( 'Paiement Intégrale', 'nexo' );?></button>
	`

	return {
		template,
		scope		:	{
			payment							:	'=',
			paidAmount						:	'=',
			addPayment						:	'=',
			bindKeyBoardEvent				:	'=',
			cancelPaymentEdition			:	'=',
			defaultAddPaymentText			:	'=',
			data 							:	'=',
			defaultAddPaymentClass			:	'=',
			defaultSelectedPaymentText		:	'=',
			defaultSelectedPaymentNamespace	:	'=',
			fullPaymentText 				:	'=',
			showCancelEditionButton			:	'=',
			fullPayment 					:	'='
		}
	}
});
</script>
