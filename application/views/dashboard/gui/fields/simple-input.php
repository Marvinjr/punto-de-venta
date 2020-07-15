<div class="input-group" style="margin-bottom:5px;">
    <span class="input-group-addon"><?php echo riake('label', $_item);?></span>
    <input <?php echo $disabled === true ? 'readonly="readonly"' : '';?> type="<?php echo $type;?>"
        name="<?php echo riake('name', $_item);?>" class="form-control"
        placeholder="<?php echo riake('placeholder', $_item);?>"
        value="<?php echo strip_tags( xss_clean( $value ) );?>">
</div>
<p><?php echo xss_clean($description);?></p>