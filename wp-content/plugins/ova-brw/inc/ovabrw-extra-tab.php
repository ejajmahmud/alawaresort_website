<?php defined( 'ABSPATH' ) || exit();

function ovabrw_woo_new_product_tab( $tabs ) {

	// Add Request Booking Tab
    $product_id = get_the_id();

    $product = wc_get_product( $product_id );

	if( ovabrw_get_setting( get_option( 'ova_brw_template_show_request_booking', 'yes' ) ) === 'yes' && $product->get_type() == 'ovabrw_car_rental' ){
		$tabs['ovabrw_reqest_booking'] = array(
			'title' 	=> esc_html__( 'Request for booking', 'ova-brw' ),
			'priority' 	=> (int)ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_order_tab', 9 ) ),
			'callback' 	=> 'ovabrw_woo_request_booking_tab_content'
		);
	}


	// Add Extra Tab
	$ovabrw_manage_extra_tab = get_post_meta( $product_id, 'ovabrw_manage_extra_tab', true );

	switch( $ovabrw_manage_extra_tab ) {
		case 'in_setting' : {
			$short_code_form = ovabrw_get_setting( get_option('ova_brw_extra_tab_shortcode_form', '') );
			break;
		}
		case 'new_form' : {
			$short_code_form = get_post_meta( $product_id, 'ovabrw_extra_tab_shortcode', true );
			break;
		}

		case 'no' : {
			$short_code_form = '';
			break;
		}

		default: {
			$short_code_form = ovabrw_get_setting( get_option('ova_brw_extra_tab_shortcode_form', '') );
			break;
		}

	}

	if ( ovabrw_get_setting( get_option( 'ova_brw_template_show_extra_tab', 'yes' ) ) === 'yes' && $short_code_form != ''   ) {
		$tabs['ovabrw_extra_tab'] = array(
			'title' 	=> esc_html__( 'Extra Tab', 'ova-brw' ),
			'priority' 	=> (int)ovabrw_get_setting( get_option( 'ova_brw_extra_tab_order_tab', 21 ) ),
			'callback' 	=> 'ova_new_product_tab_extra_tab'
		);
	}
	
	return $tabs;

}

function ova_new_product_tab_extra_tab() {

	return ovabrw_get_template( 'single/contact_form.php' );

}

function ovabrw_woo_request_booking_tab_content() { 

	return ovabrw_get_template( 'single/request_booking.php' );
	
 }



/* Send mail in Request for booking */
function ovabrw_request_booking( $data ){
    // Get subject setting
    $subject = ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_subject', esc_html__('Request For Booking', 'ova-brw') ) );
    if ( empty( $subject ) ) {
        $subject = esc_html__('Request For Booking', 'ova-brw');
    }

    // Get email setting
    $mail_to_setting = ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_from_email', get_option( 'admin_email' ) ) );

    if ( empty( $mail_to_setting ) ) {
        $mail_to_setting = get_option( 'admin_email' );
    }

    $mail_to = array( $mail_to_setting, $data['email'] );

    if ( apply_filters( 'ovabrw_remove_email_admin_in_request_booking', false ) ) {
        $mail_to = array( $data['email'] );
    }

    $body = '';

    $product_name   = isset( $data['product_name'] ) ? $data['product_name'] : '';
    $product_id     = isset( $data['product_id'] ) ? $data['product_id'] : '';

    if ( function_exists( 'wcfm_get_vendor_id_by_post' ) ) {
        $vendor_id = wcfm_get_vendor_id_by_post( $product_id );

        if ( $vendor_id ) {
            $vendor_user    = get_userdata( $vendor_id );
            $vendor_email   = $vendor_user->user_email;

            if ( $vendor_email && ! in_array( $vendor_email, $mail_to ) ) {
                array_push( $mail_to , $vendor_email );
            }
        }
    }    

    $name       = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';
    $email      = isset( $data['email'] ) ? sanitize_text_field( $data['email'] ) : '';
    $number     = isset( $data['number'] ) ? sanitize_text_field( $data['number'] ) : '';
    $address    = isset( $data['address'] ) ? sanitize_text_field( $data['address'] ) : '';
    $ovabrw_pickup_loc  = isset( $data['ovabrw_pickup_loc'] ) ? sanitize_text_field( $data['ovabrw_pickup_loc'] ) : '';
    $ovabrw_pickoff_loc = isset( $data['ovabrw_pickoff_loc'] ) ? sanitize_text_field( $data['ovabrw_pickoff_loc'] ) : '';
    $pickup_date  = isset( $data['pickup_date'] ) ? sanitize_text_field( $data['pickup_date'] ) : '';
    $pickoff_date = isset( $data['pickoff_date'] ) ? sanitize_text_field( $data['pickoff_date'] ) : '';
    $ovabrw_resource_checkboxs = isset( $data['ovabrw_resource_checkboxs'] ) ? $data['ovabrw_resource_checkboxs'] : '';

    $ovabrw_service = isset( $data['ovabrw_service'] ) ? $data['ovabrw_service'] : [];

    $extra = isset( $data['extra'] ) ? $data['extra'] : '';
    $ovabrw_period_package_id = isset( $data['ovabrw_period_package_id'] ) ? sanitize_text_field( $data['ovabrw_period_package_id'] ) : '';
    $ovabrw_number_vehicle = isset( $data['ovabrw_number_vehicle'] ) ? sanitize_text_field( $data['ovabrw_number_vehicle'] ) : 1;

    $ovabrw_rental_type = get_post_meta( $product_id, 'ovabrw_price_type', true );


    $ovabrw_service_id = get_post_meta( $product_id, 'ovabrw_service_id', true );  
    $ovabrw_service_name = get_post_meta( $product_id, 'ovabrw_service_name', true );  
    $ovabrw_service_price = get_post_meta( $product_id, 'ovabrw_service_price', true );  
    $ovabrw_service_duration_type = get_post_meta( $product_id, 'ovabrw_service_duration_type', true );  
    $ovabrw_service_str = '';
    if( ! empty( $ovabrw_service ) && is_array( $ovabrw_service ) ){
        foreach( $ovabrw_service as $id_service ){

            if( $id_service && $ovabrw_service_id && is_array( $ovabrw_service_id ) ){
                foreach ($ovabrw_service_id as $key_id => $value_id_arr) {
                    if( in_array($id_service, $value_id_arr) ) {
                        foreach ($value_id_arr as $k => $v) {
                            if( $id_service == $v ){

                                $ovabrw_service_duration_type_str = $ovabrw_service_duration_type[$key_id][$k];
                                $price_service = $ovabrw_service_price[$key_id][$k];
                                $ovabrw_service_name_str = $ovabrw_service_name[$key_id][$k];

                                $ovabrw_service_str .= $ovabrw_service_name_str . ': ' . $price_service . ' (' . $ovabrw_service_duration_type_str . ')<br>';

                            }
                        }
                    }
                }
            }
        }
    }


    $date_format = ovabrw_get_date_format();
    $time_format = ovabrw_get_time_format_php();

    if( $ovabrw_rental_type === 'transportation' ) {
        $time_dropoff = ovabrw_get_time_by_pick_up_off_loc_transport( $product_id, $ovabrw_pickup_loc, $ovabrw_pickoff_loc );
        $time_dropoff = 60 * $time_dropoff;
        
        $pickoff_date =  strtotime( $pickup_date ) + $time_dropoff;
        $pickoff_date = wp_date( $date_format . ' ' . $time_format, $pickoff_date );
    }

    // get order
    $order = $product_id ? '<h2>'. esc_html__( 'Order details: ', 'ova-brw' ) . '</h2><table><tr><td>' . esc_html__( 'Product: ', 'ova-brw' ).'</td><td><a href="'.get_permalink($product_id).'">'.$product_name.'</a><td></tr>' : '';

    $order .= $ovabrw_period_package_id ? esc_html__( 'Package: ', 'ova-brw' ).$ovabrw_period_package_id.'<br/>' : '';

    $order .= $name ? '<tr><td>' . esc_html__( 'Name: ', 'ova-brw' ) . '</td><td>' . $name . '</td></tr>' : '';
    $order .= $email ? '<tr><td>' . esc_html__( 'Email: ', 'ova-brw' ) . '</td><td>' . $email . '</td></tr>' : '';

    if( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_number', 'yes' ) ) == 'yes' ){
        $order .= $number ? '<tr><td>' . esc_html__( 'Phone: ', 'ova-brw' ) . '</td><td>' . $number . '</td></tr>' : '';
    }

    if( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_address', 'yes' ) ) == 'yes' ){
        $order .= $address ? '<tr><td>' . esc_html__( 'Address: ', 'ova-brw' ) . '</td><td>' . $address . '</td></tr>' : '';
    }

    if( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_pickup_location', 'no' ) ) == 'yes' ){
        $order .= $ovabrw_pickup_loc ? '<tr><td>' . esc_html__( 'Pick-up Location: ', 'ova-brw' ) . '</td><td>' . $ovabrw_pickup_loc . '</td></tr>' : '';
    }

    if( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_pickoff_location', 'no' ) ) == 'yes' ){
        $order .= $ovabrw_pickoff_loc ? '<tr><td>' . esc_html__( 'Drop-off Location: ', 'ova-brw' ) . '</td><td>' . $ovabrw_pickoff_loc . '</td></tr>' : '';
    }

    if( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_number_vehicle', 'no' ) === 'yes' ) ){
        $order .= $ovabrw_number_vehicle ? '<tr><td>' . esc_html__( 'Quantity: ', 'ova-brw' ) . '</td><td>' . $ovabrw_number_vehicle . '</td></tr>' : '';
    }

    if( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_pickup_date', 'yes' ) ) == 'yes' ){
        $order .= $pickup_date ? '<tr><td>' . esc_html__( 'Pick-up Date: ', 'ova-brw' ) . '</td><td>' . $pickup_date . '</td></tr>' : '';
    }

    if( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_pickoff_date', 'yes' ) ) == 'yes' ){
        $order .= $pickoff_date ? '<tr><td>' . esc_html__( 'Drop-off Date: ', 'ova-brw' ) . '</td><td>' . $pickoff_date . '</td></tr>' : '';
    }
    
    
    if( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_extra_service', 'yes' ) ) == 'yes' ){
        $order .= '<tr><td>' . esc_html__( 'Resource: ', 'ova-brw' );
        $resource = $ovabrw_resource_checkboxs ? implode(', ', $ovabrw_resource_checkboxs) : '';
        $order .= '</td><td>' . $resource . '</td></tr>';
    }

    if( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_service', 'yes' ) ) === 'yes' ){
        $order .= $ovabrw_service_str;
    }
    

    if( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_extra_info', 'yes' ) ) == 'yes' ){
        $order .= $extra ? '<tr><td>' . esc_html__( 'Extra: ', 'ova-brw' ) . '</td><td>' . $extra . '</td></tr><table>' : '';
    }

    // Get Email Content
    $body = apply_filters( 'ovabrw_request_booking_content_mail', ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_content', esc_html__( 'You have hired a vehicle: [ovabrw_vehicle_name] at [ovabrw_order_pickup_date] - [ovabrw_order_pickoff_date]. [ovabrw_order_details]', 'ova-brw' ) ) ) );
    if ( empty( $body ) ) {
        $body = esc_html__( 'You have hired a vehicle: [ovabrw_vehicle_name] at [ovabrw_order_pickup_date] - [ovabrw_order_pickoff_date]. [ovabrw_order_details]', 'ova-brw' );
    }

    $body = str_replace('[ovabrw_vehicle_name]', '<a href="'.get_permalink($product_id).'" target="_blank">'.$product_name.'</a>', $body);

    // Replace body
    $body = str_replace('[ovabrw_order_pickup_date]', $pickup_date, $body);
    $body = str_replace('[ovabrw_order_pickoff_date]', $pickoff_date, $body);
    $body = str_replace('[ovabrw_order_pickoff_date]', $pickoff_date, $body);
    $body = str_replace('[ovabrw_order_details]', $order, $body);

    return ovabrw_ovabrw_sendmail( $mail_to, $subject, $body );

}


function ova_wp_mail_from(){
    return $mail_to_setting = ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_from_email', get_option( 'admin_email' ) ) );
}

function ova_wp_mail_from_name(){
    $ova_wp_mail_from_name = ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_from_name', esc_html__( 'Request For Booking', 'ova-brw' ) ) );
    if ( empty( $ova_wp_mail_from_name ) ) {
        $ova_wp_mail_from_name = esc_html__( 'Request For Booking', 'ova-brw' );
    }
    return $ova_wp_mail_from_name;
}

function ovabrw_ovabrw_sendmail( $mail_to, $subject, $body ){

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=".get_bloginfo( 'charset' )."\r\n";
    
    add_filter( 'wp_mail_from', 'ova_wp_mail_from' );
    add_filter( 'wp_mail_from_name', 'ova_wp_mail_from_name' );

    if( wp_mail($mail_to, $subject, $body, $headers ) ){
        $result = true;
    }else{
        $result = false;
    }

    remove_filter( 'wp_mail_from', 'ova_wp_mail_from');
    remove_filter( 'wp_mail_from_name', 'ova_wp_mail_from_name' );

    return $result;
}

