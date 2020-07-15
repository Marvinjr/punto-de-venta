<?php
namespace ArabicTaxFormula\Inc;

use Tendoo_Module;

class Actions extends Tendoo_Module 
{
    public function __construct()
    {
        parent::__construct();
    }

    public function load_pos_footer()
    {
        return $this->load->module_view( 'arabia-tax-formula', 'registers.footer' );
    }
}