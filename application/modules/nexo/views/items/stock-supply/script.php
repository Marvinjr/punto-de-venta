<?php 
global $Options;
$this->load->config( 'rest' );
$this->load->model( 'Nexo_Shipping' );
?>
<script>
    var stockSupplyingCTRL          =   function( $scope, $http ) {

        $scope.storeParam           =   '<?php echo store_get_param( '?' );?>';
        $scope.stock_operation      =   <?php echo json_encode( $this->config->item( 'stock-operation' ) );?>;

        $scope.fields               =   {
            provider                :   null,
            shipping                :   null,
            item_qte                :   null,
            unit_price              :   null,
            status                  :   null,
            description             :   null,
            barcode                 :   null
        }
        $scope.hasSelectedSupply    =   false;

        $scope.history              =   [];

        $scope.resetFields              =   function(){
            $scope.fields.provider      =   null;
            $scope.fields.unit_price    =   null;
            $scope.fields.status        =   null;
            $scope.fields.description   =   null;
            $scope.fields.shipping      =   null;
            $scope.hasSelectedSupply    =   false;
        }

        $scope.reset                =   function(){            
            $scope.status               =   null;
            $scope.item                 =   [];
            $scope.history              =   [];
            $scope.resetFields();
            $scope.form === undefined || $scope.form.$setPristine();
        }

        $scope.providers            =   <?php echo json_encode( get_instance()->Nexo_Shipping->get_providers() );?>;
        $scope.shippings            =   null;<?php // echo json_encode( get_instance()->Nexo_Shipping->get_shipping() );?>;
        
        $scope.selectedOperation    =   {
            namespace       :   false,
            text            :   '<?php echo __( 'Opération', 'nexo' );?>'
        };

        // {
        //     namespace       :   'supply', 
        //     text            :   '<?php echo _s( 'Approvisionnement', 'nexo' );?>',
        // },

        $scope.actions              =   [
            {
                namespace       :   'adjustment', 
                text            :   '<?php echo _s( 'Suppression', 'nexo' );?>',
            },{
                namespace       :   'defective', 
                text            :   '<?php echo _s( 'Défectueux', 'nexo' );?>',
            }
        ];
        

        $scope.$watch( 'fields.barcode', function( next, prev ){
            if( prev != next ) {
                $scope.fetchItem( next );
            }
        });

        $scope.$watch( 'fields.item_qte', function() {
            if ( $scope.item !== undefined && $scope.item.length !== 0 ) {

                // turn that into a positive number
                $scope.fields.item_qte  =   parseFloat( $scope.fields.item_qte ) < 0 ? Math.abs( parseFloat( $scope.fields.item_qte ) ) : parseFloat( $scope.fields.item_qte );

                let totalItems      =   $scope.history.filter( history => history.type === 'supply' )
                    .map( history => history.quantity )
                    .reduce( ( a, b ) => parseFloat( a ) + parseFloat( b ), 0 );
                
                if ( totalItems - parseFloat( $scope.fields.item_qte ) < 0 ) {
                    
                    $scope.fields.item_qte  =   totalItems;

                    return NexoAPI.Notify().warning( 
                        '<?php echo __( 'Attention', 'gastro' );?>',
                        '<?php echo _s( 'La quantité de l\'opération ne peut pas être supérieure à la quantité disponible dans l\'approvisionnement.', 'gastro' );?>'
                    );
                }
            }
        });

        /**
         * convertType
         * @return string
        **/

        $scope.convertType          =   function( type ) {
            return $scope.stock_operation[ type ];
        }

        $scope.loadProductDeliveries    =   function( item ) {
            HttpRequest.get( '/api/nexopos/products/' + item[0].ID + '/deliveries' + $scope.storeParam ).then( result => {
                $scope.shippings    =   result.data;
                $scope.$apply();
                $scope.loadItemHistory();
            });
        }

        /** 
         * Fetch Item
         * @param string barcode
         * @return void
        **/

        $scope.fetchItem        =   function( value = null, type = 'search' ) {
            // reset everything
            // $scope.reset();

            if( $scope.fields.barcode != null && $scope.fields.barcode != '' && $scope.fields.barcode.length >= 3 || typeof value != "undefined" ) {
                
                let fetch       =   value == null ? $scope.fields.barcode : value;

                if( type == 'search' ) {

                    if( fetch.length < 3 ) {
                        return false;
                    }

                    tendoo.loader.show();

                    $http.post( '<?php echo site_url( array( 'rest', 'nexo', 'item_search', store_get_param( '?' ) . '&refresh=true' ) );?>', { fetch }, {
                        headers			:	{
                            '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                        }
                    }).then(function( returned ){
                        tendoo.loader.hide();

                       if ( returned.data.length > 0 ) {
                            if ( returned.data[0].STOCK_ENABLED === '2' ) {
                                return NexoAPI.Notify().warning( 
                                    '<?php echo __( 'Attention', 'nexo' );?>',
                                    `<?php echo _s( 'Impossible d\'ajuster le stock d\'un produit dont la gestion du stock est désactivée. 
                                    Pensez à activer la gestion du stock pour le produit : <strong>#s</strong>', 'nexo' );?>`.replace( '#s', returned.data[0].DESIGN )
                                );
                            }

                            $scope.item         =   returned.data;
                            // if one item is found 
                            if( $scope.item.length == 1 ) {
                                setTimeout(()=> {
                                    $scope.loadProductDeliveries( $scope.item ); // <=
                                    $( '[name="item_qte"]' ).select();  
                                }, 200 );
                            }
                        }
                    },( returned ) => {
                        tendoo.loader.hide();
                        $scope.item             =   [];
                        $scope.status           =   returned.status;
                    });
                } else {

                    tendoo.loader.show();
                    
                    $http.get( '<?php echo site_url( array( 'rest', 'nexo', 'item' ) );?>' + '/' + fetch + '/' + type + '?<?php echo store_get_param( null );?>&refresh=true', {
                        headers			:	{
                            '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                        }
                    }).then(function( returned ){
                        tendoo.loader.hide();
                        $scope.item         =   returned.data;

                        // if one item is found 
                        $scope.item         =   returned.data;
                        // if one item is found 
                        if( $scope.item.length == 1 ) {
                            setTimeout(()=> {
                                $scope.loadProductDeliveries( $scope.item ); // <=
                                $( '[name="item_qte"]' ).select();  
                            }, 200 );
                        }
                    },( returned ) => {
                        tendoo.loader.hide();
                        $scope.item             =   [];
                        $scope.status           =   returned.status;
                    });
                }                
            }            
        }

        /**
         * loadItem
         * @param object item 
         * @return void
        **/

        $scope.loadItem         =   ( item, type ) => {
            $scope.fetchItem( item.CODEBAR, type );
        }

        /**
         * loadItemHistory
         * @return void
        **/

        $scope.loadItemHistory      =   () => {

            if ( $scope.fields.shipping === null ) {
                return false;
            }

            $http.get( '<?php echo site_url( array( 'rest', 'nexo', 'item_stock' ) );?>/' + $scope.item[0].CODEBAR + '/' + $scope.fields.shipping.ID + '/<?php echo store_get_param( '?' );?>', {
                headers			:	{
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                }
            }).then(function( returned ){
                
                tendoo.loader.hide();

                /**
                 * If the item doesn't have any history
                 * then we're probably attempting to adjust a new produit
                 * with no stock or history.
                 */
                if( returned.data.length === 0 || parseFloat( $scope.item[0].QUANTITE_RESTANTE ) === 0 ) {
                    return NexoAPI.Notify().warning( 
                        '<?php echo __( 'Attention', 'nexo' );?>',
                        `<?php echo _s( 'L\'opération ne peut pas être effectué sur un produit n\'ayant pas encore reçu de stock. Vous devez envisager de changer l\'approvisionnement sélectionné.', 'nexo' );?>`
                    );
                }

                $scope.history              =   returned.data;
                $scope.hasSelectedSupply    =   true;

            },( returned ) => {
                tendoo.loader.hide();
                $scope.history          =   [];
                $scope.hasSelectedSupply    =   false;
            });
        }

        /**
         * Show Operation
         * @return string
        **/

        $scope.selectAction        =   ( action ) => {
            // $scope.resetFields();
            $scope.selectedOperation    =   action;
        }

        /**
         * submitSupply
         * @return void
        **/

        $scope.submitSupply         =   function() {
            if( $scope.selectedOperation.namespace == false ) {
                return false;
            }

            if( $scope.form.$valid == true ) {
                $( '.submitSupply' ).attr( 'disabled', 'disabled' );
                tendoo.loader.show();
                
                let data        =   {
                    unit_price      :   $scope.item[0].PRIX_DACHAT,
                    item_barcode    :   $scope.item[0].CODEBAR,
                    item_qte        :   $scope.fields.item_qte,
                    date_creation   :   tendoo.now(),
                    author          :   <?php echo User::id();?>,
                    type            :   $scope.selectedOperation.namespace,
                    description     :   $scope.fields.description,
                }

                data.ref_shipping       =   $scope.fields.shipping.ID;

                if( _.indexOf( [ 'supply' ], $scope.selectedOperation.namespace ) != -1 ) {
                    data.ref_provider       =   $scope.fields.provider.ID;
                    data.unit_price         =   $scope.fields.unit_price;
                }

                $http.post( '<?php echo site_url( array( 'rest', 'nexo', 'item_stock', store_get_param( '?' ) ) );?>', data, {
                    headers			:	{
                        '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                    }
                }).then(function( returned ){
                    $( '.submitSupply' ).removeAttr( 'disabled' );
                    tendoo.loader.hide();  
                    $scope.status           =   202;
                    // $scope.loadItem( $scope.item[0], 'CODEBAR' );
                    $scope.hasSelectedSupply    =   false;
                    $scope.reset();
                    NexoAPI.Toast()( `<?php echo __( 'L\'opération a été enregistrée', 'nexo' );?>` );
                },( returned ) => {
                    $( '.submitSupply' ).removeAttr( 'disabled' );
                    tendoo.loader.hide();
                    $scope.status           =   returned.status;
                    NexoAPI.Bootbox().alert( '<?php echo _s( 'Impossible d\'effectuer l\'opération, le stock final après l\'opération sera négatif.', 'nexo' );?>' );
                });
                
                return false;
            }
        }

        /**
         * Test Type
        **/

        $scope.testType             =   function( entry ){
            if( _.indexOf( [ 'supply', 'usable' ], entry.type ) == -1 ) {
                return false;
            } else {
                return true;
            }
        }

        $scope.reset();
        $( '.barcode-search' ).select();
    }

    stockSupplyingCTRL.$inject  =   [ '$scope', '$http' ];
    tendooApp.controller( 'stockSupplyingCTRL', stockSupplyingCTRL );

        tendooApp.directive('validNumber', function() {
        return {
            require: '?ngModel',
            link: function(scope, element, attrs, ngModelCtrl) {
            if(!ngModelCtrl) {
                return; 
            }

            ngModelCtrl.$parsers.push(function(val) {
                if (angular.isUndefined(val)) {
                    var val = '';
                }
                
                var clean = val.replace(/[^-0-9\.]/g, '');
                var negativeCheck = clean.split('-');
                var decimalCheck = clean.split('.');
                if(!angular.isUndefined(negativeCheck[1])) {
                    negativeCheck[1] = negativeCheck[1].slice(0, negativeCheck[1].length);
                    clean =negativeCheck[0] + '-' + negativeCheck[1];
                    if(negativeCheck[0].length > 0) {
                        clean =negativeCheck[0];
                    }
                    
                }
                
                if(!angular.isUndefined(decimalCheck[1])) {
                    decimalCheck[1] = decimalCheck[1].slice(0,2);
                    clean =decimalCheck[0] + '.' + decimalCheck[1];
                }

                if (val !== clean) {
                ngModelCtrl.$setViewValue(clean);
                ngModelCtrl.$render();
                }
                return clean;
            });

            element.bind('keypress', function(event) {
                if(event.keyCode === 32) {
                event.preventDefault();
                }
            });
            }
        };
        });
</script>