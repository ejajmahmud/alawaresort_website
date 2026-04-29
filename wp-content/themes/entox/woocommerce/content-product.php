<?php defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( 'entox_product', $product ); ?>>
	<?php
	/**
	 * Hook: entox_wc_before_shop_loop_item.
	 */
	do_action( 'entox_wc_before_shop_loop_item' );

	/**
	 * Hook: entox_wc_before_head_loop_item.
	 */
	do_action( 'entox_wc_before_head_loop_item' );

	/**
	 * Hook: entox_wc_head_loop_item.
	 *
	 * @hooked entox_template_head_loop_product_favourite - 10
	 * @hooked entox_template_head_loop_product_thumbnail - 10
	 */
	do_action( 'entox_wc_head_loop_item', $args );

	/**
	 * Hook: entox_wc_after_head_loop_item.
	 */
	do_action( 'entox_wc_after_head_loop_item' );

	/**
	 * Hook: entox_wc_before_foot_loop_item.
	 */
	do_action( 'entox_wc_before_foot_loop_item' );

	/**
	 * Hook: entox_wc_foot_loop_item.
	 *
	 * @hooked entox_template_foot_loop_product_title - 10
	 * @hooked entox_template_head_loop_product_price - 10
	 * @hooked entox_template_foot_loop_product_review - 10
	 * @hooked entox_template_foot_loop_product_custom_taxonomies - 10
	 * @hooked entox_template_foot_loop_product_location - 10
	 */
	do_action( 'entox_wc_foot_loop_item', $args );

	/**
	 * Hook: entox_wc_after_foot_loop_item.
	 */
	do_action( 'entox_wc_after_foot_loop_item' );

	/**
	 * Hook: entox_wc_after_shop_loop_item.
	 */
	do_action( 'entox_wc_after_shop_loop_item' );
	?>
</li>
