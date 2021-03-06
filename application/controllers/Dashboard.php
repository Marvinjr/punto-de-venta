<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Pecee\Http\Request;
use Pecee\Handlers\IExceptionHandler;
use Pecee\SimpleRouter\SimpleRouter as Route;
use Pecee\SimpleRouter\Exceptions\NotFoundHttpException;

class Dashboard extends Tendoo_Controller
{
    /**
     * Admin controller
     *
     * Maps to the following URL
     * 		http://example.com/index.php/admin
     *	- or -
     * 		http://example.com/index.php/admin/index
     *	- or -
     * this controller is in other words admin dashboard.
     */

    public function __construct()
    {
        parent::__construct();
        // $this->output->enable_profiler(TRUE);

        // $this->output->enable_profiler(TRUE);
        // All those variable are not required for option interface
        // Special assets loading for dashboard

        // include static libraries
        include_once( LIBPATH .'/Menu.php' );
        include_once( LIBPATH .'/Notice.php' );

        $this->load->model( 'Gui', null, 'gui' );
        $this->load->model( 'Update_Model'); // load update model @since 3.0
        $this->load->model( 'Dashboard_Model', 'dashboard' );

        // Load Assets/JS
        $this->_load_assets();

        // Loading Admin Menu
        // this action was move to Dashboard controler instead of aside.php output file.
        // which was called every time "create_dashboard_pages" was triggered
        $this->events->do_action( 'load_dashboard' );

        // Load CSS and JS
        $this->events->add_action( 'dashboard_header', array( $this, '_dashboard_header' ), 1 );
        $this->events->add_action( 'dashboard_footer', array( $this, '_dashboard_footer' ), 1 );
    }

    /**
     * Load dashboard widgets
     * @return void
    **/

    private function load_widgets()
    {
        // get global widget and cols
        global $AdminWidgets;
        global $AdminWidgetsCols;

        $SavedAdminWidgetsCols        =    $this->options->get('admin_widgets', User::id());
        $FinalAdminWidgetsPosition    =    array_merge($AdminWidgetsCols, force_array($SavedAdminWidgetsCols));

        // looping cols
        unset($this->Gui->cols[ 4 ]);
        // var_dump( $this->Gui->cols );die;

        for ($i = 1; $i <= count($this->Gui->cols); $i++) {
            $widgets_namespace    =    $this->dashboard_widgets->col_widgets($i);

            $this->Gui->col_width(1, 1);
            $this->Gui->col_width(2, 1);
            $this->Gui->col_width(3, 1);

            foreach ($widgets_namespace as $widget_namespace) {
                // get widget
                $widget_options    =    $this->dashboard_widgets->get($widget_namespace, User::id());
                // create meta
                $meta_array        =    array(
                    'col_id'    =>    $i,
                    'namespace'    =>    $widget_namespace,
                    'type'        =>    riake('type', $widget_options),
                    'title'        =>    riake('title', $widget_options)
                );

                $meta_array        =    array_merge($widget_options, $meta_array);
                $this->Gui->add_meta($meta_array);
                // create dom
                $this->Gui->add_item(array(
                    'type'        =>    'dom',
                    'content'    =>    riake('content', $widget_options) // $this->load->view( riake( 'content', $widget_options, '[empty_widget]' ), array(), true )
                ), $widget_namespace, $i);
            }
        }
    }

    /**
     * Dashboard Home Load
     *
     * @return void
    **/

    public function index()
    {
        $this->enqueue->js( 'angular-ui-sortable' );
        $this->enqueue->js( 'angular-queue' );
        $this->enqueue->js( 'angular-compile' );

        $this->events->add_filter( 'dashboard_dependencies', function( $deps ) {
            $deps[]     =   'ui.sortable';
            $deps[]     =   'ngQueue';
            $deps[]     =   'angular-bind-html-compile';
            return $deps;
        });

        // $this->events->add_action( 'dashboard_footer', function() {
        //     get_instance()->load->view( 'dashboard/index/script' );
        // });

        // load widget model here only
        // $this->load->model('Dashboard_Widgets_Model', 'dashboard_widgets');

        // trigger action while loading home (for registering widgets)
        $this->events->do_action('load_dashboard_home');
        
        // $this->load_widgets();

        $this->Gui->set_title(sprintf(__('Dashboard &mdash; %s'), get('core_signature')));
        $this->load->view('dashboard/index/body');
    }

    /**
     * Load Tendoo Setting Page
     * [New Permission Ready]
     * @return void
    **/

    public function settings()
    {
        // Can user access modules ?
        if (! User::can('create_options') &&
            ! User::can('edit_options') &&
            ! User::can('delete_options')
        ) {
            return show_error( __( 'You\'re not allowed to see that page' ) );
        }

        $this->Gui->set_title(sprintf(__('Settings &mdash; %s'), get('core_signature')));
        $this->load->view('dashboard/settings/body');
    }

    /**
     *  Dashboard Footer
     *  @param void
     *  @return void
    **/

    public function _dashboard_footer()
    {
        $this->events->do_action( 'common_footer' );
        $this->enqueue->load_css( 'dashboard_footer' );
        $this->enqueue->load_js( 'dashboard_footer' );
        $this->load->view( 'dashboard/js-footer' );
    }

    /**
     *  Dashboard header
     *  @param void
     *  @return void
    **/

    public function _dashboard_header()
    {
        $this->events->do_action( 'common_header' );
        $this->enqueue->load_css( 'dashboard_header' );
        $this->enqueue->load_js( 'dashboard_header' );
    }

    /**
     *  Load Assets
     *  @param void
     *  @return void
    **/

    private function _load_assets()
    {
        $this->enqueue->js_namespace( 'common_header' );
        // Underscore
        $this->enqueue->js('../plugins/underscore/underscore-min');
        $this->enqueue->js( 'tendoo.meta.options' );
        $this->enqueue->js('tendoo.core');

        // $this->enqueue->js_namespace( 'common_footer' );
        // Enqueuing slimscroll
        $this->enqueue->js('../plugins/slimScroll/jquery.slimscroll.min');
        $this->enqueue->js('../plugins/heartcode/heartcode-canvasloader-min');
        // Bootsrap Notify
        $this->enqueue->js('../plugins/bootstrap-notify-master/bootstrap-notify.min');
        // ParseParams
        $this->enqueue->js('jquery.parseParams');
        // Bootbox
        $this->enqueue->js('../plugins/bootbox/bootbox.min');

        $segments    = $this->uri->segment_array();
        if ( riake( 2, $segments, 'index' ) == 'index' ) {
            $this->enqueue->js( 'tendoo.widget.dragging' );
        }
    }

    /**
     * Remap controller methods
     *
     *
     * @access       public
     * @author       Blair Jersyer
     * @copyright    2015
     * @param        string $page part of segment
     * @param 		  array $params the siblings segments
     * @since        3.0.1
     */

    public function _remap($page, $params = array())
    {
        global $Routes;

        if (method_exists($this, $page)) {
            return call_user_func_array(array( $this, $page ), $params);
        } else {
            // Init Routes
            $Routes          =   new Route();

            // prefixed route
            $Routes->group([ 
                'prefix' => substr( request()->getHeader( 'script-name' ), 0, -10 ) . '/dashboard' 
            ], function() use ( $page, $Routes ) {

                $modules                =   Modules::get();
                
                foreach( $modules as $namespace => $module ) {
                    if( Modules::is_active( $namespace ) ) {
                        if( is_dir( $dir = MODULESPATH . $namespace . '/controllers/' ) ) {
                            foreach( glob( $dir . "*.php") as $filename) {
                                include_once( $filename );
                            }
                        }
            
                        if( is_file( MODULESPATH . $namespace . '/routes.php' ) ) {
                            include_once( MODULESPATH . $namespace . '/routes.php' );
                        }
                    }
                }
    
                $mu_modules             =   Modules::get( null, 'mu-modules' );
    
                foreach( $mu_modules as $namespace => $module ) {
                    if( is_dir( $dir = MU_MODULESPATH . $namespace . '/controllers/' ) ) {
                        foreach( glob( $dir . "*.php") as $filename) {
                            include_once( $filename );
                        }
                    }
        
                    if( is_file( MU_MODULESPATH . $namespace . '/routes.php' ) ) {
                        include_once( MU_MODULESPATH . $namespace . '/routes.php' );
                    }
                }
            });
    
            // Show Errors
            $Routes->error(function($request, \Exception $exception) {
                return show_error( sprintf( 
                    __( 'The request returned the following message : %s<br>Code : %s'  ),
                    $exception->getMessage(),
                    $exception->getCode()
                ), intval( $exception->getCode() ) );
            });
        
            // Start Route
            $Routes->start();
        }
    }

    /**
     * Module List and management controller
     * [New Permission Ready]
     *
     * @access       public
     * @author       Blair Jersyer
     * @copyright    name date
     * @param        string $page
     * @param		  string $arg2
     * @since        3.0.1
     */

    public function modules($page = 'list', $arg2 = null, $arg3 = null, $arg4 = null, $arg5 = null)
    {
        if ( $this->config->item( 'hide_modules' ) ) {
            return show_error( __( 'You\'re not allowed to see that page' ) );
        }

        if ($page === 'list') {
            // Can user access modules ?
            if (! User::can('install_modules') &&
                ! User::can('update_modules') &&
                ! User::can('delete_modules') &&
                ! User::can('toggle_modules')
            ) {
                return show_error( __( 'You\'re not allowed to see that page' ) );
            }

            $this->events->add_filter('gui_page_title', function ($title) {
                return '<section class="content-header"><h1>' . strip_tags($title) . ' <a class="btn btn-primary btn-sm pull-right" href="' . site_url(array( 'dashboard', 'modules', 'install_zip' )) . '">' . __('Upload a zip file') . '</a></h1></section>';
            });

            $this->events->add_action('displays_dashboard_errors', function () {
                if (isset($_GET[ 'extra' ])) {
                    echo tendoo_error(__('An error occured during module installation. There was a file conflict during module installation process.<br>This file seems to be already installed : ' . $_GET[ 'extra' ]));
                }
            });
            $this->Gui->set_title(sprintf(__('Module List &mdash; %s'), get('core_signature')));
            $this->load->view('dashboard/modules/list');
        } elseif ($page === 'install_zip') {

            // Can user update/install modules ?
            if (
                ! User::can('install_modules') ||
                ! User::can('update_modules')
            ) {
                return show_error( __( 'You\'re not allowed to see that page' ) );
            }

            $this->events->add_filter('gui_page_title', function ($title) {
                return '<section class="content-header"><h1>' . strip_tags($title) . ' <a class="btn btn-primary btn-sm pull-right" href="' . site_url(array( 'dashboard', 'modules' )) . '">' . __('Back to modules list') . '</a></h1></section>';
            });

            if (isset($_FILES[ 'extension_zip' ])) {
                $notice    =    Modules::install('extension_zip');
                // it means that module has been installed
                if (is_array($notice)) {
                    // Introducing Migrate
                    if (@$notice[ 'msg' ]    ==    'module-updated-migrate-required') {
                        redirect(array( 'dashboard', 'modules', 'migrate', @$notice[ 'namespace' ], @$notice[ 'from' ] ));
                    } else {
                        // Migration will start from this release
                        $this->options->set('migration_' . @$notice[ 'namespace' ], @$notice[ 'version' ], true);
                        // redirecting
                    redirect(array( 'dashboard', 'modules', 'list?highlight=' . @$notice[ 'namespace' ] . '&notice=' . $notice[ 'msg' ] . (isset($notice[ 'extra' ]) ? '&extra=' . $notice[ 'extra' ] : '') . '#module-' . $notice[ 'namespace' ] ));
                    }
                } else {
                    $this->notice->push_notice($this->lang->line($notice));
                }
            }
            $this->Gui->set_title(sprintf(__('Add a new extension &mdash; %s'), get('core_signature')));
            $this->load->view('dashboard/modules/install');
        } elseif ($page === 'enable') {

            // Can user access modules ?
            if (! User::can('toggle_modules')) {
                return show_error( __( 'You\'re not allowed to see that page' ) );
            }

            /**
             * Module should be enabled before trigger this action
            **/

            Modules::enable($arg2);

            // Enabling recently active module
            Modules::init('unique', $arg2);

            // Run the action
            $this->events->do_action('do_enable_module', $arg2);

            if ($this->events->did_action('do_enable_module')) {
                redirect(array( 'dashboard', 'modules?notice=' . $this->events->apply_filters('module_activation_status', 'module-enabled') ));
            }
        } elseif ($page === 'disable') {

            // Can user toggle modules ?
            if (! User::can('toggle_modules')) {
                return show_error( __( 'You\'re not allowed to see that page' ) );
            }

            $this->events->add_action('do_disable_module', function ($arg2) {
                Modules::disable($arg2);
            });

            $this->events->do_action('do_disable_module', $arg2);

            redirect(array( 'dashboard', 'modules?notice=' . $this->events->apply_filters('module_disabling_status', 'module-disabled') ));

        } elseif ($page === 'remove') {

            // Can user delete modules ?
            if (! User::can('delete_modules')) {
                return show_error( __( 'You\'re not allowed to see that page' ) );
            }

            $this->events->add_action('do_remove_module', function ($arg2) {
                Modules::uninstall($arg2);
                redirect(array( 'dashboard', 'modules?notice=module-removed' ));
            });

            $this->events->do_action('do_remove_module', $arg2);
        } elseif ($page === 'extract') {

            // Can user extract modules ?
            if (! User::can('extract_modules')) {
                return show_error( __( 'You\'re not allowed to see that page' ) );
            }

            $this->events->add_action('do_extract_module', function ($arg2) {
                Modules::extract($arg2);
            });

            $this->events->do_action('do_extract_module', $arg2);
        } elseif ($page == 'migrate' && $arg2 != null ) {
            
            if ( $arg3 === null ) {
                $arg3  =   get_option( 'migration_' . $arg2, '1.0' );
            }

            // Can user extract modules ?
            if (! User::can('update_modules')) {
                return show_error( __( 'You\'re not allowed to see that page' ) );
            }

            $module        =    Modules::get($arg2);

			if (! $module) {
                redirect(array( 'dashboard', 'module-not-found' ));
            }

            $this->Gui->set_title(sprintf(__('Migration &mdash; %s'), get('core_signature')));

            $this->load->view('dashboard/modules/migrate', array(
                'module'    =>  $module,
                'from'        =>    $arg3
            ));
        } elseif ($page == 'exec' && $arg2 != null && $arg3 != null && $arg4 != null) {

            // Can user extract modules ?
            if (! User::can('update_modules')) {
                return show_error( __( 'You\'re not allowed to see that page' ) );
            }

            $module        =    Modules::get($arg2);
            if (! $module) {
                echo json_encode(array(
                    'status'        =>    'failed',
                    'message'       =>    __('Unknow module')
                ));
            } else {    // If module exists
                $migrate_file        =    MODULESPATH . $module[ 'application' ][ 'namespace' ] . '/migrate.php';
                if (is_file($migrate_file)) {
                    ob_start();
                    $migration_array    =    include_once($migrate_file);
                    // If currrent migration version exists
                    if (@ $migration_array[ $arg4 ]) {
                        // if is file path, it's included
                        if (is_string($migration_array[ $arg4 ]) && is_file($migration_array[ $arg4 ])) {
                            // we asume this file exists
                            $result     =   @include_once($migration_array[ $arg4 ]);
                            if ( $result === false ) {
                                return;
                            }

                        // if it's callable, it's called
                        } elseif (is_callable($migration_array[ $arg4 ])) {
                            $function    =    $migration_array[ $arg4 ];
                            $function($module);
                        } else {
                            $content    =    false;
                        }
                        // When migrate is done the last version key is saved as previous migration version
                        // Next migration will start from here
                        $this->options->set( 'migration_' . $module[ 'application' ][ 'namespace' ], $arg4, true);
                    }
                    // Handling error
                    $content    =    ob_get_clean();
                    // If not error occured
                    if (empty($content)) {
                        echo json_encode(array(
                            'status'        =>    'success',
                            'message'       =>    __('Migration done.')
                        ));
                    } else { // else

                        if ($content === false) {
                            echo json_encode(array(
                                'status'        =>    'failed',
                                'message'       =>    sprintf(__('File not found or incorrect executable provided.'))
                            ));
                        } else {
                            echo json_encode(array(
                                'status'        =>    'failed',
                                'message'       =>    sprintf(__('An error occured'))
                            ));
                        }
                    }
                } else {
                    echo json_encode(array(
                        'status'        =>    'failed',
                        'message'       =>    __('Migration File not found.')
                    ));
                }
            }
        } elseif ( $page === 'build-assets' ) {
            // Can user extract modules ?
            if (! User::can('update_modules')) {
                return show_error( __( 'You\'re not allowed to see that page' ) );
            }

            Modules::buildAssets( $arg2 );
            return redirect([ 'dashboard', 'modules?notice=assets-builded' ]);
        } elseif ( $page === 'publish-assets' ) {
            // Can user extract modules ?
            if (! User::can('update_modules')) {
                return show_error( __( 'You\'re not allowed to see that page' ) );
            }
            
            Modules::extractAssets( $arg2 );
            return redirect([ 'dashboard', 'modules?notice=assets-published' ]);
        } elseif ( $page === 'reset-setup' ) {
            /**
             * For development purpose
             * helps to reset a module migration
             */
            $module     =   Modules::get( $arg2 );
            $action     =   $this->events->apply_filters( 'tendoo_reset_' . $module[ 'application' ][ 'namespace' ], 'reset-not-handled' );
            return redirect([ 'dashboard', 'modules?notice=' . ( ! empty( $action ) ? $action : 'reset-not-properly-handled' ) . '&highlight=' . $module[ 'application' ][ 'namespace' ] . '#module-' . $module[ 'application' ][ 'namespace' ] ]);
        }
    }

    /**
     * Options Management ocntroller
     * [New Permission Ready]
     *
     * @access       public
     * @author       blair Jersyer
     * @copyright    name date
     * @param        string $page
     * @param		 string $arg2
     * @since        3.0.1
     */

    public function options($mode = 'list')
    {
        if (in_array($mode, array( 'save', 'merge' ))) {

            // Can user extract modules ?
            if (! User::can('create_options')) {
                return show_error( __( 'You\'re not allowed to see that page' ) );
            }

            if (! $this->input->post('gui_saver_ref') && ! $this->input->post('gui_json')) {
                // if JSON mode is enabled redirect is disabled
                redirect(array( 'dashboard', 'options' ));
            }

            if ($this->input->post('gui_saver_expiration_time') >  gmt_to_local(time(), 'UTC')) {
                $content    =    array();

                // loping post value
                global $Options;
                foreach ($_POST as $key => $value) {
                    if (! in_array($key, array( 'gui_saver_option_namespace', 'gui_saver_ref', 'gui_saver_expiration_time', 'gui_saver_use_namespace', 'gui_delete_option_field', 'gui_json', 'gui_saver_user_id' ))) {
                        /**
                         * Merge options which a supposed to be wrapped within the same array
                        **/

                        if ($mode == 'merge' && is_array($value)) {
                            $options    =    $this->options->get($key);
                            $options    =    array_merge(force_array($options), $value);
                        }
                        // save only when it's not an array
                        if (! is_array($_POST[ $key ])) {
                            if ($this->input->post('gui_saver_use_namespace') === 'true') {
                                $content[ $key ]    =    ($mode == 'merge') ? $options : $this->input->post($key);
                            } else {
                                if ($mode == 'merge' && is_array($value)) {
                                    $this->options->set(
                                        $key, 
                                        $options, 
                                        true, 
                                        @$this->input->post( 'gui_saver_user_id' ) ? $this->input->post( 'gui_saver_user_id' ) : 0
                                    );
                                } else {
                                    $this->options->set(
                                        $key, 
                                        $this->input->post($key), 
                                        true,
                                        @$this->input->post( 'gui_saver_user_id' ) ? $this->input->post( 'gui_saver_user_id' ) : 0
                                    );
                                }
                            }
                        } else {
                            if ($this->input->post('gui_saver_use_namespace') === 'true') {
                                $content[ $key ]    =    ($mode == 'merge') ? $options : xss_clean($_POST[ $key ]);
                            } else {
                                if ($mode == 'merge' && is_array($value)) {
                                    $this->options->set(
                                        $key, 
                                        $options, 
                                        true,
                                        @$this->input->post( 'gui_saver_user_id' ) ? $this->input->post( 'gui_saver_user_id' ) : 0
                                    );
                                } else {
                                    $this->options->set(
                                        $key, 
                                        xss_clean($_POST[ $key ]), 
                                        true,
                                        @$this->input->post( 'gui_saver_user_id' ) ? $this->input->post( 'gui_saver_user_id' ) : 0
                                    );
                                }
                            }
                        }
                    }
                    // Fix Checkbox bug, when submiting unchecked box
                    elseif ($key == 'gui_delete_option_field') {
                        foreach (force_array($_POST[ 'gui_delete_option_field' ]) as $field_to_delete) {
                            if ($this->input->post('gui_saver_use_namespace') === 'true') {
                                unset($Options[ $this->input->post('gui_saver_option_namespace') ][ $field_to_delete ]);
                                $this->options->set(
                                    $this->input->post('gui_saver_option_namespace'), 
                                    $Options[ $this->input->post('gui_saver_option_namespace') ],
                                    true,
                                    @$this->input->post( 'gui_saver_user_id' ) ? $this->input->post( 'gui_saver_user_id' ) : 0
                                );
                            } else {
                                $this->options->delete($field_to_delete);
                            }
                        }
                    }
                }

                // saving all post using namespace
                if ($this->input->post('gui_saver_use_namespace') == 'true') {
                    $this->options->set(
                        $this->input->post('gui_saver_option_namespace'), 
                        $content, 
                        true,
                        @$this->input->post( 'gui_saver_user_id' ) ? $this->input->post( 'gui_saver_user_id' ) : 0
                    );
                }

                $ref        =   @$_SERVER[ 'HTTP_REFERER' ] === null ? $this->input->post('gui_saver_ref') : $_SERVER[ 'HTTP_REFERER' ];
                $hasQuery   =   strpos( $ref, '?' );
                if (! $this->input->post('gui_json')) { // if JSON mode is enabled redirect is disabled
                    redirect( $ref . ( $hasQuery === false ? '?' : '&' ) . 'notice=option-saved');
                }
            }
        } elseif ($mode == 'get') {

            // Can user extract modules ?
            if (! User::can('read_options')) {
                return show_error( __( 'You\'re not allowed to see that page' ) );
            }

            // Since Option Module already decode JSON
            // Fix bug
            // @since 3.0.5
            echo json_encode($this->options->get(xss_clean($_POST[ 'option_key' ])));
        } elseif (in_array($mode, array( 'save_user_meta', 'merge_user_meta' ))) {
            if (! User::can('edit_profile')) {
                return show_error( __( 'You\'re not allowed to see that page' ) );
            }

            if ($this->input->post('gui_saver_expiration_time') >  gmt_to_local(time(), 'UTC')) {
                $content    =    array();
                // loping post value
                foreach ($_POST as $key => $value) {
                    if (! in_array($key, array( 'gui_saver_option_namespace', 'gui_saver_ref', 'gui_saver_expiration_time', 'gui_saver_use_namespace', 'user_id' ))) {
                        if ($mode == 'merge_user_meta' && is_array($value)) {
                            $options    =    $this->options->get($key);
                            $options    =    array_merge(force_array($options), $value);
                        }
                        // save only when it's not an array
                        if (! is_array($_POST[ $key ])) {
                            if ($this->input->post('gui_saver_use_namespace') === 'true') {
                                $content[ $key ]    =    ($mode == 'merge') ? $options : $this->input->post($key);
                            } else {
                                if ($mode == 'merge_user_meta' && is_array($value)) {
                                    $this->options->set($key, $options, true, $this->input->post('user_id'));
                                } else {
                                    $this->options->set($key, $this->input->post($key), true, $this->input->post('user_id'));
                                }
                            }
                        } else {
                            if ($this->input->post('gui_saver_use_namespace') === 'true') {
                                $content[ $key ]    =    ($mode == 'merge') ? $options : xss_clean($_POST[ $key ]);
                            } else {
                                if ($mode == 'merge_user_meta' && is_array($value)) {
                                    $this->options->set($key, $options, true, $this->input->post('user_id'));
                                } else {
                                    $this->options->set($key, xss_clean($_POST[ $key ]), true, $this->input->post('user_id'));
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Options Management ocntroller
     * [New Permission Ready]
     *
     * @access       public
     * @author       Blair Jersyer
     * @copyright    name date
     * @param        string $page
     * @param		  string $arg2
     * @since        3.0.1
     */
    public function update($page = 'home',  $version = null)
    {
        if (! User::can('manage_core')) {
            return show_error( __( 'You\'re not allowed to see that page' ) );
        }

        if ($page === 'core') {
            $this->Gui->set_title(sprintf(__('Updating... &mdash; %s'), get('core_signature')));

            $this->load->view('dashboard/update/core', array(
                'release'    =>    $version
            ));
        } elseif ($page === 'download') {
            echo json_encode($this->Update_Model->install(1, $version));
        } elseif ($page === 'extract') {
            echo json_encode($this->Update_Model->install(2));
        } elseif ($page === 'install') {
            echo json_encode($this->Update_Model->install(3));
        } else {
            $this->Gui->set_title(sprintf(__('Update Center &mdash; %s'), get('core_signature')));
            $this->load->view('dashboard/update/home', array());
        }
    }

    /**
     * About controller
     * [New Permission Ready]
     *
     * @access       public
     * @author       blair Jersyer
     * @copyright    name date
     * @since        3.0.1
     */

    public function about()
    {
        if (! User::can('manage_core')) {
            return show_error( __( 'You\'re not allowed to see that page' ) );
        }

        $this->events-> add_filter('gui_page_title', function () { // disabling header
            return;
        });

        $this->Gui->set_title(sprintf(__('About &mdash; %s'), get('core_signature')));
        $this->load->view('dashboard/about/body');
    }
}
