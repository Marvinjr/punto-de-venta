$( '.clear_history' ).not( '.bound' ).bind( 'click', function() {
    const textDomain    =   RegistersData.textDomain;
    swal({
        title: textDomain.confirmYourOperation,
        text: textDomain.registerHistoryWillBeErased,
        showCancelButton: true,
    }).then( result => {
        if ( result.value ) {
            HttpRequest.get( 
                RegistersData.url.deleteHistory.replace( '{id}', $( this ).data( 'item-id' ) )
            ).then( result => {
                NexoAPI.Toast()( result.data.message );
            }).catch( error => {
                NexoAPI.Toast()( textDomain.unexpectedErrorOccured );
            })
        }
    });

    return false;
});
$( '.clear_history' ).addClass( 'bound' );