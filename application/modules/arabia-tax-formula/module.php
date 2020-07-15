<?php
namespace ArabiaTaxFormula;

use Tendoo_Module;
use ArabicTaxFormula\Inc\Actions;
use ArabicTaxFormula\Inc\Filters;

class Module extends Tendoo_Module {
    public function __construct()
    {
        parent::__construct();

        $this->filters      =   new Filters;
        $this->actions      =   new Actions;
        $this->events->add_filter( 'nexo_save_product', [ $this->filters, 'save_product' ]);
        $this->events->add_filter( 'nexo_update_product', [ $this->filters, 'save_product' ]);
        $this->events->add_action( 'load_pos_footer', [ $this->actions, 'load_pos_footer' ], 100 );
    }
}

new Module;