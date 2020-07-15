<script>
const accountPaymentData 	=	{
	textDomain: {
		creditLimit: `<?php echo __( 'Limite Crédit', 'nexo' );?>`,
		customerAccount: `<?php echo __( 'Compte Client', 'nexo' );?>`,
		customerHistory: `<?php echo __( 'Historique Client', 'nexo' );?>`,
		creditBalance: `<?php echo __( 'Solde Credit', 'nexo' );?>`,
		addCreditPayment: `<?php echo __( 'Ajouter', 'nexo' );?>`,
		creditAdded: `<?php echo __( 'Crédit ajouté', 'nexo' );?>`,
		cannotAddThisAsPayment: `<?php echo __( 'Impossible d\'ajouter un paiement. Veuillez choisir un type de paiement différent', 'nexo' );?>`,
		clear: `<?php echo __( 'Effacer', 'nexo' );?>`,
		invalidCreditValue: `<?php echo __( 'Montant du crédit invalide.', 'nexo' );?>`,
		cantProceed: `<?php echo __( 'Impossible de continuer', 'nexo' );?>`,
		priorPaymentRequested: `<?php echo __( 'Avant d\'effectuer un crédit, la commande doit être payée au moins à hauteur de {requested}', 'nexo' );?>`,
		accountPaymentAlreadyMade: `<?php echo __( 'Vous ne pouvez pas effectuer un autre paiement à crédit, car un précédent paiement similare à déjà été fait.', 'nexo' );?>`,
		creditReachLimit: `<?php echo __( 'Le total de crédit accordé au client dépasse la limite qui lui est accordée.', 'nexo' );?>`,
		creditExceedLimit: `<?php echo __( 'Le crédit dépasse la limite accordée au client sélectionné.', 'nexo' );?>`,
		creditNotAllowed: `<?php echo __( 'Le paiement à crédit à été désactivé pour le client sélectionné.', 'nexo' );?>`,
		hasReachedTheLimit: `<?php echo __( 'Le client à atteint la limite de crédit qui lui a été accordée.', 'nexo' );?>`,
		creditWillTurnNegativeAccount: `<?php echo __( 'Le paiement à crédit a été désactivé dans les réglages ou pour le client sélectionné. Impossible d\'effectuer un paiement sans provision', 'nexo' );?>`
	}, 
	options : {
		allow_negative_credit: '<?php echo store_option( 'allow_negative_credit', 'no' );?>',
		customer_requested_prior_payment: '<?php echo store_option( 'customer_requested_prior_payment', 'disable' );?>'
	}
};

let AccountVueApp;

NexoAPI.events.addAction( 'pos_select_payment', ([ scope, namespace ]) => {
	if ( AccountVueApp.$children.length > 0 ) {
		AccountVueApp.$children[0].changePayment( namespace );
	}
});

NexoAPI.events.addAction( 'pay_box_loaded', () => {
	$( '.tab-account' ).append( '<div id="account-payment-container" style="height: 100%"></div>' );
	$( 'account-payment' ).appendTo( '#account-payment-container' );
	Vue.filter( 'moneyFormat', ( amount ) => {
		return NexoAPI.DisplayMoney( amount );
	});
	Vue.component( 'account-payment', {
		template: `
		<div id="account-payment-wrapper" v-if="isActive">
			<div class="warning-box" v-if="customer.ALLOW_CREDIT ==='no'">
				<h4 class="text-center">{{ textDomain.creditNotAllowed }}</h4>
			</div>
			<div class="warning-box" v-else-if="hasReachedTheLimit">
				<h4 class="text-center">{{ textDomain.hasReachedTheLimit }}</h4>
			</div>
			<div v-else class="account-body" style="display: flex;flex-direction: column;">
				<h3 
					class="text-center hidden-xs hidden-sm" 
					style="margin: 15px 0px;">
						{{ textDomain.customerAccount }}
					<span class="status"></span>
				</h3>
				<div class="row" style="flex: 1;display: flex;justify-content: center;">
					<div class="col-md-8 col-sm-12 col-xs-12" style="display: flex;flex-direction: column;flex: 1;">
						<h4>
							<span class="pull-left">{{ textDomain.creditLimit }} : {{ customer.CREDIT_LIMIT | moneyFormat }}</span>
							<span class="pull-right">{{ textDomain.creditBalance }} : {{ customer.TOTAL_CREDIT | moneyFormat }}</span>
						</h4>
						<div class="bootstrapiso" style="display: flex; flex: 1;background: #EEE;box-shadow: 1px 1px 2px 0px #cacaca;border: solid 1px #a9a9a9;">
							<num-keyboard @value="handleNewValue($event)" :screen="amount" @press="handledPress( $event )">
								<template v-slot:first>
									<button @click="handlePress(7)" type="button" class="btn btn-outline-secondary border-bottom-0 border-top-0 rounded-0 border-left-0">
										<h1 class="m-0">7</h1>
									</button>
									<button @click="handlePress(8)" type="button" class="btn btn-outline-secondary border-bottom-0 border-top-0">
										<h1 class="m-0">8</h1>
									</button>
									<button @click="handlePress(9)" type="button" class="btn btn-outline-secondary border-bottom-0 border-top-0 rounded-0">
										<h1 class="m-0">9</h1>
									</button>
									<button  @click="handlePress('backspace')"  type="button" class="btn btn-outline-secondary border-bottom-0 border-top-0 rounded-0 border-right-0">
										<h1 class="m-0"><i class="fa fa-arrow-left"></i></h1>
									</button>
								</template>
								<template v-slot:second>
									<button @click="handlePress(4)" type="button" class="btn btn-outline-secondary border-bottom-0 rounded-0 border-left-0">
										<h1 class="m-0">4</h1>
									</button>
									<button @click="handlePress(5)" type="button" class="btn btn-outline-secondary border-bottom-0">
										<h1 class="m-0">5</h1>
									</button>
									<button @click="handlePress(6)" type="button" class="btn btn-outline-secondary border-bottom-0 rounded-0">
										<h1 class="m-0">6</h1>
									</button>
									<button @click="handlePress('clear')" type="button" class="btn btn-outline-secondary border-bottom-0 rounded-0 border-right-0">
										<h1 class="m-0">{{ textDomain.clear }}</h1>
									</button>
								</template>
								<template v-slot:third>
									<button @click="handlePress(1)" type="button" class="btn btn-outline-secondary border-bottom-0 rounded-0 border-left-0">
										<h1 class="m-0">1</h1>
									</button>
									<button @click="handlePress(2)" type="button" class="btn btn-outline-secondary border-bottom-0">
										<h1 class="m-0">2</h1>
									</button>
									<button @click="handlePress(3)" type="button" class="btn btn-outline-secondary border-bottom-0 rounded-0">
										<h1 class="m-0">3</h1>
									</button>
									<button type="button" class="btn disabled btn-outline-secondary border-bottom-0 rounded-0 border-right-0">
										<h1 class="m-0"></h1>
									</button>
								</template>
								<template v-slot:fourth>
									<button type="button" class="btn disabled btn-outline-secondary border-bottom-0 rounded-0 border-left-0">
										<h1 class="m-0"></h1>
									</button>
									<button @click="handlePress(0)" type="button" class="btn btn-outline-secondary border-bottom-0">
										<h1 class="m-0">0</h1>
									</button>
									<button @click="handlePress('.')" type="button" class="btn btn-outline-secondary border-bottom-0 rounded-0">
										<h1 class="m-0">.</h1>
									</button>
									<button @click="handlePress('enter')" type="button" class="btn btn-outline-secondary border-bottom-0 rounded-0 border-right-0">
										<h1 class="m-0">{{ textDomain.addCreditPayment }}</h1>
									</button>
								</template>
							</num-keyboard>
						</div>
					</div>
				</div>
			</div>
		</div>
		`,
		data: () => {
			return {
				...accountPaymentData,
				customer: null,
				amount: '0',
				isActive : false,
				zeroTyped: false
			}
		}, 
		methods: {
			changePayment( namespace ) {
				this.isActive 		=	false;
				if ( namespace === 'account' ) {
					this.isActive 	=	true;
					setTimeout( () => {
						$( '.bootstrapiso input[type="text"]' ).first().select();
					}, 200 );
				}
			},
			handlePress( button ) {
				if ([ 'ok' ].includes( button ) ) {
				} else if ([ 'enter' ].includes( button )) {
					this.handleCreditSubmittion();
				} else if ([ 'backspace' ].includes( button ) ) {
					
					if( this.amount.length > 0 ) {
						/**@type {string} */
						this.amount     =   this.amount.substr( 0, this.amount.length - 1 );
	
						if ( this.amount.substr(-1, 1) === '.' ) {
							this.amount     =  this.amount.substr( 0, this.amount.length - 1 );
							this.zeroTyped 	=	false;
						}
					}

				} else if( button === 'clear' ) {
					this.amount     =   '';
				} else if( button === '.' && this.amount.split('').filter( char => char === '.' ).length === 0 ) {
					if ( this.amount === '' ) {
						this.amount     +=  '0.0';
					} else {
						this.amount     +=  '.0';
					}
				} else if( button !== '.' ) {

					/**
					 * handle decimals
					 */
					if ( this.amount.substr(-2, 2) === '.0' ) {
						if ( this.zeroTyped ) {
							this.amount 	+=	button;
						} else if ( parseInt( button ) > 0 ) {
							this.amount     =   this.amount.substr( 0, this.amount.length - 1 ) + button;
						} else {
							this.zeroTyped 	=	true;
						}
					} else if ( this.amount === '0' ) {
						this.amount     =  button.toString();
					} else {
						this.amount     +=  button
					}

					if ( this.mode === 'percentage' && parseFloat( this.amount ) > 100 ) {
						this.amount     =   '100';
					}
				} 
			},

			handleNewValue( value ) {
				this.amount 	=	value;
			},

			handleCreditSubmittion() {

				/**
				 * let's avoid double 
				 * account payment 
				 */
				const hasAccountPayment 	=	PayBoxController
					.prototype
					.scope
					.paymentList
					.filter( payment => payment.namespace === 'account' )
					.length > 0;

				if ( hasAccountPayment ) {
					return swal({
						title: this.textDomain.cantProceed,
						type: 'error',
						text: this.textDomain.accountPaymentAlreadyMade
					});
				}
								
				/**
				 * let's check a prior 
				 * payment made on the current order
				 */
				const overAllPayment 	=	PayBoxController
					.prototype
					.scope
					.paymentList
					.map( payment => parseFloat( payment.amount ) );

				if ( this.options.customer_requested_prior_payment !== 'disable' ) {

					const percent 				=	parseFloat( this.options.customer_requested_prior_payment );
					const minPaymentRequested 	=	( percent * parseFloat( v2Checkout.CartToPay ) ) / 100;

					if( overAllPayment.length == 0 ) {
						return swal({
							title: this.textDomain.cantProceed,
							type: 'error',
							text: this.textDomain.priorPaymentRequested.replace( '{requested}', NexoAPI.DisplayMoney( minPaymentRequested ) )
						});
					}
				}

				/**
				 * credit should turn the customer
				 * account into negative
				 */
				if ( parseFloat( this.customer.TOTAL_CREDIT ) - parseFloat( this.amount ) < 0 && ( this.options.allow_negative_credit === 'no' || this.customer.ALLOW_CREDIT === 'no' ) ) {
					return swal({
						title: this.textDomain.cantProceed,
						type: 'error',
						text: this.textDomain.creditWillTurnNegativeAccount
					});
				}

				/**
				 * credit exceed credit limit
				 */
				if ( parseFloat( this.amount ) > parseFloat( this.customer.CREDIT_LIMIT ) && parseFloat( this.customer.CREDIT_LIMIT ) != 0 ) {
					return swal({
						title: this.textDomain.cantProceed,
						type: 'error',
						text: this.textDomain.creditExceedLimit
					});
				}

				if ( parseFloat( this.customer.TOTAL_CREDIT ) + parseFloat( this.amount ) > parseFloat( this.customer.CREDIT_LIMIT ) && parseFloat( this.customer.CREDIT_LIMIT ) != 0 ) {
					return swal({
						title: this.textDomain.cantProceed,
						type: 'error',
						text: this.textDomain.creditReachLimit
					});
				}

				if ( parseFloat( this.amount ) === 0 ) {
					return swal({
						title: this.textDomain.cantProceed,
						type: 'error',
						text: this.textDomain.invalidCreditValue
					});
				}

				PayBoxController.prototype.scope.addPayment( 'account', parseFloat( this.amount ) );
				this.amount 	=	'';
				NexoAPI.Toast()( this.textDomain.creditAdded );
			}
		},
		mounted() {
			this.customer 	=	v2Checkout.customers.list.filter( _customer => parseInt( _customer.ID ) === parseInt( v2Checkout.CartCustomerID ) )[0];			

			NexoAPI.events.addFilter( 'allow_payment', ({ proceed, payment_namespace, payment_amount, meta }) => {
				let message     =  false;

				// if ( payment_namespace === 'account' ) {
				// 	proceed     =   false;
				// 	message     =   this.textDomain.cannotAddThisAsPayment;
				// }

				return { proceed, payment_namespace, payment_amount, meta, message };
			});
		},
		computed: {
			payments() {
				const payments 			=	Object.values( v2Checkout.paymentTypesObject ).map(( payment, index ) => {
					payment.namespace 	=	Object.keys( v2Checkout.paymentTypesObject )[ index ];
					return payment;
				});
				return payments;
			}, 
			accountPayment() {
				console.log( this.payments );
				return this.payments.filter( payment => payment.namespace === 'account' )[0];					
			},
			hasReachedTheLimit() {
				if ( parseFloat( this.customer.TOTAL_CREDIT ) < 0 ) {
					return (
						parseFloat( this.customer.TOTAL_CREDIT ) > parseFloat( this.customer.CREDIT_LIMIT )
					) && parseFloat( this.customer.CREDIT_LIMIT ) > 0;
				} else {
					return false;
				}
			}
		}
	});
	AccountVueApp 	=	new Vue({ el: '#account-payment-container' });
});
</script>
<style>
.warning-box {
	display: flex;
    align-items: center;
    flex-direction: column;
    align-self: center;
    width: 100%;
}
.tab-account {
	height: 100%;
}
#account-payment-wrapper {
	height: 100%;
    display: flex;
    flex-direction: row;
}
.account-body {
	width: 100%;
}
</style>