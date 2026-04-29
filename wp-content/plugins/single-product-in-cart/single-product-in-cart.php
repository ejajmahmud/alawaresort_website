<?php
/**
 * Plugin Name: Single Product in Cart
 * Description: Allows only one product in the cart at a time, replacing previous items when new ones are added.
 * Version: 1.0.1
 * Author: Piyush Jangid
 * Author URI: https://piyushjangid.in
 * Text Domain: single-product-in-cart
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 * WC requires at least: 6.0
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit;
}

class PJSPIC_Single_Product_In_Cart {
    public function __construct() {
        add_action('woocommerce_add_to_cart', array($this, 'pjspic_keep_single_product_in_cart'), 15, 6);
        add_action('wp', array($this, 'pjspic_enforce_single_product_on_pages'));
    }

    public function pjspic_keep_single_product_in_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
        $cart = WC()->cart;
        $cart_items = $cart->get_cart();
        
        if (count($cart_items) > 1) {
            foreach ($cart_items as $key => $value) {
                if ($key !== $cart_item_key) {
                    $cart->remove_cart_item($key, true);
                }
            }
        }
    }

    public function pjspic_enforce_single_product_on_pages() {
        if (is_cart() || is_checkout()) {
            $cart = WC()->cart;
            if ($cart->is_empty()) return;
            
            $cart_items = $cart->get_cart();
            if (count($cart_items) > 1) {
                $keys = array_keys($cart_items);
                $keep_key = reset($keys);
                foreach ($cart_items as $key => $value) {
                    if ($key !== $keep_key) {
                        $cart->remove_cart_item($key, true);
                    }
                }
            }
        }
    }

    public static function pjspic_woocommerce_missing_notice() {
        echo '<div class="error"><p>';
        echo esc_html__('Single Product in Cart requires WooCommerce to be installed and active!', 'single-product-in-cart');
        echo '</p></div>';
    }
}

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    new PJSPIC_Single_Product_In_Cart();
} else {
    add_action('admin_notices', array('PJSPIC_Single_Product_In_Cart', 'pjspic_woocommerce_missing_notice'));
}

register_activation_hook(__FILE__, function() {
    if (!class_exists('WooCommerce')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            esc_html__('This plugin requires WooCommerce to be installed and active. Please install WooCommerce first.', 'single-product-in-cart'),
            esc_html__('Plugin Activation Error', 'single-product-in-cart'),
            array('response' => 200, 'back_link' => true)
        );
    }
});

register_deactivation_hook(__FILE__, function() {
    delete_option('pjspic_plugin_settings');
    delete_transient('pjspic_some_transient');
});
