<?php
namespace SafeDelivery;

use Tendoo_Module;

class Module extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();

        $this->events->add_action( 'load_pos_footer', function() {
            get_instance()->load->module_view( 'safe-delivery', 'script' );
        });
    }
}

new Module;