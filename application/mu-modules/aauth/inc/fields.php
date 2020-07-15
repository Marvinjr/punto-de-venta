<?php
class aauth_fields extends CI_model
{
    public function __construct()
    {
        $this->events->add_filter('installation_fields', array( $this, 'installation_fields' ), 10, 1);
        // add action to display login fields
        $this->events->add_action('display_login_fields', array( $this, 'create_login_fields' ));
        $this->events->add_action('load_users_custom_fields', array( $this, 'user_custom_fields' ));
        $this->events->add_filter('displays_registration_fields', array( $this, 'registration_fields' ));
        $this->events->add_action('displays_public_errors', array( $this, 'public_errors' ));
        $this->events->add_action('displays_dashboard_errors', array( $this, 'displays_dashboard_errors' ));
        $this->events->add_filter('custom_user_meta', array( $this, 'custom_user_meta' ), 10, 1);
        $this->events->add_filter('recovery_fields', array( $this, 'recovery_fields' ));
    }
    public function recovery_fields()
    {
        ob_start();
        ?>
        <?php echo tendoo_info(__('Please provide your user email in order to get recovery email', 'aauth'));
        ?>
        <div class="input-group">
        <span class="input-group-addon" id="basic-addon1"><?php _e('User email or Pseudo', 'aauth');
        ?></span>
        <input type="text" class="form-control" placeholder="<?php _e('User email or Pseudo', 'aauth');
        ?>" aria-describedby="basic-addon1" name="user_email">
        <span class="input-group-btn">
        <button class="btn btn-default" type="submit"><?php _e('Get recovery Email', 'aauth');
        ?></button>
        </span>
        </div>
        <?php
        return ob_get_clean();
    }
    public function installation_fields($fields)
    {
        ob_start();
        ?>
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
        ?>" name="password" value="<?php echo set_value('password');
        ?>">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="<?php _e('Password confirm', 'aauth');
        ?>" name="confirm" value="<?php echo set_value('confirm');
        ?>">
        <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
        </div>
        <?php
        return $fields    .=    ob_get_clean();
    }
    public function create_login_fields()
    {
        // default login fields
        $this->config->set_item('signin_fields', array(
            'pseudo'    =>
                '<div class="form-group has-feedback">
                    <input type="text" class="form-control" placeholder="' . __('Email or User Name', 'aauth') .'" name="username_or_email">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>',
            'password'    =>
                '<div class="form-group has-feedback">
                    <input type="password" class="form-control" placeholder="' . __('Password', 'aauth') .'" name="password">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>',
            'submit'    =>
                '<div class="row">
                    <div class="col-xs-7">
                        <div class="checkbox icheck">
                            <label>
                                <div class="icheckbox_square-blue" aria-checked="false" aria-disabled="false"><input type="checkbox" name="keep_connected"><ins class="iCheck-helper"></ins></div> ' . __('Remember me', 'aauth') . '
                            </label>
                        </div>
                    </div><!-- /.col -->
                    <div class="col-xs-5">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">' . __('Sign In', 'aauth') .'</button>
                    </div><!-- /.col -->
                </div>'
        ));
    }
    public function public_errors()
    {
        $errors    =    $this->users->auth->get_errors_array();
        if ($errors) {
            foreach ($errors as $error) {
                echo tendoo_error($error);
            }
        }
    }
    public function registration_fields($fields)
    {
        return $fields .= $this->load->mu_module_view( 'aauth', 'users/registration-fields', compact( 'fields' ), true );
    }
    
    /**
    * Adds custom fields for user creation and edit
    *
    * @access : public
    * @param : Array
    * @return : Array
    **/
    
    public function user_custom_fields($config)
    {
        if ( $config[ 'mode' ] === 'edit' ) {
            $this->Gui->add_item([
                'type'      =>      'text',
                'name'      =>      'first-name',
                'label'     =>      __('First Name', 'aauth'),
                'value'     =>      $this->options->get( 'first-name', $config[ 'user_id' ] ),
            ], $config[ 'meta_namespace' ], $config[ 'col_id' ]);
    
            $this->Gui->add_item([
                'type'      =>      'text',
                'name'      =>      'last-name',
                'label'     =>      __('Last Name', 'aauth'),
                'value'     =>      $this->options->get( 'last-name', $config[ 'user_id' ] ),
            ], $config[ 'meta_namespace' ], $config[ 'col_id' ]);  

            $skin   =   $this->options->get( 'theme-skin', $config[ 'user_id' ]);
        } else {
            $this->Gui->add_item([
                'type'      =>      'text',
                'name'      =>      'first-name',
                'label'     =>      __('First Name', 'aauth'),
                'user_id'   =>      @$config[ 'user_id' ],
            ], $config[ 'meta_namespace' ], $config[ 'col_id' ]);
    
            $this->Gui->add_item([
                'type'      =>      'text',
                'name'      =>      'last-name',
                'label'     =>      __('Last Name', 'aauth'),
                'user_id'   =>      @$config[ 'user_id' ],
            ], $config[ 'meta_namespace' ], $config[ 'col_id' ]);  
        }              
        
        $dom    =   $this->load->mu_module_view( 'aauth', 'users/custom-fields', compact( 'config' ), true );

        riake( 'gui', $config )->add_item(array(
            'type'        =>    'dom',
            'content'    =>    $dom
        ), $config[ 'meta_namespace' ], $config[ 'col_id' ]);

        unset( $skin, $config, $dom );
    }
    
    /**
    * Displays Error on Dashboard Page
    **/
    
    public function displays_dashboard_errors()
    {
        $errors    =    $this->users->auth->get_errors_array();
        if ( $errors ) {
            foreach ($errors as $error) {
                echo tendoo_error($error);
            }
        }
    }
    
    /**
    * Adds custom meta for user
    *
    * @access : public
    * @param : Array
    * @return : Array
    **/
    
    public function custom_user_meta($fields)
    {
        $fields[ 'first-name' ]        =    ($fname = $this->input->post('first-name')) ? $fname : '';
        $fields[ 'last-name' ]        =    ($lname = $this->input->post('last-name')) ? $lname : '';
        $fields[ 'theme-skin' ]        =    ($skin    =    $this->input->post('theme-skin')) ? $skin : 'skin-blue';
        return $fields;
    }
}
new aauth_fields;