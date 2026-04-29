<?php if ( !defined( 'ABSPATH' ) ) exit();


if( !class_exists( 'Ovabrw_Model' ) ){

    class Ovabrw_Model{

        public function __construct(){

            // Create new order manually
            add_action( 'admin_init', array( $this, 'ovabrw_create_new_order_manully' ) );
        }

        public function ovabrw_get_address() {

            $first_name         = isset( $_POST['ovabrw_first_name'] )  ? sanitize_text_field( $_POST['ovabrw_first_name'] )    : '';
            $last_name          = isset( $_POST['ovabrw_last_name'] )   ? sanitize_text_field( $_POST['ovabrw_last_name'] )     : '';
            $company            = isset( $_POST['ovabrw_company'] )     ? sanitize_text_field( $_POST['ovabrw_company'] )       : '';
            $email              = isset( $_POST['ovabrw_email'] )       ? sanitize_text_field( $_POST['ovabrw_email'] )         : '';
            $phone              = isset( $_POST['ovabrw_phone'] )       ? sanitize_text_field( $_POST['ovabrw_phone'] )         : '';
            $address_1          = isset( $_POST['ovabrw_address_1'] )   ? sanitize_text_field( $_POST['ovabrw_address_1'] )     : '';
            $address_2          = isset( $_POST['ovabrw_address_2'] )   ? sanitize_text_field( $_POST['ovabrw_address_2'] )     : '';
            $city               = isset( $_POST['ovabrw_city'] )        ? sanitize_text_field( $_POST['ovabrw_city'] )          : '';
            $country_setting    = isset( $_POST['ovabrw_country'] )     ? sanitize_text_field( $_POST['ovabrw_country'] )       : 'US';

            if ( strstr( $country_setting, ':' ) ) {
                $country_setting = explode( ':', $country_setting );
                $country         = current( $country_setting );
                $state           = end( $country_setting );
            } else {
                $country = $country_setting;
                $state   = '*';
            }

            $data_address = array(
                'first_name' => $first_name,
                'last_name'  => $last_name,
                'company'    => $company,
                'email'      => $email,
                'phone'      => $phone,
                'address_1'  => $address_1,
                'address_2'  => $address_2,
                'city'       => $city,
                'country'    => $country,
            );

            return apply_filters( 'ovabrw_ft_get_address', $data_address );
        }

        public function ovabrw_get_tax_default() {

            $tax_rates = array();

            if ( wc_tax_enabled() ) {

                $country_setting = isset( $_POST['ovabrw_country'] ) ? sanitize_text_field( $_POST['ovabrw_country'] ) : 'US';

                if ( strstr( $country_setting, ':' ) ) {
                    $country_setting = explode( ':', $country_setting );
                    $country         = current( $country_setting );
                    $state           = end( $country_setting );
                } else {
                    $country = $country_setting;
                    $state   = '*';
                }

                $tax_rates = WC_Tax::find_rates(
                    array(
                        'country'   => $country,
                        'state'     => $state,
                        'postcode'  => '',
                        'city'      => '',
                        'tax_class' => '',
                    )
                );
            }

            return $tax_rates;
        }

        protected function ovabrw_get_total_taxed_enable( $products ,$price ) {

            if ( empty( $products ) || ! $price ) {
                return 0;
            }

            $tax_rates = $this->ovabrw_get_tax_default();

            if ( empty( $tax_rates ) ) {
                
                $tax_rates = WC_Tax::get_rates( $products->get_tax_class() );

                if ( empty( $tax_rates ) ) {
                    return apply_filters( 'ovabrw_ft_get_total_taxed_enable', $price );
                }
            }

            if ( wc_tax_enabled() ) {

                if ( ! wc_prices_include_tax() ) {

                    $excl_tax = WC_Tax::calc_exclusive_tax( $price, $tax_rates );
                    $price   += round( array_sum( $excl_tax ), wc_get_price_decimals() );

                }
            }

            return apply_filters( 'ovabrw_ft_get_total_taxed_enable', $price );
        }

        protected function ovabrw_get_item_total_taxed_enable( $products ,$price ) {

            if ( empty( $products ) || ! $price ) {
                return 0;
            }

            $tax_rates = $this->ovabrw_get_tax_default();

            if ( empty( $tax_rates ) ) {
                
                $tax_rates = WC_Tax::get_rates( $products->get_tax_class() );

                if ( empty( $tax_rates ) ) {
                    return apply_filters( 'ovabrw_ft_get_item_total_taxed_enable', $price );
                }
            }

            if ( wc_tax_enabled() ) {

                if ( wc_prices_include_tax() ) {

                    if ( get_option( 'woocommerce_tax_display_cart' ) != 'incl' ) {
                        $incl_tax = WC_Tax::calc_inclusive_tax( $price, $tax_rates );
                        $price   -= round( array_sum( $incl_tax ), wc_get_price_decimals() );
                    }

                } else {

                    if ( get_option( 'woocommerce_tax_display_cart' ) === 'incl' ) {
                        $excl_tax = WC_Tax::calc_exclusive_tax( $price, $tax_rates );
                        $price   += round( array_sum( $excl_tax ), wc_get_price_decimals() );
                    }

                }

            }

            return apply_filters( 'ovabrw_ft_get_item_total_taxed_enable', $price );
        }

        protected function ovabrw_get_product_price_taxed_enable( $products ,$price ) {

            if ( empty( $products ) || ! $price ) {
                return 0;
            }

            $tax_rates = $this->ovabrw_get_tax_default();

            if ( empty( $tax_rates ) ) {

                $tax_rates = WC_Tax::get_rates( $products->get_tax_class() );

                if ( empty( $tax_rates ) ) {
                    return apply_filters( 'ovabrw_ft_get_product_price_taxed_enable', $price );
                }
            }

            if ( wc_tax_enabled() ) {

                if ( wc_prices_include_tax() ) {

                    $incl_tax = WC_Tax::calc_inclusive_tax( $price, $tax_rates );
                    $price   -= round( array_sum( $incl_tax ), wc_get_price_decimals() );

                }
            }

            return apply_filters( 'ovabrw_ft_get_product_price_taxed_enable', $price );
        }

        protected function ovabrw_get_tax_amount_taxed_enable( $products ,$price ) {

            $tax_amount = 0;

            if ( ! $products || ! $price ) {
                return $tax_amount;
            }

            $tax_rates = $this->ovabrw_get_tax_default();

            if ( empty( $tax_rates ) ) {
                
                $tax_rates = WC_Tax::get_rates( $products->get_tax_class() );

                if ( empty( $tax_rates ) ) {
                    return apply_filters( 'ovabrw_ft_get_tax_amount_taxed_enable', $tax_amount );
                }
            }

            if ( wc_tax_enabled() ) {

                if ( wc_prices_include_tax() ) {

                    $incl_tax   = WC_Tax::calc_inclusive_tax( $price, $tax_rates );
                    $tax_amount = round( array_sum( $incl_tax ), wc_get_price_decimals() );

                } else {

                    $excl_tax   = WC_Tax::calc_exclusive_tax( $price, $tax_rates );
                    $tax_amount = round( array_sum( $excl_tax ), wc_get_price_decimals() );
                }

            }

            return apply_filters( 'ovabrw_ft_get_tax_amount_taxed_enable', $tax_amount );
        }

        public function ovabrw_add_order_item( $order_id ) {

            // order data
            $data_order = array();

            if ( ! $order_id ) {
                return $data_order;
            }

            $order = wc_get_order( $order_id ); // Get order

            // Get order items
            $has_deposit = $item_has_deposit = false;
            $order_items = $order->get_items(); 
            $order_total = $total_remaining = $total_deposite = $total_insurance = 0;
            $item_total_remaining = $item_total_deposite = $item_total_insurance = $deposit_full_amount = 0;

            // Tax
            $tax_display_cart   = false;
            $remaining_taxes    = 0;
            if ( wc_tax_enabled() && get_option( 'woocommerce_tax_display_cart' ) === 'incl' ) {
                $tax_display_cart = true;
            }

            // Init $i
            $i = 0;

            // Loop order items
            foreach ( $order_items as $item_id => $product ) {

                $item_total = 0;
                $line_tax   = $data_item = array();

                // Tax
                $tax_rate_id = '';
                $tax_amount  = 0;

                $item = WC_Order_Factory::get_order_item( absint( $item_id ) );

                if ( ! $item ) {
                    continue;
                }

                $product_id = $product['product_id'];
                $products   = wc_get_product( $product_id );

                $ovabrw_pickup_loc       = isset( $_POST['ovabrw_pickup_loc'][$i] )       ? sanitize_text_field( $_POST['ovabrw_pickup_loc'][$i] )     : "";
                $ovabrw_pickoff_loc      = isset( $_POST['ovabrw_pickoff_loc'][$i] )      ? sanitize_text_field( $_POST['ovabrw_pickoff_loc'][$i] )    : "";

                $ovabrw_pickup_date      = isset( $_POST['ovabrw_pickup_date'][$i] )      ? sanitize_text_field( $_POST['ovabrw_pickup_date'][$i] )    : "";
                $ovabrw_pickoff_date     = isset( $_POST['ovabrw_pickoff_date'][$i] )     ? sanitize_text_field( $_POST['ovabrw_pickoff_date'][$i] )   : "";

                $ovabrw_number_vehicle   = isset( $_POST['ovabrw_number_vehicle'][$i] )   ? sanitize_text_field( $_POST['ovabrw_number_vehicle'][$i] ) : 1;
                $ovabrw_total_product    = isset( $_POST['ovabrw_total_product'][$i] )    ? floatval( $_POST['ovabrw_total_product'][$i] )             : 0;

                $ovabrw_id_vehicle       = isset( $_POST['ovabrw_id_vehicle'][$i] )       ? sanitize_text_field( $_POST['ovabrw_id_vehicle'][$i] )     : "";

                $ovabrw_resources = isset( $_POST['ovabrw_resource_checkboxs'][$product_id] )   ? $_POST['ovabrw_resource_checkboxs'][$product_id]  : "";
                $ovabrw_services  = isset( $_POST['ovabrw_service'][$product_id] )              ? $_POST['ovabrw_service'][$product_id]             : "";

                $ovabrw_rental_type      = isset( $_POST['ovabrw_rental_type'][$i] )      ? sanitize_text_field( $_POST['ovabrw_rental_type'][$i] )    : "";
                $ovabrw_define_1_day     = isset( $_POST['ovabrw_define_1_day'][$i] )     ? sanitize_text_field( $_POST['ovabrw_define_1_day'][$i] )   : "";
                $ovabrw_package          = isset( $_POST['ovabrw_package'][$i] )          ? sanitize_text_field( $_POST['ovabrw_package'][$i] )        : "";
                $ovabrw_total_time       = isset( $_POST['ovabrw_total_time'][$i] )       ? sanitize_text_field( $_POST['ovabrw_total_time'][$i] )     : "";
                $ovabrw_price_detail     = isset( $_POST['ovabrw_price_detail'][$i] )     ? sanitize_text_field( $_POST['ovabrw_price_detail'][$i] )   : 0;

                $item_total_insurance    = isset( $_POST['ovabrw_amount_insurance'][$i] ) ? floatval( $_POST['ovabrw_amount_insurance'][$i] )          : 0;
                $item_total_deposite     = isset( $_POST['ovabrw_amount_deposite'][$i] )  ? floatval( $_POST['ovabrw_amount_deposite'][$i] )           : 0;
                $item_total_remaining    = isset( $_POST['ovabrw_amount_remaining'][$i] ) ? floatval( $_POST['ovabrw_amount_remaining'][$i] )          : 0;

                $total_insurance        += $item_total_insurance;

                $total_deposite         += $this->ovabrw_get_total_taxed_enable( $products, $item_total_deposite );
                $total_remaining        += $this->ovabrw_get_item_total_taxed_enable( $products, $item_total_remaining );

                if ( $item_total_deposite && floatval( $item_total_deposite ) > 0 ) {
                    $has_deposit = $item_has_deposit = true;
                } else {
                    $item_has_deposit = false;
                }

                if ( $item_has_deposit ) {

                    $order_total += $total_deposite;
                    $item_total   = $item_total_deposite;

                    // Deposit full amount
                    $deposit_full_amount = $this->ovabrw_get_item_total_taxed_enable( $products, $ovabrw_total_product );

                } else {

                    $order_total += $this->ovabrw_get_total_taxed_enable( $products, $ovabrw_total_product );
                    $item_total   = $ovabrw_total_product;

                    $deposit_full_amount = 0;
                    $no_has_deposite     = $ovabrw_total_product;
                }
                
                if ( isset( $no_has_deposite ) && $has_deposit ) {
                    $total_deposite += $no_has_deposite;
                }

                // Get remaining taxes
                if ( wc_tax_enabled() ) {

                    $remaining_taxes += $this->ovabrw_get_tax_amount_taxed_enable( $products, $item_total_remaining );

                    // item data
                    $item_total_deposite  = $this->ovabrw_get_item_total_taxed_enable( $products, $item_total_deposite );
                    $item_total_remaining = $this->ovabrw_get_item_total_taxed_enable( $products, $item_total_remaining );

                    $tax_rates = $this->ovabrw_get_tax_default();

                    if ( empty( $tax_rates ) ) {
                        $tax_rates = WC_Tax::get_rates( $products->get_tax_class() );
                    }

                    if ( ! empty( $tax_rates ) ) {
                        $tax_item_id = key($tax_rates);

                        if ( $tax_item_id ) {

                            $tax_amount_item        = $this->ovabrw_get_tax_amount_taxed_enable( $products, $item_total );
                            $line_tax[$tax_item_id] = $tax_amount_item;
                            $tax_rate_id            = $tax_item_id;
                            $tax_amount            += $tax_amount_item;
                            $item_total             = $this->ovabrw_get_product_price_taxed_enable( $products, $item_total );
                        }
                    }
                    
                }

                $defined_one_day = defined_one_day( $product_id );

                $date_format = ovabrw_get_date_format();
                $time_format = ovabrw_get_time_format_php();
                $ovabrw_date_time_format = $date_format . ' ' . $time_format;

                //add real date
                $ovabrw_pickup_date_real    = $ovabrw_pickup_date;
                $ovabrw_dropoff_date_real   = $ovabrw_pickoff_date;
                
                if ( $ovabrw_rental_type === 'transportation' ) {

                    $ovabrw_pickup_date_timestamp   = strtotime( $ovabrw_pickup_date );
                    $ovabrw_pickup_date_real        = wp_date( $date_format, $ovabrw_pickup_date_timestamp ) . ' 00:00';
                    $ovabrw_dropoff_date_real       = wp_date( $date_format, $ovabrw_pickup_date_timestamp ) . ' 24:00';

                } elseif ( $ovabrw_rental_type === 'period_time' ) {

                    $rental_info_period     = get_rental_info_period( $product_id, strtotime( $ovabrw_pickup_date ), $ovabrw_rental_type, $ovabrw_package );
                    $ovabrw_pickup_date     = wp_date( $ovabrw_date_time_format, $rental_info_period['start_time'] );
                    $ovabrw_pickoff_date    = wp_date( $ovabrw_date_time_format, $rental_info_period['end_time'] );
                }
               
                if ( $defined_one_day == 'hotel' ) {

                    $ovabrw_pickup_date_timestamp   = strtotime( $ovabrw_pickup_date );
                    $ovabrw_pickoff_date_timestamp  = strtotime( $ovabrw_pickoff_date );

                    $ovabrw_pickup_date_real        = wp_date( $date_format, $ovabrw_pickup_date_timestamp ) . ' 00:00';
                    $ovabrw_dropoff_date_real       = wp_date(  $date_format, ( $ovabrw_pickoff_date_timestamp - ( 3600 * 24 ) ) ) . ' 24:00';

                } elseif ( $defined_one_day == 'day' ) {

                    $ovabrw_pickup_date_timestamp   = strtotime( $ovabrw_pickup_date );
                    $ovabrw_pickoff_date_timestamp  = strtotime( $ovabrw_pickoff_date );

                    $ovabrw_pickup_date_real        = wp_date( $date_format, $ovabrw_pickup_date_timestamp ) . ' 00:00';
                    $ovabrw_dropoff_date_real       = wp_date( $date_format, $ovabrw_pickoff_date_timestamp ) . ' 24:00';

                } elseif ( $defined_one_day == 'hour' ) {
                    //fixed later
                    $ovabrw_pickup_date_real    = $ovabrw_pickup_date;
                    $ovabrw_dropoff_date_real   = $ovabrw_pickoff_date;
                }

                if ( $ovabrw_rental_type === 'hour' || $ovabrw_rental_type === 'mixed' || $ovabrw_rental_type === 'period_time' ) {

                    $ovabrw_pickup_date_real    = $ovabrw_pickup_date;
                    $ovabrw_dropoff_date_real   = $ovabrw_pickoff_date;
                }

                $data_item[ 'ovabrw_pickup_loc' ]               = $ovabrw_pickup_loc;
                $data_item[ 'ovabrw_pickoff_loc' ]              = $ovabrw_pickoff_loc;
                $data_item[ 'ovabrw_pickup_date' ]              = $ovabrw_pickup_date;
                $data_item[ 'ovabrw_pickoff_date' ]             = $ovabrw_pickoff_date;
                $data_item[ 'ovabrw_number_vehicle' ]           = $ovabrw_number_vehicle;
                $data_item[ 'ovabrw_pickup_date_real' ]         = $ovabrw_pickup_date_real;
                $data_item[ 'ovabrw_pickoff_date_real' ]        = $ovabrw_dropoff_date_real;
                $data_item[ 'rental_type' ]                     = $ovabrw_rental_type;
                $data_item[ 'period_label' ]                    = $ovabrw_package;
                $data_item[ 'ovabrw_total_days' ]               = $ovabrw_total_time;
                $data_item[ 'ovabrw_price_detail' ]             = $ovabrw_price_detail;

                if ( $item_total_insurance ) {
                    $data_item[ 'ovabrw_amount_insurance_product' ] = $item_total_insurance;
                }

                if ( $item_has_deposit ) {
                    $data_item[ 'ovabrw_deposit_amount_product' ]   = $item_total_deposite;
                    $data_item[ 'ovabrw_remaining_amount_product' ] = $item_total_remaining;
                }

                if ( $deposit_full_amount ) {
                    $data_item['ovabrw_deposit_full_amount'] = $deposit_full_amount;
                }

                if ( $ovabrw_id_vehicle ) {
                    $data_item[ 'id_vehicle' ] = $ovabrw_id_vehicle;   
                }
                
                if ( $ovabrw_rental_type == 'day' ) {
                    $data_item[ 'define_day' ] = $defined_one_day;
                }

                if( ! empty( $ovabrw_services ) ){

                    $services_id      = get_post_meta( $product_id, 'ovabrw_service_id', true ); 
                    $services_name    = get_post_meta( $product_id, 'ovabrw_service_name', true );
                    $services_label   = get_post_meta( $product_id, 'ovabrw_label_service', true ); 

                    foreach( $ovabrw_services as $val_ser ) {
                        if( ! empty( $services_id ) && is_array( $services_id ) ) {
                            foreach( $services_id as $key => $value ) {
                                if( is_array( $value ) && ! empty( $value ) ) {
                                    foreach( $value as $k => $val ) {
                                        if( $val_ser == $val && ! empty( $val ) ){
                                            $service_name   = $services_name[$key][$k];
                                            $service_label  = $services_label[$key];
                                            $data_item[$service_label] = $service_name;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // Add resource
                if ( ! empty( $ovabrw_resources ) ) {
                    foreach ( $ovabrw_resources as $rs_key => $rs_value ) {
                        wc_add_order_item_meta( $item_id, 'Resource', $rs_value );
                    }    
                }

                // Add item
                foreach ( $data_item as $meta_key => $meta_value ) {
                    wc_add_order_item_meta( $item_id, $meta_key, $meta_value );
                }

                // Update item meta
                $item->set_props(
                    array(
                        'total'     => $item_total,
                        'subtotal'  => $item_total,
                        'taxes'     => array(
                            'total'    => $line_tax,
                            'subtotal' => $line_tax,
                        ),
                    )
                );

                $item->save();
                // End update item meta

                $i++;
            }

            $data_order = [
                'order_total'       => $order_total,
                'total_remaining'   => $total_remaining,
                'total_deposit'     => $total_deposite,
                'total_insurance'   => $total_insurance,
                'has_deposit'       => $has_deposit,
                'tax_display_cart'  => $tax_display_cart,
                'remaining_taxes'   => $remaining_taxes,
                'tax_rate_id'       => $tax_rate_id,
                'tax_amount'        => $tax_amount,
            ];

            return apply_filters( 'ovabrw_ft_add_order_item', $data_order, $order_id );
        }

        public function ovabrw_create_new_order_manully() {
               
            if ( isset( $_POST['ovabrw_create_order'] ) && $_POST['ovabrw_create_order'] === 'create_order' ) {

                // Check Permission
                if( !current_user_can( apply_filters( 'ovabrw_create_order' ,'publish_posts' ) ) ){

                    echo    '<div class="notice notice-error is-dismissible">
                                <h2>'.esc_html__( 'You don\'t have permission to create order', 'ova-brw' ).'</h2>
                            </div>';
                    return;
                }

                $data_order_keys = array();

                $order       = wc_create_order(); // Create new order

                $order_id    = $order->get_id(); // Get order id

                // Add product -> Get Total
                // Get array product ids
                $products = isset( $_POST['ovabrw_name_product'] ) ? $_POST['ovabrw_name_product'] : [];

                // Check order deposit
                $has_deposit = false;

                if ( ! empty( $products ) ) {

                    foreach( $products as $key => $product ) {

                        $item_total = 0;

                        $product    = trim( sanitize_text_field( $product ) );
                        $id_product = substr( $product, strpos( $product, '(#' ) );
                        $id_product = str_replace('(#', '', $id_product);
                        $id_product = str_replace(')', '', $id_product);

                        $ovabrw_number_vehicle  = isset( $_POST['ovabrw_number_vehicle'][$key] )  ? floatval( $_POST['ovabrw_number_vehicle'][$key] )  : 1;
                        $item_total_deposite    = isset( $_POST['ovabrw_amount_deposite'][$key] ) ? floatval( $_POST['ovabrw_amount_deposite'][$key] ) : 0;

                        $item_total             = isset( $_POST['ovabrw_total_product'][$key] )   ? floatval( $_POST['ovabrw_total_product'][$key] )   : 0;

                        if ( $item_total_deposite && floatval( $item_total_deposite ) > 0 ) {
                            $item_total = $item_total_deposite;
                        }

                        if ( wc_tax_enabled() ) {
                            $obj_products   = wc_get_product( $id_product );
                            $item_total     = $this->ovabrw_get_product_price_taxed_enable( $obj_products, $item_total );
                        }

                        $order->add_product( wc_get_product( $id_product ), $ovabrw_number_vehicle, array( 'total' => $item_total ) );
                    }
                }

                $order_data  = $this->ovabrw_add_order_item( $order_id ); // Get order data

                // Get data total
                $order_total = $order_data['order_total'];
                
                if ( $order_data['has_deposit'] ) {

                    $data_order_keys['_ova_remaining_amount'] = $order_data['total_remaining'];
                    $data_order_keys['_ova_deposit_amount']   = $order_data['total_deposit'];
                    $data_order_keys['_ova_has_deposit']      = 1;
                    $data_order_keys['_ova_remaining_taxes']  = 0;
                }

                if ( $order_data['tax_display_cart'] ) {
                    $data_order_keys['_ova_tax_display_cart'] = 1;
                }

                if ( $order_data['remaining_taxes'] ) {
                    $data_order_keys['_ova_remaining_taxes']  = $order_data['remaining_taxes'];
                }

                $data_order_keys['_ova_insurance_amount'] = $order_data['total_insurance'];
                $data_order_keys['_ova_original_total']   = $order_total;

                foreach ( $data_order_keys as $key => $update ) {
                    $order->update_meta_data( $key , $update );
                }

                $order->set_total( $order_total );
                $order->set_address( $this->ovabrw_get_address(), 'billing' );
                $order->set_address( $this->ovabrw_get_address(), 'shipping' );

                $status_order = isset( $_POST['status_order'] ) ? sanitize_text_field( $_POST['status_order'] ) : '';

                // Tax
                if ( wc_tax_enabled() && $order_data['tax_rate_id'] && $order_data['tax_amount'] ) {

                    $tax_rate_id = $order_data['tax_rate_id'];
                    $tax_amount  = $order_data['tax_amount'];

                    $item = new WC_Order_Item_Tax();
                    $item->set_props(
                        array(
                            'rate_id'            => $tax_rate_id,
                            'tax_total'          => $tax_amount,
                            'shipping_tax_total' => 0,
                            'rate_code'          => WC_Tax::get_rate_code( $tax_rate_id ),
                            'label'              => WC_Tax::get_rate_label( $tax_rate_id ),
                            'compound'           => WC_Tax::is_compound( $tax_rate_id ),
                            'rate_percent'       => WC_Tax::get_rate_percent_value( $tax_rate_id ),
                        )
                    );

                    // Add item to order and save.
                    $order->add_item( $item );
                }

                if ( $order_data['tax_amount'] ) {
                    $order->set_cart_tax( $order_data['tax_amount'] );
                }

                if ( $status_order ) {
                    $order->update_status( $status_order );
                }

                $order->save();
            }
        }
    }
}

new Ovabrw_Model();