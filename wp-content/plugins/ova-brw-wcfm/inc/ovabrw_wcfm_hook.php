<?php

// Add Rental Car to WCFM
add_filter( 'wcfm_product_types', function( $pro_types ) {
    $pro_types['ovabrw_car_rental'] = esc_html__( 'Rental', 'ova-brw-wcfm' );
    
    return apply_filters( 'ovabrw_wcfm_product_types', $pro_types );
}, 50 );


// Show Custom Field in WCFM when add new product
add_action( 'after_wcfm_products_manage_pricing_fields', 'ovabrw_after_wcfm_products_manage_pricing_fields', 20, 1 );
function ovabrw_after_wcfm_products_manage_pricing_fields( $product_id ){
	if( defined('OVABRW_PLUGIN_PATH') ){
		include ( OVABRW_PLUGIN_PATH.'/admin/metabox/ovabrw-custom-fields.php' );	
	}
}

// Hide Field when Edit Product
add_filter( 'ovabrw_show_backend_deposit', 'ovabrw_hide_field_setting_product_wfcm', 10, 0 );
add_filter( 'ovabrw_show_backend_custom_order', 'ovabrw_hide_field_setting_product_wfcm', 10, 0 );
add_filter( 'ovabrw_show_quantity_setting_product', 'ovabrw_hide_field_setting_product_wfcm', 10, 0 );
add_filter( 'ovabrw_show_pickup_date_setting_product', 'ovabrw_hide_field_setting_product_wfcm', 10, 0 );
add_filter( 'ovabrw_show_dropoff_date_setting_product', 'ovabrw_hide_field_setting_product_wfcm', 10, 0 );
add_filter( 'ovabrw_show_extra_tab_setting_product', 'ovabrw_hide_field_setting_product_wfcm', 10, 0 );
add_filter( 'ovabrw_show_pickup_loc_setting_product', 'ovabrw_hide_field_setting_product_wfcm', 10, 0 );
add_filter( 'ovabrw_show_dropoff_loc_setting_product', 'ovabrw_hide_field_setting_product_wfcm', 10, 0 );
add_filter( 'ovabrw_show_checkout_field_setting_product', 'ovabrw_hide_field_setting_product_wfcm', 10, 0 );
add_filter( 'ovabrw_manage_store_show_manual_setting_product', 'ovabrw_hide_field_setting_product_wfcm', 10, 0 );
add_filter( 'ovabrw_show_icon_features', 'ovabrw_hide_field_setting_product_wfcm' );

function ovabrw_hide_field_setting_product_wfcm(){

	global $wp;

	if( isset( $wp->query_vars['wcfm-products-manage'] ) ) { // Add new product

		return false;
		
	}else{

		return true;

	}

}


// Return real path template in Plugin or Theme
if( !function_exists( 'ovabrw_wcfm_locate_template' ) ){
	function ovabrw_wcfm_locate_template( $template_name, $template_path = '', $default_path = '' ) {
		
		// Set variable to search in ovabrw-templates folder of theme.
		if ( ! $template_path ) :
			$template_path = 'ovabrw-wcfm-templates/';
		endif;

		// Set default plugin templates path.
		if ( ! $default_path ) :
			$default_path = OVABRW_WCFM_PLUGIN_PATH . 'ovabrw-wcfm-templates/'; // Path to the template folder
		endif;

		// Search template file in theme folder.
		$template = locate_template( array(
			$template_path . $template_name
			
		) );

		// Get plugins template file.
		if ( ! $template ) :
			$template = $default_path . $template_name;
		endif;

		return apply_filters( 'ovabrw_wcfm_locate_template', $template, $template_name, $template_path, $default_path );
	}

}

// Include Template File
function ovabrw_wcfm_get_template( $template_name, $args = array(), $tempate_path = '', $default_path = '' ) {
	if ( is_array( $args ) && isset( $args ) ) :
		extract( $args );
	endif;
	$template_file = ovabrw_wcfm_locate_template( $template_name, $tempate_path, $default_path );
	if ( ! file_exists( $template_file ) ) :
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' );
		return;
	endif;

	
	include $template_file;
}


