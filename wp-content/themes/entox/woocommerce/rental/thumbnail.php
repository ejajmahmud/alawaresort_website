<?php if( ! defined( 'ABSPATH' ) ) exit();

global $product;

$link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );

$param = array();

$start_date = isset( $args['start_date'] ) 	? $args['start_date'] 	: '';
$end_date 	= isset( $args['end_date'] ) 	? $args['end_date'] 	: '';

if ( $start_date ) {
	$param['pickup_date'] = $start_date;
}

if ( $end_date ) {
	$param['dropoff_date'] = $end_date;
}

if ( $param ) {
	$link = add_query_arg( $param, $link );
}
	
?>

<a href="<?php echo esc_url( $link ); ?>" class="entox-product-thumbnail">
	<?php echo woocommerce_get_product_thumbnail('entox_thumb_product'); ?>
</a>