<script>
    NexoAPI.events.addFilter( 'gastro_send_to_kitchen', ( defaultValue, scope ) => {
        if( ( typeof v2Checkout.CartDeliveryInfo === 'undefined' || Object.values( v2Checkout.CartDeliveryInfo ).length === 0 ) && v2Checkout.CartSelectedOrderType.namespace === 'delivery' ) {
            NexoAPI.Toast()( 'Fill delivery informations !!!' );
            return false;
        }
        return defaultValue;
    });

    NexoAPI.events.addFilter( 'openPayBox', ( status ) => {
        if ( v2Checkout.CartSelectedOrderType !== undefined && v2Checkout.CartSelectedOrderType.namespace === 'delivery' ) {
            if( ( typeof v2Checkout.CartDeliveryInfo === 'undefined' || Object.values( v2Checkout.CartDeliveryInfo ).length === 0 ) ) {
                NexoAPI.Toast()( 'Fill delivery informations !!!' );
                return false;
            }
        }
        return status;
    });

    NexoAPI.events.addFilter( 'gastro_open_payment_box', ( action, scope ) => {
        if ( scope.selectedOrderType.namespace === 'delivery' ) {
            NexoAPI.Toast()( 'Fill delivery informations !!!' );
            return false;
        }
        return action;
    });
</script>