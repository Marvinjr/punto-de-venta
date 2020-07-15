<?php 
use Carbon\Carbon;
?>
<script>
    <?php $dateString    =   Carbon::parse( date_now() )->toDateString();?>
    var report      =   <?php echo json_encode([
        'url'       =>  dashboard_url([ 'reports', 'json-daily-log', store_get_param( '?' ) ]),
        'today'     =>  dashboard_url([ 'reports', 'save-daily-log', store_get_param( '?' ) . '&date=' . $dateString ]),
        'week'      =>  dashboard_url([ 'reports', 'save-daily-log', store_get_param( '?' ) ]),
        'labels'    =>  [
            __( 'Lundi', 'nexo' ),
            __( 'Mardi', 'nexo' ),
            __( 'Mercredi', 'nexo' ),
            __( 'Jeudi', 'nexo' ),
            __( 'Vendredi', 'nexo' ),
            __( 'Samedi', 'nexo' ),
            __( 'Dimanche', 'nexo' ),
        ],
        'dateFormat'        =>  store_option( 'nexo_js_datetime_format', 'YYYY-MM-DD' ),
        'textDomain'        =>  [
            'cantProceed'       =>  __( 'Impossible de continuer', 'nexo' ),
            'weekReachLimit'    =>  __( 'Vous ne pouvez pas afficher un rapport pour une semaine qui n\'a pas encore été atteinte', 'nexo' ),
        ],
        'weekStarts'        =>  Carbon::parse( date_now() )->startOfWeek()->toDateString(),
        'weekEnds'          =>  Carbon::parse( date_now() )->endOfWeek()->toDateString(),
        'serverDate'        =>  date_now(),
        'totalPaid'         =>  __( 'Commandes Payées', 'nexo' ),
        'totalRefunds'      =>  __( 'Commandes Remboursées', 'nexo' ),
        'totalUnpaid'       =>  __( 'Commandes Impayées', 'nexo' ),
        'totalPartially'    =>  __( 'Commandes Partielles', 'nexo' ),
        'totalSales'        =>  __( 'Toutes les ventes', 'nexo' ),
    ]);?>
</script>
<?php include_once( MODULESPATH . 'nexo/inc/angular/order-list/filters/money-format.php' );?>
<script src="<?php echo module_url( 'nexo' ) . '/js/dashboard-report.js';?>"></script>