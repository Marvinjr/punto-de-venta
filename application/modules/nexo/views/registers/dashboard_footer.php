<script>
    const RegistersData     =   {
        textDomain: {
            close: `<?php echo __( 'Fermer', 'nexo' );?>`,
            operation: `<?php echo __( 'Opération', 'nexo' );?>`,
            amount: `<?php echo __( 'Montant', 'nexo' );?>`,
            reason: `<?php echo __( 'Raison', 'nexo' );?>`,
            date: `<?php echo __( 'Date', 'nexo' );?>`,
            author: `<?php echo __( 'Auteur', 'nexo' );?>`,
            opening: '<?php echo _s( 'Ouvrir', 'nexo' );?>', 
            disbursement: '<?php echo _s( 'Décaissement', 'nexo' );?>', 
            cashing: '<?php echo _s( 'Encaissement', 'nexo' );?>', 
            closing: '<?php echo _s( 'Fermer', 'nexo' );?>', 
            idle_starts: '<?php echo _s( 'Début d\'inactivité ', 'nexo' );?>', 
            idle_ends: '<?php echo _s( 'Fin d\'inactivité ', 'nexo' );?>', 
            unknownOperationType: '<?php echo _s( 'Opération Inconnue', 'nexo' );?>', 
            unexpectedErrorOccured: '<?php echo _s( "Une erreur inattendue s'est produite", 'nexo' );?>', 
            nextPage: '<?php echo _s( 'Page Suivante', 'nexo' );?>', 
            prevPage: '<?php echo _s( 'Page Précédente', 'nexo' );?>', 
            confirmYourOperation: '<?php echo _s( 'Souhaitez-vous continuer ?', 'nexo' );?>', 
            registerHistoryWillBeErased: '<?php echo _s( 'L\'historique de la caisse enregistreuse sera supprimé ?', 'nexo' );?>', 
            noEntry: '<?php echo _s( 'Aucune entrée à afficher...', 'nexo' );?>', 
        },
        element: '#register-vue-wrapper',
        url: {
            deleteHistory: `<?php echo site_url([ 'api', 'nexopos', 'registers', 'clearHistory', '{id}' . store_get_param('?')]);?>`
        }
    }
</script>
<script src="<?php echo module_url( 'nexo' ) . '/js/registers-list.vue.js';?>"></script>
<script src="<?php echo module_url( 'nexo' ) . '/js/clear-register-history.vue.js';?>"></script>
<script type="text/javascript">
    "use strict";

    $( document ).ajaxComplete(function(e) {
        $( '.open_register' ).not( '.bound' ).bind( 'click', function(){
            const $this     =   $( this );
            HttpRequest.get( '<?php echo site_url( array( 'api', 'nexopos', 'registers', store_get_param('?') ) );?>' ).then( result => {
                const registers     =   result.data;
                const openByUser    =   registers.filter( register => register.USED_BY === '<?php echo User::id();?>' && $( this ).data( 'item-id' ) !== parseInt( register.ID ) && register.STATUS === 'opened' );
                const targeted      =   registers.filter( register => $( this ).data( 'item-id' ) === parseInt( register.ID ) )[0];

                if ( openByUser.length > 0 ) {
                    return NexoAPI.Notify().warning(
                        '<?php echo _s( 'Attention', 'nexo' );?>',
                        `<?php echo __( 'Vous ne pouvez plus ouvrir de nouvelle caisse enregistreuse, car vous avez déjà ouvert une caisse. 
                        Fermez cette caisse et essayez à nouveau', 'nexo' );?>`
                    );
                }

                if( targeted.STATUS == 'opened' ) {
                    if( targeted.USED_BY != '<?php echo User::id();?>' && <?php echo ( User::in_group([ 'master', 'store.manager' ]) ) ? 'false == true' : 'true == true';?> ) {
                        // Display confirm box to logout current user and login
                        bootbox.alert( '<?php echo _s( 'Impossible d\'accéder à une caisse en cours d\'utilisation. Si le problème persiste, contactez l\'administrateur.', 'nexo' );?>' );
                    } else {
                        bootbox.alert( '<?php echo _s( 'Vous allez être redirigé vers la caisse...', 'nexo' );?>' );
                        // Document Location
                    }
                } else if( targeted.STATUS == 'locked' ) {
                    bootbox.alert( '<?php echo _s( 'Impossible d\'accéder à une caisse verrouillée. Si le problème persiste, contactez l\'administrateur.', 'nexo' );?>' );

                } else if( targeted.STATUS == 'closed' ) {
                    var dom		=	'<h3 class="modal-title"><?php echo _s( 'Ouverture de la caisse', 'nexo' );?></h3><hr style="margin:10px 0px;">';

                        dom		+=	'<p><?php echo tendoo_info( sprintf( _s( '%s, vous vous préparez à ouvrir une caisse. Veuillez spécifier le montant initiale de la caisse', 'nexo' ), User::pseudo() ) );?></p>' +
                                    `
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon" id="basic-addon1"><?php echo _s( 'Solde d\'ouverture de la caisse', 'nexo' );?></span>
                                            <input type="text" class="form-control open_balance" placeholder="<?php echo _s( 'Montant', 'nexo' );?>" aria-describedby="basic-addon1">
                                        </div>  
                                    </div>                                
                                    <div class="form-group">
                                        <label for="textarea"><?php echo __( 'Remarques', 'nexo' );?></label>
                                        <textarea name="" id="textarea" class="form-control note" rows="3" required="required"></textarea>
                                    </div>
                                    `;

                    bootbox.confirm( dom, function( action ) {
                        if( action ) {
                            $.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'open_register' ) );?>/' + $this.data( 'item-id' ) + '?<?php echo store_get_param( null );?>', {
                                dataType	:	'json',
                                type		:	'POST',
                                data		:	_.object( [ 'date', 'balance', 'used_by', 'note' ], [ '<?php echo date_now();?>', $( '.open_balance' ).val(), '<?php echo User::id();?>', $( '.note' ).val() ]),
                                success: function( data ){
                                    bootbox.alert( '<?php echo _s( 'La caisse a été ouverte. Veuillez patientez...', 'nexo' );?>' );
                                    document.location	=	'<?php echo dashboard_url([ 'use', 'register' ]);?>/' + $this.data( 'item-id');
                                }
                            });
                        }
                    });

                    // Set custom width
                    $( '.modal-title' ).closest( '.modal-dialog' ).css({
                        'width'		:	'50%'
                    })
                }
            }).catch( ( err ) => {
                bootbox.alert( '<?php echo _s( 'Une erreur s\'est produite durant l\'ouverture de la caisse.', 'nexo' );?>' );
            })

            return false;
        });
        $( '.open_register' ).addClass( 'bound' );

        $( '.close_register' ).not( '.bound' ).bind( 'click', function(){
            var $this	=	$( this );
            $.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'register_status' ) );?>/' + $( this ).data( 'item-id' ) + '?<?php echo store_get_param( null );?>', {
                success		:	function( data ){
                    // Somebody is logged in
                    if( data[0].STATUS == 'opened' ) {

                        if( data[0].USED_BY != '<?php echo User::id();?>' && <?php echo ( User::in_group([ 'master', 'store.manager' ]) ) ? 'false == true' : 'true == true';?> ) {
                            bootbox.alert( '<?php echo _s( 'Vous ne pouvez pas fermer cette caisse. Si le problème persiste, contactez l\'administrateur.', 'nexo' );?>' );
                            return;
                        }

                        var dom		=	'<h3 class="modal-title"><?php echo _s( 'Fermeture de la caisse', 'nexo' );?></h3><hr style="margin:10px 0px;">';

                            dom		+=	'<p><?php echo tendoo_info( sprintf( _s( '%s, vous vous préparez à fermer une caisse. Veuillez spécifier le montant finale de la caisse', 'nexo' ), User::pseudo() ) );?></p>' +
                                        `<div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon" id="basic-addon1"><?php echo _s( 'Solde de fermeture de la caisse', 'nexo' );?></span>
                                                <input type="text" class="form-control open_balance" placeholder="<?php echo _s( 'Montant', 'nexo' );?>" aria-describedby="basic-addon1">
                                            </div>  
                                        </div> 
                                        <div class="form-group">
                                            <label for="textarea"><?php echo __( 'Remarques', 'nexo' );?></label>
                                            <textarea name="" id="textarea" class="form-control note" rows="3" required="required"></textarea>
                                        </div>
                                        `

                        bootbox.confirm( dom, function( action ) {
                            if( action == true ) {
                                $.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'close_register' ) );?>/' + $this.data( 'item-id' ) + '?<?php echo store_get_param( null );?>', {
                                    dataType	:	'json',
                                    type		:	'POST',
                                    data		:	_.object( [ 'date', 'balance', 'used_by', 'note' ], [ '<?php echo date_now();?>', $( '.open_balance' ).val(), '<?php echo User::id();?>', $( '.note' ).val() ]),
                                    success: function( data ){
                                        bootbox.alert( '<?php echo _s( 'La caisse a été fermée. Veuillez patientez...', 'nexo' );?>' );
                                        document.location	=	'<?php echo current_url();?>';
                                    }
                                });
                            }
                        });

                        // Set custom width
                        $( '.modal-title' ).closest( '.modal-dialog' ).css({
                            'width'		:	'50%'
                        })

                    } else if( data[0].STATUS == 'locked' ) {

                        bootbox.alert( '<?php echo _s( 'Impossible de fermer une caisse verrouillée. Si le problème persiste, contactez l\'administrateur.', 'nexo' );?>' );

                    } else if( data[0].STATUS == 'closed' ) {

                        bootbox.alert( '<?php echo _s( 'Cette caisse est déjà fermée.', 'nexo' );?>' );

                    }

                },
                dataType	:	"json",
                error		:	function(){
                    bootbox.alert( '<?php echo _s( 'Une erreur s\'est produite durant l\'ouverture de la caisse.', 'nexo' );?>' );
                }
            })

            return false;
        });
        $( '.close_register' ).addClass( 'bound' );

        $( '.register_history' ).not( '.bound' ).bind( 'click', function(){
            const textDomain    =   RegistersData.textDomain;
            RegistersData.url   =   {
                get: '<?php echo site_url([ 'api', 'nexopos', 'registers', 'history' ]);?>/' + $( this ).data( 'item-id' ) + '?page={page}<?php echo store_get_param( '&' );?>',
                ...RegistersData.url
            }

            bootbox.alert( `
                <table class="table">
                    <thead>
                        <tr>
                            <th>${textDomain.operation}</th>
                            <th>${textDomain.amount}</th>
                            <th>${textDomain.reason}</th>
                            <th>${textDomain.date}</th>
                            <th>${textDomain.author}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="entry of crudResult.entries">
                            <td>{{ entry.TYPE | registerOperation }}</td>
                            <td>{{ entry.BALANCE | moneyFormat }}</td>
                            <td>{{ entry.NOTE }}</td>
                            <td>{{ entry.DATE_CREATION }}</td>
                            <td>{{ entry.AUTHOR_NAME }}</td>
                        </tr>
                        <tr v-if="crudResult.entries.length === 0">
                            <td colspan="5">${textDomain.noEntry}</td>
                        </tr>
                    </tbody>
                </table>
            ` );

            $( '.modal-dialog' ).css({
                'height': '100%',
                'width': '100%',
                'display': 'flex',
                'flex-direction': 'column',
                'margin': '0',
                'justify-content': 'center',
                'align-items': 'center'
            });

            $( '.modal-content' ).css({
                width: '90%',
                height: '90%',
                'display': 'flex',
                'flex-direction': 'column',
            });

            $( '.modal-body' ).css( 'flex', 1 );
            $( '.modal-body' ).css( 'overflow-y', 'auto' );
            $( '.modal-footer' ).html(
                `<div style="display: flex; justify-content: space-between">
                    <div>
                        <nav aria-label="Page navigation example" v-if="crudResult">
                            <ul class="pagination" style="margin:0">
                                <li :class="crudResult.prev_page === -1 ? 'disabled' : ''"@click="loadRegisterHistory( crudResult.prev_page )" class="page-item"><a class="page-link" href="#">${textDomain.prevPage}</a></li>
                                <li :class="crudResult.next_page === -1 ? 'disabled' : ''"@click="loadRegisterHistory( crudResult.next_page )" class="page-item"><a class="page-link" href="#">${textDomain.nextPage}</a></li>
                            </ul>
                        </nav>
                    </div>
                    <div>
                        <a class="btn btn-default" @click="loadRegisterHistory()"><i class="fa fa-refresh"></i></a>
                        <a class="btn btn-primary" @click="closePopup()">${textDomain.close}</a>
                    </div>
                </div>`
            )

            $( '.modal-dialog' ).attr( 'id', RegistersData.element.substr(1) );

            const VueApp    =   new RegistersListVueApp( RegistersData );

            return false;
        });
        $( '.register_history' ).addClass( 'bound' );
    });
</script>