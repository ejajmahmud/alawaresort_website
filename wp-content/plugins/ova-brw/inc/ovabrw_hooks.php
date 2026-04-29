<?php if ( !defined( 'ABSPATH' ) ) exit();


/* Load Template */
add_filter( 'template_include', 'ovabrw_template_loader', 99 );

function ovabrw_template_loader( $template ) {

    $search = isset( $_REQUEST['ovabrw_search'] ) ? esc_html( $_REQUEST['ovabrw_search'] ) : '';
    $request_booking = isset( $_REQUEST['request_booking'] ) ? esc_html( $_REQUEST['request_booking'] ) : '';

    // Get product template
    $product_template   = get_option( 'ova_brw_template_elementor_template', 'default' );

    if ( is_product() ) {

        if ( $product_template == 'default' ) {

            $product_id = get_the_id();

            $template_by_category = ovabrw_get_product_template( $product_id );


            if ( $template_by_category && $template_by_category !== 'default' ) {

                $template = ovabrw_get_template( 'ovabrw_single_product.php' );
            }
        } else {
            $template = ovabrw_get_template( 'ovabrw_single_product.php' );
        }
    }
    
    // Search Form
    if( $search != '' ){
        return ovabrw_get_template( 'search_result.php' );
    }
    
    // Request Booking Form
    if( $request_booking != ''){

        if( ovabrw_request_booking( $_REQUEST ) ){

            wp_redirect( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_thank_page', home_url('/') ) ) );

        }else{
            
            wp_redirect( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_error_page', home_url('/') ) ) );

        }

        exit();

    }

    return $template;

}




// ADD A PRODUCT TYPE **************************************/
add_filter( 'product_type_selector', 'ovabrw_add_custom_product_type', 10, 1 );
add_action( 'init', 'ovabrw_create_custom_product_type' );

function ovabrw_add_custom_product_type( $types ){
    $types[ 'ovabrw_car_rental' ] = esc_html__( 'Rental', 'ova-brw' );
    return $types;
}

function ovabrw_create_custom_product_type(){
     // declare the product class
     class WC_Product_Ovabrw_car_rental extends WC_Product{
        public function get_type(){
            return 'ovabrw_car_rental';
        }
    }
}





/* Hide Rental Type, Price Detail at Frontend */
if( !is_admin() ){ add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'change_formatted_meta_data', 20, 2 ); }
function change_formatted_meta_data( $meta_data, $item ) {
    $new_meta = array();
    foreach ( $meta_data as $id => $meta_array ) {
        // We are removing the meta with the key 'something' from the whole array.
        if ( 'rental_type' === $meta_array->key || 'ovabrw_price_detail' === $meta_array->key || 'package_type' === $meta_array->key ) { continue; }
        $new_meta[ $id ] = $meta_array;
    }
    return $new_meta;
}


// Filter Quantity for Cart
add_filter( 'woocommerce_widget_cart_item_quantity', 'ovabrw_woocommerce_widget_cart_item_quantity', 10, 3 );
function ovabrw_woocommerce_widget_cart_item_quantity( $product_quantity,  $cart_item, $cart_item_key ) {

    if( $cart_item['data']->is_type('ovabrw_car_rental') ){

        if( isset( $cart_item['rental_type'] ) && ( $cart_item['rental_type'] == 'period_time' ) ){

            if ( isset($cart_item['ovabrw_number_vehicle']) && $cart_item['ovabrw_number_vehicle'] ) {
                return esc_html__('× ', 'ova-brw') . $cart_item['ovabrw_number_vehicle'];
            }
            return '× 1';

        }else{

            $product_id = $cart_item['product_id'];
            $ovabrw_pickup_date = strtotime( $cart_item['ovabrw_pickup_date'] );
            $ovabrw_pickoff_date = strtotime( $cart_item['ovabrw_pickoff_date'] );
            if( $cart_item['rental_type'] == 'transportation' ) {

                return esc_html__(' × Day(s)', 'ova-brw');
                
            } else {
                return '× '.get_real_quantity( $product_quantity, $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date );
            }
        }

    }else{

        return $product_quantity;

    }
    
}; 


add_filter( 'woocommerce_cart_item_quantity', 'ovabrw_filter_woocommerce_cart_item_quantity', 10, 3 ); 
function ovabrw_filter_woocommerce_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {

    if( $cart_item['data']->is_type('ovabrw_car_rental') ){

        $product_id = $cart_item['product_id'];
        $ovabrw_rental_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

        if( isset( $cart_item['rental_type'] ) && ( $cart_item['rental_type'] == 'period_time' ) ){

            if ( isset($cart_item['ovabrw_number_vehicle']) && $cart_item['ovabrw_number_vehicle'] ) {
                return $cart_item['ovabrw_number_vehicle'] . esc_html__(' ×', 'ova-brw') . '(' . $cart_item['period_label'] . ')';
            }
            return intval(1);

        }else{

            $product_id = $cart_item['product_id'];
            $ovabrw_pickup_date = strtotime( $cart_item['ovabrw_pickup_date'] );
            $ovabrw_pickoff_date = strtotime( $cart_item['ovabrw_pickoff_date'] );

            if( $ovabrw_rental_type === 'transportation' ) {
                if ( isset($cart_item['ovabrw_number_vehicle']) && $cart_item['ovabrw_number_vehicle'] ) {
                    return $cart_item['ovabrw_number_vehicle'] . esc_html__(' × Day(s)', 'ova-brw');
                } else {
                    return intval(1) . esc_html__('Day(s)', 'ova-brw');
                }
                
            } else {
                return get_real_quantity( $product_quantity, $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date );
            }
               
        }

    }else{

        return  $product_quantity;

    }
    
}; 


// Filter Quantity for Checkout
add_filter( 'woocommerce_checkout_cart_item_quantity', 'ovabrw_woocommerce_checkout_cart_item_quantity', 10, 3 );
function ovabrw_woocommerce_checkout_cart_item_quantity( $product_quantity, $cart_item, $cart_item_key ){

    if( $cart_item['data']->is_type('ovabrw_car_rental') ){

        $product_id = $cart_item['product_id'];
        $ovabrw_rental_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

        if( ( $ovabrw_rental_type == 'period_time' || $ovabrw_rental_type == 'transportation' ) ){
            return '<span class="ovabrw_qty">1</span>';
        }else{

            $ovabrw_pickup_date = strtotime( $cart_item['ovabrw_pickup_date'] );
            $ovabrw_pickoff_date = strtotime( $cart_item['ovabrw_pickoff_date'] );

            return '<span class="ovabrw_qty">'.get_real_quantity( $product_quantity, $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date ).'</span>';    
        }
        

    }else{

        return  $product_quantity;

    }

}


// Filter Price for Cart
add_filter( 'woocommerce_cart_item_price', 'ovabrw_filter_woocommerce_cart_item_price', 10, 3 ); 
function ovabrw_filter_woocommerce_cart_item_price( $product_price, $cart_item, $cart_item_key ) {

    $product_id = $cart_item['product_id'];

    if ( $cart_item['data']->is_type('ovabrw_car_rental') ) {

        $ovabrw_rental_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

        if( $ovabrw_rental_type == 'period_time' ){

            return isset( $cart_item['period_price'] ) ? wc_price( ovabrw_get_price_include_tax( $product_id, $cart_item['period_price'] ) ) : 0;

        } else {
            
            $ovabrw_pickup_date  = isset( $cart_item['ovabrw_pickup_date'] ) ? strtotime( $cart_item['ovabrw_pickup_date'] ) : '';
            $ovabrw_pickoff_date = isset( $cart_item['ovabrw_pickoff_date'] ) ? strtotime( $cart_item['ovabrw_pickoff_date'] ) : '';
            if( $ovabrw_rental_type == 'transportation' ) {
                return isset( $cart_item['price_transport'] ) ? wc_price( ovabrw_get_price_include_tax( $product_id, $cart_item['price_transport'] ) ) : 0;
            } else {
                return  get_real_price( $product_price, $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date );
            }
            
        }     
        
    } else {
        return $product_price;
    }
    
}

// Filter Subtotal for Checkout
add_filter( 'woocommerce_cart_item_subtotal', 'ovabrw_filter_woocommerce_cart_item_subtotal', 10, 3 ); 
function ovabrw_filter_woocommerce_cart_item_subtotal( $product_price, $cart_item, $cart_item_key ) {

    $pay_total_html = $html = '';

    if ( $cart_item['data']->is_type('ovabrw_car_rental') ) {

        // Show full amount
        $ova_enable_deposit = isset ( $cart_item['ova_enable_deposit'] ) ? $cart_item['ova_enable_deposit'] : 'no';
        $ova_type_deposit   = isset ( $cart_item['ova_type_deposit'] )   ? $cart_item['ova_type_deposit']   : 'full';

        // Get total price
        $pay_total          = $cart_item['data']->get_meta('pay_total');

        if ( $ova_enable_deposit === 'yes' && $ova_type_deposit == 'deposit' && $pay_total ) {

            $pay_total_html .= ovabrw_get_html_total_pay( $pay_total, $cart_item );
        }

        // Init html resources + services
        $product_id = $cart_item['product_id'];
        $ovabrw_pickup_date  = strtotime( $cart_item['ovabrw_pickup_date'] );
        $ovabrw_pickoff_date = strtotime( $cart_item['ovabrw_pickoff_date'] );

        // Get html Resources
        $resource_html = ovabrw_get_html_resources( $product_id, $cart_item['resources'], $ovabrw_pickup_date, $ovabrw_pickoff_date );

        // Get html Services
        $service_html  = ovabrw_get_html_services( $product_id, json_decode( $cart_item['ovabrw_service'] ), $ovabrw_pickup_date, $ovabrw_pickoff_date );

        // Check exist resource_html and service_html
        $html = ovabrw_get_html_extra( $resource_html, $service_html );
    }

    return $product_price . $pay_total_html . $html;
}

// Filter Subtotal for Order Detail
add_filter( 'woocommerce_order_formatted_line_subtotal', 'ovabrw_filter_woocommerce_order_formatted_line_subtotal', 10, 3 ); 
function ovabrw_filter_woocommerce_order_formatted_line_subtotal( $subtotal, $item, $order ) {
    
    $pay_total_html = $html = '';

    // Show full amount
    $item_id = $item->get_id();
    $deponsit_amount  = wc_get_order_item_meta( $item_id, 'ovabrw_deposit_amount_product' );
    $remaining_amount = wc_get_order_item_meta( $item_id, 'ovabrw_remaining_amount_product' );
    if ( $deponsit_amount && $remaining_amount ) {
        // Get total price
        $pay_total       = $deponsit_amount + $remaining_amount;
        $pay_total_html .= '<br/><small>' . sprintf( __( '%s payable in total', 'ova-brw' ), wc_price( $pay_total ) ) . '</small>';
    }

    // Init html resources + services
    $resource_html = $service_html = '';
    $product_id = $item['product_id'];
    $resources  = $item->get_meta('ovabrw_resources');
    $services   = $item->get_meta('ovabrw_services');
    $start_date = strtotime( $item->get_meta('ovabrw_pickup_date') );
    $end_date   = strtotime( $item->get_meta('ovabrw_pickoff_date') );

    if ( !empty( $resources ) && is_array( $resources ) ) {
        $resource_html = ovabrw_get_html_resources( $product_id, $resources, $start_date, $end_date );
    }

    if ( !empty( $services ) && is_array( $services ) ) {
        $service_html = ovabrw_get_html_services($product_id, $services, $start_date, $end_date );
    }

    // Check exist resource_html and service_html
    $html = ovabrw_get_html_extra( $resource_html, $service_html );

    return $subtotal . $pay_total_html . $html; 
}

// Filter Quantity for Order detail after checkout
add_filter( 'woocommerce_order_item_quantity_html', 'ovabrw_woocommerce_order_item_quantity_html', 10, 2 ); 
function ovabrw_woocommerce_order_item_quantity_html( $quantity, $item ){

    $product_id = $item->get_product_id();
    $product = wc_get_product( $product_id );

    $ovabrw_date_format = ovabrw_get_date_format();
    $ovabrw_time_format = ovabrw_get_time_format_php();
    

    $ovabrw_date_time_format = $ovabrw_date_format . ' ' . $ovabrw_time_format;

    $rental_type_period = false;

    if( $product->is_type( 'ovabrw_car_rental' ) ){

        foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
            if( $meta->key == 'ovabrw_pickup_date' ){
                $ovabrw_pickup_date = date_i18n( $ovabrw_date_time_format, strtotime( $meta->value ) );
            }
            if( $meta->key == 'ovabrw_pickoff_date' ){
                $ovabrw_pickoff_date = date_i18n( $ovabrw_date_time_format, strtotime( $meta->value ) );
            }
            if( $meta->key == 'period_label' ){
                $rental_type_period = true;
            }
        }

        return '<span class="ovabrw_qty"></span>';  
        
    }

    return $quantity;
    
}


/**
 * Changing a meta title
 * @param  string        $key  The meta key
 * @param  WC_Meta_Data  $meta The meta object
 * @param  WC_Order_Item $item The order item object
 * @return string        The title
 */
/* Change Order at backend */
add_filter( 'woocommerce_order_item_display_meta_key', 'ovabrw_change_order_item_meta_title', 20, 3 );
function ovabrw_change_order_item_meta_title( $key, $meta, $item ) {

    $ovabrw_date_format = ovabrw_get_date_format();
    $ovabrw_time_format = ovabrw_get_time_format_php();
    
    $ovabrw_date_time_format = $ovabrw_date_format . ' ' . $ovabrw_time_format;
    $package_type = $item->get_meta('package_type');
    if ( 'other' === $package_type ) {
        $ovabrw_date_time_format = $ovabrw_date_format;
    }
    
    $id_product = $item['product_id'];
    $defined_one_day = defined_one_day( $id_product );

    // wc_tax_enabled
    $tax_text = $tax_text_remaining = '';

    if ( wc_tax_enabled() ) {

        $item_id  = $item->get_id();
        $order_id = $item->get_order_id();
        $order    = wc_get_order( $order_id );

        $remaining_item  = wc_get_order_item_meta( $item_id, 'ovabrw_remaining_amount_product', true );
        $is_tax_included = $order->get_meta( '_ova_tax_display_cart', true );
        $remaining_taxes = $order->get_meta( '_ova_remaining_taxes', true );
        $tax_message     = $is_tax_included ? __( '(incl. tax)', 'ova-brw' ) : __( '(excl. tax)', 'ova-brw' );

        if ( ! empty( $remaining_taxes ) ) {
            $tax_text    = ' <small class="tax_label">' . $tax_message . '</small>';
        }

        if ( ! empty( $remaining_item && ! empty( $remaining_taxes ) ) ) {
            $tax_text_remaining = ' <small class="tax_label">' . $tax_message . '</small>';
        }
    }

    // By using $meta-key we are sure we have the correct one.
    if ( 'ovabrw_number_vehicle' === $meta->key ) { $key = esc_html__(' Quantity ', 'ova-brw'); }
    if ( 'ovabrw_pickup_loc' === $meta->key ) { $key = esc_html__(' Pick-up Location ', 'ova-brw'); }
    if ( 'ovabrw_pickoff_loc' === $meta->key ) { $key = esc_html__(' Drop-off Location ', 'ova-brw'); }
    if ( 'ovabrw_pickup_date' === $meta->key ) { 

        $defined_one_day = defined_one_day( $id_product );

        $ovabrw_pickup_date = $meta->value;
        $meta->value = date_i18n( $ovabrw_date_time_format, strtotime( $ovabrw_pickup_date ) );

        if( $defined_one_day == 'hotel' ) {
            $meta->value = date_i18n( $ovabrw_date_format, strtotime( $ovabrw_pickup_date ) );
        }

        $key = esc_html__(' Pick-up date ', 'ova-brw'); 
    }
    if ( 'ovabrw_pickoff_date' === $meta->key ) { 

        $ovabrw_pickoff_date = $meta->value;
        $meta->value = date_i18n( $ovabrw_date_time_format, strtotime( $ovabrw_pickoff_date ) );

        if( $defined_one_day == 'hotel' ) {
            $meta->value = date_i18n( $ovabrw_date_format, strtotime( $ovabrw_pickoff_date ) );
        }

        $key = esc_html__(' Drop-off date ', 'ova-brw'); 
    }
    if ( 'ovabrw_total_days' === $meta->key ) { $key = esc_html__(' Total Time ', 'ova-brw'); }

    if( is_admin() ){
        if ( 'ovabrw_pickup_date_real' === $meta->key ) { 
            $key = esc_html__(' Pick-up date real', 'ova-brw');
        }
        if ( 'ovabrw_pickoff_date_real' === $meta->key ) { 
            $key = esc_html__(' Drop-off date real ', 'ova-brw'); 
        }
        if ( 'define_day' === $meta->key ) {
            $key = esc_html__(' Define day ', 'ova-brw'); 
        }    

        if ( 'id_vehicle' === $meta->key ) { 
            $key = esc_html__(' ID Vehicle ', 'ova-brw'); 
        }
    }
    
    if( apply_filters( 'brw_show_vehicle_order_frontend', false ) ){
        if ( 'id_vehicle' === $meta->key ) { 
            $key = esc_html__(' ID Vehicle ', 'ova-brw'); 
        }   
    }

    if ( 'ovabrw_price_detail' === $meta->key ) { $key = esc_html__(' Price Detail ', 'ova-brw'); }
    
    if ( 'ovabrw_amount_insurance_product' === $meta->key ) { $key =esc_html__( 'Amount Of Insurance', 'ova-brw' ); }
    if ( 'ovabrw_deposit_amount_product' === $meta->key ) { $key =esc_html__( 'Deposit Amount', 'ova-brw' ) . $tax_text; }
    if ( 'ovabrw_remaining_amount_product' === $meta->key ) { $key =esc_html__( 'Remaining Amount', 'ova-brw' ) . $tax_text_remaining; }
    if ( 'ovabrw_deposit_full_amount' === $meta->key ) { $key =esc_html__( 'Full Amount', 'ova-brw' ) . $tax_text; }

    if ( 'rental_type' === $meta->key ) { $key = esc_html__(' Rental Type ', 'ova-brw'); }
    if ( 'period_label' === $meta->key ) { $key = esc_html__(' Package ', 'ova-brw'); }
    if ( 'package_type' === $meta->key ) { $key = esc_html__(' Package Type ', 'ova-brw'); }
    
    
    $list_fields = get_option( 'ovabrw_booking_form', array() );

    if( is_array( $list_fields ) && ! empty( $list_fields ) ) {
        foreach( $list_fields as $key_field => $field ) {

            if( $key_field === $meta->key ) {
                $key = $field['label'];
            }
        }
    }
    
    return $key;
}

/**
 * Changing a meta value
 * @param  string        $value  The meta value
 * @param  WC_Meta_Data  $meta   The meta object
 * @param  WC_Order_Item $item   The order item object
 * @return string        The title
 */
/* Change in mail */
add_filter( 'woocommerce_order_item_display_meta_value', 'change_order_item_meta_value', 20, 3 );
function change_order_item_meta_value( $value, $meta, $item ) {

    // By using $meta-key we are sure we have the correct one.
    if ( 'ovabrw_pickup_loc' === $meta->key ) { $key = esc_html__(' Pick-up Location ', 'ova-brw'); }
    if ( 'ovabrw_pickoff_loc' === $meta->key ) { $key = esc_html__(' Drop-off Location ', 'ova-brw'); }
    if ( 'ovabrw_pickup_date' === $meta->key ) { $key = esc_html__(' Pick-up date ', 'ova-brw'); }
    if ( 'ovabrw_pickoff_date' === $meta->key ) { $key = esc_html__(' Drop-off date  ', 'ova-brw'); }
    if ( 'ovabrw_total_days' === $meta->key ) { $key = esc_html__(' Total Time ', 'ova-brw'); }
    if ( 'ovabrw_price_detail' === $meta->key ) { $key = esc_html__(' Price Detail ', 'ova-brw'); }

    if ( 'id_vehicle' === $meta->key ) { $key = esc_html__(' ID Vehicle ', 'ova-brw'); }

    if ( 'ovabrw_amount_insurance_product' === $meta->key ) { 
        $key = esc_html__( 'Amount Of Insurance', 'ova-brw' );
        $value = wc_price($meta->value);
    }
    if ( 'ovabrw_deposit_amount_product' === $meta->key ) { 
        $key =esc_html__( 'Deposit Amount', 'ova-brw' );
        $value = wc_price($meta->value);
    }
    if ( 'ovabrw_remaining_amount_product' === $meta->key ) { 
        $key = esc_html__( 'Remaining Amount', 'ova-brw' );
        $value = wc_price($meta->value);
    }
    if ( 'ovabrw_deposit_full_amount' === $meta->key ) { 
        $key = esc_html__( 'Full Amount', 'ova-brw' );
        $value = wc_price($meta->value);
    }
    
    if ( 'rental_type' === $meta->key ) { $key = esc_html__(' Rental Type ', 'ova-brw'); }
    if ( 'period_label' === $meta->key ) { $key = esc_html__(' Package ', 'ova-brw'); }
    if ( 'package_type' === $meta->key ) { $key = esc_html__(' Package Type ', 'ova-brw'); }
    if ( 'define_day' === $meta->key ) { $key = esc_html__(' Define day ', 'ova-brw'); }

    return $value;
}


// Add javascript to head
add_action('admin_head', 'ovabrw_hook_javascript');
add_action('wp_head', 'ovabrw_hook_javascript');
function ovabrw_hook_javascript() {

    $lang_general_calendar = ovabrw_get_setting( get_option( 'ova_brw_calendar_language_general', 'en' ) );

    $lang = apply_filters( 'wpml_current_language', NULL );

    if ( $lang ) {
        $lang_general_calendar = $lang;
    }

    $time_to_book_general_calendar = ovabrw_group_time_pickup_date_global_setting();
    $time_to_book_general_calendar_endate = ovabrw_group_time_dropoff_date_global_setting();

    $date_format = ovabrw_get_date_format();
    $time_format = ovabrw_get_time_format();

    $data_step = ovabrw_get_setting( get_option( 'ova_brw_booking_form_step_time', '30' ) );
    $disable_week_day = ovabrw_get_setting( get_option( 'ova_brw_calendar_disable_week_day', '' ) );

    if ( $time_format == 12 ) {
        $format_time = "h:mm A";
    } else {
        $format_time = "H:mm";
    }


    $hour_default_start = ovabrw_get_default_time( get_the_id(), 'start' );
    $hour_default_end = ovabrw_get_default_time(  get_the_id(), 'end' );


    // Defined label for custom checkout field
    $label_option_value =  esc_html__( 'Option Value', 'ova-brw' );
    $label_option_text =  esc_html__( 'Option Text', 'ova-brw' );
    $label_add_new_opt = esc_html__( 'Add new option', 'ova-brw' );
    $label_remove_opt = esc_html__( 'Remove option', 'ova-brw' );
    $label_are_you_sure = esc_html__( 'Are you sure ?', 'ova-brw' );

    // Defined label for Discount
    $label_per_day = esc_html__( '/Day', 'ova-brw' );
    $label_regular_per_day = esc_html__( 'Regular Price / Day', 'ova-brw' );

    $label_price = esc_html__( 'Price', 'ova-brw' );

    $notifi_disable_day = esc_html__( 'You cannot book on this day!', 'ova-brw' );


    ?>
        <script type="text/javascript">
            var brw_data_step = '<?php echo esc_html( $data_step ); ?>';
            var brw_date_format = '<?php echo esc_html( $date_format ); ?>';
            var brw_time_format = '<?php echo esc_html( $time_format ); ?>';
            var brw_format_time = '<?php echo esc_html( $format_time ); ?>';
            var brw_lang_general_calendar = '<?php echo esc_html( $lang_general_calendar ); ?>';
            var brw_time_to_book_general_calendar = '<?php echo esc_html( $time_to_book_general_calendar ); ?>';
            var brw_time_to_book_general_calendar_endate = '<?php echo esc_html( $time_to_book_general_calendar_endate ); ?>';
            var brw_disable_week_day = '<?php echo esc_html( $disable_week_day ); ?>';
            var brw_hour_default_start = '<?php echo esc_html( $hour_default_start ); ?>';
            var brw_hour_default_end = '<?php echo esc_html( $hour_default_end ); ?>';

            var label_option_value = '<?php echo esc_html( $label_option_value ); ?>';
            var label_option_text = '<?php echo esc_html( $label_option_text ); ?>';
            var label_add_new_opt = '<?php echo esc_html( $label_add_new_opt ); ?>';
            var label_remove_opt = '<?php echo esc_html( $label_remove_opt ); ?>';
            var label_are_you_sure = '<?php echo esc_html( $label_are_you_sure ); ?>';

            var label_per_day = '<?php echo esc_html( $label_per_day ); ?>';
            var label_regular_per_day = '<?php echo esc_html( $label_regular_per_day ); ?>';

            var label_price = '<?php echo esc_html( $label_price ); ?>';

            // init notificate disable day calendar
            var notifi_disable_day = '<?php echo esc_html( $notifi_disable_day ); ?>';
            
        </script>
    <?php
}



add_filter( 'woocommerce_get_price_html', 'ovabrw_woocommerce_hide_product_price' );
function ovabrw_woocommerce_hide_product_price( $price ) {
    global $product;
    if ( $product && $product->get_type() === 'ovabrw_car_rental' ) {
        return '';
    }
    return $price;
}

/**
 * Hook: woocommerce_product_tabs
 * @hooked: ovabrw_woo_new_product_tab - 10
 */
add_filter( 'woocommerce_product_tabs', 'ovabrw_woo_new_product_tab', 10 );


/**
 * Hook: woocommerce_after_shop_loop_item_title.
 *
 * @hooked ovabrw_product_price - 9
 */
add_action( 'woocommerce_after_shop_loop_item_title', 'ovabrw_product_price', 9 );
function ovabrw_product_price () {
    return ovabrw_get_template( 'loop/price.php' );
    
}


/**
 * Hook: woocommerce_after_shop_loop_item.
 *
 * @hooked ovabrw_loop_featured - 9
 */
add_action( 'woocommerce_after_shop_loop_item', 'ovabrw_loop_featured', 9 );
function ovabrw_loop_featured () {

    if( ovabrw_get_setting( get_option( 'ova_brw_archive_product_show_features', 'yes' ) ) == 'yes' ){
        return ovabrw_get_template( 'loop/featured.php' );
    }
    
}



/**
 * Hook: woocommerce_after_shop_loop_item.
 *
 * @hooked ovabrw_loop_taxonomy - 9
 */
add_action( 'woocommerce_after_shop_loop_item', 'ovabrw_loop_taxonomy', 9 );
function ovabrw_loop_taxonomy () {

    // if( ovabrw_get_setting( get_option( 'ova_brw_archive_product_show_features', 'yes' ) ) == 'yes' ){
        return ovabrw_get_template( 'loop/taxonomy.php' );
    // }
    
}

/**
 * Hook: woocommerce_after_shop_loop_item.
 *
 * @hooked ovabrw_loop_attributes - 8
 */
add_action( 'woocommerce_after_shop_loop_item', 'ovabrw_loop_attributes', 8 );
function ovabrw_loop_attributes() {
    
    if( ovabrw_get_setting( get_option( 'ova_brw_archive_product_show_attribute', 'yes' ) ) == 'yes' ){

        return ovabrw_get_template( 'loop/attributes.php' );

    }
    
}


/**
 * Hook: woocommerce_single_product_summary
 * @hooked ovabrw_woocommerce_template_single_price - 10
 */
add_action( 'woocommerce_single_product_summary', 'ovabrw_woocommerce_template_single_price', 10 );
function ovabrw_woocommerce_template_single_price() {
    
    return ovabrw_get_template( 'single/price.php' );
    
}


/**
 * Hook: woocommerce_single_product_summary
 * @hooked ovabrw_woocommerce_template_single_cus_tax - 65
 */

add_action( 'woocommerce_single_product_summary', 'ovabrw_woocommerce_template_single_cus_tax', 65 );
function ovabrw_woocommerce_template_single_cus_tax() {

    if( ovabrw_get_setting( get_option( 'ova_brw_template_show_cus_tax', 'yes' ) ) === 'yes' ){

        return ovabrw_get_template( 'single/custom_taxonomy.php' );

    }

    
}


/**
 * Hook: woocommerce_single_product_summary
 * @hooked ovabrw_woocommerce_template_single_featured - 70
 */

add_action( 'woocommerce_single_product_summary', 'ovabrw_woocommerce_template_single_featured', 70 );
function ovabrw_woocommerce_template_single_featured() {

    if( ovabrw_get_setting( get_option( 'ova_brw_template_show_feature', 'yes' ) ) === 'yes' ){

        return ovabrw_get_template( 'single/features.php' );

    }

    
}

/**
 * Hook: woocommerce_single_product_summary
 * @hooked ovabrw_woocommerce_template_single_table_price - 71
 */
add_action( 'woocommerce_single_product_summary', 'ovabrw_woocommerce_template_single_table_price', 71 );
function ovabrw_woocommerce_template_single_table_price() {
    
    if ( ovabrw_get_setting( get_option( 'ova_brw_template_show_table_price', 'yes' ) ) === 'yes' ) {

        return ovabrw_get_template( 'single/table_price.php' );

    }

    
}

/**
 * Hook: woocommerce_single_product_summary
 * @hooked ovabrw_woocommerce_template_single_untime - 72
 */
add_action( 'woocommerce_single_product_summary', 'ovabrw_woocommerce_template_single_untime', 72 );
function ovabrw_woocommerce_template_single_untime() {
    
    if ( ovabrw_get_setting( get_option( 'ova_brw_template_show_maintenance', 'yes' ) ) === 'yes' ) {

        return ovabrw_get_template( 'single/unavailable_time.php' );

    }

    
}

/**
 * Hook: woocommerce_single_product_summary
 * @hooked ovabrw_woocommerce_template_single_calendar - 73
 */
add_action( 'woocommerce_single_product_summary', 'ovabrw_woocommerce_template_single_calendar', 73 );
function ovabrw_woocommerce_template_single_calendar() {

    if( ovabrw_get_setting( get_option( 'ova_brw_template_show_calendar', 'yes' ) ) === 'yes' ){ 

        return ovabrw_get_template( 'single/calendar.php' );

    }

}

/**
 * Hook: woocommerce_single_product_summary
 * @hooked ovabrw_woocommerce_template_single_booking_form - 74
 */
add_action( 'woocommerce_single_product_summary', 'ovabrw_woocommerce_template_single_booking_form', 74 );
function ovabrw_woocommerce_template_single_booking_form() {

    
    if( ovabrw_get_setting( get_option( 'ova_brw_template_show_booking_form', 'yes' ) ) === 'yes' ){ 
        return ovabrw_get_template( 'single/booking-form.php' );
    }

}





/**
 * Hook: ovabrw_booking_form
 * @hooked: ovabrw_booking_form_fields - 5
 */
add_action( 'ovabrw_booking_form', 'ovabrw_booking_form_fields', 5, 1 );
function ovabrw_booking_form_fields( $product_id ) {

    return ovabrw_get_template( 'single/booking-form/fields.php', array(  'product_id' => $product_id ) ); 
    
}


/**
 * Hook: ovabrw_booking_form
 * @hooked: ovabrw_booking_form_extra_fields - 10
 */
add_action( 'ovabrw_booking_form', 'ovabrw_booking_form_extra_fields', 10, 1 );
function ovabrw_booking_form_extra_fields( $product_id ) {

    return ovabrw_get_template( 'single/booking-form/extra_fields.php', array(  'product_id' => $product_id ) ); 
    
}


/**
 * Hook: ovabrw_booking_form
 * @hooked: ovabrw_booking_form_resource - 15
 */
add_action( 'ovabrw_booking_form', 'ovabrw_booking_form_resource', 15, 1 );
function ovabrw_booking_form_resource( $product_id ) {

    if( ovabrw_get_setting( get_option( 'ova_brw_booking_form_show_extra_resource', 'yes' ) ) == 'yes' ){
        return ovabrw_get_template( 'single/booking-form/resource.php', array(  'product_id' => $product_id ) );
    }
    
}

/**
 * Hook: ovabrw_booking_form
 * @hooked: ovabrw_booking_form_services - 20
 */
add_action( 'ovabrw_booking_form', 'ovabrw_booking_form_services', 20, 1 );
function ovabrw_booking_form_services( $product_id ) {

    if( ovabrw_get_setting( get_option( 'ova_brw_booking_form_show_extra_service', 'yes' ) ) == 'yes' ){
        return ovabrw_get_template( 'single/booking-form/services.php', array(  'product_id' => $product_id ) );
    }
    
}



/**
 * Hook: ovabrw_booking_form
 * @hooked: ovabrw_booking_form_deposit - 25
 */
add_action( 'ovabrw_booking_form', 'ovabrw_booking_form_deposit', 25, 1 );
function ovabrw_booking_form_deposit($product_id) {

    $deposit_enable = get_post_meta ( $product_id, 'ovabrw_enable_deposit', true );
    if ( $deposit_enable === 'yes' ) {
       return ovabrw_get_template( 'single/booking-form/deposit.php', array(  'product_id' => $product_id ) );
    }
    return;
    
}

/**
 * Hook: ovabrw_booking_form
 * @hooked: ovabrw_booking_form_ajax_total - 30
 */
add_action( 'ovabrw_booking_form', 'ovabrw_booking_form_ajax_total', 30, 1 );
function ovabrw_booking_form_ajax_total($product_id) {

   return ovabrw_get_template( 'single/booking-form/ajax_total.php', array(  'product_id' => $product_id ) );
    
}



/**
 * Hook: ovabrw_table_price_weekdays
 * @hooked: ovabrw_table_price_weekdays - 10
 */
add_action( 'ovabrw_table_price_weekdays', 'ovabrw_table_price_weekdays', 10, 1 );
function ovabrw_table_price_weekdays( $product_id ) {

    $price_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

    $monday = get_post_meta( $product_id, 'ovabrw_daily_monday', true );
    $tuesday = get_post_meta( $product_id, 'ovabrw_daily_tuesday', true );
    $wednesday = get_post_meta( $product_id, 'ovabrw_daily_wednesday', true );

    $thursday = get_post_meta( $product_id, 'ovabrw_daily_thursday', true );

    $friday = get_post_meta( $product_id, 'ovabrw_daily_friday', true );

    $saturday = get_post_meta( $product_id, 'ovabrw_daily_saturday', true );
    $sunday = get_post_meta( $product_id, 'ovabrw_daily_sunday', true );

    if( ( $price_type == 'day' || $price_type == 'mixed' ) && $monday && $tuesday && $wednesday && $thursday && $friday && $saturday && $sunday ){ 

        return ovabrw_get_template( 'single/table-price/weekdays.php', array(  'product_id' => $product_id ) );
        
    }
    
}

/**
 * Hook: ovabrw_table_price_global_discount_day
 * @hooked: ovabrw_table_price_global_discount_day - 10
 */
add_action( 'ovabrw_table_price_global_discount_day', 'ovabrw_table_price_global_discount_day', 10, 1 );
function ovabrw_table_price_global_discount_day( $product_id ) {

    return ovabrw_get_template( 'single/table-price/global_discount_day.php',  array(  'product_id' => $product_id ) );
    
}


/**
 * Hook: ovabrw_table_price_global_discount_hour
 * @hooked: ovabrw_table_price_global_discount_hour - 10
 */
add_action( 'ovabrw_table_price_global_discount_hour', 'ovabrw_table_price_global_discount_hour', 10, 1 );
function ovabrw_table_price_global_discount_hour( $product_id ) {

    return ovabrw_get_template( 'single/table-price/global_discount_hour.php', array(  'product_id' => $product_id ) );
    
}

/**
 * Hook: ovabrw_table_price_seasons_day
 * @hooked: ovabrw_table_price_seasons_day - 10
 */
add_action( 'ovabrw_table_price_seasons_day', 'ovabrw_table_price_seasons_day', 10, 1 );
function ovabrw_table_price_seasons_day( $product_id ) {

    return ovabrw_get_template( 'single/table-price/seasons_day.php', array(  'product_id' => $product_id ) );
    
}


/**
 * Hook: ovabrw_table_price_seasons_hour
 * @hooked: ovabrw_table_price_seasons_hour - 10
 */
add_action( 'ovabrw_table_price_seasons_hour', 'ovabrw_table_price_seasons_hour', 10, 1 );
function ovabrw_table_price_seasons_hour( $product_id ) {

    return ovabrw_get_template( 'single/table-price/seasons_hour.php', array(  'product_id' => $product_id ) );
    
}

/**
 * Hook: ovabrw_table_price_period_time
 * @hooked: ovabrw_table_price_period_time - 10
 */
add_action( 'ovabrw_table_price_period_time', 'ovabrw_table_price_period_time', 10, 1 );
function ovabrw_table_price_period_time( $product_id ) {

    return ovabrw_get_template( 'single/table-price/period_time.php', array(  'product_id' => $product_id ) );
    
}

/**
 * Hook: ovabrw_request_booking_form
 * @hooked: ovabrw_request_booking_form - 10
 */
add_action( 'ovabrw_request_booking_form', 'ovabrw_request_booking_form', 10, 0 );
function ovabrw_request_booking_form() {

    return ovabrw_get_template( 'single/request_booking.php' );
    
}


add_filter( 'wc_order_statuses', 'wc_closed_order_statuses' );
// Register in wc_order_statuses.
function wc_closed_order_statuses( $order_statuses ) {
    $order_statuses['wc-closed'] = _x( 'Closed', 'Order status', 'ova-brw' );

    return $order_statuses;
}


// Replace product link in Search Result Page
add_filter( 'woocommerce_loop_product_link', 'ovarbrw_woocommerce_loop_product_link', 10, 1 );
function ovarbrw_woocommerce_loop_product_link( $product_link ){
    

    if( isset( $_GET['ovabrw_search'] ) ){

        if( isset( $_GET['ovabrw_pickup_date'] ) && $_GET['ovabrw_pickup_date'] ){
            $product_link = add_query_arg( 'pickup_date', $_GET['ovabrw_pickup_date'], $product_link );
        }

        if( isset( $_GET['ovabrw_pickoff_date'] ) && $_GET['ovabrw_pickoff_date'] ){
            $product_link = add_query_arg( 'dropoff_date', $_GET['ovabrw_pickoff_date'], $product_link );
        }

        if( isset( $_GET['ovabrw_pickup_loc'] ) && $_GET['ovabrw_pickup_loc'] ){
            $product_link = add_query_arg( 'pickup_loc', $_GET['ovabrw_pickup_loc'], $product_link );
        }

        if( isset( $_GET['ovabrw_pickoff_loc'] ) && $_GET['ovabrw_pickoff_loc'] ){
            $product_link = add_query_arg( 'pickoff_loc', $_GET['ovabrw_pickoff_loc'], $product_link );
        }

    }
    return $product_link;
}

add_filter( 'woocommerce_loop_add_to_cart_link', 'ovarbrw_woocommerce_loop_add_to_cart_link', 10, 3 );
function ovarbrw_woocommerce_loop_add_to_cart_link( $link, $product, $args ){

    $product_link = $product->add_to_cart_url();

    if( isset( $_GET['ovabrw_search'] ) ){

        if( isset( $_GET['ovabrw_pickup_date'] ) && $_GET['ovabrw_pickup_date'] ){
            $product_link = add_query_arg( 'pickup_date', $_GET['ovabrw_pickup_date'], $product_link );
        }

        if( isset( $_GET['ovabrw_pickoff_date'] ) && $_GET['ovabrw_pickoff_date'] ){
            $product_link = add_query_arg( 'dropoff_date', $_GET['ovabrw_pickoff_date'], $product_link );
        }

        if( isset( $_GET['ovabrw_pickup_loc'] ) && $_GET['ovabrw_pickup_loc'] ){
            $product_link = add_query_arg( 'pickup_loc', $_GET['ovabrw_pickup_loc'], $product_link );
        }

        if( isset( $_GET['ovabrw_pickoff_loc'] ) && $_GET['ovabrw_pickoff_loc'] ){
            $product_link = add_query_arg( 'pickoff_loc', $_GET['ovabrw_pickoff_loc'], $product_link );
        }

    }
    

    return sprintf(
        '<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
        esc_url( $product_link ),
        esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
        esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
        isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
        esc_html( $product->add_to_cart_text() )
    );

}

// Allow users cancel Order
add_filter( 'woocommerce_valid_order_statuses_for_cancel', 'ovabrw_woo_valid_order_statuses_for_cancel', 10, 2 );
function ovabrw_woo_valid_order_statuses_for_cancel( $array_status, $order ){

    $order_status_can_cancel = $time_can_cancel = $other_condition = $total_order_valid = true;
     
     if( in_array( $order->get_status(), array( 'pending', 'failed' ) ) ){
        return array( 'pending', 'failed' );
     }

    // Check order status can order
    if( !in_array( $order->get_status(), apply_filters( 'ovabrw_order_status_can_cancel', array( 'completed', 'processing', 'on-hold', 'pending', 'failed' ) ) )  ){
        $order_status_can_cancel = false;
    }
    
    // Validate before x hours can cancel
    // Get Meta Data type line_item of Order
    $order_line_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
    foreach ( $order_line_items as $item_id => $item ) {

        $product_id = $item->get_product_id();
        $product = wc_get_product( $product_id );

        $cancel_valid_minutes = ovabrw_get_setting( get_option( 'ova_brw_cancel_before_x_hours', 0 ) );
        $cancel_valid_total = ovabrw_get_setting( get_option( 'ova_brw_cancel_condition_total_order', 1 ) );

        // Check if product type is rental
        if( $product->get_type() == 'ovabrw_car_rental' ){
             // Get value of pickup date, pickoff date
            foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
                

                if( $meta->key == 'ovabrw_pickup_date' ){
                    
                    $ovabrw_pickup_date = strtotime( $meta->value );

                    if( ! ( $ovabrw_pickup_date > current_time( 'timestamp' ) && $ovabrw_pickup_date - current_time( 'timestamp' ) > $cancel_valid_minutes*60*60  ) ){

                       $time_can_cancel = false;
                       break;
                    }
                }

            }
        }

    }

    // Cancel by total order
    if( empty( $cancel_valid_total ) ){
        $total_order_valid = true;
    }else if( $order->get_total() > floatval( $cancel_valid_total ) ){
        $total_order_valid = false;
    }
    

    // Other condition
    $other_condition = apply_filters( 'ovabrw_other_condition_to_cancel_order', true, $order );

    if( $order_status_can_cancel && $time_can_cancel && $total_order_valid && $other_condition ){
        return array( 'completed', 'processing', 'on-hold', 'pending', 'failed' );
    }else{
        return array();
    }

    
}


// Display Item Meta in Order Detail
add_filter( 'woocommerce_display_item_meta', 'ovabrw_woocommerce_display_item_meta', 10, 3 );
function ovabrw_woocommerce_display_item_meta( $html, $item, $args ){

    $strings = array();
    $html    = '';
    $args    = wp_parse_args(
        $args,
        array(
            'before'       => '<ul class="wc-item-meta"><li>',
            'after'        => '</li></ul>',
            'separator'    => '</li><li>',
            'echo'         => true,
            'autop'        => false,
            'label_before' => '<strong class="wc-item-meta-label">',
            'label_after'  => ':</strong> ',
        )
    );

    foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {

        if( in_array( $meta->key , apply_filters( 'ovabrw_order_detail_hide_fields', array( 'ovabrw_pickup_date_real' ,'ovabrw_pickoff_date_real', 'id_vehicle', 'define_day' ) ) )  ){
            $strings[] = '';
        }else{
            $value     = $args['autop'] ? wp_kses_post( $meta->display_value ) : wp_kses_post( make_clickable( trim( $meta->display_value ) ) );
            $strings[] = $args['label_before'] . wp_kses_post( $meta->display_key ) . $args['label_after'] . $value;    
        }
        
      
    }

    if ( $strings ) {

        $html = $args['before'] . implode( $args['separator'], $strings ). $args['after'];
    }

    $html = str_replace('ovabrw_pickup_loc', esc_html__(' Pick-up Location ', 'ova-brw') , $html );
    $html = str_replace('ovabrw_pickoff_loc', esc_html__(' Drop-off Location ', 'ova-brw') , $html );
    $html = str_replace('ovabrw_pickup_date', esc_html__(' Pick-up date ', 'ova-brw') , $html );
    $html = str_replace('ovabrw_pickoff_date', esc_html__(' Drop-off date ', 'ova-brw') , $html );
    $html = str_replace('ovabrw_total_days', esc_html__(' Total Time ', 'ova-brw') , $html );
    $html = str_replace('ovabrw_price_detail', esc_html__(' Price Detail ', 'ova-brw') , $html );
    $html = str_replace('rental_type', esc_html__(' Rental Type', 'ova-brw') , $html );
    $html = str_replace('period_label', esc_html__(' Package', 'ova-brw') , $html );
    $html = str_replace('package_type', esc_html__(' Package Type', 'ova-brw') , $html );
    $html = str_replace('define_day', esc_html__(' Define day', 'ova-brw') , $html );

    return $html;
}

