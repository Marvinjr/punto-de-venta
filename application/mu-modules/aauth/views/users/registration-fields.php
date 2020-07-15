<div class="form-group has-feedback">
    <input type="text" class="form-control" placeholder="<?php _e('User Name', 'aauth');
        ?>" name="username" value="<?php echo set_value('username');
        ?>">
    <span class="glyphicon glyphicon-user form-control-feedback"></span>
</div>
<div class="form-group has-feedback">
    <input type="email" class="form-control" placeholder="<?php _e('Email', 'aauth');
        ?>" name="email" value="<?php echo set_value('email');
        ?>">
    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
</div>
<div class="form-group has-feedback">
    <input type="password" class="form-control" placeholder="<?php _e('Password', 'aauth');
        ?>" name="password">
    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
</div>
<div class="form-group has-feedback">
    <input type="password" class="form-control" placeholder="<?php _e('Confirm', 'aauth');
        ?>" name="confirm">
    <span class="glyphicon glyphicon-lock  form-control-feedback"></span>
</div>
<div class="row">
    <div class="col-xs-8">
        <div class="checkbox icheck">
        </div>
    </div><!-- /.col -->
    <div class="col-xs-4">
        <button type="submit" class="btn btn-primary btn-block btn-flat"><?php _e('Sign Up', 'aauth');
        ?></button>
    </div><!-- /.col -->
</div>