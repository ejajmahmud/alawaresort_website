<?php if( ! defined( 'ABSPATH' ) ) exit();

global $product;

if( $product->get_type() !== 'ovabrw_car_rental' ) return;

$id = $product->get_id();

$custom_taxonomy = ovabrw_get_taxonomy_choosed_product( $id );

?>

<?php if ( $custom_taxonomy && is_array( $custom_taxonomy ) ): ?>
	<div class="entox-custom-taxonomy">
		<ul class="listing-custom-taxonomy">
			<?php foreach ( $custom_taxonomy as $key => $items ): ?>
				<li class="item-taxonomy" data-slug="<?php echo 'tax_'. esc_attr( $key ); ?>">
					<span class="name">
						<?php echo esc_html( $items['name'] ). esc_html__(':', 'entox'); ?>
					</span>
					<span class="value">
						<?php echo implode(", ", $items['value'] ); ?>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>