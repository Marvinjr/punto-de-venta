<?php if ( store_option( 'nexo_vat_type' ) === 'item_vat' ):?>
<script>
const itemsTaxData  =   {
    textDomain: {
        openTaxBreakDownTitle: `<?php echo __( 'Détails de taxes', 'nexo' );?>`,
        total: `<?php echo __( 'Total', 'nexo' );?>`
    }
}
</script>
<script src="<?php echo module_url( 'nexo' ) . '/js/v2-1.tax-compute.js';?>"></script>
<?php endif;?>