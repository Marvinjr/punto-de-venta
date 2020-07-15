<script>
const CustomerAccountData   =   {
    textDomain: {
        invalidForm: `<?php echo __( 'Le formulaire n\'est pas valide', 'nexo' ) ;?>`,
        wrongAmountProvided: `<?php echo __( 'Le montant fourni n\'est pas valide.', 'nexo' );?>`,
        operationTypeRequired: `<?php echo __( 'Le type de l\'opération est requis.', 'nexo' );?>`,
        descriptionRequired: `<?php echo __( 'La description est requise.', 'nexo' );?>`,
        confirmTitle: `<?php echo __( 'Voulez-vous continuer ?', 'nexo' );?>`,
        anErrorOccured: '<?php echo __( 'Une erreur s\'est produite', 'nexo' );?>',
        confirmMessage: `<?php echo __( 'L\'opération sera enregistrée. Veuillez confirmer votre action', 'nexo' );?>`,
    }, 
    url : {
        submitCreditOperation : '<?php echo site_url([ 'api', 'nexopos', 'customers', 'account' ]);?>'
    },
    customer    :   <?php echo json_encode( $customer );?>
}
</script>
<?php include_once( dirname( __FILE__ ) . '/new-dom-vue.php' );?>
<script src="<?php echo module_url( 'nexo' ) . '/js/customer-account.vue.js';?>"></script>