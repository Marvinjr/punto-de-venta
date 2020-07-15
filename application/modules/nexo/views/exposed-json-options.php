<script>
/**
 * Exposed Tendoo options
 * @since 3.12.16
 */
const tendooOptions     =   <?php echo json_encode( get_option() );?>;

/**
 * Exposed Store Options
 * @since 3.14.30
 */
const storeDetails      =   <?php echo json_encode([
    'multistore_enabled'    =>  multistore_enabled(),
    'store_id'              =>  get_store_id(),
    'store_slug'            =>  store_slug(),
    'store_prefix'          =>  store_prefix(),
    'is_multistore'         =>  is_multistore()
]);?>
</script>