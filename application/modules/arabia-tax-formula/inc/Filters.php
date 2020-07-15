<?php
namespace ArabicTaxFormula\Inc;

use Tendoo_Module;

class Filters extends Tendoo_Module 
{
    public function __construct()
    {
        parent::__construct();
    }

    public function save_product( $param )
    {
        $param[ 'PRIX_DE_VENTE_TTC' ]       =   $param[ 'PRIX_DE_VENTE' ];
        $param[ 'PRIX_DE_VENTE_BRUT' ]      =   $param[ 'PRIX_DE_VENTE' ];


        if( ! empty( $param[ 'REF_TAXE' ] ) ) {
            $tax    =   $this->db->where( 'ID', $param[ 'REF_TAXE' ] )->get( store_prefix() . 'nexo_taxes' )->result_array();

            if( $tax ) {
                /**
                 * Adding inclusive and exclusive tax calculation
                 */
                if ( $param[ 'TAX_TYPE' ] == 'inclusive' ) {
                    $percent    =   ( floatval( $tax[0][ 'RATE' ] ) * floatval( $param[ 'PRIX_DE_VENTE' ] ) ) / ( 100 + floatval( $tax[0][ 'RATE' ] ) );
                    $param[ 'PRIX_DE_VENTE_TTC' ]       =    $param[ 'PRIX_DE_VENTE' ];
                    /**
                     * the following line actually cause a bug
                     * of changing the defined selling price
                     */
                    $param[ 'PRIX_DE_VENTE_BRUT' ]           =   floatval( $param[ 'PRIX_DE_VENTE' ] ) - $percent;
                } else {
                    $percent    =   ( floatval( $tax[0][ 'RATE' ] ) * floatval( $param[ 'PRIX_DE_VENTE' ] ) ) / ( 100 + floatval( $tax[0][ 'RATE' ] ) );
                    $param[ 'PRIX_DE_VENTE_TTC' ]       =   floatval( $param[ 'PRIX_DE_VENTE' ] ) + $percent;
                    $param[ 'PRIX_DE_VENTE_BRUT' ]      =   floatval( $param[ 'PRIX_DE_VENTE' ] );
                }

            } 
        }

        return $param;
    }
}