<?php

if(!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * WCFM - Custom Menus Query Var
 */
function ovabrw_wcfmcsm_query_vars( $query_vars ) {
	$wcfm_modified_endpoints = (array) get_option( 'wcfm_endpoints' );
	
	$query_custom_menus_vars = array(
		'wcfm-manage-order' => ! empty( $wcfm_modified_endpoints['wcfm-manage-order'] ) ? $wcfm_modified_endpoints['wcfm-manage-order'] : 'manage-order',
	);
	
	$query_vars = array_merge( $query_vars, $query_custom_menus_vars );
	
	return $query_vars;
}
add_filter( 'wcfm_query_vars', 'ovabrw_wcfmcsm_query_vars', 50 );

/**
 * WCFM - Custom Menus End Point Title
 */
function ovabrw_wcfmcsm_endpoint_title( $title, $endpoint ) {
	global $wp;
	switch ( $endpoint ) {
		
		case 'wcfm-manage-order' :
			$title = esc_html__( 'Manage Booking', 'ova-brw-wcfm' );
		break;
		
	}
	
	return $title;
}
add_filter( 'wcfm_endpoint_title', 'ovabrw_wcfmcsm_endpoint_title', 50, 2 );

/**
 * WCFM - Custom Menus Endpoint Intialize
 */
function ovabrw_wcfmcsm_init() {
	global $WCFM_Query;

	// Intialize WCFM End points
	$WCFM_Query->init_query_vars();
	$WCFM_Query->add_endpoints();
	
	if( !get_option( 'wcfm_updated_end_point_cms' ) ) {
		// Flush rules after endpoint update
		flush_rewrite_rules();
		update_option( 'wcfm_updated_end_point_cms', 1 );
	}
}
add_action( 'init', 'ovabrw_wcfmcsm_init', 50 );

/**
 * WCFM - Custom Menus Endpoiint Edit
 */
function ovabrw_wcfm_custom_menus_endpoints_slug( $endpoints ) {
	
	$custom_menus_endpoints = array( 'wcfm-manage-order'      => 'manage-order',);
	
	$endpoints = array_merge( $endpoints, $custom_menus_endpoints );
	
	return $endpoints;
}
add_filter( 'wcfm_endpoints_slug', 'ovabrw_wcfm_custom_menus_endpoints_slug' );

if(!function_exists('get_wcfm_custom_menus_url')) {
	function get_wcfm_custom_menus_url( $endpoint ) {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$wcfm_custom_menus_url = wcfm_get_endpoint_url( $endpoint, '', $wcfm_page );
		return $wcfm_custom_menus_url;
	}
}

/**
 * WCFM - Custom Menus
 */
function ovabrw_wcfmcsm_wcfm_menus( $menus ) {
	global $WCFM;
	
	$custom_menus = array( 
		'wcfm-manage-order' => array(   
			'label'  	=> esc_html__( 'Manage Booking', 'wcfm-custom-menus'),
			'url'       => get_wcfm_custom_menus_url( 'wcfm-manage-order' ),
			'icon'      => 'cubes',
			'priority'  => 5.2
		)
	);
	
	$menus = array_merge( $menus, $custom_menus );
		
	return $menus;
}
add_filter( 'wcfm_menus', 'ovabrw_wcfmcsm_wcfm_menus', 20 );

/**
 *  WCFM - Custom Menus Views
 */
function ovabrw_wcfm_csm_load_views( $end_point ) {
	global $WCFM, $WCFMu;
	
	
	switch( $end_point ) {
		
		case 'wcfm-manage-order':
			 
			require_once( OVABRW_WCFM_PLUGIN_PATH . 'views/wcfm-views-manage-order.php' );
			
			
		break;
		
	}
}
add_action( 'wcfm_load_views', 'ovabrw_wcfm_csm_load_views', 50 );
add_action( 'before_wcfm_load_views', 'ovabrw_wcfm_csm_load_views', 50 );
