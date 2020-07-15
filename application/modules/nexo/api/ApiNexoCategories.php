<?php
class ApiNexoCategories extends Tendoo_Api
{
    /**
     * get all categories
     * @return void
     */
    public function categories( $id = 0 ) 
    {
        $this->load->module_model( 'nexo', 'NexoCategories', 'cat_model' );

        if ( ! empty( $id ) ) {
            return $this->response( $this->cat_model->getSingle( $id ) );
        }
        
        return $this->response( $this->cat_model->get() );
    }

    /**
     * save a category reorder
     * @return json
     */
    public function postReorder()
    {
        $this->load->module_model( 'nexo', 'NexoCategories', 'cat_model' );
        $this->cat_model->saveReorder( $this->post( 'categories' ) );
        return $this->response([ 
            'status'    =>  'success',
            'message'   =>  __( 'L\'organisation des catégories a été enregistrée', 'nexo' )
        ]);
    }

    /**
     * get category products
     * @param int category id
     * @return json
     */
    public function categoryProducts( $catid ) 
    {
        $this->load->module_model( 'nexo', 'NexoProducts', 'product_model' );
        
        return $this->response( 
            $this->product_model->getCategoriesProducts( $catid ) 
        );
    }

    public function getCategoriesItems( $category )
    {
        $this->load->module_model( 'nexo', 'NexoCategories', 'cat_model' );
        $this->load->module_model( 'nexo', 'NexoItems', 'item_model' );
        
        $category   =   $this->cat_model->getSingle( $category );

        if ( ! empty( $category ) ) {
            return $this->response(
                $this->item_model->getHavingCategory( $category[ 'ID' ] )
            );
        }

        return $this->response([
            'status'    =>  'failed',
            'message'   =>  __( 'Impossible de retrouver la catégorie', 'nexo' ),
        ], 403 );
    }
}