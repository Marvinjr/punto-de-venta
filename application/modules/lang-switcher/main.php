<?php
namespace LanguageSwitcher;

use Tendoo_Module;
use LanguageSwitcher\Inc\Actions;
use LanguageSwitcher\Inc\Filters;

class Module extends Tendoo_Module
{
    protected $session;
    protected $language;

    public function __construct()
    {
        parent::__construct();

        $this->actions      =   new Actions;
        $this->filters      =   new Filters;
        $this->session      =   get_instance()->session;
        
        $this->load->config( 'tendoo' );

        $this->languages    =   $this->config->item( 'supported_languages' );

        $this->events->add_action( 'dashboard_footer', [ $this->actions, 'dashboard_footer' ]);
        $this->events->add_action( 'after_app_init', [ $this->actions, 'after_app_init' ], 30 );
        $this->events->add_filter( 'site_language', [ $this->filters, 'site_language' ]);
    }
}
new Module;