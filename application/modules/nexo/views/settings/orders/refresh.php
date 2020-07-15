<div id="reassign-code">
    <button @click="reassignCode()" type="button" class="btn btn-primary"><?php echo __( 'Reassigner les codes', 'nexo' );?></button><br><br>
    <?php echo tendoo_info( __( 'Si vous n\'arrivez pas à effectuer une vente à cause d\'un conflict de code de commande, veuillez reassigner les codes', 'nexo' ) );?>
</div>
<script>
const ReassignData = {
    textDomain: {
        codeReassigned  :   `<?php echo __( 'Les codes ont été reassignés', 'nexo' );?>`
    },
    url: {
        reassign: `<?php echo site_url([ 'api', 'nexopos', 'reassign', store_get_param('?')]);?>`
    }
}
</script>
<script>
new Vue({
    el: '#reassign-code',
    data: {
        ...ReassignData
    },
    mounted() {

    },
    methods: {
        reassignCode() {
            HttpRequest.get( this.url.reassign ).then( result => {
                NexoAPI.Toast()( this.textDomain.codeReassigned );
            });
        }
    }
})
</script>