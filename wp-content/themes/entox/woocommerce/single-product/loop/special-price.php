<?php if( ! defined( 'ABSPATH' ) ) exit();
global $product;

if( $product->get_type() !== 'ovabrw_car_rental' ) return;

$id = $product->get_id();

$price_type = get_post_meta( $id, 'ovabrw_price_type', true );
?>

<div class="special-price">
	<div class="special-price-wrapper">
		<?php if ( $price_type == 'period_time' ): ?>
			<h2 class="special-price-title">
				<?php esc_html_e( 'Price & Discount', 'entox' ); ?>
			</h2>
			<div class="special-price-content">
				<?php do_action( 'entox_wc_template_single_period' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $price_type == 'hour' || $price_type == 'mixed' ): ?>
			<h2 class="special-price-title">
				<?php esc_html_e( 'Price - Hour', 'entox' ); ?>
			</h2>
			<div class="special-price-content">
				<?php do_action( 'entox_wc_template_single_hour' ); ?>
			</div>
		<?php endif; ?>
		
		<?php if ( $price_type == 'day' || $price_type == 'mixed' ): ?>
			<h2 class="special-price-title">
				<?php esc_html_e( 'Price - Day', 'entox' ); ?>
			</h2>
			<div class="special-price-content">
				<?php do_action( 'entox_wc_template_single_day'); ?>
			</div>
		<?php endif; ?>
		<div class="close-popup">x</div>
	</div>
</div>