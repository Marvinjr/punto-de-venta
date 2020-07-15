<?php

class ApiLangSwitcher extends Tendoo_Api
{
    public function __construct()
    {
        parent::__construct();
    }

    public function changeLanguage()
    {
        $this->options->set( 'ls-language', $this->post( 'lang' ), true, User::id() );
        
        return $this->response([
            'status'    =>  'success',
            'message'   =>  __( 'The language has been defined.', 'lang-switcher' )
        ]);
    }
}