<?php defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Ovabrw_Cron' ) ) {

	/**
	 * Class Ovabrw_Mail
	 */
	class Ovabrw_Cron {

		public $hook_remind_pickup_date = 'ovabrw_cron_hook_remind_pickup_date';
		public $time_repeat_remind_pickup_date = 'time_repeat_remind_pickup_date';
		/**
		 * Ovabrw_Cron constructor.
		 */
		public function __construct() {

			add_filter( 'cron_schedules', array( $this, 'ovabrw_add_cron_interval' ) );
			add_action( 'init', array( $this, 'ovabrw_check_scheduled' ) );
			register_deactivation_hook( __FILE__, array( $this, 'ovabrw_deactivate_cron' ) ); 

			add_action( $this->hook_remind_pickup_date, array( $this, 'ovabrw_remind_event_time' ) );
			
		}

		

		public function ovabrw_check_scheduled(){

			if ( ! wp_next_scheduled( $this->hook_remind_pickup_date ) ) {
			    wp_schedule_event( time(), $this->time_repeat_remind_pickup_date, $this->hook_remind_pickup_date );
			}

		}

		/**
		 * init time repeat hook
		 * @param  array $schedules 
		 * @return array schedule
		 */
		public function ovabrw_add_cron_interval( $schedules ) {



			$remind_mail_send_per_seconds = intval( ovabrw_get_setting( get_option( 'remind_mail_send_per_seconds', 86400 ) ) );

		    $schedules[$this->time_repeat_remind_pickup_date] = array(
		        'interval' => $remind_mail_send_per_seconds,
		        'display' => sprintf( esc_html__( 'Every % seconds', 'ova-brw' ), $remind_mail_send_per_seconds )
		    );

		    return $schedules;
		}


		public function ovabrw_deactivate_cron() {
		    $timestamp = wp_next_scheduled( $this->hook_remind_pickup_date );
		    wp_unschedule_event( $timestamp, $this->hook_remind_pickup_date );
		}


		public function ovabrw_remind_event_time(){

			if( ovabrw_get_setting( get_option( 'remind_mail_enable', 'yes' ) ) == 'no' ) return;

			$send_x_day = intval( ovabrw_get_setting( get_option( 'remind_mail_before_xday', 1 ) ) );

			$send_before_x_time = current_time('timestamp') + $send_x_day*24*60*60;

			$order_ids = ovabrw_get_orders_feature();
			
			foreach ( $order_ids as $key => $order_id ) {
					
				$order = wc_get_order( $order_id );

				// Get billing mail
				$customer_mail = $order->get_billing_email();

				// Get Meta Data type line_item of Order
    	    	$order_line_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );

				foreach ( $order_line_items as $item_id => $item ) {

					$product_name = $item->get_name();
					$product_id = $item->get_product_id();

					foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
                            
                        if( $meta->key == 'ovabrw_pickup_date' &&  strtotime( $meta->value ) > current_time('timestamp') && strtotime( $meta->value ) < $send_before_x_time && apply_filters( 'ovabrw_reminder_other_condition', true, $item ) ){

                            $ovabrw_pickup_date = $meta->value;

                            ovabrw_mail_remind_event_time( $order, $customer_mail, $product_name, $product_id, $ovabrw_pickup_date );
                        }

                    }
				}
				

			}
			

		}

	}
}

new Ovabrw_Cron();