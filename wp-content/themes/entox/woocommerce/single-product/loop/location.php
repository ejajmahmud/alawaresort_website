<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;

$id = $product->get_id();

$address 	= get_post_meta( $id, 'ovabrw_address', true );
$latitude 	= get_post_meta( $id, 'ovabrw_latitude', true );
$longitude 	= get_post_meta( $id, 'ovabrw_longitude', true );

if ( ! $address ) {
	return;
}

?>
<div class="entox-product-location" id="ova-map">
	<div class="heading-location">
		<h2 class="title-location">
			<?php esc_html_e( 'Location', 'entox' ); ?>
		</h2>
		<p class="address" latitude="<?php echo esc_attr( $latitude ); ?>" longitude="<?php echo esc_attr( $longitude ); ?>">
			<?php echo esc_html( $address ); ?>
		</p>
		<input type="hidden" class="pac-input" name="pac-input" id="pac-input" value="<?php echo esc_attr($address); ?>" autocomplete="off" autocapitalize="none">
	</div>
	<div id="product-show-map" class="product-show-map"></div>
</div>