<?php if (!defined( 'ABSPATH' )) exit;

if( !class_exists('Entox_Main') ){

	class Entox_Main {

		public function __construct() {
           	
           	
			/* Add theme support */
			add_action( 'after_setup_theme', array( $this, 'entox_theme_support' ) );
	

			/**
			 * Register Menu
			 */
			add_action( 'init', array( $this, 'entox_register_menus' ) );

			

			/**
			 * Load google font from customize
			 */
			add_action('wp_enqueue_scripts', array( $this, 'entox_load_google_fonts' ) );

			/**
			 * Add Body class
			 */
			add_filter('body_class', array( $this, 'entox_body_classes' ) );
			

			/**
			 * Enqueue CSS, Javascript
			 */
			add_action('wp_enqueue_scripts', array( $this, 'entox_enqueue_scripts' ) );

			/**
			 * Enqueue style from customize
			 */
			add_action('wp_enqueue_scripts', array( $this, 'entox_enqueue_customize' ), 11 );
			

        }



		function entox_theme_support(){

			$GLOBALS['content_width'] = apply_filters('entox_content_width', 800);
		    

		    add_theme_support('title-tag');

		    // Adds RSS feed links to <head> for posts and comments.
		    add_theme_support( 'automatic-feed-links' );

		    // Switches default core markup for search form, comment form, and comments    
		    // to output valid HTML5.
		    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );

		    add_theme_support( 'responsive-embeds' );

		    /* See http://codex.wordpress.org/Post_Formats */
		    add_theme_support( 'post-formats', array( 'image', 'gallery', 'audio', 'video') );

		    add_theme_support( 'post-thumbnails' );
		    
		    add_theme_support( 'custom-header' );
		    add_theme_support( 'custom-background');

		    add_theme_support('responsive-embeds');

		    add_theme_support( 'woocommerce' );
		    
		    add_theme_support( 'ova_framework_hf_el' );

		    add_filter('gutenberg_use_widgets_block_editor', '__return_false');
            add_filter('use_widgets_block_editor', '__return_false');

            add_image_size( 'entox_thumb_product', '600', '400', true );
		    
		}



		function entox_register_menus() {
		  register_nav_menus( array(
		    'primary'   => esc_html__( 'Primary Menu', 'entox' )

		  ) );
		}


		
		function entox_load_google_fonts(){

		    $fonts_url = '';

		    $default_primary_font = json_decode( entox_default_primary_font() );
		   
		    $custom_fonts = get_theme_mod('ova_custom_font','');

		    // Primary Font 
		    $primary_font = json_decode( get_theme_mod( 'primary_font' ) ) ? json_decode( get_theme_mod( 'primary_font' ) ) : $default_primary_font;
		    $primary_font_family = $primary_font->font;
		    $primary_font_weights_string = $primary_font->regularweight ? $primary_font->regularweight : '100,200,300,400,500,600,700,800,900';
		    $is_custom_primary_font = $custom_fonts != '' ? !strpos($primary_font_family, $custom_fonts) : true;


		   
		    
		    
		    $general_flag = _x( 'on', $primary_font_family.': on or off', 'entox');
		   
		    

		 
		    if ( 'off' !== $general_flag  ) {
		        $font_families = array();
		 
		        if ( 'off' !== $general_flag && $is_custom_primary_font ) {
		            $font_families[] = $primary_font_family.':'.$primary_font_weights_string;
		        }
		 
		        


		        if($font_families != null){
		          $query_args = array(
		              'family' => urlencode( implode( '|', $font_families ) )
		          );  
		          $fonts_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );
		        }
		        
		    }
		 
		    $google_fonts =  esc_url_raw( $fonts_url );

		    /**
			 * Load google font from customize
			 */
			wp_enqueue_style( 'ova-google-fonts', $google_fonts, array(), null );

		}


		function entox_body_classes( $classes ){

            global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;
            if ($is_lynx) {
                $classes[] = 'lynx';
            } elseif ($is_gecko) {
                $classes[] = 'gecko';
            } elseif ($is_opera) {
                $classes[] = 'opera';
            } elseif ($is_NS4) {
                $classes[] = 'ns4';
            } elseif ($is_safari) {
                $classes[] = 'safari';
            } elseif ($is_chrome) {
                $classes[] = 'chrome';
            } elseif ($is_IE) {
                $classes[] = 'ie';
            }

            if ($is_iphone) {
                $classes[] = 'iphone';
            }

            // Adds a class to blogs with more than 1 published author.
            if (is_multi_author()) {
                $classes[] = 'group-blog';
            }

            // Add class when using homepage template + featured image.
            if (has_post_thumbnail()) {
                $classes[] = 'has-post-thumbnail';
            }

            

            $classes[] = apply_filters( 'entox_theme_sidebar','' );

            $classes[] = entox_woo_sidebar();

            $wide_site = apply_filters( 'entox_wide_site', '' );
            if( $wide_site == 'boxed' ){
				$classes[] = 'container_boxed';
            }


            return $classes;
        }



		

		function entox_enqueue_scripts() {

			if ( is_singular( 'product' ) ) {
		    	/* Google Maps */
				if ( get_option( 'ova_brw_google_key_map', false ) ) {
					wp_enqueue_script( 'google_map','https://maps.googleapis.com/maps/api/js?key='.get_option( 'ova_brw_google_key_map', '' ).'&libraries=places', false, true );
				} else {
					wp_enqueue_script( 'google_map_product','https://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places', array('jquery'), false, true);
				}
		    }

		    // enqueue the javascript that performs in-link comment reply fanciness
		    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		        wp_enqueue_script( 'comment-reply' ); 
		    }

		    // Carousel
			wp_enqueue_script('carousel', ENTOX_URI.'/assets/libs/carousel/owl.carousel.min.js', array('jquery'),null,true);
			wp_enqueue_style('carousel', ENTOX_URI.'/assets/libs/carousel/assets/owl.carousel.min.css', array(), null);

		    // Font Icon
		    wp_enqueue_style('ovaicon', ENTOX_URI.'/assets/libs/ovaicon/font/ovaicon.css', array(), null);
		    wp_enqueue_style('entoxicon', ENTOX_URI.'/assets/libs/entoxicon/font/entoxicon.css', array(), null);
		    wp_enqueue_style('flaticon', ENTOX_URI.'/assets/libs/flaticon/font/flaticon.css', array(), null);

		    wp_enqueue_script('entox-script', ENTOX_URI.'/assets/js/script.js', array('jquery'),null,true);
		    wp_enqueue_script('entox-woo', ENTOX_URI.'/assets/js/woo.js', array('jquery'),null,true);

		    if ( is_singular( 'product' ) ) {
				wp_enqueue_script('fancybox', ENTOX_URI.'/assets/libs/fancybox/fancybox.umd.js', array('jquery'),null,true);
				wp_enqueue_style('fancybox', ENTOX_URI.'/assets/libs/fancybox/fancybox.css', array(), null);
			}

		    wp_enqueue_style( 'entox-style', get_template_directory_uri() . '/style.css' );
		    
		}

		

        function entox_enqueue_customize(){

        	$css = '';
           
			$primary_color = get_theme_mod( 'primary_color', '#0aa5cd' );

			$text_color = get_theme_mod( 'text_color', '#677283' );

			$heading_color = get_theme_mod( 'heading_color', '#17202d' );
			


			/* Primary Font */
			$default_primary_font = json_decode( entox_default_primary_font() );
			$primary_font = json_decode( get_theme_mod( 'primary_font' ) ) ? json_decode( get_theme_mod( 'primary_font' ) ) : $default_primary_font;
			$primary_font_family = $primary_font->font;

			/* General Typo */
			$general_font_size = get_theme_mod( 'general_font_size', '16px' );
			$general_line_height = get_theme_mod( 'general_line_height', '1.8em' );
			$general_letter_space = get_theme_mod( 'general_letter_space', '0px' );

			
			
			// Width Sidebar
			$global_layout_sidebar = apply_filters( 'entox_get_layout', '' );
			$width_sidebar = is_array( $global_layout_sidebar ) ? $global_layout_sidebar[1] : '0';

			// Container width
			$container_width = get_theme_mod( 'global_boxed_container_width', '1290' );
			$container_width_break = $container_width + 60;
			$boxed_offset = get_theme_mod( 'global_boxed_offset', '20' );


			// WooCommerce Sidebar
			$woo_archive_layout = get_theme_mod( 'woo_archive_layout', 'layout_1c' );
			$woo_sidebar_width = get_theme_mod( 'woo_sidebar_width', '320' );


            $css .= '--primary: '.$primary_color.';';

            $css .= '--text: '.$text_color.';';

            $css .= '--heading: '.$heading_color.';';
           

            $css .= '--primary-font: '.$primary_font_family.';';
            $css .= '--font-size: '.$general_font_size.';';
            $css .= '--line-height: '.$general_line_height.';';
            $css .= '--letter-spacing: '.$general_letter_space.';';

           

            $css .= '--width-sidebar: '.$width_sidebar.'px;';
            $css .= '--main-content:  calc( 100% - '.$width_sidebar.'px )'.';';

            $css .= '--container-width: '.$container_width.'px;';

            $css .= '--boxed-offset: '.$boxed_offset.'px;';

            $css .= '--woo-layout: '.$woo_archive_layout.';';
            $css .= '--woo-width-sidebar: '.$woo_sidebar_width.'px;';
            $css .= '--woo-main-content:  calc( 100% - '.$woo_sidebar_width.'px )'.';';
            

            $var = ":root{{$css}}";

            $var .= '@media (min-width: 1024px) and ( max-width: '.$container_width_break.'px ){
		        body .row_site,
		        body .elementor-section.elementor-section-boxed>.elementor-container{
		            max-width: 100%;
		            padding-left: 30px;
		            padding-right: 30px;
		        }
		    }';

            wp_add_inline_style( 'entox-style', $var );

        }

        


	}

}

return new Entox_Main();