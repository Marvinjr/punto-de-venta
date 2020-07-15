<script>
const v21DiscountData   =   {
    textDomain  :   {
        discount    :   `<?php echo __( 'Remises', 'nexo' );?>`,
        cancel: `<?php echo __( 'Annuler', 'nexo' );?>`,
        ok: `<?php echo __( 'Ok', 'nexo' );?>`,
        cartDiscount: `<?php echo __( 'Remise sur le panier', 'nexo' );?>`,
        percentage: `<?php echo __( 'Pourcent', 'nexo' );?>`,
        flat: `<?php echo __( 'Fixe', 'nexo' );?>`,
        clear: `<?php echo __( 'Effacer', 'nexo' );?>`,
        warning: `<?php echo __( 'Attention', 'nexo' );?>`,
        discountExceedSalePrice: `<?php echo __( 'La remise excède le prix de vente', 'nexo' );?>`
    }
}
</script>
<script src="<?php echo module_url( 'nexo' ) . '/js/v2-1.discount.js';?>"></script>
<script src="<?php echo module_url( 'nexo' ) . '/js/pos.num-keyboard.js';?>"></script>