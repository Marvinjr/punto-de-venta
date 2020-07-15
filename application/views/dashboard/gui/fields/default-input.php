<input <?php echo $disabled === true ? 'readonly="readonly"' : '';?> type="<?php echo $type;?>"
    name="<?php echo riake('name', $_item);?>" class="form-control"
    placeholder="<?php echo riake('placeholder', $_item);?>" value="<?php echo strip_tags( xss_clean( $value ) );?>">
<p><?php echo xss_clean($description);?></p>