<?php include_once( MODULESPATH . 'nexo/inc/angular/order-list/filters/money-format.php' );?>
<?php include_once( MODULESPATH . 'nexo/views/customers/import-parser.php' );?>
<script>
const stockTransfertTextDomain  =   {
    notEnoughStock: '<?php echo __( 'Not enough Stock. The remaining quantity has been used.', 'nexo' );?>',
    notEnoughStockCantProceed: `<?php echo __( 'Not enough Stock. The operation can\'t proceed.', 'nexo' );?>`,
    unableToFindTheItem: `<?php echo __( 'Unable to find the requested item.', 'nexo' );?>`,
    warning: `<?php echo __( 'Warning', 'stock-manager' );?>`,
    itemAdded: `<?php echo __( 'The product has been added.', 'stock-manager' );?>`,
    wrongExtension: `<?php echo __( 'The provided file doesn\'t have a valid extension. ".csv" file is expected.', 'stock-manager' );?>`,
    notEnoughData: `<?php echo __( 'The provided file doesn\'t have enough data', 'stock-manager' );?>`,
    unableToProceed: `<?php echo __( 'Unable to Proceed', 'stock-manager' );?>`,
    addedSuccessFully: `<?php echo __( 'Added Successfully', 'stock-manager' );?>`,
    anErrorOccuredWhileAdding: `<?php echo __( 'Error Occured while adding the item. The item might be missing.', 'stock-manager' );?>`,
    incorrectFileProvided: `<?php echo __( 'The file you\'ve provided is not valid.', 'stock-manager' );?>`,
}
var StockTransferCTRL   =   function( $scope, $http, $timeout ) {

    $scope.order       =   {
        title       : '',
        store       :   {},
        items       :   []
    };

    $scope.isRequesting     =   <?php echo isset( $_GET[ 'request' ] ) && $_GET[ 'request' ] === 'true' ? 'true': 'false';?>;
    $scope.textDomain       =   stockTransfertTextDomain;

    $scope.ajaxHeader		=	{
		'<?php echo $this->config->item('rest_key_name');?>'	    :	'<?php echo get_option( 'rest_key' );?>'
		// 'Content-Type'											: 	'application/x-www-form-urlencoded'
	}

    $scope.stores       =   <?php echo json_encode( $this->Nexo_Stores->get() );?>;

    <?php if( is_multistore() ):?>
    $scope.stores.unshift({
        ID      :   0,
        NAME    :   '<?php echo _s( 'Main Warehouse', 'stock-manager' );?>'
    });

    var indexToSplice;
    _.each( $scope.stores, function( store, key ) {
        if( parseInt( store.ID ) == <?php echo get_store_id();?> ) {
            indexToSplice    =   key;
        }
    });

    $scope.stores.splice( indexToSplice, 1 );
    <?php endif;?>

    $scope.$watch( 'barcode', function(){
        $scope.fetchItem( $scope.barcode );
    });

    $scope.__fetchItem  =   function( barcode ) {
        return $http.get( '<?php echo site_url( array( 'rest', 'nexo', 'item' ) );?>' + '/' + barcode + '/sku-barcode?<?php echo store_get_param( null );?>', {
			headers			:	{
				'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo get_option( 'rest_key' );?>'
			}
		});
    }

    /** 
     * Fetch item
    **/
    $scope.fetchItem    =   function( barcode, quantity = 1 ) {
        if( barcode == '' || typeof barcode == 'undefined' ) {
            return;
        }
        
        $scope.__fetchItem( barcode ).then(function( returned ){
			$scope.addToCart( returned.data[0], quantity );
            $scope.barcode      =   '';
            $( '.barcode-field' ).val( '' );
            $( '.barcode-field ' ).focus();
		}, function(){
            NexoAPI.Toast()( '<?php echo _s( 'Unable to find this product', 'stock-manager' );?>' );
            $( '.barcode-field' ).val( '' );
            $( '.barcode-field ' ).focus();
        });
    }

    $scope.productExists        =   function( item ) {
        
    }

    $scope.increaseQuantity     =   function( item, quantity = 1 ) {
        // make sure it's not a string
        quantity                =   parseFloat( quantity );
        let hasFound            =   false;
        let existingReturn      =   {};

        $scope.order.items.forEach( existingItem => {
            if ( existingItem.CODEBAR === item.CODEBAR ) {
                
                hasFound    =   true;

                /**
                 * let's update the existing item
                 */
                if ( ! $scope.isRequesting ) {
                    existingReturn      =   $scope.fnIncreaseQuantity({ item, quantity, existingItem });
                } else {
                    existingReturn      =   existingItem;
                }
            }
        });

        if ( ! $scope.isRequesting ) {
            /**
             * if the product has been found, 
             * it has been handled above
             */
            if ( ! hasFound ) {
                /**
                * We need to check if the 
                * quantity requested could be added. If not, we'll use
                * the existing item details and returns it udpated 
                * with the QTE_ADDED values
                */
                const newReturn   =   $scope.fnIncreaseQuantity({ item, quantity, existingItem : { ...item, QTE_ADDED : 0 }});

                $scope.order.items.push( newReturn.item );

                return newReturn;
            } else {
                return existingReturn;
            }
        } else {
            if ( ! hasFound ) {
                item.QTE_ADDED      =   quantity;
                $scope.order.items.push( item );
            } else {
                existingReturn.QTE_ADDED      +=  quantity
            }
            return { item, status: 'success', message : $scope.textDomain.itemAdded };
        }
    }

    $scope.fnIncreaseQuantity   =   function({ item, quantity, existingItem }) {
        /**
        * Let's compare and see if the remaining
        * quantity after the increase is negative of not
        */
        const remainAfterTransfer   =   parseFloat( item.QUANTITE_RESTANTE ) - ( existingItem.QTE_ADDED + quantity );
        
        if ( remainAfterTransfer <= 0 ) {
            NexoAPI.Toast()( $scope.textDomain.notEnoughStock );
            item.QTE_ADDED      =   parseFloat( item.QUANTITE_RESTANTE );
            return { item, message : $scope.textDomain.notEnoughStock, status : 'info' };
        } else {
            existingItem.QTE_ADDED   +=   quantity;
            return { item: existingItem, message : $scope.textDomain.itemAdded, status : 'success' };
        }
    }

    /**
     * Add to cart
     * @return void
    **/
    $scope.addToCart    =   function( item, qty = 1 ) {
        const increaseQtyResult     =   $scope.increaseQuantity( item, qty );
        console.log( increaseQtyResult );
        if ( ! increaseQtyResult ) {
            // check if a request is about to be
            // send, to verify the stock
            if ( ! $scope.isRequesting && ( parseFloat( item.QUANTITE_RESTANTE ) - qty ) < 0 ) {
                return NexoAPI.Toast()( $scope.textDomain.notEnoughStockCantProceed );
            }

            item.QTE_ADDED      =   parseFloat( qty );
            $scope.order.items.push( item );
        }
    }

    /**
     * Remove
     * @param int index
     * @return void
    **/

    $scope.remove           =   function( index ) {
        $scope.order.items.splice( index, 1 );
    }

    /**
     * Watch Item
     * save old qte_added
     * @param object
     * @return void
    **/

    $scope.watchItem        =   function( item ) {
        $scope.oldQte           =   parseFloat( item.QTE_ADDED );
        $scope.oldRemaining     =   parseFloat( item.QUANTITE_RESTANTE );
    }

    /**
     * Check Change
     * @param object item
     * @return void
    **/

    $scope.checkChange      =   function( item ) {
        if( isNaN( item.QTE_ADDED ) || parseFloat( item.QTE_ADDED ) < 1 ) {
            return item.QTE_ADDED  =   1;
        }

        let currentValue    =   parseFloat( item.QTE_ADDED );

        if( $scope.oldRemaining < currentValue ) {
            NexoAPI.Toast()( $scope.textDomain.notEnoughStock );
            item.QTE_ADDED  =   parseFloat( $scope.oldRemaining );
        } else {
            item.QTE_ADDED  =   parseFloat( item.QTE_ADDED );
        }
    }

    /**
     * Increase and decrease
     * @param object
     * @param string operation
    **/

    $scope.quantity         =   function( item, operation ) {
        if( operation == 'increase' ) {
            $scope.oldQte   =   parseFloat( item.QTE_ADDED);
            $scope.fetchItem( item.CODEBAR );
        } else if( operation == 'decrease' && parseFloat( item.QTE_ADDED ) > 1 ) {
            item.QTE_ADDED--;
        }
    }

    /**
     * Submit Stock
     * @return void
    **/

    $scope.canSubmitTransfert       =   true;

    $scope.submitStock      =   function() {

        if( $scope.canSubmitTransfert == true ) {
            $scope.canSubmitTransfert   =   false;
        } else {
            return false;
        }

        if( $scope.order.items.length == 0 ) {
            $scope.canSubmitTransfert       =   true;
            return false;
        }

        if( $scope.order.title == '' ) {
            $scope.canSubmitTransfert       =   true;
            return NexoAPI.Toast()( '<?php echo _s( 'You must provide a title', 'stock-manager' );?>' );
        }

        if( angular.equals( $scope.order.store, {} ) ) {
            $scope.canSubmitTransfert       =   true;
            return NexoAPI.Toast()( '<?php echo _s( 'You must select a store', 'stock-manager' );?>' );
        }

        /**
         * Display the right text according to the operation
         */
        $scope.transfertText            =   $scope.isRequesting   ? 
            '<?php echo _s( 'The stock request is ready to be send. Should we proceed ?', 'stock-manager' );?>' :
            '<?php echo _s( 'The stock transfert is ready to be send. Should we proceed ?', 'stock-manager' );?>';
        $scope.operationSuccessfulText  =   $scope.isRequesting ? 
            '<?php echo _s( 'The request has been successfully send !', 'stock-manager' );?>' :
            '<?php echo _s( 'The stock has been successfully send !', 'stock-manager' );?>';

        swal({
            title: '<?php echo _s( 'Would you confirm ?', 'stock-manager' );?>',
            text: $scope.transfertText,
            showCancelButton: true
        }).then( result => {
            if ( result.value ) {
                $http({
                    url		        :	'<?php echo site_url( array( 'rest', 'stock_manager', 'stock_transfert', store_get_param( '?' ) ) );?>',
                    method	        :	'POST',
                    data	        :	Object.assign( $scope.order, {
                        is_request  :   $scope.isRequesting
                    }),
                    headers			:	$scope.ajaxHeader
                }).then(function( response ){
                    NexoAPI.Toast()( $scope.operationSuccessfulText );

                    $scope.order       =   {
                        title       : '',
                        store       :   {},
                        items       :   []
                    };
                    
                    $scope.canSubmitTransfert   =   true;
                }, function(){
                    $scope.canSubmitTransfert   =   true;
                });
            } else {
                $scope.canSubmitTransfert       =   true;
            }
        }).catch( error => {
            $scope.canSubmitTransfert       =   true;
        })
    }

    $scope.npAutocompleteOptions = {
        url: '<?php echo site_url( array( 'rest', 'nexo', 'search_item' ) );?>' +  '?<?php echo store_get_param( null );?>',
        headers		:	{
            '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo get_option( 'rest_key' );?>'
        },
        queryMode           :   true,
        callback            :   function( data, option ) {
            if( data.length == 1 ) {
                $scope.addToCart( data[0], 1 );
                angular.element( '.search-input' ).val('');
                angular.element( '.search-input' ).select();
                option.close();
                return false;
            }
            return true;
        },
        listClass           :   'list-class',
        nameAttr            :   'DESIGN',
        clearOnSelect       :   true,
        onSelect            :   function( item ) {
            $scope.addToCart( item, 1 );
        }, 
        onError             :   function(){
            NexoAPI.Toast()( '<?php echo _s( 'Unable to find this item', 'stock-manager' );?>' );
            angular.element( '.search-input' ).val('');
            angular.element( '.search-input' ).select();
        },
        delay               :   500
    };

    $scope.detectFile       =   function( e ){
        $scope.csvContent   =   [];
        $scope.isValid      =   false;
        let file            =   e.target.files[0];
        let fileExtension   =   file.name.split('.').pop();

        if ( [ 'csv' ].indexOf( fileExtension ) === -1 ) {
            return NexoAPI.Notify().warning(
                $scope.textDomain.warning,
                $scope.textDomain.wrongExtension
            );
        }

        let fileReader              =   new FileReader();
        fileReader.readAsText( file, 'UTF-8' );
        fileReader.onload           =   function( data ) {
            let lines               =   CSVToArray( data.target.result, ',' );
            if ( lines.length < 2 ) {
                return NexoAPI.Notify().warning(
                    $scope.textDomain.warning,
                    $scope.textDomain.notEnoughData
                );
            }

            $timeout( () => {
                $scope.fileColumns  =   lines[0];
                $scope.csvContent   =   lines.filter( ( line, index ) => index !== 0 );
                $scope.isValid      =   true;                                                                                                   
            });
        }
    }

    $scope.proceed      =   async function() {
        if ( $scope._isProcessing ) {
            return false;
        }

        if ( ! $scope.isValid ) {
            return swal({
                title: $scope.textDomain.unableToProceed,
                text: $scope.textDomain.incorrectFileProvided,
                type: 'error'
            });
        }

        $scope._isProcessing        =   true;
        $scope._processPercentage   =   '0.00';
        $scope._results             =   [];

        for( index in $scope.csvContent ) {
            try {
                const { item, barcode, quantity, message }  =   await new Promise( ( resolve, reject ) => {
                    const barcode       =   $scope.csvContent[ index ][0];
                    const quantity      =   parseFloat( $scope.csvContent[ index ][1] );
                    const response      =   $scope.__fetchItem( barcode );

                    response.then( returned => {
                        result  =   $scope.increaseQuantity( returned.data[0], quantity );

                        console.log( result );
                        if ( result.status === 'success' ) {
                            resolve({ item: returned.data[0], barcode, quantity, message : result.message, status: result.status });
                        } else {
                            reject({ item : result.item, barcode, quantity, message: result.message, status : result.status });
                        }

                        $scope._processPercentage   =   ( ( parseFloat( index ) / $scope.csvContent.length ) * 100 ).toFixed(2);
                    }, error => {
                        reject({ error, barcode, quantity, message : $scope.textDomain.unableToFindTheItem, status : 'failed' });
                    });
                });

                console.log( item, message, quantity );

                $scope._results.push({
                    barcode,
                    quantity,
                    item,
                    message
                });
            } catch({ barcode, item, quantity, message, status }) {
                $scope._processPercentage   =   ( ( parseFloat( index ) / $scope.csvContent.length ) * 100 ).toFixed(2);
                $scope._results.push({
                    barcode,
                    quantity,
                    message: $scope.textDomain.anErrorOccuredWhileAdding
                });

                if ( status === 'info' ) {
                    result  =   $scope.increaseQuantity( item, quantity );
                }
            }
        }

        $timeout( () => {
            $scope._isProcessing        =   false;
            $scope._processPercentage   =   '0.00';
            $scope.isValid              =   false;
            jQuery( '[type="file"]' ).val('');
        })
    }

    jQuery( '[type="file"]' ).bind( 'change', function( e ){
        $scope.detectFile( e );
    });

    // Initial focus
    $( '.barcode-field' ).focus();
}

StockTransferCTRL.$inject       =   [ '$scope', '$http', '$timeout' ];

tendooApp.controller( 'StockTransferCTRL',  StockTransferCTRL );
</script>