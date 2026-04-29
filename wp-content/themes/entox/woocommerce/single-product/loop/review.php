<?php

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! comments_open() ) {
	return;
}

$product_tab = [
	'title' 	=> esc_html__('Reviews', 'entox' ),
	'priority' 	=> 30,
	'callback' 	=> 'comments_template'
];

?>

<div class="entox-product-review">
	<?php call_user_func( 'comments_template', 'reviews', $product_tab ); ?>
</div>

