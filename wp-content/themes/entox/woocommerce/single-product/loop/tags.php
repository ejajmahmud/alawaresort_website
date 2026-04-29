<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;

$id = $product->get_id();

if ( $product->get_type() !== 'ovabrw_car_rental' ) return;

$tags = get_the_terms( $id, 'product_tag' );

if ( ! $tags ) return;

?>

<div class="entox-product-tags">
	<h2 class="title-tags">
		<?php esc_html_e( 'Tags', 'entox' ); ?>
	</h2>
	<ul class="tags">
		<?php foreach( $tags as $item_tags ): 
			$term_link = get_term_link( $item_tags->term_id );
		?>
			<li class="item-tags">
				<a href="<?php echo esc_url( $term_link ); ?>">
					<?php echo esc_html( $item_tags->name ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>