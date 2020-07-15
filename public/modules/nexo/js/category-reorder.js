jQuery( document ).ready( function() {
    new Vue({
        el: '#category-reorder',
        data: {
            ...Object.assign({}, categoryReorderData ),
            grid: null,
            productGrid: null,
            itemClass: 'item',
            productLoaded: false,
            products: []
        },
        mounted() {
            this.mountMuuri();
        },
        methods: {
            saveOrder() {
                swal({
                    title: this.textDomain.confirmSorting,
                    text: this.productLoaded ? this.textDomain.sortingWillBesavedForProducts : this.textDomain.sortingWillBeSaved,
                    showCancelButton: true
                }).then( action => {
                    if ( action.value ) {
                        if ( this.productLoaded ) {
                            HttpRequest.post( this.url.saveProductOrder, { products : this.products }).then( result => {
                                NexoAPI.Toast()( result.data.message );
                            }).catch( error => {
                                NexoAPI.Toast()( this.textDomain.anErrorOccured );
                            })
                        } else {
                            HttpRequest.post( this.url.saveCategoryOrder, { categories : this.categories }).then( result => {
                                NexoAPI.Toast()( result.data.message );
                            }).catch( error => {
                                NexoAPI.Toast()( this.textDomain.anErrorOccured );
                            })
                        }
                    }
                })
            },
            mountMuuri() {
                this.grid = new Muuri('.category-grid-container', {
                    dragEnabled: true
                });
    
                this.grid.on( 'dragEnd', ( item, event ) => {
                    this.grid.getItems().forEach( ( item, index ) => {
                        const category  =   this.categories.filter( _item => parseInt( _item.ID ) === parseInt( $( item._element ).attr( 'data-id' ) ) );
                        if ( category.length > 0 ) {
                            category[0].ORDER   =   parseInt( index );
                        }
                    });
                });
            },
            setAsDefault( category ) {
                this.categories.forEach( cat => cat.ENABLED = 'false' );
                category.ENABLED  =   'true';
            },
            closeCategory() {
                this.productLoaded  =   false;
                this.products       =   [];
                this.productGrid.destroy();
                setTimeout( () => {
                    this.mountMuuri();
                }, 500 );
            },
            loadProducts( category ) {
                HttpRequest.get( this.url.categoryProducts.replace( '#', category.ID ) ).then( result => {
                    this.grid.destroy();
                    this.category       =   category;
                    this.products       =   result.data;
                    this.productLoaded  =   true;
                    setTimeout( () => {
                        this.productGrid    =   new Muuri( '.products-grid-container', {
                            dragEnabled: true
                        });
                        this.productGrid.on( 'dragEnd', ( item, event ) => {
                            this.productGrid.getItems().forEach( ( item, index ) => {                                
                                const product  =   this.products.filter( _item => parseInt( _item.ID ) === parseInt( $( item._element ).attr( 'data-id' ) ) );
                                if ( product.length > 0 ) {
                                    product[0].ORDER   =   parseInt( index );
                                }
                            });
                        });
                    }, 500 );
                })
            }
        }
    })
})