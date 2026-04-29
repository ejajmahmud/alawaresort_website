<?php

defined( 'ABSPATH' ) || exit;

global $product;
$post_id = get_the_id();

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}

?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>
	<div class="entox-single-product">
		<div class="entox-before-single-product">
			<?php
			/**
			 * Hook: entox_wc_before_single_product.
			 *
			 * @hooked entox_wc_template_single_title - 5
			 * @hooked entox_wc_template_single_price - 5
			 */
			do_action( 'entox_wc_before_single_product' );
			?>
		</div>

		<div class="entox-summary">
			<div class="entox-summary-left">
				<?php
				/**
				 * Hook: entox_wc_single_product_summary_left.
				 *
				 * @hooked entox_wc_template_single_image - 10
				 * @hooked entox_wc_template_product_description - 10
				 * @hooked entox_wc_template_product_features - 15
				 * @hooked entox_wc_template_product_policies - 15
				 * @hooked entox_wc_template_product_location - 15
				 * @hooked entox_wc_template_product_calendar - 20
				 * @hooked entox_wc_template_product_tags - 20
				 * @hooked entox_wc_template_product_review - 20
				 */
				do_action( 'entox_wc_single_product_summary_left' );
				?>
			</div>
			<div class="entox-summary-right">
				<?php
				/**
				 * Hook: entox_wc_single_product_summary_right.
				 *
				 * @hooked entox_wc_template_product_taxonomy - 10
				 * @hooked entox_wc_template_product_forms_tab - 10
				 */
				do_action( 'entox_wc_single_product_summary_right' );
				?>
			</div>
			
		</div>

		<?php
		/**
		 * Hook: entox_wc_after_single_product.
		 *
		 * @hooked entox_wc_template_product_related - 20
		 */
		do_action( 'entox_wc_after_single_product' );
		?>
	</div>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
