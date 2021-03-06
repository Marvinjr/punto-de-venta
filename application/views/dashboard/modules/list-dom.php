<div class="input-group">
  <span class="input-group-addon" id="basic-addon1"><?php echo __( 'Filter' );?></span>
  <input type="text" class="form-control search-module-namespace" placeholder="<?php echo __( 'Module Namespace' );?>" aria-describedby="basic-addon1">
</div>
<br>
<script>
$( document ).ready( function() {
    $( '.search-module-namespace' ).keyup( function() {
        const searchVal     =   $( this ).val();
        if ( searchVal.length >= 3 ) {
            $( '[data-namespace]' ).each( function() {
                if ( $( this ).data( 'namespace' ).includes( searchVal ) || $( this ).data( 'name' ).includes( searchVal ) ) {
                    $( this ).closest( '.col-lg-4' ).show();
                } else {
                    $( this ).closest( '.col-lg-4' ).hide();
                }
            });
        } else {
            $( '[data-namespace]' ).closest( '.col-lg-4' ).show();
        }
    });
});
</script>
<div class="row">
    <?php
    global $Options;
    $modules            	=    Modules::get();
    // $modules_status        	=    $this->options->get('modules_status');// get whether an update is available
    foreach (force_array($modules) as $_module) {
        if (isset($_module[ 'application' ][ 'namespace' ])) {
            $module_namespace        =    $_module[ 'application' ][ 'namespace' ];
            $module_version         =    $_module[ 'application' ][ 'version' ];
            $last_version           =   get_option( 'migration_' . $module_namespace );?>
    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">
        <div 
            data-namespace="<?php echo $_module[ 'application' ][ 'namespace' ];?>" 
            data-name="<?php echo $_module[ 'application' ][ 'name' ];?>" 
            class="box <?php echo (riake('highlight', $_GET) == $_module[ 'application' ][ 'namespace' ]) ? 'box-primary' : '' ;?> "
            id="#module-<?php echo $_module[ 'application' ][ 'namespace' ];?>" <?php if (!
            Modules::is_active($module_namespace)):?> style="background:#F3F3F3;" <?php endif;?>>
            <div class="box-header with-border">
                <h3 class="box-title" style="line-height: 30px;">
                    <?php echo isset($_module[ 'application' ][ 'name' ]) ? $_module[ 'application' ][ 'name' ] : __('Tendoo Extension');?>
                </h3>
                <small>
                    <?php echo 'v' . (isset($_module[ 'application' ][ 'version' ]) ? $_module[ 'application' ][ 'version' ] : 0.1);?>
                </small>
                <?php
                    $hasMigration   =   Modules::migration_files( 
                        $module_namespace, 
                        $last_version, 
                        $module_version
                    );

                    if( $hasMigration ):?>
                <a href="<?php echo site_url([ 'dashboard', 'modules', 'migrate', $module_namespace, $last_version ]);?>"
                    class="migrate-module pull-right btn btn-sm btn-primary"><i class="fa fa-database"></i></a>
                <?php endif;?>
            </div>
            <div class="box-body" style="height:100px;">
                <?php echo isset($_module[ 'application' ][ 'description' ]) ? $_module[ 'application' ][ 'description' ] : '';?>
            </div>
            <div class="box-footer" <?php if (! Modules::is_active($module_namespace)):?> style="background:#F3F3F3;"
                <?php endif;?>>
                <div class="box-tools pull-right">
                    <div class="btn-group btn-group-justified">
                        <?php
                if (isset($_module[ 'application' ][ 'main' ])) { // if the module has a main file, it can be activated
                    if (! Modules::is_active($module_namespace)) {?>
                        <a href="<?php echo site_url(array( 'dashboard', 'modules', 'enable', $module_namespace ));?>"
                            class="btn btn-sm btn-default btn-box-tool" data-action="enable"><i style="font-size:20px;"
                                class="fa fa-toggle-on"></i> Enable</a>
                        <?php

                    } else {?>
                        <a href="<?php echo site_url(array( 'dashboard', 'modules', 'disable', $module_namespace ));?>"
                            class="btn btn-sm btn-default btn-box-tool" data-action="disable"><i style="font-size:20px;"
                                class="fa fa-toggle-off"></i> Disable</a>
                        <?php

                    }
                }?>
                        <a href="<?php echo site_url(array( 'dashboard', 'modules', 'remove', $module_namespace ));?>"
                            class="btn btn-sm btn-default btn-box-tool" data-action="uninstall"><i
                                style="font-size:20px;" class="fa fa-trash"></i>
                            <?php _e('Remove');?></a>

                        <?php if (intval(riake('webdev_mode', $Options)) == true):?>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <?php echo __( 'Options' );?>
                            <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                            <li><a href="<?php echo site_url(array( 'dashboard', 'modules', 'extract', $module_namespace ));?>"
                             data-action="extract"><i style="font-size:20px;"
                                class="fa fa-archive"></i>
                            <?php _e('Extract');?></a></li>
                            <li><a href="<?php echo site_url(array( 'dashboard', 'modules', 'build-assets', $module_namespace ));?>"
                            data-action="extract"><i style="font-size:20px;"
                                class="fa fa-file-zip-o"></i>
                            <?php _e('Build Assets');?></a></li>
                            <li><a href="<?php echo site_url(array( 'dashboard', 'modules', 'publish-assets', $module_namespace ));?>"
                            data-action="extract"><i style="font-size:20px;"
                                class="fa fa-download"></i>
                            <?php _e('Publish Assets');?></a></li>
                            <li><a href="<?php echo site_url(array( 'dashboard', 'modules', 'reset-setup', $module_namespace ));?>"
                            data-action="extract"><i style="font-size:20px;"
                                class="fa fa-refresh"></i>
                            <?php _e('Reset Setup');?></a></li>
                            </ul>
                        </div>
                        
                            
                        
                        <?php endif;?>
                        <?php ?>
                        <!-- <button class="btn btn-default btn-box-tool" data-action="update"><i style="font-size:20px;" class="fa fa-refresh"></i></button>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php

        }
    }
    ?>
</div>
<script>
    $(document).ready(function () {
        $('[data-action="uninstall"]').bind('click', function () {
            if (confirm(`<?php _e('
                    Do you really want to delete this module ? ');?>`)) {
                return true;
            }
            return false;
        });

        $('.migrate-module').bind('click', function () {
            if (confirm(`<?php _e('
                    Do you really want to make a migration ? You should always have a backup of the current system
                    .
                    ');?>`)) {
                return true;
            }
            return false;
        });
    })
</script>