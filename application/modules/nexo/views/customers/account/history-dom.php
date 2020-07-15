<div class="box" id="customer-history">
    <div class="box-header with-border">
        <h3 class="box-title">
            <?php echo __( 'Liste des opérations', 'nexo' );?>
        </h3>
        <div class="box-tools pull-right">
            <button @click="loadHistory( page )" type="button" class="btn btn-box-tool">
                <i class="fa fa-refresh"></i>
            </button>
        </div>
    </div>
    <div class="box-body no-padding">        
        <table class="table table-hover" style="margin:0">
            <thead>
                <tr>
                    <th><?php echo __( 'Action', 'nexo' );?></th>
                    <th><?php echo __( 'Montant', 'nexo' );?></th>
                    <th><?php echo __( 'Date', 'nexo' );?></th>
                    <th><?php echo __( 'Options', 'nexo' );?></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="entry in entries" :class="{ 'success' : entry.OPERATION === 'add', 'danger': entry.OPERATION === 'remove', 'info': entry.OPERATION }">
                    <td>{{ getOperationName( entry.OPERATION ) }}</td>
                    <td>{{ entry.VALUE | moneyFormat }}</td>
                    <td>{{ entry.DATE_CREATION }}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                Dropdown
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                <li><a v-if="[ 'add', 'remove' ].includes( entry.OPERATION )" href="javascript:void(0)" @click="deleteEntry( entry )"><?php echo __( 'Supprimer', 'nexo' );?></a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="box-footer" v-if="crudResult !== null">
        <ul class="pagination pagination-sm" style="margin:0">
            <li><a v-if="crudResult.prev_page !== null" @click="loadHistory( crudResult.prev_page )" href="javascript:void(0)"><i class="fa fa-arrow-circle-left"></i> <?php echo __( 'Précédent', 'nexo' );?></a></li>
            <li><a v-if="crudResult.next_page !== null" @click="loadHistory( crudResult.next_page )" href="javascript:void(0)"><?php echo __( 'Suivant', 'nexo' );?> <i class="fa fa-arrow-circle-right"></i></a></li>
        </ul>
    </div>
</div>
<script>
const CustomerAccountHistoryData    =   {
    url: {
        getHistory: '<?php echo site_url([ 'api', 'nexopos', 'customers', 'account', 'history', $customer_id, store_get_param('?') ]);?>',
        cancelTransaction: '<?php echo site_url([ 'api', 'nexopos', 'customers', 'account', 'cancel', '#', store_get_param('?')]);?>'
    }, 
    textDomain: {
        operationType: {
            add: '<?php echo __( 'Crédit Manuel', 'nexo' );?>',
            remove: '<?php echo __( 'Débit Manuel', 'nexo' );?>',
            payment: '<?php echo __( 'Paiement de commande', 'nexo' );?>'
        },
        confirmAction: '<?php echo __( 'Confirmez Votre Action ?', 'nexo' );?>',
        cancelTranslation: '<?php echo __( 'La transaction sera annulée.', 'nexo' );?>',
        operationNotDefined: '<?php echo __( 'Operation Non Défini', 'nexo' );?>',
    }
}
</script>
<script src="<?php echo module_url( 'nexo' ) . '/js/customer-account-history.js';?>"></script>