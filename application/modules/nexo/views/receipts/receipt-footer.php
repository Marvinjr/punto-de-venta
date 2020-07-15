<?php if( @$_GET[ 'autoprint' ] == 'true' ):?>
<script>
document.onreadystatechange = () => {
  if (document.readyState === 'complete') {
    setTimeout( () => {
        // document ready
      window.print();
      window.close();
    }, <?php echo $this->config->item( 'nexo_print_timeout' ) ?: 500 ;?> );
  }
};
</script>
<?php endif;?>
<?php $this->events->do_action( 'nexopos_receipt_footer' );?>