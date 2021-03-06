<?php if( @$_GET[ 'exclude_header' ] != 'true' ):?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
    </head>
    <body>
        <div class="container">
<?php endif;?>
            <div class="row">
                <div class="col-md-12" style="margin-bottom:20px">
                    <div class="invoice-title">
                        <h2><?php echo store_option( 'site_name' );?> </h2>
                    </div>
                    <hr>
                    <?php ob_start();?>
                    <div class="row order-details">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <?php echo xss_clean( store_option(  'supply_receipt_col_1' ) );?>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
                            <?php echo xss_clean( store_option(  'supply_receipt_col_2' ) );?>
                        </div>
                    </div>
                    <?php if( @$_GET[ 'is-pdf' ] ):?>
                    <br>
                    <br>
                    <?php endif;?>
                    <?php
                    $string_to_parse	=	ob_get_clean();
                    echo $this->parser->parse_string( $string_to_parse, $template , true );
                    ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><strong><?php echo __( 'Facture d\'approvisionnement', 'nexo' );?></strong></h3>
                        </div>
                        <div class="panel-body no-padding">
                            <div class="table-responsive">
                                <table class="table table-condensed">
                                    <?php $this->events->do_action( 'supply_invoice_before_thead', compact( 'items', 'procurement' ) );?>
                                    <thead>
                                        <tr>
                                            <td><strong><?php echo __( 'Produit', 'nexo' );?></strong></td>
                                            <td><strong><?php echo __( 'Fournisseur', 'nexo' );?></strong></td>
                                            <td class="text-center"><strong><?php echo __( 'Prix unitaire', 'nexo' );?></strong></td>
                                            <td class="text-center"><strong><?php echo __( 'Quantite', 'nexo' );?></strong></td>
                                            <td class="text-right"><strong><?php echo __( 'Total', 'nexo' );?></strong></td>
                                        </tr>
                                    </thead>
                                    <?php $this->events->do_action( 'supply_invoice_before_tbody', compact( 'items', 'procurement' ) );?>
                                    <tbody>
                                        <?php
                                        $total          =   0;
                                        $total_unit     =   0;
                                        $total_qte      =   0;
                                        ?>
                                        <?php foreach( $items as $item ):?>
                                        <tr>
                                            <td><?php echo $item[ 'DESIGN' ];?></td>
                                            <td>
                                                <?php 
                                                switch( $item[ 'PROVIDER_TYPE' ] ) {
                                                    case 'store':
                                                        
                                                        if ( $item[ 'REF_PROVIDER' ] === '0' ) {
                                                            echo __( 'Entrepôt Principale', 'nexo' );
                                                            break;
                                                        }

                                                        $this->load->module_model( 'nexo', 'Nexo_Stores_Model' );
                                                        $store =    get_instance()->Nexo_Stores_Model->get( $item[ 'REF_PROVIDER' ] );
                                                        echo $store[ 'NAME' ] ?: __( 'Boutique Inconnue', 'nexo' );
                                                    break;
                                                    case 'suppliers':
                                                        $this->load->module_model( 'nexo', 'NexoProvidersModel' );
                                                        $store =    get_instance()->NexoProvidersModel->get( $item[ 'STOCK_FLOW_PROVIDER' ] );
                                                        echo $store[ 'NOM' ] ?: __( 'Fournisseur Inconnu', 'nexo' );
                                                    break;
                                                    default:
                                                        $this->load->module_model( 'nexo', 'NexoProvidersModel' );
                                                        $store =    get_instance()->NexoProvidersModel->get( $item[ 'REF_PROVIDER' ] );
                                                        echo $store[ 'NOM' ] ?: __( 'Fournisseur Inconnu', 'nexo' );
                                                    break;
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center"><?php echo $this->Nexo_Misc->cmoney_format( $item[ 'UNIT_PRICE' ] );?></td>
                                            <td class="text-center"><?php echo $item[ 'QUANTITE' ];?></td>
                                            <td class="text-right"><?php echo $this->Nexo_Misc->cmoney_format( $item[ 'TOTAL_PRICE' ] );?></td>
                                        </tr>
                                        <?php
                                        $total          +=   floatval( $item[ 'TOTAL_PRICE' ] );
                                        $total_unit     +=   floatval( $item[ 'UNIT_PRICE' ] );
                                        $total_qte      +=   floatval( $item[ 'QUANTITE' ] );
                                        ?>
                                        <?php endforeach;?>
                                        <tr class="active">
                                            <td colspan="2"><?php echo __( 'Total', 'nexo' );?></td>
                                            <td class="text-center"><?php echo $this->Nexo_Misc->cmoney_format( $total_unit );?></td>
                                            <td class="text-center"><?php echo $total_qte;?></td>
                                            <td class="text-right"><?php echo $this->Nexo_Misc->cmoney_format( $total );?></td>
                                        </tr>
                                    </tbody>
                                    <?php $this->events->do_action( 'supply_invoice_before_tfooter', compact( 'total', 'total_unit', 'total_qte', 'items', 'procurement' ) );?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<?php if( @$_GET[ 'exclude_header' ] != 'true' ):?>
            <a href="<?php echo dashboard_url([ 'supplies']);?>" class="btn btn-primary hidden-print"><?php echo __( 'Revenir en arrière', 'nexo' );?></a>
        </div>
    </body>
    <style>
    <?php include( MODULESPATH . 'nexo/inc/bootstrap3-style.php' );?>
    </style>
</html>
<?php endif;?>