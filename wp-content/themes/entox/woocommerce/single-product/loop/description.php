<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $post;

$description = apply_filters('the_content', $post->post_content);

if ( ! trim( strip_tags( $description ) ) ) {
	return;
}

?>

<div class="entox-product-description">
	<h2 class="title-description">
		<?php esc_html_e( 'Description', 'entox' ); ?>
	</h2>
	<?php printf( $description ); // WPCS: XSS ok. ?>
</div>
<div class="entox-line"></div>
