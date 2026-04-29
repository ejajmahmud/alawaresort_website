<?php
/*
Plugin Name: BRW - WCFM Addon Plugin
Plugin URI: https://themeforest.net/user/ovatheme/portfolio
Description: Make multi vendor with WCFM
Author: Ovatheme
Version: 12.0.3
Author URI: https://themeforest.net/user/ovatheme
Text Domain: ova-brw-wcfm
Domain Path: /languages/
*/
if ( !defined( 'ABSPATH' ) ) exit();


if( !class_exists( 'OVABRW_WCFM' ) ){

	 class OVABRW_WCFM{

		/**
		 * OVABRW_WCFM Constructor
		 */

		public function __construct(){
			
				$this->define_constants();
				

				$this->includes();
		}

		/**
		 * Define constants
		 */
		public function define_constants(){
			define( 'OVABRW_WCFM_PLUGIN_FILE', __FILE__ );
			define( 'OVABRW_WCFM_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
			define( 'OVABRW_WCFM_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

			load_plugin_textdomain( 'ova-brw-wcfm', false, basename( dirname( __FILE__ ) ) .'/languages' ); 
		}


		public function includes(){


				// Funciton
				require_once( OVABRW_WCFM_PLUGIN_PATH.'/inc/ovabrw_wcfm_hook.php' );

				// Script
				require_once( OVABRW_WCFM_PLUGIN_PATH.'/inc/ovabrw_wcfm_assets.php' );

				// Ajax
				require_once( OVABRW_WCFM_PLUGIN_PATH.'/inc/ovabrw_wcfm_ajax.php' );

				// Core
				require_once( OVABRW_WCFM_PLUGIN_PATH.'/inc/ovabrw_wcfm_core-functions.php' );
					
				// Add Manage Order
				require_once( OVABRW_WCFM_PLUGIN_PATH.'/inc/ovabrw_wcfm_manage_order.php' );

				// Add Settings
				require_once( OVABRW_WCFM_PLUGIN_PATH.'/inc/ovabrw_wcfm_settings.php' );

		}

	}
}
		
	
if(!function_exists('is_plugin_active')){
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); // Require plugin.php to use is_plugin_active() below
}



if ( is_plugin_active( 'woocommerce/woocommerce.php' ) && is_plugin_active( 'ova-brw/ova-brw.php' )  ) {

	add_action ('plugins_loaded',  'ovabrw_wcfm_my_check_function' );

	function ovabrw_wcfm_my_check_function(){
		if( class_exists('WCFM') ){
			new OVABRW_WCFM();	
		}
	}
	

}else{

	add_action( 'admin_notices', 'ovabrw_wcfm_admin_notices' );

	function ovabrw_wcfm_admin_notices(){ ?>
		<div id="message" class="error">
		<p><?php esc_html_e( 'The WooCommerce or BRW plugin is inactive', 'ova-brw-wcfm' ); ?></p>
		</div>
	<?php }

}