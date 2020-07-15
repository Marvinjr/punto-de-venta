Vue.filter( 'registerOperation', ( type ) => {
    switch( type ) {
        case 'opening':         return RegistersData.textDomain.opening; break;
        case 'disbursement':    return RegistersData.textDomain.disbursement; break;
        case 'cashing':         return RegistersData.textDomain.cashing; break;
        case 'closing':         return RegistersData.textDomain.closing; break;
        case 'idle_starts':     return RegistersData.textDomain.idle_starts; break;
        case 'idle_ends':       return RegistersData.textDomain.idle_ends; break;
    }

    return RegistersData.textDomain.unknownOperationType
})
class RegistersListVueApp {
    constructor( data ) {
        data        =   {
            ...data,
            ...{
                page        :   1,
                crudResult  :   {},
                url         :   data.url.get
            }
        }

        this.vue        =   new Vue({
            el: data.element,
            methods: {
                loadRegisterHistory( page = 1 ) {
                    console.log( page, this.url );
                    HttpRequest.get( 
                        this.url.replace( '{page}', page )
                    ).then( result => {
                        this.crudResult     =   result.data;
                        this.$forceUpdate();
                    });
                },
                closePopup() {
                    this.$destroy();
                    document.querySelector( '[data-dismiss="modal"]' ).click();
                }
            },
            mounted() {
                this.loadRegisterHistory();
            },
            data,
        });
    }
}