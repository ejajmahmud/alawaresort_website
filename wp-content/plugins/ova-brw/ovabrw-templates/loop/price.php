<?php
/**
 * The template for displaying price content within loop
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/loop/price.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

global $product;

// if the product type isn't ovabrw_car_rental
if( $product->get_type() !== 'ovabrw_car_rental' ) return;

$pid = $product->get_id();


$rental_type    = get_post_meta( $pid, 'ovabrw_price_type', true ) ? get_post_meta( $pid, 'ovabrw_price_type', true ) : 'day' ;
$price_hour     = get_post_meta( $pid, 'ovabrw_regul_price_hour', true );
$price_day      = get_post_meta( $pid, '_regular_price', true );

// Get price
$petime_price   = get_post_meta( $pid, 'ovabrw_petime_price', true );
$price_location = get_post_meta( $pid, 'ovabrw_price_location', true );

$min = $max = 0;
if ( $rental_type == 'period_time' && $petime_price && is_array( $petime_price ) ) {
    $min = min( $petime_price );
    $max = max( $petime_price );
}

if ( $rental_type == 'transportation' && $price_location && is_array( $price_location ) ) {
    $min = min( $price_location );
    $max = max( $price_location );
}



$ovabrw_pickup_date     = isset( $_GET['ovabrw_pickup_date'] ) ? sanitize_text_field( $_GET['ovabrw_pickup_date'] ) : '';
$ovabrw_pickoff_date    = isset( $_GET['ovabrw_pickoff_date'] ) ? sanitize_text_field( $_GET['ovabrw_pickoff_date'] ) : '';

$pickup_loc     = isset( $_GET['ovabrw_pickup_loc'] ) ? sanitize_text_field( $_GET['ovabrw_pickup_loc'] ) : '';
$pickoff_loc    = isset( $_GET['ovabrw_pickoff_loc'] ) ? sanitize_text_field( $_GET['ovabrw_pickoff_loc'] ) : '';

// If it is search page with date, time
if(  ( $rental_type == 'day' || $rental_type == 'hour' || $rental_type == 'mixed' ) && $ovabrw_pickup_date != '' && $ovabrw_pickoff_date != '' && apply_filters( 'ova_brw_finish_price_search', true ) ){

    $total_price =  get_price_by_date( $pid, strtotime( $ovabrw_pickup_date ), strtotime( $ovabrw_pickoff_date )); 

    $new_input_date = ovabrw_new_input_date( $pid, strtotime( $ovabrw_pickup_date ), strtotime( $ovabrw_pickoff_date ) );
    $real_quantity = get_real_quantity( 1, $pid, $new_input_date['pickup_date_new'], $new_input_date['pickoff_date_new'] );

    ?>

    <p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'ovabrw-price price' ) );?>">
        <span class="amount"><?php echo wp_kses_post( wc_price( $total_price['line_total'] ) ); ?></span>
        <span class="time">/ <?php echo wp_kses_post( $real_quantity ); ?> </span>
    </p>

<?php }else if( $rental_type == 'period_time' || $rental_type == 'transportation' ) { ?>
    
        <?php if ( $min && $max && $min == $max ): ?>
            <span class="amount"><?php echo wp_kses_post( wc_price( $min ) ); ?></span>
        <?php elseif ( $min && $max ): ?>
            <span class="amount">
                <?php echo wp_kses_post( wc_price( $min ).' - '.wc_price( $max ) ); ?>
            </span>
        <?php else: ?>
            <span class="amount"><?php esc_html_e( 'Optional price', 'ova-brw' ); ?></span>
        <?php endif; ?>

<?php }  else if( $rental_type == 'hour' ){ ?>
    <p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'ovabrw-price price' ) );?>">
        <span class="amount"><?php echo wp_kses_post( wc_price( $price_hour) ); ?> </span>
        <span class="label"><?php esc_html_e( '/ Hour', 'ova-brw' ); ?></span>
    </p>
<?php } else if( $rental_type == 'day' ){ ?>
    <p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'ovabrw-price price' ) );?>">
        <span class="amount"><?php echo wp_kses_post( wc_price( $price_day ) ); ?></span>
        <span class="label"><?php esc_html_e( '/ Day', 'ova-brw' ); ?></span>
    </p>
<?php }else if( $rental_type == 'mixed' ){ ?>
    <p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'ovabrw-price price' ) );?>">
        <span class="ovabrw_woo_price">
            <span class="amount"><?php echo wp_kses_post( wc_price( $price_hour ) ); ?></span>
            <span class="label"><?php esc_html_e( '/ Hour', 'ova-brw' ); ?></span>
        </span>
        <span class="ovabrw_woo_price">
            <span class="amount"><?php echo wp_kses_post( wc_price( $price_day ) ); ?></span>
            <span class="label"><?php esc_html_e( '/ Day', 'ova-brw' ); ?></span>
        </span>
    </p>
<?php }else{ ?>
    <p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'ovabrw-price price' ) );?>">
        <?php esc_html_e( 'Prices from £20', 'ova-brw' ); ?>
    </p>
<?php }
