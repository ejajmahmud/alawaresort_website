<?php if (!defined( 'ABSPATH' )) exit;

// Get current ID of post/page, etc
if( !function_exists( 'entox_get_current_id' )):
	function entox_get_current_id(){
	    
	    $current_page_id = '';
	    // Get The Page ID You Need
	    
	    if(class_exists("woocommerce")) {
	        if( is_shop() ){ ///|| is_product_category() || is_product_tag()) {
	            $current_page_id  =  get_option ( 'woocommerce_shop_page_id' );
	        }elseif(is_cart()) {
	            $current_page_id  =  get_option ( 'woocommerce_cart_page_id' );
	        }elseif(is_checkout()){
	            $current_page_id  =  get_option ( 'woocommerce_checkout_page_id' );
	        }elseif(is_account_page()){
	            $current_page_id  =  get_option ( 'woocommerce_myaccount_page_id' );
	        }elseif(is_view_order_page()){
	            $current_page_id  = get_option ( 'woocommerce_view_order_page_id' );
	        }
	    }
	    if($current_page_id=='') {
	        if ( is_home () && is_front_page () ) {
	            $current_page_id = '';
	        } elseif ( is_home () ) {
	            $current_page_id = get_option ( 'page_for_posts' );
	        } elseif ( is_search () || is_category () || is_tag () || is_tax () || is_archive() ) {
	            $current_page_id = '';
	        } elseif ( !is_404 () ) {
	           $current_page_id = get_the_id();
	        } 
	    }

	    return $current_page_id;
	}
endif;



if (!function_exists('entox_is_elementor_active')) {
    function entox_is_elementor_active(){
        return did_action( 'elementor/loaded' );
    }
}

if (!function_exists('entox_is_woo_active')) {
    function entox_is_woo_active(){
        return class_exists('woocommerce');    
    }
}

if (!function_exists('entox_is_blog_archive')) {
    function entox_is_blog_archive() {
        return (is_home() && is_front_page()) || is_archive() || is_category() || is_tag() || is_home();
    }
}



/* Get ID from Slug of Header Footer Builder - Post Type */
function entox_get_id_by_slug( $page_slug ) {
    $page = get_page_by_path( $page_slug, OBJECT, 'ova_framework_hf_el' ) ;
    if ($page) {
        return $page->ID;
    } else {
        return null;
    }
}


function entox_custom_text ($content = "",$limit = 15) {

    $content = explode(' ', $content, $limit);

    if (count($content)>=$limit) {
        array_pop($content);
        $content = implode(" ",$content).'';
    } else {
        $content = implode(" ",$content);
    }

    $content = preg_replace('`[[^]]*]`','',$content);
    
    return strip_tags( $content );
}



/**
 * Google Font sanitization
 *
 * @param  string   JSON string to be sanitized
 * @return string   Sanitized input
 */
if ( ! function_exists( 'entox_google_font_sanitization' ) ) {
    function entox_google_font_sanitization( $input ) {
        $val =  json_decode( $input, true );
        if( is_array( $val ) ) {
            foreach ( $val as $key => $value ) {
                $val[$key] = sanitize_text_field( $value );
            }
            $input = json_encode( $val );
        }
        else {
            $input = json_encode( sanitize_text_field( $val ) );
        }
        return $input;
    }
}


/* Default Primary Font in Customize */
if ( ! function_exists( 'entox_default_primary_font' ) ) {
    function entox_default_primary_font() {
        $customizer_defaults = json_encode(
            array(
                'font' => 'Roboto',
                'regularweight' => '100,200,300,400,500,600,700,800,900',
                'category' => 'Sans Serif'
            )
        );

        return $customizer_defaults;
    }
}



if ( ! function_exists( 'entox_woo_sidebar' ) ) {
    function entox_woo_sidebar(){
        if( class_exists('woocommerce') && is_product() ){
            return get_theme_mod( 'woo_product_layout', 'woo_layout_1c' );
        }else{
            return get_theme_mod( 'woo_archive_layout', 'woo_layout_1c' );
        }
    }
}

if ( ! function_exists( 'entox_product_breadcrumbs' ) ) {
    function entox_product_breadcrumbs( $id, $args, $search_url = '' ) {
        if ( ! $id ) {
            global $post;
        } else {
            $post = get_post( $id ); // WPCS: override ok.
        }

        $crumbs[] = array( wp_strip_all_tags( $args['home'] ), home_url() );

        $terms = wc_get_product_terms(
            $post->ID,
            'product_cat',
            apply_filters(
                'entox_wc_breadcrumb_product_terms_args',
                array(
                    'orderby' => 'parent',
                    'order'   => 'DESC',
                )
            )
        );

        if ( $terms ) {
            $first_term = apply_filters( 'entox_wc_breadcrumb_main_term', $terms[0], $terms );
            $ancestors  = get_ancestors( $first_term->term_id, 'product_cat' );
            $ancestors  = array_reverse( $ancestors );

            foreach ( $ancestors as $ancestor ) {
                $ancestor = get_term( $ancestor, 'product_cat' );

                if ( ! is_wp_error( $ancestor ) && $ancestor ) {
                    if ( $search_url ) {
                        $term_link  = esc_url( $search_url ) . '?cat=' . trim( $ancestor->slug );
                        $crumbs[]   = array( wp_strip_all_tags( $ancestor->name ), $term_link );
                    } else {
                        $crumbs[] = array( wp_strip_all_tags( $ancestor->name ), get_term_link( $ancestor ) );
                    }
                }
            }

            if ( $search_url ) {
                $term_link  = esc_url( $search_url ) . '?cat=' . trim( $first_term->slug );
                $crumbs[]   = array( wp_strip_all_tags( $first_term->name ), $term_link );
            } else {
                $crumbs[] = array( wp_strip_all_tags( $first_term->name ), get_term_link( $first_term ) );
            }
        }

        $crumbs[] = array( wp_strip_all_tags( get_the_title( $post ) ), get_permalink( $post ) );

        return $crumbs;
    }
}
