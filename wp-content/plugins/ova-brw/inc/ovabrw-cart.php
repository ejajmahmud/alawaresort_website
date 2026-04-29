<?php
defined( 'ABSPATH' ) || exit();


// 1: Validate Booking Form And Rent Time
add_filter( 'woocommerce_add_to_cart_validation', 'ovabrw_validation_booking_form', 10, 5 );
if ( ! function_exists( 'ovabrw_validation_booking_form' ) ) {
    function ovabrw_validation_booking_form( $passed ) {

        // Check product type: rental
        $custom_product_type = filter_input( INPUT_POST, 'custom_product_type' );
        if( $custom_product_type != 'ovabrw_car_rental' ) return true;

        // Get Value From Booking Form
        $ovabrw_pickup_loc = trim( sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_pickup_loc' ) ) );
        $ovabrw_pickoff_loc = trim( sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_pickoff_loc' ) ) );
        $ovabrw_pickup_date = strtotime( sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_pickup_date' ) ) );
        $ovabrw_pickoff_date = strtotime( sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_pickoff_date' ) ) );

        $ovabrw_number_vehicle = sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_number_vehicle' ) );
        $ovabrw_number_vehicle = ! empty( $ovabrw_number_vehicle ) ? (int)$ovabrw_number_vehicle : 1;

        $ovabrw_period_package_id = filter_input( INPUT_POST, 'ovabrw_period_package_id' ) ? sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_period_package_id' ) ) : '';

        $car_id = sanitize_text_field( filter_input( INPUT_POST, 'car_id' ) );

        $defined_one_day = defined_one_day( $car_id );

        // Check Rental Type Type
        $rental_type = get_post_meta( $car_id, 'ovabrw_price_type', true );

        // Rent day min
        $ovabrw_rent_day_min = (int)get_post_meta( $car_id, 'ovabrw_rent_day_min', true );

        // Rent hour min
        $ovabrw_rent_hour_min = (int)get_post_meta( $car_id, 'ovabrw_rent_hour_min', true );
        
        // Set Pick-up, Drop-off Date again
        $new_input_date = ovabrw_new_input_date( $car_id, $ovabrw_pickup_date, $ovabrw_pickoff_date, $ovabrw_period_package_id, $ovabrw_pickup_loc, $ovabrw_pickoff_loc );

        $pickup_date_new = $new_input_date['pickup_date_new'];
        $pickoff_date_new = $new_input_date['pickoff_date_new'];


        // Error: Pick-up Location
        if( ovabrw_show_pick_location_product( $car_id, 'pickup' ) && empty( $ovabrw_pickup_loc ) ){
            wc_clear_notices();
            wc_add_notice( __("Insert PickUp Location", 'ova-brw'), 'error');
            return false;
        }

        // Error: Pick-off Location
        if( ovabrw_show_pick_location_product( $car_id, 'dropoff' ) && empty( $ovabrw_pickoff_loc ) ){
            wc_clear_notices();
            wc_add_notice( __("Insert PickOff Location", 'ova-brw'), 'error');
            return false;
        }

        // Error Pick Up Date < Current Time
        if( $ovabrw_number_vehicle < 1 ){
            wc_clear_notices();
            wc_add_notice( __("Please choose quantity greater 0", 'ova-brw'), 'error');   
            return false;
        }

        // Error Pick Up Date < Current Time
        if( $pickup_date_new < current_time('timestamp') ){
            wc_clear_notices();
            wc_add_notice( __("Pick-up Date must be greater than Current Time", 'ova-brw'), 'error');   
            return false;
        }

        // Check eror rental_type == period_time
        if( $rental_type == 'period_time' ) {
            if ( empty( $ovabrw_period_package_id ) ) {
                wc_clear_notices();
                wc_add_notice( __("No package selected", 'ova-brw'), 'error');
                return false;
            }
        }

        //check eror rental_type not transportation
        if( $rental_type !== 'transportation' ) {
            
            // Error empty Pick Up Date or Pick Off Date
            if ( empty( $pickup_date_new ) || empty( $pickoff_date_new ) ) {
                wc_clear_notices();
                wc_add_notice( __("Insert Pick-up, Drop-off date", 'ova-brw'), 'error');
                return false;
            }

            // Error Pick Up Date > Pick Off Date
            if( $pickup_date_new >  $pickoff_date_new){
                wc_clear_notices();
                wc_add_notice( __("Drop-off Date must be greater than Pick-up Date", 'ova-brw'), 'error');
                return false;
            }


        } else { //Error rental_type transportation

            //Error pickup loc not exists in product
            if( ! ovabrw_check_pickup_dropoff_loc_transport( $car_id, $ovabrw_pickup_loc, 'pickup' ) ){
                wc_clear_notices();
                wc_add_notice( __("Pick-up location is wrong", 'ova-brw'), 'error');
                return false;
            }

            //Error dropoff loc not exists in product
            if( ! ovabrw_check_pickup_dropoff_loc_transport( $car_id, $ovabrw_pickoff_loc, 'dropoff' ) ){
                wc_clear_notices();
                wc_add_notice( __("Drop-off location is wrong", 'ova-brw'), 'error');
                return false;
            }

            // Error empty Pick Up Date or Pick Off Date
            if ( empty( $pickup_date_new ) ) {
                wc_clear_notices();
                wc_add_notice( __("Insert Pick-up date", 'ova-brw'), 'error');
                return false;
            }
        } 

        // Check service
        if( isset($_POST['ovabrw_service']) && $ovabrw_service = $_POST['ovabrw_service'] ){
            $ovabrw_service_required = get_post_meta( $car_id, 'ovabrw_service_required', true );
            if( $ovabrw_service_required ){
                foreach ($ovabrw_service_required as $key => $value) {
                    if( $value == 'yes' ){
                        if( ! ( isset( $ovabrw_service[$key] ) && $ovabrw_service[$key] ) ){
                            wc_clear_notices();
                            wc_add_notice( __("Please choose Service", 'ova-brw'), 'error');   
                            return false;
                            break;
                        }
                    }
                }
            }
        }
        

        // Error Rent Time Min
        switch ($rental_type) {
            case 'day':
            if( ovabrw_val_day_min( $pickoff_date_new, $pickup_date_new, $ovabrw_rent_day_min ) ){
                
                wc_clear_notices();
                wc_add_notice( sprintf( esc_html__( 'Min Rental Period: %d day', 'ova-brw' ), $ovabrw_rent_day_min ), 'error');   
                return false;
            }
            break;

            case 'hour':
            if( ovabrw_val_hour_min( $pickoff_date_new, $pickup_date_new, $ovabrw_rent_hour_min ) ){

                wc_clear_notices();
                wc_add_notice( sprintf( esc_html__( 'Min Rental Hours %d hour', 'ova-brw' ), $ovabrw_rent_hour_min ), 'error');   
                return false;
            }
            break;


            case 'mixed':
            if( ovabrw_val_hour_min( $pickoff_date_new, $pickup_date_new, $ovabrw_rent_hour_min ) ){

                wc_clear_notices();
                wc_add_notice( sprintf( esc_html__( 'Min Rental Hours %d hour', 'ova-brw' ), $ovabrw_rent_hour_min ), 'error');   
               return false;
            }
            break;
        }

        
        
        $ova_validate_manage_store = ova_validate_manage_store( $car_id, $pickup_date_new, $pickoff_date_new, $ovabrw_pickup_loc, $ovabrw_pickoff_loc, $passed, $validate = 'cart', $ovabrw_number_vehicle );
        
        if ( !empty($ova_validate_manage_store) ) {
            return $ova_validate_manage_store['status'];
        }

        return false;
    }
}



// 2: Add Extra Data To Cart Item
add_filter( 'woocommerce_add_cart_item_data', 'ovabrw_add_extra_data_to_cart_item',10, 3 );
if ( ! function_exists( 'ovabrw_add_extra_data_to_cart_item' ) ) {
    function ovabrw_add_extra_data_to_cart_item( $cart_item_data, $product_id, $variation_id ) {

        // Check product type: rental
        $product = wc_get_product( $product_id );

        if ( $product->get_type() !== 'ovabrw_car_rental' ) return $cart_item_data;

        
        $ovabrw_pickup_loc = sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_pickup_loc' ) );
        $ovabrw_pickoff_loc = sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_pickoff_loc' ) );
        $ovabrw_pickup_date = sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_pickup_date' ) );
        $ovabrw_pickoff_date = sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_pickoff_date' ) );
        $ova_type_deposit = sanitize_text_field( filter_input( INPUT_POST, 'ova_type_deposit' ) );
        $ovabrw_number_vehicle = sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_number_vehicle' ) );

        $date_format = ovabrw_get_date_format();
        $time_format = ovabrw_get_time_format_php();
        
        $ovabrw_date_time_format = $date_format . ' ' . $time_format;

        $ovabrw_period_label = '';


        // Number vehicle ( quantity)
        $cart_item_data['ovabrw_number_vehicle'] = ( $ovabrw_number_vehicle > 0 ) ? $ovabrw_number_vehicle : 1;

        // Get Custom Field Booking Form
        $list_fields = get_option( 'ovabrw_booking_form', array() );

        if( is_array( $list_fields ) && ! empty( $list_fields ) ) {
            foreach( $list_fields as $key => $field ) {
                if( $field['enabled'] == 'on' ) {
                    $cart_item_data[$key] = sanitize_text_field( filter_input( INPUT_POST, $key ) );
                }
            }
        }


        // Define day 
        $defined_one_day = defined_one_day( $product_id );

        // When Price Type is Period, We have to Set Start Time, End Time again.
        $price_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

        // If rental type is Location
        if( $price_type === 'transportation' ) {

            $time_dropoff = ovabrw_get_time_by_pick_up_off_loc_transport( $product_id, $ovabrw_pickup_loc, $ovabrw_pickoff_loc );

            // Get Second
            $time_dropoff_seconds = 60 * $time_dropoff;
            
            $dropoff_date_timestamp =  strtotime( $ovabrw_pickup_date ) + $time_dropoff_seconds;
            $ovabrw_pickoff_date = wp_date( $date_format . ' ' . $time_format, $dropoff_date_timestamp );

        }


        // If rental type is Period
        if( $price_type == 'period_time' ){

            $start_date = sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_pickup_date' ) );
            $start_date = strtotime($start_date);
            $period_package_id = sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_period_package_id' ) );
            $rental_info_period = get_rental_info_period( $product_id, $start_date, $price_type, $period_package_id );

            $ovabrw_unfixed = get_post_meta( $product_id, 'ovabrw_unfixed_time', true );
            if( $ovabrw_unfixed == 'yes' ) {
                $date_time_format = $ovabrw_date_time_format;
            } else {
                $date_time_format = $date_format;

                if( $rental_info_period['package_type'] == 'inday' ){
                    $date_time_format = $ovabrw_date_time_format;    
                }
            }
            
            $ovabrw_pickup_date = $rental_info_period['start_time'] ? wp_date( $date_time_format, $rental_info_period['start_time'] ) : '';
            $ovabrw_pickoff_date = $rental_info_period['end_time'] ? wp_date( $date_time_format, $rental_info_period['end_time'] ) : '';


            $ovabrw_period_label = $rental_info_period['period_label'];
            $ovabrw_period_price = $rental_info_period['period_price'];
            $ovabrw_package_type = $rental_info_period['package_type'];
        }

        // If all fields is empty
        if ( empty( $ovabrw_pickup_loc ) && empty( $ovabrw_pickoff_loc ) && empty( $ovabrw_pickup_date ) && empty( $ovabrw_pickoff_date ) ) {
            return $cart_item_data;
        }

        // Get resource
        $ovabrw_resource_checkboxs = isset( $_POST['ovabrw_resource_checkboxs'] ) ? $_POST['ovabrw_resource_checkboxs'] : '';

        if (isset($_POST['booking_form_ajax_get_extra_resource']) && $_POST['booking_form_ajax_get_extra_resource'] == 'booking-form') {
            $data = $_POST['ovabrw_resource_checkboxs'];
            $data = str_replace('\\', '', $data);
            $data = json_decode( $data, true );
            $ovabrw_resource_checkboxs = $data;
        }

        // Get Services
        $ovabrw_service = isset( $_POST['ovabrw_service'] ) ? json_encode( $_POST['ovabrw_service'] ) : json_encode( [] );     

        
        // Add pick-up location
        if( ovabrw_show_pick_location_product( $product_id, 'pickup' ) ){
            $cart_item_data['ovabrw_pickup_loc'] = $ovabrw_pickup_loc;    
        }

        // Add drop-off location
        if( ovabrw_show_pick_location_product( $product_id, 'dropoff' ) ){
            $cart_item_data['ovabrw_pickoff_loc'] = $ovabrw_pickoff_loc;    
        }

        // Add define one day
        $cart_item_data['rental_type'] = $price_type;
        if( $price_type === 'day' ){
           $cart_item_data['define_day'] = $defined_one_day;
        }


        //real date
        $real_date = ovabrw_get_real_date( $price_type, $defined_one_day, $ovabrw_pickup_date, $ovabrw_pickoff_date );

        
        // add price transportation
        if( $price_type === 'transportation' ){
            $cart_item_data['price_transport'] = ovabrw_get_price_by_start_date_transport( $product_id, $ovabrw_pickup_loc, $ovabrw_pickoff_loc );
        }
        
        // add pick-up, drop-off date that the guest choose
        $cart_item_data['ovabrw_pickup_date'] = $ovabrw_pickup_date;
        $cart_item_data['ovabrw_pickoff_date'] = $ovabrw_pickoff_date;

        // add real date to compare in search
        $cart_item_data['ovabrw_pickup_date_real'] = $real_date['pickup_date_real'];
        $cart_item_data['ovabrw_pickoff_date_real'] = $real_date['pickoff_date_real'];

        // Add resource
        $cart_item_data['resources'] = $ovabrw_resource_checkboxs;

        // Add service
        $cart_item_data['ovabrw_service'] = $ovabrw_service;    


        if( $ovabrw_period_label ){
            $cart_item_data['rental_type'] = $price_type;
            $cart_item_data['period_label'] = $ovabrw_period_label;    
            $cart_item_data['period_price'] = $ovabrw_period_price;
            $cart_item_data['package_type'] = $ovabrw_package_type;
        }


        $id_vehicle_available = '';
        if( WC()->session->__isset( 'id_vehicle_available' ) ){
            $id_vehicle_available = WC()->session->get( 'id_vehicle_available' );
            WC()->session->__unset( 'id_vehicle_available' );
        }


        $cart_item_data['id_vehicle'] = trim( $id_vehicle_available );

        if( get_post_meta( $product_id, 'ovabrw_amount_insurance', true ) ){
            $cart_item_data['ovabrw_amount_insurance'] = $cart_item_data['ovabrw_number_vehicle'] * get_post_meta( $product_id, 'ovabrw_amount_insurance', true );
        }

        $deposit_enable = get_post_meta ( $product_id, 'ovabrw_enable_deposit', true );
        $cart_item_data['ova_enable_deposit'] = $deposit_enable;

        $ova_type_deposit = (trim($ova_type_deposit) === 'deposit') ? 'deposit' : 'full';
        $cart_item_data['ova_type_deposit'] = $ova_type_deposit;

        return $cart_item_data;
    }
}



// 3: Display Extra Data in the Cart
add_filter( 'woocommerce_get_item_data', 'ovabrw_display_extra_data_cart', 10, 2 );
if ( ! function_exists( 'ovabrw_display_extra_data_cart' ) ) {
    function ovabrw_display_extra_data_cart( $item_data, $cart_item ) {
        
        // Check product type: rental
        if( !$cart_item['data']->is_type('ovabrw_car_rental') ) return $item_data;
        
        if ( $item_data ) {
            unset( $item_data );
        }


        if ( empty( $cart_item['ovabrw_pickup_loc'] ) && empty( $cart_item['ovabrw_pickoff_loc'] ) && empty( $cart_item['ovabrw_pickup_date'] ) && empty( $cart_item['ovabrw_pickoff_date'] ) ) {

            wc_clear_notices();
            wc_add_notice( __("Insert full data in booking form", 'ova-brw'), 'notice');
            return false;

        }

        if( ovabrw_show_pick_location_product( $cart_item['product_id'], 'pickup' ) ){
            $item_data[] = array(
                'key'     => esc_html__( 'Pick-up Location', 'ova-brw' ),
                'value'   => wc_clean( $cart_item['ovabrw_pickup_loc'] ),
                'display' => '',
            );
        }

        if( ovabrw_show_pick_location_product( $cart_item['product_id'], 'dropoff' ) ){
            $item_data[] = array(
                'key'     => esc_html__( 'Drop-off Location', 'ova-brw' ),
                'value'   => wc_clean( $cart_item['ovabrw_pickoff_loc'] ),
                'display' => '',
            );
        }

        $ovabrw_date_format = ovabrw_get_date_format();
        $ovabrw_time_format = ovabrw_get_time_format_php();
        $ovabrw_date_time_format = $ovabrw_date_format . ' ' . $ovabrw_time_format;
        $date_time_format = $ovabrw_date_time_format;

        $price_type = get_post_meta( $cart_item['product_id'], 'ovabrw_price_type', true );
        if ( $price_type == 'period_time' ) {

            $ovabrw_unfixed = get_post_meta( $cart_item['product_id'], 'ovabrw_unfixed_time', true );
            if( $ovabrw_unfixed == 'yes' ) {
                $date_time_format = $ovabrw_date_time_format;
            } else {
                $date_time_format = $ovabrw_date_format;

                if( $cart_item['package_type'] == 'inday' ){
                    $date_time_format = $ovabrw_date_time_format;    
                }
            }
        }

        if( isset( $cart_item['ovabrw_pickup_date'] ) ) {

            if( ( isset( $cart_item['define_day'] ) && $cart_item['define_day'] == 'hotel' ) || ( ! ovabrw_check_time_to_book( $cart_item['product_id'], 'start' ) ) ) {
                $date_time_format = $ovabrw_date_format;
            }

            $ovabrw_pickup_date = date_i18n( $date_time_format, strtotime( $cart_item['ovabrw_pickup_date'] ) );
            $item_data[] = array(
                'key'     => esc_html__( 'Pick-up Date', 'ova-brw' ),
                'value'   => wc_clean( $ovabrw_pickup_date ),
                'display' => '',
            );
            
        }

        if( isset( $cart_item['ovabrw_pickoff_date'] ) ) {

            if( ( isset( $cart_item['define_day'] ) && $cart_item['define_day'] == 'hotel' ) || ( ! ovabrw_check_time_to_book( $cart_item['product_id'], 'end' ) ) ) {
                $date_time_format = $ovabrw_date_format;
            }

            $ovabrw_pickoff_date = date_i18n( $date_time_format, strtotime( $cart_item['ovabrw_pickoff_date'] ) );

            

            $item_data[] = array(
                'key'     => esc_html__( 'Drop-off Date', 'ova-brw' ),
                'value'   => wc_clean( $ovabrw_pickoff_date ),
                'display' => '',
            );
        }

        if( ovabrw_show_number_vehicle( $cart_item['product_id'] ) === 'yes' ){
            $item_data[] = array(
                'key'     => esc_html__( 'Quantity', 'ova-brw' ),
                'value'   => wc_clean( $cart_item['ovabrw_number_vehicle'] ),
                'display' => '',
            );
        }


        if( isset( $cart_item['ovabrw_service'] ) && $cart_item['ovabrw_service'] ){

            $ovabrw_service_id = get_post_meta( $cart_item['product_id'], 'ovabrw_service_id', true ); 
            $ovabrw_service_name = get_post_meta( $cart_item['product_id'], 'ovabrw_service_name', true );
            $ovabrw_label_service = get_post_meta( $cart_item['product_id'], 'ovabrw_label_service', true ); 
            $ovabrw_service = $cart_item['ovabrw_service'];
            $ovabrw_service_arr = json_decode( $ovabrw_service, true );
            if( ! empty( $ovabrw_service_arr ) && is_array( $ovabrw_service_arr ) ) {
                foreach( $ovabrw_service_arr as $val_ser ) {
                    if( ! empty( $ovabrw_service_id ) && is_array( $ovabrw_service_id ) ) {
                        foreach( $ovabrw_service_id as $key => $value ) {
                            if( is_array( $value ) && ! empty( $value ) ) {
                                foreach( $value as $k => $val ) {
                                    if( $val_ser == $val && ! empty( $val ) ){
                                        $name_service = $ovabrw_service_name[$key][$k];
                                        $label_service = $ovabrw_label_service[$key];
                                        $item_data[] = array(
                                            'key'     => $label_service,
                                            'value'   => wc_clean( $name_service ),
                                            'display' => '',
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if( isset( $cart_item['resources'] ) && $cart_item['resources'] ){
            foreach ($cart_item['resources'] as $key_re => $value_re) {
                $item_data[] = array(
                    'key'     => esc_html__( 'Resource', 'ova-brw' ),
                    'value'   => wc_clean( $value_re ),
                    'display' => '',
                );
            }    
        }


        $manage_store = get_post_meta( $cart_item['product_id'], 'ovabrw_manage_store', true );

        if ( $manage_store != 'store' ) {
            $id_vehicles_arr = json_decode( $cart_item['id_vehicle'], true );
            $id_vehicles_str = implode(',', $id_vehicles_arr);

            if( apply_filters( 'brw_show_vehicle_order_frontend', false ) ){
                $item_data[] = array(
                    'key'     => esc_html__( 'Id Vehicle', 'ova-brw' ),
                    'value'   => wc_clean( $id_vehicles_str ),
                    'display' => '',
                );
            }
        }
        
        if ( isset( $cart_item['ovabrw_amount_insurance'] ) && $cart_item['ovabrw_amount_insurance'] ) {
            $item_data[] = array(
                'key'     => esc_html__( 'Amount Of Insurance', 'ova-brw' ),
                'value'   => wc_price( $cart_item['ovabrw_amount_insurance'] ),
                'display' => '',
            );
        }

        

        if( isset( $cart_item['period_label'] ) && $cart_item['period_label'] ){
            $item_data[] = array(
                'key'     => esc_html__( 'Package', 'ova-brw' ),
                'value'   => wc_clean( trim( $cart_item['period_label'] ) ),
                'display' => '',
            );
        }

        $list_fields = get_option( 'ovabrw_booking_form', array() );

        if( is_array( $list_fields ) && ! empty( $list_fields ) ) {
            foreach( $list_fields as $key => $field ) {
                $value = array_key_exists( $key, $cart_item ) ? $cart_item[$key] : '';
                if( ! empty( $value ) && $field['enabled'] == 'on' ) {
                    $item_data[] = array(
                        'key'     => $field['label'],
                        'value'   => wc_clean( $value ),
                        'display' => '',
                    );
                }
                
            }
        }

        return $item_data;
    }
}

// 4: Checkout Validate
add_action('woocommerce_after_checkout_validation', 'ovabrw_after_checkout_validation');
if ( ! function_exists( 'ovabrw_after_checkout_validation' ) ) {
    function ovabrw_after_checkout_validation( $posted ) {

        foreach ( WC()->cart->get_cart() as $cart_item ) {

            $product = $cart_item['data'];

            $pickup_date =  isset( $cart_item['ovabrw_pickup_date'] ) ? strtotime( $cart_item['ovabrw_pickup_date'] ) : '';
            $pickoff_date = isset( $cart_item['ovabrw_pickoff_date'] ) ?  strtotime( $cart_item['ovabrw_pickoff_date'] ) : '';
            $pickup_loc =  isset( $cart_item['ovabrw_pickup_loc'] ) ? $cart_item['ovabrw_pickup_loc'] : '' ;
            $pickoff_loc = isset( $cart_item['ovabrw_pickoff_loc'] ) ? $cart_item['ovabrw_pickoff_loc'] : '';
            $ovabrw_number_vehicle = isset( $cart_item['ovabrw_number_vehicle'] ) ? (int)$cart_item['ovabrw_number_vehicle'] : 1;

            if ( !empty( $product ) && $product->is_type( 'ovabrw_car_rental' ) ) {

                $car_id = $product->get_id();

                // Set Pick-up, Drop-off Date again
                $new_input_date = ovabrw_new_input_date( $car_id, $pickup_date, $pickoff_date, '', $pickup_loc, $pickoff_loc );
                $pickup_date_new = $new_input_date['pickup_date_new'];
                $pickoff_date_new = $new_input_date['pickoff_date_new'];


                $ova_validate_manage_store = ova_validate_manage_store( $car_id, $pickup_date_new, $pickoff_date_new, $pickup_loc, $pickoff_loc, $passed = true, $validate = 'checkout', $ovabrw_number_vehicle );

                if ( !empty( $ova_validate_manage_store ) ) {
                    return $ova_validate_manage_store['status'];
                } else {
                    $errors->add( 'validation', sprintf( __('Vehicle %s isn\'t available for this time, Please book other time.', 'ova-brw'), $product->name ) );
                }
            }
        }

    }
}


// 5: Save to Order
add_action( 'woocommerce_checkout_create_order_line_item', 'ovabrw_add_extra_data_to_order_items', 10, 4 );
if ( ! function_exists( 'ovabrw_add_extra_data_to_order_items' ) ) {
    function ovabrw_add_extra_data_to_order_items( $item, $cart_item_key, $values, $order ) {
        $product_id = $item->get_product_id();

        // Check product type: rental
        $product = wc_get_product( $product_id );
        if ( $product->get_type() !== 'ovabrw_car_rental' ) return;

        if ( empty( $values['ovabrw_pickup_loc'] ) && empty( $values['ovabrw_pickoff_loc'] ) && empty( $values['ovabrw_pickup_date'] ) && empty( $values['ovabrw_pickoff_date'] ) ) {
            return;
        }

        if( ovabrw_show_pick_location_product( $product_id, 'pickup' ) ){
            $item->add_meta_data( 'ovabrw_pickup_loc', $values['ovabrw_pickup_loc'] );
        }
        if( ovabrw_show_pick_location_product( $product_id, 'dropoff' ) ){
            $item->add_meta_data( 'ovabrw_pickoff_loc', $values['ovabrw_pickoff_loc'] );
        }


        $item->add_meta_data( 'ovabrw_pickup_date', $values['ovabrw_pickup_date']);
        $item->add_meta_data( 'ovabrw_pickoff_date', $values['ovabrw_pickoff_date'] );

        $item->add_meta_data( 'ovabrw_pickup_date_real', $values['ovabrw_pickup_date_real']);
        $item->add_meta_data( 'ovabrw_pickoff_date_real', $values['ovabrw_pickoff_date_real'] );

        if( ovabrw_show_number_vehicle( $product_id ) === 'yes' ){
            if ( $values['ovabrw_number_vehicle'] ) {
                $item->add_meta_data( 'ovabrw_number_vehicle', $values['ovabrw_number_vehicle'] );
            } else {
                $item->add_meta_data( 'ovabrw_number_vehicle', 1 );
            }
            
        }

        if( isset( $values['rental_type'] ) && $values['rental_type'] == 'day' && isset( $values['define_day'] ) && ( $values['define_day'] == 'hotel' || $values['define_day'] == 'day' || $values['define_day'] == 'hour' ) ) {
            $item->add_meta_data( 'define_day', $values['define_day']);
        }
       

        $ovabrw_rental_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

        if( ( $ovabrw_rental_type == 'period_time' ) ){
            $item->add_meta_data( 'ovabrw_total_days', $real_quantity );
            $item->add_meta_data( 'ovabrw_price_detail', wp_strip_all_tags( wc_price( $values['period_price'] ) ) );

        }else{
            
            if( $ovabrw_rental_type == 'transportation' ) {
                
                $item->add_meta_data( 'ovabrw_total_days', intval(1));
                $item->add_meta_data( 'ovabrw_price_detail', wc_price( $values['price_transport'] ) );

            } else {

                $real_quantity = get_real_quantity( 1, $product_id, strtotime( $values['ovabrw_pickup_date'] ), strtotime( $values['ovabrw_pickoff_date'] ) );
                $item->add_meta_data( 'ovabrw_total_days', $real_quantity );

                $real_price = get_real_price( 1, $product_id, strtotime( $values['ovabrw_pickup_date'] ), strtotime( $values['ovabrw_pickoff_date'] ) );
                $item->add_meta_data( 'ovabrw_price_detail', $real_price ); 

            }
               
        }


        if( isset( $values['ovabrw_service'] ) && $values['ovabrw_service'] ){
            $ovabrw_service_id = get_post_meta( $values['product_id'], 'ovabrw_service_id', true ); 
            $ovabrw_service_name = get_post_meta( $values['product_id'], 'ovabrw_service_name', true );
            $ovabrw_label_service = get_post_meta( $values['product_id'], 'ovabrw_label_service', true );
            $ovabrw_service = $values['ovabrw_service'];
            $ovabrw_service_arr = json_decode( $ovabrw_service, true );
            $values['ovabrw_service'] = $ovabrw_service_arr;
            if( ! empty( $ovabrw_service_arr ) && is_array( $ovabrw_service_arr ) ) {

                $item->add_meta_data( 'ovabrw_services', $ovabrw_service_arr );

                foreach( $ovabrw_service_arr as $val_ser ) {
                    if( ! empty( $ovabrw_service_id ) && is_array( $ovabrw_service_id ) ) {
                        foreach( $ovabrw_service_id as $key => $value ) {
                            if( is_array( $value ) && ! empty( $value ) ) {
                                foreach( $value as $k => $val ) {
                                    if( $val_ser == $val && ! empty( $val ) ){
                                        $name_service = $ovabrw_service_name[$key][$k];
                                        $label_service = $ovabrw_label_service[$key];

                                        $item->add_meta_data( $label_service, $name_service );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if( $values['resources'] ){
            foreach ($values['resources'] as $key_re => $value_re) {
                $item->add_meta_data( esc_html__( 'Resource', 'ova-brw' ), $value_re );
            }

            $item->add_meta_data( 'ovabrw_resources', $values['resources'] ); 
        }


        if( isset( $values['rental_type'] ) ){
            $item->add_meta_data( 'rental_type', $values['rental_type'] );
        }

        if( isset( $values['package_type'] ) ){
            $item->add_meta_data( 'package_type', $values['package_type'] );
        }


        if( isset( $values['period_label'] ) ){
            $item->add_meta_data( 'period_label', $values['period_label'] );
        }

        if( isset( $values['ovabrw_amount_insurance'] ) ){
            $item->add_meta_data( 'ovabrw_amount_insurance_product', $values['ovabrw_amount_insurance'] );
        }
       

        $deposit_enable = get_post_meta ( $product_id, 'ovabrw_enable_deposit', true );

        
        $deposit_remaining_amount = ova_calculate_deposit_remaining_amount($values);

        $total      = round( $item->get_total(), wc_get_price_decimals() );
        $subtotal   = round( $item->get_subtotal(), wc_get_price_decimals() );
        $item->set_total( $total );
        $item->set_subtotal( $subtotal );

        /* Get totol include tax */
        if ( wc_tax_enabled() && wc_prices_include_tax() ) {
            $total += round( $values['line_tax'], wc_get_price_decimals() );
        }

        if( $deposit_remaining_amount['ova_type_deposit'] === 'full' ) {
            $deposit_amount     = $total;
            $remaining_amount   = 0;
        } else {
            $deposit_amount     = $deposit_remaining_amount['deposit_amount'];
            $remaining_amount   = $deposit_remaining_amount['remaining_amount'];
        }

        if( get_post_meta( $product_id, 'ovabrw_enable_deposit', true ) == 'yes' ){

            $item->add_meta_data( 'ovabrw_remaining_amount_product', $remaining_amount );
            $item->add_meta_data( 'ovabrw_deposit_amount_product', $deposit_amount );
            $item->add_meta_data( 'ovabrw_deposit_full_amount', $deposit_amount + $remaining_amount );
        }
        
        

        $list_fields = get_option( 'ovabrw_booking_form', array() );

        if( is_array( $list_fields ) && ! empty( $list_fields ) ) {
            foreach( $list_fields as $key => $field ) {
                $value = array_key_exists( $key, $values ) ? $values[$key] : '';
                if( ! empty( $value ) && $field['enabled'] == 'on' ) {
                    $item->add_meta_data( $key, $value );
                }
            }
        }
        

        if( $values['id_vehicle'] ){
            $id_vehicles_arr = json_decode( $values['id_vehicle'], true );

            if ( $id_vehicles_arr && is_array( $id_vehicles_arr ) ) {
                $id_vehicles_agrs = array();
                $i = 1;
                $number_vehicle = isset( $values['ovabrw_number_vehicle'] ) ? $values['ovabrw_number_vehicle'] : 1;

                if ( ! $number_vehicle ) {
                    $number_vehicle = 1;
                }

                foreach( $id_vehicles_arr as $id_vehicles ) {
                    array_push( $id_vehicles_agrs, $id_vehicles );

                    if ( $i >= $number_vehicle ) {
                        break;
                    }

                    $i++;
                }

                $id_vehicles_str = implode( ',', $id_vehicles_agrs );
                $item->add_meta_data( 'id_vehicle', $id_vehicles_str );
            }
        }
    }
}



// Validate Rent Day Min
if ( ! function_exists( 'ovabrw_val_day_min' ) ) {
    function ovabrw_val_day_min( $ovabrw_pickoff_date, $ovabrw_pickup_date, $ovabrw_rent_day_min ){

        if( ( $ovabrw_pickoff_date - $ovabrw_pickup_date ) < $ovabrw_rent_day_min*24*60*60 ) {
            $result = true;
        } else {
            $result = false;
        }

        return  $result;
    }
}

// Validate Rent Hour Min
if ( ! function_exists( 'ovabrw_val_hour_min' ) ) {
    function ovabrw_val_hour_min( $ovabrw_pickoff_date, $ovabrw_pickup_date, $ovabrw_rent_hour_min ){
        return ( $ovabrw_pickoff_date - $ovabrw_pickup_date ) < $ovabrw_rent_hour_min*60*60 ? true : false;
    }
}


// Return array deposit info
if ( ! function_exists( 'ova_calculate_deposit_remaining_amount' ) ) {
    function ova_calculate_deposit_remaining_amount ( $cart_item ) {

        $remaining_amount = $deposit_amount = 0;
        $product_id       = $cart_item['product_id'];
        $number_vehicle   = 1;

        if ( isset( $cart_item['ovabrw_number_vehicle'] ) && is_numeric( $cart_item['ovabrw_number_vehicle'] ) ) {
            $number_vehicle = $cart_item['ovabrw_number_vehicle'];
        }

        // Calculate Rent Time
        $ovabrw_pickup_date = strtotime( $cart_item['ovabrw_pickup_date'] );
        $ovabrw_pickoff_date = strtotime( $cart_item['ovabrw_pickoff_date'] );

        $rent_time = get_time_bew_2day( $ovabrw_pickup_date, $ovabrw_pickoff_date, $product_id );

        if(  isset( $cart_item['rental_type'] ) && $cart_item['rental_type'] == 'period_time'  ){

            $line_total = floatval( $cart_item['period_price'] );
            $ovabrw_amount_insurance = get_post_meta( $product_id, 'ovabrw_amount_insurance', true );
            $line_total +=  floatval($ovabrw_amount_insurance);

            if ( $cart_item['resources'] && is_array( $cart_item['resources'] ) ) {
                $cart_re = $cart_item['resources'];
                $line_total += ovabrw_get_total_resoure( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date, $cart_re );
            }

            if ( $cart_item['ovabrw_service'] && is_array( $cart_item['ovabrw_service'] ) ) {
                $line_total += ovabrw_get_total_service( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date, $cart_item['ovabrw_service'] );
            }
            
        }else{

            $get_price_by_date = get_price_by_date( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date );
            $quantity = $get_price_by_date['quantity'];
            $line_total = $get_price_by_date['line_total'];
            
            $ovabrw_amount_insurance = get_post_meta( $product_id, 'ovabrw_amount_insurance', true );
            $line_total +=  floatval($ovabrw_amount_insurance);

            /* Price = Price + Resource Price - by Resource ********************************************/
            if ( $cart_item['resources'] && is_array( $cart_item['resources'] ) ) {
                $cart_re = $cart_item['resources'];
                $line_total += ovabrw_get_total_resoure( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date, $cart_re );
            }

            if ( $cart_item['ovabrw_service'] && is_array( $cart_item['ovabrw_service'] ) ) {
                $line_total += ovabrw_get_total_service( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date, $cart_item['ovabrw_service'] );
            }

        }

        // x quantity
        $line_total = round( $line_total, wc_get_price_decimals() ) * $number_vehicle;

        $deposit_enable = get_post_meta ( $product_id, 'ovabrw_enable_deposit', true );
        $value_deposit  = get_post_meta ( $product_id, 'ovabrw_amount_deposit', true );
        $value_deposit  = ( ! empty( $value_deposit ) ) ? ( floatval( $value_deposit ) ) : 0;
        $deposit_type_deposit = get_post_meta ( $product_id, 'ovabrw_type_deposit', true );

        $sub_remaining_amount = 0;
        $sub_deposit_amount = $line_total;

        if ($deposit_enable === 'yes') {

            if (isset($cart_item['ova_type_deposit']) && $cart_item['ova_type_deposit'] === 'full') {
                $sub_remaining_amount = 0;
                $sub_deposit_amount = $line_total;
            } elseif ($cart_item['ova_type_deposit'] === 'deposit') {
                if ($deposit_type_deposit === 'percent') {
                    $sub_deposit_amount = ($line_total * $value_deposit) / 100;
                    $a = $sub_deposit_amount;
                    $sub_remaining_amount = $line_total - $sub_deposit_amount;

                }elseif($deposit_type_deposit === 'value') {
                    $sub_deposit_amount = $value_deposit;
                    $sub_remaining_amount = $line_total - $sub_deposit_amount;
                }
            }
            
        }

        $deposit_amount += $sub_deposit_amount;
        $remaining_amount += $sub_remaining_amount;

        $deposit_remaining_amount                       = [];
        $deposit_remaining_amount['deposit_amount']     = round( ovabrw_get_price_tax( $deposit_amount, $cart_item ), wc_get_price_decimals() );
        $deposit_remaining_amount['remaining_amount']   = round( ovabrw_get_price_tax( $remaining_amount, $cart_item ), wc_get_price_decimals() );
        $deposit_remaining_amount['ova_type_deposit']   = $cart_item['ova_type_deposit'];
        $deposit_remaining_amount['pay_total']          = round( $deposit_remaining_amount['deposit_amount'] + $deposit_remaining_amount['remaining_amount'] );

        return $deposit_remaining_amount;
    }
}



/**
 * [ova_validate_manage_store check product available]
 * @param  [number]  $car_id                product id
 * @param  [strtotime]  $ovabrw_pickup_date    the date has been filtered via function
 * @param  [strtotime]  $ovabrw_pickoff_date   the date has been filtered via function
 * @param  [string]  $ovabrw_pickup_loc     
 * @param  [string]  $ovabrw_pickoff_loc    
 * @param  [string]  $passed                true, false
 * @param  string  $validate              cart, checkout, empty
 * @param  integer $number_vehicle quantity in cart
 * @return [array]                         status, vehicle_availables
 */
if ( ! function_exists( 'ova_validate_manage_store' ) ) {
    function ova_validate_manage_store( $car_id, $pickup_date, $pickoff_date, $pickup_loc, $pickoff_loc, $passed,  $validate = 'cart', $number_vehicle = 1 ) {

        $number_vehicle = (int)$number_vehicle;

        // Get all Order ID by Product ID
        $statuses   = brw_list_order_status();
        $orders_ids = ovabrw_get_orders_by_product_id( $car_id, $statuses );
        
        // Check is Auto/Manual to make ID vehicle 
        $manage_store = get_post_meta( $car_id, 'ovabrw_manage_store', true );

        // Total Car in the Store
        $total_car_store = ovabrw_get_total_stock( $car_id );

        // Get array product ids when use WPML
        $array_product_ids = ovabrw_get_wpml_product_ids( $car_id );

        //init vedicle array
        $cart_vehicle_rented = $store_vehicle_rented = array();

        // Store all ID Vehicle avaiable
        $vehicle_availables = array();

        // Error: Unvailable time for renting
        $untime_startdate = get_post_meta( $car_id, 'ovabrw_untime_startdate', true );
        $untime_enddate   = get_post_meta( $car_id, 'ovabrw_untime_enddate', true );

        if( $untime_startdate ){
            foreach ($untime_startdate as $key => $value) {
                if( ! ($pickoff_date < strtotime( $untime_startdate[$key] ) || strtotime( $untime_enddate[$key] ) < $pickup_date ) ){

                    if( $validate != 'search' ){
                        wc_clear_notices();
                        wc_add_notice( esc_html__( 'This time is not available for renting', 'ova-brw' ), 'error');
                    }
                    
                    return false; 
                }
            }
        }

        // Error: Unavailable Date for booking in settings
        $disable_week_day      = get_option( 'ova_brw_calendar_disable_week_day', '' );
        $data_disable_week_day = $disable_week_day != '' ? explode( ',', $disable_week_day ) : '';

        if ( $data_disable_week_day && $pickup_date && $pickoff_date ) {

            $datediff       = (int)$pickoff_date - (int)$pickup_date;
            $total_datediff = round( $datediff / (60 * 60 * 24), wc_get_price_decimals() ) + 1;

            // get number day
            $pickup_date_of_week   = wp_date('w', $pickup_date );

            $pickup_date_timestamp = $pickup_date;
            
            $i = 0;

            while ( $i <= $total_datediff ) {
                if ( in_array( $pickup_date_of_week, $data_disable_week_day ) ) {
                    if ( $validate == 'search' ) {
                        return array( 'status' => false, 'vehicle_availables' => [] );
                    }
                    wc_clear_notices();
                    wc_add_notice( esc_html__( 'This time is not available for renting', 'ova-brw' ), 'error');
                    return false;
                }

                $pickup_date_of_week  = wp_date('w', $pickup_date_timestamp );
                
                $pickup_date_timestamp = strtotime('+1 day', $pickup_date_timestamp);

                $i++;
            }
        }

        // Check Count Product in Order
        $store_vehicle_rented = ovabrw_vehicle_rented_in_order( $car_id, $pickup_date, $pickoff_date );
        
        // Check Count Product in Cart
        $cart_vehicle_rented  = ovabrw_vehicle_rented_in_cart( $car_id, $validate, $pickup_date, $pickoff_date );
        
        // Vehicle availables
        $data_vehicle = ovabrw_get_vehicle_available( $car_id, $store_vehicle_rented, $cart_vehicle_rented, $number_vehicle, $manage_store, $pickup_date, $pickoff_date, $pickup_loc, $pickoff_loc, $passed, $validate );
        
        if (  $data_vehicle && ( !empty( $data_vehicle['vehicle_availables'] ) || !empty( $data_vehicle['number_vehicle_available'] > 0 ) ) ) {
            return array( 'status' => $data_vehicle['passed'], 'vehicle_availables' => $data_vehicle['vehicle_availables'], 'number_vehicle_available' => $data_vehicle['number_vehicle_available'] );
        }

        return false;
    }
}


/**
 * Check vehicle rented in order
 */
if ( ! function_exists( 'ovabrw_vehicle_rented_in_order' ) ) {
    function ovabrw_vehicle_rented_in_order( $car_id, $pickup_date, $pickoff_date ) {
        
        $store_vehicle_rented_array = [];
        $quantity = $qty_order = 0;

        // Get array product ids when use WPML
        $array_product_ids = ovabrw_get_wpml_product_ids( $car_id );

        // Get Manage Vehicles
        $manage_vehicles = get_post_meta( $car_id, 'ovabrw_manage_store', true );

        // Get all Order ID by Product ID
        $statuses   = brw_list_order_status();
        $orders_ids = ovabrw_get_orders_by_product_id( $car_id, $statuses );

        if( $orders_ids ) {
            foreach ($orders_ids as $key => $value) {

                // Get Order Detail by Order ID
                $order = wc_get_order($value);

                // Get Meta Data type line_item of Order
                $order_line_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
                
                // For Meta Data
                foreach ( $order_line_items as $item_id => $item ) {

                    $pickup_date_store = $pickoff_date_store = $id_vehicle_rented = '';

                    // Get product
                    $product_id = $item->get_product_id();

                    // Check Line Item have item ID is Car_ID
                    if( in_array( $product_id , $array_product_ids ) ) {
                        
                        // Get rental type
                        $ovabrw_rental_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

                        // Check time to Prepare before delivered
                        if ( $ovabrw_rental_type == 'day' || $ovabrw_rental_type == 'transportation' ) {
                            $prepare_time = get_post_meta( $car_id, 'ovabrw_prepare_vehicle_day', true ) ? get_post_meta( $car_id, 'ovabrw_prepare_vehicle_day', true ) * 86400 : 0;
                        } else {
                            $prepare_time = get_post_meta( $car_id, 'ovabrw_prepare_vehicle', true ) ? get_post_meta( $car_id, 'ovabrw_prepare_vehicle', true ) * 60 : 0;
                        }

                        // Get value of pickup date, pickoff date
                        foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
                            
                            if( $meta->key == 'ovabrw_pickup_date_real' ) {
                                $pickup_date_store = strtotime( $meta->value );
                            }
                            if( $meta->key == 'ovabrw_pickoff_date_real' ) {
                                $pickoff_date_store = strtotime( $meta->value ) + $prepare_time;
                            }

                            if( $meta->key == 'id_vehicle' ) {
                                $id_vehicle_rented = trim( $meta->value );
                            }

                            if( $meta->key == 'ovabrw_number_vehicle' ) {
                                $qty_order = trim( $meta->value );
                            }
                        }

                        // Only compare date when "PickOff Date in Store" > "Current Time" becaue "PickOff Date Rent" have to > "Current Time"
                        if( $pickoff_date_store >= current_time( 'timestamp' ) ) {

                            if( !( $pickup_date >= $pickoff_date_store || $pickoff_date <= $pickup_date_store ) ){

                                $quantity += $qty_order;
                                if ( $id_vehicle_rented != '' ) {
                                    $id_vehicle_rented_arr = explode( ',', $id_vehicle_rented );

                                    if( ! empty( $id_vehicle_rented_arr ) && is_array( $id_vehicle_rented_arr ) ) {
                                        foreach( $id_vehicle_rented_arr as $value ){
                                            array_push( $store_vehicle_rented_array, $value );
                                        }
                                    }
                                }
                            }
                        }  
                    }
                } 

            }

            if( $store_vehicle_rented_array != null ) {

                // Only get unique Id vehicle
                $store_vehicle_rented_array = array_unique( $store_vehicle_rented_array );
            }
        }

        if ( $manage_vehicles == 'store' ) {
            return $quantity;
        }

        return $store_vehicle_rented_array;
    }
}

/**
 * Check vehicle rented in cart
 */
if ( ! function_exists( 'ovabrw_vehicle_rented_in_cart' ) ) {
    function ovabrw_vehicle_rented_in_cart( $car_id, $validate, $pickup_date, $pickoff_date ) {

        // Init cart vehicle rented
        $cart_vehicle_rented_array = [];
        $quantity = 0;

        // Get Manage Vehicles
        $manage_vehicles = get_post_meta( $car_id, 'ovabrw_manage_store', true );

        // Get array product ids when use WPML
        $array_product_ids = ovabrw_get_wpml_product_ids( $car_id );

        if( $validate == 'cart' ) {

            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

                $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                if( in_array( $product_id, $array_product_ids ) ) {

                     $ovabrw_rental_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

                    // Check time to Prepare before delivered
                    if ( $ovabrw_rental_type == 'day' || $ovabrw_rental_type == 'transportation' ) {
                        $prepare_time = get_post_meta( $car_id, 'ovabrw_prepare_vehicle_day', true ) ? get_post_meta( $car_id, 'ovabrw_prepare_vehicle_day', true ) * 86400 : 0;
                    } else {
                        $prepare_time = get_post_meta( $car_id, 'ovabrw_prepare_vehicle', true ) ? get_post_meta( $car_id, 'ovabrw_prepare_vehicle', true ) * 60 : 0;
                    }

                    if( isset( $cart_item['ovabrw_pickup_date_real'] ) && isset( $cart_item['ovabrw_pickoff_date_real'] ) ) {

                        if( !( $pickup_date >= strtotime( $cart_item['ovabrw_pickoff_date_real'] )+$prepare_time || $pickoff_date <= strtotime( $cart_item['ovabrw_pickup_date_real'] ) ) ) {

                            $quantity += $cart_item['ovabrw_number_vehicle'];

                            if( isset( $cart_item['id_vehicle'] ) && $cart_item['id_vehicle'] != ''  ) {
                                $id_vehicle_rented_arr = json_decode( $cart_item['id_vehicle'], true );
                                
                                if( !empty( $id_vehicle_rented_arr ) && is_array( $id_vehicle_rented_arr ) ) {
                                    foreach( $id_vehicle_rented_arr as $value ) {
                                        array_push( $cart_vehicle_rented_array, $value );
                                    }
                                }  
                            }
                        }
                    }
                }
            }

            if( $cart_vehicle_rented_array != null ) {
                $cart_vehicle_rented_array = array_unique( $cart_vehicle_rented_array );
            }
        }

        if ( $manage_vehicles == 'store' ) {
            return $quantity;
        }

        return $cart_vehicle_rented_array;
    }
}

/**
 * Get ids vehicle available
 */
if ( ! function_exists( 'ovabrw_get_ids_vehicle_available') ) {
    function ovabrw_get_ids_vehicle_available( $car_id, $store_vehicle_rented = [], $cart_vehicle_rented = [] ) {
        // init array
        $id_vehicles    = $ids_vehicle_available = [];
        $id_rented      = array_unique( array_merge( $store_vehicle_rented, $cart_vehicle_rented ) );

        if ( is_array( get_post_meta( $car_id, 'ovabrw_id_vehicles', true ) ) ) {
            $id_vehicles = get_post_meta( $car_id, 'ovabrw_id_vehicles', true );
        }

        $ids_vehicle_available  = array_diff( $id_vehicles, $id_rented );

        return $ids_vehicle_available;
    }
}

/**
 * Get vehicle available store
 */
if ( !function_exists( 'ovabrw_get_vehicle_available' ) ) {
    function ovabrw_get_vehicle_available( $car_id, $store_vehicle_rented = [], $cart_vehicle_rented = [], $number_vehicle = 1, $manage_store = '', $pickup_date = '', $pickoff_date = '', $pickup_loc = '', $pickoff_loc = '', $passed = false, $validate = 'cart' ) {

        $vehicle_add_availables = $vehicle_availables = $data_vehicle = $id_rented = $ids_vehicle_available = [];

        // Total Car in the Store
        $total_car_store = ovabrw_get_total_stock( $car_id );

        if ( $manage_store == 'store' ) {
            $number_vehicle_available = $total_car_store - $store_vehicle_rented - $cart_vehicle_rented;
            if ( $number_vehicle_available > 0 && $number_vehicle_available >= $number_vehicle ) {
                $passed = true;
            }
        } else {
            // Unique Product ID Rented
            $id_rented = array_unique( array_merge( $store_vehicle_rented, $cart_vehicle_rented ) );

            // Array vehicle available
            $ids_vehicle_available    = ovabrw_get_ids_vehicle_available( $car_id, $store_vehicle_rented, $cart_vehicle_rented );
            $number_vehicle_available = count( $ids_vehicle_available );
        }

        if ( $manage_store == 'store' ) {

            if( count($id_rented) < $total_car_store && ( $number_vehicle_available >= $number_vehicle ) ) {

                $i = 1;
                foreach ($ids_vehicle_available as $value) {

                    $vehicle_add_availables[] = trim( $value );
                    
                    $passed = true;
                    array_push( $vehicle_availables, $value );

                    if( $i >= $number_vehicle ) {
                        break;
                    }
                    
                    $i++;
                }

                if( $validate != 'search' && !wp_doing_ajax() ) {
                    WC()->session->set( 'id_vehicle_available' , json_encode( $vehicle_add_availables) );
                }

            } else {

                if( $validate != 'search' && !wp_doing_ajax() ) {
                    if( $number_vehicle > $number_vehicle_available && $number_vehicle_available != 0 && $number_vehicle_available > 0 ) {
                        wc_clear_notices();
                        wc_add_notice( sprintf( esc_html__( 'Available vehicle is %s', 'ova-brw'  ), $number_vehicle_available ), 'error');
                    } else {
                        wc_clear_notices();
                        wc_add_notice( esc_html__( 'Vehicle isn\'t available for this time, Please book other time.', 'ova-brw' ), 'error');
                    }
                }
            }
        }

        if ( $manage_store == 'id_vehicle' ) {

            $vehicle_avai = '';
            $check_vehicle_availables = [];

            if ( $ids_vehicle_available ) {
                
                foreach ($ids_vehicle_available as $key => $value) {

                    $vehicle_avai = ovabrw_get_vehicle_loc_title($value);
                    $id_vehicle_untime_startday = !empty( $vehicle_avai['untime'] ) ?  $vehicle_avai['untime']['startdate']  : '';
                    $id_vehicle_untime_enddate  = !empty( $vehicle_avai['untime'] ) ?  $vehicle_avai['untime']['enddate']  : '';

                    if ( isset($vehicle_avai['require_loc']) && $vehicle_avai['require_loc'] == 'yes' ) {
                        if ( $pickup_loc == $vehicle_avai['loc'] ) {
                            if( !( $pickup_date >= strtotime( $id_vehicle_untime_enddate ) || $pickoff_date <= strtotime( $id_vehicle_untime_startday ) ) && $id_vehicle_untime_startday != '' && $id_vehicle_untime_enddate != '' ) {

                                $number_vehicle_available--;
                          
                            } else {
                                array_push( $check_vehicle_availables, $value );
                            }
                        } else {
                            $number_vehicle_available--;
                        }
                    } else {
                        if( !( $pickup_date >= strtotime( $id_vehicle_untime_enddate ) || $pickoff_date <= strtotime( $id_vehicle_untime_startday ) ) && $id_vehicle_untime_startday != '' && $id_vehicle_untime_enddate != '' ) {

                                $number_vehicle_available--;
                          
                        } else {
                            array_push( $check_vehicle_availables, $value );
                        }
                    }
                }
            }

            if ( $number_vehicle_available >= $number_vehicle ) {
                if ( !empty( $check_vehicle_availables ) && is_array( $check_vehicle_availables ) ) {
                    foreach ( $check_vehicle_availables as $val ) {
                        $vehicle_add_availables[] = $val;
                        array_push( $vehicle_availables, $val );
                    }
                }

                $passed = true;
                if ( $validate != 'search' && !wp_doing_ajax() ) {
                    WC()->session->set( 'id_vehicle_available' , json_encode( array_slice( $vehicle_add_availables, 0, $number_vehicle ) ) );
                }

            } else {
                if ( $validate != 'search' && !wp_doing_ajax() ) {
                    wc_clear_notices();
                    if( $number_vehicle > $number_vehicle_available && $number_vehicle_available != 0 && $number_vehicle_available > 0 ) {
                        wc_clear_notices();
                        wc_add_notice( sprintf( esc_html__( 'Available vehicle is %s', 'ova-brw'  ), $number_vehicle_available ), 'error');
                    } else {
                        wc_clear_notices();
                        wc_add_notice( esc_html__( 'Vehicle isn\'t available for this time, Please book other time.', 'ova-brw' ), 'error');
                    }
                    return false;
                }
            }
        }

        $data_vehicle = [
            'passed'                    => $passed,
            'vehicle_availables'        => $vehicle_availables,
            'number_vehicle_available'  => $number_vehicle_available,
        ];

        return $data_vehicle;
    }
}

/**
 * Standardized Pick-up, Drop-off that the Guest enter at frontend
 * User for: Search, Compare with real date
 */
if ( ! function_exists( 'ovabrw_new_input_date' ) ) {
    function ovabrw_new_input_date( $product_id = '', $pickup_date = '', $pickoff_date = '', $period_package = '', $pick_up_loc = '', $pick_off_loc = '' ){

        if( !$product_id ) return array( 'pickup_date_new' => '', 'pickoff_date_new' => '' );

        // When Rental Type is Period, We have to Set Start Time, End Time again.
        $rental_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

        $defined_one_day = defined_one_day( $product_id );

        if( $rental_type == 'period_time' && $period_package != '' && $pickup_date != '' ){
            
            $rental_time_period = get_rental_info_period( $product_id, $pickup_date, $rental_type, $period_package );

            $pickup_date = $rental_time_period['start_time'];
            $pickoff_date = $rental_time_period['end_time'];

        }

       
        if( $rental_type == 'day' && $defined_one_day == 'day' ) {
            
            $pickup_date = $pickup_date ? strtotime( wp_date( apply_filters( 'ovabrw_new_pickup_date_day_day' , 'Y-m-d H:i' ), $pickup_date ) ) : '';
            $pickoff_date = $pickoff_date ? strtotime( wp_date( apply_filters( 'ovabrw_new_dropoff_date_day_day' , 'Y-m-d H:i' ), $pickoff_date ) ) : '';

        }else if( $rental_type == 'day' && $defined_one_day == 'hour' ) {

            $pickup_date = $pickup_date ? strtotime( wp_date( apply_filters( 'ovabrw_new_pickup_date_day_hour' , 'Y-m-d H:i' ), $pickup_date ) ) : '';
            $pickoff_date = $pickoff_date ? strtotime( wp_date( apply_filters( 'ovabrw_new_dropoff_date_day_hour' , 'Y-m-d H:i' ), $pickoff_date ) ) : '';

        }else if( $rental_type == 'day' && $defined_one_day == 'hotel' ) {

            $pickup_date = $pickup_date ? strtotime( wp_date( 'Y-m-d', $pickup_date ) .' '.apply_filters( 'brw_real_pickup_time_hotel', '14:00' ) ) : '';
            $pickoff_date = $pickoff_date ? strtotime( wp_date( 'Y-m-d', $pickoff_date ) .' '.apply_filters( 'brw_real_dropoff_time_hotel', '11:00' ) ) : '';
            
        }

        //get dropoff in price type transportation
        if( $rental_type === 'transportation' && $pick_up_loc != '' && $pick_off_loc != '' ) {

            $time_dropoff = ovabrw_get_time_by_pick_up_off_loc_transport( $product_id, $pick_up_loc, $pick_off_loc );
            $time_dropoff = 60 * $time_dropoff;
            
            $pickoff_date = $pickup_date + $time_dropoff;
        }

        return array( 'pickup_date_new' => $pickup_date, 'pickoff_date_new' => $pickoff_date );

    }
}
