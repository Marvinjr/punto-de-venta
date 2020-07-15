<div id="customer-account-new">
    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <?php echo __( 'Créditer un compte', 'nexo' );?>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon"><?php echo __( 'Opération', 'nexo' );?></div>
                            <select v-model="operation.type" name="" id="" class="form-control">
                                <option value="remove"><?php echo __( 'Déduire du compte', 'nexo' );?></option>
                                <option value="add"><?php echo __( 'Créditer le compte', 'nexo' );?></option>
                            </select>
                        </div>
                        <span class="help-description"><?php echo __( 'L\'action permet d\'ajouter du crédit ou de retirer du crédit du compte d\'un client.', 'nexo' );?></span>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon"><?php echo __( 'Montant', 'nexo' );?></div>                            
                            <input v-model="operation.amount" type="number" name="" id="input" class="form-control">                            
                        </div>
                        <span class="help-description"><?php echo __( 'Définir le montant de l\'opération.', 'nexo' );?></span>
                    </div>
                    <div class="form-group">
                        <label for="textarea" class="control-label"><?php echo __( 'Détails', 'nexo' );?></label>
                        <div>
                            <textarea v-model="operation.description" id="textarea" class="form-control" rows="3" required="required"></textarea>
                        </div>
                        <span class="help-description"><?php echo __( 'Fournir plus de détails sur l\'opération.', 'nexo' );?></span>
                    </div>
                </div>
                <div class="box-footer">
                    <button @click="submitOperation()" type="button" class="btn btn-primary"><?php echo __( 'Enregistrer', 'nexo' );?></button>
                </div>
            </div>
        </div>
    </div>
</div>