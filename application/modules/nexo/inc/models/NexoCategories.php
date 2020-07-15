<?php
class NexoCategories extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * return a list of available categories
     * @return array
     */
    public function get()
    {
        return $this->db
            ->get( store_prefix() . 'nexo_categories' )
            ->result_array();
    }

    /**
     * get Asc ordered categories
     * @return array
     */
    public function getAscOrdered()
    {
        return $this->db
            ->order_by( 'ORDER', 'asc' )
            ->get( store_prefix() . 'nexo_categories' )
            ->result_array();
    }

    /**
     * it's not perfect, it should also 
     * include product with status 1
     * @return array
     */
    public function getProductsCategoriesAscOrdered()
    {
        return $this->db
            ->select( 
                store_prefix() . 'nexo_categories.NOM as NOM,' .
                store_prefix() . 'nexo_categories.ORDER as ORDER,' .
                store_prefix() . 'nexo_categories.ENABLED as ENABLED,' .
                store_prefix() . 'nexo_categories.THUMB as THUMB,' .
                store_prefix() . 'nexo_categories.ID as ID,' .
                store_prefix() . 'nexo_categories.PARENT_REF_ID as PARENT_REF_ID'
            )
            ->from( store_prefix() . 'nexo_categories' )
            ->join( store_prefix() . 'nexo_articles', store_prefix() . 'nexo_articles.REF_CATEGORIE = ' . store_prefix() . 'nexo_categories.ID' )
            ->group_by( store_prefix() . 'nexo_categories.ID' )
            // ->where( store_prefix() . 'nexo_articles.QUANTITE_RESTANTE > 0' )
            // ->where( store_prefix() . 'nexo_articles.STATUS > 1' )
            ->order_by( 'ORDER', 'asc' )
            ->get()
            ->result_array();
    }

    /**
     * get single category
     * @param int cateogry id
     * @return array | false
     */
    public function getSingle( $id ) 
    {
        $category   =   $this->db->where( 'ID', $id )
            ->get( store_prefix() . 'nexo_categories' )
            ->result_array();
        
        return ! empty( $category ) ? $category[0] : false;
    }

    /**
     * save categories reorde
     * @param array categories
     * @return void
     */
    public function saveReorder( $categories )
    {
        foreach( $categories as $category ) {
            $data   =   [
                'ORDER'     =>  $category[ 'ORDER' ]
            ];

            /**
             * if the enabled parameter is 
             * provided
             */
            if( $category[ 'ENABLED' ] ) {
                $data[ 'ENABLED' ]   =   $category[ 'ENABLED' ];
            }

            $this->db->where( 'ID', $category[ 'ID' ])
                ->update( store_prefix() . 'nexo_categories', $data );
        }
    }
}