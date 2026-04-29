<?php
/**
 * Plugin Name: Alawa HostPlatform Sync
 * Plugin URI: https://alawaresort.local/
 * Description: Production bridge between WooCommerce/OVA BRW bookings and HostPlatform reservations, inventory, and webhooks.
 * Version: 1.1.0
 * Author: Ejaj Mahmud
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Text Domain: alawa-hostplatform-sync
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Alawa_HostPlatform_Sync {
	const VERSION = '1.1.0';
	const OPTION = 'alawa_hps_settings';
	const SCHEMA_OPTION = 'alawa_hps_schema_version';
	const CRON_HOOK = 'alawa_hps_cron_sync';
	const META_ROOM_ID = '_alawa_hps_room_id';
	const META_UNIT_ID = '_alawa_hps_unit_id';
	const META_UNIT_POOL = '_alawa_hps_unit_pool';
	const META_LISTING_TYPE = '_alawa_hps_listing_type';
	const META_PUSH_ENABLED = '_alawa_hps_push_enabled';
	const ORDER_META_SYNCED = '_alawa_hps_synced';
	const ORDER_META_RESERVATIONS = '_alawa_hps_reservations';

	private static $instance = null;
	private $settings = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		add_filter( 'cron_schedules', array( $this, 'cron_schedules' ) );
		add_action( 'plugins_loaded', array( $this, 'load' ) );
	}

	public function load() {
		$this->maybe_upgrade_schema();
		add_action( self::CRON_HOOK, array( $this, 'cron_sync' ) );

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'handle_admin_actions' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'current_screen', array( $this, 'suppress_foreign_admin_notices' ) );

		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'product_fields' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_fields' ) );

		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_to_cart' ), 20, 5 );
		add_action( 'woocommerce_check_cart_items', array( $this, 'validate_cart' ), 20 );
		add_action( 'woocommerce_order_status_processing', array( $this, 'push_order_to_hostplatform' ), 20 );
		add_action( 'woocommerce_order_status_completed', array( $this, 'push_order_to_hostplatform' ), 20 );
		add_action( 'woocommerce_order_status_cancelled', array( $this, 'mark_order_cancelled' ), 20 );
		add_action( 'woocommerce_order_status_refunded', array( $this, 'mark_order_cancelled' ), 20 );
		add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'render_order_sync_status' ) );

		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	public function activate() {
		$this->create_tables();
		$this->ensure_settings();
		update_option( self::SCHEMA_OPTION, self::VERSION, false );
		$this->schedule_cron();
		flush_rewrite_rules();
	}

	public function deactivate() {
		wp_clear_scheduled_hook( self::CRON_HOOK );
		flush_rewrite_rules();
	}

	public function cron_schedules( $schedules ) {
		$schedules['alawa_hps_5min'] = array(
			'interval' => 5 * MINUTE_IN_SECONDS,
			'display'  => __( 'Every 5 minutes', 'alawa-hostplatform-sync' ),
		);

		$schedules['alawa_hps_15min'] = array(
			'interval' => 15 * MINUTE_IN_SECONDS,
			'display'  => __( 'Every 15 minutes', 'alawa-hostplatform-sync' ),
		);

		return $schedules;
	}

	private function schedule_cron() {
		if ( wp_next_scheduled( self::CRON_HOOK ) ) {
			return;
		}

		wp_schedule_event( time() + 60, 'alawa_hps_15min', self::CRON_HOOK );
	}

	private function create_tables() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset = $wpdb->get_charset_collate();
		$inventory = $wpdb->prefix . 'alawa_hps_inventory';
		$logs = $wpdb->prefix . 'alawa_hps_logs';
		$webhooks = $wpdb->prefix . 'alawa_hps_webhooks';
		$retry_queue = $wpdb->prefix . 'alawa_hps_retry_queue';

		dbDelta(
			"CREATE TABLE {$inventory} (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				product_id bigint(20) unsigned NOT NULL,
				listing_type varchar(20) NOT NULL DEFAULT 'room',
				listing_id varchar(80) NOT NULL,
				inventory_date date NOT NULL,
				available int(11) NOT NULL DEFAULT 0,
				occupied int(11) NOT NULL DEFAULT 0,
				booked int(11) NOT NULL DEFAULT 0,
				blocked int(11) NOT NULL DEFAULT 0,
				raw longtext NULL,
				synced_at datetime NOT NULL,
				PRIMARY KEY  (id),
				UNIQUE KEY product_listing_date (product_id, listing_type, listing_id, inventory_date),
				KEY inventory_date (inventory_date),
				KEY listing_id (listing_id)
			) {$charset};"
		);

		dbDelta(
			"CREATE TABLE {$logs} (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				created_at datetime NOT NULL,
				level varchar(20) NOT NULL DEFAULT 'info',
				source varchar(40) NOT NULL DEFAULT 'system',
				message text NOT NULL,
				context longtext NULL,
				PRIMARY KEY  (id),
				KEY created_at (created_at),
				KEY level (level),
				KEY source (source)
			) {$charset};"
		);

		dbDelta(
			"CREATE TABLE {$webhooks} (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				received_at datetime NOT NULL,
				event_key varchar(191) NULL,
				signature varchar(191) NULL,
				payload longtext NOT NULL,
				processed tinyint(1) NOT NULL DEFAULT 0,
				note text NULL,
				PRIMARY KEY  (id),
				KEY received_at (received_at),
				KEY event_key (event_key),
				KEY processed (processed)
			) {$charset};"
		);

		dbDelta(
			"CREATE TABLE {$retry_queue} (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				action varchar(40) NOT NULL DEFAULT 'create_reservation',
				status varchar(20) NOT NULL DEFAULT 'pending',
				order_id bigint(20) unsigned NOT NULL DEFAULT 0,
				order_item_id bigint(20) unsigned NOT NULL DEFAULT 0,
				product_id bigint(20) unsigned NOT NULL DEFAULT 0,
				listing_type varchar(20) NOT NULL DEFAULT 'room',
				listing_id varchar(80) NOT NULL DEFAULT '',
				payload longtext NOT NULL,
				last_error text NULL,
				attempts int(11) NOT NULL DEFAULT 0,
				next_retry_at datetime NOT NULL,
				last_attempt_at datetime NULL,
				completed_at datetime NULL,
				response longtext NULL,
				created_at datetime NOT NULL,
				updated_at datetime NOT NULL,
				PRIMARY KEY  (id),
				KEY status_next_retry (status, next_retry_at),
				KEY order_item_action (order_id, order_item_id, action),
				KEY product_id (product_id)
			) {$charset};"
		);
	}

	private function maybe_upgrade_schema() {
		$installed = (string) get_option( self::SCHEMA_OPTION, '' );
		if ( self::VERSION === $installed ) {
			return;
		}

		$this->create_tables();
		update_option( self::SCHEMA_OPTION, self::VERSION, false );
	}

	private function ensure_settings() {
		if ( get_option( self::OPTION, null ) === null ) {
			add_option( self::OPTION, $this->default_settings(), '', false );
		}
	}

	private function default_settings() {
		return array(
			'enabled'              => 'yes',
			'active_mode'          => 'production',
			'base_url'             => 'https://nebulapi-asg.hostplatform.com',
			'api_namespace'        => '/external/v1',
			'auth_mode'            => 'access_token',
			'access_token'         => '',
			'property_id'          => '',
			'production_inventory_base_url'        => '',
			'production_inventory_api_namespace'   => '/v1',
			'production_inventory_auth_mode'       => 'jwt',
			'production_inventory_access_token'    => '',
			'production_inventory_property_id'     => '',
			'production_reservation_base_url'      => '',
			'production_reservation_api_namespace' => '/external/v1',
			'production_reservation_auth_mode'     => 'access_token',
			'production_reservation_access_token'  => '',
			'production_reservation_property_id'   => '',
			'staging_inventory_base_url'           => 'https://nebulapi-stg.hostastay.com',
			'staging_inventory_api_namespace'      => '/external/v1',
			'staging_inventory_auth_mode'          => 'access_token',
			'staging_inventory_access_token'       => '',
			'staging_inventory_property_id'        => '',
			'staging_reservation_base_url'         => 'https://nebulapi-stg.hostastay.com',
			'staging_reservation_api_namespace'    => '/external/v1',
			'staging_reservation_auth_mode'        => 'access_token',
			'staging_reservation_access_token'     => '',
			'staging_reservation_property_id'      => '',
			'webhook_secret'       => wp_generate_password( 32, false, false ),
			'webhook_hmac_secret'  => '',
			'default_source'       => 'website',
			'sync_days_forward'    => 365,
			'sync_days_back'       => 2,
			'cron_enabled'         => 'yes',
			'cron_schedule'        => 'alawa_hps_15min',
			'push_on_statuses'     => array( 'processing', 'completed' ),
			'checkout_live_check'  => 'yes',
			'cache_fallback'       => 'yes',
			'create_guest_email'   => 'no',
			'log_retention_days'   => 30,
			'last_full_sync'       => '',
			'last_cron_run'        => '',
			'last_cron_status'     => '',
			'last_retry_run'       => '',
		);
	}

	private function settings() {
		if ( null === $this->settings ) {
			$saved = get_option( self::OPTION, array() );
			$this->settings = wp_parse_args( is_array( $saved ) ? $saved : array(), $this->default_settings() );
			$this->settings = $this->normalize_settings( $this->settings );
		}

		return $this->settings;
	}

	private function update_settings( $settings ) {
		$this->settings = $this->normalize_settings( wp_parse_args( $settings, $this->default_settings() ) );
		update_option( self::OPTION, $this->settings, false );
	}

	private function normalize_settings( array $settings ) {
		$settings['active_mode'] = isset( $settings['active_mode'] ) && in_array( $settings['active_mode'], array( 'staging', 'production' ), true ) ? $settings['active_mode'] : 'production';

		foreach ( array( 'production', 'staging' ) as $mode ) {
			foreach ( array( 'inventory', 'reservation' ) as $purpose ) {
				$prefix = $mode . '_' . $purpose . '_';
				$legacy_base_url = $settings['base_url'] ?? '';
				$legacy_namespace = $settings['api_namespace'] ?? '/external/v1';
				$legacy_auth_mode = $settings['auth_mode'] ?? 'access_token';
				$legacy_token = $settings['access_token'] ?? '';
				$legacy_property = $settings['property_id'] ?? '';

				if ( 'production' === $mode && empty( $settings[ $prefix . 'base_url' ] ) && ! empty( $legacy_base_url ) ) {
					$settings[ $prefix . 'base_url' ] = $legacy_base_url;
				}

				if ( 'production' === $mode && empty( $settings[ $prefix . 'api_namespace' ] ) && ! empty( $legacy_namespace ) ) {
					$settings[ $prefix . 'api_namespace' ] = $legacy_namespace;
				}

				if ( 'production' === $mode && empty( $settings[ $prefix . 'auth_mode' ] ) && ! empty( $legacy_auth_mode ) ) {
					$settings[ $prefix . 'auth_mode' ] = $legacy_auth_mode;
				}

				if ( 'production' === $mode && empty( $settings[ $prefix . 'access_token' ] ) && ! empty( $legacy_token ) ) {
					$settings[ $prefix . 'access_token' ] = $legacy_token;
				}

				if ( 'production' === $mode && empty( $settings[ $prefix . 'property_id' ] ) && ! empty( $legacy_property ) ) {
					$settings[ $prefix . 'property_id' ] = $legacy_property;
				}
			}
		}

		return $settings;
	}

	private function active_mode() {
		return $this->settings()['active_mode'] ?? 'production';
	}

	private function profile_config( $purpose = 'inventory', $mode = null ) {
		$settings = $this->settings();
		$mode = $mode && in_array( $mode, array( 'staging', 'production' ), true ) ? $mode : $this->active_mode();
		$prefix = $mode . '_' . $purpose . '_';

		return array(
			'mode'          => $mode,
			'purpose'       => $purpose,
			'base_url'      => $settings[ $prefix . 'base_url' ] ?? '',
			'api_namespace' => $settings[ $prefix . 'api_namespace' ] ?? '/external/v1',
			'auth_mode'     => $settings[ $prefix . 'auth_mode' ] ?? 'access_token',
			'access_token'  => $settings[ $prefix . 'access_token' ] ?? '',
			'property_id'   => $settings[ $prefix . 'property_id' ] ?? '',
		);
	}

	private function profile_is_live( $purpose = 'inventory', $mode = null ) {
		$profile = $this->profile_config( $purpose, $mode );
		return false === strpos( $profile['api_namespace'], '/external/v1' );
	}

	private function profile_label( $purpose = 'inventory', $mode = null ) {
		$profile = $this->profile_config( $purpose, $mode );
		$mode_label = 'staging' === $profile['mode'] ? 'Staging' : 'Production';
		$purpose_label = 'reservation' === $purpose ? 'reservation push' : 'inventory';
		return $mode_label . ' ' . $purpose_label;
	}

	public function admin_menu() {
		add_menu_page(
			__( 'HostPlatform Sync', 'alawa-hostplatform-sync' ),
			__( 'HostPlatform Sync', 'alawa-hostplatform-sync' ),
			'manage_woocommerce',
			'alawa-hps',
			array( $this, 'render_app_page' ),
			'dashicons-update-alt',
			56
		);

		add_submenu_page( 'alawa-hps', __( 'Settings', 'alawa-hostplatform-sync' ), __( 'Settings', 'alawa-hostplatform-sync' ), 'manage_woocommerce', 'alawa-hps-settings', array( $this, 'render_app_page' ) );
		add_submenu_page( 'alawa-hps', __( 'Room Mapping', 'alawa-hostplatform-sync' ), __( 'Room Mapping', 'alawa-hostplatform-sync' ), 'manage_woocommerce', 'alawa-hps-mapping', array( $this, 'render_app_page' ) );
		add_submenu_page( 'alawa-hps', __( 'Inventory', 'alawa-hostplatform-sync' ), __( 'Inventory', 'alawa-hostplatform-sync' ), 'manage_woocommerce', 'alawa-hps-inventory', array( $this, 'render_app_page' ) );
		add_submenu_page( 'alawa-hps', __( 'Reconciliation', 'alawa-hostplatform-sync' ), __( 'Reconciliation', 'alawa-hostplatform-sync' ), 'manage_woocommerce', 'alawa-hps-reconciliation', array( $this, 'render_app_page' ) );
		add_submenu_page( 'alawa-hps', __( 'Retry Queue', 'alawa-hostplatform-sync' ), __( 'Retry Queue', 'alawa-hostplatform-sync' ), 'manage_woocommerce', 'alawa-hps-retries', array( $this, 'render_app_page' ) );
		add_submenu_page( 'alawa-hps', __( 'Logs', 'alawa-hostplatform-sync' ), __( 'Logs', 'alawa-hostplatform-sync' ), 'manage_woocommerce', 'alawa-hps-logs', array( $this, 'render_app_page' ) );
	}

	public function admin_enqueue_scripts( $hook ) {
		if ( ! $this->is_plugin_admin_page() && false === strpos( (string) $hook, 'alawa-hps' ) ) {
			return;
		}

		wp_enqueue_style(
			'alawa-hps-admin',
			plugins_url( 'assets/admin.css', __FILE__ ),
			array( 'wp-components' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'assets/admin.css' )
		);

		wp_enqueue_script(
			'alawa-hps-admin',
			plugins_url( 'assets/admin.js', __FILE__ ),
			array( 'wp-element', 'wp-components', 'wp-api-fetch' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'assets/admin.js' ),
			true
		);

		wp_add_inline_script(
			'alawa-hps-admin',
			'window.AlawaHPS = ' . wp_json_encode(
				array(
					'nonce'    => wp_create_nonce( 'wp_rest' ),
					'page'     => isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : 'alawa-hps',
					'pageUrls' => array(
						'dashboard'      => admin_url( 'admin.php?page=alawa-hps' ),
						'settings'       => admin_url( 'admin.php?page=alawa-hps-settings' ),
						'mapping'        => admin_url( 'admin.php?page=alawa-hps-mapping' ),
						'inventory'      => admin_url( 'admin.php?page=alawa-hps-inventory' ),
						'reconciliation' => admin_url( 'admin.php?page=alawa-hps-reconciliation' ),
						'retries'        => admin_url( 'admin.php?page=alawa-hps-retries' ),
						'logs'           => admin_url( 'admin.php?page=alawa-hps-logs' ),
					),
				)
			) . ';',
			'before'
		);

		wp_add_inline_style(
			'alawa-hps-admin',
			'
			body.toplevel_page_alawa-hps #wpbody-content > .notice,
			body.toplevel_page_alawa-hps #wpbody-content > .error,
			body.toplevel_page_alawa-hps #wpbody-content > .updated,
			body.toplevel_page_alawa-hps #wpbody-content > .update-nag,
			body.toplevel_page_alawa-hps #wpbody-content > div.error,
			body.toplevel_page_alawa-hps #wpbody-content > div.updated,
			body.toplevel_page_alawa-hps #wpbody-content > div.notice,
			body.hostplatform-sync_page_alawa-hps-settings #wpbody-content > .notice,
			body.hostplatform-sync_page_alawa-hps-settings #wpbody-content > .error,
			body.hostplatform-sync_page_alawa-hps-settings #wpbody-content > .updated,
			body.hostplatform-sync_page_alawa-hps-settings #wpbody-content > .update-nag,
			body.hostplatform-sync_page_alawa-hps-settings #wpbody-content > div.error,
			body.hostplatform-sync_page_alawa-hps-settings #wpbody-content > div.updated,
			body.hostplatform-sync_page_alawa-hps-settings #wpbody-content > div.notice,
			body.hostplatform-sync_page_alawa-hps-mapping #wpbody-content > .notice,
			body.hostplatform-sync_page_alawa-hps-mapping #wpbody-content > .error,
			body.hostplatform-sync_page_alawa-hps-mapping #wpbody-content > .updated,
			body.hostplatform-sync_page_alawa-hps-mapping #wpbody-content > .update-nag,
			body.hostplatform-sync_page_alawa-hps-mapping #wpbody-content > div.error,
			body.hostplatform-sync_page_alawa-hps-mapping #wpbody-content > div.updated,
			body.hostplatform-sync_page_alawa-hps-mapping #wpbody-content > div.notice,
			body.hostplatform-sync_page_alawa-hps-inventory #wpbody-content > .notice,
			body.hostplatform-sync_page_alawa-hps-inventory #wpbody-content > .error,
			body.hostplatform-sync_page_alawa-hps-inventory #wpbody-content > .updated,
			body.hostplatform-sync_page_alawa-hps-inventory #wpbody-content > .update-nag,
			body.hostplatform-sync_page_alawa-hps-inventory #wpbody-content > div.error,
			body.hostplatform-sync_page_alawa-hps-inventory #wpbody-content > div.updated,
			body.hostplatform-sync_page_alawa-hps-inventory #wpbody-content > div.notice,
			body.hostplatform-sync_page_alawa-hps-reconciliation #wpbody-content > .notice,
			body.hostplatform-sync_page_alawa-hps-reconciliation #wpbody-content > .error,
			body.hostplatform-sync_page_alawa-hps-reconciliation #wpbody-content > .updated,
			body.hostplatform-sync_page_alawa-hps-reconciliation #wpbody-content > .update-nag,
			body.hostplatform-sync_page_alawa-hps-reconciliation #wpbody-content > div.error,
			body.hostplatform-sync_page_alawa-hps-reconciliation #wpbody-content > div.updated,
			body.hostplatform-sync_page_alawa-hps-reconciliation #wpbody-content > div.notice,
			body.hostplatform-sync_page_alawa-hps-retries #wpbody-content > .notice,
			body.hostplatform-sync_page_alawa-hps-retries #wpbody-content > .error,
			body.hostplatform-sync_page_alawa-hps-retries #wpbody-content > .updated,
			body.hostplatform-sync_page_alawa-hps-retries #wpbody-content > .update-nag,
			body.hostplatform-sync_page_alawa-hps-retries #wpbody-content > div.error,
			body.hostplatform-sync_page_alawa-hps-retries #wpbody-content > div.updated,
			body.hostplatform-sync_page_alawa-hps-retries #wpbody-content > div.notice,
			body.hostplatform-sync_page_alawa-hps-logs #wpbody-content > .notice,
			body.hostplatform-sync_page_alawa-hps-logs #wpbody-content > .error,
			body.hostplatform-sync_page_alawa-hps-logs #wpbody-content > .updated,
			body.hostplatform-sync_page_alawa-hps-logs #wpbody-content > .update-nag,
			body.hostplatform-sync_page_alawa-hps-logs #wpbody-content > div.error,
			body.hostplatform-sync_page_alawa-hps-logs #wpbody-content > div.updated,
			body.hostplatform-sync_page_alawa-hps-logs #wpbody-content > div.notice,
			body.toplevel_page_alawa-hps #screen-meta,
			body.hostplatform-sync_page_alawa-hps-settings #screen-meta,
			body.hostplatform-sync_page_alawa-hps-mapping #screen-meta,
			body.hostplatform-sync_page_alawa-hps-inventory #screen-meta,
			body.hostplatform-sync_page_alawa-hps-reconciliation #screen-meta,
			body.hostplatform-sync_page_alawa-hps-retries #screen-meta,
			body.hostplatform-sync_page_alawa-hps-logs #screen-meta {
				display: none !important;
			}
			'
		);
	}

	private function is_plugin_admin_page() {
		if ( ! is_admin() ) {
			return false;
		}

		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		return in_array( $page, array( 'alawa-hps', 'alawa-hps-settings', 'alawa-hps-mapping', 'alawa-hps-inventory', 'alawa-hps-reconciliation', 'alawa-hps-retries', 'alawa-hps-logs' ), true );
	}

	public function suppress_foreign_admin_notices() {
		if ( ! $this->is_plugin_admin_page() ) {
			return;
		}

		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );
		remove_all_actions( 'network_admin_notices' );
		remove_all_actions( 'user_admin_notices' );
	}

	public function render_app_page() {
		echo '<div class="wrap"><div id="alawa-hps-admin-root"></div></div>';
	}

	public function admin_notices() {
		if ( empty( $_GET['alawa_hps_notice'] ) ) {
			return;
		}

		$type = 'success' === $_GET['alawa_hps_notice'] ? 'success' : 'error';
		$message = isset( $_GET['alawa_hps_message'] ) ? sanitize_text_field( wp_unslash( $_GET['alawa_hps_message'] ) ) : '';
		if ( $message ) {
			printf( '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>', esc_attr( $type ), esc_html( $message ) );
		}
	}

	public function handle_admin_actions() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		if ( isset( $_POST['alawa_hps_save_settings'] ) ) {
			check_admin_referer( 'alawa_hps_save_settings' );
			$this->save_settings_from_post();
			$this->redirect_notice( 'success', __( 'Settings saved.', 'alawa-hostplatform-sync' ) );
		}

		if ( isset( $_POST['alawa_hps_save_mapping'] ) ) {
			check_admin_referer( 'alawa_hps_save_mapping' );
			$this->save_mapping_from_post();
			$this->redirect_notice( 'success', __( 'Room mapping saved.', 'alawa-hostplatform-sync' ) );
		}

		if ( isset( $_POST['alawa_hps_test_connection'] ) ) {
			check_admin_referer( 'alawa_hps_tools' );
			$result = $this->api_get_rooms();
			if ( is_wp_error( $result ) ) {
				$this->log( 'error', 'admin', 'Connection test failed.', array( 'error' => $result->get_error_message(), 'details' => $result->get_error_data() ) );
				$this->redirect_notice( 'error', $result->get_error_message() );
			}
			$count = isset( $result['rooms'] ) && is_array( $result['rooms'] ) ? count( $result['rooms'] ) : ( isset( $result['units'] ) && is_array( $result['units'] ) ? count( $result['units'] ) : 0 );
			$this->log( 'info', 'admin', 'Connection test succeeded.', array( 'items' => $count ) );
			$this->redirect_notice( 'success', sprintf( __( 'Connection successful. %d item(s) returned.', 'alawa-hostplatform-sync' ), $count ) );
		}

		if ( isset( $_POST['alawa_hps_run_sync'] ) ) {
			check_admin_referer( 'alawa_hps_tools' );
			$result = $this->sync_all_mapped_products( true );
			if ( is_wp_error( $result ) ) {
				$this->redirect_notice( 'error', $result->get_error_message() );
			}
			$this->redirect_notice( 'success', sprintf( __( 'Sync complete. %d product(s) refreshed.', 'alawa-hostplatform-sync' ), (int) $result ) );
		}

		if ( isset( $_POST['alawa_hps_clear_logs'] ) ) {
			check_admin_referer( 'alawa_hps_tools' );
			$this->clear_logs();
			$this->redirect_notice( 'success', __( 'Logs cleared.', 'alawa-hostplatform-sync' ) );
		}
	}

	private function redirect_notice( $type, $message ) {
		wp_safe_redirect(
			add_query_arg(
				array(
					'alawa_hps_notice' => $type,
					'alawa_hps_message' => rawurlencode( $message ),
				),
				wp_get_referer() ? wp_get_referer() : admin_url( 'admin.php?page=alawa-hps' )
			)
		);
		exit;
	}

	private function save_settings_from_post() {
		$settings = $this->settings();
		$post = wp_unslash( $_POST );

		$settings['enabled'] = ! empty( $post['enabled'] ) ? 'yes' : 'no';
		$settings['active_mode'] = isset( $post['active_mode'] ) && in_array( $post['active_mode'], array( 'staging', 'production' ), true ) ? $post['active_mode'] : 'production';
		foreach ( array( 'production', 'staging' ) as $mode ) {
			foreach ( array( 'inventory', 'reservation' ) as $purpose ) {
				$prefix = $mode . '_' . $purpose . '_';
				$settings[ $prefix . 'base_url' ] = isset( $post[ $prefix . 'base_url' ] ) ? esc_url_raw( trim( $post[ $prefix . 'base_url' ] ) ) : '';
				$settings[ $prefix . 'api_namespace' ] = isset( $post[ $prefix . 'api_namespace' ] ) ? sanitize_text_field( trim( $post[ $prefix . 'api_namespace' ] ) ) : '/external/v1';
				$settings[ $prefix . 'auth_mode' ] = isset( $post[ $prefix . 'auth_mode' ] ) && in_array( $post[ $prefix . 'auth_mode' ], array( 'access_token', 'bearer', 'jwt' ), true ) ? $post[ $prefix . 'auth_mode' ] : 'access_token';
				$settings[ $prefix . 'access_token' ] = isset( $post[ $prefix . 'access_token' ] ) ? sanitize_text_field( trim( $post[ $prefix . 'access_token' ] ) ) : '';
				$settings[ $prefix . 'property_id' ] = isset( $post[ $prefix . 'property_id' ] ) ? sanitize_text_field( trim( $post[ $prefix . 'property_id' ] ) ) : '';
			}
		}
		$active_prefix = $settings['active_mode'] . '_inventory_';
		$settings['base_url'] = $settings[ $active_prefix . 'base_url' ] ?? '';
		$settings['api_namespace'] = $settings[ $active_prefix . 'api_namespace' ] ?? '/external/v1';
		$settings['auth_mode'] = $settings[ $active_prefix . 'auth_mode' ] ?? 'access_token';
		$settings['access_token'] = $settings[ $active_prefix . 'access_token' ] ?? '';
		$settings['property_id'] = $settings[ $active_prefix . 'property_id' ] ?? '';
		$settings['webhook_secret'] = isset( $post['webhook_secret'] ) ? sanitize_text_field( trim( $post['webhook_secret'] ) ) : '';
		$settings['webhook_hmac_secret'] = isset( $post['webhook_hmac_secret'] ) ? sanitize_text_field( trim( $post['webhook_hmac_secret'] ) ) : '';
		$settings['default_source'] = isset( $post['default_source'] ) ? sanitize_key( $post['default_source'] ) : 'website';
		$settings['sync_days_forward'] = max( 1, absint( $post['sync_days_forward'] ?? 365 ) );
		$settings['sync_days_back'] = max( 0, absint( $post['sync_days_back'] ?? 2 ) );
		$settings['cron_enabled'] = ! empty( $post['cron_enabled'] ) ? 'yes' : 'no';
		$settings['cron_schedule'] = in_array( $post['cron_schedule'] ?? '', array( 'alawa_hps_5min', 'alawa_hps_15min', 'hourly', 'twicedaily', 'daily' ), true ) ? $post['cron_schedule'] : 'alawa_hps_15min';
		$settings['checkout_live_check'] = ! empty( $post['checkout_live_check'] ) ? 'yes' : 'no';
		$settings['cache_fallback'] = ! empty( $post['cache_fallback'] ) ? 'yes' : 'no';
		$settings['create_guest_email'] = ! empty( $post['create_guest_email'] ) ? 'yes' : 'no';
		$settings['log_retention_days'] = max( 1, absint( $post['log_retention_days'] ?? 30 ) );

		$this->update_settings( $settings );

		wp_clear_scheduled_hook( self::CRON_HOOK );
		if ( 'yes' === $settings['cron_enabled'] ) {
			wp_schedule_event( time() + 60, $settings['cron_schedule'], self::CRON_HOOK );
		}
	}

	private function save_mapping_from_post() {
		$products = $this->rental_products();
		$post = wp_unslash( $_POST );
		$mapping = isset( $post['mapping'] ) && is_array( $post['mapping'] ) ? $post['mapping'] : array();

		foreach ( $products as $product ) {
			$product_id = $product->ID;
			$row = isset( $mapping[ $product_id ] ) && is_array( $mapping[ $product_id ] ) ? $mapping[ $product_id ] : array();
			$listing_type = isset( $row['listing_type'] ) && 'unit' === $row['listing_type'] ? 'unit' : 'room';
			$room_id = isset( $row['room_id'] ) ? sanitize_text_field( trim( $row['room_id'] ) ) : '';
			$unit_id = isset( $row['unit_id'] ) ? sanitize_text_field( trim( $row['unit_id'] ) ) : '';
			$push_enabled = ! empty( $row['push_enabled'] ) ? 'yes' : 'no';

			update_post_meta( $product_id, self::META_LISTING_TYPE, $listing_type );
			update_post_meta( $product_id, self::META_ROOM_ID, $room_id );
			update_post_meta( $product_id, self::META_UNIT_ID, $unit_id );
			update_post_meta( $product_id, self::META_PUSH_ENABLED, $push_enabled );
		}
	}

	public function render_settings_page() {
		$settings = $this->settings();
		$webhook_url = rest_url( 'alawa/v1/hostplatform-webhook' );
		$secret_url = add_query_arg( 'secret', rawurlencode( $settings['webhook_secret'] ), $webhook_url );
		?>
		<div class="wrap alawa-hps-wrap">
			<h1><?php esc_html_e( 'HostPlatform Sync Settings', 'alawa-hostplatform-sync' ); ?></h1>
			<p><?php esc_html_e( 'Switch between staging and production while keeping separate inventory and reservation credentials for each environment.', 'alawa-hostplatform-sync' ); ?></p>
			<form method="post">
				<?php wp_nonce_field( 'alawa_hps_save_settings' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Integration enabled', 'alawa-hostplatform-sync' ); ?></th>
						<td><label><input type="checkbox" name="enabled" value="1" <?php checked( $settings['enabled'], 'yes' ); ?>> <?php esc_html_e( 'Allow API sync, webhooks, checkout validation, and order push.', 'alawa-hostplatform-sync' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><label for="active_mode"><?php esc_html_e( 'Active mode', 'alawa-hostplatform-sync' ); ?></label></th>
						<td>
							<select id="active_mode" name="active_mode">
								<option value="staging" <?php selected( $settings['active_mode'] ?? 'production', 'staging' ); ?>><?php esc_html_e( 'Staging', 'alawa-hostplatform-sync' ); ?></option>
								<option value="production" <?php selected( $settings['active_mode'] ?? 'production', 'production' ); ?>><?php esc_html_e( 'Production', 'alawa-hostplatform-sync' ); ?></option>
							</select>
							<p class="description"><?php esc_html_e( 'The selected mode drives live inventory syncs, checkout checks, and reservation pushes.', 'alawa-hostplatform-sync' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="default_source"><?php esc_html_e( 'WordPress reservation source', 'alawa-hostplatform-sync' ); ?></label></th>
						<td><input class="regular-text code" type="text" id="default_source" name="default_source" value="<?php echo esc_attr( $settings['default_source'] ); ?>" placeholder="website"></td>
					</tr>
					<?php foreach ( array( 'staging' => 'Staging', 'production' => 'Production' ) as $mode_key => $mode_label ) : ?>
						<tr>
							<th scope="row"><?php echo esc_html( $mode_label . ' inventory' ); ?></th>
							<td>
								<p><input class="regular-text code" type="url" name="<?php echo esc_attr( $mode_key ); ?>_inventory_base_url" value="<?php echo esc_attr( $settings[ $mode_key . '_inventory_base_url' ] ?? '' ); ?>" placeholder="https://nebulapi-asg.hostplatform.com"> <span class="description"><?php esc_html_e( 'Base URL', 'alawa-hostplatform-sync' ); ?></span></p>
								<p><input class="regular-text code" type="text" name="<?php echo esc_attr( $mode_key ); ?>_inventory_api_namespace" value="<?php echo esc_attr( $settings[ $mode_key . '_inventory_api_namespace' ] ?? '/external/v1' ); ?>" placeholder="/v1 or /external/v1"> <span class="description"><?php esc_html_e( 'API prefix', 'alawa-hostplatform-sync' ); ?></span></p>
								<p>
									<select name="<?php echo esc_attr( $mode_key ); ?>_inventory_auth_mode">
										<option value="access_token" <?php selected( $settings[ $mode_key . '_inventory_auth_mode' ] ?? 'access_token', 'access_token' ); ?>>access-token header</option>
										<option value="bearer" <?php selected( $settings[ $mode_key . '_inventory_auth_mode' ] ?? 'access_token', 'bearer' ); ?>>Authorization Bearer</option>
										<option value="jwt" <?php selected( $settings[ $mode_key . '_inventory_auth_mode' ] ?? 'access_token', 'jwt' ); ?>>Authorization JWT</option>
									</select>
									<span class="description"><?php esc_html_e( 'Authentication mode', 'alawa-hostplatform-sync' ); ?></span>
								</p>
								<p><input class="regular-text code" type="password" name="<?php echo esc_attr( $mode_key ); ?>_inventory_access_token" value="<?php echo esc_attr( $settings[ $mode_key . '_inventory_access_token' ] ?? '' ); ?>" autocomplete="new-password"> <span class="description"><?php esc_html_e( 'Access token', 'alawa-hostplatform-sync' ); ?></span></p>
								<p><input class="regular-text code" type="text" name="<?php echo esc_attr( $mode_key ); ?>_inventory_property_id" value="<?php echo esc_attr( $settings[ $mode_key . '_inventory_property_id' ] ?? '' ); ?>" placeholder="6912a0a80e1df0038cc9a5b5 or i12p1"> <span class="description"><?php esc_html_e( 'Property ID', 'alawa-hostplatform-sync' ); ?></span></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo esc_html( $mode_label . ' reservation' ); ?></th>
							<td>
								<p><input class="regular-text code" type="url" name="<?php echo esc_attr( $mode_key ); ?>_reservation_base_url" value="<?php echo esc_attr( $settings[ $mode_key . '_reservation_base_url' ] ?? '' ); ?>" placeholder="https://nebulapi-asg.hostplatform.com"> <span class="description"><?php esc_html_e( 'Base URL', 'alawa-hostplatform-sync' ); ?></span></p>
								<p><input class="regular-text code" type="text" name="<?php echo esc_attr( $mode_key ); ?>_reservation_api_namespace" value="<?php echo esc_attr( $settings[ $mode_key . '_reservation_api_namespace' ] ?? '/external/v1' ); ?>" placeholder="/external/v1 or /v1"> <span class="description"><?php esc_html_e( 'API prefix', 'alawa-hostplatform-sync' ); ?></span></p>
								<p>
									<select name="<?php echo esc_attr( $mode_key ); ?>_reservation_auth_mode">
										<option value="access_token" <?php selected( $settings[ $mode_key . '_reservation_auth_mode' ] ?? 'access_token', 'access_token' ); ?>>access-token header</option>
										<option value="bearer" <?php selected( $settings[ $mode_key . '_reservation_auth_mode' ] ?? 'access_token', 'bearer' ); ?>>Authorization Bearer</option>
										<option value="jwt" <?php selected( $settings[ $mode_key . '_reservation_auth_mode' ] ?? 'access_token', 'jwt' ); ?>>Authorization JWT</option>
									</select>
									<span class="description"><?php esc_html_e( 'Authentication mode', 'alawa-hostplatform-sync' ); ?></span>
								</p>
								<p><input class="regular-text code" type="password" name="<?php echo esc_attr( $mode_key ); ?>_reservation_access_token" value="<?php echo esc_attr( $settings[ $mode_key . '_reservation_access_token' ] ?? '' ); ?>" autocomplete="new-password"> <span class="description"><?php esc_html_e( 'Access token', 'alawa-hostplatform-sync' ); ?></span></p>
								<p><input class="regular-text code" type="text" name="<?php echo esc_attr( $mode_key ); ?>_reservation_property_id" value="<?php echo esc_attr( $settings[ $mode_key . '_reservation_property_id' ] ?? '' ); ?>" placeholder="i91p1 or i12p1"> <span class="description"><?php esc_html_e( 'Property ID (optional for reservation lookups)', 'alawa-hostplatform-sync' ); ?></span></p>
							</td>
						</tr>
					<?php endforeach; ?>
					<tr>
						<th scope="row"><label for="webhook_secret"><?php esc_html_e( 'Webhook URL secret', 'alawa-hostplatform-sync' ); ?></label></th>
						<td>
							<input class="regular-text code" type="text" id="webhook_secret" name="webhook_secret" value="<?php echo esc_attr( $settings['webhook_secret'] ); ?>">
							<p class="description"><?php esc_html_e( 'Use this URL in HostPlatform Webhook:', 'alawa-hostplatform-sync' ); ?></p>
							<input class="large-text code" readonly value="<?php echo esc_attr( $secret_url ); ?>">
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="webhook_hmac_secret"><?php esc_html_e( 'Webhook HMAC secret', 'alawa-hostplatform-sync' ); ?></label></th>
						<td><input class="regular-text code" type="password" id="webhook_hmac_secret" name="webhook_hmac_secret" value="<?php echo esc_attr( $settings['webhook_hmac_secret'] ); ?>" autocomplete="new-password"><p class="description"><?php esc_html_e( 'Optional. If HostPlatform provides signed webhooks, paste that secret here.', 'alawa-hostplatform-sync' ); ?></p></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Sync window', 'alawa-hostplatform-sync' ); ?></th>
						<td>
							<label><?php esc_html_e( 'Back', 'alawa-hostplatform-sync' ); ?> <input class="small-text" type="number" min="0" name="sync_days_back" value="<?php echo esc_attr( $settings['sync_days_back'] ); ?>"> <?php esc_html_e( 'days', 'alawa-hostplatform-sync' ); ?></label>
							&nbsp;
							<label><?php esc_html_e( 'Forward', 'alawa-hostplatform-sync' ); ?> <input class="small-text" type="number" min="1" name="sync_days_forward" value="<?php echo esc_attr( $settings['sync_days_forward'] ); ?>"> <?php esc_html_e( 'days', 'alawa-hostplatform-sync' ); ?></label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Background sync', 'alawa-hostplatform-sync' ); ?></th>
						<td>
							<label><input type="checkbox" name="cron_enabled" value="1" <?php checked( $settings['cron_enabled'], 'yes' ); ?>> <?php esc_html_e( 'Enable scheduled inventory refresh', 'alawa-hostplatform-sync' ); ?></label>
							<select name="cron_schedule">
								<option value="alawa_hps_5min" <?php selected( $settings['cron_schedule'], 'alawa_hps_5min' ); ?>><?php esc_html_e( 'Every 5 minutes', 'alawa-hostplatform-sync' ); ?></option>
								<option value="alawa_hps_15min" <?php selected( $settings['cron_schedule'], 'alawa_hps_15min' ); ?>><?php esc_html_e( 'Every 15 minutes', 'alawa-hostplatform-sync' ); ?></option>
								<option value="hourly" <?php selected( $settings['cron_schedule'], 'hourly' ); ?>><?php esc_html_e( 'Hourly', 'alawa-hostplatform-sync' ); ?></option>
								<option value="twicedaily" <?php selected( $settings['cron_schedule'], 'twicedaily' ); ?>><?php esc_html_e( 'Twice daily', 'alawa-hostplatform-sync' ); ?></option>
								<option value="daily" <?php selected( $settings['cron_schedule'], 'daily' ); ?>><?php esc_html_e( 'Daily', 'alawa-hostplatform-sync' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Checkout protection', 'alawa-hostplatform-sync' ); ?></th>
						<td>
							<label><input type="checkbox" name="checkout_live_check" value="1" <?php checked( $settings['checkout_live_check'], 'yes' ); ?>> <?php esc_html_e( 'Ask HostPlatform live before cart/checkout accepts a booking.', 'alawa-hostplatform-sync' ); ?></label><br>
							<label><input type="checkbox" name="cache_fallback" value="1" <?php checked( $settings['cache_fallback'], 'yes' ); ?>> <?php esc_html_e( 'If the live API is unavailable, fall back to the latest synced inventory cache.', 'alawa-hostplatform-sync' ); ?></label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Guest email fallback', 'alawa-hostplatform-sync' ); ?></th>
						<td><label><input type="checkbox" name="create_guest_email" value="1" <?php checked( $settings['create_guest_email'], 'yes' ); ?>> <?php esc_html_e( 'Generate a placeholder email when the order has no billing email.', 'alawa-hostplatform-sync' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><label for="log_retention_days"><?php esc_html_e( 'Log retention', 'alawa-hostplatform-sync' ); ?></label></th>
						<td><input class="small-text" type="number" min="1" id="log_retention_days" name="log_retention_days" value="<?php echo esc_attr( $settings['log_retention_days'] ); ?>"> <?php esc_html_e( 'days', 'alawa-hostplatform-sync' ); ?></td>
					</tr>
				</table>
				<p><button type="submit" name="alawa_hps_save_settings" class="button button-primary"><?php esc_html_e( 'Save Settings', 'alawa-hostplatform-sync' ); ?></button></p>
			</form>

			<hr>
			<h2><?php esc_html_e( 'Tools', 'alawa-hostplatform-sync' ); ?></h2>
			<form method="post">
				<?php wp_nonce_field( 'alawa_hps_tools' ); ?>
				<button type="submit" name="alawa_hps_test_connection" class="button"><?php esc_html_e( 'Test Connection', 'alawa-hostplatform-sync' ); ?></button>
				<button type="submit" name="alawa_hps_run_sync" class="button"><?php esc_html_e( 'Run Full Inventory Sync', 'alawa-hostplatform-sync' ); ?></button>
				<button type="submit" name="alawa_hps_clear_logs" class="button" onclick="return confirm('Clear all integration logs?');"><?php esc_html_e( 'Clear Logs', 'alawa-hostplatform-sync' ); ?></button>
			</form>
		</div>
		<?php
	}

	public function render_mapping_page() {
		$products = $this->rental_products();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Room Mapping', 'alawa-hostplatform-sync' ); ?></h1>
			<p><?php esc_html_e( 'Connect each OVA BRW rental product to its HostPlatform room or unit ID.', 'alawa-hostplatform-sync' ); ?></p>
			<form method="post">
				<?php wp_nonce_field( 'alawa_hps_save_mapping' ); ?>
				<table class="widefat striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Product', 'alawa-hostplatform-sync' ); ?></th>
							<th><?php esc_html_e( 'Listing type', 'alawa-hostplatform-sync' ); ?></th>
							<th><?php esc_html_e( 'Host room_id', 'alawa-hostplatform-sync' ); ?></th>
							<th><?php esc_html_e( 'Host unit_id', 'alawa-hostplatform-sync' ); ?></th>
							<th><?php esc_html_e( 'Push WP bookings', 'alawa-hostplatform-sync' ); ?></th>
							<th><?php esc_html_e( 'Cached availability', 'alawa-hostplatform-sync' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $products ) ) : ?>
							<tr><td colspan="6"><?php esc_html_e( 'No OVA BRW rental products found.', 'alawa-hostplatform-sync' ); ?></td></tr>
						<?php endif; ?>
						<?php foreach ( $products as $product ) : ?>
							<?php
							$product_id = $product->ID;
							$listing_type = get_post_meta( $product_id, self::META_LISTING_TYPE, true ) ?: 'room';
							$room_id = get_post_meta( $product_id, self::META_ROOM_ID, true );
							$unit_id = get_post_meta( $product_id, self::META_UNIT_ID, true );
							$push_enabled = get_post_meta( $product_id, self::META_PUSH_ENABLED, true );
							$push_enabled = '' === $push_enabled ? 'yes' : $push_enabled;
							$today = current_time( 'Y-m-d' );
							$available = $this->cache_available_for_range( $product_id, $today, gmdate( 'Y-m-d', strtotime( $today . ' +1 day' ) ) );
							?>
							<tr>
								<td><strong><a href="<?php echo esc_url( get_edit_post_link( $product_id ) ); ?>"><?php echo esc_html( get_the_title( $product_id ) ); ?></a></strong><br><code>#<?php echo esc_html( $product_id ); ?></code></td>
								<td>
									<select name="mapping[<?php echo esc_attr( $product_id ); ?>][listing_type]">
										<option value="room" <?php selected( $listing_type, 'room' ); ?>>room</option>
										<option value="unit" <?php selected( $listing_type, 'unit' ); ?>>unit</option>
									</select>
								</td>
								<td><input type="text" class="regular-text code" name="mapping[<?php echo esc_attr( $product_id ); ?>][room_id]" value="<?php echo esc_attr( $room_id ); ?>"></td>
								<td><input type="text" class="regular-text code" name="mapping[<?php echo esc_attr( $product_id ); ?>][unit_id]" value="<?php echo esc_attr( $unit_id ); ?>"></td>
								<td><label><input type="checkbox" name="mapping[<?php echo esc_attr( $product_id ); ?>][push_enabled]" value="1" <?php checked( $push_enabled, 'yes' ); ?>> <?php esc_html_e( 'Enabled', 'alawa-hostplatform-sync' ); ?></label></td>
								<td><?php echo null === $available ? esc_html__( 'No cache', 'alawa-hostplatform-sync' ) : esc_html( (string) $available ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<p><button type="submit" name="alawa_hps_save_mapping" class="button button-primary"><?php esc_html_e( 'Save Mapping', 'alawa-hostplatform-sync' ); ?></button></p>
			</form>
		</div>
		<?php
	}

	public function render_inventory_page() {
		global $wpdb;

		$table = $wpdb->prefix . 'alawa_hps_inventory';
		$rows = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY inventory_date DESC, product_id ASC LIMIT 250" );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Cached Inventory', 'alawa-hostplatform-sync' ); ?></h1>
			<table class="widefat striped">
				<thead><tr><th><?php esc_html_e( 'Date', 'alawa-hostplatform-sync' ); ?></th><th><?php esc_html_e( 'Product', 'alawa-hostplatform-sync' ); ?></th><th><?php esc_html_e( 'Host ID', 'alawa-hostplatform-sync' ); ?></th><th><?php esc_html_e( 'Available', 'alawa-hostplatform-sync' ); ?></th><th><?php esc_html_e( 'Occupied', 'alawa-hostplatform-sync' ); ?></th><th><?php esc_html_e( 'Booked', 'alawa-hostplatform-sync' ); ?></th><th><?php esc_html_e( 'Blocked', 'alawa-hostplatform-sync' ); ?></th><th><?php esc_html_e( 'Synced', 'alawa-hostplatform-sync' ); ?></th></tr></thead>
				<tbody>
				<?php if ( empty( $rows ) ) : ?>
					<tr><td colspan="8"><?php esc_html_e( 'No inventory has been synced yet.', 'alawa-hostplatform-sync' ); ?></td></tr>
				<?php endif; ?>
				<?php foreach ( $rows as $row ) : ?>
					<tr>
						<td><?php echo esc_html( $row->inventory_date ); ?></td>
						<td><a href="<?php echo esc_url( get_edit_post_link( (int) $row->product_id ) ); ?>"><?php echo esc_html( get_the_title( (int) $row->product_id ) ); ?></a></td>
						<td><code><?php echo esc_html( $row->listing_type . ':' . $row->listing_id ); ?></code></td>
						<td><?php echo esc_html( $row->available ); ?></td>
						<td><?php echo esc_html( $row->occupied ); ?></td>
						<td><?php echo esc_html( $row->booked ); ?></td>
						<td><?php echo esc_html( $row->blocked ); ?></td>
						<td><?php echo esc_html( $row->synced_at ); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	public function render_logs_page() {
		global $wpdb;

		$table = $wpdb->prefix . 'alawa_hps_logs';
		$rows = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY id DESC LIMIT 250" );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Integration Logs', 'alawa-hostplatform-sync' ); ?></h1>
			<table class="widefat striped">
				<thead><tr><th><?php esc_html_e( 'Time', 'alawa-hostplatform-sync' ); ?></th><th><?php esc_html_e( 'Level', 'alawa-hostplatform-sync' ); ?></th><th><?php esc_html_e( 'Source', 'alawa-hostplatform-sync' ); ?></th><th><?php esc_html_e( 'Message', 'alawa-hostplatform-sync' ); ?></th><th><?php esc_html_e( 'Context', 'alawa-hostplatform-sync' ); ?></th></tr></thead>
				<tbody>
				<?php if ( empty( $rows ) ) : ?>
					<tr><td colspan="5"><?php esc_html_e( 'No logs yet.', 'alawa-hostplatform-sync' ); ?></td></tr>
				<?php endif; ?>
				<?php foreach ( $rows as $row ) : ?>
					<tr>
						<td><?php echo esc_html( $row->created_at ); ?></td>
						<td><?php echo esc_html( strtoupper( $row->level ) ); ?></td>
						<td><?php echo esc_html( $row->source ); ?></td>
						<td><?php echo esc_html( $row->message ); ?></td>
						<td><textarea readonly rows="3" class="large-text code"><?php echo esc_textarea( $row->context ); ?></textarea></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	public function product_fields() {
		global $post;

		if ( ! $post ) {
			return;
		}

		echo '<div class="options_group show_if_ovabrw_car_rental">';
		echo '<p class="form-field"><strong style="display:block;margin-bottom:6px;">' . esc_html__( 'HostPlatform Sync', 'alawa-hostplatform-sync' ) . '</strong></p>';

		woocommerce_wp_select(
			array(
				'id'      => self::META_LISTING_TYPE,
				'label'   => __( 'Host listing type', 'alawa-hostplatform-sync' ),
				'value'   => get_post_meta( $post->ID, self::META_LISTING_TYPE, true ) ?: 'room',
				'options' => array(
					'room' => 'room',
					'unit' => 'unit',
				),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => self::META_ROOM_ID,
				'label'       => __( 'Host room_id', 'alawa-hostplatform-sync' ),
				'value'       => get_post_meta( $post->ID, self::META_ROOM_ID, true ),
				'placeholder' => 'i14p6r6',
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => self::META_UNIT_ID,
				'label'       => __( 'Host unit_id', 'alawa-hostplatform-sync' ),
				'value'       => get_post_meta( $post->ID, self::META_UNIT_ID, true ),
				'placeholder' => 'i14p6u1',
			)
		);

		woocommerce_wp_checkbox(
			array(
				'id'          => self::META_PUSH_ENABLED,
				'label'       => __( 'Push bookings', 'alawa-hostplatform-sync' ),
				'description' => __( 'Create/update HostPlatform reservation when this product is booked in WooCommerce.', 'alawa-hostplatform-sync' ),
				'value'       => get_post_meta( $post->ID, self::META_PUSH_ENABLED, true ) ?: 'yes',
			)
		);

		echo '</div>';
	}

	public function save_product_fields( $post_id ) {
		$listing_type = isset( $_POST[ self::META_LISTING_TYPE ] ) && 'unit' === $_POST[ self::META_LISTING_TYPE ] ? 'unit' : 'room';
		update_post_meta( $post_id, self::META_LISTING_TYPE, $listing_type );
		update_post_meta( $post_id, self::META_ROOM_ID, isset( $_POST[ self::META_ROOM_ID ] ) ? sanitize_text_field( wp_unslash( $_POST[ self::META_ROOM_ID ] ) ) : '' );
		update_post_meta( $post_id, self::META_UNIT_ID, isset( $_POST[ self::META_UNIT_ID ] ) ? sanitize_text_field( wp_unslash( $_POST[ self::META_UNIT_ID ] ) ) : '' );
		update_post_meta( $post_id, self::META_PUSH_ENABLED, isset( $_POST[ self::META_PUSH_ENABLED ] ) ? 'yes' : 'no' );
	}

	public function register_rest_routes() {
		register_rest_route(
			'alawa/v1',
			'/admin/overview',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'rest_overview' ),
				'permission_callback' => array( $this, 'rest_admin_permission' ),
			)
		);

		register_rest_route(
			'alawa/v1',
			'/admin/health',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'rest_health' ),
				'permission_callback' => array( $this, 'rest_admin_permission' ),
			)
		);

		register_rest_route(
			'alawa/v1',
			'/admin/settings',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'rest_get_settings' ),
					'permission_callback' => array( $this, 'rest_admin_permission' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'rest_save_settings' ),
					'permission_callback' => array( $this, 'rest_admin_permission' ),
				),
			)
		);

		register_rest_route(
			'alawa/v1',
			'/admin/mapping',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'rest_get_mapping' ),
					'permission_callback' => array( $this, 'rest_admin_permission' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'rest_save_mapping' ),
					'permission_callback' => array( $this, 'rest_admin_permission' ),
				),
			)
		);

		register_rest_route(
			'alawa/v1',
			'/admin/inventory',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'rest_inventory' ),
				'permission_callback' => array( $this, 'rest_admin_permission' ),
			)
		);

		register_rest_route(
			'alawa/v1',
			'/admin/reconciliation',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'rest_reconciliation' ),
				'permission_callback' => array( $this, 'rest_admin_permission' ),
			)
		);

		register_rest_route(
			'alawa/v1',
			'/admin/retries',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'rest_retries' ),
				'permission_callback' => array( $this, 'rest_admin_permission' ),
			)
		);

		register_rest_route(
			'alawa/v1',
			'/admin/process-retries',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'rest_process_retries' ),
				'permission_callback' => array( $this, 'rest_admin_permission' ),
			)
		);

		register_rest_route(
			'alawa/v1',
			'/admin/retry/(?P<id>\d+)',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'rest_retry_single' ),
				'permission_callback' => array( $this, 'rest_admin_permission' ),
			)
		);

		register_rest_route(
			'alawa/v1',
			'/admin/logs',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'rest_logs' ),
				'permission_callback' => array( $this, 'rest_admin_permission' ),
			)
		);

		register_rest_route(
			'alawa/v1',
			'/admin/notifications',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'rest_notifications' ),
				'permission_callback' => array( $this, 'rest_admin_permission' ),
			)
		);

		register_rest_route(
			'alawa/v1',
			'/admin/test-connection',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'rest_test_connection' ),
				'permission_callback' => array( $this, 'rest_admin_permission' ),
			)
		);

		register_rest_route(
			'alawa/v1',
			'/admin/run-sync',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'rest_run_sync' ),
				'permission_callback' => array( $this, 'rest_admin_permission' ),
			)
		);

		register_rest_route(
			'alawa/v1',
			'/admin/clear-logs',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'rest_clear_logs' ),
				'permission_callback' => array( $this, 'rest_admin_permission' ),
			)
		);

		register_rest_route(
			'alawa/v1',
			'/hostplatform-webhook',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'handle_webhook' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	public function rest_admin_permission() {
		return current_user_can( 'manage_woocommerce' );
	}

	public function rest_overview() {
		global $wpdb;

		$settings = $this->settings();
		$inventory_profile = $this->profile_config( 'inventory' );
		$reservation_profile = $this->profile_config( 'reservation' );
		$all_products = $this->rental_products();
		$mapped_products = $this->mapped_products();
		$inventory_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}alawa_hps_inventory" );
		$log_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}alawa_hps_logs" );
		$webhook_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}alawa_hps_webhooks" );
		$issue_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}alawa_hps_logs WHERE level IN ('error','warning')" );
		$retry_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}alawa_hps_retry_queue WHERE status IN ('pending','retrying','failed')" );
		$next_cron = wp_next_scheduled( self::CRON_HOOK );

		return rest_ensure_response(
			array(
				'enabled'        => $settings['enabled'],
				'activeMode'     => $settings['active_mode'] ?? 'production',
				'base_url'       => $settings['base_url'],
				'property_id'    => $settings['property_id'],
				'inventoryProfile' => array(
					'mode'          => $inventory_profile['mode'],
					'baseUrl'       => $inventory_profile['base_url'],
					'apiNamespace'  => $inventory_profile['api_namespace'],
					'authMode'      => $inventory_profile['auth_mode'],
					'propertyId'    => $inventory_profile['property_id'],
					'hasToken'      => ! empty( $inventory_profile['access_token'] ),
					'isLive'        => $this->profile_is_live( 'inventory' ),
				),
				'reservationProfile' => array(
					'mode'          => $reservation_profile['mode'],
					'baseUrl'       => $reservation_profile['base_url'],
					'apiNamespace'  => $reservation_profile['api_namespace'],
					'authMode'      => $reservation_profile['auth_mode'],
					'propertyId'    => $reservation_profile['property_id'],
					'hasToken'      => ! empty( $reservation_profile['access_token'] ),
					'isLive'        => $this->profile_is_live( 'reservation' ),
				),
				'cronScheduled'  => (bool) $next_cron,
				'nextCronRun'    => $next_cron ? get_date_from_gmt( gmdate( 'Y-m-d H:i:s', (int) $next_cron ), 'Y-m-d H:i:s' ) : '',
				'productCount'   => count( $all_products ),
				'mappedCount'    => count( $mapped_products ),
				'mappedProducts' => count( $mapped_products ),
				'inventoryCount' => $inventory_count,
				'webhookCount'   => $webhook_count,
				'issueCount'     => $issue_count,
				'retryCount'     => $retry_count,
				'logCount'       => $log_count,
				'lastFullSync'   => $settings['last_full_sync'],
				'lastCronRun'    => $settings['last_cron_run'] ?? '',
				'lastCronStatus' => $settings['last_cron_status'] ?? '',
				'lastRetryRun'   => $settings['last_retry_run'] ?? '',
				'webhookUrl'     => add_query_arg( 'secret', rawurlencode( $settings['webhook_secret'] ), rest_url( 'alawa/v1/hostplatform-webhook' ) ),
			)
		);
	}

	public function rest_health() {
		global $wpdb;

		$settings = $this->settings();
		$next_cron = wp_next_scheduled( self::CRON_HOOK );
		$inventory_table = $wpdb->prefix . 'alawa_hps_inventory';
		$logs_table = $wpdb->prefix . 'alawa_hps_logs';
		$retry_table = $wpdb->prefix . 'alawa_hps_retry_queue';

		$inventory_freshness = $wpdb->get_var( "SELECT MAX(synced_at) FROM {$inventory_table}" );
		$pending_retry_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$retry_table} WHERE status IN ('pending','retrying','failed')" );
		$failed_retry_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$retry_table} WHERE status = 'failed'" );
		$last_sync_error = $wpdb->get_row( "SELECT created_at, message, context FROM {$logs_table} WHERE source = 'sync' AND level = 'error' ORDER BY id DESC LIMIT 1", ARRAY_A );
		$last_order_error = $wpdb->get_row( "SELECT created_at, message, context FROM {$logs_table} WHERE source = 'order' AND level = 'error' ORDER BY id DESC LIMIT 1", ARRAY_A );

		return rest_ensure_response(
			array(
				'cron' => array(
					'enabled'     => $settings['cron_enabled'],
					'schedule'    => $settings['cron_schedule'],
					'nextRun'     => $next_cron ? get_date_from_gmt( gmdate( 'Y-m-d H:i:s', (int) $next_cron ), 'Y-m-d H:i:s' ) : '',
					'lastRun'     => $settings['last_cron_run'] ?? '',
					'lastStatus'  => $settings['last_cron_status'] ?? '',
					'lastRetryRun'=> $settings['last_retry_run'] ?? '',
				),
				'cache' => array(
					'lastInventorySync' => $settings['last_full_sync'] ?? '',
					'freshestInventory' => $inventory_freshness ?: '',
					'daysBack'          => (int) $settings['sync_days_back'],
					'daysForward'       => (int) $settings['sync_days_forward'],
				),
				'retries' => array(
					'pending' => $pending_retry_count,
					'failed'  => $failed_retry_count,
				),
				'errors' => array(
					'lastSyncError'  => $last_sync_error,
					'lastOrderError' => $last_order_error,
				),
			)
		);
	}

	public function rest_get_settings() {
		$settings = $this->settings();
		foreach ( array( 'production', 'staging' ) as $mode ) {
			foreach ( array( 'inventory', 'reservation' ) as $purpose ) {
				$key = $mode . '_' . $purpose . '_access_token';
				$settings[ 'has_' . $key ] = ! empty( $settings[ $key ] );
				$settings[ $key ] = '';
			}
		}
		$settings['has_access_token'] = ! empty( $settings['access_token'] );
		$settings['access_token'] = '';
		$settings['has_webhook_hmac_secret'] = ! empty( $settings['webhook_hmac_secret'] );
		$settings['webhook_url'] = add_query_arg( 'secret', rawurlencode( $settings['webhook_secret'] ), rest_url( 'alawa/v1/hostplatform-webhook' ) );

		return rest_ensure_response( $settings );
	}

	public function rest_save_settings( WP_REST_Request $request ) {
		$current = $this->settings();
		$data = $request->get_json_params();
		$data = is_array( $data ) ? $data : array();

		$current['enabled'] = $this->rest_bool( $data['enabled'] ?? false ) ? 'yes' : 'no';
		$current['active_mode'] = isset( $data['active_mode'] ) && in_array( $data['active_mode'], array( 'staging', 'production' ), true ) ? $data['active_mode'] : $current['active_mode'];
		foreach ( array( 'production', 'staging' ) as $mode ) {
			foreach ( array( 'inventory', 'reservation' ) as $purpose ) {
				$prefix = $mode . '_' . $purpose . '_';
				$current[ $prefix . 'base_url' ] = isset( $data[ $prefix . 'base_url' ] ) ? esc_url_raw( trim( $data[ $prefix . 'base_url' ] ) ) : $current[ $prefix . 'base_url' ];
				$current[ $prefix . 'api_namespace' ] = isset( $data[ $prefix . 'api_namespace' ] ) ? sanitize_text_field( trim( $data[ $prefix . 'api_namespace' ] ) ) : $current[ $prefix . 'api_namespace' ];
				$current[ $prefix . 'auth_mode' ] = isset( $data[ $prefix . 'auth_mode' ] ) && in_array( $data[ $prefix . 'auth_mode' ], array( 'access_token', 'bearer', 'jwt' ), true ) ? $data[ $prefix . 'auth_mode' ] : $current[ $prefix . 'auth_mode' ];
				$current[ $prefix . 'property_id' ] = isset( $data[ $prefix . 'property_id' ] ) ? sanitize_text_field( trim( $data[ $prefix . 'property_id' ] ) ) : $current[ $prefix . 'property_id' ];
				if ( array_key_exists( $prefix . 'access_token', $data ) && '' !== (string) $data[ $prefix . 'access_token' ] ) {
					$current[ $prefix . 'access_token' ] = sanitize_text_field( trim( $data[ $prefix . 'access_token' ] ) );
				}
			}
		}
		$active_prefix = $current['active_mode'] . '_inventory_';
		$current['base_url'] = $current[ $active_prefix . 'base_url' ] ?? '';
		$current['api_namespace'] = $current[ $active_prefix . 'api_namespace' ] ?? '/external/v1';
		$current['auth_mode'] = $current[ $active_prefix . 'auth_mode' ] ?? 'access_token';
		$current['property_id'] = $current[ $active_prefix . 'property_id' ] ?? '';
		$current['webhook_secret'] = isset( $data['webhook_secret'] ) ? sanitize_text_field( trim( $data['webhook_secret'] ) ) : $current['webhook_secret'];
		$current['webhook_hmac_secret'] = isset( $data['webhook_hmac_secret' ] ) && '' !== (string) $data['webhook_hmac_secret'] ? sanitize_text_field( trim( $data['webhook_hmac_secret'] ) ) : $current['webhook_hmac_secret'];
		$current['default_source'] = isset( $data['default_source'] ) ? sanitize_key( $data['default_source'] ) : $current['default_source'];
		$current['sync_days_forward'] = max( 1, absint( $data['sync_days_forward'] ?? $current['sync_days_forward'] ) );
		$current['sync_days_back'] = max( 0, absint( $data['sync_days_back'] ?? $current['sync_days_back'] ) );
		$current['cron_enabled'] = $this->rest_bool( $data['cron_enabled'] ?? false ) ? 'yes' : 'no';
		$current['cron_schedule'] = in_array( $data['cron_schedule'] ?? '', array( 'alawa_hps_5min', 'alawa_hps_15min', 'hourly', 'twicedaily', 'daily' ), true ) ? $data['cron_schedule'] : $current['cron_schedule'];
		$current['checkout_live_check'] = $this->rest_bool( $data['checkout_live_check'] ?? false ) ? 'yes' : 'no';
		$current['cache_fallback'] = $this->rest_bool( $data['cache_fallback'] ?? false ) ? 'yes' : 'no';
		$current['create_guest_email'] = $this->rest_bool( $data['create_guest_email'] ?? false ) ? 'yes' : 'no';
		$current['log_retention_days'] = max( 1, absint( $data['log_retention_days'] ?? $current['log_retention_days'] ) );

		$current['access_token'] = $current[ $active_prefix . 'access_token' ] ?? '';

		$this->update_settings( $current );
		wp_clear_scheduled_hook( self::CRON_HOOK );
		if ( 'yes' === $current['cron_enabled'] ) {
			wp_schedule_event( time() + 60, $current['cron_schedule'], self::CRON_HOOK );
		}

		return $this->rest_get_settings();
	}

	private function rest_bool( $value ) {
		return true === $value || 1 === $value || '1' === $value || 'yes' === $value || 'true' === $value || 'on' === $value;
	}

	public function rest_get_mapping() {
		$items = array();

		foreach ( $this->rental_products() as $product ) {
			$product_id = (int) $product->ID;
			$mapping = $this->product_mapping( $product_id );
			$push_enabled = get_post_meta( $product_id, self::META_PUSH_ENABLED, true );

			$items[] = array(
				'id'           => $product_id,
				'title'        => get_the_title( $product_id ),
				'editUrl'      => get_edit_post_link( $product_id, 'raw' ),
				'listing_type' => $mapping['listing_type'],
				'room_id'      => $mapping['room_id'],
				'unit_id'      => $mapping['unit_id'],
				'unit_pool'    => $mapping['unit_pool_text'],
				'unit_count'   => count( $mapping['unit_pool'] ),
				'push_enabled' => '' === $push_enabled ? 'yes' : $push_enabled,
				'last_available' => (string) get_post_meta( $product_id, '_alawa_hps_last_available', true ),
				'last_sync'      => (string) get_post_meta( $product_id, '_alawa_hps_last_stock_sync', true ),
			);
		}

		return rest_ensure_response( array( 'items' => $items ) );
	}

	public function rest_save_mapping( WP_REST_Request $request ) {
		$data = $request->get_json_params();
		$items = isset( $data['items'] ) && is_array( $data['items'] ) ? $data['items'] : array();

		foreach ( $items as $item ) {
			$product_id = absint( $item['id'] ?? 0 );
			if ( ! $product_id ) {
				continue;
			}

			$listing_type = isset( $item['listing_type'] ) && in_array( $item['listing_type'], array( 'room', 'unit', 'unit_pool' ), true ) ? $item['listing_type'] : 'room';
			update_post_meta( $product_id, self::META_LISTING_TYPE, $listing_type );
			update_post_meta( $product_id, self::META_ROOM_ID, sanitize_text_field( trim( $item['room_id'] ?? '' ) ) );
			update_post_meta( $product_id, self::META_UNIT_ID, sanitize_text_field( trim( $item['unit_id'] ?? '' ) ) );
			update_post_meta( $product_id, self::META_UNIT_POOL, $this->normalize_unit_pool_text( $item['unit_pool'] ?? '' ) );
			update_post_meta( $product_id, self::META_PUSH_ENABLED, ! empty( $item['push_enabled'] ) && 'no' !== $item['push_enabled'] ? 'yes' : 'no' );
		}

		return $this->rest_get_mapping();
	}

	public function rest_inventory() {
		global $wpdb;

		$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}alawa_hps_inventory ORDER BY inventory_date DESC, product_id ASC LIMIT 500", ARRAY_A );
		foreach ( $rows as &$row ) {
			$row['product_title'] = get_the_title( (int) $row['product_id'] );
			$row['edit_url'] = get_edit_post_link( (int) $row['product_id'], 'raw' );
		}

		return rest_ensure_response( array( 'items' => $rows ) );
	}

	public function rest_reconciliation() {
		if ( ! function_exists( 'wc_get_orders' ) ) {
			return rest_ensure_response( array( 'items' => array() ) );
		}

		$orders = wc_get_orders(
			array(
				'limit'   => 50,
				'orderby' => 'date',
				'order'   => 'DESC',
				'status'  => array_keys( wc_get_order_statuses() ),
			)
		);

		$items = array();
		foreach ( $orders as $order ) {
			if ( ! $order instanceof WC_Order ) {
				continue;
			}

			foreach ( $order->get_items( 'line_item' ) as $item ) {
				$product_id = $item->get_product_id();
				if ( ! $product_id || ! $this->is_mapped_product( $product_id ) ) {
					continue;
				}

				$mapping = $this->product_mapping( $product_id );
				$retry_row = $this->latest_retry_row_for_item( $order->get_id(), $item->get_id() );
				$sync_state = $this->reconciliation_sync_state( $order, $item, $retry_row );
				$items[] = array(
					'order_id'          => $order->get_id(),
					'order_number'      => $order->get_order_number(),
					'order_status'      => $order->get_status(),
					'order_date'        => $order->get_date_created() ? $order->get_date_created()->date_i18n( 'Y-m-d H:i:s' ) : '',
					'item_id'           => $item->get_id(),
					'product_id'        => $product_id,
					'product_title'     => get_the_title( $product_id ),
					'check_in'          => $this->date_only( $item->get_meta( 'ovabrw_pickup_date_real' ) ?: $item->get_meta( 'ovabrw_pickup_date' ) ),
					'check_out'         => $this->date_only( $item->get_meta( 'ovabrw_pickoff_date_real' ) ?: $item->get_meta( 'ovabrw_pickoff_date' ) ),
					'quantity'          => (int) ( $item->get_meta( 'ovabrw_number_vehicle' ) ?: $item->get_quantity() ),
					'hostplatform_code' => (string) wc_get_order_item_meta( $item->get_id(), '_alawa_hps_reservation_code', true ),
					'sync_state'        => $sync_state['key'],
					'sync_label'        => $sync_state['label'],
					'sync_detail'       => $sync_state['detail'],
					'push_enabled'      => 'no' === get_post_meta( $product_id, self::META_PUSH_ENABLED, true ) ? 'no' : 'yes',
					'listing_type'      => $mapping['listing_type'],
					'listing_id'        => $mapping['listing_id'],
					'retry_status'      => $retry_row['status'] ?? '',
					'retry_attempts'    => isset( $retry_row['attempts'] ) ? (int) $retry_row['attempts'] : 0,
					'retry_next'        => $retry_row['next_retry_at'] ?? '',
					'retry_error'       => $retry_row['last_error'] ?? '',
				);
			}
		}

		return rest_ensure_response( array( 'items' => $items ) );
	}

	private function reconciliation_sync_state( WC_Order $order, WC_Order_Item_Product $item, $retry_row ) {
		$code = (string) wc_get_order_item_meta( $item->get_id(), '_alawa_hps_reservation_code', true );
		$status = $order->get_status();

		if ( $code || 'yes' === $order->get_meta( self::ORDER_META_SYNCED ) ) {
			return array(
				'key'    => 'synced',
				'label'  => 'Synced',
				'detail' => $code ? 'Reservation code stored.' : 'Order marked synced.',
			);
		}

		if ( is_array( $retry_row ) && ! empty( $retry_row['status'] ) && in_array( $retry_row['status'], array( 'pending', 'retrying', 'failed' ), true ) ) {
			return array(
				'key'    => 'retry',
				'label'  => 'Retry queue',
				'detail' => ! empty( $retry_row['last_error'] ) ? $retry_row['last_error'] : 'Waiting for retry queue.',
			);
		}

		if ( in_array( $status, array( 'processing', 'completed' ), true ) ) {
			return array(
				'key'    => 'ready',
				'label'  => 'Ready to push',
				'detail' => 'Eligible order status but no reservation code recorded yet.',
			);
		}

		if ( in_array( $status, array( 'failed', 'cancelled', 'refunded' ), true ) ) {
			return array(
				'key'    => 'blocked',
				'label'  => 'Not eligible',
				'detail' => 'Order status does not trigger HostPlatform push.',
			);
		}

		return array(
			'key'    => 'waiting',
			'label'  => 'Waiting',
			'detail' => 'Push begins when the order reaches processing/completed.',
		);
	}

	public function rest_retries() {
		global $wpdb;

		$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}alawa_hps_retry_queue ORDER BY updated_at DESC, id DESC LIMIT 200", ARRAY_A );
		foreach ( $rows as &$row ) {
			$row['product_title'] = $row['product_id'] ? get_the_title( (int) $row['product_id'] ) : '';
			$row['order_url'] = $row['order_id'] ? admin_url( 'post.php?post=' . (int) $row['order_id'] . '&action=edit' ) : '';
		}

		return rest_ensure_response( array( 'items' => $rows ) );
	}

	public function rest_process_retries() {
		$count = $this->process_retry_queue( true );
		if ( is_wp_error( $count ) ) {
			return $count;
		}

		return rest_ensure_response( array( 'ok' => true, 'processed' => (int) $count ) );
	}

	public function rest_retry_single( WP_REST_Request $request ) {
		$result = $this->process_retry_queue_item( absint( $request['id'] ) );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return rest_ensure_response( array( 'ok' => true, 'item' => $result ) );
	}

	public function rest_logs() {
		global $wpdb;

		$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}alawa_hps_logs ORDER BY id DESC LIMIT 300", ARRAY_A );
		return rest_ensure_response( array( 'items' => $rows ) );
	}

	public function rest_notifications() {
		global $wpdb;

		$webhook_rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}alawa_hps_webhooks ORDER BY id DESC LIMIT 40", ARRAY_A );
		$log_rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}alawa_hps_logs WHERE source IN ('webhook','sync','admin','retry','order') ORDER BY id DESC LIMIT 40", ARRAY_A );
		$items = array();

		foreach ( $webhook_rows as $row ) {
			$payload = json_decode( $row['payload'], true );
			$payload = is_array( $payload ) ? $payload : array();
			$items[] = array(
				'id'         => 'webhook-' . $row['id'],
				'type'       => 'webhook',
				'level'      => ! empty( $row['processed'] ) ? 'info' : 'warning',
				'created_at' => $row['received_at'],
				'title'      => $this->notification_title_from_webhook( $row, $payload ),
				'message'    => $this->notification_message_from_webhook( $row, $payload ),
				'meta'       => array(
					'event_key' => $row['event_key'],
					'processed' => (int) $row['processed'],
					'note'      => $row['note'],
				),
			);
		}

		foreach ( $log_rows as $row ) {
			$items[] = array(
				'id'         => 'log-' . $row['id'],
				'type'       => 'log',
				'level'      => $row['level'],
				'created_at' => $row['created_at'],
				'title'      => ucfirst( (string) $row['source'] ) . ' update',
				'message'    => $row['message'],
				'meta'       => array(
					'source'  => $row['source'],
					'context' => $row['context'],
				),
			);
		}

		usort(
			$items,
			static function( $a, $b ) {
				return strcmp( (string) $b['created_at'], (string) $a['created_at'] );
			}
		);

		$items = array_slice( $items, 0, 60 );

		return rest_ensure_response(
			array(
				'items'       => $items,
				'generatedAt' => current_time( 'mysql' ),
			)
		);
	}

	public function rest_test_connection() {
		$result = $this->api_get_rooms();
		if ( is_wp_error( $result ) ) {
			$this->log( 'error', 'admin', 'Connection test failed.', array( 'error' => $result->get_error_message(), 'details' => $result->get_error_data() ) );
			return $result;
		}

		$count = isset( $result['rooms'] ) && is_array( $result['rooms'] ) ? count( $result['rooms'] ) : ( is_array( $result ) ? count( $result ) : 0 );
		$this->log( 'info', 'admin', 'Connection test succeeded.', array( 'items' => $count ) );

		return rest_ensure_response(
			array(
				'ok'      => true,
				'message' => sprintf( __( 'Connection successful. %d item(s) returned.', 'alawa-hostplatform-sync' ), $count ),
				'items'   => $count,
				'result'  => $result,
			)
		);
	}

	public function rest_run_sync() {
		$result = $this->sync_all_mapped_products( true );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return rest_ensure_response( array( 'ok' => true, 'products' => (int) $result ) );
	}

	public function rest_clear_logs() {
		$this->clear_logs();
		return rest_ensure_response( array( 'ok' => true ) );
	}

	public function handle_webhook( WP_REST_Request $request ) {
		$settings = $this->settings();
		$body = $request->get_body();
		$payload = json_decode( $body, true );
		$payload = is_array( $payload ) ? $payload : array();

		if ( 'yes' !== $settings['enabled'] ) {
			return new WP_REST_Response( array( 'ok' => false, 'message' => 'Integration disabled.' ), 423 );
		}

		$secret = $request->get_param( 'secret' );
		if ( empty( $settings['webhook_secret'] ) || ! hash_equals( (string) $settings['webhook_secret'], (string) $secret ) ) {
			$this->log( 'warning', 'webhook', 'Webhook rejected: invalid URL secret.' );
			return new WP_REST_Response( array( 'ok' => false, 'message' => 'Invalid secret.' ), 403 );
		}

		if ( ! $this->verify_webhook_signature( $request, $body ) ) {
			$this->log( 'warning', 'webhook', 'Webhook rejected: invalid signature.' );
			return new WP_REST_Response( array( 'ok' => false, 'message' => 'Invalid signature.' ), 403 );
		}

		$event_key = $this->webhook_event_key( $payload );
		$webhook_id = $this->store_webhook( $event_key, $request->get_header( 'x-hostplatform-signature' ), $body );
		$this->log( 'info', 'webhook', 'Webhook received.', array( 'event_key' => $event_key, 'payload' => $payload ) );

		$handled = $this->process_webhook_payload( $payload );
		$this->update_webhook_record( $webhook_id, 1, sprintf( 'Handled %d reservation sync target(s).', (int) $handled ) );
		$this->log( 'info', 'webhook', 'Webhook processed.', array( 'event_key' => $event_key, 'handled' => $handled ) );

		return new WP_REST_Response(
			array(
				'ok'      => true,
				'handled' => $handled,
			),
			200
		);
	}

	private function verify_webhook_signature( WP_REST_Request $request, $body ) {
		$settings = $this->settings();
		if ( empty( $settings['webhook_hmac_secret'] ) ) {
			return true;
		}

		$signature = $request->get_header( 'x-hostplatform-signature' );
		if ( ! $signature ) {
			$signature = $request->get_header( 'x-signature' );
		}
		if ( ! $signature ) {
			return false;
		}

		$expected = hash_hmac( 'sha256', $body, $settings['webhook_hmac_secret'] );
		$signature = preg_replace( '/^sha256=/i', '', trim( $signature ) );

		return hash_equals( $expected, $signature );
	}

	private function webhook_event_key( array $payload ) {
		$candidates = array(
			$payload['event'] ?? '',
			$payload['type'] ?? '',
			$payload['code'] ?? '',
			$payload['reservation']['code'] ?? '',
			$payload['data']['code'] ?? '',
			$payload['source_id'] ?? '',
		);

		$key = implode( ':', array_filter( array_map( 'strval', $candidates ) ) );
		return $key ? substr( $key, 0, 191 ) : null;
	}

	private function store_webhook( $event_key, $signature, $payload ) {
		global $wpdb;

		$wpdb->insert(
			$wpdb->prefix . 'alawa_hps_webhooks',
			array(
				'received_at' => current_time( 'mysql' ),
				'event_key'   => $event_key,
				'signature'   => $signature ? substr( $signature, 0, 191 ) : null,
				'payload'     => $payload,
				'processed'   => 0,
				'note'        => null,
			),
			array( '%s', '%s', '%s', '%s', '%d', '%s' )
		);

		return (int) $wpdb->insert_id;
	}

	private function update_webhook_record( $webhook_id, $processed, $note ) {
		global $wpdb;

		if ( ! $webhook_id ) {
			return;
		}

		$wpdb->update(
			$wpdb->prefix . 'alawa_hps_webhooks',
			array(
				'processed' => (int) $processed,
				'note'      => $note,
			),
			array( 'id' => (int) $webhook_id ),
			array( '%d', '%s' ),
			array( '%d' )
		);
	}

	private function notification_title_from_webhook( array $row, array $payload ) {
		$reservation = $this->extract_reservations( $payload );
		$reservation = ! empty( $reservation[0] ) && is_array( $reservation[0] ) ? $reservation[0] : array();
		$event = $payload['event'] ?? $payload['type'] ?? $row['event_key'] ?? 'Reservation event';
		$guest = $reservation['guest_name'] ?? $reservation['name'] ?? $reservation['guest']['name'] ?? '';
		$room = $reservation['room_type'] ?? $reservation['roomType'] ?? $reservation['room_name'] ?? '';

		$title = trim( implode( ' - ', array_filter( array( $event, $room ?: '', $guest ?: '' ) ) ) );
		return $title ?: 'HostPlatform reservation update';
	}

	private function notification_message_from_webhook( array $row, array $payload ) {
		$reservation = $this->extract_reservations( $payload );
		$reservation = ! empty( $reservation[0] ) && is_array( $reservation[0] ) ? $reservation[0] : array();
		$check_in = $reservation['check_in_date'] ?? $reservation['checkin_date'] ?? $reservation['start_date'] ?? '';
		$check_out = $reservation['check_out_date'] ?? $reservation['checkout_date'] ?? $reservation['end_date'] ?? '';
		$code = $reservation['code'] ?? $reservation['reservation_code'] ?? '';
		$parts = array();

		if ( $code ) {
			$parts[] = 'Code ' . $code;
		}
		if ( $check_in || $check_out ) {
			$parts[] = trim( 'Stay ' . $this->date_only( $check_in ) . ' to ' . $this->date_only( $check_out ) );
		}
		if ( ! empty( $row['note'] ) ) {
			$parts[] = $row['note'];
		}

		return $parts ? implode( '. ', $parts ) . '.' : 'Webhook received from HostPlatform.';
	}

	private function process_webhook_payload( array $payload ) {
		$reservations = $this->extract_reservations( $payload );
		$count = 0;

		foreach ( $reservations as $reservation ) {
			$product_id = $this->find_product_for_reservation( $reservation );
			$start = $reservation['check_in_date'] ?? $reservation['checkin_date'] ?? $reservation['start_date'] ?? '';
			$end = $reservation['check_out_date'] ?? $reservation['checkout_date'] ?? $reservation['end_date'] ?? '';

			if ( $product_id && $start && $end ) {
				$this->sync_product_inventory( $product_id, $this->date_only( $start ), $this->date_only( $end ) );
				$count++;
			}
		}

		if ( 0 === $count ) {
			$this->sync_all_mapped_products( false, 30 );
		}

		return $count;
	}

	private function extract_reservations( array $payload ) {
		if ( isset( $payload['reservations'] ) && is_array( $payload['reservations'] ) ) {
			return $payload['reservations'];
		}
		if ( isset( $payload['reservation'] ) && is_array( $payload['reservation'] ) ) {
			return array( $payload['reservation'] );
		}
		if ( isset( $payload['data']['reservations'] ) && is_array( $payload['data']['reservations'] ) ) {
			return $payload['data']['reservations'];
		}
		if ( isset( $payload['data'] ) && is_array( $payload['data'] ) ) {
			return array( $payload['data'] );
		}

		return array( $payload );
	}

	private function find_product_for_reservation( array $reservation ) {
		$room_id = $reservation['room_id'] ?? $reservation['room'] ?? '';
		$unit_id = $reservation['unit_id'] ?? $reservation['unit'] ?? '';
		$room_type = $reservation['room_type'] ?? $reservation['roomType'] ?? '';

		if ( $room_id ) {
			$product = $this->product_by_meta( self::META_ROOM_ID, $room_id );
			if ( $product ) {
				return $product;
			}
		}

		if ( $unit_id ) {
			$product = $this->product_by_meta( self::META_UNIT_ID, $unit_id );
			if ( $product ) {
				return $product;
			}
		}

		if ( $room_type ) {
			foreach ( $this->rental_products() as $product ) {
				if ( false !== stripos( get_the_title( $product->ID ), $room_type ) ) {
					return (int) $product->ID;
				}
			}
		}

		return 0;
	}

	private function product_by_meta( $key, $value ) {
		$query = new WP_Query(
			array(
				'post_type'      => 'product',
				'post_status'    => array( 'publish', 'private', 'draft' ),
				'fields'         => 'ids',
				'posts_per_page' => 1,
				'meta_key'       => $key,
				'meta_value'     => $value,
			)
		);

		return ! empty( $query->posts ) ? (int) $query->posts[0] : 0;
	}

	private function rental_products() {
		$query = new WP_Query(
			array(
				'post_type'      => 'product',
				'post_status'    => array( 'publish', 'private', 'draft' ),
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'tax_query'      => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => 'ovabrw_car_rental',
					),
				),
			)
		);

		return $query->posts;
	}

	private function mapped_products() {
		$products = array();

		foreach ( $this->rental_products() as $product ) {
			$mapping = $this->product_mapping( $product->ID );
			if ( $mapping['listing_id'] ) {
				$products[] = $product;
			}
		}

		return $products;
	}

	private function product_mapping( $product_id ) {
		$listing_type = get_post_meta( $product_id, self::META_LISTING_TYPE, true ) ?: 'room';
		$room_id = get_post_meta( $product_id, self::META_ROOM_ID, true );
		$unit_id = get_post_meta( $product_id, self::META_UNIT_ID, true );
		$unit_pool_text = get_post_meta( $product_id, self::META_UNIT_POOL, true );
		$unit_pool = $this->parse_unit_pool( $unit_pool_text );

		return array(
			'listing_type'   => in_array( $listing_type, array( 'room', 'unit', 'unit_pool' ), true ) ? $listing_type : 'room',
			'listing_id'     => 'unit_pool' === $listing_type ? ( $unit_pool ? $unit_pool[0] : '' ) : ( 'unit' === $listing_type ? $unit_id : $room_id ),
			'room_id'        => $room_id,
			'unit_id'        => $unit_id,
			'unit_pool'      => $unit_pool,
			'unit_pool_text' => implode( "\n", $unit_pool ),
		);
	}

	public function cron_sync() {
		$settings = $this->settings();
		if ( 'yes' !== $settings['enabled'] || 'yes' !== $settings['cron_enabled'] ) {
			return;
		}

		$sync_result = $this->sync_all_mapped_products( false );
		$retry_result = $this->process_retry_queue( false );
		$settings = $this->settings();
		$settings['last_cron_run'] = current_time( 'mysql' );
		$settings['last_cron_status'] = ( is_wp_error( $sync_result ) || is_wp_error( $retry_result ) ) ? 'error' : 'ok';
		if ( ! is_wp_error( $retry_result ) ) {
			$settings['last_retry_run'] = current_time( 'mysql' );
		}
		$this->update_settings( $settings );
		$this->prune_logs();
	}

	private function sync_all_mapped_products( $manual = false, $days_forward = null ) {
		$settings = $this->settings();

		if ( 'yes' !== $settings['enabled'] ) {
			return new WP_Error( 'alawa_hps_disabled', __( 'Integration is disabled.', 'alawa-hostplatform-sync' ) );
		}

		$products = $this->mapped_products();
		$start = gmdate( 'Y-m-d', strtotime( '-' . absint( $settings['sync_days_back'] ) . ' days', current_time( 'timestamp' ) ) );
		$forward = null === $days_forward ? absint( $settings['sync_days_forward'] ) : absint( $days_forward );
		$end = gmdate( 'Y-m-d', strtotime( '+' . $forward . ' days', current_time( 'timestamp' ) ) );
		$count = 0;

		foreach ( $products as $product ) {
			$result = $this->sync_product_inventory( $product->ID, $start, $end );
			if ( is_wp_error( $result ) ) {
				$this->log( 'error', 'sync', 'Product inventory sync failed.', array( 'product_id' => $product->ID, 'error' => $result->get_error_message() ) );
				if ( $manual ) {
					return $result;
				}
				continue;
			}
			$count++;
		}

		$settings['last_full_sync'] = current_time( 'mysql' );
		$this->update_settings( $settings );
		$this->log( 'info', 'sync', 'Inventory sync complete.', array( 'products' => $count, 'start' => $start, 'end' => $end ) );

		return $count;
	}

	private function sync_product_inventory( $product_id, $start_date, $end_date ) {
		$mapping = $this->product_mapping( $product_id );
		if ( empty( $mapping['listing_id'] ) ) {
			return new WP_Error( 'alawa_hps_missing_mapping', sprintf( 'Product %d is not mapped to HostPlatform.', $product_id ) );
		}

		if ( $this->is_live_app_mode() ) {
			$room_type_id = $this->resolve_live_room_type_id_from_mapping( $mapping );
			if ( is_wp_error( $room_type_id ) ) {
				return $room_type_id;
			}

			$result = $this->api_get_inventory( 'room', $room_type_id, $start_date, $end_date );
			if ( is_wp_error( $result ) ) {
				return $result;
			}

			$inventories = isset( $result['inventories'] ) && is_array( $result['inventories'] ) ? $result['inventories'] : array();
			$this->store_inventory_rows( $product_id, 'room', $room_type_id, $inventories );
			$this->sync_ova_stock_snapshot( $product_id, $start_date, $end_date );

			return count( $inventories );
		}

		if ( 'unit_pool' === $mapping['listing_type'] ) {
			$total_rows = 0;

			foreach ( $mapping['unit_pool'] as $unit_id ) {
				$result = $this->api_get_inventory( 'unit', $unit_id, $start_date, $end_date );
				if ( is_wp_error( $result ) ) {
					return $result;
				}

				$inventories = isset( $result['inventories'] ) && is_array( $result['inventories'] ) ? $result['inventories'] : array();
				$this->store_inventory_rows( $product_id, 'unit', $unit_id, $inventories );
				$total_rows += count( $inventories );
			}

			$this->store_stock_snapshot( $product_id, $start_date, $end_date );
			return $total_rows;
		}

		$result = $this->api_get_inventory( $mapping['listing_type'], $mapping['listing_id'], $start_date, $end_date );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$inventories = isset( $result['inventories'] ) && is_array( $result['inventories'] ) ? $result['inventories'] : array();
		$this->store_inventory_rows( $product_id, $mapping['listing_type'], $mapping['listing_id'], $inventories );
		$this->sync_ova_stock_snapshot( $product_id, $start_date, $end_date );

		return count( $inventories );
	}

	private function store_inventory_rows( $product_id, $listing_type, $listing_id, array $rows ) {
		global $wpdb;

		$table = $wpdb->prefix . 'alawa_hps_inventory';
		foreach ( $rows as $row ) {
			$date = $row['date'] ?? '';
			if ( ! $date ) {
				continue;
			}

			$available = isset( $row['inventory'] ) ? (int) $row['inventory'] : ( ( isset( $row['is_available'] ) && $row['is_available'] ) ? 1 : 0 );
			$occupied = isset( $row['occupiedInventory'] ) ? (int) $row['occupiedInventory'] : 0;
			$booked = isset( $row['breakdown']['bookedInventory'] ) ? (int) $row['breakdown']['bookedInventory'] : 0;
			$blocked = isset( $row['breakdown']['blockInventory'] ) ? (int) $row['breakdown']['blockInventory'] : 0;

			$wpdb->replace(
				$table,
				array(
					'product_id'     => (int) $product_id,
					'listing_type'   => $listing_type,
					'listing_id'     => $listing_id,
					'inventory_date' => $this->date_only( $date ),
					'available'      => $available,
					'occupied'       => $occupied,
					'booked'         => $booked,
					'blocked'        => $blocked,
					'raw'            => wp_json_encode( $row ),
					'synced_at'      => current_time( 'mysql' ),
				),
				array( '%d', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s' )
			);
		}
	}

	private function sync_ova_stock_snapshot( $product_id, $start_date, $end_date ) {
		$available = $this->cache_available_for_range( $product_id, $start_date, $end_date );
		if ( null === $available ) {
			return;
		}

		update_post_meta( $product_id, '_alawa_hps_last_available', $available );
		update_post_meta( $product_id, '_alawa_hps_last_stock_sync', current_time( 'mysql' ) );

		if ( 'store' === get_post_meta( $product_id, 'ovabrw_manage_store', true ) ) {
			update_post_meta( $product_id, 'ovabrw_car_count', max( 0, (int) $available ) );
		}
	}

	public function validate_add_to_cart( $passed, $product_id, $quantity, $variation_id = 0, $variations = array() ) {
		if ( ! $passed || ! $this->is_mapped_product( $product_id ) ) {
			return $passed;
		}

		$data = wp_unslash( $_POST );
		$start = $data['ovabrw_pickup_date'] ?? $data['pickup_date'] ?? '';
		$end = $data['ovabrw_pickoff_date'] ?? $data['dropoff_date'] ?? '';
		$rooms = isset( $data['ovabrw_number_vehicle'] ) ? absint( $data['ovabrw_number_vehicle'] ) : absint( $quantity );

		if ( ! $start || ! $end ) {
			return $passed;
		}

		$result = $this->is_available( $product_id, $start, $end, max( 1, $rooms ) );
		if ( is_wp_error( $result ) ) {
			wc_add_notice( $result->get_error_message(), 'error' );
			return false;
		}

		if ( ! $result ) {
			wc_add_notice( __( 'This room is no longer available for the selected dates.', 'alawa-hostplatform-sync' ), 'error' );
			return false;
		}

		return $passed;
	}

	public function validate_cart() {
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return;
		}

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$product_id = isset( $cart_item['product_id'] ) ? (int) $cart_item['product_id'] : 0;
			if ( ! $product_id || ! $this->is_mapped_product( $product_id ) ) {
				continue;
			}

			$start = $cart_item['ovabrw_pickup_date'] ?? $cart_item['ovabrw_pickup_date_real'] ?? '';
			$end = $cart_item['ovabrw_pickoff_date'] ?? $cart_item['ovabrw_pickoff_date_real'] ?? '';
			$rooms = isset( $cart_item['ovabrw_number_vehicle'] ) ? absint( $cart_item['ovabrw_number_vehicle'] ) : 1;
			if ( ! $start || ! $end ) {
				continue;
			}

			$result = $this->is_available( $product_id, $start, $end, max( 1, $rooms ) );
			if ( is_wp_error( $result ) ) {
				wc_add_notice( $result->get_error_message(), 'error' );
			} elseif ( ! $result ) {
				wc_add_notice( sprintf( __( '%s is no longer available for the selected dates.', 'alawa-hostplatform-sync' ), get_the_title( $product_id ) ), 'error' );
			}
		}
	}

	private function is_available( $product_id, $start, $end, $requested = 1 ) {
		$settings = $this->settings();
		$start_date = $this->date_only( $start );
		$end_date = $this->date_only( $end );

		if ( 'yes' === $settings['checkout_live_check'] ) {
			$result = $this->sync_product_inventory( $product_id, $start_date, $end_date );
			if ( is_wp_error( $result ) ) {
				$this->log( 'warning', 'checkout', 'Live inventory check failed.', array( 'product_id' => $product_id, 'error' => $result->get_error_message() ) );
				if ( 'yes' !== $settings['cache_fallback'] ) {
					return $result;
				}
			}
		}

		$available = $this->cache_available_for_range( $product_id, $start_date, $end_date );
		if ( null === $available ) {
			return new WP_Error( 'alawa_hps_no_inventory', __( 'Availability could not be confirmed. Please try again in a moment.', 'alawa-hostplatform-sync' ) );
		}

		return (int) $available >= (int) $requested;
	}

	private function cache_available_for_range( $product_id, $start_date, $end_date ) {
		global $wpdb;

		$start_date = $this->date_only( $start_date );
		$end_date = $this->date_only( $end_date );
		if ( ! $start_date || ! $end_date ) {
			return null;
		}

		$checkout_exclusive = strtotime( $end_date ) > strtotime( $start_date ) ? gmdate( 'Y-m-d', strtotime( $end_date . ' -1 day' ) ) : $end_date;
		$table = $wpdb->prefix . 'alawa_hps_inventory';
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT available FROM {$table} WHERE product_id = %d AND inventory_date BETWEEN %s AND %s ORDER BY inventory_date ASC",
				$product_id,
				$start_date,
				$checkout_exclusive
			)
		);

		if ( empty( $rows ) ) {
			return null;
		}

		$values = wp_list_pluck( $rows, 'available' );
		return min( array_map( 'intval', $values ) );
	}

	public function push_order_to_hostplatform( $order_id ) {
		$settings = $this->settings();
		if ( 'yes' !== $settings['enabled'] ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		if ( 'yes' === $order->get_meta( self::ORDER_META_SYNCED ) ) {
			return;
		}

		$reservations = array();
		foreach ( $order->get_items( 'line_item' ) as $item_id => $item ) {
			$product_id = $item->get_product_id();
			if ( ! $product_id || ! $this->is_mapped_product( $product_id ) ) {
				continue;
			}
			if ( 'no' === get_post_meta( $product_id, self::META_PUSH_ENABLED, true ) ) {
				continue;
			}

			$payload = $this->build_reservation_payload( $order, $item );
			if ( is_wp_error( $payload ) ) {
				$this->log( 'error', 'order', 'Unable to build HostPlatform reservation payload.', array( 'order_id' => $order_id, 'item_id' => $item_id, 'error' => $payload->get_error_message() ) );
				continue;
			}

			$mapping = $this->product_mapping( $product_id );
			$response = $this->api_create_reservation( $mapping['listing_type'], $mapping['listing_id'], $payload );
			if ( is_wp_error( $response ) ) {
				$this->enqueue_retry( 'create_reservation', $order_id, $item_id, $product_id, $mapping['listing_type'], $mapping['listing_id'], $payload, $response->get_error_message() );
				$this->log( 'error', 'order', 'HostPlatform reservation creation failed.', array( 'order_id' => $order_id, 'item_id' => $item_id, 'error' => $response->get_error_message(), 'payload' => $payload ) );
				$order->add_order_note( 'HostPlatform sync failed: ' . $response->get_error_message() );
				continue;
			}

			$code = $this->extract_reservation_code( $response );
			wc_update_order_item_meta( $item_id, '_alawa_hps_reservation_code', $code );
			wc_update_order_item_meta( $item_id, '_alawa_hps_reservation_response', wp_json_encode( $response ) );
			$reservations[] = array(
				'item_id'    => $item_id,
				'product_id' => $product_id,
				'code'       => $code,
				'response'   => $response,
			);

			$this->log( 'info', 'order', 'HostPlatform reservation created.', array( 'order_id' => $order_id, 'item_id' => $item_id, 'code' => $code ) );
			$this->complete_retry_for_item( $order_id, $item_id, wp_json_encode( $response ) );
		}

		if ( $reservations ) {
			$order->update_meta_data( self::ORDER_META_SYNCED, 'yes' );
			$order->update_meta_data( self::ORDER_META_RESERVATIONS, $reservations );
			$order->add_order_note( 'HostPlatform reservation sync complete. Codes: ' . implode( ', ', array_filter( wp_list_pluck( $reservations, 'code' ) ) ) );
			$order->save();
		}
	}

	private function build_reservation_payload( WC_Order $order, WC_Order_Item_Product $item ) {
		$product_id = $item->get_product_id();
		$start = $item->get_meta( 'ovabrw_pickup_date_real' ) ?: $item->get_meta( 'ovabrw_pickup_date' );
		$end = $item->get_meta( 'ovabrw_pickoff_date_real' ) ?: $item->get_meta( 'ovabrw_pickoff_date' );

		if ( ! $start || ! $end ) {
			return new WP_Error( 'alawa_hps_missing_dates', 'Order item is missing OVA BRW booking dates.' );
		}

		$email = $order->get_billing_email();
		if ( ! $email && 'yes' === $this->settings()['create_guest_email'] ) {
			$email = 'guest+' . $order->get_id() . '@alawaresort.local';
		}

		$quantity = (int) ( $item->get_meta( 'ovabrw_number_vehicle' ) ?: $item->get_quantity() );
		$guest_count = max( 1, $quantity );
		$total = (float) $item->get_total();

		return array(
			'check_in_date'  => $this->date_only( $start ),
			'check_out_date' => $this->date_only( $end ),
			'remarks'        => 'WooCommerce order #' . $order->get_order_number(),
			'guest_details'  => array(
				'number_of_pax' => $guest_count,
				'first_name'    => $order->get_billing_first_name() ?: 'Guest',
				'last_name'     => $order->get_billing_last_name(),
				'contact_no'    => $order->get_billing_phone(),
				'email'         => $email,
			),
			'price_details'  => array(
				'room'         => $total,
				'platform_fee' => 0,
			),
			'source'         => $this->settings()['default_source'],
			'source_id'      => 'WC-' . $order->get_id() . '-' . $item->get_id(),
			'booking_status' => 'Confirmed',
			'status'         => 'Confirmed',
			'wordpress'      => array(
				'order_id'     => $order->get_id(),
				'order_number' => $order->get_order_number(),
				'item_id'      => $item->get_id(),
				'product_id'   => $product_id,
				'product_name' => $item->get_name(),
			),
		);
	}

	private function extract_reservation_code( $response ) {
		if ( isset( $response['code'] ) ) {
			return (string) $response['code'];
		}
		if ( isset( $response['reservation']['code'] ) ) {
			return (string) $response['reservation']['code'];
		}
		if ( isset( $response['reservations'][0]['code'] ) ) {
			return (string) $response['reservations'][0]['code'];
		}

		return '';
	}

	public function mark_order_cancelled( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		$this->log( 'info', 'order', 'WooCommerce order cancelled/refunded. HostPlatform cancellation should be reviewed.', array( 'order_id' => $order_id, 'reservations' => $order->get_meta( self::ORDER_META_RESERVATIONS ) ) );
		$order->add_order_note( 'HostPlatform sync: order was cancelled/refunded. Review matching HostPlatform reservation cancellation manually unless the API cancel endpoint is enabled.' );
	}

	private function enqueue_retry( $action, $order_id, $order_item_id, $product_id, $listing_type, $listing_id, array $payload, $error_message ) {
		global $wpdb;

		$table = $wpdb->prefix . 'alawa_hps_retry_queue';
		$existing_id = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$table} WHERE action = %s AND order_id = %d AND order_item_id = %d AND status IN ('pending','retrying','failed') ORDER BY id DESC LIMIT 1",
				$action,
				$order_id,
				$order_item_id
			)
		);

		$data = array(
			'action'        => $action,
			'status'        => 'pending',
			'order_id'      => (int) $order_id,
			'order_item_id' => (int) $order_item_id,
			'product_id'    => (int) $product_id,
			'listing_type'  => (string) $listing_type,
			'listing_id'    => (string) $listing_id,
			'payload'       => wp_json_encode( $payload ),
			'last_error'    => $error_message,
			'attempts'      => 0,
			'next_retry_at' => current_time( 'mysql' ),
			'updated_at'    => current_time( 'mysql' ),
		);

		if ( $existing_id ) {
			$wpdb->update(
				$table,
				$data,
				array( 'id' => $existing_id ),
				array( '%s', '%s', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s' ),
				array( '%d' )
			);
			return $existing_id;
		}

		$data['created_at'] = current_time( 'mysql' );
		$wpdb->insert(
			$table,
			$data,
			array( '%s', '%s', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s' )
		);

		return (int) $wpdb->insert_id;
	}

	private function complete_retry_for_item( $order_id, $order_item_id, $response = '' ) {
		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->prefix}alawa_hps_retry_queue SET status = 'completed', completed_at = %s, updated_at = %s, response = %s WHERE order_id = %d AND order_item_id = %d AND status IN ('pending','retrying','failed')",
				current_time( 'mysql' ),
				current_time( 'mysql' ),
				(string) $response,
				(int) $order_id,
				(int) $order_item_id
			)
		);
	}

	public function render_order_sync_status( $order ) {
		if ( ! $order instanceof WC_Order ) {
			return;
		}

		$reservations = $order->get_meta( self::ORDER_META_RESERVATIONS );
		echo '<p class="form-field form-field-wide"><strong>' . esc_html__( 'HostPlatform Sync:', 'alawa-hostplatform-sync' ) . '</strong> ';
		if ( empty( $reservations ) ) {
			echo esc_html__( 'No reservation pushed yet.', 'alawa-hostplatform-sync' );
		} else {
			$codes = array_filter( wp_list_pluck( (array) $reservations, 'code' ) );
			echo esc_html( implode( ', ', $codes ) );
		}
		echo '</p>';
	}

	private function latest_retry_row_for_item( $order_id, $order_item_id ) {
		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}alawa_hps_retry_queue WHERE order_id = %d AND order_item_id = %d ORDER BY id DESC LIMIT 1",
				(int) $order_id,
				(int) $order_item_id
			),
			ARRAY_A
		);
	}

	private function process_retry_queue( $manual = false, $limit = 20 ) {
		global $wpdb;

		$table = $wpdb->prefix . 'alawa_hps_retry_queue';
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE status IN ('pending','retrying','failed') AND next_retry_at <= %s ORDER BY next_retry_at ASC, id ASC LIMIT %d",
				current_time( 'mysql' ),
				max( 1, (int) $limit )
			),
			ARRAY_A
		);

		$processed = 0;
		foreach ( $rows as $row ) {
			$result = $this->process_retry_queue_item( (int) $row['id'] );
			if ( is_wp_error( $result ) && $manual ) {
				return $result;
			}
			if ( ! is_wp_error( $result ) ) {
				$processed++;
			}
		}

		return $processed;
	}

	private function process_retry_queue_item( $id ) {
		global $wpdb;

		$table = $wpdb->prefix . 'alawa_hps_retry_queue';
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d LIMIT 1", (int) $id ), ARRAY_A );
		if ( empty( $row ) ) {
			return new WP_Error( 'alawa_hps_retry_missing', __( 'Retry queue item not found.', 'alawa-hostplatform-sync' ) );
		}

		$payload = json_decode( (string) $row['payload'], true );
		$payload = is_array( $payload ) ? $payload : array();
		$attempts = (int) $row['attempts'] + 1;

		$wpdb->update(
			$table,
			array(
				'status'          => 'retrying',
				'attempts'        => $attempts,
				'last_attempt_at' => current_time( 'mysql' ),
				'updated_at'      => current_time( 'mysql' ),
			),
			array( 'id' => (int) $id ),
			array( '%s', '%d', '%s', '%s' ),
			array( '%d' )
		);

		if ( 'create_reservation' !== $row['action'] ) {
			return new WP_Error( 'alawa_hps_retry_unsupported', __( 'Unsupported retry action.', 'alawa-hostplatform-sync' ) );
		}

		$response = $this->api_create_reservation( $row['listing_type'], $row['listing_id'], $payload );
		if ( is_wp_error( $response ) ) {
			$next_retry = gmdate( 'Y-m-d H:i:s', current_time( 'timestamp', true ) + min( 6 * HOUR_IN_SECONDS, max( 300, (int) pow( 2, $attempts ) * 300 ) ) );
			$wpdb->update(
				$table,
				array(
					'status'        => 'failed',
					'last_error'    => $response->get_error_message(),
					'next_retry_at' => get_date_from_gmt( $next_retry, 'Y-m-d H:i:s' ),
					'updated_at'    => current_time( 'mysql' ),
				),
				array( 'id' => (int) $id ),
				array( '%s', '%s', '%s', '%s' ),
				array( '%d' )
			);
			$this->log( 'error', 'retry', 'Retry queue reservation push failed.', array( 'retry_id' => (int) $id, 'order_id' => (int) $row['order_id'], 'item_id' => (int) $row['order_item_id'], 'error' => $response->get_error_message() ) );
			return $response;
		}

		$code = $this->extract_reservation_code( $response );
		if ( function_exists( 'wc_update_order_item_meta' ) && ! empty( $row['order_item_id'] ) ) {
			wc_update_order_item_meta( (int) $row['order_item_id'], '_alawa_hps_reservation_code', $code );
			wc_update_order_item_meta( (int) $row['order_item_id'], '_alawa_hps_reservation_response', wp_json_encode( $response ) );
		}

		$order = wc_get_order( (int) $row['order_id'] );
		if ( $order ) {
			$order->update_meta_data( self::ORDER_META_SYNCED, 'yes' );
			$reservations = (array) $order->get_meta( self::ORDER_META_RESERVATIONS );
			$reservations[] = array(
				'item_id'    => (int) $row['order_item_id'],
				'product_id' => (int) $row['product_id'],
				'code'       => $code,
				'response'   => $response,
			);
			$order->update_meta_data( self::ORDER_META_RESERVATIONS, $reservations );
			$order->add_order_note( 'HostPlatform retry queue succeeded for item #' . (int) $row['order_item_id'] . ( $code ? ' with code ' . $code : '.' ) );
			$order->save();
		}

		$wpdb->update(
			$table,
			array(
				'status'       => 'completed',
				'completed_at' => current_time( 'mysql' ),
				'updated_at'   => current_time( 'mysql' ),
				'response'     => wp_json_encode( $response ),
				'last_error'   => '',
			),
			array( 'id' => (int) $id ),
			array( '%s', '%s', '%s', '%s', '%s' ),
			array( '%d' )
		);

		$this->log( 'info', 'retry', 'Retry queue reservation push succeeded.', array( 'retry_id' => (int) $id, 'order_id' => (int) $row['order_id'], 'item_id' => (int) $row['order_item_id'], 'code' => $code ) );
		return array(
			'id'       => (int) $id,
			'code'     => $code,
			'response' => $response,
		);
	}

	private function is_mapped_product( $product_id ) {
		$mapping = $this->product_mapping( $product_id );
		return ! empty( $mapping['listing_id'] );
	}

	private function api_get_rooms() {
		$profile = $this->profile_config( 'inventory' );
		if ( empty( $profile['property_id'] ) ) {
			return new WP_Error( 'alawa_hps_missing_property', sprintf( __( '%s property ID is missing. Open Settings and fill the active inventory profile.', 'alawa-hostplatform-sync' ), $this->profile_label( 'inventory' ) ) );
		}

		if ( $this->profile_is_live( 'inventory' ) ) {
			$units = $this->api_request_profile( 'inventory', 'GET', '/unit', array( 'propertyId' => $profile['property_id'] ) );
			if ( is_wp_error( $units ) ) {
				return $units;
			}

			return array(
				'rooms' => $this->derive_room_summaries_from_units( is_array( $units ) ? $units : array() ),
				'units' => is_array( $units ) ? $units : array(),
			);
		}

		return $this->api_request_profile( 'inventory', 'GET', '/room', array( 'property_id' => $profile['property_id'] ) );
	}

	private function api_get_inventory( $type, $listing_id, $start_date, $end_date ) {
		if ( $this->profile_is_live( 'inventory' ) ) {
			return $this->api_get_live_inventory( $listing_id, $start_date, $end_date );
		}

		$key = 'unit' === $type ? 'unit_id' : 'room_id';
		return $this->api_request_profile(
			'inventory',
			'GET',
			'/inventory',
			array(
				'type'       => $type,
				$key         => $listing_id,
				'start_date' => $start_date,
				'end_date'   => $end_date,
			)
		);
	}

	private function is_live_app_mode() {
		return $this->profile_is_live( 'inventory' );
	}

	private function resolve_live_room_type_id_from_mapping( array $mapping ) {
		if ( 'room' === $mapping['listing_type'] && ! empty( $mapping['room_id'] ) ) {
			return $mapping['room_id'];
		}

		$unit_id = '';
		if ( 'unit' === $mapping['listing_type'] && ! empty( $mapping['unit_id'] ) ) {
			$unit_id = $mapping['unit_id'];
		} elseif ( 'unit_pool' === $mapping['listing_type'] && ! empty( $mapping['unit_pool'] ) ) {
			$unit_id = $mapping['unit_pool'][0];
		}

		if ( ! $unit_id ) {
			return new WP_Error( 'alawa_hps_live_room_type_missing', __( 'Could not resolve a live HostPlatform room type from the current product mapping.', 'alawa-hostplatform-sync' ) );
		}

		$profile = $this->profile_config( 'inventory' );
		$units = $this->api_request_profile( 'inventory', 'GET', '/unit', array( 'propertyId' => $profile['property_id'] ) );
		if ( is_wp_error( $units ) ) {
			return $units;
		}

		foreach ( (array) $units as $unit ) {
			if ( ! is_array( $unit ) ) {
				continue;
			}

			if ( ( $unit['_id'] ?? '' ) === $unit_id ) {
				$room_type_id = $unit['roomType']['_id'] ?? '';
				if ( $room_type_id ) {
					return $room_type_id;
				}
			}
		}

		return new WP_Error( 'alawa_hps_live_room_type_not_found', __( 'The mapped HostPlatform unit could not be matched to a room type.', 'alawa-hostplatform-sync' ) );
	}

	private function api_get_live_inventory( $room_type_id, $start_date, $end_date ) {
		$property_id = $this->profile_config( 'inventory' )['property_id'] ?? '';
		if ( ! $property_id ) {
			return new WP_Error( 'alawa_hps_missing_property', sprintf( __( '%s property ID is missing. Open Settings and fill the active inventory profile.', 'alawa-hostplatform-sync' ), $this->profile_label( 'inventory' ) ) );
		}

		$result = $this->api_request_profile(
			'inventory',
			'GET',
			'/property/' . rawurlencode( $property_id ) . '/rates',
			array(
				'startDate' => $start_date,
				'endDate'   => $end_date,
				'otas'      => 'agoda,agodahomes,airbnb,bookingcom,ctripcm,expedia,traveloka,tiketcom',
			)
		);
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$room_data = null;
		foreach ( (array) $result as $item ) {
			if ( is_array( $item ) && ( $item['_id'] ?? '' ) === $room_type_id ) {
				$room_data = $item;
				break;
			}
		}

		if ( ! $room_data ) {
			return new WP_Error(
				'alawa_hps_live_inventory_room_missing',
				__( 'The live HostPlatform rates feed did not return the mapped room type.', 'alawa-hostplatform-sync' ),
				array( 'room_type_id' => $room_type_id )
			);
		}

		return array(
			'inventories' => $this->normalize_live_inventory_rows( $room_data ),
			'room'        => $room_data,
		);
	}

	private function normalize_live_inventory_rows( array $room_data ) {
		$rows = array();

		foreach ( (array) ( $room_data['inventories'] ?? array() ) as $inventory_group ) {
			foreach ( (array) ( $inventory_group['roomsByDates'] ?? array() ) as $day ) {
				$date = $day['date'] ?? '';
				if ( ! $date || isset( $rows[ $date ] ) ) {
					continue;
				}

				$available = isset( $day['rooms'] ) ? (int) $day['rooms'] : 0;
				$total = isset( $day['total'] ) ? (int) $day['total'] : 0;
				$blocked = isset( $day['blockOrMaintenance'] ) ? (int) $day['blockOrMaintenance'] : 0;
				$booked = isset( $day['validReservation'] ) ? (int) $day['validReservation'] : max( 0, $total - $available - $blocked );

				$rows[ $date ] = array(
					'date'              => $date,
					'inventory'         => $available,
					'occupiedInventory' => $booked,
					'breakdown'         => array(
						'bookedInventory' => $booked,
						'blockInventory'  => $blocked,
						'totalInventory'  => $total,
					),
					'raw_source'        => 'live_property_rates',
					'raw_room_type_id'  => $room_data['_id'] ?? '',
				);
			}
		}

		ksort( $rows );
		return array_values( $rows );
	}

	private function api_create_reservation( $type, $listing_id, array $payload ) {
		$key = 'unit' === $type ? 'unit_id' : 'room_id';
		$query = array(
			'type' => $type,
			$key   => $listing_id,
		);

		return $this->api_request_profile( 'reservation', 'POST', '/reservation', $query, $payload );
	}

	private function api_request_profile( $purpose, $method, $path, array $query = array(), ?array $body = null ) {
		$profile = $this->profile_config( $purpose );
		if ( empty( $profile['base_url'] ) || empty( $profile['access_token'] ) ) {
			return new WP_Error( 'alawa_hps_missing_credentials', sprintf( __( '%s API URL or access token is missing. Open Settings and complete the active profile.', 'alawa-hostplatform-sync' ), $this->profile_label( $purpose ) ) );
		}

		$namespace = ! empty( $profile['api_namespace'] ) ? rtrim( $profile['api_namespace'], '/' ) : '/external/v1';
		$url = rtrim( $profile['base_url'], '/' ) . $namespace . $path;
		if ( $query ) {
			$url = add_query_arg( $query, $url );
		}

		$token = trim( $profile['access_token'] );
		// Strip leading JWT or Bearer if pasted by mistake
		$token = preg_replace( '/^(Bearer|JWT)\s+/i', '', $token );
		$auth_mode = $profile['auth_mode'] ?? 'access_token';

		$headers = array(
			'Accept' => 'application/json',
		);

		if ( 'jwt' === $auth_mode ) {
			$headers['Authorization'] = 'JWT ' . $token;
		} elseif ( 'bearer' === $auth_mode ) {
			$headers['Authorization'] = 'Bearer ' . $token;
		} else {
			$headers['access-token'] = $token;
		}

		$args = array(
			'method'  => $method,
			'timeout' => 25,
			'headers' => $headers,
		);

		if ( null !== $body ) {
			$args['headers']['Content-Type'] = 'application/json';
			$args['body'] = wp_json_encode( $body );
		}

		$response = wp_remote_request( $url, $args );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		$raw = wp_remote_retrieve_body( $response );
		$data = json_decode( $raw, true );

		if ( $code < 200 || $code >= 300 ) {
			return new WP_Error( 'alawa_hps_api_error', sprintf( 'HostPlatform API returned HTTP %d.', $code ), array( 'url' => $url, 'body' => $data ? $data : $raw ) );
		}

		return is_array( $data ) ? $data : array( 'raw' => $raw );
	}

	private function parse_unit_pool( $value ) {
		if ( is_array( $value ) ) {
			$value = implode( "\n", $value );
		}

		$parts = preg_split( '/[\s,;]+/', (string) $value );
		$ids = array();

		foreach ( $parts as $part ) {
			$part = trim( $part );
			if ( '' === $part ) {
				continue;
			}

			if ( preg_match( '~listing/([A-Za-z0-9_-]+)~', $part, $matches ) ) {
				$part = $matches[1];
			}

			$part = preg_replace( '/[^A-Za-z0-9_-]/', '', $part );
			if ( $part ) {
				$ids[] = $part;
			}
		}

		return array_values( array_unique( $ids ) );
	}

	private function normalize_unit_pool_text( $value ) {
		return implode( "\n", $this->parse_unit_pool( $value ) );
	}

	private function derive_room_summaries_from_units( array $units ) {
		$rooms = array();

		foreach ( $units as $unit ) {
			if ( ! is_array( $unit ) ) {
				continue;
			}

			$room_type = isset( $unit['roomType'] ) && is_array( $unit['roomType'] ) ? $unit['roomType'] : array();
			$room_id = $room_type['_id'] ?? '';
			if ( ! $room_id ) {
				continue;
			}

			if ( ! isset( $rooms[ $room_id ] ) ) {
				$name = '';
				if ( ! empty( $room_type['name'] ) ) {
					$name = (string) $room_type['name'];
				} elseif ( ! empty( $unit['detailedDescription']['summary'] ) ) {
					$name = (string) $unit['detailedDescription']['summary'];
				}

				$rooms[ $room_id ] = array(
					'id'          => $room_id,
					'name'        => trim( $name ),
					'property_id' => $room_type['property']['_id'] ?? '',
					'unit_count'  => 0,
					'unit_ids'    => array(),
				);
			}

			$rooms[ $room_id ]['unit_count']++;
			if ( ! empty( $unit['_id'] ) ) {
				$rooms[ $room_id ]['unit_ids'][] = $unit['_id'];
			}
		}

		foreach ( $rooms as &$room ) {
			$room['unit_ids'] = array_values( array_unique( $room['unit_ids'] ) );
			if ( '' === $room['name'] ) {
				$room['name'] = $room['id'];
			}
		}
		unset( $room );

		return array_values( $rooms );
	}

	private function log( $level, $source, $message, array $context = array() ) {
		global $wpdb;

		$wpdb->insert(
			$wpdb->prefix . 'alawa_hps_logs',
			array(
				'created_at' => current_time( 'mysql' ),
				'level'      => sanitize_key( $level ),
				'source'     => sanitize_key( $source ),
				'message'    => $message,
				'context'    => $context ? wp_json_encode( $context, JSON_PRETTY_PRINT ) : null,
			),
			array( '%s', '%s', '%s', '%s', '%s' )
		);
	}

	private function clear_logs() {
		global $wpdb;
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}alawa_hps_logs" );
	}

	private function prune_logs() {
		global $wpdb;
		$days = absint( $this->settings()['log_retention_days'] );
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}alawa_hps_logs WHERE created_at < %s",
				gmdate( 'Y-m-d H:i:s', strtotime( '-' . $days . ' days', current_time( 'timestamp' ) ) )
			)
		);
	}

	private function date_only( $value ) {
		if ( ! $value ) {
			return '';
		}

		$timestamp = is_numeric( $value ) ? (int) $value : strtotime( (string) $value );
		return $timestamp ? gmdate( 'Y-m-d', $timestamp ) : '';
	}
}

Alawa_HostPlatform_Sync::instance();
