<?php
/*
Plugin Name: BRW - Booking & Rental Plugin
Plugin URI: https://themeforest.net/user/ovatheme/portfolio
Description: OvaTheme Booking, Rental WooCommerce Plugin.
Author: Ovatheme
Version: 12.4.3
Author URI: https://themeforest.net/user/ovatheme
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: ova-brw
Domain Path: /languages/
*/

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'OVABRW' ) ) {

	 class OVABRW {

		/**
		 * OVABRW Constructor
		 */
		public function __construct() {
			// Define
			$this->define_constants();

			// Include files
			$this->includes();

			// Load textdomain
			add_action( 'init', array( $this, 'load_textdomain' ) );

			// Register elementor
			$this->ovabrw_register_elementor();
		}

		/**
		 * Define constants
		 */
		public function define_constants() {
			define( 'OVABRW_PLUGIN_FILE', __FILE__ );
			define( 'OVABRW_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
			define( 'OVABRW_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Include files
		 */
		public function includes() {
			// Funciton
			require_once( OVABRW_PLUGIN_PATH . 'inc/ovabrw-functions.php' );

			// Get data
			require_once( OVABRW_PLUGIN_PATH . 'inc/ovabrw-get-data.php' );

			// Admin
			add_action( 'init', function() {
				require_once OVABRW_PLUGIN_PATH . 'admin/init.php';
			});

			// Add taxonomy type
			require_once( OVABRW_PLUGIN_PATH . 'inc/ovabrw-taxonomy.php' );

			// Add Js Css
			require_once( OVABRW_PLUGIN_PATH . 'inc/ovabrw-assets.php' );
			
			// Cart
			require_once( OVABRW_PLUGIN_PATH . 'inc/ovabrw-cart.php' );

			// Calculate Before add to cart
			require_once( OVABRW_PLUGIN_PATH . 'inc/ovabrw-cus-cal-cart.php' );

			// Add tab beside description
			require_once( OVABRW_PLUGIN_PATH . 'inc/ovabrw-extra-tab.php' );			

			// Filter name
			require_once( OVABRW_PLUGIN_PATH . 'inc/ovabrw_hooks.php' );

			// Deposit
			require_once( OVABRW_PLUGIN_PATH . 'inc/ovabrw_deposit.php' );

			// Ajax
			require_once( OVABRW_PLUGIN_PATH . 'inc/ovabrw_ajax.php' );

			// Register Custom Post Type
			require_once( OVABRW_PLUGIN_PATH . 'custom-post-type/register_cpt.php' );

			// Shortcode
			require_once( OVABRW_PLUGIN_PATH . 'shortcodes/shortcodes.php' );

			// Cron
			require_once( OVABRW_PLUGIN_PATH . 'inc/ovabrw-cron.php' );

			// Send mail
			require_once( OVABRW_PLUGIN_PATH . 'inc/ovabrw-mail.php' );
		}

		/**
		 * Load textdomain
		 */
		public function load_textdomain() {
			if ( is_textdomain_loaded( 'ova-brw' ) ) return;

			load_plugin_textdomain( 'ova-brw', false, basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Register elementor
		 * @return [type] [description]
		 */
		public function ovabrw_register_elementor() {
			if ( did_action( 'elementor/loaded' ) ) {
				include OVABRW_PLUGIN_PATH . 'elementor/ovabrw-register-elementor.php';
			}
		}
	}
}

/**
 * Require plugin.php to use is_plugin_active() below
 */
if ( !function_exists('is_plugin_active') ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

/**
 * Init OVABRW()
 */
if ( is_plugin_active( 'woocommerce/woocommerce.php' )) {
	new OVABRW();
}