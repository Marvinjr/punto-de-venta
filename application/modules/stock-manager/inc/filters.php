<?php
class Nexo_Stock_Manager_Filters extends Tendoo_Module
{
    /**
     * Filter Admin Menus
     * @param array
     * @return array
    **/

    public function admin_menus( $menus )
    {
        // echo json_encode( $menus );die;
        if( multistore_enabled() ) {
            if( ! is_multistore() ) {

                $menus          =   array_insert_after( 'nexo_shop', $menus, 'stock-manager', [
                    [
                        'title'     =>  __( 'Stock Transfert', 'stock-manager' ),
                        'href'      =>  '#',
                        'icon'      =>  'fa fa-exchange',
                        'disable'   =>  true,
                        'permission'    =>  'nexo.manage.transfers'
                    ],
                    [
                        'title'     =>  __( 'Transfert History', 'stock-manager' ),
                        'href'      =>  dashboard_url([ 'transfert' ]),
                    ],
                    [
                        'title'     =>  __( 'New Transfert', 'stock-manager' ),
                        'href'      =>  dashboard_url([ 'transfert', 'add' ]),
                    ],
                    [
                        'title'     =>  __( 'New Request', 'stock-manager' ),
                        'href'      =>  dashboard_url([ 'transfert', 'request' ]),
                    ],
                    [
                        'title'     =>  __( 'Transfert Settings', 'stock-manager' ),
                        'href'      =>  dashboard_url([ 'settings', 'stock' ]),
                    ]
                ]);

                if (
                    User::can('nexo.create.items') ||
                    User::can('nexo.create.categories') ||
                    User::can('nexo.create.providers') ||
                    User::can('nexo.create.shippings')
                ) {
                    $menus                      =   array_insert_after( 'stock-manager', $menus, 'arrivages', array(
                        array(
                            'title'        =>    __('Inventaire', 'nexo'),
                            'href'        =>    '#',
                            'disable'    =>    true,
                            'icon'        =>    'fa fa-archive',
                            'some-permissions'	=>	[
                                'nexo.view.items',
                                'nexo.view.categories',
                                'nexo.view.departments',
                                'nexo.view.supplies',
                                'nexo.view.taxes',
                            ]
                        ),
                        array(
                            'title'        =>    __('Approvisionnements', 'nexo'),
                            'href'        =>    dashboard_url([ 'supplies' ]),
                            'permission'    =>  'nexo.view.supplies'
                        ),
                        array(
                            'title'        =>    __('Nouvel Approvisionnement', 'nexo'),
                            'href'        =>    dashboard_url([ 'supplies', 'stock' ]),
                            'permission'    =>  'nexo.create.supplies',
                        ),
                        // @since 3.0.20
                        array(
                            'title'		=>	__( 'Ajustement des quantités', 'nexo' ),
                            'href'		=>		dashboard_url([ 'items', 'stock-adjustment' ] ),
                            'permission'    =>  'nexo.edit.items'
                        ),
                        array(
                            'title'        =>    __('Liste des articles', 'nexo'),
                            'href'        =>    dashboard_url([ 'items' ]),
                            'permission'    =>  'nexo.view.items'
                        ),
                        array(
                            'title'        =>    __('Ajouter un article', 'nexo'),
                            'href'        =>    dashboard_url([ 'items' ,'add' ]),
                            'permission'    =>  'nexo.create.items'
                        ),
                        array(
                            'title'        =>    __('Ajouter des produits groupés', 'nexo'),
                            'href'        =>    dashboard_url([ 'grouped-items' ,'add' ]),
                            'permission'    =>  'nexo.create.items'
                        ),
                        array(
                            'title'         =>  __( 'Importer les articles', 'nexo' ),
                            'href'          =>  dashboard_url([ 'items', 'import' ]),
                            'permission'    =>  'nexo.create.items'
                        ),
                        array(
                            'title'        =>    __('Liste des taxes', 'nexo'),
                            'href'		=>	dashboard_url([ 'taxes']),
                            'permission'    =>  'nexo.view.taxes'
                        ),
                        array(
                            'title'        =>    __('Ajouter une taxe', 'nexo'),
                            'href'        =>    dashboard_url([ 'taxes', 'add' ]),
                            'permission'    =>  'nexo.create.taxes'
                        ),
                        array(
                            'title'        =>    __('Liste des catégories', 'nexo'),
                            'href'        =>    dashboard_url([ 'categories']),
                            'permission'    =>  'nexo.view.categories'
                        ),
                        [
                            'title'			=>	__( 'Organisation des catégories', 'nexo' ),
                            'href'			=>	dashboard_url([ 'categories-reorder' ]),
                            'premission'	=>	'nexo.edit.items'
                        ],
                        array(
                            'title'        =>    __('Ajouter une catégorie', 'nexo'),
                            'href'        	=>    dashboard_url([ 'categories', 'add' ]),
                            'permission'    =>  'nexo.create.categories'
                        )
                    ));
                    
                    $menus                      =   array_insert_after( 'arrivages', $menus, 'vendors', array(
                        array(
                            'title'        =>    __('Suppliers', 'stock-manager'),
                            'disable'        =>  true,
                            'href'			=>	'#',
                            'icon'			=>	'fa fa-truck'
                        ),
                        array(
                            'title'        =>    __('Suppliers List', 'stock-manager'),
                            'href'        =>    dashboard_url([ 'providers']),
                        ),
                        array(
                            'title'        =>    __('Add a supplier', 'stock-manager'),
                            'href'        =>    dashboard_url([ 'providers', 'add' ]),
                        ),
                    ) );

                    $menus                      =   array_insert_after( 'arrivages', $menus, 'warehouse-settings', array(
                        array(
                            'title'        =>    __('Warehouse Settings', 'stock-manager'),
                            'href'			=>	dashboard_url([ 'settings' ]),
                            'icon'			=>	'fa fa-wrench',
                            'permission'   =>  [
                                'nexo.warehouse.settings'
                            ]
                        ),
                        array(
                            'title'        =>    __('Others Settings', 'stock-manager'),
                            'href'			=>	dashboard_url([ 'settings', 'checkout' ]),
                            'icon'			=>	'fa fa-wrench',
                            'permission'    =>  'nexo.warehouse.settings',
                        ),
                        array(
                            'title'        =>    __('Receipt & Invoice', 'stock-manager'),
                            'href'			=>	dashboard_url([ 'settings', 'invoices' ]),
                            'icon'			=>	'fa fa-wrench',
                            'permisision'   =>  'nexo.warehouse.settings'
                        )
                    ) );
                }
            } else {
                $menus          =   array_insert_after( 'arrivages', $menus, 'stock-manager', [
                    [
                        'title'     =>  __( 'Stock Transfert', 'stock-manager' ),
                        'href'      =>  '#',
                        'icon'      =>  'fa fa-exchange',
                        'disable'   =>  true,
                        'permission'    =>  'nexo.manage.transfers'
                    ],
                    [
                        'title'     =>  __( 'Transfert History', 'stock-manager' ),
                        'href'      =>  dashboard_url([ 'transfert' ]),
                    ],
                    [
                        'title'     =>  __( 'New Transfert', 'stock-manager' ),
                        'href'      =>  dashboard_url([ 'transfert', 'add' ]),
                    ],
                    [
                        'title'     =>  __( 'New Request', 'stock-manager' ),
                        'href'      =>  dashboard_url([ 'transfert', 'request' ]),
                    ],
                    [
                        'title'     =>  __( 'Transfert Settings', 'stock-manager' ),
                        'href'      =>  dashboard_url([ 'settings', 'stock' ]),
                    ]
                ]);
            }
        }
        return $menus;
    }
}