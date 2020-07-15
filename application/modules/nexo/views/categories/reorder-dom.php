<script src="<?php echo module_url( 'nexo' ) . 'js/web-animations.js';?>"></script>
<script src="<?php echo module_url( 'nexo' ) . 'js/hammer.min.js';?>"></script>
<script src="<?php echo module_url( 'nexo' ) . 'js/muuri.draggable.js';?>"></script>
<script>
    const categoryReorderData = {
        categories : <?php echo json_encode($categories); ?> ,
        url : {
            saveCategoryOrder: '<?php echo site_url([ 'api', 'nexopos', 'categories', 'reorder', store_get_param( '?' )]);?>',
            categoryProducts: '<?php echo site_url([ 'api', 'nexopos', 'categories', 'products', '#', store_get_param("?")]);?>',
            saveProductOrder: '<?php echo site_url([ 'api', 'nexopos', 'products', 'reorder', store_get_param( '?' )]);?>'
        },
        textDomain: {
            confirmSorting: `<?php echo __( 'Souhaitez-vous confirmer ?', 'nexo' );?>`,
            anErrorOccured: `<?php echo __( 'Une erreur s\'est produite durant le processus', 'nexo' );?>`,
            sortingWillBeSaved: `<?php echo __( 'La mise en page sera enregistrée pour les catégories sélectionnées.', 'nexo' );?>`,
            sortingWillBesavedForProducts: `<?php echo __( 'La mise en page sera enregistrée pour les produits affichés.', 'nexo' );?>`,
        }
    }
</script>
<script src="<?php echo module_url( 'nexo' ) . 'js/category-reorder.js';?>"></script>
<?php echo tendoo_info( __( 'Vous avez également la possibilité de définir qu\'elle sera la catégorie par défaut ouverte sur le point de vente.', 'nexo' ) );?>
<div id="category-reorder">
    <h4 v-if="productLoaded"><?php echo __( 'Réorganisation des produits : {{ category.NOM }}', 'nexo' );?></h4>
    <div class="products-grid-container" v-if="productLoaded">        
        <div v-for="(product, index) of products" :data-index="index" :data-id="product.ID"
            :class="itemClass" class="drag-item">
            <div class="item-content">
                {{ product.DESIGN }} &mdash; {{ product.ORDER }}
            </div>
        </div>
    </div>

    <div class="category-grid-container" v-if="productLoaded === false">        
        <div v-for="(category, index) of categories" :data-index="index" :data-id="category.ID"
            :class="[{ 'enabled' : category.ENABLED === 'true' }, itemClass ]" class="drag-item">
            <div class="item-content">
                {{ category.NOM }} &mdash; {{ category.ORDER }}
                <span class="btn-group btn-group-justified" style="position: absolute;left: 0;bottom: 0;padding: 10px;">
                    <div class="btn-group" role="group">
                        <button class="btn btn-info btn-xs" @click="setAsDefault( category )"><i
                                class="fa fa-check-square-o"></i> <?php echo __( 'Activer', 'nexo' );?></button>
                    </div>
                    <div class="btn-group" role="group">
                        <button class="btn btn-default btn-xs" @click="loadProducts( category )"><i
                                class="fa fa-ticket"></i> <?php echo __( 'Produits', 'nexo' );?></button>
                    </div>
                </span>
            </div>
        </div>
    </div>
    <div class="category-grid-footer">
        <button v-if="productLoaded" @click="closeCategory()"
            class="btn btn-default"><?php echo __( 'Returns', 'nexo' );?></button>
        <button @click="saveOrder()"
            class="btn btn-primary"><?php echo __( 'Enregistrer la mise en page', 'nexo' );?></button>
    </div>
</div>
<style>
    .category-grid-container, .products-grid-container {
        position: relative;
    }

    .item {
        display: block;
        position: absolute;
        width: 150px;
        height: 150px;
        margin-right: 5px;
        margin-bottom: 5px;
        z-index: 1;
        background: #dedede;
        color: #424242;
        border: solid 1px #d2d2d2;
    }

    .item.enabled {
        background: #dbdeff;
        border: solid 1px #c1c0ff;
    }

    .item.muuri-item-dragging {
        z-index: 3;
    }

    .item.muuri-item-releasing {
        z-index: 2;
    }

    .item.muuri-item-hidden {
        z-index: 0;
    }

    .item-content {
        position: relative;
        width: 100%;
        height: 100%;
        padding: 10px;
    }
</style>