<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $WCFM, $product;

$id = $product->get_id();
$vendor_id = 0;

if ( $product->get_type() !== 'ovabrw_car_rental' ) return;

if ( is_product() && $product && is_object( $product ) && function_exists( 'wcfm_get_vendor_id_by_post' ) ) {
	$vendor_id = wcfm_get_vendor_id_by_post( $id );
} else {
	return;
}

$button_class = '';
if ( !is_user_logged_in() && apply_filters( 'wcfm_is_allow_enquiry_with_login', false ) ) { 
	$button_class = ' wcfm_login_popup';
}

?>

<div class="entox-ask-question">
	<div class="wcfm_ele_wrapper wcfm_catalog_enquiry_button_wrapper">
	<div class="wcfm-clearfix"></div>
		<a href="#" 
			class="wcfm_catalog_enquiry <?php echo esc_attr( $button_class ); ?>" 
			data-store="<?php echo esc_attr( $vendor_id ); ?>" 
			data-product="<?php echo esc_attr( $id ); ?>">
			<span class="label-ask-question">
				<?php esc_html_e( 'Ask a Question', 'entox' ); ?>
			</span>
		</a>
		<?php do_action( 'wcfm_after_product_catalog_enquiry_button' ); ?>
		<div class="wcfm-clearfix"></div>
	</div>
</div>

