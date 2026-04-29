<?php 
defined( 'ABSPATH' ) || exit();


//add row To pay, Remaining in Cart page
add_action( 'woocommerce_cart_totals_after_order_total',  'ovabrw_woocommerce_cart_totals_after_order_total',10,1 ); 
function ovabrw_woocommerce_cart_totals_after_order_total( $cart_object ){
    display_deposit_in_hook_cart_and_checkout();
}

//add row To pay, Remaining in checkout page
add_action('woocommerce_review_order_after_order_total', 'ovabrw_woocommerce_review_order_after_order_total');
function ovabrw_woocommerce_review_order_after_order_total ($order) {
    display_deposit_in_hook_cart_and_checkout();
}

function display_deposit_in_hook_cart_and_checkout() {

    $has_deposit = isset(WC()->cart->deposit_info[ '_ova_has_deposit' ]) ? WC()->cart->deposit_info[ '_ova_has_deposit' ] : '';

    if ( isset(WC()->cart->deposit_info[ 'ova_deposit_amount' ]) && $has_deposit ){

        $total = WC()->cart->total;
        $deposit_amount   = WC()->cart->deposit_info[ 'ova_deposit_amount' ];
        $remaining_amount = WC()->cart->deposit_info[ 'ova_remaining_amount' ];
        $remaining_taxes  = WC()->cart->deposit_info[ 'ova_remaining_taxes' ];

        $tax_text         = '';

        if ( wc_tax_enabled() ) {

            $tax_string_array = array();
            $cart_tax_totals  = WC()->cart->get_tax_totals();

            if ( WC()->cart->display_prices_including_tax() ) {
                foreach ( $cart_tax_totals as $code => $tax ) {
                    $tax_string_array[] = sprintf( '%s %s', wc_price( $remaining_taxes ), $tax->label );
                }
            } else {
                $tax_string_array[] = sprintf( '%s %s', wc_price( $remaining_taxes ), WC()->countries->tax_or_vat() );
            }

            $tax_string       = sprintf( '%s %s', wc_price( $remaining_taxes ), WC()->countries->tax_or_vat() );
            $is_tax_included  = 'excl' != WC()->cart->get_tax_price_display_mode();
            $tax_message      = $is_tax_included ? __( 'includes', 'ova-brw' ) : __( 'excludes', 'ova-brw' );

            if ( ! empty( $remaining_amount ) ) {
                $tax_text = ' <small class="ova_tax_label">' . esc_html( '('.$tax_message.' '.implode( ', ', $tax_string_array ).')' ) . '</small>';
            }
        }
    ?>
    <tr class="order-paid">
        <th><?php esc_html_e('To Pay','ova-brw'); ?></th>
        <td data-title="<?php esc_html_e('To Pay','ova-brw'); ?>">
            <strong><?php echo wp_kses_post( wc_price( $total ) ); ?></strong></td>
    </tr>
    <tr class="order-remaining">
        <th><?php esc_html_e('Remaining','ova-brw'); ?></th>
        <td data-title="<?php esc_html_e('Remaining','ova-brw'); ?>">
            <strong><?php echo wp_kses_post( wc_price( $remaining_amount ) ); ?><?php echo wp_kses_post( apply_filters( 'ova_remaining_tax_text_html', $tax_text ) ); ?></strong></td>
    </tr>
    <?php
    }
}


//add row To pay, Remaining in success checkout 
add_filter( 'woocommerce_get_order_item_totals' , 'get_order_item_totals' , 10 , 2 );
function get_order_item_totals( $total_rows , $order ){
    
    $order_has_deposit = $order->get_meta( '_ova_has_deposit' , true );
    $deposit_amount    = !empty($order->get_meta( '_ova_deposit_amount' , true )) ? floatval($order->get_meta( '_ova_deposit_amount' , true )) : 0;
    $remaining_amount  = $order->get_meta( '_ova_remaining_amount' , true );
    $remaining_taxes   = $order->get_meta( '_ova_remaining_taxes' , true );

    $tax_text          = '';

    if ( wc_tax_enabled() ) {
        
        $tax_string_array = array();
        $cart_tax_totals  = $order->get_tax_totals();

        if ( $order->get_meta( '_ova_tax_display_cart' , true ) == 1 ) {
            foreach ( $cart_tax_totals as $code => $tax ) {
                $tax_string_array[] = sprintf( '%s %s', wc_price( $remaining_taxes ), $tax->label );
            }
        } else {
            $tax_string_array[] = sprintf( '%s %s', wc_price( $remaining_taxes ), WC()->countries->tax_or_vat() );
        }

        $is_tax_included  = 'excl' != get_option( 'woocommerce_tax_display_cart' );
        $tax_message      = $is_tax_included ? __( 'includes', 'ova-brw' ) : __( 'excludes', 'ova-brw' );

        if ( ! empty( $remaining_amount ) && $remaining_taxes > 0 ) {
            $tax_text = ' <small class="ova_tax_label_checkout">' . esc_html( '('.$tax_message.' '.implode( ', ', $tax_string_array ).')' ) . '</small>';
        } 
    }

    if ( $order_has_deposit ):

        if( is_checkout() ){

            $total_rows[ 'deposit_amount' ] = array(
                'label' => esc_html__('To Pay:', 'ova-brw'),
                'value' => wc_price( $deposit_amount , array( 'currency' => $order->get_currency() ) )
            );

            $total_rows[ 'remaining_amount' ] = array(
                'label' => esc_html__('Remaining:', 'ova-brw'),
                'value' => wc_price( $remaining_amount , array( 'currency' => $order->get_currency() ) ) . apply_filters( 'ova_remaining_tax_text_checkout_html', $tax_text )
            );
        }

    endif;
    return $total_rows;
}

//set price is deposit amount and set_meta data (deposit amount, remaining);
add_action('woocommerce_checkout_order_processed', 'ova_checkout_order_processed', 10, 2);
function ova_checkout_order_processed( $order_id ) {

    $order = wc_get_order($order_id);
    $has_deposit      = isset( WC()->cart->deposit_info[ '_ova_has_deposit' ] )    ? WC()->cart->deposit_info[ '_ova_has_deposit' ]    : '';
    $insurance_amount = isset( WC()->cart->deposit_info['ova_insurance_amount'] )  ? WC()->cart->deposit_info['ova_insurance_amount']  : 0;
    $remaining_amount = isset( WC()->cart->deposit_info['ova_remaining_amount'] )  ? WC()->cart->deposit_info['ova_remaining_amount']  : 0;
    $remaining_taxes  = isset( WC()->cart->deposit_info[ 'ova_remaining_taxes' ] ) ? WC()->cart->deposit_info[ 'ova_remaining_taxes' ] : 0;
    $original_total   = WC()->cart->total;

    $incl_tax         = WC()->cart->display_prices_including_tax();

    $order->add_meta_data( '_ova_insurance_amount', $insurance_amount, true );

    if ( $has_deposit ) {

        $order->add_meta_data( '_ova_tax_display_cart', $incl_tax, true );
        $order->add_meta_data( '_ova_remaining_taxes', $remaining_taxes, true );
        $order->add_meta_data( '_ova_deposit_amount', $original_total, true );
        $order->add_meta_data( '_ova_remaining_amount', round( $remaining_amount, wc_get_price_decimals() ), true );
        
        $order->add_meta_data( '_ova_original_total', $original_total, true );
        $order->add_meta_data( '_ova_has_deposit', $has_deposit, true );
        $order->save();
    } else {
        $order->delete_meta_data( '_ova_deposit_amount' );
        $order->delete_meta_data( '_ova_remaining_amount' );
        $order->delete_meta_data( '_ova_original_total' );
        $order->delete_meta_data( '_ova_has_deposit' );
        $order->save();
    }
}

//set total original if has deposit display total is checkout page, else set_total to display is order admin page
add_filter( 'woocommerce_order_get_total' ,'ova_get_order_total', 10 , 2 );
function ova_get_order_total( $total , $order ){

    $has_deposit = $order->get_meta( '_ova_has_deposit' , true );
    $_ova_original_total = $order->get_meta( '_ova_original_total' , true );
    if ( $has_deposit && !did_action('valid-paypal-standard-ipn-request') && (is_order_received_page()  || ! is_checkout()) )  {
        $total = $_ova_original_total;
        $order->set_total( $total );
    } 

    return $total;
}


//add column is amount deposit in order admin page
add_filter( 'manage_edit-shop_order_columns', 'add_deposit_column', 10, 1 );
add_action( 'manage_shop_order_posts_custom_column','ova_populate_deposit_column', 10, 1 );

function add_deposit_column($columns){

    $new_columns = array();

    foreach($columns as $key => $column){

        if($key === 'order_total'){
            $new_columns['ova_amount_deposit_column'] = esc_html__('Deposit', 'ova-brw');
        }
        $new_columns[$key] = $column;

    }

    return $new_columns;

}
function ova_populate_deposit_column($column){

    if ( 'ova_amount_deposit_column' === $column ) {
        global $post;
        $order = wc_get_order($post->ID);
        if($order){
            $order_amount_deposit = $order->get_meta( '_ova_deposit_amount' , true );
            if ($order_amount_deposit) {
            ?>
                <span class="ova_deposit_amount"><?php echo wp_kses_post( wc_price($order_amount_deposit) ); ?></span>
            <?php
            }
        }
    }

}


//add column is amount remaining
add_filter( 'manage_edit-shop_order_columns', 'add_amount_remaining_column', 10, 2 );
add_action( 'manage_shop_order_posts_custom_column','ova_populate_amount_remaining_column', 10, 2 );

function add_amount_remaining_column($columns){

    $new_columns = array();

    foreach($columns as $key => $column){

        if($key === 'order_total'){
            $new_columns['ova_amount_remaing_column'] = esc_html__('Remaining', 'ova-brw');
        }
        $new_columns[$key] = $column;

    }

    return $new_columns;

}
function ova_populate_amount_remaining_column($column){

    if ( 'ova_amount_remaing_column' === $column ) {
        global $post;

        $order      = wc_get_order($post->ID);
        $order_item = $order->get_items();
        
        $order_remaining_amount = $order->get_meta( '_ova_remaining_amount' , true );
        $order_tax_display_cart = $order->get_meta( '_ova_tax_display_cart' , true );
        $order_remaining_taxes  = floatval( $order->get_meta( '_ova_remaining_taxes' , true ) );

        if ( wc_tax_enabled() ) {

            $order_remaining_amount = 0;

            foreach ( $order_item as $items ) {

                $product_id      = $items->get_product_id();
                $remaining_item  = $items->get_meta( 'ovabrw_remaining_amount_product' );

                if ( ! $order_tax_display_cart && $order_remaining_taxes ) {
                    $remaining_item += $order_remaining_taxes;
                }

                if ( $remaining_item ) {
                    $order_remaining_amount += $remaining_item;
                }

            }
        }
        ?>
        <span class="ova_amount_remaining_column ">
            <?php echo wp_kses_post( wc_price( $order_remaining_amount ) ); ?>
        </span>
        <?php
    }

}

//add column status deposit
add_filter( 'manage_edit-shop_order_columns', 'ova_add_status_deposit_column', 10, 2 );
add_action( 'manage_shop_order_posts_custom_column','ova_populate_status_deposit_column', 10, 2 );

function ova_add_status_deposit_column($columns){

    $new_columns = array();

    foreach($columns as $key => $column){

        if($key === 'order_total'){
            $new_columns['ova_status_deposit_column'] = esc_html__('Deposit status', 'ova-brw');
        }
        $new_columns[$key] = $column;

    }

    return $new_columns;

}
function ova_populate_status_deposit_column($column){

    if ( 'ova_status_deposit_column' === $column ) {
        global $post;
        $order = wc_get_order($post->ID);
        $order_amount_remaining = $order->get_meta( '_ova_remaining_amount' , true );

        if ($order_amount_remaining == 0) {
            ?>
                <span class="ova_amount_status_deposit_column ova_paid"><?php esc_html_e('Full Payment', 'ova-brw') ?></span>
            <?php
        } else {
            ?>
                <span class="ova_amount_status_deposit_column ova_pending"><?php esc_html_e('Partial Payment', 'ova-brw') ?></span>
            <?php
        }
        ?>

        
        <?php
    }

}


//add column status process insurance 
add_filter( 'manage_edit-shop_order_columns', 'ova_add_status_insurance_column', 10, 2 );
add_action( 'manage_shop_order_posts_custom_column','ova_populate_status_insurance_column', 10, 2 );

function ova_add_status_insurance_column($columns){

    $new_columns = array();

    foreach($columns as $key => $column){

        if($key === 'order_total'){
            $new_columns['ova_status_insurance_column'] = esc_html__('Insurance Status', 'ova-brw');
        }
        $new_columns[$key] = $column;

    }

    return $new_columns;

}
function ova_populate_status_insurance_column($column){

    if ( 'ova_status_insurance_column' === $column ) {
        global $post;
        $order = wc_get_order($post->ID);
        $order_amount_insurance = $order->get_meta( '_ova_insurance_amount' , true );

        if ($order_amount_insurance == 0) {
            ?>
                <span class="ova_amount_status_insurance_column ova_paid"><?php esc_html_e('Paid for Customers', 'ova-brw') ?></span>
            <?php
        } else {
            ?>
                <span class="ova_amount_status_deposit_column ova_pending"><?php esc_html_e('Received', 'ova-brw') ?></span>
            <?php
        }
        ?>

        
        <?php
    }

}


//add column and display Deposit amount and Remaining amount in detail order
add_action( 'woocommerce_admin_order_item_headers' , 'ova_admin_order_item_headers' );
add_action( 'woocommerce_admin_order_item_values' , 'ova_admin_order_item_values', 10, 3 );

function ova_admin_order_item_headers( $order ){
  
    if ( get_option( 'ova_brw_booking_form_show_extra', 'no' ) == 'yes' ): ?>
        <th class="ovabrw-extra-price"><?php esc_html_e( 'Extra Price' , 'ova-brw' ); ?></th>
    <?php endif; ?>

    <th class="insurance-amount"><?php esc_html_e( 'Amount Insurance' , 'ova-brw' ); ?></th>
    <th class="deposit-paid"><?php esc_html_e( 'Deposit' , 'ova-brw' ); ?></th>
    <th class="deposit-remaining"><?php esc_html_e( 'Remaining' , 'ova-brw' ); ?></th>
    <?php
}

function ova_admin_order_item_values( $product , $item , $item_id ){

    global $post;
    $deposit_meta = null;

    $order_id = wc_get_order_id_by_order_item_id( $item_id );

    if ( ! $order_id ) {
        return;
    }

    $order       = wc_get_order( $order_id );

    $has_deposit = ! empty( $order->get_meta( '_ova_has_deposit' , true ) ) ? floatval( $order->get_meta( '_ova_has_deposit' , true ) ) : 0;

    if( ! $product ) return;

    if ( $product ) {
        $deposit_meta = isset( $item[ 'wc_deposit_meta' ] ) ? $item[ 'wc_deposit_meta' ] : null;
    }

    $total = $item->get_total();
    $ovabrw_deposit_amount   = isset( $item[ 'ovabrw_deposit_amount_product' ] )   ? floatval($item[ 'ovabrw_deposit_amount_product' ])   : 0;
    $ovabrw_remaining_amount = isset( $item[ 'ovabrw_remaining_amount_product' ] ) ? floatval($item[ 'ovabrw_remaining_amount_product' ]) : 0;
    $ovabrw_amount_insurance = isset( $item[ 'ovabrw_amount_insurance_product' ] ) ? floatval($item[ 'ovabrw_amount_insurance_product' ]) : 0;

    ?>

    <?php
        $service_html = $resource_html = '';
        if ( get_option( 'ova_brw_booking_form_show_extra', 'no' ) == 'yes' ) {
            // Get html services and resource
            $resources  = $item->get_meta('ovabrw_resources');
            $services   = $item->get_meta('ovabrw_services');
            $product_id = $item['product_id'];
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
    
    ?>
        <td class="ovabrw-extra-price" width="12%">
            <div class="view">
                <?php
                    echo wp_kses_post( $html );
                ?>
            </div>
        </td>
    <?php } ?>

    <td class="ova-amount-insurance" width="1%">
        <div class="view">
            <?php
                if ( $ovabrw_amount_insurance ) {
                    echo wp_kses_post( wc_price( $ovabrw_amount_insurance ) );
                }
            ?>
        </div>
        <?php if( $product ){ 
            ?>
            <div class="edit" style="display: none;">
               <input type="text" <?php echo $ovabrw_amount_insurance > 0 ? esc_attr( 'disabled="disabled"' ) : esc_attr( '' ); ?> name="amount_insurance[<?php echo esc_attr( absint( $item_id ) ); ?>]"
               placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>"
               value="<?php echo esc_attr( $ovabrw_amount_insurance ); ?>"
               class="amount_insurance wc_input_price"
               data-total="<?php echo esc_attr( $ovabrw_amount_insurance ); ?>"/>
            </div>
        <?php } ?>
    </td>

    <td class="ova-deposit-paid" width="1%">
        <div class="view">
            <?php
                if ( $has_deposit ) {
                    echo wp_kses_post( wc_price( $ovabrw_deposit_amount ) );
                }
            ?>
        </div>

        <?php if ( $product && $has_deposit ) { ?>
            <div class="edit" style="display: none;">
                <input type="text" <?php echo $has_deposit != 1 ? esc_attr( 'disabled="disabled"' ) : esc_attr( '' ); ?> name="amount_deposit[<?php echo esc_attr( absint( $item_id ) ); ?>]"
                       placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo esc_attr( $ovabrw_deposit_amount ); ?>"
                       class="amount_deposit wc_input_price" data-total="<?php echo esc_attr( $ovabrw_deposit_amount ); ?>"/>
            </div>
        <?php } ?>
        
    </td>
    <td class="deposit-remaining" width="1%">
        <div class="view">
            <?php
                if ( $has_deposit ) {
                    echo wp_kses_post( wc_price( $ovabrw_remaining_amount ) );
                }
            ?>
        </div>

        <?php if ( $product && $has_deposit ) { ?>
            <div class="edit" style="display: none;">
                <input type="text" disabled="disabled" name="amount_remaining[<?php echo esc_attr( absint( $item_id ) ); ?>]"
                       placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>"
                       value="<?php echo esc_attr( $ovabrw_remaining_amount ); ?>"
                       class="amount_remaining wc_input_price"
                       data-total="<?php echo esc_attr( $ovabrw_remaining_amount ); ?>"/>
            </div>
        <?php } ?>
    </td>
    <?php
}

//dissplay total deposit and total ramaining in order detail
add_action( 'woocommerce_admin_order_totals_after_total' , 'ova_admin_order_totals_after_total', 10, 3 );
function ova_admin_order_totals_after_total( $order_id ){

    $order = wc_get_order( $order_id );

    $deposit_amount   = ! empty( $order->get_meta( '_ova_deposit_amount' , true ) )    ? floatval( $order->get_meta( '_ova_deposit_amount' , true ) )    : 0;
    $remaining_amount = ! empty( $order->get_meta( '_ova_remaining_amount' , true ) )  ? floatval( $order->get_meta( '_ova_remaining_amount' , true ) )  : 0;
    $insurance_amount = ! empty( $order->get_meta( '_ova_insurance_amount' , true ) )  ? floatval( $order->get_meta( '_ova_insurance_amount' , true ) )  : 0;
    $has_deposit      = ! empty( $order->get_meta( '_ova_has_deposit' , true ) )       ? floatval( $order->get_meta( '_ova_has_deposit' , true ) )       : 0;

    $tax_element      = '';

    if ( wc_tax_enabled() ) {
        $is_tax_included  = $order->get_meta( '_ova_tax_display_cart', true );
        $tax_message      = $is_tax_included ? __( '(incl. tax)', 'ova-brw' ) : __( '(excl. tax)', 'ova-brw' );
        $remaining_taxes  = $order->get_meta( '_ova_remaining_taxes', true );

        if ( ! empty( $remaining_amount ) && ! empty( $remaining_taxes ) ) {
            $tax_element = ' <small class="tax_label">' . $tax_message . '</small>';
        }
    }

    $order_item = $order->get_items();
    $total_deposit = 0;
    $subtotal = 0;
    foreach( $order_item as $item ) {

        $subtotal += $item->get_subtotal();
        
        $meta_data = $item->get_formatted_meta_data('key');

        if( ! empty( $meta_data ) && is_array( $meta_data ) ) {
            foreach( $meta_data as $data ) {
                if ( $data->key == 'ovabrw_deposit_amount_product' ) {
                    $total_deposit += (float)$data->value;
                }
            }
        }

        if ( $total_deposit == 0 ) {
            $total_deposit += $subtotal;
        }

        $item_taxes = $item->get_taxes();
        if ( !empty( $item_taxes ) && is_array( $item_taxes ) && isset( $item_taxes['total'] ) ) {
            $tax = reset( $item_taxes['total'] );
            $total_deposit += round( $tax, wc_get_price_decimals() );
        }
    }

    if ( $deposit_amount == 0 ) {
        $deposit_amount = $total_deposit;
        $remaining_amount = $order->get_total() - $deposit_amount;
    }
    ?>

        <?php if ( ! empty( $deposit_amount ) && $has_deposit == 1 ) : ?>
            <tr>
                <td class="label"><?php esc_html_e( 'Total Deposit Amount:' , 'ova-brw' ); ?></td>
                <td>
                </td>
                <td class="total">
                    <div class="view"><?php echo wp_kses_post( wc_price( $deposit_amount , array( 'currency' => $order->get_currency() ) ) ); ?></div>
                </td>
            </tr>

            <tr>
                <td class="label">
                    <?php esc_html_e( 'Total Remaining Amount' , 'ova-brw' ); ?>
                    <?php echo wp_kses_post( $tax_element ); ?>:</td>
                <td>
                </td>
                <td class="total">
                    <div class="view"><?php echo wp_kses_post( wc_price( $remaining_amount , array( 'currency' => $order->get_currency() ) ) ); ?></div>
                </td>
            </tr>
        <?php endif; ?>
        <?php if ( $insurance_amount ): ?>
            <tr>
                <td class="label"><?php esc_html_e( ' Total Insurance Amount' , 'ova-brw' ); ?>:</td>
                <td>
                </td>
                <td class="total">
                    <div class="view">
                        <?php echo wp_kses_post( wc_price( $insurance_amount , array( 'currency' => $order->get_currency() ) ) ); ?></div>
                </td>
            </tr>
        <?php endif; ?>
    <?php
}

/**
 * Show Pay Full when remaining amount > 0
 */
add_action( 'woocommerce_after_order_itemmeta', 'ova_after_order_itemmeta', 10, 3 );
function ova_after_order_itemmeta( $item_id, $item, $product ) {

    $order_id = $item->get_order_id();
    $order    = wc_get_order( $order_id );

    //Get remaining_amount
    $remaining_amount = wc_get_order_item_meta( $item_id, 'ovabrw_remaining_amount_product' );

    if ( $remaining_amount && is_numeric( $remaining_amount ) && (float)$remaining_amount > 0 ):
    ?>
        <div class="ova_pay_full">
            <a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'pay_full' => $item_id ), admin_url( 'post.php?post=' . $order_id . '&action=edit' ) ), 'pay_full', 'pay_full_nonce' ) ); ?>"><?php esc_html_e( 'PAY FULL', 'ova-brw' ) ?></a>
        </div>
    <?php
    endif;
}

/**
 * When click Pay Full
 */
add_action( 'admin_init', 'ova_order_action_pay_full' );
function ova_order_action_pay_full() {

    $action   = false;
    $item_id  = false;
    $order_id = false;

    $data_item_amount = array(
        'ovabrw_deposit_amount_product'     => 0,
        'ovabrw_remaining_amount_product'   => 0,
    );

    $data_order_keys  = array(
        '_ova_remaining_amount' => 0,
        '_ova_deposit_amount'   => 0,
        '_ova_original_total'   => 0,
    );

    $total_order = $item_total = $total_remaining_amount = $total_deposit_amount = 0;
    $line_tax    = $item_taxes = array();

    if ( ! empty( $_GET['pay_full'] ) && isset( $_GET['pay_full_nonce'] ) && wp_verify_nonce( $_GET['pay_full_nonce'], 'pay_full' ) ) {
        $action  = 'pay_full';
        $item_id = absint( $_GET['pay_full'] );
    }

    if ( ! $item_id ) {
        return;
    }

    $item = WC_Order_Factory::get_order_item( absint( $item_id ) );

    if ( ! $item ) {
        return;
    }

    $product_id = $item->get_product_id();

    $order_id = wc_get_order_id_by_order_item_id( $item_id );

    if ( ! $order_id ) {
        return;
    }

    $order = wc_get_order( $order_id );

    $tax_display_cart   = get_post_meta( $order_id, '_ova_tax_display_cart', true ) ? floatval( get_post_meta( $order_id, '_ova_tax_display_cart', true ) ) : 0;
    $prices_include_tax = get_post_meta( $order_id, '_prices_include_tax', true )   ? get_post_meta( $order_id, '_prices_include_tax', true ) : 'yes';

    // Order
    $order_remaining_amount = $order->get_meta( '_ova_remaining_amount', true );
    $order_deposit_amount   = $order->get_meta( '_ova_deposit_amount', true );

    // Item
    $item_remaining_amount  = wc_get_order_item_meta( $item_id, 'ovabrw_remaining_amount_product', true );
    $item_deposit_amount    = wc_get_order_item_meta( $item_id, 'ovabrw_deposit_amount_product', true );

    $total_remaining_amount = $order_remaining_amount - $item_remaining_amount;
    $total_deposit_amount   = $order_deposit_amount + $item_remaining_amount;
    $item_deposit_amount   += $item_remaining_amount;
    $item_total             = $item_deposit_amount;

    // Tax enabled
    if ( wc_tax_enabled() ) {

        foreach ( $order->get_items('tax') as $item_taxes ) {
            $rate_id        = $item_taxes->get_rate_id(); // Get rate id
            $rate_percent   = $item_taxes->get_rate_percent(); // Get rare percent
        }

        if ( $rate_id && $rate_percent ) {

            if ( $prices_include_tax === 'yes' ) {

                if ( $tax_display_cart == 1 ) {

                    $tax_amount = ovabrw_get_tax_amount_by_tax_rates( $item_deposit_amount, $rate_percent, $prices_include_tax );
                    $item_total = $item_deposit_amount - $tax_amount;

                    $line_tax[ $rate_id ] = $tax_amount;

                } else {

                    $item_deposit_amount_incl_tax   = round( ( $item_deposit_amount * ( $rate_percent + 100 ) ) / 100, wc_get_price_decimals() );
                    $item_remaining_amount_incl_tax = round( ( $item_remaining_amount * ( $rate_percent + 100 ) ) / 100, wc_get_price_decimals() );

                    $tax_amount            = ovabrw_get_tax_amount_by_tax_rates( $item_deposit_amount_incl_tax, $rate_percent, $prices_include_tax );
                    $tax_remaining         = ovabrw_get_tax_amount_by_tax_rates( $item_remaining_amount_incl_tax, $rate_percent, $prices_include_tax );
                    $item_total            = $item_deposit_amount_incl_tax - $tax_amount;
                    $total_deposit_amount += $tax_remaining;
                    $item_total            = $item_deposit_amount;
                    $line_tax[ $rate_id ]  = $tax_amount;
                }
                
            } else {

                if ( $tax_display_cart == 1 ) {

                    $item_deposit_amount_excl_tax   = round( ( $item_deposit_amount * 100 ) / ( $rate_percent + 100 ), wc_get_price_decimals() );

                    $tax_amount    = ovabrw_get_tax_amount_by_tax_rates( $item_deposit_amount_excl_tax, $rate_percent, $prices_include_tax );
                    $item_total    = $item_deposit_amount_excl_tax;

                    $line_tax[ $rate_id ] = $tax_amount;

                } else {

                    $tax_amount            = ovabrw_get_tax_amount_by_tax_rates( $item_deposit_amount, $rate_percent, $prices_include_tax );
                    $tax_remaining         = ovabrw_get_tax_amount_by_tax_rates( $item_remaining_amount, $rate_percent, $prices_include_tax );
                    $total_deposit_amount += $tax_remaining;
                    $item_total            = $item_deposit_amount;

                    $line_tax[ $rate_id ]  = $tax_amount;
                }
            }
        }
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

    $data_item_amount['ovabrw_deposit_amount_product'] = $item_deposit_amount;

    foreach ( $data_item_amount as $key => $update ) {
        wc_update_order_item_meta( $item_id , $key , $update );
    }

    $item->save();
    // End update item meta

    $data_order_keys['_ova_remaining_amount'] = $total_remaining_amount;
    $data_order_keys['_ova_deposit_amount']   = $total_deposit_amount;
    $data_order_keys['_ova_original_total']   = $total_deposit_amount;

    foreach ( $data_order_keys as $key => $update ) {
        $order->update_meta_data( $key , $update );
    }

    $order->set_total( $total_deposit_amount );
    $order->update_taxes();
    $order->save();

    // Email invoice
    $emails = WC_Emails::instance();
    $emails->customer_invoice( $order );

    wp_redirect( admin_url( 'post.php?post=' . absint( $order_id ) . '&action=edit' ) );
    exit;
}


add_action( 'woocommerce_saved_order_items' , 'ova_saved_order_items' , 10 , 2 );
function ova_saved_order_items( $order_id , $items ){

    $order = wc_get_order( $order_id );
    $order->read_meta_data( true );

    $data_item_amount   = array(
        'ovabrw_deposit_amount_product'     => 0,
        'ovabrw_remaining_amount_product'   => 0,
    );

    $data_order_keys    = array(
        '_ova_insurance_amount' => 0,
        '_ova_deposit_amount'   => 0,
        '_ova_remaining_amount' => 0,
        '_ova_original_total'   => 0,
    );

    $has_deposit        = ! empty( $order->get_meta( '_ova_has_deposit' , true ) )      ? floatval( $order->get_meta( '_ova_has_deposit' , true ) )      : 0;
    $tax_display_cart   = ! empty( $order->get_meta( '_ova_tax_display_cart', true ) )  ? floatval( $order->get_meta( '_ova_tax_display_cart' , true ) ) : 0;
    $prices_include_tax = get_post_meta( $order_id, '_prices_include_tax', true )       ? get_post_meta( $order_id, '_prices_include_tax', true )        : 'yes';

    if( isset( $items[ 'order_item_id' ] ) && $_POST[ 'action' ] === 'woocommerce_save_order_items' && $has_deposit == 1 ){

        $total_order        = $total_deposit_amount =  $total_remaining_amount = $total_insurance_amount = 0;

        $amount_insurance   = isset( $items[ 'amount_insurance' ] ) ? $items[ 'amount_insurance' ]  : array();
        $amount_deposit     = isset( $items[ 'amount_deposit' ] )   ? $items[ 'amount_deposit' ]    : array();

        foreach( $items[ 'order_item_id' ] as $item_id ){

            $item = WC_Order_Factory::get_order_item( absint( $item_id ) );

            if ( ! $item ) {
                continue;
            }

            $product_id = $item->get_product_id();

            $deposit    = isset( $amount_deposit[ $item_id ] )   ? floatval( wc_format_decimal( $amount_deposit[ $item_id ] ) )   : 0;
            $insurance  = isset( $amount_insurance[ $item_id ] ) ? floatval( wc_format_decimal( $amount_insurance[ $item_id ] ) ) : 0;

            $original_deposit    = floatval( wc_get_order_item_meta( $item_id, 'ovabrw_deposit_amount_product', true ) );
            if ( ! $original_deposit ) {
               $original_deposit = 0; 
            }

            $original_insurance  = floatval( wc_get_order_item_meta( $item_id, 'ovabrw_amount_insurance_product', true ) );
            if ( ! $original_insurance ) {
                $original_insurance = 0;
            }

            $deposit_full_amount = floatval( wc_get_order_item_meta( $item_id , 'ovabrw_deposit_full_amount' ) );
            if ( ! $deposit_full_amount ) {
                $deposit_full_amount = 0;
            }

            if ( $deposit && $deposit != $original_deposit ) {

                $item_total = $deposit;
                $line_tax   = array();

                $data_item_amount['ovabrw_deposit_amount_product']   = $deposit;
                $data_item_amount['ovabrw_remaining_amount_product'] = $deposit_full_amount - $deposit;

                if ( wc_tax_enabled() ) {

                    // Get taxes array
                    $line_tax    = isset( $items['line_tax'][ $item_id ] ) ? $items['line_tax'][ $item_id ] : array();
                    $order_taxes = isset( $items['order_taxes'] )          ? $items['order_taxes']          : array();

                    $tax_amount  = ovabrw_get_taxes_by_price( $deposit, $product_id, $prices_include_tax );

                    if (  $line_tax && $order_taxes ) {
                        $tax_item_id = reset( $order_taxes ); 

                        if ( $tax_item_id ) {
                            $line_tax[$tax_item_id] = $tax_amount;
                        }   
                    }

                    if (  $prices_include_tax === 'yes' ) {

                        if ( $tax_display_cart == 1 ) {

                            $item_total  = $deposit - $tax_amount;

                            // Decimal tax
                            if ( $data_item_amount['ovabrw_remaining_amount_product'] < 0.01 ) {
                                $data_item_amount['ovabrw_remaining_amount_product'] = 0;
                            }

                        } else {

                            $deposit -= $tax_amount;
                            $data_item_amount['ovabrw_deposit_amount_product']   = $deposit;
                            $data_item_amount['ovabrw_remaining_amount_product'] = $deposit_full_amount - $deposit;

                            $item_total  = $deposit;

                            // Decimal tax
                            if ( $data_item_amount['ovabrw_remaining_amount_product'] < 0.01 ) {
                                $data_item_amount['ovabrw_remaining_amount_product'] = 0;
                            }
                        }
                    } else {

                        if ( $tax_display_cart == 1 ) {

                            $tax_rate = WC_Tax::get_rate_percent_value( $tax_item_id );

                            if ( $tax_rate ) {

                                $item_total_excl_tax    = round( ( $deposit * 100 ) / ( $tax_rate + 100 ), wc_get_price_decimals() );
                                $tax_amount             = ovabrw_get_taxes_by_price( $item_total_excl_tax, $product_id, $prices_include_tax );
                                $item_total             = $item_total_excl_tax;

                                $line_tax[$tax_item_id] = $tax_amount;
                            } else {
                                exit;
                            }

                        } else {

                            $item_total  = $deposit;
                        }
                    }
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

                foreach ( $data_item_amount as $key => $update ) {
                    wc_update_order_item_meta( $item_id , $key , $update );
                }

                $item->save();
                // End update item meta
            }

            $item_insurance = 0;

            if ( $insurance && $insurance != $original_insurance ) {

                $item_insurance = $insurance - $original_insurance;

                //update deposit, total
                $total = floatval( wc_get_order_item_meta( $item_id , '_line_total' ) );
                wc_update_order_item_meta( $item_id , 'ovabrw_deposit_amount_product' , $deposit + $amount_insurance_update_total );
                wc_update_order_item_meta( $item_id , '_line_total' , $total + $amount_insurance_update_total );
                wc_update_order_item_meta( $item_id , '_line_subtotal' , $total + $amount_insurance_update_total );
            }

            $sub_insurance_amount    = wc_get_order_item_meta( $item_id, 'ovabrw_amount_insurance_product', true );

            // Get sub-deposit amount
            $sub_deposit_amount      = wc_get_order_item_meta( $item_id, 'ovabrw_deposit_amount_product', true );

            if ( empty( $sub_deposit_amount ) ) {

                $item_line_total     = round( wc_get_order_item_meta( $item_id, '_line_total', true ), wc_get_price_decimals() );
                $sub_deposit_amount  = $item_line_total + wc_get_order_item_meta( $item_id, '_line_tax', true );
            } else {

                if ( $tax_display_cart != 1 ) {
                    $sub_deposit_amount += wc_get_order_item_meta( $item_id, '_line_tax', true );
                }
            }
            // End
            
            $sub_remaining_amount    = wc_get_order_item_meta( $item_id, 'ovabrw_remaining_amount_product', true );
            // wc_add_order_item_meta($item_id, 'prices_include_tax', $prices_include_tax);

            $total_insurance_amount += $sub_insurance_amount;
            $total_deposit_amount   += $sub_deposit_amount;
            $total_remaining_amount += $sub_remaining_amount;

        }

        $data_order_keys['_ova_insurance_amount']   = $total_insurance_amount;
        $data_order_keys['_ova_deposit_amount']     = $total_deposit_amount;
        $data_order_keys['_ova_remaining_amount']   = $total_remaining_amount;
        $data_order_keys['_ova_original_total']     = $total_deposit_amount;

        foreach ( $data_order_keys as $key => $update ) {
            $order->update_meta_data( $key , $update );
        }

        $order->set_total( $total_deposit_amount );
        $order->update_taxes();
        $order->save();     
    }

}


