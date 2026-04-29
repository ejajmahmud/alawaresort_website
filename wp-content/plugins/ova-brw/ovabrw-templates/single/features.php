<?php 
/**
 * The template for displaying features content within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/features.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

// Get product_id from do_action - use when insert shortcode
if( isset( $args['id'] ) && $args['id'] ){
	$pid = $args['id'];
}else{
	$pid = get_the_id();
}

// Check product type: rental
$product = wc_get_product( $pid );
if ( $product->get_type() !== 'ovabrw_car_rental' ) return;



$ovabrw_features_desc = get_post_meta( $pid, 'ovabrw_features_desc', true );
$ovabrw_features_label = get_post_meta( $pid, 'ovabrw_features_label', true );



if( !empty( $ovabrw_features_desc )){ ?>
<ul class="ovabrw_woo_features">
	<?php foreach ($ovabrw_features_desc as $key => $value) { ?>

		<li>
			<?php if( isset( $ovabrw_features_label[$key] ) && !empty( $ovabrw_features_label[$key] ) ){ ?>
				<label><?php echo esc_html( $ovabrw_features_label[$key] ); ?>: </label>
			<?php } ?>

			<?php if( !empty( $ovabrw_features_desc[$key] ) ){ ?>
				<span><?php echo esc_html( $value ); ?></span>
			<?php } ?>
		</li>

	<?php } ?>
</ul>
<?php } ?>
