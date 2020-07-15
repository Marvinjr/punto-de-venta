<?php global $Options;?>
<script>
let checkoutHeaderCTRL      =   function( $scope, hotkeys ) {
    /**
     * GoTo
     * @param string url
     * @return void
    **/
    
    $scope.goTo = function( url ){
        document.location   =   url;
    }

    /**
	 * Open FullScreen
	**/

	$scope.openFullScreen		=	function(){
		toggleFullScreen();
		setTimeout( function(){
			if( v2Checkout.CompactMode == true ) {
				v2Checkout.fixHeight(true);
			} else {
				v2Checkout.toggleCompactMode(true);
			}
			NexoAPI.events.doAction( 'toggle_fullscreen' );	
		}, 200 );
	}

	/**
	 * Logout
	 */
	$scope.signOut 		=	function() {
		document.location 	=	'<?php echo site_url([ 'sign-out' ]);?>';
	}

	/**
	 *  Open Calculator
	**/
	
	$scope.openCalculator 		=	function(){

		let dom 	= 	`
		<div class="row calculator-dom">
			<div class="col-lg-3 col-md-4 col-xs-7 col-sm-5 calculator-container">
				<div class="box" style="box-shadow:0px 2px 5px 1px #969696;">
					<div class="box-body">
						<div class="calculator">
							<input type="text" readonly>
							<div class="row">
								<div class="col-md-12 col-xs-12 col-sm-12">
									<div class="key">1</div>
									<div class="key">2</div>
									<div class="key">3</div>
									<div class="key last">0</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 col-xs-12 col-sm-12">
									<div class="key">4</div>
									<div class="key">5</div>
									<div class="key">6</div>
									<div class="key last action instant">cl</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 col-xs-12 col-sm-12">
									<div class="key">7</div>
									<div class="key">8</div>
									<div class="key">9</div>
									<div class="key last action instant">=</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 col-xs-12 col-sm-12">
									<div class="key action">+</div>
									<div class="key action">-</div>
									<div class="key action">x</div>
									<div class="key last action">/</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		`;

		if( $( '.calculator-dom' ).length == 0 ) {
			if( 
				layout.is().includes( 'xs' ) || 
				layout.is().includes( 'sm' ) || 
				layout.is().includes( 'md' ) 
			) {
				$( 'body' ).append( dom );
				$( '.calculator-container .box-body' ).append(
					`
					<span class="calculator-close-button"><i class="fa fa-times"></i></span>
					`
				)
				$( '.calculator-dom' ).append(
					`
					<style>
					.calculator-dom {
						width: 100%;
						height: 100%;
						top: 0;
						z-index: 9999;
						position: absolute;
						background: rgba(0, 0, 0, 0.5 );
					}
					.calculator-container {
						position: absolute;
						top: 25%;
						left: 25%;
						width: 337px;
						z-index: 9;
					}
					.calculator-close-button {
						border-radius: 20px;
						background: #ff681d;
						float: left;
						padding: 10px 15px;
						font-weight: 600;
						color: #FFF;
						border: solid 1px #ea4e00;
						box-shadow: 0 0 3px 0px #8c8c8c;
						position: absolute;
						left: -22px;
						top: -22px;
					}
					</style>
					`
				)
			} else {
				$( '[ng-click="openCalculator()"]' ).parent().append( dom );
				$( '.calculator-dom' ).append(
					`
					<style>
					.calculator-container {
						position: absolute;
						top: 40px;
						left: 46px;
						width: 337px;
						z-index: 9;
					}
					</style>
					`
				)
			}

			$( '.calculator-close-button' ).bind( 'click',  $scope.openCalculator );

		} else {
			$( '.calculator-dom' ).remove();
		}

		new NexoCalculator();
	}

	$( document ).bind( 'click', function( e ) {
		// if the calculator is open
		if( $( '.calculator-dom' ).length == 1 ) {
			if( $( e.target ).closest( '.calculator-dom' ).length == 0 && ! $( e.target ).hasClass( 'calculator-button' ) ) {
				$scope.openCalculator(); // we want to close the calculator
			}

			if ( $( e.target ).hasClass( 'calculator-dom' ) ) {
				$scope.openCalculator(); 
			}
		}		
	})

	hotkeys.add({
		combo: '<?php echo @$Options[ 'toggle_fullscreen' ] == null ? "shift+0" : @$Options[ 'toggle_fullscreen' ];?>',
		description: 'Open order Note',
		allowIn: ['INPUT', 'SELECT', 'TEXTAREA'],
		callback: function() {
			$scope.openFullScreen();
		}
	});
}
checkoutHeaderCTRL.$inject  =   [ '$scope', 'hotkeys' ];
tendooApp.controller( 'checkoutHeaderCTRL', checkoutHeaderCTRL );
</script>
<script type="text/javascript" src="<?php echo module_url( 'nexo' ) . '/js/calculator.js';?>"></script>
<script>
const v2NotificationData = {
	textDomain: {
		itemAdded: `<?php echo __( 'Le produit a été ajouté au panier', 'nexo' );?>`
	}
}
</script>
<script type="text/javascript" src="<?php echo module_url( 'nexo' ) . '/js/v2-1.notification.js';?>"></script>