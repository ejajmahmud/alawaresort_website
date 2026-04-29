<?php if( ! defined( 'ABSPATH' ) ) exit();

global $product;

$id = $product->get_id();

if ( ! wc_review_ratings_enabled() ) {
	return;
}

$review_count 	= $product->get_review_count();
$rating      	= $product->get_average_rating();
$html 		  	= '';

if ( $rating > 0 ) {
	?>
	<div class="entox-product-review">
		<div class="star-rating" role="img" aria-label="<?php echo sprintf( __( 'Rated %s out of 5', 'entox' ), $rating ); ?>">
			<span class="rating-percent" style="width: <?php echo esc_attr( ( $rating / 5 ) * 100 ).'%'; ?>;"></span>
			<?php if ( $review_count > 0 ): ?>
				<strong class="rating"><?php echo esc_html( $rating ); ?></strong>
				<span class="rating"><?php echo esc_html( $review_count ); ?></span>'
			<?php else: ?>
				<strong class="rating"><?php echo esc_html( $rating ); ?></strong>
			<?php endif; ?>
		</div>
		<?php if ( $review_count > 0 ): ?>
			<span class="count">(<?php echo esc_html( $review_count ); ?>)</span>
		<?php endif; ?>
	</div>
	<?php
}
