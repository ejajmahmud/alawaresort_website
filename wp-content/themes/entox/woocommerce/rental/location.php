<?php if( ! defined( 'ABSPATH' ) ) exit();

global $product;

$id = $product->get_id();

$location = get_post_meta( $id, 'ovabrw_address', true );

if ( ! empty( $location ) ): ?>
	<div class="entox-product-location">
		<i class="fas fa-map-marker-alt"></i>
		<span class="location"><?php echo esc_html( $location ); ?></span>
	</div>
<?php endif;