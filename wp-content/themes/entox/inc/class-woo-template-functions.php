<?php
/**
 * Entox\WooCommerce Template
 *
 * Functions for the templating system.
 *
 * @package  Entox\WooCommerce\Functions
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit;


if ( ! function_exists( 'entox_template_before_head_loop_item' ) ) {
    /**
     * Insert before head products in the loop.
     */
    function entox_template_before_head_loop_item() {
        echo '<div class="entox_head_product">';
    }
}

if ( ! function_exists( 'entox_template_head_loop_product_favourite' ) ) {
    /**
     * Get head product favourite in the loop.
     */
    function entox_template_head_loop_product_favourite() {
        wc_get_template( 'rental/favourite.php' );
    }
}

if ( ! function_exists( 'entox_template_head_loop_product_thumbnail' ) ) {
    /**
     * Get head product thumbnail in the loop.
     */
    function entox_template_head_loop_product_thumbnail( $args ) {
        wc_get_template( 'rental/thumbnail.php', $args );
    }
}

if ( ! function_exists( 'entox_template_head_loop_product_featured' ) ) {
    /**
     * Get head product featured in the loop.
     */
    function entox_template_head_loop_product_featured() {
        wc_get_template( 'rental/featured.php' );
    }
}

if ( ! function_exists( 'entox_template_after_head_loop_item' ) ) {
    /**
     * Insert after head products in the loop.
     */
    function entox_template_after_head_loop_item() {
        echo '</div>';
    }
}

if ( ! function_exists( 'entox_template_before_loop_product_price' ) ) {
    function entox_template_before_loop_product_price() {
        ?>
        <div class="entox-product-wrapper-price">
        <?php
    }
}

if ( ! function_exists( 'entox_template_after_loop_product_price' ) ) {
    function entox_template_after_loop_product_price() {
        ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'entox_template_head_loop_product_price' ) ) {
    /**
     * Get after head product price in the loop.
     */
    function entox_template_head_loop_product_price() {
        wc_get_template( 'rental/price.php' );
    }
}

if ( ! function_exists( 'entox_template_before_foot_loop_item' ) ) {
    /**
     * Insert before foot products in the loop.
     */
    function entox_template_before_foot_loop_item() {
        echo '<div class="entox_foot_product">';
    }
}

if ( ! function_exists( 'entox_template_foot_loop_product_title' ) ) {
    /**
     * Get foot product title in the loop.
     */
    function entox_template_foot_loop_product_title( $args ) {
        wc_get_template( 'rental/title.php', $args );
    }
}

if ( ! function_exists( 'entox_template_foot_loop_product_review' ) ) {
    /**
     * Get foot product review in the loop.
     */
    function entox_template_foot_loop_product_review() {
        wc_get_template( 'rental/review.php' );
    }
}

if ( ! function_exists( 'entox_template_foot_loop_product_custom_taxonomies' ) ) {
    /**
     * Get foot product custom taxonomy in the loop.
     */
    function entox_template_foot_loop_product_custom_taxonomies() {
        wc_get_template( 'rental/custom_taxonomies.php' );
    }
}

if ( ! function_exists( 'entox_template_foot_loop_product_location' ) ) {
    /**
     * Get foot product location in the loop.
     */
    function entox_template_foot_loop_product_location() {
        wc_get_template( 'rental/location.php' );
    }
}

if ( ! function_exists( 'entox_template_after_foot_loop_item' ) ) {
    /**
     * Insert after foot products in the loop.
     */
    function entox_template_after_foot_loop_item() {
        echo '</div">';
    }
}

/**
 * Single Product Hooks
 */

if ( ! function_exists( 'entox_wc_template_single_title' ) ) {
    /**
     * Get product title and map.
     */
    function entox_wc_template_single_title() {
        wc_get_template( 'single-product/loop/title.php' );
    }
}

if ( ! function_exists( 'entox_wc_template_single_price' ) ) {
    /**
     * Get product price
     */
    function entox_wc_template_single_price() {
        wc_get_template( 'single-product/loop/price.php' );
    }
}

if ( ! function_exists( 'entox_wc_template_single_special_price' ) ) {
    /**
     * Get product special price
     */
    function entox_wc_template_single_special_price() {
        wc_get_template( 'single-product/loop/special-price.php' );
    }
}

if ( ! function_exists( 'entox_wc_template_single_period' ) ) {
    /**
     * Get product discount by period
     */
    function entox_wc_template_single_period() {
        wc_get_template( 'single-product/discount/period.php');
    }
}

if ( ! function_exists( 'entox_wc_template_single_hour' ) ) {
    /**
     * Get product discount by hour
     */
    function entox_wc_template_single_hour() {
        wc_get_template( 'single-product/discount/hour.php' );
    }
}

if ( ! function_exists( 'entox_wc_template_single_day' ) ) {
    /**
     * Get product discount by day
     */
    function entox_wc_template_single_day() {
        wc_get_template( 'single-product/discount/day.php' );
    }
}

if ( ! function_exists( 'entox_wc_template_single_weekdays_price' ) ) {
    /**
     * Get product discount by weekdays
     */
    function entox_wc_template_single_weekdays_price() {
        wc_get_template( 'single-product/discount/weekdays.php' );
    }
}

if ( ! function_exists( 'entox_wc_template_product_images' ) ) {
    /**
     * Get product image
     */
    function entox_wc_template_product_images() {
        wc_get_template( 'single-product/loop/image.php' );
    }
}

if ( ! function_exists( 'entox_wc_template_product_thumbnails' ) ) {
    /**
     * Get product thumbnails
     */
    function entox_wc_template_product_thumbnails() {
        wc_get_template( 'single-product/loop/thumbnails.php' );
    }
}

if ( ! function_exists( 'entox_wc_template_product_description' ) ) {
    /**
     * Get product description
     */
    function entox_wc_template_product_description() {
        wc_get_template( 'single-product/loop/content.php' );
    }
}

if ( ! function_exists( 'entox_wc_template_product_policies' ) ) {
    /**
     * Get product policies
     */
    function entox_wc_template_product_policies() {
        wc_get_template( 'single-product/loop/policies.php' );
    }
}

if ( ! function_exists( 'entox_wc_template_product_location' ) ) {
    /**
     * Get product location
     */
    function entox_wc_template_product_location() {
        wc_get_template( 'single-product/loop/location.php' );
    }
}

if ( ! function_exists( 'entox_wc_template_product_calendar' ) ) {
    /**
     * Get product calendar
     */
    function entox_wc_template_product_calendar() {
        wc_get_template( 'single-product/loop/calendar.php' );
    }
}

if ( ! function_exists( 'entox_wc_template_product_tags' ) ) {
    /**
     * Get product tags
     */
    function entox_wc_template_product_tags() {
        wc_get_template( 'single-product/loop/tags.php' );
    }
}

if ( ! function_exists( 'entox_wc_template_product_review' ) ) {
    /**
     * Get product review
     */
    function entox_wc_template_product_review() {
        wc_get_template( 'single-product/loop/review.php' );
    }
}

if ( ! function_exists( 'entox_wc_comments' ) ) {
    function entox_wc_comments( $comment, $args, $depth ) {
        $GLOBALS['comment'] = $comment;
        wc_get_template(
            'single-product/loop/review-meta.php',
            array(
                'comment' => $comment,
                'args'    => $args,
                'depth'   => $depth,
            )
        );
    }
}

if ( ! function_exists( 'entox_wc_template_product_taxonomy' ) ) {
    /**
     * Get product taxonomy
     */
    function entox_wc_template_product_taxonomy() {
        wc_get_template( 'single-product/loop/taxonomy.php' );
    }
}

if ( ! function_exists( 'entox_wc_template_product_vendor' ) ) {
    /**
     * Get product vendor
     */
    function entox_wc_template_product_vendor() {
        wc_get_template( 'single-product/loop/vendor.php' );
    }
}

if ( ! function_exists( 'entox_wc_template_product_ask_question' ) ) {
    /**
     * Get product ask question
     */
    function entox_wc_template_product_ask_question() {
        wc_get_template( 'single-product/loop/ask-question.php' );
    }
}

if ( ! function_exists( 'entox_wc_template_product_forms_tab' ) ) {
    /**
     * Get product forms tab
     */
    function entox_wc_template_product_forms_tab() {
        wc_get_template( 'single-product/forms-tab.php' );
    }
}

if ( ! function_exists( 'entox_wc_template_product_booking_form' ) ) {
    /**
     * Get product booking form
     */
    function entox_wc_template_product_booking_form() {
        wc_get_template( 'single-product/booking-form.php' );
    }
}

if ( ! function_exists( 'entox_wc_template_product_request_booking' ) ) {
    /**
     * Get product request booking
     */
    function entox_wc_template_product_request_booking() {
        wc_get_template( 'single-product/request-booking.php' );
    }
}

if ( ! function_exists( 'entox_wc_booking_form_fields' ) ) {
    /**
     * Get product booking form fields
     */
    function entox_wc_booking_form_fields() {
        wc_get_template( 'single-product/booking-form/fields.php' );
    }
}

if ( ! function_exists( 'entox_wc_booking_form_extra_fields' ) ) {
    /**
     * Get product extra fields
     */
    function entox_wc_booking_form_extra_fields() {
        wc_get_template( 'single-product/booking-form/extra-fields.php' );
    }
}

if ( ! function_exists( 'entox_wc_booking_form_services' ) ) {
    /**
     * Get product services
     */
    function entox_wc_booking_form_services() {
        wc_get_template( 'single-product/booking-form/services.php' );
    }
}

if ( ! function_exists( 'entox_wc_booking_form_resource' ) ) {
    /**
     * Get product resource
     */
    function entox_wc_booking_form_resource() {
        wc_get_template( 'single-product/booking-form/resource.php' );
    }
}

if ( ! function_exists( 'entox_wc_booking_form_deposit' ) ) {
    /**
     * Get product deposit
     */
    function entox_wc_booking_form_deposit() {
        wc_get_template( 'single-product/booking-form/deposit.php' );
    }
}

if ( ! function_exists( 'entox_wc_booking_form_ajax_total' ) ) {
    /**
     * Get product ajax total
     */
    function entox_wc_booking_form_ajax_total() {
        wc_get_template( 'single-product/booking-form/ajax-total.php' );
    }
}

if ( ! function_exists( 'entox_wc_template_product_related' ) ) {
    /**
     * Get product related products
     */
    function entox_wc_template_product_related() {
        global $product;

        if ( ! $product ) {
            return;
        }

        $args = array(
            'posts_per_page' => 3,
            'columns'        => 3,
            'orderby'        => 'rand',
            'order'          => 'desc',
        );

        // Get visible related products then sort them at random.
        $args['related_products'] = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), $args['posts_per_page'], $product->get_upsell_ids() ) ), 'wc_products_array_filter_visible' );

        // Handle orderby.
        $args['related_products'] = wc_products_array_orderby( $args['related_products'], $args['orderby'], $args['order'] );

        // Set global loop values.
        wc_set_loop_prop( 'name', 'related' );
        wc_set_loop_prop( 'columns', apply_filters( 'woocommerce_related_products_columns', $args['columns'] ) );

        wc_get_template( 'single-product/loop/related.php', $args );
    }
}