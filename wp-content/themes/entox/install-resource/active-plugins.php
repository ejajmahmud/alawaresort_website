<?php

require_once (ENTOX_URL.'/install-resource/class-tgm-plugin-activation.php');

// Register required plugins
add_action( 'tgmpa_register', function() {
    $plugins = array(
        array(
            'name'                     => esc_html__('WCFM – Frontend Manager','entox'),
            'slug'                     => 'wc-frontend-manager',
            'required'                 => true,
        ),

        array(
            'name'                     => esc_html__('WCFM – Multivendor Marketplace','entox'),
            'slug'                     => 'wc-multivendor-marketplace',
            'required'                 => true,
        ),

        array(
            'name'                     => esc_html__('Woocommerce','entox'),
            'slug'                     => 'woocommerce',
            'required'                 => true,
        ),

        array(
            'name'                     => esc_html__('Elementor','entox'),
            'slug'                     => 'elementor',
            'required'                 => true,
        ),
        array(
            'name'                     => esc_html__('Contact Form 7','entox'),
            'slug'                     => 'contact-form-7',
            'required'                 => true,
        ),
        array(
            'name'                     => esc_html__('Widget importer exporter','entox'),
            'slug'                     => 'widget-importer-exporter',
            'required'                 => true,
        ),
        array(
            'name'                     => esc_html__('One click demo import','entox'),
            'slug'                     => 'one-click-demo-import',
            'required'                 => true,
        ),
        
        array(
            'name'                     => esc_html__('OvaTheme Framework','entox'),
            'slug'                     => 'ova-framework',
            'required'                 => true,
            'source'                   => get_template_directory() . '/install-resource/plugins/ova-framework.zip',
            'version'                   => '1.0.2'
            
        ),
        array(
            'name'                     => esc_html__('OvaTheme BRW','entox'),
            'slug'                     => 'ova-brw',
            'required'                 => true,
            'source'                   => get_template_directory() . '/install-resource/plugins/ova-brw.zip',
            'version'                   => '12.4.3'
            
        ),
        array(
            'name'                     => esc_html__('OvaTheme BRW WCFM','entox'),
            'slug'                     => 'ova-brw-wcfm',
            'required'                 => true,
            'source'                   => get_template_directory() . '/install-resource/plugins/ova-brw-wcfm.zip',
            'version'                   => '12.0.3'
            
        ),
        array(
            'name'                     => esc_html__('YITH WooCommerce Wishlist','entox'),
            'slug'                     => 'yith-woocommerce-wishlist',
            'required'                 => true,
        ),
    );

    $config = array(
        'id'           => 'entox',                 // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '',                      // Default absolute path to bundled plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.

        
    );

    entox_tgmpa( $plugins, $config );
});

// After import setup
add_action( 'ocdi/after_import', function() {
    // Assign menus to their locations.
    $primary = get_term_by( 'name', 'Primary Menu', 'nav_menu' );
    if ( !is_wp_error( $primary ) ) {
        set_theme_mod( 'nav_menu_locations', [
            'primary' => $primary->term_id
        ]);
    }

    // Assign front page and posts page (blog page).
    $front_page_id = entox_get_page_by_title( 'Home - Car' );
    $blog_page_id  = entox_get_page_by_title( 'Blog' );

    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $front_page_id->ID );
    update_option( 'page_for_posts', $blog_page_id->ID );

    // Update customize
    entox_replace_url_in_customize();

    // After import replace URLs
    entox_replace_url_after_import();

    // Replace image URLs
    $upload_dir = wp_get_upload_dir();
    $base_url   = $upload_dir['baseurl'];
    entox_replace_url_after_import( $base_url, 'https://ovatheme.nyc3.cdn.digitaloceanspaces.com/entox' );
});

// Import files
add_filter( 'ocdi/import_files', function() {
    return array(
        array(
            'import_file_name'             => 'Demo Import',
            'categories'                   => array( 'Category 1', 'Category 2' ),
            'local_import_file'            => trailingslashit( get_template_directory() ) . 'install-resource/demo-import/demo-content.xml',
            'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'install-resource/demo-import/widgets.wie',
            'local_import_customizer_file'   => trailingslashit( get_template_directory() ) . 'install-resource/demo-import/customize.dat',
        )
    );
});

// Get page by title
if ( ! function_exists( 'entox_get_page_by_title' ) ) {
    function entox_get_page_by_title( $page_title, $output = OBJECT, $post_type = 'page' ) {
        global $wpdb;

        if ( is_array( $post_type ) ) {
            $post_type           = esc_sql( $post_type );
            $post_type_in_string = "'" . implode( "','", $post_type ) . "'";
            $sql                 = $wpdb->prepare(
                "
                SELECT ID
                FROM $wpdb->posts
                WHERE post_title = %s
                AND post_type IN ($post_type_in_string)
            ",
                $page_title
            );
        } else {
            $sql = $wpdb->prepare(
                "
                SELECT ID
                FROM $wpdb->posts
                WHERE post_title = %s
                AND post_type = %s
            ",
                $page_title,
                $post_type
            );
        }

        $page = $wpdb->get_var( $sql );

        if ( $page ) {
            return get_post( $page, $output );
        }

        return null;
    }
}

// Replace url in customize
if ( !function_exists( 'entox_replace_url_in_customize' ) ) {
    function entox_replace_url_in_customize() {
        $demo_url = apply_filters( 'entox_demo_url', 'https://demo.ovathemewp.com/entox' );

        // Get theme mods
        $theme_mods = get_theme_mods();

        if ( !empty( $theme_mods ) ) {
            foreach ( $theme_mods as $key => $val ) {
                if ( is_string( $val ) && str_contains( $val, $demo_url ) ) {
                    $val = str_replace( $demo_url, get_site_url(), $val );

                    // Update theme mod
                    set_theme_mod( $key, $val );
                }
            }
        }
    }
}

// Replace url after import demo data
if ( !function_exists( 'entox_replace_url_after_import' ) ) {
    function entox_replace_url_after_import( $site_url = '', $demo_url = '' ) {
        global $wpdb;

        // Site URL
        if ( !$site_url ) {
            $site_url = apply_filters( 'entox_site_url', get_site_url() );
        }

        // Demo URL
        if ( !$demo_url ) {
            $demo_url = apply_filters( 'entox_demo_url', 'https://demo.ovathemewp.com/entox' );
        }

        // Replace in option value
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->options} " .
                "SET `option_value` = REPLACE(`option_value`, %s, %s);",
                $demo_url,
                $site_url
            )
        );

        // Replace in posts
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->posts} " .
                "SET `post_content` = REPLACE(`post_content`, %s, %s), `guid` = REPLACE(`guid`, %s, %s);",
                $demo_url,
                $site_url,
                $demo_url,
                $site_url
            )
        );

        // Replace in meta value
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->postmeta} " .
                "SET `meta_value` = REPLACE(`meta_value`, %s, %s) " .
                "WHERE `meta_key` <> '_elementor_data';",
                $demo_url,
                $site_url
            )
        );

        // Elementor Data
        $escaped_from       = str_replace( '/', '\\/', $demo_url );
        $escaped_to         = str_replace( '/', '\\/', $site_url );
        $meta_value_like    = '[%'; // meta_value LIKE '[%' are json formatted

        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->postmeta} " .
                'SET `meta_value` = REPLACE(`meta_value`, %s, %s) ' .
                "WHERE `meta_key` = '_elementor_data' AND `meta_value` LIKE %s;",
                $escaped_from,
                $escaped_to,
                $meta_value_like
            )
        );
    }
}