<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;
$id = $product->get_id();

// Get address product
$address = get_post_meta( $id, 'ovabrw_address', true );

$html = '';
$review_count   = $product->get_review_count();
$rating         = $product->get_average_rating();

?>

<div class="top-left">
    <?php the_title( '<h1 class="product-title">', '</h1>' ); ?>
    <?php if ( $address ): ?>
        <div class="address">
            <i class="ovaicon-maps-and-flags"></i>
            <span class="address-name"><?php echo esc_html( $address ); ?></span>
            <a href="#ova-map" class=""><?php esc_html_e( 'See map', 'entox' ); ?></a>
            <?php if ( $rating > 0 ): ?>
                <span class="product-review">
                    <div class="entox-product-review">
                        <div class="star-rating" role="img" aria-label="<?php echo sprintf( __( 'Rated %s out of 5', 'entox' ), $rating ); ?>">
                            <span class="rating-percent" style="width: <?php echo esc_attr( ( $rating / 5 ) * 100 ).'%'; ?>;"></span>
                        </div>
                    </div>
                    <?php if ( $review_count > 0 ): ?>
                        <span class="count">(<?php echo esc_html( $review_count ); ?>)</span>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
