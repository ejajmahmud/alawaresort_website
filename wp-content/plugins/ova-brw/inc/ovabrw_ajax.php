<?php
if ( !defined( 'ABSPATH' ) ) exit();

add_action( 'wp_ajax_ovabrw_load_name_product', 'ovabrw_load_name_product' );
add_action( 'wp_ajax_nopriv_ovabrw_load_name_product', 'ovabrw_load_name_product' );
function ovabrw_load_name_product() {
	$keyword = isset($_POST['keyword']) ? wp_unslash( $_POST['keyword'] ) : '';

	$the_query = new WP_Query( array( 'post_type' => 'product' , 's' => $keyword, 'posts_per_page'=> '10') );
	?>

	<?php

	if( $the_query->have_posts() ) :
		while( $the_query->have_posts() ): $the_query->the_post();

			$title[] = get_the_title();

		endwhile;
		wp_reset_postdata();  
	endif;

	echo json_encode($title);

	wp_die();
}


add_action( 'wp_ajax_ovabrw_load_name_product_id', 'ovabrw_load_name_product_id' );
add_action( 'wp_ajax_nopriv_ovabrw_load_name_product_id', 'ovabrw_load_name_product_id' );
function ovabrw_load_name_product_id() {
	$keyword = isset($_POST['keyword']) ? wp_unslash( $_POST['keyword'] ) : '';

	$the_query = new WP_Query( array( 'post_type' => 'product' , 's' => $keyword, 'posts_per_page'=> '10') );
	?>

	<?php

	if( $the_query->have_posts() ) :
		while( $the_query->have_posts() ): $the_query->the_post();

			$title[] = get_the_title() . '(#'.get_the_id().')';

		endwhile;
		wp_reset_postdata();  
	endif;

	echo json_encode($title);

	wp_die();
}

add_action( 'wp_ajax_ovabrw_get_rental_type_product', 'ovabrw_get_rental_type_product' );
add_action( 'wp_ajax_nopriv_ovabrw_get_rental_type_product', 'ovabrw_get_rental_type_product' );
function ovabrw_get_rental_type_product() {

	$id_product = isset($_POST['id_product']) ? wp_unslash( $_POST['id_product'] ) : '';
	$rental_type = get_post_meta( $id_product, 'ovabrw_price_type', true );

	$data = [
		'rental_type' => $rental_type,
	];
	echo json_encode( $data );

	wp_die();
}


add_action( 'wp_ajax_ovabrw_get_id_vehicle_rest', 'ovabrw_get_id_vehicle_rest' );
add_action( 'wp_ajax_nopriv_ovabrw_get_id_vehicle_rest', 'ovabrw_get_id_vehicle_rest' );
function ovabrw_get_id_vehicle_rest() {

	$id_product 	= isset($_POST['id_product']) ? wp_unslash( $_POST['id_product'] ) : '';
	$pickup_date 	= isset($_POST['pickup_date']) ? strtotime(wp_unslash( $_POST['pickup_date'] )) : '';
	$dropp_off 		= isset($_POST['dropp_off']) ? strtotime(wp_unslash( $_POST['dropp_off'] )) : '';
	$pickup_loc 	= isset($_POST['pickup_loc']) ? wp_unslash( $_POST['pickup_loc'] ) : '';
	$dropoff_loc 	= isset($_POST['dropoff_loc']) ? wp_unslash( $_POST['dropoff_loc'] ) : '';
	$package_id 	= isset($_POST['package_id']) ? wp_unslash( $_POST['package_id'] ) : '';

	$new_input_date = ovabrw_new_input_date( $id_product, $pickup_date, $dropp_off, $package_id, $pickup_loc, $dropoff_loc );

	$pickup_date_new 	= $new_input_date['pickup_date_new'];
	$pickoff_date_new 	= $new_input_date['pickoff_date_new'];

	$data_vehicle 		= ova_validate_manage_store( $id_product, $pickup_date_new, $pickoff_date_new, $pickup_loc, $dropoff_loc, true, 'search' );
	if ( $data_vehicle ) {
		$data = $data_vehicle;
	} else {
		$data = [
			'error' => true,
			'start_date' => 'empty',
			'message' => esc_html__("Vehicle is not available for this time, Please book other time.", 'ova-brw'),
		];
	}
	
	echo json_encode( $data );

	wp_die();
}


add_action( 'wp_ajax_ovabrw_load_data_product_create_order', 'ovabrw_load_data_product_create_order' );
add_action( 'wp_ajax_nopriv_ovabrw_load_data_product_create_order', 'ovabrw_load_data_product_create_order' );
function ovabrw_load_data_product_create_order() {
	$name_product 	= isset($_POST['name_product']) ? sanitize_text_field( $_POST['name_product'] ) : '';
	$id_product 	= trim( sanitize_text_field( $name_product ) );

	$rental_type 	= get_post_meta( $id_product, 'ovabrw_price_type', true );
	$petime_id 		= get_post_meta( $id_product, 'ovabrw_petime_id', true );
	$petime_label 	= get_post_meta( $id_product, 'ovabrw_petime_label', true );
	$product_price 	= get_post_meta( $id_product, '_regular_price', true);
	$ovabrw_amount_insurance 	= get_post_meta( $id_product, 'ovabrw_amount_insurance', true );
	$ovabrw_id_vehicles 		= get_post_meta( $id_product, 'ovabrw_id_vehicles', true );
	$ovabrw_define_1_day 		= get_post_meta( $id_product, 'ovabrw_define_1_day', true);

	$unfixed_time 	= get_post_meta( $id_product, 'ovabrw_unfixed_time', true);

	if ( $rental_type == 'transportation' ) {
		// Get html pick-up location
		ob_start();
		ovabrw_get_locations_transport_html( 'ovabrw_pickoff_loc', 'required', '', $id_product, 'pickup' );
		$html_pickup_location = ob_get_contents(); 
		ob_end_clean();

		// Get html drop-off location
		ob_start();
		ovabrw_get_locations_transport_html( 'ovabrw_pickoff_loc', 'required', '', $id_product, 'dropoff' );
		$html_dropoff_location = ob_get_contents(); 
		ob_end_clean();
	} else {
		// Get html pick-up location
		ob_start();
		ovabrw_get_locations_html( 'ovabrw_pickoff_loc', 'required', '', $id_product, 'pickup' );
		$html_pickup_location = ob_get_contents(); 
		ob_end_clean();

		// Get html drop-off location
		ob_start();
		ovabrw_get_locations_html( 'ovabrw_pickoff_loc', 'required', '', $id_product, 'dropoff' );
		$html_dropoff_location = ob_get_contents(); 
		ob_end_clean();
	}

	// Show/hide Pick-up date
	$show_pickup_date 		= ovabrw_show_pick_date_product( $id_product, $type = 'pickup' );

	// Show/hide Drop-off date
	$show_pickoff_date 		= ovabrw_show_pick_date_product( $id_product, $type = 'dropoff' );

	// Get show number vehicle
	$show_number_vehicle 	= get_post_meta( $id_product, 'ovabrw_show_number_vehicle', true);

	// Get show pick-up, drop-off date location
	$show_pickup_loc 	= ovabrw_show_pick_location_product( $id_product, $type = 'pickup' );
	$show_pickoff_loc 	= ovabrw_show_pick_location_product( $id_product, $type = 'dropoff' );

	// Get html resources
	$html_resources 	= ovabrw_get_html_resources_order( $id_product );

	// Get html resources
	$html_services	 	= ovabrw_get_html_services_order( $id_product );

	$statuses 	= brw_list_order_status();
    $order_date = get_order_rent_time( $id_product, $statuses );

	$data = [
		'rental_type' 				=> $rental_type,
		'petime_id' 				=> $petime_id,
		'petime_label' 				=> $petime_label,
		'ovabrw_amount_insurance' 	=> $ovabrw_amount_insurance,
		'ovabrw_id_vehicles' 		=> $ovabrw_id_vehicles,
		'product_price' 			=> $product_price,
		'ovabrw_define_1_day' 		=> $ovabrw_define_1_day,
		'order_date' 				=> $order_date,
		'show_number_vehicle' 		=> $show_number_vehicle,
		'show_pickup_loc' 	  		=> $show_pickup_loc ? "yes" : "no",
		'show_pickoff_loc' 	  		=> $show_pickoff_loc ? "yes" : "no",
		'show_pickup_date'			=> $show_pickup_date ? 'yes' : 'no',
		'show_pickoff_date'			=> $show_pickoff_date ? 'yes' : 'no',
		'html_pickup_location'		=> $html_pickup_location,
		'html_dropoff_location' 	=> $html_dropoff_location,
		'html_resources' 			=> $html_resources,
		'html_services' 			=> $html_services,
		'unfixed_time'				=> $unfixed_time
	];

	echo json_encode( $data );

	wp_die();
}



add_action( 'wp_ajax_ovabrw_load_tag_product', 'ovabrw_load_tag_product' );
add_action( 'wp_ajax_nopriv_ovabrw_load_tag_product', 'ovabrw_load_tag_product' );
function ovabrw_load_tag_product() {
	$keyword = isset($_POST['keyword']) ? sanitize_text_field( $_POST['keyword'] ) : '';
	$args = array(
        'taxonomy'               => 'product_tag',
        'search' => $keyword
    );
    $the_query = new WP_Term_Query($args);

    $title = [];
    if ( $the_query->terms ) {
        foreach( $the_query->terms as $term ) {
            $title[] =  $term->name;
        }
        wp_reset_postdata();  
    }

	echo json_encode($title);

	wp_die();
}

add_action( 'wp_ajax_ovabrw_get_total_price_and_quantity', 'ovabrw_get_total_price_and_quantity' );
add_action( 'wp_ajax_nopriv_ovabrw_get_total_price_and_quantity', 'ovabrw_get_total_price_and_quantity' );
function ovabrw_get_total_price_and_quantity() {

	$id_product 	= isset($_POST['id_product']) ? trim( sanitize_text_field( $_POST['id_product'] ) ) : '';

	// Get rental type product
	$rental_type 	= get_post_meta( $id_product, 'ovabrw_price_type', true );

	// Check is Auto/Manual to make ID vehicle 
    $manage_store 	= get_post_meta( $id_product, 'ovabrw_manage_store', true );

	$start_date 	= isset($_POST['start_date']) ? trim( sanitize_text_field( $_POST['start_date'] ) ) : '';
	$end_date 		= isset($_POST['end_date']) ? trim( sanitize_text_field( $_POST['end_date'] ) ) : '';
	$id_package 	= isset($_POST['id_package']) ? trim( sanitize_text_field( $_POST['id_package'] ) ) : '';
	$number_vehicle = isset($_POST['number_vehicle']) ? trim( sanitize_text_field( $_POST['number_vehicle'] ) ) : 1;
	$pickup_loc 	= isset($_POST['pickup_loc']) ? trim( sanitize_text_field( $_POST['pickup_loc'] ) ) : '';
	$dropoff_loc 	= isset($_POST['dropoff_loc']) ? trim( sanitize_text_field( $_POST['dropoff_loc'] ) ) : '';

	$resources 		= isset($_POST['resources']) ? str_replace( '\\', '', $_POST['resources'] )  : '';
	$resources_data = (array) json_decode( $resources );

	$services 		= isset($_POST['services']) ? str_replace( '\\', '', $_POST['services'] )  : '';
	$services_data 	= (array) json_decode( $services );

	$new_input_date = ovabrw_new_input_date( $id_product, strtotime( $start_date ), strtotime( $end_date ), $id_package, $pickup_loc, $dropoff_loc );

	$pickup_date_new 	= $new_input_date['pickup_date_new'];
	$pickoff_date_new 	= $new_input_date['pickoff_date_new'];
	
	// For Rental Type: Period Time
	$price_type 		= get_post_meta( $id_product, 'ovabrw_price_type', true );
	$rental_info_period = get_rental_info_period( $id_product, $pickup_date_new, $price_type, $id_package );

	$cart_item['product_id'] 			= $id_product;
	$cart_item['ovabrw_number_vehicle'] = 1;
	$cart_item['resources'] 			= $resources_data;
	$cart_item['ovabrw_service'] 		= json_encode( $services_data );
	$cart_item['period_price'] 			= $rental_info_period['period_price'];

	// Get product price
	$product_price = (float)get_post_meta( $id_product, '_regular_price', true );
	
	// Get product detail
	$product_detail = get_real_price_detail( $product_price, $id_product, $pickup_date_new, $pickoff_date_new );

	if( $rental_type == 'period_time' ) {

		if( empty( $start_date ) ) {
			$data = [ 
				'error' => true,
				'start_date' => 'empty',
				'message' => esc_html__('Please Choose Pick-up Date', 'ova-brw'),
			];
		} elseif( empty( $id_package ) ) {
			$data = [ 
				'error' => true,
				'id_package' => 'empty',
				'message' => esc_html__('No Package', 'ova-brw'),
			];
		} else {
	
			$total = get_price_by_date( $id_product, $pickup_date_new, $pickoff_date_new, $cart_item, $pickup_loc, $dropoff_loc, $id_package );
			$total['line_total'] = $total['line_total'];
            $total['product_price'] = $total['line_total'];
			$data = $total;
		}

	} else {
		if( empty( $start_date ) ) {
			$data = [ 
				'error' => true,
				'start_date' => 'empty',
				'message' => esc_html__('Please Choose Pick-up Date', 'ova-brw'),
			];
		} elseif( empty( $end_date ) ) {
			$data = [ 
				'error' => true,
				'end_date' => 'empty',
				'message' => esc_html__('Please Choose Drop-off Date', 'ova-brw'),
			];
		} else {

			$total = get_price_by_date( $id_product, $pickup_date_new, $pickoff_date_new, $cart_item, $pickup_loc, $dropoff_loc, $id_package );

			if( $rental_type == 'hour' ) {
                $total['quantity'] = $total['quantity'] . ' Hours';
            } else if( $rental_type != 'period_time' ) {
                $total['quantity'] = $total['quantity'] . ' Days';
            }
            $total['line_total'] = $total['line_total'];
            $total['product_price'] = $product_detail;
			$data = $total;
		}
	}

	if ( empty( $number_vehicle ) || $number_vehicle < 0 ) {
		$data = [ 
			'error' => true,
			'start_date' => 'empty',
			'message' => esc_html__('Quantity does not exist or is less than 0', 'ova-brw'),
		];
	}

	$data_vehicle = ova_validate_manage_store( $id_product, $pickup_date_new, $pickoff_date_new, $pickup_loc, $dropoff_loc, true, 'search');

	$number_vehicle_available = 0;
	if ( $data_vehicle ) {
		$number_vehicle_available = $data_vehicle['number_vehicle_available'];
		if ( $number_vehicle_available && (int)$number_vehicle_available >= (int)$number_vehicle ) {
			$data['number_vehicle_available'] = (int)$number_vehicle_available;
		} else {
			$data = [
				'error' => true,
				'start_date' => 'empty',
				'message' => esc_html__( "Vehicle is not available for this time, Please book other time.", 'ova-brw' ),
			];
		}
		
	} else {
		$data = [
			'error' => true,
			'start_date' => 'empty',
			'message' => esc_html__( "Vehicle is not available for this time, Please book other time.", 'ova-brw' ),
		];
	}

	if ( isset( $data['line_total'] ) ) {
		$data['line_total'] *= $number_vehicle;
	}

	echo json_encode( $data );

	wp_die();
}


add_action( 'wp_ajax_ovabrw_get_package_by_time', 'ovabrw_get_package_by_time_cus' );
add_action( 'wp_ajax_nopriv_ovabrw_get_package_by_time', 'ovabrw_get_package_by_time_cus' );
function ovabrw_get_package_by_time_cus() {

	$id = isset($_POST['post_id']) ? sanitize_text_field( $_POST['post_id'] ) : '';
	$start_date = isset($_POST['startdate']) ? strtotime( $_POST['startdate'] ) : '';
	
	 // Total Car in the Store
    $total_car_store = ovabrw_get_total_stock( $id );
    
    $post_array_id = ovabrw_get_wpml_product_ids( $id );
    $statuses = brw_list_order_status();
    $orders_ids = ovabrw_get_orders_by_product_id( $id, $statuses );


    $car_using_this_time = array();

    // Get list package of period time
	$ovabrw_petime_id	= get_post_meta( $id, 'ovabrw_petime_id', true );

	$ovabrw_petime_label = get_post_meta( $id, 'ovabrw_petime_label', true );

    // For Order ID
    foreach ($orders_ids as $key => $value) {

        // Get Order Detail by Order ID
        $order = wc_get_order($value);

        // Get Meta Data type line_item of Order
        $order_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
       
        // For Meta Data
        foreach ( $order_items as $item_id => $item ) {

            $ovabrw_pickup_date_store = $ovabrw_pickoff_date_store = $id_vehicle = $quantity = '';

            // Check Line Item have item ID is Car_ID
            if( in_array( $item->get_product_id(), $post_array_id ) ){

                // Check time to Prepare before delivered
                $prepare_time = get_post_meta( $id, 'ovabrw_prepare_vehicle', true ) ? get_post_meta( $id, 'ovabrw_prepare_vehicle', true ) * 60 : 0;


                // Get value of pickup date, pickoff date
                foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
                    if( $meta->key == 'ovabrw_pickup_date' ){
                        $ovabrw_pickup_date_store = strtotime( $meta->value );
                    }
                    if( $meta->key == 'ovabrw_pickoff_date' ){
                        $ovabrw_pickoff_date_store = strtotime( $meta->value ) + $prepare_time;
                    }
                    if( $meta->key == 'id_vehicle' ){
                        $id_vehicle = trim( $meta->value );
                    }
                    if( $meta->key == 'ovabrw_number_vehicle' ){
                        $quantity = intval( $meta->value );
                    }
                }

                // Only compare date when "PickOff Date in Store" > "Current Time" becaue "PickOff Date Rent" have to > "Current Time"
                if( $ovabrw_pickoff_date_store >= current_time( 'timestamp' ) && ( $id_vehicle != '' || $quantity ) ){
                    
				    if( $ovabrw_petime_id ){ 
				        foreach ( $ovabrw_petime_id as $petime_id ) {

				        	$start_end_time_package = get_rental_info_period( $id, $start_date, 'period_time', $petime_id );
				        	$ovabrw_pickup_date = $start_end_time_package['start_time'];
				        	$ovabrw_pickoff_date = $start_end_time_package['end_time'];

				        	// Check rent time
		                    if( ! ($ovabrw_pickoff_date <= $ovabrw_pickup_date_store || $ovabrw_pickoff_date_store <= $ovabrw_pickup_date ) ){
		                    	for($i = 0; $i < $quantity; $i++ ) {
		                    		array_push( $car_using_this_time, $petime_id );
		                    	}
		                        
		                    }

				        }
				    }
                    

                }
                
            }

        }

    }

    // Package available
    $pack_avail = array( esc_html__( 'Select Package', 'ova-brw' ) );

    if( $ovabrw_petime_id ){ 
		foreach ( $ovabrw_petime_id as $key => $petime_id ) {

			$i = 0;
			foreach ($car_using_this_time as $v) {
				if( $petime_id == $v ){
					$i ++;
				}
			}
			if( $i < $total_car_store ){

				$pack_avail[ $petime_id ] = $ovabrw_petime_label[$key];
				
			} 
			
		}
	}

	
	echo json_encode( $pack_avail );

	wp_die();
}



add_action( 'wp_ajax_ovabrw_calculate_total', 'ovabrw_calculate_total', 10, 0 );
add_action( 'wp_ajax_nopriv_ovabrw_calculate_total', 'ovabrw_calculate_total', 10, 0 );
function ovabrw_calculate_total() {

	$id = isset($_POST['id']) ? sanitize_text_field( $_POST['id'] ) : '';

	$pickup_loc = isset($_POST['pickup_loc']) ? sanitize_text_field( $_POST['pickup_loc'] ) : '';
	$dropoff_loc = isset($_POST['dropoff_loc']) ? sanitize_text_field( $_POST['dropoff_loc'] ) : '';

	$pickup_date = isset($_POST['pickup_date']) ? sanitize_text_field( $_POST['pickup_date'] ) : '';
	$dropoff_date = isset($_POST['dropoff_date']) ? sanitize_text_field( $_POST['dropoff_date'] ) : '';
	$package_id = isset($_POST['package_id']) ? sanitize_text_field( $_POST['package_id'] ) : '';

	$quantity = isset($_POST['quantity']) ? sanitize_text_field( $_POST['quantity'] ) : 1;

	$deposit = isset($_POST['deposit']) ? sanitize_text_field( $_POST['deposit'] ) : '';

	$resources = isset($_POST['resources']) ? str_replace( '\\', '', $_POST['resources'] )  : '';

	$services = isset($_POST['services']) ? str_replace( '\\', '', $_POST['services'] )  : '';

	$services_array = (array) json_decode( $services );

	// Get new date
	$new_date = ovabrw_new_input_date( $id, strtotime($pickup_date), strtotime($dropoff_date), $package_id, $pickup_loc, $dropoff_loc );

	// For Rental Type: Period Time
	$price_type = get_post_meta( $id, 'ovabrw_price_type', true );
	$rental_info_period = get_rental_info_period( $id, $new_date['pickup_date_new'], $price_type, $package_id );

	$cart_item['product_id'] = $id;
	$cart_item['ovabrw_number_vehicle'] = $quantity;
	$cart_item['resources'] = (array) json_decode( $resources );
	$cart_item['ovabrw_service'] = json_encode( $services_array );
	$cart_item['period_price'] = $rental_info_period['period_price'];
	$cart_item['ova_type_deposit'] = $deposit;

	$total = get_price_by_date( $id, $new_date['pickup_date_new'], $new_date['pickoff_date_new'], $cart_item, $pickup_loc, $dropoff_loc, $package_id );
	$line_total = $total['line_total'];

	// Calculate When choose Deposit or Full Amonunt at fronend
	$sub_deposit_amount = $line_total;

	/**
	 * Check Vehicle availables
	 */

	// Check product in order
	$store_vehicle_rented = ovabrw_vehicle_rented_in_order( $id, $new_date['pickup_date_new'], $new_date['pickoff_date_new'] );

	// Check product in cart
	$cart_vehicle_rented  = ovabrw_vehicle_rented_in_cart( $id, 'cart', $new_date['pickup_date_new'], $new_date['pickoff_date_new'] );

	// Get manage store
	$manage_store = get_post_meta( $id, 'ovabrw_manage_store', true );

	// Get array vehicle available
    $data_vehicle = ovabrw_get_vehicle_available($id, $store_vehicle_rented, $cart_vehicle_rented, $quantity, $manage_store, $new_date['pickup_date_new'], $new_date['pickoff_date_new'], $pickup_loc, $dropoff_loc, false, $validate = 'cart' );

    // Number vehicle available
    $total['number_vehicle_available'] = $data_vehicle['number_vehicle_available'];

    // Deposit
    $deposit_enable = get_post_meta ( $id, 'ovabrw_enable_deposit', true );

    if ( $deposit_enable === 'yes' ) {

    	$value_deposit = ! empty( get_post_meta ( $id, 'ovabrw_amount_deposit', true ) ) ? floatval( get_post_meta ( $id, 'ovabrw_amount_deposit', true ) ) : 0;

    	$deposit_type  = get_post_meta ( $id, 'ovabrw_type_deposit', true );

    	if ( $deposit === 'deposit' ) {

    		if ( $deposit_type === 'percent' ) {
    			$line_total = ( $line_total * $value_deposit ) / 100;
    		} else {
    			$line_total = $value_deposit;
    		}
    	}
    }
	
	if( $total['line_total'] <= 0 ){
		echo 0;	
	}else{

		$total['line_total'] = wc_price( apply_filters( 'ovabrw_ajax_total_filter', $line_total ) );	
		echo json_encode($total);
		
	}

	wp_die();
}

add_action( 'wp_ajax_ovabrw_get_tax_in_cat', 'ovabrw_get_tax_in_cat', 10, 0 );
add_action( 'wp_ajax_nopriv_ovabrw_get_tax_in_cat', 'ovabrw_get_tax_in_cat', 10, 0 );
function ovabrw_get_tax_in_cat(){

	$cat_val = isset( $_POST['cat_val'] ) ?  $_POST['cat_val'] : '';

	$list_tax_values = array();
	
	
	$get_term = get_term_by( 'slug', $cat_val, 'product_cat' );
	$term_id = $get_term->term_id;

	$ovabrw_custom_tax = get_term_meta($term_id, 'ovabrw_custom_tax', true);
			
	if( $ovabrw_custom_tax ){
		foreach ($ovabrw_custom_tax as $key => $value) {
			if( !in_array($value, $list_tax_values) ){
				if( $value ){
					array_push( $list_tax_values, $value);		

				}
				
			}
			
		}
	}

	wp_send_json( $list_tax_values );
}


add_action( 'wp_ajax_ovabrw_get_dropoff_date_transportation', 'ovabrw_get_dropoff_date_transportation' );
add_action( 'wp_ajax_nopriv_ovabrw_get_dropoff_date_transportation', 'ovabrw_get_dropoff_date_transportation' );
function ovabrw_get_dropoff_date_transportation() {

	$date_format = ovabrw_get_date_format();
    $time_format = ovabrw_get_time_format_php();

	$id_product 	= isset($_POST['id_product']) ? sanitize_text_field( $_POST['id_product'] ) : '';
	$pickup_date 	= isset($_POST['pickup_date']) ? strtotime(sanitize_text_field( $_POST['pickup_date'] )) : '';
	
	$pickup_loc 	= isset($_POST['pickup_loc']) ? sanitize_text_field( $_POST['pickup_loc'] ) : '';
	$dropoff_loc 	= isset($_POST['dropoff_loc']) ? sanitize_text_field( $_POST['dropoff_loc'] ) : '';


	$time_dropoff = ovabrw_get_time_by_pick_up_off_loc_transport( $id_product, $pickup_loc, $dropoff_loc );

    // Get Second
    $time_dropoff_seconds = 60 * $time_dropoff;
    
    $dropoff_date_timestamp =  $pickup_date + $time_dropoff_seconds;
    $pickoff_date = wp_date( $date_format . ' ' . $time_format, $dropoff_date_timestamp );
	

	if ( $pickoff_date && $pickup_loc && $dropoff_loc ) {
		$data = [
			'error' => false,
			'dropoff' => $pickoff_date
		];
	} else {
		$data = [
			'error' => true,
			'start_date' => 'empty',
			'message' => esc_html__("Please insert Pick-up, Drop-off Location", 'ova-brw'),
		];
	}
	
	echo json_encode( $data );

	wp_die();
}

/**
 * ajax search map
 */
add_action( 'wp_ajax_ovabrw_search_map', 'ovabrw_search_map' );
add_action( 'wp_ajax_nopriv_ovabrw_search_map', 'ovabrw_search_map' );
function ovabrw_search_map() {
	$data = $_POST;

	$map_lat 	= isset( $data['map_lat'] ) ? floatval( $data['map_lat'] ) 	: '';
	$map_lng 	= isset( $data['map_lng'] ) ? floatval( $data['map_lng'] ) 	: '';
	$radius 	= isset( $data['radius'] ) 	? floatval( $data['radius'] ) 	: '';
	$radius_lenght = isset( $data['radius_lenght'] ) ? sanitize_text_field( $data['radius_lenght'] ) : 'km';

	/***** Query Radius *****/
	$args_query_radius = array(
		'post_type' 		=> 'product',
		'post_status' 		=> 'publish',
		'posts_per_page' 	=> -1
	);

	$the_query 	= new WP_Query( $args_query_radius);

	$post_in 	= $arr_distance = array();

	$posts = $the_query->get_posts();

	if ( $map_lat != '' || $map_lng != '' ) {
		foreach( $posts as $post )  {
			/* Latitude Longitude Search */
			$lat_search = deg2rad($map_lat);
			$lng_search = deg2rad($map_lng);

			/* Latitude Longitude Post */
			$lat_post = deg2rad( floatval( get_post_meta( $post->ID, 'ovabrw_latitude', true ) ) );
			$lng_post = deg2rad( floatval( get_post_meta( $post->ID, 'ovabrw_longitude', true ) ) );

			$lat_delta = $lat_post - $lat_search;
			$lon_delta = $lng_post - $lng_search;

			// $angle = 2 * asin(sqrt(pow(sin($lat_delta / 2), 2) + cos($lat_search) * cos($lat_post) * pow(sin($lon_delta / 2), 2)));
			$angle = acos(sin($lat_search) * sin($lat_post) + cos($lat_search) * cos($lat_post) * cos($lng_search - $lng_post));

			if ( is_nan( $angle ) ) {
				$angle = 0;
			}

			/* 6371 = the earth's radius in km */
			/* 3959 = the earth's radius in mi */
			$distance =  6371 * $angle;

			if ( 'mi' === $radius_lenght ) {
				$distance =  3959 * $angle;
			}

			if( $distance <= $radius || !$map_lat ) {
				array_push( $arr_distance, $distance );
				array_push( $post_in, $post->ID );
			}
		}

		wp_reset_postdata();
		array_multisort($arr_distance, $post_in);

	} else {
		foreach( $posts as $post )  {
			array_push( $post_in, $post->ID );
		}
	}

	if ( $map_lat && ! $post_in ) {
		$post_in = array('');
	}
	/***** End Query Radius *****/

	$sort 			= isset( $data['sort'] ) 		? sanitize_text_field( $data['sort'] ) 			: '';
	$order 			= isset( $data['order'] ) 		? sanitize_text_field( $data['order'] ) 		: '';
	$orderby 		= isset( $data['orderby'] ) 	? sanitize_text_field( $data['orderby'] ) 		: '';
	$per_page 		= isset( $data['per_page'] ) 	? sanitize_text_field( $data['per_page'] ) 		: '';
	$paged 			= isset( $data['paged'] ) 		? (int)$data['paged']  							: 1;
	$name 			= isset( $data['name'] ) 		? sanitize_text_field( $data['name'] ) 			: '';
    $pickup_loc 	= isset( $data['pickup_loc'] ) 	? sanitize_text_field( $data['pickup_loc'] ) 	: '';
    $pickoff_loc 	= isset( $data['dropoff_loc'] ) ? sanitize_text_field( $data['dropoff_loc'] ) 	: '';
    $pickup_date 	= isset( $data['start_date'] )  ? strtotime( $data['start_date'] ) 				: '';
    $pickoff_date 	= isset( $data['end_date'] ) 	? strtotime( $data['end_date'] ) 				: '';
    $category 		= isset( $data['cat'] ) 		? sanitize_text_field( $data['cat'] ) 			: '';
    $type 			= isset( $data['type'] ) 		? sanitize_text_field( $data['type'] ) 			: '';
    $column 		= isset( $data['column'] ) 		? sanitize_text_field( $data['column'] ) 		: '';
    $attribute 		= isset( $data['attribute'] ) 	? sanitize_text_field( $data['attribute'] ) 	: '';
    $attr_value 	= isset( $data['attr_value'] ) 	? sanitize_text_field( $data['attr_value'] ) 	: '';
    $tags 			= isset( $data['tags'] ) 		? sanitize_text_field( $data['tags'] ) 			: '';
    $taxonomies 	= isset( $data['taxonomies'] ) 	? sanitize_text_field( $data['taxonomies'] )	: '';

    $show_featured 	= isset( $data['show_featured'] ) ? sanitize_text_field( $data['show_featured'] ) : '';

    $taxonomies 	= str_replace( '\\', '',  $taxonomies);
	if( $taxonomies ){
		$taxonomies = json_decode( $taxonomies, true );
	}

    $args_base = array(
		'post_type'   		=> 'product',
		'post_status' 		=> 'publish',
		'fields' 			=> 'ids',
		'posts_per_page' 	=> -1
	);

	if ( 'yes' === $show_featured ) {
		$args_base['tax_query'] = array(
			array(
				'taxonomy' => 'product_visibility',
			    'field'    => 'name',
			    'terms'    => 'featured',
			    'operator' => 'IN'
			),
		);
	}

    // sort
    $args_order 	= ! empty( $order ) 	? array( 'order' => $order ) 		: array( 'order' => 'DESC' );
    $args_orderby 	= ! empty( $orderby ) 	? array( 'orderby' => $orderby ) 	: array( 'orderby' => 'title' );

    if ( 'featured' === $orderby ) {
    	$args_orderby = array( 'orderby' => 'date' );
    }

    switch ($sort) {
		case 'date-desc':
		$args_orderby =  array( 'orderby' => 'date' );
		break;

		case 'date-asc':
		$args_orderby = array( 'orderby' => 'date' );
		$args_order = array( 'order' => 'ASC' );
		break;

		case 'near':
		$args_orderby = array( 'orderby' => 'post__in');
		$args_order = array( 'order' => 'ASC' );
		break;

		case 'a-z':
		$args_orderby = array( 'orderby' => 'title');
		$args_order = array( 'order' => 'ASC' );
		break;

		case 'z-a':
		$args_orderby = array( 'orderby' => 'title');
		break;

		default:
		break;
	}

	$args_basic = array_merge_recursive( $args_base, $args_order, $args_orderby );

	$args_radius = $args_name = $args_taxonomy = $args_tax_attr = $item_ids = array();

	// Query Result
	if ( $post_in ) {
		$args_radius = array( 'post__in' => $post_in );
	}

	// Query Name
	if ( $name ){
		$args_name = array( 's' => $name );
	}

	// Query Categories
	if ( $category ){
		$args_tax_attr[] = [
            'taxonomy' 	=> 'product_cat',
            'field' 	=> 'slug',
            'terms' 	=> $category
        ];
	}

	// Query Attribute
	if ( $attribute ) {
        $args_tax_attr[] = [
            'taxonomy' 	=> 'pa_' . $attribute,
            'field' 	=> 'slug',
            'terms' 	=> [$attr_value],
            'operator'  => 'IN',
        ];
    }

    // Query Tags
	if ( $tags ) {
		$args_tax_attr[] = [
            'taxonomy' 	=> 'product_tag',
            'field' 	=> 'name',
            'terms' 	=> $tags
        ];
	}

	// Query taxonomy custom
    if ( $taxonomies && is_array( $taxonomies ) ) {
    	foreach ( $taxonomies as $slug => $value) {
    		$taxo_name = isset( $data[$slug] ) ? sanitize_text_field( $data[$slug] ) : '';
    		if ( $taxo_name ) {
    			$args_tax_attr[] = [
		            'taxonomy' 	=> $slug,
		            'field' 	=> 'slug',
		            'terms' 	=> $taxo_name
		        ];
    		}
    	}
    }

	// Query taxonomy
	if ( ! empty( $args_tax_attr )) {
        $args_taxonomy = array(
            'tax_query' => array(
                'relation'  => 'AND',
                $args_tax_attr
            )
        );
    }

	$args = array_merge_recursive( $args_basic, $args_radius, $args_name, $args_taxonomy );

	// Get All products
    $items = new WP_Query( apply_filters( 'ovabrw_ft_items_query_search_map', $args, $data ));

    if ( $items->have_posts() ) : while ( $items->have_posts() ) : $items->the_post();

        // Product ID
        $id = get_the_id();

        // Set Pick-up, Drop-off Date again
        $new_input_date 	= ovabrw_new_input_date( $id, $pickup_date, $pickoff_date, '', $pickup_loc, $pickoff_loc );

        $pickup_date_new 	= $new_input_date['pickup_date_new'];
        $pickoff_date_new 	= $new_input_date['pickoff_date_new'];

        $ova_validate_manage_store = ova_validate_manage_store( $id, $pickup_date_new, $pickoff_date_new, $pickup_loc, $pickoff_loc, $passed = false, $validate = 'search' ) ;
        
        if ( $ova_validate_manage_store && $ova_validate_manage_store['status'] ){
            array_push( $item_ids, $id );
        }

    endwhile; else :

        $result = '<div class="not_found_product">'. esc_html( 'Not found product', 'ova-brw' ) .'</div>';
    	$results_found = '<div class="results_found"><span>'. esc_html__( '0 Result Found', 'ova-brw' ) .'</span></div>';

    endif; wp_reset_postdata();

    if ( $item_ids ) {

    	$featured_product = array();
    	if ( 'featured' === $orderby ) {
    		$featured_product_ids = wc_get_featured_product_ids();
    		foreach( $featured_product_ids as $featured_product_id ) {
    			if ( in_array( $featured_product_id, $item_ids ) ) {
    				array_push( $featured_product, $featured_product_id );
    			}
    		}

    		if ( $featured_product ) {

    			if ( 'ASC' === $order ) {
    				sort( $featured_product );
    			}

    			if ( 'DESC' === $order ) {
    				rsort( $featured_product );
    			}

    			$item_ids = array_unique(array_merge_recursive( $featured_product, $item_ids ));

    			$args_orderby = array( 'orderby' => 'post__in' );
    		}
    	}

        $args_product = array(
            'post_type' 		=> 'product',
            'posts_per_page' 	=> $per_page,
            'paged' 			=> $paged,
            'post_status' 		=> 'publish',
            'post__in' 			=> $item_ids,
            'fields' 			=> 'ids'
        );

        $args_query_product = array_merge_recursive( $args_product, $args_order, $args_orderby );

        $products = new WP_Query( apply_filters( 'ovabrw_ft_query_search_map', $args_query_product, $data ));

        ob_start();
        ?>

        <div class="ovabrw_product_archive <?php echo esc_attr( $type . ' '. $column ); ?>">
			<?php
				woocommerce_product_loop_start();
				if ( $products->have_posts() ) : while ( $products->have_posts() ) : $products->the_post();

					$id 			= get_the_id();
					$product 		= wc_get_product( $id );

					$html_price 	= ovabrw_get_html_price( $id );
					if ( $html_price ) {
						$data_html_price = htmlentities( $html_price );
					} else {
						$data_html_price = '';
					}

					$lat_product 	= get_post_meta( $id, 'ovabrw_latitude', true );
					$lng_product 	= get_post_meta( $id, 'ovabrw_longitude', true );

					wc_get_template( 'content-product.php', $data );
					?>

					<div class="data_product" style="display: none;"
						data-link_product="<?php echo esc_attr( get_the_permalink() ); ?>"
						data-title_product="<?php echo esc_attr( get_the_title() ); ?>"
						data-average_rating="<?php echo esc_attr( $product->get_average_rating() ); ?>"
						data-number_comment="<?php echo esc_attr( get_comments_number( $id ) ); ?>"
						data-map_lat_product="<?php echo esc_attr( $lat_product ); ?>"
						data-map_lng_product="<?php echo esc_attr( $lng_product ); ?>"
						data-thumbnail_product="<?php echo esc_url( wp_get_attachment_image_url( get_post_thumbnail_id() , 'thumbnail' ) ); ?>"
						data-html_price="<?php echo wp_kses_post( $data_html_price ); ?>">
					</div>

					<?php

				endwhile; else :
				?>
					<div class="not_found_product"><?php esc_html_e( 'Not found product', 'ova-brw' ); ?></div>
				<?php
				endif; wp_reset_postdata();
				woocommerce_product_loop_end();
			?>
		</div>

        <?php

        $total = $products->max_num_pages;

		if (  $total > 1 ): ?>
			<div class="ovabrw_pagination_ajax">
			<?php
				ovabrw_pagination_ajax( $products->found_posts, $products->query_vars['posts_per_page'], $paged );
			?>
			</div>
			<?php
		endif;

		$result = ob_get_contents(); 
		ob_end_clean();

		ob_start();
		?>
			<div class="results_found">
				<?php if ( $products->found_posts == 1 ): ?>
				<span>
					<?php echo sprintf( esc_html__( '%s Result Found', 'ova-brw' ), esc_html( $products->found_posts ) ); ?>
				</span>
				<?php else: ?>
				<span>
					<?php echo sprintf( esc_html__( '%s Results Found', 'ova-brw' ), esc_html( $products->found_posts ) ); ?>
				</span>
				<?php endif; ?>

				<?php if ( 1 == ceil( $products->found_posts/ $products->query_vars['posts_per_page']) && $products->have_posts() ): ?>
					<span>
						<?php echo sprintf( esc_html__( '(Showing 1-%s)', 'ova-brw' ), esc_html( $products->found_posts ) ); ?>
					</span>
				<?php elseif ( !$products->have_posts() ): ?>
					<span></span>
				<?php else: ?>
					<span>
						<?php echo sprintf( esc_html__( '(Showing 1-%s)', 'ova-brw' ), esc_html( $products->query_vars['posts_per_page'] ) ); ?>
					</span>
				<?php endif; ?>
			</div>

		<?php
		$results_found = ob_get_contents();
		ob_end_clean();

		echo json_encode( array( "result" => $result, "results_found" => $results_found ));
		wp_die();
    } else {
    	echo json_encode( array( "result" => $result, "results_found" => $results_found ));
    	wp_die();
    }
}

?>