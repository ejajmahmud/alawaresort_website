<?php
	if(defined('ENTOX_URL') 	== false) 	define('ENTOX_URL', get_template_directory());
	if(defined('ENTOX_URI') 	== false) 	define('ENTOX_URI', get_template_directory_uri());

	load_theme_textdomain( 'entox', ENTOX_URL . '/languages' );

	// Main Feature
	require_once( ENTOX_URL.'/inc/class-main.php' );

	// Functions
	require_once( ENTOX_URL.'/inc/functions.php' );

	// Hooks
	require_once( ENTOX_URL.'/inc/class-hook.php' );

	// Widget
	require_once (ENTOX_URL.'/inc/class-widgets.php');
	

	// Elementor
	if (defined('ELEMENTOR_VERSION')) {
		require_once (ENTOX_URL.'/inc/class-elementor.php');
	}
	
	// WooCommerce
	if (class_exists('WooCommerce')) {
		require_once (ENTOX_URL.'/inc/class-woo.php');	
	}
	
	
	/* Customize */
	if( current_user_can('customize') ){
	    require_once ENTOX_URL.'/customize/custom-control/google-font.php';
	    require_once ENTOX_URL.'/customize/custom-control/heading.php';
	    require_once ENTOX_URL.'/inc/class-customize.php';
	}
    
   
	require_once ( ENTOX_URL.'/install-resource/active-plugins.php' );
	
	/* Customize WCFM plugin */
	require_once ( ENTOX_URL.'/inc/class-customize-wcfm.php' );

	/* Template Hooks */
	require_once ( ENTOX_URL.'/inc/class-woo-template-hooks.php' );

	/* Template Fuctions */
	require_once ( ENTOX_URL.'/inc/class-woo-template-functions.php' );
	