<?php defined( 'ABSPATH' ) || exit();

// Custom Calculate Total Add To Cart
add_action( 'woocommerce_before_calculate_totals',  'ovabrw_woocommerce_before_calculate_totals' , 10, 1); 

if( ! function_exists( 'ovabrw_woocommerce_before_calculate_totals' ) ) {
    function ovabrw_woocommerce_before_calculate_totals( $cart_object ){

        $remaining_amount = $deposit_amount = $total_amount_insurance = $remaining_tax_total = 0;
        $has_deposit = false;
        $type_deposit = 'full';

        $i = 0;
        foreach ( $cart_object->get_cart() as $cart_item_key => $cart_item) {

            // Check custom product type is ovabrw_car_rental
            if( !$cart_item['data']->is_type('ovabrw_car_rental') ) continue;

            $product_id = $cart_item['product_id'];

            // Calculate Rent Time
            $ovabrw_pickup_date  = isset( $cart_item['ovabrw_pickup_date'] ) ? $cart_item['ovabrw_pickup_date'] : '';
            $ovabrw_pickoff_date = isset( $cart_item['ovabrw_pickoff_date'] ) ? $cart_item['ovabrw_pickoff_date'] : '' ;

            $ovabrw_pickup_loc   = isset( $cart_item['ovabrw_pickup_loc'] ) ? $cart_item['ovabrw_pickup_loc'] : '';
            $ovabrw_pickoff_loc  = isset( $cart_item['ovabrw_pickoff_loc'] ) ? $cart_item['ovabrw_pickoff_loc'] : '';

            $ovabrw_rental_type  = get_post_meta( $product_id, 'ovabrw_price_type', true );

            $total = get_price_by_date( $product_id, strtotime( $ovabrw_pickup_date ), strtotime( $ovabrw_pickoff_date ), $cart_item );

            $ovabrw_number_vehicle = (int)$cart_item['ovabrw_number_vehicle'];

            $line_total = round( $total['line_total'], wc_get_price_decimals() );

            $ovabrw_amount_insurance = $total['amount_insurance'];

            $deposit_enable = get_post_meta ( $product_id, 'ovabrw_enable_deposit', true );
            $value_deposit  = get_post_meta ( $product_id, 'ovabrw_amount_deposit', true );
            $value_deposit  = (!empty($value_deposit)) ? (floatval($value_deposit)) : 0;
            $deposit_type_deposit = get_post_meta ( $product_id, 'ovabrw_type_deposit', true );

            $sub_remaining_amount = $deposit_amount = 0;
            $sub_deposit_amount   = $line_total;

            WC()->cart->deposit_info = array();
            if ($deposit_enable === 'yes') {
                $has_deposit = true;
                if (isset($cart_item['ova_type_deposit']) && $cart_item['ova_type_deposit'] === 'full') {
                    $sub_remaining_amount = 0;
                    $sub_deposit_amount = $line_total;
                } elseif ( isset($cart_item['ova_type_deposit']) && $cart_item['ova_type_deposit'] === 'deposit') {
                    if ($deposit_type_deposit === 'percent') {
                        $sub_deposit_amount = ($line_total * $value_deposit) / 100;
                        $sub_remaining_amount = $line_total - $sub_deposit_amount;

                    }elseif($deposit_type_deposit === 'value') {
                        $sub_deposit_amount = $value_deposit;
                        $sub_remaining_amount = $line_total - $sub_deposit_amount;
                    }
                }
                
            }

            $remaining_amount       += ovabrw_get_price_tax( $sub_remaining_amount, $cart_item );
            $deposit_amount         += $sub_deposit_amount;
            $total_amount_insurance += $ovabrw_amount_insurance;

            // Add tax total remanining
            if ( wc_tax_enabled() ) {
                $prices_include_tax   = wc_prices_include_tax() ? 'yes' : 'no';
                $remaining_tax_total += ovabrw_get_taxes_by_price( $sub_remaining_amount, $product_id, $prices_include_tax );
            }

            // Check order deposit
            if ( $cart_item['ova_type_deposit'] == 'deposit' ) {
                $type_deposit = 'deposit';
            }

            WC()->cart->deposit_info[ 'ova_deposit_amount' ]    = round( $deposit_amount, wc_get_price_decimals() );
            WC()->cart->deposit_info[ 'ova_remaining_amount' ]  = round( $remaining_amount, wc_get_price_decimals() );
            WC()->cart->deposit_info[ 'ova_type_deposit' ]      = $type_deposit;
            WC()->cart->deposit_info[ 'ova_insurance_amount' ]  = round( $total_amount_insurance, wc_get_price_decimals() );
            WC()->cart->deposit_info[ '_ova_has_deposit' ]      = $has_deposit;
            WC()->cart->deposit_info[ 'ova_remaining_taxes' ]   = $remaining_tax_total;

            $cart_item['data']->set_price( round( $line_total, wc_get_price_decimals() ) );
            $cart_item['data']->add_meta_data( 'pay_total', round( $line_total, wc_get_price_decimals() ), true );

            // Check deposit
            if ( $deposit_amount && $has_deposit && $deposit_enable === 'yes' ) {
                $cart_item['data']->set_price( $deposit_amount );
            }

            $cart_object->cart_contents[ $cart_item_key ]['quantity'] = 1;

        } // End foreach
    }
}



function ovabrw_get_total_resoure( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date, $ovabrw_resource = [] ) {

    $rent_time = get_time_bew_2day( $ovabrw_pickup_date, $ovabrw_pickoff_date, $product_id );

    $resource_id = get_post_meta( $product_id, 'ovabrw_resource_id', true );
    $resource_price = get_post_meta( $product_id, 'ovabrw_resource_price', true );
    $resource_duration_type = get_post_meta( $product_id, 'ovabrw_resource_duration_type', true );

    $total_price = 0;
    if( is_array( $ovabrw_resource ) && ! empty( $ovabrw_resource ) ){
        foreach ( $ovabrw_resource as $key_c_re => $value_c_re) {
            foreach ($resource_id as $key_id => $value_id) {
                if( $key_c_re ==  $value_id ){

                    if( $resource_duration_type[$key_id] == 'total' ){
                        $total_price += $resource_price[$key_id];
                    }else if( $resource_duration_type[$key_id] == 'days' ){
                        $total_price += $resource_price[$key_id] * $rent_time['rent_time_day_raw'];
                    }if( $resource_duration_type[$key_id] == 'hours' ){
                        $total_price += $resource_price[$key_id] * $rent_time['rent_time_hour_raw'];
                    }

                }
            }
        }
    }
    return $total_price;
}

function ovabrw_get_total_service( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date, $ovabrw_service = [] ){

    $rent_time = get_time_bew_2day( $ovabrw_pickup_date, $ovabrw_pickoff_date, $product_id );
    $rental_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

    $ovabrw_service_id = get_post_meta( $product_id, 'ovabrw_service_id', true );  
    $ovabrw_service_price = get_post_meta( $product_id, 'ovabrw_service_price', true );  
    $ovabrw_service_duration_type = get_post_meta( $product_id, 'ovabrw_service_duration_type', true ); 

    $total_service = 0;

    if( ! empty( $ovabrw_service ) && is_array( $ovabrw_service ) ){
        foreach( $ovabrw_service as $id_service ){

            if( $id_service && $ovabrw_service_id && is_array( $ovabrw_service_id ) ){
                foreach ($ovabrw_service_id as $key_id => $value_id_arr) {
                    if( in_array($id_service, $value_id_arr) ) {
                        foreach ($value_id_arr as $k => $v) {
                            if( $id_service == $v ){

                                $ovabrw_service_duration_type_str = $ovabrw_service_duration_type[$key_id][$k];
                                $price_service = floatval( $ovabrw_service_price[$key_id][$k] );


                                if( $ovabrw_service_duration_type_str == 'total' ){
                                    $total_service += $price_service;
                                }else if( $ovabrw_service_duration_type_str == 'days' ){

                                    $total_service += $price_service * $rent_time['rent_time_day_raw'];

                                }if( $ovabrw_service_duration_type_str == 'hours' ){
                                    $total_service += $price_service * $rent_time['rent_time_hour_raw'];
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    /* end calculate service */
    return $total_service;
}



/**
 * Get Price a product with Pick-up date, Drop-off date
 * @param  [type] $product_id          [description]
 * @param  [strtotime] $ovabrw_pickup_date
 * @param  [strtotime] $ovabrw_pickoff_date
 * @return [array] quantity, line_total
 */
if( ! function_exists( 'get_price_by_date' ) ) {
    function get_price_by_date( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date, $cart_item = [], $ovabrw_pickup_loc = '', $ovabrw_pickoff_loc ='', $id_package = '' ){

        if( empty( $ovabrw_pickup_loc ) ) {
            $ovabrw_pickup_loc = isset( $cart_item['ovabrw_pickup_loc'] ) ? $cart_item['ovabrw_pickup_loc'] : '';
        }
        if( empty( $ovabrw_pickoff_loc ) ) {
            $ovabrw_pickoff_loc = isset( $cart_item['ovabrw_pickoff_loc'] ) ? $cart_item['ovabrw_pickoff_loc'] : '';
        }


        if( is_array( $cart_item ) && ! empty( $cart_item ) ) {
            $product_id = $cart_item['product_id'];
        }


        // Get New Date to match per product
        $new_date = ovabrw_new_input_date( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date );
        $ovabrw_pickup_date = $new_date['pickup_date_new'];
        $ovabrw_pickoff_date = $new_date['pickoff_date_new'];

        $rent_time = get_time_bew_2day( $ovabrw_pickup_date, $ovabrw_pickoff_date, $product_id );
        
        // Get Range Time or ST
        $ovabrw_rt_startdate = get_post_meta( $product_id, 'ovabrw_rt_startdate', true );
        $ovabrw_rt_starttime = get_post_meta( $product_id, 'ovabrw_rt_starttime', true );

        $ovabrw_rt_enddate = get_post_meta( $product_id, 'ovabrw_rt_enddate', true );
        $ovabrw_rt_endtime = get_post_meta( $product_id, 'ovabrw_rt_endtime', true );
        

        

        /* Price calculate by Global - Priority 0 *******************************************/
        $gl_price = $gl_price_total = ovabrw_price_global( $product_id, $rent_time );
        $quantity = $gl_quantity = $gl_quantity_total = ovabrw_quantity_global( $product_id, $rent_time );
        
        $line_total = $gl_price * $gl_quantity;


        // Price calculate by Weekday - Priority 1 *******************************************/
        $rental_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

        if( $rental_type == 'period_time' ){

            if( isset( $cart_item ) && is_array( $cart_item ) && ! empty( $cart_item ) ) {
                 $ovabrw_period_price = isset( $cart_item['period_price'] ) ? $cart_item['period_price'] : 0;
            } else {

                if( $id_package ) {
                    $ovabrw_period_package_id = $id_package;
                } else {
                    $ovabrw_period_package_id = isset( $_POST['ovabrw_period_package_id'] ) ? sanitize_text_field( $_POST['ovabrw_period_package_id'] ) : '' ;
                }



                $rental_info_period = get_rental_info_period( $product_id, $ovabrw_pickup_date, $rental_type, $ovabrw_period_package_id );
                $ovabrw_period_price = $rental_info_period['period_price'];
            }

            $quantity = 1;
            $line_total = floatval( $ovabrw_period_price );

           
        } else if( $rental_type === "transportation" ){

            $quantity = 1;
            $line_total = ovabrw_get_price_by_start_date_transport( $product_id, $ovabrw_pickup_loc, $ovabrw_pickoff_loc );

        } else if( $rental_type === "day" ){

            $get_price_by_day = get_price_by_rental_type_day( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date );

            $quantity = $get_price_by_day['quantity'];
            $line_total = $get_price_by_day['line_total'];

        } elseif ($rental_type === "mixed") {

            $total_price_day = get_price_by_rental_type_day ( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date );

            $quantity_day = floor($rent_time['rent_time_day_raw']);

            if ($quantity_day <= 0) { 
                $quantity_hour = $rent_time['rent_time_hour_raw'];
            } elseif ($quantity_day > 0) {
                $quantity_hour = ($rent_time['rent_time_hour_raw'] - $quantity_day * 24);
            }

            $gl_price_hour = get_regular_price_hour_mixed($product_id, $quantity_hour);

            $day_total = (float)$total_price_day['line_total'];
            $hour_total = $quantity_hour * $gl_price_hour;

            $line_total = $day_total + $hour_total;
        }
       
        // /Price calculate by Weekday *******************************************/



       
        /* Price calculate by - Special Time ( Range Time) - Priority 2 *******************************************/

        $rt_quantity_total_4 = $rt_price_total_4 = 0;
        $rt_quantity_total_3 = $rt_price_total_3 = 0;
        $rt_quantity_total_2 = $rt_price_total_2 = 0;
        $rt_quantity_total_1 = $rt_price_total_1 = 0;

        $i = 0; $data_max=0; $flag_max = false;

        if( $ovabrw_rt_startdate ){
            foreach ($ovabrw_rt_startdate as $key_rt => $value_rt) {

                $i++;

                if( $i === 1 ) {
                    $data_max = strtotime( $ovabrw_rt_enddate[$key_rt]  . ' ' .  $ovabrw_rt_endtime[$key_rt]);
                    $flag_max = true;
                } elseif( $data_max < strtotime( $ovabrw_rt_enddate[$key_rt]  . ' ' .  $ovabrw_rt_endtime[$key_rt] ) ) {

                    $data_max = strtotime( $ovabrw_rt_enddate[$key_rt]  . ' ' .  $ovabrw_rt_endtime[$key_rt] );
                    $flag_max = true;
                } else {
                    $flag_max = false;
                }


                $start_date_str = $value_rt . ' ' . $ovabrw_rt_starttime[$key_rt];
                $end_date_str = $ovabrw_rt_enddate[$key_rt] . ' ' . $ovabrw_rt_endtime[$key_rt];

                $start_date = strtotime( $start_date_str );
                $end_date = strtotime( $end_date_str );

                // If Special_Time_Start_Date <= pickup_date && pickoff_date <= Special_Time_End_Date
                if( $ovabrw_pickup_date >=  $start_date  && $ovabrw_pickoff_date <= $end_date ){

                    $rt_price = ovabrw_price_special_time( $product_id, $rent_time, $key_rt );
                    $rt_quantity = ovabrw_quantity_global( $product_id, $rent_time );

                    $rt_quantity_total_1 += $rt_quantity;
                    $rt_price_total_1 += $rt_quantity * $rt_price;

                    $quantity = $rt_quantity;
                    $line_total = $rt_price * $rt_quantity;


                    if ($rental_type === "mixed") {
                        $ovabrw_rt_price_hour = get_post_meta( $product_id, 'ovabrw_rt_price_hour', true );
                        $rt_price_hour = get_special_price_hour_mixed($product_id, $ovabrw_rt_price_hour[$key_rt],$key_rt, $quantity_hour);

                        // ovabrw_price_special_time
                        $line_total = $quantity_day * $rt_price + $quantity_hour * $rt_price_hour;

                        if( $flag_max ) {
                            $hour_total = $quantity_hour * $rt_price_hour;
                        }
                        

                    }

                    

                }else if( $ovabrw_pickup_date < $start_date && $ovabrw_pickoff_date <=  $end_date && $ovabrw_pickoff_date >=  $start_date ){

                    $gl_quantity_array = get_time_bew_2day( $ovabrw_pickup_date, $start_date, $product_id );
                    $gl_price = ovabrw_price_global( $product_id, $gl_quantity_array );
                    $gl_quantity = ovabrw_quantity_global( $product_id, $gl_quantity_array );

                    $rt_quantity_array = get_time_bew_2day( $start_date, $ovabrw_pickoff_date, $product_id );
                    $rt_price = ovabrw_price_special_time( $product_id, $rt_quantity_array, $key_rt );
                    $rt_quantity = ovabrw_quantity_global( $product_id, $rt_quantity_array );

                    $rt_quantity_total_2 += $rt_quantity;
                    $rt_price_total_2 += $rt_quantity * $rt_price;

                    $quantity = $gl_quantity + $rt_quantity;
                    $line_total = $gl_price * $gl_quantity + $rt_quantity * $rt_price;

                    if ($rental_type === "mixed") {
                        $ovabrw_rt_price_hour = get_post_meta( $product_id, 'ovabrw_rt_price_hour', true );
                        $gl_quantity = floor($gl_quantity_array['rent_time_day_raw']);
                        $rt_quantity = $quantity_day - $gl_quantity;
                        $rt_price_hour = get_special_price_hour_mixed($product_id, $ovabrw_rt_price_hour[$key_rt],$key_rt, $quantity_hour);
                        $line_total = $gl_price * $gl_quantity + $rt_quantity * $rt_price + $quantity_hour * $rt_price_hour;

                        if( $flag_max ) {
                            $hour_total = $quantity_hour * $rt_price_hour;
                        } 

                    }

                    

                }else if( $ovabrw_pickup_date >=  $start_date && $ovabrw_pickup_date <= $end_date && $ovabrw_pickoff_date >= $end_date ){

                    $rt_quantity_array = get_time_bew_2day( $ovabrw_pickup_date,  $end_date, $product_id );
                    $rt_price = ovabrw_price_special_time( $product_id, $rt_quantity_array, $key_rt );
                    $rt_quantity = ovabrw_quantity_global( $product_id, $rt_quantity_array );

                    $gl_quantity_array = get_time_bew_2day( $end_date, $ovabrw_pickoff_date, $product_id );
                    $gl_price = ovabrw_price_global( $product_id, $gl_quantity_array );
                    $gl_quantity = ovabrw_quantity_global( $product_id, $gl_quantity_array );

                    $rt_quantity_total_3 += $rt_quantity;
                    $rt_price_total_3 += $rt_quantity * $rt_price;

                    $quantity = $gl_quantity + $rt_quantity;
                    $line_total = $gl_price * $gl_quantity + $rt_quantity * $rt_price;

                    if ($rental_type === "mixed") {
                        $ovabrw_rt_price_hour = get_post_meta( $product_id, 'ovabrw_rt_price_hour', true );
                        $rt_quantity = ceil($rt_quantity_array['rent_time_day_raw']);
                        $gl_quantity = $quantity_day - $rt_quantity;
                        $line_total = $gl_price * $gl_quantity + $rt_quantity * $rt_price + $quantity_hour * $gl_price_hour;


                        if( $flag_max ) {
                            $hour_total = $quantity_hour * $gl_price_hour;
                        }
                    }

                    
                }else if( $ovabrw_pickup_date < $start_date && $ovabrw_pickoff_date > $end_date ){

                    $gl_rent_time_1 = get_time_bew_2day( $ovabrw_pickup_date, $start_date, $product_id );
                    $gl_quantity_1 = ovabrw_quantity_global( $product_id, $gl_rent_time_1 );

                    $gl_rent_time_3 = get_time_bew_2day( $end_date, $ovabrw_pickoff_date, $product_id );
                    $gl_quantity_3 = ovabrw_quantity_global( $product_id, $gl_rent_time_3 );

                    $gl_quantity = $gl_quantity_1 + $gl_quantity_3;


                    $rt_rent_time_2 = get_time_bew_2day( $start_date, $end_date, $product_id );
                    $rt_quantity = ovabrw_quantity_global( $product_id, $rt_rent_time_2 );

                    


                    $gl_price = ovabrw_price_global( $product_id, $gl_rent_time_1 );
                    $rt_price = ovabrw_price_special_time( $product_id, $rt_rent_time_2, $key_rt );

                    $rt_quantity_total_4 += $rt_quantity;
                    $rt_price_total_4 += $rt_quantity * $rt_price;

                    $quantity = $gl_quantity + $rt_quantity;
                    $line_total = $gl_price * $gl_quantity + $rt_quantity * $rt_price;


                    if ($rental_type === "mixed") {
                        $ovabrw_rt_price_hour = get_post_meta( $product_id, 'ovabrw_rt_price_hour', true );
                        $rt_quantity = ($rt_rent_time_2['rent_time_day_raw']);
                        $gl_quantity = $quantity_day - $rt_quantity;
                        
                        $line_total = $gl_price * $gl_quantity + $rt_quantity * $rt_price + $quantity_hour * $gl_price_hour;

                        if( $flag_max ) {
                            $hour_total = $quantity_hour * $gl_price_hour;
                        }
                    }
                    

                }

            }
        }

        $rt_quantity_total = $rt_quantity_total_1 + $rt_quantity_total_2 + $rt_quantity_total_3 + $rt_quantity_total_4;
        $rt_price_total = $rt_price_total_1 + $rt_price_total_2 + $rt_price_total_3 + $rt_price_total_4;


        if( $rt_quantity_total ) {

            $gl_quantity = $gl_quantity_total - $rt_quantity_total;
            $line_total = $gl_quantity * $gl_price_total + $rt_price_total;

        }
        /* /Table Price - Special Time ( Range Time) *******************************************/



        $ovabrw_amount_insurance = floatval(get_post_meta( $product_id, 'ovabrw_amount_insurance', true ));
        $line_total +=  $ovabrw_amount_insurance;

        if( is_array( $cart_item ) && ! empty( $cart_item ) ) {

            $ovabrw_resource = $cart_item['resources'];

        } else {

            $ovabrw_resource =  isset( $_POST['ovabrw_resource_checkboxs'] ) ? $_POST['ovabrw_resource_checkboxs'] : [];

        }

        $total_resource = ovabrw_get_total_resoure( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date, $ovabrw_resource );


        $ovabrw_service = isset( $cart_item['ovabrw_service'] ) ? $cart_item['ovabrw_service'] : '';
        $ovabrw_service = json_decode( $ovabrw_service );
        $total_service = ovabrw_get_total_service( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date, $ovabrw_service );


        $line_total += $total_resource;
        $line_total += $total_service;

        $ovabrw_number_vehicle = isset( $cart_item['ovabrw_number_vehicle'] ) ? (int)$cart_item['ovabrw_number_vehicle'] : 1;

        $line_total *= $ovabrw_number_vehicle;
        $ovabrw_amount_insurance *= $ovabrw_number_vehicle;

        return [ 'quantity' => $quantity, 'line_total' => $line_total, 'amount_insurance' => $ovabrw_amount_insurance ];

    }
}



function get_price_by_global_discount($product_id, $ovabrw_global_discount_duration_val_min = array(), $price_type = '', $rent_time = 0 ){

    if( $ovabrw_global_discount_duration_val_min ){

        foreach ($ovabrw_global_discount_duration_val_min as $key_dur_min => $value_dur_min) {

           $ovabrw_global_discount_duration_val_max = get_post_meta( $product_id, 'ovabrw_global_discount_duration_val_max', true );
           $ovabrw_global_discount_duration_type = get_post_meta( $product_id, 'ovabrw_global_discount_duration_type', true );

           if( $ovabrw_global_discount_duration_type[$key_dur_min] == $price_type && $rent_time >=  $value_dur_min && $ovabrw_global_discount_duration_val_max[$key_dur_min] >= $rent_time ){
                $ovabrw_global_discount_price = get_post_meta( $product_id, 'ovabrw_global_discount_price', true );
                return $ovabrw_global_discount_price[$key_dur_min];
            }
        }
    }

    return false;

}


function get_price_by_rt_discount( $ovabrw_rt_discount_duration_min = array(), $ovabrw_rt_discount_duration_max = array(), $ovabrw_rt_discount_duration_type = array(), $ovabrw_rt_discount_duration_price = array(), $price_type = '', $rent_time = 0 ){

    if( $ovabrw_rt_discount_duration_min ){

        foreach ($ovabrw_rt_discount_duration_min as $key_dur => $value_dur) {

           if( $ovabrw_rt_discount_duration_type[$key_dur] == $price_type && $rent_time >=  $value_dur && $ovabrw_rt_discount_duration_max[$key_dur] >= $rent_time){

            return $ovabrw_rt_discount_duration_price[$key_dur];

            }

        }

    }

    return false;

}


// Quantity in Global
function ovabrw_quantity_global( $product_id, $rent_time ){

    $price_type = get_post_meta( $product_id, 'ovabrw_price_type', true );
    
    if( $price_type == 'day' ){
        return (int)$rent_time['rent_time_day'];
    }else if( $price_type == 'hour' ){
        return (int)$rent_time['rent_time_hour'];
    }else{
        if( $rent_time['rent_time_day_raw'] < 1 ){
            return (int)$rent_time['rent_time_hour'];
        }else{
            return (int)$rent_time['rent_time_day'];
        }
    }
}

function ovabrw_price_global_by_type_day ( $product_id, $rent_time  ) {

    $price_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

    $ovabrw_global_discount_duration_val_min = get_post_meta( $product_id, 'ovabrw_global_discount_duration_val_min', true );
    if( $ovabrw_global_discount_duration_val_min ){ asort( $ovabrw_global_discount_duration_val_min ); }
    $gl_price = 0;
    if( $price_type == 'day' ){
        // Set Price by Global Discount
        if( $ovabrw_global_discount_duration_val_min ){
            $gl_set_price = get_price_by_global_discount( $product_id, $ovabrw_global_discount_duration_val_min, $price_type='days', $rent_time['rent_time_day'] );
            $gl_price = ( $gl_set_price != false ) ? $gl_set_price : $gl_price;
        }
    } elseif( $price_type == 'mixed' ) {
        if( $rent_time['rent_time_day_raw'] < 1 ){

            $gl_price = get_post_meta( $product_id, 'ovabrw_regul_price_hour', true );
            if( $ovabrw_global_discount_duration_val_min ){
                $gl_set_price = get_price_by_global_discount( $product_id, $ovabrw_global_discount_duration_val_min, $price_type='hours', $rent_time['rent_time_hour'] );
                $gl_price = ( $gl_set_price != false ) ? $gl_set_price : $gl_price;
            }
            return $gl_price;

        }else{

            $gl_price = get_post_meta( $product_id, '_regular_price', true );

            if( $ovabrw_global_discount_duration_val_min ){
                $gl_set_price = get_price_by_global_discount( $product_id, $ovabrw_global_discount_duration_val_min, $price_type='days', floor( $rent_time['rent_time_day_raw'] ) );
                $gl_price = ( $gl_set_price != false ) ? $gl_set_price : $gl_price;
            }
            return $gl_price;
        }
    }

    return $gl_price;
}

// Price in Global
function ovabrw_price_global( $product_id, $rent_time ){

    $price_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

    $ovabrw_global_discount_duration_val_min = get_post_meta( $product_id, 'ovabrw_global_discount_duration_val_min', true );
    if( $ovabrw_global_discount_duration_val_min ){ asort( $ovabrw_global_discount_duration_val_min ); }
    
    if( $price_type == 'day' ){

        // Set Price by Global Discount
        $gl_price = get_post_meta( $product_id, '_regular_price', true );
        if( $ovabrw_global_discount_duration_val_min ){
            $gl_set_price = get_price_by_global_discount( $product_id, $ovabrw_global_discount_duration_val_min, $price_type='days', $rent_time['rent_time_day'] );
            $gl_price = ( $gl_set_price != false ) ? $gl_set_price : $gl_price;
        }
        return (float)$gl_price;

    }else if( $price_type == 'hour' ){

        $gl_price = get_post_meta( $product_id, 'ovabrw_regul_price_hour', true );

        if( $ovabrw_global_discount_duration_val_min ){
            $gl_set_price = get_price_by_global_discount( $product_id, $ovabrw_global_discount_duration_val_min, $price_type='hours', $rent_time['rent_time_hour'] );
            $gl_price = ( $gl_set_price != false ) ? $gl_set_price : $gl_price;
        }
        return (float)$gl_price;

    }else{ // Price type is Mixed

        if( $rent_time['rent_time_day_raw'] < 1 ){

            $gl_price = get_post_meta( $product_id, 'ovabrw_regul_price_hour', true );
            if( $ovabrw_global_discount_duration_val_min ){
                $gl_set_price = get_price_by_global_discount( $product_id, $ovabrw_global_discount_duration_val_min, $price_type='hours', $rent_time['rent_time_hour'] );
                $gl_price = ( $gl_set_price != false ) ? $gl_set_price : $gl_price;
            }
            return (float)$gl_price;

        }else{

            $gl_price = get_post_meta( $product_id, '_regular_price', true );

            if( $ovabrw_global_discount_duration_val_min ){
                $gl_set_price = get_price_by_global_discount( $product_id, $ovabrw_global_discount_duration_val_min, $price_type='days', floor( $rent_time['rent_time_day_raw'] ) );
                $gl_price = ( $gl_set_price != false ) ? $gl_set_price : $gl_price;
            }
            return (float)$gl_price;
        }
    }

}

// Get Price for Spceial Time
function ovabrw_price_special_time( $product_id, $rent_time, $key_rt ){

    // $ovabrw_start_timestamp = get_post_meta( $product_id, 'ovabrw_start_timestamp', true );
    $ovabrw_rt_string_start = get_post_meta( $product_id, 'ovabrw_rt_string_start', true );

    $ovabrw_rt_price = get_post_meta( $product_id, 'ovabrw_rt_price', true );
    $ovabrw_rt_price_hour = get_post_meta( $product_id, 'ovabrw_rt_price_hour', true );
    $ovabrw_rt_discount = get_post_meta( $product_id, 'ovabrw_rt_discount', true );

    $price_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

    if( $ovabrw_rt_string_start[$key_rt] ){

        // Check if PickUp Date bettwen date of Range Time
        if( $price_type == 'day' ){

            $rt_price = $ovabrw_rt_price[$key_rt];
                // Return ST Price Discount
            if( isset( $ovabrw_rt_discount[$key_rt] ) ){
                $ovabrw_rt_discount_duration_min = $ovabrw_rt_discount[$key_rt]['min'];
                $ovabrw_rt_discount_duration_max = $ovabrw_rt_discount[$key_rt]['max'];
                arsort($ovabrw_rt_discount_duration_min);

                $ovabrw_rt_discount_duration_type = $ovabrw_rt_discount[$key_rt]['duration_type'];
                $ovabrw_rt_discount_duration_price = $ovabrw_rt_discount[$key_rt]['price'];
                $st_set_price = get_price_by_rt_discount($ovabrw_rt_discount_duration_min, $ovabrw_rt_discount_duration_max, $ovabrw_rt_discount_duration_type, $ovabrw_rt_discount_duration_price, $price_type='days', $rent_time['rent_time_day'] );



                $rt_price = $st_set_price != false ? $st_set_price : $rt_price;
            }

            return $rt_price;
        }else if( $price_type == 'hour' ){

            $rt_price = $ovabrw_rt_price_hour[$key_rt];

                // Set Price by RT(ST) Discount
            if( isset( $ovabrw_rt_discount[$key_rt] ) ){
                $ovabrw_rt_discount_duration_min = $ovabrw_rt_discount[$key_rt]['min'];
                $ovabrw_rt_discount_duration_max = $ovabrw_rt_discount[$key_rt]['max'];

                arsort($ovabrw_rt_discount_duration_min);

                $ovabrw_rt_discount_duration_type = $ovabrw_rt_discount[$key_rt]['duration_type'];
                $ovabrw_rt_discount_duration_price = $ovabrw_rt_discount[$key_rt]['price'];
                $st_set_price =  get_price_by_rt_discount( $ovabrw_rt_discount_duration_min, $ovabrw_rt_discount_duration_max, $ovabrw_rt_discount_duration_type, $ovabrw_rt_discount_duration_price, $price_type='hours', $rent_time['rent_time_hour'] );
                $rt_price = $st_set_price != false ? $st_set_price : $rt_price;
            }

            return $rt_price;

            }else{ // Price type is Mixed

                if( $rent_time['rent_time_day_raw'] < 1 ){

                    $rt_price = $ovabrw_rt_price_hour[$key_rt];
                    // Set Price by RT(ST) Discount
                    if( isset( $ovabrw_rt_discount[$key_rt] ) ){
                        $ovabrw_rt_discount_duration_min = $ovabrw_rt_discount[$key_rt]['min'];
                        $ovabrw_rt_discount_duration_max = $ovabrw_rt_discount[$key_rt]['max'];
                        arsort($ovabrw_rt_discount_duration_min);

                        $ovabrw_rt_discount_duration_type = $ovabrw_rt_discount[$key_rt]['duration_type'];
                        $ovabrw_rt_discount_duration_price = $ovabrw_rt_discount[$key_rt]['price'];
                        $st_set_price = get_price_by_rt_discount( $ovabrw_rt_discount_duration_min, $ovabrw_rt_discount_duration_max, $ovabrw_rt_discount_duration_type, $ovabrw_rt_discount_duration_price, $price_type='hours', $rent_time['rent_time_hour'] );
                        $rt_price = $st_set_price != false ? $st_set_price : $rt_price;
                    }

                    return $rt_price;

                }else{

                    $rt_price = $ovabrw_rt_price[$key_rt];
                    // Set Price by RT(ST) Discount
                    if( isset( $ovabrw_rt_discount[$key_rt] ) ){
                        $ovabrw_rt_discount_duration_min = $ovabrw_rt_discount[$key_rt]['min'];
                        $ovabrw_rt_discount_duration_max = $ovabrw_rt_discount[$key_rt]['max'];
                        arsort($ovabrw_rt_discount_duration_min);

                        $ovabrw_rt_discount_duration_type = $ovabrw_rt_discount[$key_rt]['duration_type'];
                        $ovabrw_rt_discount_duration_price = $ovabrw_rt_discount[$key_rt]['price'];
                        $st_set_price = get_price_by_rt_discount( $ovabrw_rt_discount_duration_min, $ovabrw_rt_discount_duration_max, $ovabrw_rt_discount_duration_type, $ovabrw_rt_discount_duration_price, $price_type='days', $rent_time['rent_time_day'] );
                        $rt_price = $st_set_price != false ? $st_set_price : $rt_price;
                    }

                    return $rt_price;

                }
            }

    } // endif
}

function get_regular_price_hour_mixed ($product_id, $quantity_hour = 0) {
    $gl_price_hour = get_post_meta( $product_id, 'ovabrw_regul_price_hour', true );

    $ovabrw_global_discount_duration_val_min = get_post_meta( $product_id, 'ovabrw_global_discount_duration_val_min', true );
    if( $ovabrw_global_discount_duration_val_min ){ asort( $ovabrw_global_discount_duration_val_min ); }

    if( $ovabrw_global_discount_duration_val_min ){
        $gl_set_price = get_price_by_global_discount( $product_id, $ovabrw_global_discount_duration_val_min, $price_type='hours', $quantity_hour );
        $gl_price_hour = ( $gl_set_price != false ) ? $gl_set_price : $gl_price_hour;
    }
    return $gl_price_hour;
}

function get_special_price_hour_mixed ($product_id, $ovabrw_rt_price_hour_key, $key_rt, $quantity_hour) {
    $rt_price_hour = $ovabrw_rt_price_hour_key;
    $ovabrw_rt_discount = get_post_meta( $product_id, 'ovabrw_rt_discount', true );

    // Set Price by RT(ST) Discount
    if( isset( $ovabrw_rt_discount[$key_rt] ) ){
        $ovabrw_rt_discount_duration_min = $ovabrw_rt_discount[$key_rt]['min'];
        $ovabrw_rt_discount_duration_max = $ovabrw_rt_discount[$key_rt]['max'];
        arsort($ovabrw_rt_discount_duration_min);
        $ovabrw_rt_discount_duration_type = $ovabrw_rt_discount[$key_rt]['duration_type'];
        $ovabrw_rt_discount_duration_price = $ovabrw_rt_discount[$key_rt]['price'];
        $st_set_price = get_price_by_rt_discount( $ovabrw_rt_discount_duration_min, $ovabrw_rt_discount_duration_max, $ovabrw_rt_discount_duration_type, $ovabrw_rt_discount_duration_price, $price_type='hours', $quantity_hour );
        $rt_price_hour = $st_set_price != false ? $st_set_price : $rt_price_hour;
    }
    return $rt_price_hour;
}


function ovabrw_get_price_by_start_date_transport( $product_id, $ovabrw_pickup_loc, $ovabrw_pickoff_loc ) {
    $list_price_transport = ovabrw_get_list_price_loc_transport($product_id);
    $price_transport = 0;
    if( ! empty( $list_price_transport ) && is_array( $list_price_transport ) ) {
        foreach( $list_price_transport as $pickup => $dropoff_price_arr ) {
            if( $ovabrw_pickup_loc == $pickup && ! empty( $dropoff_price_arr ) && is_array( $dropoff_price_arr ) ) {
                foreach( $dropoff_price_arr as $dropoff => $price ) {
                    if( $ovabrw_pickoff_loc ==  $dropoff ) {
                        $price_transport = $price;
                    }
                }
            }
        }
    }
    return $price_transport;

}


//total price if have discount
function get_total_price_special_by_type_price_day($product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date) {

    $ovabrw_rt_startdate = get_post_meta( $product_id, 'ovabrw_rt_startdate', true );
    $ovabrw_rt_starttime = get_post_meta( $product_id, 'ovabrw_rt_starttime', true );

    $ovabrw_rt_enddate = get_post_meta( $product_id, 'ovabrw_rt_enddate', true );
    $ovabrw_rt_endtime = get_post_meta( $product_id, 'ovabrw_rt_endtime', true );

    

    $price_type = get_post_meta( $product_id, 'ovabrw_price_type', true );
    $total_price_special =  0;
    $rent_time_day = 0;
    if( $ovabrw_rt_startdate ){
        foreach ($ovabrw_rt_startdate as $key_rt => $value_rt) {

            $start_date_str = $value_rt  . ' ' . $ovabrw_rt_starttime[$key_rt];
            $end_date_str = $ovabrw_rt_enddate[$key_rt]  . ' ' . $ovabrw_rt_endtime[$key_rt];

            $start_date = strtotime( $start_date_str );
            $end_date = strtotime( $end_date_str );

            if( $ovabrw_pickup_date >= $start_date && $ovabrw_pickoff_date <=  $end_date ){

                $rent_time = get_time_bew_2day( $ovabrw_pickup_date, $ovabrw_pickoff_date, $product_id );
                $price_special = ovabrw_price_special_time( $product_id, $rent_time, $key_rt );


                if( $price_type == 'mixed' ) {
                    $rent_time_day = floor( $rent_time['rent_time_day_raw'] );
                } else {
                    $rent_time_day = $rent_time['rent_time_day'];
                }

                $total_price_special += $price_special * $rent_time_day;

            }else if( $ovabrw_pickup_date < $start_date && $ovabrw_pickoff_date <=  $end_date && $ovabrw_pickoff_date >= $start_date ){

                $rent_time = get_time_bew_2day( $start_date, $ovabrw_pickoff_date, $product_id );
                $price_special = ovabrw_price_special_time( $product_id, $rent_time, $key_rt );


                if( $price_type == 'mixed' ) {
                    $rent_time_day = floor( $rent_time['rent_time_day_raw'] );
                } else {
                    $rent_time_day = $rent_time['rent_time_day'];
                }

                $total_price_special += $price_special * $rent_time_day;


            }else if( $ovabrw_pickup_date >= $start_date && $ovabrw_pickup_date <= $end_date && $ovabrw_pickoff_date >= $end_date ){

                $rent_time = get_time_bew_2day( $ovabrw_pickup_date, $end_date, $product_id );
                $price_special = ovabrw_price_special_time( $product_id, $rent_time, $key_rt );


                if( $price_type == 'mixed' ) {
                    $rent_time_day = floor( $rent_time['rent_time_day_raw'] );
                } else {
                    $rent_time_day = $rent_time['rent_time_day'] ;
                }

                $total_price_special += $price_special * $rent_time_day;

            }else if( $ovabrw_pickup_date < $start_date && $ovabrw_pickoff_date > $end_date ){

                $rent_time = get_time_bew_2day( $start_date, $end_date, $product_id );
                $price_special = ovabrw_price_special_time( $product_id, $rent_time, $key_rt );


                if( $price_type == 'mixed' ) {
                    $rent_time_day = floor( $rent_time['rent_time_day_raw']);
                } else {
                    $rent_time_day = $rent_time['rent_time_day'];
                }

                $total_price_special += $price_special * $rent_time_day;
            }

        }
    }


    return $total_price_special;
}

//total price real if no discount
function get_total_price_special_by_type_price_day_sub( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date, $array_price_week = [], $weekday_start = null ) {

    $ovabrw_rt_startdate = get_post_meta( $product_id, 'ovabrw_rt_startdate', true );
    $ovabrw_rt_starttime = get_post_meta( $product_id, 'ovabrw_rt_starttime', true );


    $ovabrw_rt_enddate = get_post_meta( $product_id, 'ovabrw_rt_enddate', true );
    $ovabrw_rt_endtime = get_post_meta( $product_id, 'ovabrw_rt_endtime', true );

   


    $price_type = get_post_meta( $product_id, 'ovabrw_price_type', true );
    $total_price_sub = 0;


    if( $ovabrw_rt_startdate ){
        foreach ($ovabrw_rt_startdate as $key_rt => $value_rt) {

            $start_date_str = $value_rt  . ' ' . $ovabrw_rt_starttime[$key_rt];
            $end_date_str = $ovabrw_rt_enddate[$key_rt] . ' ' . $ovabrw_rt_endtime[$key_rt];

            $start_date = strtotime( $start_date_str );
            $end_date = strtotime( $end_date_str );

            if( $ovabrw_pickup_date >= $start_date && $ovabrw_pickoff_date <= $end_date ){

                $rent_time = get_time_bew_2day( $ovabrw_pickup_date, $ovabrw_pickoff_date, $product_id );
                $weekday_start = wp_date('N', $ovabrw_pickup_date);
                $price_special = ovabrw_price_special_time( $product_id, $rent_time, $key_rt  );

                if( $price_type == 'mixed' ) {
                    $rent_time_day = floor( $rent_time['rent_time_day_raw'] );
                } else {
                    $rent_time_day = $rent_time['rent_time_day'];
                }


                $total_price_sub += get_price_day_by_weekday_start($weekday_start, $rent_time_day, $array_price_week);

            }else if( $ovabrw_pickup_date < $start_date && $ovabrw_pickoff_date <= $end_date && $ovabrw_pickoff_date >= $start_date ){

                $rent_time = get_time_bew_2day( $start_date, $ovabrw_pickoff_date, $product_id );
                $weekday_start = wp_date('N', $start_date);
                
                $price_special = ovabrw_price_special_time( $product_id, $rent_time, $key_rt  );

                if( $price_type == 'mixed' ) {
                    $rent_time_day = floor( $rent_time['rent_time_day_raw'] );
                } else {
                    $rent_time_day = $rent_time['rent_time_day'];
                }


                $total_price_sub += get_price_day_by_weekday_start($weekday_start, $rent_time_day, $array_price_week);

            }else if( $ovabrw_pickup_date >= $start_date && $ovabrw_pickup_date <= $end_date && $ovabrw_pickoff_date >= $end_date ){

                $rent_time = get_time_bew_2day( $ovabrw_pickup_date, $end_date, $product_id );
                $weekday_start = wp_date('N', $ovabrw_pickup_date );
                $price_special = ovabrw_price_special_time( $product_id, $rent_time, $key_rt );
                
                if( $price_type == 'mixed' ) {
                    $rent_time_day = floor( $rent_time['rent_time_day_raw']);
                } else {
                    $rent_time_day = $rent_time['rent_time_day'];
                }

                $total_price_sub += get_price_day_by_weekday_start($weekday_start, $rent_time_day, $array_price_week);

            }else if( $ovabrw_pickup_date < $start_date && $ovabrw_pickoff_date > $end_date ){

                $rent_time = get_time_bew_2day( $start_date, $end_date, $product_id );
                $weekday_start = wp_date('N', $start_date);
                $price_special = ovabrw_price_special_time( $product_id, $rent_time, $key_rt );

                if( $price_type == 'mixed' ) {
                    $rent_time_day = floor( $rent_time['rent_time_day_raw'] );
                } else {
                    $rent_time_day = $rent_time['rent_time_day'];
                }

                $total_price_sub += get_price_day_by_weekday_start($weekday_start, $rent_time_day, $array_price_week);

            }    
            
        }
    }
    return $total_price_sub;

}


function get_price_by_rental_type_day ( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date ) {

    $ovabrw_pickup_date     = $ovabrw_pickup_date == '' ? null : $ovabrw_pickup_date;
    $ovabrw_pickoff_date    = $ovabrw_pickoff_date == '' ? null : $ovabrw_pickoff_date;

    $rent_time = get_time_bew_2day( $ovabrw_pickup_date, $ovabrw_pickoff_date, $product_id );
    $weekstart = wp_date('N', $ovabrw_pickup_date);

    $price_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

    if( $price_type == 'mixed' ) {
        $number_day = floor( $rent_time['rent_time_day_raw'] );
    } else {
        $number_day = $rent_time['rent_time_day'];
    }

    $regular_price = ! empty( get_post_meta( $product_id, '_regular_price', true ) ) ? get_post_meta( $product_id, '_regular_price', true ) : 0;


    $ovabrw_daily_monday =  get_post_meta( $product_id, 'ovabrw_daily_monday', true ) ? (float)get_post_meta( $product_id, 'ovabrw_daily_monday', true ) : $regular_price;
    $ovabrw_daily_tuesday = get_post_meta( $product_id, 'ovabrw_daily_tuesday', true ) ? (float)get_post_meta( $product_id, 'ovabrw_daily_tuesday', true ) : $regular_price;
    $ovabrw_daily_wednesday = get_post_meta( $product_id, 'ovabrw_daily_wednesday', true ) ? (float)get_post_meta( $product_id, 'ovabrw_daily_wednesday', true ) : $regular_price;
    $ovabrw_daily_thursday = get_post_meta( $product_id, 'ovabrw_daily_thursday', true ) ? (float)get_post_meta( $product_id, 'ovabrw_daily_thursday', true ) : $regular_price;
    $ovabrw_daily_friday = get_post_meta( $product_id, 'ovabrw_daily_friday', true ) ?  (float)get_post_meta( $product_id, 'ovabrw_daily_friday', true ) : $regular_price;
    $ovabrw_daily_saturday = get_post_meta( $product_id, 'ovabrw_daily_saturday', true ) ? (float)get_post_meta( $product_id, 'ovabrw_daily_saturday', true ) : $regular_price;
    $ovabrw_daily_sunday = get_post_meta( $product_id, 'ovabrw_daily_sunday', true ) ? (float)get_post_meta( $product_id, 'ovabrw_daily_sunday', true ) : $regular_price;

    $price_global = ovabrw_price_global_by_type_day( $product_id, $rent_time );


    if ( ! empty ( $price_global ) ) {
        $ovabrw_daily_monday = $ovabrw_daily_tuesday = $ovabrw_daily_wednesday = $ovabrw_daily_thursday = $ovabrw_daily_friday = $ovabrw_daily_saturday = $ovabrw_daily_sunday = $price_global;
    }

    $array_price_week = [$ovabrw_daily_monday, $ovabrw_daily_tuesday, $ovabrw_daily_wednesday, $ovabrw_daily_thursday, $ovabrw_daily_friday, $ovabrw_daily_saturday, $ovabrw_daily_sunday];

    // Get price total from monday to sunday
    $price_total_by_weekday = get_price_day_by_weekday_start($weekstart, $number_day, $array_price_week);
    
    // Get total cost in special time
    $price_special_day = get_total_price_special_by_type_price_day( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date );

    // Get total cost
    $price_special_day_sub = get_total_price_special_by_type_price_day_sub( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date, $array_price_week, $weekstart );

    $price_total = $price_total_by_weekday + $price_special_day - $price_special_day_sub;

    return array( 'quantity' => $number_day, 'line_total' => $price_total );
}


function get_price_day_by_weekday_start ( $weekstart, $number_day, $arry_price ) {


    $number_weeks = floor( $number_day / 7 );
    $rent_day_of_week = $number_day % 7;

    $ovabrw_daily_monday = $arry_price[0];
    $ovabrw_daily_tuesday = $arry_price[1];
    $ovabrw_daily_wednesday = $arry_price[2];
    $ovabrw_daily_thursday = $arry_price[3];
    $ovabrw_daily_friday = $arry_price[4];
    $ovabrw_daily_saturday = $arry_price[5];
    $ovabrw_daily_sunday = $arry_price[6];

    $price_total = $price_number_week = $price_rent_day_of_week = 0;
    switch ( $weekstart ) {

        //monday
        case 1 : {

            $price_number_week = 0;

            if ( $number_weeks > 0 ) {

                $price_mondays = $ovabrw_daily_monday * $number_weeks;
                $price_tuesday = $ovabrw_daily_tuesday * $number_weeks;
                $price_wednesday = $ovabrw_daily_wednesday * $number_weeks;
                $price_thursday = $ovabrw_daily_thursday * $number_weeks;
                $price_friday = $ovabrw_daily_friday * $number_weeks;
                $price_saturday = $ovabrw_daily_saturday * $number_weeks;
                $price_sunday = $ovabrw_daily_sunday * $number_weeks;

                $price_number_week = $price_mondays + $price_tuesday + $price_wednesday + $price_thursday + $price_friday + $price_saturday + $price_sunday;
            }

            if ( $rent_day_of_week > 0 ) {
                if ( $rent_day_of_week === 1 ) {
                    $price_rent_day_of_week = $ovabrw_daily_monday;
                } elseif ( $rent_day_of_week === 2 ) {
                    $price_rent_day_of_week = $ovabrw_daily_monday + $ovabrw_daily_tuesday;
                } elseif ( $rent_day_of_week === 3 ) {
                    $price_rent_day_of_week = $ovabrw_daily_monday + $ovabrw_daily_tuesday + $ovabrw_daily_wednesday;
                } elseif ( $rent_day_of_week === 4 ) {
                    $price_rent_day_of_week = $ovabrw_daily_monday + $ovabrw_daily_tuesday + $ovabrw_daily_wednesday + $ovabrw_daily_thursday;
                } elseif ( $rent_day_of_week === 5 ) {
                    $price_rent_day_of_week = $ovabrw_daily_monday + $ovabrw_daily_tuesday + $ovabrw_daily_wednesday + $ovabrw_daily_thursday + $ovabrw_daily_friday;
                } elseif ( $rent_day_of_week === 6 ) {
                    $price_rent_day_of_week = $ovabrw_daily_monday + $ovabrw_daily_tuesday + $ovabrw_daily_wednesday + $ovabrw_daily_thursday + $ovabrw_daily_friday + $ovabrw_daily_saturday;
                }

            }

            $price_total = $price_number_week + $price_rent_day_of_week;
            break;
        }

        //tuesday
        case 2 : {

            if ( $number_weeks > 0 ) {

                $price_mondays = $ovabrw_daily_monday * $number_weeks;
                $price_tuesday = $ovabrw_daily_tuesday * $number_weeks;
                $price_wednesday = $ovabrw_daily_wednesday * $number_weeks;
                $price_thursday = $ovabrw_daily_thursday * $number_weeks;
                $price_friday = $ovabrw_daily_friday * $number_weeks;
                $price_saturday = $ovabrw_daily_saturday * $number_weeks;
                $price_sunday = $ovabrw_daily_sunday * $number_weeks;

                $price_number_week = $price_mondays + $price_tuesday + $price_wednesday + $price_thursday + $price_friday + $price_saturday + $price_sunday;
            }

            if ( $rent_day_of_week > 0 ) {
                if ( $rent_day_of_week === 1 ) {
                    $price_rent_day_of_week = $ovabrw_daily_tuesday;
                } elseif ( $rent_day_of_week === 2 ) {
                    $price_rent_day_of_week = $ovabrw_daily_tuesday + $ovabrw_daily_wednesday;
                } elseif ( $rent_day_of_week === 3 ) {
                    $price_rent_day_of_week = $ovabrw_daily_tuesday + $ovabrw_daily_wednesday + $ovabrw_daily_thursday;
                } elseif ( $rent_day_of_week === 4 ) {
                    $price_rent_day_of_week = $ovabrw_daily_tuesday + $ovabrw_daily_wednesday + $ovabrw_daily_thursday + $ovabrw_daily_friday;
                } elseif ( $rent_day_of_week === 5 ) {
                    $price_rent_day_of_week = $ovabrw_daily_tuesday + $ovabrw_daily_wednesday + $ovabrw_daily_thursday + $ovabrw_daily_friday + $ovabrw_daily_saturday;
                } elseif ( $rent_day_of_week === 6 ) {
                    $price_rent_day_of_week = $ovabrw_daily_tuesday + $ovabrw_daily_wednesday + $ovabrw_daily_thursday + $ovabrw_daily_friday + $ovabrw_daily_saturday + $ovabrw_daily_sunday;
                }

            }

            $price_total = $price_number_week + $price_rent_day_of_week;
            break;
        }

        //wecnesday
        case 3 : {

            if ( $number_weeks > 0 ) {

                $price_mondays = $ovabrw_daily_monday * $number_weeks;
                $price_tuesday = $ovabrw_daily_tuesday * $number_weeks;
                $price_wednesday = $ovabrw_daily_wednesday * $number_weeks;
                $price_thursday = $ovabrw_daily_thursday * $number_weeks;
                $price_friday = $ovabrw_daily_friday * $number_weeks;
                $price_saturday = $ovabrw_daily_saturday * $number_weeks;
                $price_sunday = $ovabrw_daily_sunday * $number_weeks;

                $price_number_week = $price_mondays + $price_tuesday + $price_wednesday + $price_thursday + $price_friday + $price_saturday + $price_sunday;
            }

            if ( $rent_day_of_week > 0 ) {
                if ( $rent_day_of_week === 1 ) {
                    $price_rent_day_of_week = $ovabrw_daily_wednesday;
                } elseif ( $rent_day_of_week === 2 ) {
                    $price_rent_day_of_week = $ovabrw_daily_wednesday + $ovabrw_daily_thursday;
                } elseif ( $rent_day_of_week === 3 ) {
                    $price_rent_day_of_week = $ovabrw_daily_wednesday + $ovabrw_daily_thursday + $ovabrw_daily_friday;
                } elseif ( $rent_day_of_week === 4 ) {
                    $price_rent_day_of_week = $ovabrw_daily_wednesday + $ovabrw_daily_thursday + $ovabrw_daily_friday + $ovabrw_daily_saturday;
                } elseif ( $rent_day_of_week === 5 ) {
                    $price_rent_day_of_week = $ovabrw_daily_wednesday + $ovabrw_daily_thursday + $ovabrw_daily_friday + $ovabrw_daily_saturday + $ovabrw_daily_sunday;
                } elseif ( $rent_day_of_week === 6 ) {
                    $price_rent_day_of_week = $ovabrw_daily_wednesday + $ovabrw_daily_thursday + $ovabrw_daily_friday + $ovabrw_daily_saturday + $ovabrw_daily_sunday + $ovabrw_daily_monday;
                }

            }

            $price_total = $price_number_week + $price_rent_day_of_week;
            break;
        }

        //thursday
        case 4 : {

            if ( $number_weeks > 0 ) {

                $price_mondays = $ovabrw_daily_monday * $number_weeks;
                $price_tuesday = $ovabrw_daily_tuesday * $number_weeks;
                $price_wednesday = $ovabrw_daily_wednesday * $number_weeks;
                $price_thursday = $ovabrw_daily_thursday * $number_weeks;
                $price_friday = $ovabrw_daily_friday * $number_weeks;
                $price_saturday = $ovabrw_daily_saturday * $number_weeks;
                $price_sunday = $ovabrw_daily_sunday * $number_weeks;

                $price_number_week = $price_mondays + $price_tuesday + $price_wednesday + $price_thursday + $price_friday + $price_saturday + $price_sunday;
            }

            if ( $rent_day_of_week > 0 ) {
                if ( $rent_day_of_week === 1 ) {
                    $price_rent_day_of_week = $ovabrw_daily_thursday;
                } elseif ( $rent_day_of_week === 2 ) {
                    $price_rent_day_of_week = $ovabrw_daily_thursday + $ovabrw_daily_friday;
                } elseif ( $rent_day_of_week === 3 ) {
                    $price_rent_day_of_week = $ovabrw_daily_thursday + $ovabrw_daily_friday + $ovabrw_daily_saturday;
                } elseif ( $rent_day_of_week === 4 ) {
                    $price_rent_day_of_week = $ovabrw_daily_thursday + $ovabrw_daily_friday + $ovabrw_daily_saturday + $ovabrw_daily_sunday;
                } elseif ( $rent_day_of_week === 5 ) {
                    $price_rent_day_of_week = $ovabrw_daily_thursday + $ovabrw_daily_friday + $ovabrw_daily_saturday + $ovabrw_daily_sunday + $ovabrw_daily_monday;
                } elseif ( $rent_day_of_week === 6 ) {
                    $price_rent_day_of_week = $ovabrw_daily_thursday + $ovabrw_daily_friday + $ovabrw_daily_saturday + $ovabrw_daily_sunday + $ovabrw_daily_monday +  $ovabrw_daily_tuesday;
                }

            }

            $price_total = $price_number_week + $price_rent_day_of_week;
            break;
        }


        //friday
        case 5 : {

            if ( $number_weeks > 0 ) {

                $price_mondays = $ovabrw_daily_monday * $number_weeks;
                $price_tuesday = $ovabrw_daily_tuesday * $number_weeks;
                $price_wednesday = $ovabrw_daily_wednesday * $number_weeks;
                $price_thursday = $ovabrw_daily_thursday * $number_weeks;
                $price_friday = $ovabrw_daily_friday * $number_weeks;
                $price_saturday = $ovabrw_daily_saturday * $number_weeks;
                $price_sunday = $ovabrw_daily_sunday * $number_weeks;

                $price_number_week = $price_mondays + $price_tuesday + $price_wednesday + $price_thursday + $price_friday + $price_saturday + $price_sunday;
            }

            if ( $rent_day_of_week > 0 ) {
                if ( $rent_day_of_week === 1 ) {
                    $price_rent_day_of_week = $ovabrw_daily_friday;
                } elseif ( $rent_day_of_week === 2 ) {
                    $price_rent_day_of_week = $ovabrw_daily_friday + $ovabrw_daily_saturday;
                } elseif ( $rent_day_of_week === 3 ) {
                    $price_rent_day_of_week = $ovabrw_daily_friday + $ovabrw_daily_saturday + $ovabrw_daily_sunday;
                } elseif ( $rent_day_of_week === 4 ) {
                    $price_rent_day_of_week = $ovabrw_daily_friday + $ovabrw_daily_saturday + $ovabrw_daily_sunday + $ovabrw_daily_monday;
                } elseif ( $rent_day_of_week === 5 ) {
                    $price_rent_day_of_week = $ovabrw_daily_friday + $ovabrw_daily_saturday + $ovabrw_daily_sunday + $ovabrw_daily_monday +  $ovabrw_daily_tuesday;
                } elseif ( $rent_day_of_week === 6 ) {
                    $price_rent_day_of_week = $ovabrw_daily_friday + $ovabrw_daily_saturday + $ovabrw_daily_sunday + $ovabrw_daily_monday +  $ovabrw_daily_tuesday + $ovabrw_daily_wednesday;
                }

            }

            $price_total = $price_number_week + $price_rent_day_of_week;
            break;
        }

        //saturday
        case 6 : {

            if ( $number_weeks > 0 ) {

                $price_mondays = $ovabrw_daily_monday * $number_weeks;
                $price_tuesday = $ovabrw_daily_tuesday * $number_weeks;
                $price_wednesday = $ovabrw_daily_wednesday * $number_weeks;
                $price_thursday = $ovabrw_daily_thursday * $number_weeks;
                $price_friday = $ovabrw_daily_friday * $number_weeks;
                $price_saturday = $ovabrw_daily_saturday * $number_weeks;
                $price_sunday = $ovabrw_daily_sunday * $number_weeks;

                $price_number_week = $price_mondays + $price_tuesday + $price_wednesday + $price_thursday + $price_friday + $price_saturday + $price_sunday;
            }

            if ( $rent_day_of_week > 0 ) {
                if ( $rent_day_of_week === 1 ) {
                    $price_rent_day_of_week = $ovabrw_daily_saturday;
                } elseif ( $rent_day_of_week === 2 ) {
                    $price_rent_day_of_week = $ovabrw_daily_saturday + $ovabrw_daily_sunday;
                } elseif ( $rent_day_of_week === 3 ) {
                    $price_rent_day_of_week = $ovabrw_daily_saturday + $ovabrw_daily_sunday + $ovabrw_daily_monday;
                } elseif ( $rent_day_of_week === 4 ) {
                    $price_rent_day_of_week = $ovabrw_daily_saturday + $ovabrw_daily_sunday + $ovabrw_daily_monday +  $ovabrw_daily_tuesday;
                } elseif ( $rent_day_of_week === 5 ) {
                    $price_rent_day_of_week = $ovabrw_daily_saturday + $ovabrw_daily_sunday + $ovabrw_daily_monday +  $ovabrw_daily_tuesday + $ovabrw_daily_wednesday;
                } elseif ( $rent_day_of_week === 6 ) {
                    $price_rent_day_of_week = $ovabrw_daily_saturday + $ovabrw_daily_sunday + $ovabrw_daily_monday +  $ovabrw_daily_tuesday + $ovabrw_daily_wednesday + $ovabrw_daily_thursday;
                }

            }

            $price_total = $price_number_week + $price_rent_day_of_week;
            break;
        }


        //sunday
        case 7 : {

            if ( $number_weeks > 0 ) {

                $price_mondays = $ovabrw_daily_monday * $number_weeks;
                $price_tuesday = $ovabrw_daily_tuesday * $number_weeks;
                $price_wednesday = $ovabrw_daily_wednesday * $number_weeks;
                $price_thursday = $ovabrw_daily_thursday * $number_weeks;
                $price_friday = $ovabrw_daily_friday * $number_weeks;
                $price_saturday = $ovabrw_daily_saturday * $number_weeks;
                $price_sunday = $ovabrw_daily_sunday * $number_weeks;

                $price_number_week = $price_mondays + $price_tuesday + $price_wednesday + $price_thursday + $price_friday + $price_saturday + $price_sunday;
            }

            if ( $rent_day_of_week > 0 ) {
                if ( $rent_day_of_week === 1 ) {
                    $price_rent_day_of_week = $ovabrw_daily_sunday;
                } elseif ( $rent_day_of_week === 2 ) {
                    $price_rent_day_of_week = $ovabrw_daily_sunday + $ovabrw_daily_monday;
                } elseif ( $rent_day_of_week === 3 ) {
                    $price_rent_day_of_week = $ovabrw_daily_sunday + $ovabrw_daily_monday +  $ovabrw_daily_tuesday;
                } elseif ( $rent_day_of_week === 4 ) {
                    $price_rent_day_of_week = $ovabrw_daily_sunday + $ovabrw_daily_monday +  $ovabrw_daily_tuesday + $ovabrw_daily_wednesday;
                } elseif ( $rent_day_of_week === 5 ) {
                    $price_rent_day_of_week = $ovabrw_daily_sunday + $ovabrw_daily_monday +  $ovabrw_daily_tuesday + $ovabrw_daily_wednesday + $ovabrw_daily_thursday;
                } elseif ( $rent_day_of_week === 6 ) {
                    $price_rent_day_of_week = $ovabrw_daily_sunday + $ovabrw_daily_monday +  $ovabrw_daily_tuesday + $ovabrw_daily_wednesday + $ovabrw_daily_thursday + $ovabrw_daily_friday;
                }

            }

            $price_total = $price_number_week + $price_rent_day_of_week;
            break;
        }
    }
    return $price_total;
}

// Get Real Price
function get_real_price_detail( $product_price, $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date ){

    $rent_time = get_time_bew_2day( $ovabrw_pickup_date, $ovabrw_pickoff_date, $product_id );
    $price_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

    $ovabrw_rt_startdate = get_post_meta( $product_id, 'ovabrw_rt_startdate', true );
    $ovabrw_rt_enddate = get_post_meta( $product_id, 'ovabrw_rt_enddate', true );


        // Global
    $gl_price = ovabrw_price_global( $product_id, $rent_time );
    $product_price =  $gl_price ;


    if( $ovabrw_rt_startdate ){
        foreach ($ovabrw_rt_startdate as $key_rt => $value_rt) {

                // If Special_Time_Start_Date <= pickup_date && pickoff_date <= Special_Time_End_Date
            if( $ovabrw_pickup_date >= strtotime( $ovabrw_rt_startdate[$key_rt] ) && $ovabrw_pickoff_date <= strtotime( $ovabrw_rt_enddate[$key_rt] ) ){

                $rt_price = ovabrw_price_special_time( $product_id, $rent_time, $key_rt );

                $product_price =  $rt_price.' '.ovabrw_text_time_rt( $price_type, $rent_time );

                break;
                
            }else if( $ovabrw_pickup_date < strtotime( $ovabrw_rt_startdate[$key_rt] ) && $ovabrw_pickoff_date <= strtotime( $ovabrw_rt_enddate[$key_rt] ) && $ovabrw_pickoff_date >= strtotime( $ovabrw_rt_startdate[$key_rt] ) ){


                $gl_quantity_array = get_time_bew_2day( $ovabrw_pickup_date, strtotime( $ovabrw_rt_startdate[$key_rt] ), $product_id );
                $gl_price = ovabrw_price_global( $product_id, $gl_quantity_array );

                $rt_quantity_array = get_time_bew_2day( strtotime( $ovabrw_rt_startdate[$key_rt] ), $ovabrw_pickoff_date, $product_id );
                $rt_price = ovabrw_price_special_time( $product_id, $rt_quantity_array, $key_rt );

                $product_price =  $gl_price .' '.ovabrw_text_time_gl( $price_type, $rent_time ).' ' . $rt_price  .' '.ovabrw_text_time_rt( $price_type, $rent_time );

                break;


            }else if( $ovabrw_pickup_date >= strtotime( $ovabrw_rt_startdate[$key_rt] ) && $ovabrw_pickup_date <= strtotime( $ovabrw_rt_enddate[$key_rt] ) && $ovabrw_pickoff_date >= strtotime( $ovabrw_rt_enddate[$key_rt] ) ){


                $rt_quantity_array = get_time_bew_2day( $ovabrw_pickup_date, strtotime( $ovabrw_rt_enddate[$key_rt] ), $product_id );
                $rt_price = ovabrw_price_special_time( $product_id, $rt_quantity_array, $key_rt );


                $gl_quantity_array = get_time_bew_2day( strtotime( $ovabrw_rt_enddate[$key_rt] ), $ovabrw_pickoff_date, $product_id );
                $gl_price = ovabrw_price_global( $product_id, $gl_quantity_array );


                $product_price =  $gl_price . ' ' . ovabrw_text_time_gl( $price_type, $rent_time ) . ' ' . $rt_price . ' ' . ovabrw_text_time_rt( $price_type, $rent_time );

                break;

            }else if( $ovabrw_pickup_date < strtotime( $ovabrw_rt_startdate[$key_rt] ) && $ovabrw_pickoff_date > strtotime( $ovabrw_rt_enddate[$key_rt] ) ){

                    // Time section 1
                $gl_quantity_array = get_time_bew_2day( $ovabrw_pickup_date, strtotime( $ovabrw_rt_startdate[$key_rt] ), $product_id );
                $gl_price = ovabrw_price_global( $product_id, $gl_quantity_array );


                $rent_time_2 = get_time_bew_2day( strtotime( $ovabrw_rt_startdate[$key_rt] ), strtotime( $ovabrw_rt_enddate[$key_rt] ), $product_id );
                $rt_price = ovabrw_price_special_time( $product_id, $rent_time_2, $key_rt );

                $product_price = $rt_price . ' ' . ovabrw_text_time_rt( $price_type, $rent_time ) . ' ' . $gl_price . ' ' . ovabrw_text_time_gl( $price_type, $rent_time );

                break;

            }

        }
    }

    return $product_price; 
}
