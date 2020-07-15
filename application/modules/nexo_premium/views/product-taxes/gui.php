<?php
$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
     'type'			=>    'unwrapped',
     'col_id'		=>    1,
     'namespace'	=>    'nexo_premium_product_taxes'
));

$this->Gui->add_item( array(
     'type'          =>    'dom',
     'content'       =>    $this->load->module_view( 'nexo_premium', 'product-taxes.dom', null, true )
), 'nexo_premium_product_taxes', 1 );

$this->Gui->output();