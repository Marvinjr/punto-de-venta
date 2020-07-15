<script>
    const CashRegisterData  =   {
        textDomain: {
            outingCash: `<?php echo __( 'Sortie d\'argent', 'nexo' );?>`,
            amount: `<?php echo __( 'Montant', 'nexo' );?>`,
            ok: `<?php echo __( 'Ok', 'nexo' );?>`,
            reason: `<?php echo __( 'Raison', 'nexo' );?>`,
            anErrorOccured: `<?php echo __( 'Une erreur s\'est produite.', 'nexo' );?>`,
            confirmYourAction: `<?php echo __( 'Confirmez Votre Action', 'nexo' );?>`,
            wouldYouLikeToCashOut: `<?php echo __( 'Souhaitez-vous confirmer la sortie d\'argent ?', 'nexo' );?>`,
            unexpectedErrorOccured: `<?php echo __( 'Une erreur inattendue s\'est produite', 'nexo' );?>`,
            theAmountIsInvalid: `<?php echo __( 'Le montant est invalide.', 'nexo' );?>`,
            theReasonIsMissing: `<?php echo __( 'La raison est manquante. Fournissez-en une.', 'nexo' );?>`,
        },
        url: {
            post: `<?php echo site_url([ 'api', 'nexopos', 'registers', 'cash-out', '{id}', store_get_param('?') ]);?>`
        }
    }
</script>
<script src="<?php echo module_url( 'nexo' ) . 'js/pos.cash-out.js';?>"></script>