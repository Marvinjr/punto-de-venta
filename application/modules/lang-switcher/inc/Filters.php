<?php
namespace LanguageSwitcher\Inc;

use Tendoo_Module;
use User;

class Filters extends Tendoo_Module 
{
    public function site_language( $language )
    {
        $userLanguage   =   $this->options->get( 'ls-language', User::id() );

        if ( ! empty( $userLanguage ) ) {
            return $userLanguage;
        }

        return $language;
    }
}