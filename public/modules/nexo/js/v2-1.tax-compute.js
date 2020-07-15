  class TaxCompute {
    constructor() {
        NexoAPI.events.addAction( 'before_cart_refreshed', this.refreshCart );
    }

    /**
     * compute taxes used by the system
     * @return void
     */
    refreshCart() {
        v2Checkout.CartItems.forEach( item => {
            const taxes   =   v2Checkout.taxes.filter( tax => tax.ID === item.REF_TAXE );
            if ( taxes.length > 0 ) {

                /**
                 * compute tax
                 */
                const total     =   ( parseFloat( item.QTE_ADDED ) * parseFloat( item.PRIX_DE_VENTE ) );
                const tax       =   Object.assign({}, taxes[0] );
                tax.VALUE       =   ( total * parseFloat( tax.RATE ) ) / 100; 
                item.metas      =   Object.assign({}, item.metas || {}, { tax });
            }
        });
    }
}

$( document ).ready( () => {
    if ( $( '#cart-details' ).length > 0 ) {
        const taxCompute    =   new TaxCompute;
        new Vue({
            el: '#cart-details',
            data: {
                ...itemsTaxData
            },
            mounted() {
                NexoAPI.events.addAction( 'before_cart_refreshed', this.$forceUpdate );
                NexoAPI.events.addFilter( 'before_submit_order', this.updateOrderDetails );
            },
            computed: {
                
            },
            methods: {
                /**
                 * @param { order_details, saving_order } data order details
                 */
                updateOrderDetails( data ) {
                    
                    data.order_details.TVA      =   this.getTotal();
                    data.order_details.metas[ 'taxes' ]   =   this.getTaxes();
                    return data;
                },

                /**
                 * get the taxes
                 * @return {array} of taxes or empty
                 */
                getTaxes() {
                    const stack     =   [];
                    v2Checkout.CartItems.forEach( item => {
                        let hasFoundSimilarTax  =   false;
                        if ( stack.length > 0 ) {
                            stack.forEach( tax => {
                                if ( tax.ID === item.REF_TAXE ) {
                                    hasFoundSimilarTax  =   true;
                                    tax.TOTAL_TAX     +=  item.metas.tax.VALUE;
                                    return;
                                } 
                            })
                        } 

                        /**
                         * the value is defined above on the refreshCart
                         * method
                         */
                        if ( item.metas !== undefined && item.metas.tax !== undefined && ! hasFoundSimilarTax ) {
                            stack.push( Object.assign({}, item.metas.tax, {
                                TOTAL_TAX   :   NexoAPI.round( item.metas.tax.VALUE )
                            }));
                        } 
                    });
                    return stack;
                },

                /**
                 * get the current total of taxes
                 * @return number
                 */
                getTotal() {
                    let total = 0;
                    const taxes     =   this.getTaxes();
                    /**
                     * update the total if there is a tax
                     */
                    if ( taxes.length > 0 ) {
                        total       =   taxes.map( tax => tax.TOTAL_TAX )
                            .reduce( ( before, after ) => NexoAPI.round( before ) + NexoAPI.round( after ) )
                    }   
                    return total;
                },

                /**
                 * open tax break down
                 * @return void
                 */
                openItemTaxBreakDown() {
                    swal({
                        title: this.textDomain.openTaxBreakDownTitle,
                        html: `
                        <div id="tax-list-container">
                            <ul class="list-group">
                                ${this.getTaxes().map( tax => {
                                    return `<li class="list-group-item text-left"><span>${tax.NAME}</span><span class="pull-right">${NexoAPI.DisplayMoney( tax.TOTAL_TAX )}</span></li>`
                                }).join('')}  
                                <li class="list-group-item text-left">
                                    <span>${this.textDomain.total}</span>
                                    <span class="pull-right">${NexoAPI.DisplayMoney( this.getTotal() )}</span>
                                </li> 
                            </ul>
                        </div>
                        `
                    });
                }
            }
        })

    }
});