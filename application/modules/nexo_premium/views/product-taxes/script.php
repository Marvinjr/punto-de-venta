
<?php $this->events->do_action( 'nexopos_before_product_taxes_js' );?>
<script>
const taxReportData     =   NexoAPI.events.applyFilters( 'nexopos_filter_product_taxes_data', {
    url: {
        get: '<?php echo site_url([ 'api', 'nexopos', 'reports', 'product-taxes', store_get_param( '?' ) ]);?>'
    },
    textDomain: {
        cantProceed: `<?php echo __( 'Impossible de continuer', 'nexo_premium' );?>`,
        wrongDates: `<?php echo __( 'Les dates utilisées ne sont pas valides.', 'nexo_premium' );?>`,
        errorOccured: `<?php echo __( 'Une erreur s\'est produite', 'nexo_premium' );?>`,
        unexpectedError: `<?php echo __( 'Une erreur inattendue s\'est produite.', 'nexo_premium' );?>`,
        wrongTimeRange: `<?php echo __( 'L\'intervalle entre les deux dates n\'est pas valide.', 'nexo_premium' );?>`
    },
    allTaxes: <?php echo json_encode( $taxes );?>
});
const hookedMethods     =   NexoAPI.events.applyFilters( 'nexopos_hook_methods_product_taxes', {});
const hookedComputed    =   NexoAPI.events.applyFilters( 'nexopos_hook_computed_product_taxes', {});
const hookedWatch       =   NexoAPI.events.applyFilters( 'nexopos_hook_watches_product_taxes', {});
Vue.filter( 'moneyFormat', function( value ) {
    return NexoAPI.DisplayMoney( value );
});

var datepickerComponent = Vue.component( 'date-picker', {
    //v-el:select
    template: `
    <div class="input-group date" v-el:inputgroup>
        <span class="input-group-addon">{{ label }}</span>
        <input type="text" class="form-control" v-model="value">
        <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
    </div>
    `,
    props: [ 'value', 'dateFormat', 'label' ],
    data: function() {
        return {};
    },
    mounted() {
        this.label      =   this.label || 'Date';
        $(this.$el).datetimepicker({
            format: this.dateFormat || 'YYYY-MM-DD'
        });
        $( this.$el ).on( 'dp.change', ( e ) => {
            this.startDate  =   $( e.currentTarget ).find( 'input' ).val();
            this.$emit( 'changed', this.startDate );
        })
    },
    beforeDestroy: function() {
        $(this.$el).datepicker('hide').datepicker('destroy');
    }
});
</script>
<script src="<?php echo module_url( 'nexo' ) . 'js/product-taxes.js';?>"></script>