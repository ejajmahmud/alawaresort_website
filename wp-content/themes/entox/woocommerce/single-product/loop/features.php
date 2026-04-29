<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;

$id = $product->get_id();

$features = get_post_meta( $id, 'ovabrw_features_label', true );

if ( ! $features ) {
	return;
}

?>
<div class="entox-product-features">
	<h2 class="title-features">
		<?php esc_html_e( 'Features', 'entox' ); ?>
	</h2>
	<div class="features-list">
		<?php foreach ( $features as $label ): ?>
			<div class="feature-item">
				<span class="icon">
					<i class="fas fa-check-circle"></i>
				</span>
				<span class="label">
					<?php echo esc_html( $label ); ?>
				</span>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<div class="entox-line"></div>