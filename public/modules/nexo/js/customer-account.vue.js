const CustomerAccountVue    =   new Vue({
    el: '#customer-account-new',
    data: {
        ...CustomerAccountData,
        operation: {}
    },
    mounted() {
    },
    methods: {
        submitOperation() {
            if ( parseFloat( this.operation[ 'amount' ] ) <= 0 ) {
                return swal({
                    type: 'error',
                    title: this.textDomain.invalidForm,
                    text: this.textDomain.wrongAmountProvided
                });
            }

            if ( ! [ 'add', 'remove' ].includes( this.operation.type ) ) {
                return swal({
                    type: 'error',
                    title: this.textDomain.invalidForm,
                    text: this.textDomain.operationTypeRequired
                });
            }

            if ( this.operation.description === '' ) {
                return swal({
                    type: 'error',
                    title: this.textDomain.invalidForm,
                    text: this.textDomain.descriptionRequired
                });
            }

            swal({
                type: 'info',
                title: this.textDomain.confirmTitle,
                text: this.textDomain.confirmMessage,
                showCancelButton: true
            }).then( result => {
                if ( result.value ) {
                    this.operation.customer_id  =   this.customer.ID
                    HttpRequest.post( this.url.submitCreditOperation, this.operation ).then( result => {
                        NexoAPI.Toast()( result.data.message );
                        this.operation  =   {};
                    }).catch( result => {
                        console.log( this.textDomain.anErrorOccured);
                        NexoAPI.Notify().warning(
                            this.textDomain.anErrorOccured,
                            result.response.data.message
                        );
                    })
                }
            })
        }
    }
})