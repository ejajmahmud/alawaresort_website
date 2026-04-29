<?php defined( 'ABSPATH' ) || exit();

// Get All Rooms of vendor
function get_all_rooms_vendors( $author_id ){
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => '-1',
        'post_status'   => 'publish',
        'author'	=> $author_id
    );
    $rooms = new WP_Query( $args );

    return $rooms;
}

// Get Order ID of a vendor
function get_order_vendors( $author_id, $post ) {
	global $wpdb; //This is used only if making any database queries

	$order_ids = array();

	$filter_order_status =  isset( $post['filter_order_status'] ) ? sanitize_text_field( $post['filter_order_status'] ) : '';

	if ( ! empty( $filter_order_status ) ) {
	    $order_status = array( $filter_order_status );
	} else {
	    $order_status = array( 'wc-processing','wc-completed', 'wc-half-completed', 'wc-on-hold', 'wc-cancelled', 'wc-closed' );    
	}

	$room_query = ( isset( $post['room_id'] ) && $post['room_id'] != '' ) ? 'AND order_item_meta.meta_value = '. sanitize_text_field( $post['room_id'] ) : '';

	$order_number 	= isset( $post['order_number'] ) ? (int)( $post['order_number'] ) : '';
	$name_customer 	= isset( $post['name_customer'] ) ? sanitize_text_field( $post['name_customer'] ) : '';

	if ( function_exists( 'ovabrw_wc_custom_orders_table_enabled' ) && ovabrw_wc_custom_orders_table_enabled() ) {
		$result = $wpdb->get_col("
            SELECT DISTINCT o.id
            FROM {$wpdb->prefix}wc_orders AS o
            LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oitems
            ON o.id = oitems.order_id
            LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
            ON oitems.order_item_id = oitem_meta.order_item_id
            WHERE oitems.order_item_type = 'line_item'
            AND oitem_meta.meta_key = '_product_id'
        ");
	} else {
		$result = $wpdb->get_col("
		    SELECT DISTINCT order_items.order_id
		    FROM {$wpdb->prefix}woocommerce_order_items as order_items
		    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
		    LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
		    WHERE posts.post_type = 'shop_order'
		    AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' )
		    AND order_items.order_item_type = 'line_item'
		    AND order_item_meta.meta_key = '_product_id'
		    $room_query
		");
	}

	$room_code_filter 	= isset( $post['room_code'] ) ? sanitize_text_field( $post['room_code'] ) : '';
	$from_day 			= isset( $post['from_day'] ) ? strtotime( $post['from_day'] ) : '';
	$to_day 			= isset( $post['to_day'] ) ? strtotime( $post['to_day'] ) : '';
	$check_in_out 		= isset( $post['check_in_out'] ) ? sanitize_text_field( $post['check_in_out'] ) : '';

	$check = $check_in_flag = $check_out_flag = $pickup_loc_flag = $pickoff_loc_flag = true;

	$current_pickup_loc 	= isset( $post['pickup_loc'] ) ? sanitize_text_field( $post['pickup_loc'] ) : '';
	$current_pickoff_loc 	= isset( $post['pickoff_loc'] ) ? sanitize_text_field( $post['pickoff_loc'] ) : '';
	$ovabrw_date_format 	= ovabrw_get_date_format();
	$ovabrw_time_format 	= ovabrw_get_time_format_php();
	$ovabrw_date_time_format = $ovabrw_date_format . ' ' . $ovabrw_time_format;

	foreach ( $result as $key => $value ) {
	    $order 		= wc_get_order( $value );
	    $fullname 	= $order->get_formatted_billing_full_name();
	    $is_vendor 	= false;

	    if ( ( empty( $order_number ) || ( ! empty( $order_number ) && $order_number == $value  ) ) && ( empty( $name_customer ) || ( strpos( $fullname, $name_customer ) ) !== false ) ) {

	        // Get Meta Data type line_item of Order
	        $order_line_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );

	        // For Meta Data
	        foreach ( $order_line_items as $item_id => $item ) {
	            $room_check_in 	= $room_check_out = $room_code = $pickup_loc = $pickoff_loc = '';
	            $room_name 		= $item->get_name();
	            
	            // Get value of check-in, check-out
	            foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
	                if ( $meta->key == 'ovabrw_pickup_date' ) {
	                    $room_check_in = date( $ovabrw_date_time_format, strtotime( $meta->value ) );
	                }
	                if ( $meta->key == 'ovabrw_pickoff_date' ) {
	                    $room_check_out = date( $ovabrw_date_time_format, strtotime( $meta->value ) );
	                }
	                if ( $meta->key == 'ovabrw_pickup_loc' ) {
	                    $pickup_loc = $meta->value;
	                }
	                if ( $meta->key == 'ovabrw_pickoff_loc' ) {
	                    $pickoff_loc = $meta->value;
	                }
	            }

	            // Check vendor id like current user id
	            $line_item 	= new WC_Order_Item_Product( $item );
				$product  	= $line_item->get_product();
				$product_id = $line_item->get_product_id();
				$vendor_id  = wcfm_get_vendor_id_by_post( $product_id );
				$roles 		= false;

				if ( $author_id ) {
					$user = get_userdata( $author_id );

					if ( is_a( $user, 'WP_User' ) ) {
						if ( in_array( 'administrator', (array) $user->roles ) ) $roles = true;
					}
				}

				if ( ( $vendor_id && (int)$vendor_id == (int)$author_id ) || $roles ) {
					$is_vendor = true;
				}

	            $room_check_in_timep 	= strtotime( $room_check_in );
	            $room_check_out_timep 	= strtotime( $room_check_out );

	            if ( $check_in_out == 'check_in' && $from_day != '' && $to_day != '' ) {
	                $check = ( $from_day <=  $room_check_in_timep && $room_check_in_timep <= $to_day ) ? true : false;
	            } elseif ( $check_in_out == 'check_out' ) {
	                $check = ( $from_day <=  $room_check_out_timep &&  $room_check_out_timep <= $to_day ) ? true : false;
	            } elseif ( $from_day != '' && $to_day != '' ) {
	                $check_in_flag = ( $from_day <=  $room_check_in_timep && $room_check_in_timep <= $to_day ) ? true : false;
	                $check_out_flag = ( $from_day <=  $room_check_out_timep &&  $room_check_out_timep <= $to_day ) ? true : false;
	            }

	            if ( $current_pickup_loc != '' && $pickup_loc != $current_pickup_loc ) {
	                $pickup_loc_flag = false;
	            } else {
	                $pickup_loc_flag = true;
	            }

	            if ( $current_pickoff_loc != '' && $pickoff_loc != $current_pickoff_loc ) {
	                $pickoff_loc_flag = false;
	            } else {
	                $pickoff_loc_flag = true;
	            }

	            $order_amount_insurance = $item['ovabrw_amount_insurance_product'];

	            if ( $order_amount_insurance == 0 ) {
	                $status_insurance = '<span class="ova_amount_status_insurance_column ova_paid">'.esc_html__("Paid for Customers", "ova-brw").'</span>';
	            } else {
	                $status_insurance = '<span class="ova_amount_status_insurance_column ova_pending">'.esc_html__("Received", "ova-brw").'</span>';
	            }

	            $order_amount_remaining = $item['ovabrw_remaining_amount_product'];

	            if ( $order_amount_remaining == 0 ) {
	                $status_deposit = '<span class="ova_amount_status_insurance_column ova_paid">'.esc_html__("Full Payment", "ova-brw").'</span>';
	            } else {
	                $status_deposit = '<span class="ova_amount_status_insurance_column ova_pending">'.esc_html__("Partial Payment", "ova-brw").'</span>';
	            }

	            if ( empty( $pickup_loc ) && empty( $pickoff_loc ) ) {
	                $pickup_off_loc =  '';
	            } else {
	                $pickup_off_loc =  $pickup_loc . ' @ ' . $pickoff_loc;
	            }

	            if ( $check && $check_in_flag && $check_out_flag && $pickup_loc_flag && $pickoff_loc_flag && $is_vendor && ! in_array( $order->get_ID(), $order_ids ) ) {

	            	array_push( $order_ids, $order->get_ID() );
	            }
	        }
	    }
	}

	return $order_ids;
}

// Get order status
function ovabrw_wcfm_get_order_status( $code ) {

	switch ( $code ) {
        case 'processing':
            $order_status_text = esc_html__( 'Processing', 'ova-brw-wcfm' );
            break;
        case 'completed':
            $order_status_text = esc_html__( 'Completed', 'ova-brw-wcfm' );
            break;
        case 'on-hold':
            $order_status_text = esc_html__( 'On hold', 'ova-brw-wcfm' );
            break;
        case 'cancelled':
            $order_status_text = esc_html__( 'Cancelled', 'ova-brw-wcfm' );
            break;    

        case 'closed':
            $order_status_text = esc_html__( 'Closed', 'ova-brw-wcfm' );
            break;        
        
        default:
            $order_status_text = esc_html__( 'Update Order', 'ova-brw-wcfm' );
            break;
    }
    return $order_status_text;
}

// Get field in manage order
function ovabrw_wcfm_get_field_text( $value ) {

	switch ( $value ) {
		case 'id':
			$filed = '<th>' . esc_html__( 'ID', 'ova-brw-wcfm' ) . '</th>';
			break;
		case 'customer':
			$filed = '<th>' . esc_html__( 'Customer', 'ova-brw-wcfm' ) . '</th>';
			break;
		case 'product':
			$filed = '<th>' . esc_html__( 'Product', 'ova-brw-wcfm' ) . '</th>';
			break;
		case 'total':
			$filed = '<th>' . esc_html__( 'Total', 'ova-brw-wcfm' ) . '</th>';
			break;
		case 'time':
			$filed = '<th>' . esc_html__( 'Time', 'ova-brw-wcfm' ) . '</th>';
			break;
		case 'location':
			$filed = '<th>' . esc_html__( 'Location', 'ova-brw-wcfm' ) . '</th>';
			break;
		case 'insurance':
			$filed = '<th>' . esc_html__( 'Insurance', 'ova-brw-wcfm' ) . '</th>';
			break;
		case 'status':
			$filed = '<th>' . esc_html__( 'Status', 'ova-brw-wcfm' ) . '</th>';
			break;
		case 'actions':
			$filed = '<th>' . esc_html__( 'Actions', 'ova-brw-wcfm' ) . '</th>';
			break;
		default:
			$filed = '';
			break;
	}

	return $filed;
}

// Sort fields in manage order
function ovabrw_wcfm_get_fields_order() {

	$args 		= $options = array();

	// Get option settings
	$id 		= get_option( 'vendor_manage_order_show_id', 1 );
	if ( $id ) {
		$options['id'] = $id;
	}

	$customer 	= get_option( 'vendor_manage_order_show_customer', 2 );
	if ( $customer ) {
		$options['customer'] = $customer;
	}

	$product 	= get_option( 'vendor_manage_order_show_product', 3 );
	if ( $product ) {
		$options['product'] = $product;
	}

	$total 		= get_option( 'vendor_manage_order_show_total', 4 );
	if ( $total ) {
		$options['total'] = $total;
	}

	$time 		= get_option( 'vendor_manage_order_show_time', 5 );
	if ( $time ) {
		$options['time'] = $time;
	}

	$location	= get_option( 'vendor_manage_order_show_location', 6 );
	if ( $location ) {
		$options['location'] = $location;
	}

	$insurance	= get_option( 'vendor_manage_order_show_insurance', 7 );
	if ( $insurance ) {
		$options['insurance'] = $insurance;
	}

	$status		= get_option( 'vendor_manage_order_show_status', 8 );
	if ( $status ) {
		$options['status'] = $status;
	}

	$actions	= get_option( 'vendor_manage_order_show_actions', 9 );
	if ( $actions ) {
		$options['actions'] = $actions;
	}

	if ( ! empty( $options ) ) {
		asort( $options );
	}

	foreach ( $options as $key => $value ) {
		array_push( $args, $key );
	}

	return apply_filters( 'ovabrw_wcfm_ft_get_fields_order', $args );
}



