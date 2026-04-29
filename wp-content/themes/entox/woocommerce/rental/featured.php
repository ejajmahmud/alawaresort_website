<?php if( ! defined( 'ABSPATH' ) ) exit();

global $product;

$id = $product->get_id();

$is_featured = $product->is_featured();

?>

<?php if ( $is_featured ): ?>
	<div class="entox-is-featured">
		<?php esc_html_e( 'Featured', 'entox' ); ?>
	</div>
<?php endif; ?>

