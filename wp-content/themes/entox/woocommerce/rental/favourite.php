<?php if( ! defined( 'ABSPATH' ) ) exit(); 
	
	global $product;
	$id = $product->get_id();

	$wishlist = do_shortcode('[yith_wcwl_add_to_wishlist]');
?>

<?php if ( '[yith_wcwl_add_to_wishlist]' != $wishlist ): ?>
	<div class="entox-product-wishlist">
		<?php echo do_shortcode('[yith_wcwl_add_to_wishlist]'); ?>
	</div>
<?php endif; ?>