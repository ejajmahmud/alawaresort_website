<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $related_products ) : ?>
	<div class="entox-related-products">
		<div class="ova-heading">
			<h4 class="sub_title second_font"><?php esc_html_e( 'Browse Hot Offers', 'entox' ); ?></h4>
			<h3 class="title second_font"><?php esc_html_e( 'Similar Listings', 'entox' ); ?></h3>
		</div>
		<?php woocommerce_product_loop_start(); ?>
			<?php foreach ( $related_products as $related_product ) : ?>
				<?php
					$post_object = get_post( $related_product->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object );

					wc_get_template_part( 'content', 'product' );
				?>
			<?php endforeach; ?>
		<?php woocommerce_product_loop_end(); ?>
	</div>
<?php
endif;

wp_reset_postdata();
