=== Single Product in Cart ===
Contributors: piyushjangid
Tags: woocommerce, cart, single product, checkout, ecommerce
Requires at least: 5.6
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Allows only one product in the WooCommerce cart at a time. When a new product is added, it replaces the existing product without any warning.

== Description ==

The **Single Product in Cart** plugin ensures that only one product can exist in the WooCommerce cart at any given time. When a customer adds a new product to the cart, the plugin automatically removes any existing product and replaces it with the new one. This happens silently, without any warning messages.

This plugin is ideal for stores that want to enforce a single-product purchase flow, such as subscription-based services, exclusive product sales, or limited-time offers.

### Key Features:
- Ensures only one product is in the cart at a time.
- Automatically replaces existing products when a new one is added.
- Works seamlessly with WooCommerce cart and checkout pages.
- No warning messages shown to customers during product replacement.
- Compatible with both simple and variable products.

== Installation ==

1. Upload the `single-product-in-cart` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Ensure WooCommerce is installed and activated.

That's it! The plugin will automatically enforce a single-product cart.

== Frequently Asked Questions ==

= Does this plugin work with WooCommerce subscriptions? =
Yes, it works with all WooCommerce product types, including subscriptions.

= Can customers adjust the quantity of the single product? =
Yes, customers can adjust the quantity of the single product in the cart.

= What happens if a customer adds multiple products via a direct link? =
The plugin ensures only the last added product remains in the cart, removing all others.

= Is this plugin compatible with WooCommerce Blocks? =
Yes, it works seamlessly with WooCommerce Blocks and the classic cart/checkout pages.

== Screenshots ==

1. Cart page showing a single product.
2. Checkout page with only one product.

== Changelog ==

= 1.0.1 =
* Fixed bugs and errors.

= 1.0.0 =
* Initial release.

== Credits ==

Developed by [Piyush Jangid](https://piyushjangid.in).