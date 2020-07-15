<div id="product-tax-report">
    <form class="form-inline hidden-print">
        <date-picker label="<?php echo __( 'Début', 'nexo_premium' );?>" date-format="YYYY-MM-DD" @changed="changeStartDate( $event )" :value="startDate" v-once></date-picker>
        <date-picker label="<?php echo __( 'Fin', 'nexo_premium' );?>" date-format="YYYY-MM-DD" @changed="changeEndDate( $event )" :value="endDate" v-once></date-picker>
        <input 
            type="button" 
            class="btn btn-primary" 
            @click="getReport()"
            value="<?php _e('Afficher les résultats', 'nexo_premium');?>" />
        <div class="input-group">
            <span class="input-group-btn">
                <button class="btn btn-default" print-item=".report-wrapper"
                    type="button"><?php _e('Imprimer', 'nexo_premium');?></button>
            </span>
        </div>
    </form>
    <br>
    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-header"><?php echo  __( 'Taxes Collectées', 'nexo_premium' );?></div>
                <div class="box-body no-padding">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="200"><?php echo __( 'Date', 'nexo_premium' );?></th>
                                <th><?php echo __( 'Taxe', 'nexo_premium' );?></th>
                                <th><?php echo __( 'Total', 'nexo_premium' );?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="rawEntries.length === 0">
                                <td colspan="3"><?php echo __( 'Aucune entrée à afficher.', 'nexo_premium' );?></td>
                            </tr>
                            <template v-if="rawEntries.length > 0">
                                <template v-for="entry of entries">
                                    <tr class="bg-info">
                                        <td colspan="3">{{ entry.starts }}</td>
                                    </tr>
                                    <tr v-for="tax of entry.taxes">
                                        <td></td>
                                        <td>{{ tax.NAME }}</td>
                                        <td class="text-right">{{ tax.TOTAL_VALUE | moneyFormat }}</td>
                                    </tr>
                                    <tr class="bg-success">
                                        <td>{{ entry.ends }}</td>
                                        <td><?php echo __( 'Total', 'nexo_premium' );?></td>
                                        <td class="text-right">{{ totalTaxes( entry.taxes ) | moneyFormat }}</td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border"><?php echo __( 'Total Taxes Cumulées', 'nexo_premium' );?></div>
                <div class="body-body no-padding">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo __( 'Nom', 'nexo_premium' );?></th>
                                <th class="text-right"><?php echo __( 'Total', 'nexo_premium' );?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="collectedTaxes.length === 0">
                                <td colspan="2"><?php echo __( 'Aucune entrée à afficher', 'nexo_premium' );?></td>
                            </tr>
                            <tr v-for="tax of collectedTaxes">
                                <td>{{ tax.name }}</td>
                                <td class="text-right">{{ tax.total | moneyFormat }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-success">
                                <td><?php echo __( 'Total', 'nexo_premium' );?></td>
                                <td class="text-right">{{ overAllTaxes | moneyFormat }}</td>
                            </tr>
                        </tfoot>
                    </table>                    
                </div>
            </div>
        </div>
    </div>
    <?php $this->events->do_action( 'hook_into_product_taxes_dom' );?>   
</div>