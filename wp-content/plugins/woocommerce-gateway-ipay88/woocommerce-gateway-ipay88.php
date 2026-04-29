<?php
/*
 * Plugin Name: WooCommerce iPay88 Payment Gateway
 * Description: Allows you to use <a href="http://www.ipay88.com">iPay88</a> payment gateway with the WooCommerce plugin.
 */

if (!defined('ABSPATH')) {
    exit;
}

// /**
 // * Required functions
 // */
// if ( ! function_exists( 'woothemes_queue_update' ) ) {
    // require_once( 'woo-includes/woo-functions.php' );
// }

// /**
 // * Plugin updates
 // */
// woothemes_queue_update( plugin_basename( __FILE__ ), '0d38de1dde5a04dea0109743b5629d8f', '18724' );

// if ( ! is_woocommerce_active() ) {
    // return;
// }

class WC_iPay88
{
    /**
     * WC Logger object
     * @var object
     */
    private static $log;

    /**
     * Plugin URL
     * @var type
     */
    private static $plugin_url;

    /**
     * Plugin Path
     * @var string
     */
    private static $plugin_path;

    /**
     * Do we have debug mode enabled
     * @var bool
     */
    private static $is_debug_enabled;

    public function __construct()
    {
        // Add required files
        $this->load_gateway_files();

        add_action('init', [$this, 'load_text_domain']);
        add_filter('woocommerce_payment_gateways', [$this, 'add_ipay88_gateway']);

        // Add a 'Settings' link to the plugin action links
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'settings_support_link'], 10, 4);
    }

    /**
     * Localisation
     **/
    public function load_text_domain()
    {
        load_textdomain('wc_ipay88', WP_LANG_DIR . '/woocommerce-gateway-ipay88/wc_ipay88-' . get_locale() . '.mo');
        load_plugin_textdomain('wc_ipay88', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * Add 'Settings' link to the plugin actions links
     *
     * @since 1.1
     * @return array associative array of plugin action links
     */
    public function settings_support_link($actions, $plugin_file, $plugin_data, $context)
    {
        $gateway = $this->get_gateway_class();

        return array_merge(
            ['settings' => '<a href="' . WC_Compat_iPay88::gateway_settings_page($gateway) . '">' . __('Settings', 'wc_ipay88') . '</a>'],
            $actions
        );
    }

    /**
     * Get the correct gateway class name to load
     *
     * @since 1.1
     * @return string Class name
     */
    private function get_gateway_class()
    {
        return 'WC_Gateway_iPay88';
    }

    /**
     * Load gateway files
     *
     * @since 1.1
     */
    public function load_gateway_files()
    {
        include_once 'includes/class-wc-gateway-ipay88.php';
        include_once 'includes/class-wc-compat-ipay88.php';
        include_once 'includes/class-wc-requery-ipay88.php';
    }

    /**
     * Safely get POST variables
     *
     * @since      1.1
     * @deprecated 1.2.4
     *
     * @param string $name POST variable name
     *
     * @return string The variable value
     */
    public static function get_post($name)
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }

        return null;
    }

    /**
     * Safely get GET variables
     *
     * @since      1.1
     * @deprecated 1.2.4
     *
     * @param string $name GET variable name
     *
     * @return string The variable value
     */
    public static function get_get($name)
    {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }

        return null;
    }

    /**
     * Retrieve a value from an array
     *
     * @since 1.2.4
     *
     * @param string     $name
     * @param array      $array
     * @param null|mixed $default
     *
     * @return null|mixed
     */
    public static function get_field($name, array $array, $default = null)
    {
        if (isset($array[$name])) {
            return $array[$name];
        }

        return $default;
    }

    /**
     * Add the gateway to WooCommerce
     *
     * @since 1.1
     *
     * @param array $methods
     *
     * @return array
     */
    public function add_ipay88_gateway($methods)
    {
        $methods[] = $this->get_gateway_class();

        return $methods;
    }

    /**
     * Add debug log message
     *
     * @since 1.1
     *
     * @param string $message
     */
    public static function add_debug_log($message)
    {
        if (!is_object(self::$log)) {
            self::$log = WC_Compat_iPay88::get_wc_logger();
        }

        self::$log->add('ipay88', $message);
    }

    /**
     * Check, if debug logging is enabled
     *
     * @since 1.2.4
     * @return bool
     */
    public static function is_debug_enabled()
    {
        if (self::$is_debug_enabled) {
            return self::$is_debug_enabled;
        } else {
            $settings = get_option('woocommerce_ipay88_settings');

            self::$is_debug_enabled = ('yes' == $settings['debug']);

            return self::$is_debug_enabled;
        }
    }

    /**
     * Get the plugin url
     *
     * @since 1.1
     * @return string
     */
    public static function plugin_url()
    {
        if (self::$plugin_url) {
            return self::$plugin_url;
        }

        return self::$plugin_url = untrailingslashit(plugins_url('/', __FILE__));
    }

    /**
     * Get the plugin path
     *
     * @since 1.1
     * @return string
     */
    public static function plugin_path()
    {
        if (self::$plugin_path) {
            return self::$plugin_path;
        }

        return self::$plugin_path = untrailingslashit(plugin_dir_path(__FILE__));
    }
}

// Load the plugin
add_action('plugins_loaded', 'load_ipay88_gateway_files');
function load_ipay88_gateway_files()
{
    if (!class_exists('WC_Payment_Gateways')) {
        return;
    }
    new WC_iPay88();
}

// schedule event upon plugin activation
register_activation_hook(__FILE__, 'cron_activation');
function cron_activation()
{
    if (!wp_next_scheduled('ipay88_requery_event')) {
        wp_schedule_event(time(), 'every_10_minute', 'ipay88_requery_event');
    }
    error_log('activate requery cron');
}

// unschedule event upon plugin deactivation
register_deactivation_hook(__FILE__, 'cron_deactivation');
function cron_deactivation()
{
    wp_clear_scheduled_hook('ipay88_requery_event');
    error_log('deactivate requery cron');
}

add_action('ipay88_requery_event', 'ipay88_check_requery');
function ipay88_check_requery()
{
    new WC_Requery_iPay88();
}

function ipay88_schedules($schedules)
{
    if (!isset($schedules['every_10_minute'])) {
        $schedules['every_10_minute'] = [
            'interval' => 60 * 10,
            'display'  => __('Every 10 minutes')];
    }
    return $schedules;
}
add_filter('cron_schedules', 'ipay88_schedules');
