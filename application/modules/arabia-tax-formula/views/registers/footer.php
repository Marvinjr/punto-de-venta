<script>
$( document ).ready( function() {
    TaxCompute.prototype.refreshCart    =   () => {
        v2Checkout.CartItems.forEach( item => {
            const taxes   =   v2Checkout.taxes.filter( tax => tax.ID === item.REF_TAXE );
            if ( taxes.length > 0 ) {

                /**
                 * compute tax
                 */
                const total     =   ( parseFloat( item.QTE_ADDED ) * parseFloat( item.PRIX_DE_VENTE ) );
                const tax       =   Object.assign({}, taxes[0] );
                tax.VALUE       =   ( total * parseFloat( tax.RATE ) ) / ( 100 + parseFloat( tax.RATE ) ); 
                item.metas      =   Object.assign({}, item.metas || {}, { tax });
            }
        });
    }
});
</script>