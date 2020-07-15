const CustomerAccountHistory    =   new Vue({
    el: '#customer-history',
    mounted() {
        this.loadHistory( this.page );
    },
    data: {
        ...CustomerAccountHistoryData,
        page: 1,
        crudResult: null
    },
    computed: {
        entries() {
            if ( this.crudResult !== null ) {
                return this.crudResult.data;
            }
            return [];
        }
    },
    methods: {
        loadHistory( page ) {
            HttpRequest.get( `${this.url.getHistory}&page=${page}` ).then( result => {
                this.crudResult     =   result.data
                this.page           =   result.data.current_page;
            })
        },

        getOperationName( name ) {
            const keys  =   Object.keys( this.textDomain.operationType );
            if ( keys.includes( name ) ) {
                return this.textDomain.operationType[ name ];
            }
            return this.textDomain.operationNotDefined
        },

        deleteEntry( entry ) {
            swal({
                title: this.textDomain.confirmAction,
                text: this.textDomain.cancelTranslation,
                showCancelButton: true
            }).then( result => {
                if ( result.value ) {
                    this.confirmCancel( entry );
                }
            })
        },

        confirmCancel( entry ) {
            HttpRequest.get( this.url.cancelTransaction.replace( '#', entry.ID ) ).then( result => {
                this.loadHistory( this.page );
            })
        }
    },
    filters: {
        moneyFormat( amount ) {
            return NexoAPI.DisplayMoney( amount );
        }
    }
})