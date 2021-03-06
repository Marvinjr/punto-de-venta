<div class="row checkout-header" ng-controller="checkoutHeaderCTRL" style="padding-bottom:10px">
    <div class="col-lg-6 left-button-columns">
        <?php
        /**
         * If cashier see order list
         */

        $menus  =   [];
        if ( User::can( 'nexo.view.orders' ) ) {
            $menus[]    =   [
                'class' =>  'default',
                'text'  =>  __( 'Tableau de bord', 'nexo' ),
                'icon'  =>  'home',
                'attrs' =>  [
                    'ng-click'  =>  'goTo( \'' . dashboard_url([ 'orders' ] ) . '\' )'
                ]
            ];
        }
        
        /**
         * If cashier can use Hold order button
         */
        if ( true ) {
            $menus[]    =   [
                'class' =>  'default history-box-button',
                'text'  =>  __( 'En Attente', 'nexo' ),
                'icon'  =>  'history',
                'attrs' =>  [
                    'ng-click'  =>  'openHistoryBox()'
                ]
            ];
        }
        
        foreach( $this->events->apply_filters( 'checkout_header_menus_1', $menus ) as $menu ) {
            $attrs      =   '';
            if( is_array( @$menu[ 'attrs' ] ) ) {
                foreach( @$menu[ 'attrs' ] as $name => $value ) {
                    $attrs  .= $name . '="' . $value . '" ';
                }
            }

            include( dirname( __FILE__ ) . '/button.php' );
        };?>
    </div>
    <div class="col-lg-6 right-button-columns">
        <?php 
        $buttons    =   [
            [
                'class' =>  'default',
                'icon'  =>  'window-maximize',
                'attrs' =>  [
                    'ng-click'  =>  'openFullScreen()'
                ]
            ],[
                'class' =>  'default calculator-button',
                'text'  =>  __( 'Calculatrice', 'nexo' ),
                'icon'  =>  'calculator',
                'attrs' =>  [
                    'ng-click'  =>  'openCalculator()',
                ]
            ], [
                'class' =>  'warning',
                'text'  =>  __( 'Déconnexion', 'nexo' ),
                'icon'  =>  'sign-out',
                'attrs' =>  [
                    'ng-click'  =>  'signOut()',
                ]
            ]
        ];

        if ( store_option( 'nexo_use_cashrawer' ) === 'yes' ) {
            $buttons[]  =   [
                'class' =>  'default',
                'icon'  =>  'toggle-up',
                'attrs' =>  [
                    'id'  =>  'drawer-vue',
                    '@click'    =>  'openCashDrawer()'
                ]
            ];
        }

        foreach( $this->events->apply_filters( 'checkout_header_menus_2', $buttons ) as $menu ) {
            $attrs      =   '';
            if( is_array( @$menu[ 'attrs' ] ) ) {
                foreach( @$menu[ 'attrs' ] as $name => $value ) {
                    $attrs  .= $name . '="' . $value . '" ';
                }
            }
            
            include( dirname( __FILE__ ) . '/button.php' );
        };?>
    </div>
    <!--<div class="col-md-12 hidden-lg" style="margin-bottom:10px">
        <?php foreach( $this->events->apply_filters( 'checkout_header_menus_1', [
            [
                'class' =>  'default',
                'text'  =>  __( 'Tableau de bord', 'nexo' ),
                'icon'  =>  'home',
                'attrs' =>  [
                    'ng-click'  =>  'goTo( \'' . dashboard_url([ 'commandes', 'lists' ] ) . '\' )'
                ]
            ], [
                'class' =>  'default calculator-button',
                'text'  =>  __( 'Calculatrice', 'nexo' ),
                'icon'  =>  'calculator',
                'attrs' =>  [
                    'ng-click'  =>  'openCalculator()',
                ]
            ], [
                'class' =>  'default history-box-button',
                'text'  =>  __( 'En Attente', 'nexo' ),
                'icon'  =>  'history',
                'attrs' =>  [
                    'ng-click'  =>  'openHistoryBox()'
                ]
            ]
        ]) as $menu ) {
            $attrs      =   '';
            if( is_array( @$menu[ 'attrs' ] ) ) {
                foreach( @$menu[ 'attrs' ] as $name => $value ) {
                    $attrs  .= $name . '="' . $value . '" ';
                }
            }
            ?>
            <button <?php echo $attrs;?> class="btn btn-sm btn-<?php echo @$menu[ 'class' ] == null ? 'default' : $menu[ 'class' ];?>">
                <i class="fa fa-<?php echo @$menu[ 'icon' ];?>"></i> <?php echo @$menu[ 'text' ];?>
            </button>
            <?php
        };?>
        <?php foreach( $this->events->apply_filters( 'checkout_header_menus_2', [
            [
                'class' =>  'default',
                'icon'  =>  'window-maximize',
                'attrs' =>  [
                    'ng-click'  =>  'openFullScreen()'
                ]
            ],
        ]) as $menu ) {
            $attrs      =   '';
            if( is_array( @$menu[ 'attrs' ] ) ) {
                foreach( @$menu[ 'attrs' ] as $name => $value ) {
                    $attrs  .= $name . '="' . $value . '" ';
                }
            }
            ?>
            <button <?php echo $attrs;?> class="btn btn-sm btn-<?php echo @$menu[ 'class' ] == null ? 'default' : $menu[ 'class' ];?>">
                <i class="fa fa-<?php echo @$menu[ 'icon' ];?>"></i> <?php echo @$menu[ 'text' ];?>
            </button>
            <?php
        };?>
    </div>-->
</div>
<style type="text/css">
body > div.wrapper > div > div.content {
    padding-top: 10px;
}

.checkout-header .btn {
    box-shadow: 1px 1px 1px 0px #909090;
    border: solid 1px #cacaca;
    margin-right: 8px;
}

.checkout-header .btn-warning {
    background: #ff6262;
    color: #FFF;
    border: solid 1px #ca0000;
}
.shadowed-dropdown {
    box-shadow: 0px 2px 5px 0px #717171;
    border: solid 1px #9a9a9a;
}
</style>