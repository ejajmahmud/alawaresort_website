<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;

$id = $product->get_id();

$policies = get_post_meta( $id, 'wcfm_policy_product_options', true );

$shipping_policy 		= isset( $policies['shipping_policy'] ) ? $policies['shipping_policy'] : '';
$refund_policy 			= isset( $policies['refund_policy'] ) ? $policies['refund_policy'] : '';
$cancellation_policy 	= isset( $policies['cancellation_policy'] ) ? $policies['cancellation_policy'] : '';

if ( ! $policies ) {
	return;
}

if ( ! trim( strip_tags($shipping_policy) ) && ! trim( strip_tags($refund_policy) ) && ! trim( strip_tags($cancellation_policy) ) ) {
	return;
}

?>
<div class="entox-product-policies">
	<h2 class="title-policies">
		<?php esc_html_e( 'Store Policies', 'entox' ); ?>
	</h2>
	<?php if ( trim( strip_tags( $shipping_policy ) ) ): ?>
		<div class="shipping-policy product-policies">
			<h3 class="label-policy"><?php esc_html_e( 'Shipping', 'entox' ); ?></h3>
			<p class="description-policy"><?php echo trim( strip_tags($shipping_policy) ); ?></p>
		</div>
	<?php endif; ?>
	<?php if ( trim( strip_tags( $refund_policy ) ) ): ?>
		<div class="refund-policy product-policies">
			<h3 class="label-policy"><?php esc_html_e( 'Refund', 'entox' ); ?></h3>
			<p class="description-policy"><?php echo trim( strip_tags($refund_policy) ); ?></p>
		</div>
	<?php endif; ?>
	<?php if ( trim( strip_tags( $cancellation_policy ) ) ): ?>
		<div class="cancellation-policy product-policies">
			<h3 class="label-policy"><?php esc_html_e( 'Cancellation', 'entox' ); ?></h3>
			<p class="description-policy"><?php echo trim( strip_tags($cancellation_policy) ); ?></p>
		</div>
	<?php endif; ?>
</div>
<div class="entox-line"></div>