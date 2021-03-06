<?php
// Auto Load
require_once(dirname(__FILE__) . '/vendor/autoload.php');
include_once(dirname(__FILE__) . '/inc/actions.php');
include_once(dirname(__FILE__) . '/inc/filters.php');
include_once(dirname(__FILE__) . '/inc/helpers.php');
include_once(dirname(__FILE__) . '/inc/install.php');
include_once(dirname(__FILE__) . '/inc/log.php');
include_once(dirname(__FILE__) . '/inc/OrderReceipt.php');

if (get_instance()->setup->is_installed()) {
    include_once(dirname(__FILE__) . '/inc/controller.php');
    include_once(dirname(__FILE__) . '/inc/tours.php');
}

class Nexo_Main extends CI_Model
{
    public function __construct()
    {
		global $PageNow;

		// Default PageNow value
		$PageNow	=	'nexo/index';

        parent::__construct();
        $this->actions      =   new Nexo_Actions;
        $this->filters      =   new Nexo_Filters;
        $this->log          =   new NexoLogWrapper;

        
        $this->load->helper('nexopos');
        $this->load->module_model( 'nexo', 'Nexo_Notices_Model', 'Nexo_Notices' );
        
        define('NEXO_BARCODE_PATH', get_store_upload_path() . '/codebar/');

        $this->events->add_action( 'load_dashboard_home', [ $this->actions, 'init' ] );
        $this->events->add_action( 'load_frontend', [ $this->actions, 'load_frontend' ], 10, 2 );
        $this->events->add_action( 'dashboard_footer', [ $this->actions, 'dashboard_footer' ] );
        $this->events->add_action( 'dashboard_header', [ $this->actions, 'dashboard_header' ] );
        $this->events->add_action( 'after_app_init', [ $this->actions, 'after_app_init' ] );
        $this->events->add_action( 'load_dashboard', [ $this->actions, 'load_dashboard' ], 5 );
        $this->events->add_action( 'nexo_order_refunded', [ $this->actions, 'order_refuned' ], 10, 1 );
        $this->events->add_action( 'nexo_order_refunded', [ $this->actions, 'stock_release' ], 10, 1 );
        $this->events->add_action( 'nexo_delete_order', [ $this->actions, 'delete_order' ], 10, 1 );
        $this->events->add_action( 'after_update_supply_history', [ $this->actions, 'update_supply_history' ], 10, 3 );
        $this->events->add_action( 'pos_ready', [ $this->actions, 'pos_ready' ]);

        // $this->events->add_filter( 'ui_notices', [ $this->filters, 'ui_notices' ] );
        $this->events->add_filter( 'default_js_libraries', [ $this->filters, 'default_js_libraries' ] );        
        $this->events->add_filter( 'nexo_daily_details_link', [ $this->filters, 'remove_link' ] );        
        $this->events->add_filter( 'nexo_cart_buttons', [ $this->filters, 'nexo_cart_buttons' ] );
        $this->events->add_filter( 'login_redirection', [ $this->filters, 'login_redirection' ] );
        $this->events->add_filter( 'dashboard_dependencies', [ $this->filters, 'dashboard_dependencies' ] );
        $this->events->add_filter( 'signin_logo', [ $this->filters, 'signin_logo' ] );
        $this->events->add_filter( 'dashboard_footer_right', [ $this->filters, 'dashboard_footer_right' ] );
        $this->events->add_filter( 'dashboard_logo_long', [ $this->filters, 'dashboard_logo_long' ]);
        $this->events->add_filter( 'dashboard_logo_small', [ $this->filters, 'dashboard_logo_small' ], 10, 1 );
        $this->events->add_filter( 'dashboard_footer_text', [ $this->filters, 'dashboard_footer_text' ] );
        $this->events->add_filter( 'nexo_store_menus', [ $this->filters, 'store_menus' ] );
        $this->events->add_filter( 'ac_filter_get_request', [ $this->filters, 'ac_filter_get_request' ] ); // Awesome CRUD
        $this->events->add_filter( 'ac_delete_entry', [ $this->filters, 'ac_delete_entry' ] );
        $this->events->add_filter( 'after_order_placed_details', [ $this->filters, 'post_order_details' ], 9, 2 );
        $this->events->add_filter( 'nexo_customers_basic_fields', [ $this->filters, 'add_customers_accounts_fields' ]);
        $this->events->add_filter( 'nexo_filters_customers_post_fields', [ $this->filters, 'filter_customer_post_fields' ], 10, 2 );
        $this->events->add_filter( 'nexo_filters_customers_put_fields', [ $this->filters, 'filter_customer_post_fields' ], 10, 3 );
        $this->events->add_filter( 'nexo_clients_columns', [ $this->filters, 'filter_nexo_clients_columns' ], 10, 1 );

        /**
         * Register log and history events
         */
        $this->events->add_action( 'nexo_delete_order', [ $this->log, 'delete_order' ], 10, 1 );
        $this->events->add_filter( 'after_submit_order', [ $this->log, 'after_submit_order' ], 10, 1 );
        $this->events->add_action( 'delete_products', [ $this->log, 'delete_products' ], 10, 1 );
        $this->events->add_action( 'update_stock', [ $this->log, 'update_stock' ], 10, 1 );
        $this->events->add_action( 'create_grouped_products', [ $this->log, 'create_grouped_products' ], 10, 1 );
        $this->events->add_filter( 'nexo_update_product', [ $this->log, 'nexo_update_product' ], 10, 1 );
        $this->events->add_filter( 'nexo_save_product', [ $this->log, 'nexo_save_product' ], 10, 1 );

        /**
         * clearing cache wisely
         */
        $this->events->add_filter( 'nexo_after_update_product', [ $this->actions, 'clear_pos_cache' ]);
        $this->events->add_filter( 'nexo_after_save_product', [ $this->actions, 'clear_pos_cache' ]);
        $this->events->add_filter( 'delete_products', [ $this->actions, 'clear_pos_cache' ]);

        /**
         * updating register total cash
         */
        // $this->events->add_filter( 'after_submit_order', [ $this->filters, 'update_register_total_cash' ]);
        $this->events->add_action( 'nexo_orders_payments_updated', [ $this->actions, 'orders_payments_updated' ]);
    }
}
new Nexo_Main;
