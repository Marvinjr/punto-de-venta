<?php
namespace LanguageSwitcher\Inc;

use Tendoo_Module;
use User;

class Actions extends Tendoo_Module 
{
    public function dashboard_footer()
    {
        $this->load->module_view( 'lang-switcher', 'footer' );
    }

    public function after_app_init()
    {
        $siteLanguage     =   get_instance()->session->userdata( 'site_language' );

        if ( $siteLanguage !==  $this->input->get( 'lang' ) && ! empty( $this->input->get( 'lang' ) ) ) {
            if( ! empty( $this->input->get( 'lang' ) ) ) {
                $lang   =  $this->input->get( 'lang' );
            } else {
                die( 'ok' );
                $lang   =   'en_US';
            }
            
            set_option( 'site_language',  ( string ) $lang );
            get_instance()->session->set_userdata( 'site_language', $lang );
            redirect( current_url() );
        }
    }
}