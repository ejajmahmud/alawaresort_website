<?php

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;

$attachment_ids = $product->get_gallery_image_ids();

?>

<?php if ( $attachment_ids  && $product->get_image_id() ): ?>
	<div class="entox-thumbnails-product owl-carousel">
		<?php foreach ( $attachment_ids as $attachment_id ): 
			$classes 	= array( '' );
			$image_link = wp_get_attachment_url( $attachment_id );

			if ( ! $image_link ) continue;

			$image_title 	= esc_attr( get_the_title( $attachment_id ) );
			$image_caption 	= esc_attr( get_post_field( 'post_excerpt', $attachment_id ) );

			$image = wp_get_attachment_image( 
				$attachment_id, 
				'thumbnail', 
				false, 
				$attr = array(
					'title'	=> esc_attr( $image_title ),
					'alt'	=> esc_attr( $image_title )
				)
			);

			$image_class = esc_attr( implode( ' ', $classes ) );
		?>
			<div class="item">
				<?php
					echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<a href="%s" class="%s" title="%s" data-gal="prettyPhoto[gal]">%s</a>', $image_link, $image_class, $image_caption, $image ), $attachment_id, get_the_id(), $image_class );
				?>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>