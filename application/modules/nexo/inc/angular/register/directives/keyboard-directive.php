<?php
global $Options;
 ?>
<script>
tendooApp.directive( 'keyboard', function(){

	HTML.body.add( 'angular-cache' );



	HTML.query( 'angular-cache' )
	.add( 'div.row.keyboard-separator-wrapper>div.keys-wrapper' )
	.add( 'div.keyboard-wrapper.row' )
	.each( 'style', 'padding:15px 0;' );

	HTML.query( '.keys-wrapper' )
	.each( 'ng-class', '{ \'col-md-12\' : hideSideKeys, \'col-md-9\' : ! hideSideKeys }' );

	for( var i = 7; i <= 9; i++ ) {
		HTML.query( '.keyboard-wrapper' )
		.add( 'div.col-lg-4.col-sm-4.col-xs-4>button.btn.btn-default.btn-block.input-' + i )
		.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
		.each( 'ng-click', 'keyinput( ' + i + ', inputName )' )
		.textContent	=	i;
	}



	for( var i = 4; i <= 6; i++ ) {
		HTML.query( '.keyboard-wrapper' )
		.add( 'div.col-lg-4.col-sm-4.col-xs-4>button.btn.btn-default.btn-block.input-' + i )
		.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
		.each( 'ng-click', 'keyinput( ' + i + ', inputName )' )
		.textContent	=	i;
	}

	for( var i = 1; i <= 3; i++ ) {
		HTML.query( '.keyboard-wrapper' )
		.add( 'div.col-lg-4.col-sm-4.col-xs-4>button.btn.btn-default.btn-block.input-' + i )
		.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
		.each( 'ng-click', 'keyinput( ' + i + ', inputName )' )
		.textContent	=	i;
	}

    HTML.query( '.keyboard-wrapper' )
	.add( 'div.col-lg-4.col-sm-4.col-xs-4.clear-long>button.btn.btn-default.btn-block.input-clear' )
	.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
	.each( 'ng-click', 'keyinput( "clear", inputName )' )
	.textContent	=	'C';

    HTML.query( '.keyboard-wrapper .clear-long' )
    .each( 'ng-show', 'hideButton[ "dot" ]' );

	HTML.query( '.keyboard-wrapper' )
	.add( 'div.col-lg-2.col-sm-4.col-xs-4.clear-small>button.btn.btn-default.btn-block.input-clear' )
	.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
	.each( 'ng-click', 'keyinput( "clear", inputName )' )
    .each( 'ng-hide', 'hideButton[ "dot" ]' )
	.textContent	=	'C';

    HTML.query( '.keyboard-wrapper .clear-small' )
    .each( 'ng-hide', 'hideButton[ "dot" ]' );

	HTML.query( '.keyboard-wrapper' )
	.add( 'div.col-lg-2.col-sm-4.col-xs-4.dot-button>button.btn.btn-default.btn-block.input-dot' )
	.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
	.each( 'ng-click', 'keyinput( ".", inputName )' )
	.textContent	=	'.';

    HTML.query( '.keyboard-wrapper .dot-button' )
    .each( 'ng-hide', 'hideButton[ "dot" ]' )
    .each( 'ng-hide', 'hideButton[ "dot" ]' );

	HTML.query( '.keyboard-wrapper' )
	.add( 'div.col-lg-4.col-sm-4.col-xs-4>button.btn.btn-default.btn-block.input-dot' )
	.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
	.each( 'ng-click', 'keyinput( 0, inputName )' )
	.textContent	=	'0';

	HTML.query( '.keyboard-wrapper' )
	.add( 'div.col-lg-4.col-sm-12.col-xs-12>button.btn.btn-default.btn-block.input-back' )
	.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
	.each( 'ng-click', 'keyinput( "back", inputName )' )
	.textContent	=	'←';

	HTML.query( '.keyboard-separator-wrapper' )
	.add( 'div.col-md-3.right-side-keyboard>div.row' )
	.each( 'style', 'padding:15px 0;' )

	HTML.query( '.right-side-keyboard' )
	.each( 'ng-hide', 'hideSideKeys' )

	<?php
	$keyShortcuts 		=	@$Options[ store_prefix() . 'keyshortcuts' ];
	$allKeys 			=	explode( '|', $keyShortcuts );
	if( $allKeys ) {
        if( ! empty( $allKeys[0] ) ) {
            foreach( $allKeys as $key ) {
    			?>
    			HTML.query( '.right-side-keyboard .row' )
    			.add( 'div.col-lg-12.col-sm-12.col-xs-12>button.btn.btn-info.btn-block.input-' + <?php echo $key;?> )
    			.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
    			.each( 'ng-click', 'keyinput( ' + <?php echo  $key;?> + ', inputName, true )' )
    			.textContent	=	<?php echo  $key;?>;
    			<?php
    		}
        }
	}
	 ?>

	var payBoxHTML		=	angular.element( 'angular-cache' ).html();

	angular.element( 'angular-cache' ).remove();

	const template 		=	`
	<div class="row keyboard-separator-wrapper">
		<div class="keys-wrapper col-md-9 col-sm-9 col-xs-9" ng-class="{ 'col-md-12' : hideSideKeys, 'col-md-9' : ! hideSideKeys }">
			<div class="keyboard-wrapper row" style="padding: 15px 0px;">
				<div class="col-lg-4 col-sm-4 col-xs-4"><button class="btn btn-default btn-block input-7"
						ng-click="keyinput( 7, inputName )"
						style="margin-bottom: 15px; line-height: 30px; font-size: 24px; font-weight: 800;">7</button></div>
				<div class="col-lg-4 col-sm-4 col-xs-4"><button class="btn btn-default btn-block input-8"
						ng-click="keyinput( 8, inputName )"
						style="margin-bottom: 15px; line-height: 30px; font-size: 24px; font-weight: 800;">8</button></div>
				<div class="col-lg-4 col-sm-4 col-xs-4"><button class="btn btn-default btn-block input-9"
						ng-click="keyinput( 9, inputName )"
						style="margin-bottom: 15px; line-height: 30px; font-size: 24px; font-weight: 800;">9</button></div>
				<div class="col-lg-4 col-sm-4 col-xs-4"><button class="btn btn-default btn-block input-4"
						ng-click="keyinput( 4, inputName )"
						style="margin-bottom: 15px; line-height: 30px; font-size: 24px; font-weight: 800;">4</button></div>
				<div class="col-lg-4 col-sm-4 col-xs-4"><button class="btn btn-default btn-block input-5"
						ng-click="keyinput( 5, inputName )"
						style="margin-bottom: 15px; line-height: 30px; font-size: 24px; font-weight: 800;">5</button></div>
				<div class="col-lg-4 col-sm-4 col-xs-4"><button class="btn btn-default btn-block input-6"
						ng-click="keyinput( 6, inputName )"
						style="margin-bottom: 15px; line-height: 30px; font-size: 24px; font-weight: 800;">6</button></div>
				<div class="col-lg-4 col-sm-4 col-xs-4"><button class="btn btn-default btn-block input-1"
						ng-click="keyinput( 1, inputName )"
						style="margin-bottom: 15px; line-height: 30px; font-size: 24px; font-weight: 800;">1</button></div>
				<div class="col-lg-4 col-sm-4 col-xs-4"><button class="btn btn-default btn-block input-2"
						ng-click="keyinput( 2, inputName )"
						style="margin-bottom: 15px; line-height: 30px; font-size: 24px; font-weight: 800;">2</button></div>
				<div class="col-lg-4 col-sm-4 col-xs-4"><button class="btn btn-default btn-block input-3"
						ng-click="keyinput( 3, inputName )"
						style="margin-bottom: 15px; line-height: 30px; font-size: 24px; font-weight: 800;">3</button></div>
				<div class="col-lg-4 col-sm-4 col-xs-4 clear-long ng-hide" ng-show="hideButton[ &quot;dot&quot; ]"><button
						class="btn btn-default btn-block input-clear" ng-click="keyinput( &quot;clear&quot;, inputName )"
						style="margin-bottom: 15px; line-height: 30px; font-size: 24px; font-weight: 800;">C</button></div>
				<div class="col-lg-2 col-sm-4 col-xs-4 clear-small" ng-hide="hideButton[ &quot;dot&quot; ]"><button
						class="btn btn-default btn-block input-clear" ng-click="keyinput( &quot;clear&quot;, inputName )"
						ng-hide="hideButton[ &quot;dot&quot; ]"
						style="margin-bottom: 15px; line-height: 30px; font-size: 24px; font-weight: 800;">C</button></div>
				<div class="col-lg-2 col-sm-4 col-xs-4 dot-button" ng-hide="hideButton[ &quot;dot&quot; ]"><button
						class="btn btn-default btn-block input-dot" ng-click="keyinput( &quot;.&quot;, inputName )"
						style="margin-bottom: 15px; line-height: 30px; font-size: 24px; font-weight: 800;">.</button></div>
				<div class="col-lg-4 col-sm-4 col-xs-4"><button class="btn btn-default btn-block input-dot"
						ng-click="keyinput( 0, inputName )"
						style="margin-bottom: 15px; line-height: 30px; font-size: 24px; font-weight: 800;">0</button></div>
				<div class="col-lg-4 col-sm-12 col-xs-12"><button class="btn btn-default btn-block input-back"
						ng-click="keyinput( &quot;back&quot;, inputName )"
						style="margin-bottom: 15px; line-height: 30px; font-size: 24px; font-weight: 800;">←</button></div>
			</div>
		</div>
		<div class="col-md-3 col-sm-3 col-xs-3 right-side-keyboard" ng-hide="hideSideKeys">
			<div class="row" style="padding: 15px 0px;">
			<?php
			$keyShortcuts 		=	@$Options[ store_prefix() . 'keyshortcuts' ];
			$allKeys 			=	explode( '|', $keyShortcuts );
			if( $allKeys ) {
				if( ! empty( $allKeys[0] ) ) {
					foreach( $allKeys as $key ) {
					?>
					<div class="col-lg-12 col-sm-12 col-xs-12">
						<button 
							style="margin-bottom: 15px;line-height: 30px;font-size: 24px;font-weight: 800;"
							ng-click="keyinput( <?php echo  floatval( $key );?>, inputName, true )" 
							class="btn btn-info btn-block input-<?php echo $key;?>">
							<?php echo $key;?>
						</button>
					</div>
					<?php
					}
				}
			}
			?>
			</div>
		</div>
	</div>
	`

	return {
		restrict	:	'E',
		scope		:	{
			keyinput	:	'=',
            hideSideKeys    :   '=',
            hideButton      :   '='
		},
		link		:	function( scope, element, attrs ) {
			scope.inputName					=	attrs.inputName
		},
		template
	}
} );
</script>
