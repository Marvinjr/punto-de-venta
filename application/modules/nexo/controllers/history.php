<?php
class NexoLogHistoryController extends CI_Model
{
    /**
     * Show the history of the current 
     * store page.
     */
    public function logHistory()
    {
        $this->load->module_model( 'nexo', 'NexoLogModel' );
        $this->Gui->set_title( store_title( __( 'Historique & Activité', 'nexo' ) ) );
        $this->load->module_view( 'nexo', 'history.gui' );
    }
}