<?php
class Number 
{
    public static function currency( $amount )
    {
        get_instance()->load->model( 'Nexo_Misc' );
        return get_instance()->Nexo_Misc->cmoney_format( $amount );
    }
}