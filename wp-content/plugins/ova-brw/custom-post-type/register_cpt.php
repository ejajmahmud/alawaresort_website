<?php

// Location //////////////////////////////////////////////////////////////////////////////////
add_action( 'init', 'ovabrw_location',0 );
function ovabrw_location() {
    
    $labels = array(
        'name'               => _x( 'Location', 'post type general name', 'ova-brw' ),
        'singular_name'      => _x( 'Location', 'post type singular name', 'ova-brw' ),
        'menu_name'          => _x( 'Location', 'admin menu', 'ova-brw' ),
        'name_admin_bar'     => _x( 'Location', 'add new on admin bar', 'ova-brw' ),
        'add_new'            => _x( 'Add New Location', 'Location', 'ova-brw' ),
        'add_new_item'       => __( 'Add New Location', 'ova-brw' ),
        'new_item'           => __( 'New Location', 'ova-brw' ),
        'edit_item'          => __( 'Edit Location', 'ova-brw' ),
        'view_item'          => __( 'View Location', 'ova-brw' ),
        'all_items'          => __( 'All Location', 'ova-brw' ),
        'search_items'       => __( 'Search Location', 'ova-brw' ),
        'parent_item_colon'  => __( 'Parent Location:', 'ova-brw' ),
        'not_found'          => __( 'No Location found.', 'ova-brw' ),
        'not_found_in_trash' => __( 'No Location found in Trash.', 'ova-brw' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'menu_icon'          => 'dashicons-format-gallery',
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'location' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'author', 'thumbnail', ),
    );

    register_post_type( 'location', $args );
}


// Vehicle
add_action( 'init', 'ovabrw_vehicle',0 );
function ovabrw_vehicle() {
    
    $labels = array(
        'name'               => _x( 'Vehicle', 'post type general name', 'ova-brw' ),
        'singular_name'      => _x( 'Vehicle', 'post type singular name', 'ova-brw' ),
        'menu_name'          => _x( 'Manage Vehicle', 'admin menu', 'ova-brw' ),
        'name_admin_bar'     => _x( 'Vehicle', 'add new on admin bar', 'ova-brw' ),
        'add_new'            => _x( 'Add New Vehicle', 'Vehicle', 'ova-brw' ),
        'add_new_item'       => __( 'Add New Vehicle', 'ova-brw' ),
        'new_item'           => __( 'New Vehicle', 'ova-brw' ),
        'edit_item'          => __( 'Edit Vehicle', 'ova-brw' ),
        'view_item'          => __( 'View Vehicle', 'ova-brw' ),
        'all_items'          => __( 'All Vehicle', 'ova-brw' ),
        'search_items'       => __( 'Search Vehicle', 'ova-brw' ),
        'parent_item_colon'  => __( 'Parent Vehicle:', 'ova-brw' ),
        'not_found'          => __( 'No Vehicle found.', 'ova-brw' ),
        'not_found_in_trash' => __( 'No Vehicle found in Trash.', 'ova-brw' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'menu_icon'          => 'dashicons-format-gallery',
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'vehicle' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'author' ),
    );

    register_post_type( 'vehicle', $args );
}

// Add Closed status in WooCommerce
add_action( 'init', 'register_wc_closed_order_statuses' );
function register_wc_closed_order_statuses() {
    register_post_status( 'wc-closed', array(
        'label'                     => _x( 'Closed', 'Order status', 'ova-brw' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Closed <span class="count">(%s)</span>', 'Closed <span class="count">(%s)</span>', 'ova-brw' )
    ) );
}






