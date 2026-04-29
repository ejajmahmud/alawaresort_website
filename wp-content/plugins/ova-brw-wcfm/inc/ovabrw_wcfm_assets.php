<?php defined( 'ABSPATH' ) || exit();

if( !class_exists( 'OVABRW_WCFM_Assets' ) ){
	class OVABRW_WCFM_Assets{

		public function __construct(){
			
			add_action('wp_enqueue_scripts', array( $this, 'ovabrw_wcfm_enqueue_scripts' ) );
			
			
		}

		public function ovabrw_wcfm_enqueue_scripts(){


			//fullcalendar
			if( defined('OVABRW_PLUGIN_URI') ){

				wp_enqueue_script('moment', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/moment.min.js', array('jquery'),null,true);
			    wp_enqueue_script('fullcalendar', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/main.js', array('jquery'),null,true);
			    wp_enqueue_script('locale-all', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/locales-all.js', array('jquery'),null,true);
			    wp_enqueue_style('fullcalendar', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/main.min.css', array(), null);

				

				wp_enqueue_script('calendar_booking', OVABRW_PLUGIN_URI.'assets/js/admin/calendar.js', array('jquery'), false, true );


				wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ) );
							
				wp_enqueue_script('jquery-timepicker', OVABRW_PLUGIN_URI.'assets/libs/jquery-timepicker/jquery.timepicker.min.js', array('jquery'), false, true);
				wp_enqueue_style('jquery-timepicker', OVABRW_PLUGIN_URI.'assets/libs/jquery-timepicker/jquery.timepicker.min.css' );


				// Date Time Picker
				wp_enqueue_script('datetimepicker', OVABRW_PLUGIN_URI.'assets/libs/datetimepicker/jquery.datetimepicker.js', array('jquery'), null, true );

				// Admin Css
				wp_enqueue_style('datetimepicker', OVABRW_PLUGIN_URI.'assets/libs/datetimepicker/jquery.datetimepicker.css', array(), null);
				wp_enqueue_style('ovabrw_admin', OVABRW_PLUGIN_URI.'assets/css/admin/ovabrw_admin.css', array(), null);


				//Admin js
				global $wp;
				if( isset( $wp->query_vars['wcfm-products-manage'] ) || isset( $wp->query_vars['wcfm-manage-order'] ) ) { // Add new product
					wp_enqueue_script('admin_script', OVABRW_PLUGIN_URI.'assets/js/admin/admin_script.min.js', array('jquery'),false,true);
				    wp_localize_script( 'admin_script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

				    // Map
					if ( get_option( 'ova_brw_google_key_map', false ) ) {
						wp_enqueue_script( 'google','https://maps.googleapis.com/maps/api/js?key='.get_option( 'ova_brw_google_key_map', '' ).'&loading=async&callback=Function.prototype&libraries=places', false, true );
					} else {
						wp_enqueue_script( 'google_map','https://maps.googleapis.com/maps/api/js?sensor=false&loading=async&callback=Function.prototype&libraries=places', array('jquery'), false, true);
					}
				}

			}

			wp_enqueue_script('brw_wcfm', OVABRW_WCFM_PLUGIN_URI.'assets/js/ovabrw-wcfm-script.js', array('jquery'),false,true);
			wp_localize_script( 'brw_wcfm', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

			wp_enqueue_style('ovabrw_wcfm', OVABRW_WCFM_PLUGIN_URI.'assets/css/ovabrw_wcfm.css', array(), null);
			

		}
		

	}
	new OVABRW_WCFM_Assets();
}
