<?php
/**
 * Entox\WooCommerce Template Hooks
 *
 * Action/filter hooks used for Entox\WooCommerce functions/templates.
 *
 * @package Entox\WooCommerce\Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Entox Product Loop Items.
 *
 * @see entox_template_before_head_loop_item()
 * @see entox_template_head_loop_product_favourite()
 * @see entox_template_head_loop_product_thumbnail()
 * @see entox_template_head_loop_product_price()
 * @see entox_template_after_head_loop_item()
 * 
 * @see entox_template_before_foot_loop_item()
 * @see entox_template_foot_loop_product_title()
 * @see entox_template_foot_loop_product_review()
 * @see entox_template_foot_loop_product_custom_taxonomies()
 * @see entox_template_foot_loop_product_location()
 * @see entox_template_after_foot_loop_item()
 */

add_action( 'entox_wc_before_head_loop_item', 'entox_template_before_head_loop_item', 10 );
add_action( 'entox_wc_head_loop_item', 'entox_template_head_loop_product_favourite', 10 );
add_action( 'entox_wc_head_loop_item', 'entox_template_head_loop_product_thumbnail', 10 );
add_action( 'entox_wc_head_loop_item', 'entox_template_head_loop_product_featured', 10 );
add_action( 'entox_wc_after_head_loop_item', 'entox_template_after_head_loop_item', 10 );

add_action( 'entox_wc_before_foot_loop_item', 'entox_template_before_foot_loop_item', 10 );
add_action( 'entox_wc_foot_loop_item', 'entox_template_foot_loop_product_title', 10 );
add_action( 'entox_wc_foot_loop_item', 'entox_template_before_loop_product_price', 10 );
add_action( 'entox_wc_foot_loop_item', 'entox_template_head_loop_product_price', 10 );
add_action( 'entox_wc_foot_loop_item', 'entox_template_foot_loop_product_review', 10 );
add_action( 'entox_wc_foot_loop_item', 'entox_template_after_loop_product_price', 10 );
add_action( 'entox_wc_foot_loop_item', 'entox_template_foot_loop_product_custom_taxonomies', 10 );
add_action( 'entox_wc_foot_loop_item', 'entox_template_foot_loop_product_location', 10 );
add_action( 'entox_wc_after_foot_loop_item', 'entox_template_after_foot_loop_item', 10 );

/**
 * Entox Single Product.
 *
 * @see entox_wc_before_single_product()
 */

add_action( 'entox_wc_before_single_product', 'entox_wc_template_single_title', 5 );
add_action( 'entox_wc_before_single_product', 'entox_wc_template_single_price', 5 );
add_action( 'entox_wc_template_single_special_price', 'entox_wc_template_single_special_price', 10 );
add_action( 'entox_wc_template_single_period', 'entox_wc_template_single_period', 10 );
add_action( 'entox_wc_template_single_day', 'entox_wc_template_single_day', 10 );
add_action( 'entox_wc_template_single_hour', 'entox_wc_template_single_hour', 10 );
add_action( 'entox_wc_template_single_weekdays_price', 'entox_wc_template_single_weekdays_price', 10 );
add_action( 'entox_wc_single_product_summary_left', 'entox_wc_template_product_images', 10 );
add_action( 'entox_wc_template_product_thumbnails', 'entox_wc_template_product_thumbnails', 10 );
add_action( 'entox_wc_single_product_summary_left', 'entox_wc_template_product_description', 10 );
add_action( 'entox_wc_single_product_summary_left', 'entox_wc_template_product_location', 15 );
add_action( 'entox_wc_single_product_summary_left', 'entox_wc_template_product_calendar', 15 );
add_action( 'entox_wc_single_product_summary_left', 'entox_wc_template_product_policies', 20 );
add_action( 'entox_wc_single_product_summary_left', 'entox_wc_template_product_tags', 20 );
add_action( 'entox_wc_single_product_summary_left', 'entox_wc_template_product_review', 20 );

add_action( 'entox_wc_single_product_summary_right', 'entox_wc_template_product_taxonomy', 10 );
add_action( 'entox_wc_single_product_summary_right', 'entox_wc_template_product_ask_question', 10 );
add_action( 'entox_wc_single_product_summary_right', 'entox_wc_template_product_forms_tab', 10 );
add_action( 'entox_wc_template_product_booking_form', 'entox_wc_template_product_booking_form', 10 );
add_action( 'entox_wc_single_product_summary_right', 'entox_wc_template_product_vendor', 10 );
add_action( 'entox_wc_template_product_request_booking', 'entox_wc_template_product_request_booking', 10 );

/* Booking Form */
add_action( 'entox_wc_booking_form', 'entox_wc_booking_form_fields', 5 );
add_action( 'entox_wc_booking_form', 'entox_wc_booking_form_extra_fields', 10 );
add_action( 'entox_wc_booking_form', 'entox_wc_booking_form_services', 15 );
add_action( 'entox_wc_booking_form', 'entox_wc_booking_form_resource', 20 );
add_action( 'entox_wc_booking_form', 'entox_wc_booking_form_deposit', 25 );
add_action( 'entox_wc_booking_form', 'entox_wc_booking_form_ajax_total', 30 );

add_action( 'entox_wc_after_single_product', 'entox_wc_template_product_related', 20 );