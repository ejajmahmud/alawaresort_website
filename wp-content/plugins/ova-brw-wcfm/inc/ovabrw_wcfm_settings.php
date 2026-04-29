<?php if ( !defined( 'ABSPATH' ) ) exit();

if( !class_exists( 'Ovabrw_WCFM_Settings' ) ) {

    class Ovabrw_WCFM_Settings {

        public function __construct(){
                
			add_filter( 'wc_settings_tab_ova-brw_settings', array( $this, 'ovabrw_wcfm_add_settings_tab' ), 10, 2 );

        }

        public function ovabrw_wcfm_add_settings_tab( $settings, $section ) {

        	$wcfm_settings = array();
        	
		  	switch ( $section ) {

		  		case 'manage_order':

	                $wcfm_settings = array(
	                   
	                    array(
	                        'title' => esc_html__( 'Vendor Settings', 'ova-brw-wcfm' ),
	                        'type'  => 'title',
	                        'id'    => 'ova_brw_vendor_manage_order_setting',
                            'desc'  => esc_html__( 'The fields are sorted ascending. To hide the field, enter the number: 0 or empty', 'ova-brw-wcfm' ),
	                    ),

                        array(
                            'name'      => esc_html__( 'Show ID', 'ova-brw-wcfm' ),
                            'type'      => 'number',
                            'class'     => '',
                            'default'   => 1,
                            'id'        => 'vendor_manage_order_show_id',
                        ),

                        array(
                            'name'      => esc_html__( 'Show Customer', 'ova-brw-wcfm' ),
                            'type'      => 'number',
                            'class'     => '',
                            'default'   => 2,
                            'id'        => 'vendor_manage_order_show_customer',
                        ),

                        array(
                            'name'      => esc_html__( 'Show Product', 'ova-brw-wcfm' ),
                            'type'      => 'number',
                            'class'     => '',
                            'default'   => 3,
                            'id'        => 'vendor_manage_order_show_product',
                        ),

                        array(
                            'name'      => esc_html__( 'Show Total', 'ova-brw-wcfm' ),
                            'type'      => 'number',
                            'class'     => '',
                            'default'   => 4,
                            'id'        => 'vendor_manage_order_show_total',
                        ),

                        array(
                            'name'      => esc_html__( 'Show Time', 'ova-brw-wcfm' ),
                            'type'      => 'number',
                            'class'     => '',
                            'default'   => 5,
                            'id'        => 'vendor_manage_order_show_time',
                        ),

                        array(
                            'name'      => esc_html__( 'Show Location', 'ova-brw-wcfm' ),
                            'type'      => 'number',
                            'class'     => '',
                            'default'   => 6,
                            'id'        => 'vendor_manage_order_show_location',
                        ),

                        array(
                            'name'      => esc_html__( 'Show Insurance', 'ova-brw-wcfm' ),
                            'type'      => 'number',
                            'class'     => '',
                            'default'   => 7,
                            'id'        => 'vendor_manage_order_show_insurance',
                        ),

                        array(
                            'name'      => esc_html__( 'Show Status', 'ova-brw-wcfm' ),
                            'type'      => 'number',
                            'class'     => '',
                            'default'   => 8,
                            'id'        => 'vendor_manage_order_show_status',
                        ),
                        
                        array(
                            'name'      => esc_html__( 'Show Actions', 'ova-brw-wcfm' ),
                            'type'      => 'number',
                            'class'     => '',
                            'default'   => 9,
                            'id'        => 'vendor_manage_order_show_actions',
                        ),

	                    array(
	                        'type' => 'sectionend',
	                        'id'   => 'ova_brw_vendor_manage_order_setting',
	                    ),  
	                );
	                
	            break;
		  	}

		  	$settings = array_merge( $settings , $wcfm_settings );

		  	return $settings;
		}


    }
}

new Ovabrw_WCFM_Settings();