<?php if ( store_option( 'nexo_vat_type' ) === 'fixed' ):?>
    <td class="text-right">
        <span class="pull-left"><?php echo sprintf(__('TVA (%s%%)', 'nexo'), $Options[ store_prefix() . 'nexo_vat_percent' ]);?></span>
        <span class="cart-vat pull-right"></span>
    </td>
<?php elseif ( store_option( 'nexo_vat_type' ) === 'item_vat' ):?>
<td>
    <div>
        <button @click="openItemTaxBreakDown()" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i></button>
        <?php echo __( 'Total des taxes', 'nexo' );?>
        <span class="cart-item-vat pull-right"></span>
    </div>
</td>
<?php else:?>
<td class="text-right">
    <div class="input-group input-group-sm">
        <select type="text" class="form-control taxes_select">
            <option value=""><?php echo __( 'Selectionner une taxe', 'nexo' );?></option>
        </select>
        <span class="input-group-addon cart-vat"></span>
    </div>
</td>
<?php endif;?>