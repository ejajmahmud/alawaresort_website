<?php

class Entox_Elementor {
	
	/**
	 * Construct
	 */
	function __construct() {
		// Register Header Footer Category in Pane
	    add_action( 'elementor/elements/categories_registered', [ $this, 'entox_add_category' ] );

	    // After register styles
	    add_action( 'elementor/frontend/after_register_styles', [ $this, 'entox_enqueue_styles' ] );

	    // After register scripts
	    add_action( 'elementor/frontend/after_register_scripts', [ $this, 'entox_enqueue_scripts' ] );
		
		// Regiter widgets
		add_action( 'elementor/widgets/register', [ $this, 'entox_include_widgets' ] );
		
		// Add new animations
		add_filter( 'elementor/controls/animations/additional_animations', [ $this, 'entox_add_animations' ], 10 , 0 );
		
		// Remove animations style from Elementor
		add_action( 'wp_enqueue_scripts', [ $this, 'entox_remove_animations_styles' ] );
	}

	/**
	 * Add category
	 */
	public function entox_add_category(  ) {
	    \Elementor\Plugin::instance()->elements_manager->add_category(
	        'hf',
	        [
	            'title' => esc_html__( 'Header Footer', 'entox' ),
	            'icon' 	=> 'fa fa-plug',
	        ]
	    );

	    \Elementor\Plugin::instance()->elements_manager->add_category(
	        'entox',
	        [
	            'title' => esc_html__( 'Entox', 'entox' ),
	            'icon' 	=> 'fa fa-plug',
	        ]
	    );
	}

	/**
	 * Widget social icons style
	 */
	public function entox_enqueue_styles() {
		// Widget social icons
        if ( defined( 'ELEMENTOR_ASSETS_PATH' ) && defined( 'ELEMENTOR_ASSETS_URL' ) ) {
        	if ( file_exists( ELEMENTOR_ASSETS_PATH . 'css/widget-social-icons.min.css' ) ) {
                wp_enqueue_style( 'widget-social-icons', ELEMENTOR_ASSETS_URL . 'css/widget-social-icons.min.css', [], ELEMENTOR_VERSION );
            }
        }
	}

	/**
	 * Enqueue scripts
	 */
	public function entox_enqueue_scripts() {
        $files = glob( get_theme_file_path( '/assets/js/elementor/*.js' ) );
        
        foreach ( $files as $file ) {
            $file_name = wp_basename( $file );
            $handle    = str_replace( ".js", '', $file_name );
            $src       = get_theme_file_uri( '/assets/js/elementor/' . $file_name );

            if ( file_exists( $file ) ) {
                wp_register_script( 'entox-elementor-' . $handle, $src, ['jquery'], false, true );
            }
        }
	}

	/**
	 * Include widget files
	 */
	public function entox_include_widgets( $widgets_manager ) {
        $files = glob( get_theme_file_path( 'elementor/widgets/*.php' ) );

        foreach ( $files as $file ) {
            $file = get_theme_file_path( 'elementor/widgets/' . wp_basename( $file ) );

            if ( file_exists( $file ) ) {
                require_once $file;
            }
        }
    }

    /**
     * Add animations
     */
    public function entox_add_animations() {
    	$animations = [
    		'Entox' => [
            	'ova-move-up' 		=> esc_html__( 'Move Up', 'entox' ),
                'ova-move-down' 	=> esc_html__( 'Move Down', 'entox' ),
                'ova-move-left'     => esc_html__( 'Move Left', 'entox' ),
                'ova-move-right'    => esc_html__( 'Move Right', 'entox' ),
                'ova-scale-up'      => esc_html__( 'Scale Up', 'entox' ),
                'ova-flip'          => esc_html__( 'Flip', 'entox' ),
                'ova-helix'         => esc_html__( 'Helix', 'entox' ),
                'ova-popup'			=> esc_html__( 'PopUp','entox' )
            ]
    	];

        return $animations;
    }

    /**
     * Remove animations style from Elementor
     */
	public function entox_remove_animations_styles() {
		// Deregister the stylesheet by handle
	    foreach ( $this->entox_add_animations() as $animations ) {
	    	if ( !empty( $animations ) && is_array( $animations ) ) {
	    		foreach ( array_keys( $animations ) as $animation ) {
	    			wp_deregister_style( 'e-animation-'.$animation );
	    			wp_enqueue_style( 'e-animation-'.$animation, ENTOX_URI.'/assets/scss/none.css', array(), null);
	    		}
	    	}
	    }
	}
}

return new Entox_Elementor();
