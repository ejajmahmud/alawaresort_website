<?php if( ! defined( 'ABSPATH' ) ) exit();

global $product;

if( $product->get_type() !== 'ovabrw_car_rental' ) return;

$id = $product->get_id();

$has_special_offers = false;
$price_type = get_post_meta( $id, 'ovabrw_price_type', true );

if ( 'period_time' === $price_type ) {
	$petime_label = get_post_meta( $id, 'ovabrw_petime_label', true ); 

	if ( ! empty( $petime_label ) ) {
		$has_special_offers = true;
	}
} elseif ( 'hour' === $price_type || 'mixed' === $price_type ) {
	$rt_price_hour 	= get_post_meta( $id, 'ovabrw_rt_price_hour', true );

	if ( $rt_price_hour ) {
		$has_special_offers = true;
	}
} elseif ( 'day' === $price_type || 'mixed' === $price_type ) {
	$rt_price = get_post_meta( $id, 'ovabrw_rt_price', true );

	if ( $rt_price ) {
		$has_special_offers = true;
	}
}

$has_daily_price 	= false;
$monday_price 		= get_post_meta( $id, 'ovabrw_daily_monday', true );
$tuesday_price 		= get_post_meta( $id, 'ovabrw_daily_tuesday', true );
$wednesday_price 	= get_post_meta( $id, 'ovabrw_daily_wednesday', true );
$thursday_price 	= get_post_meta( $id, 'ovabrw_daily_thursday', true );
$friday_price 		= get_post_meta( $id, 'ovabrw_daily_friday', true );
$saturday_price 	= get_post_meta( $id, 'ovabrw_daily_saturday', true );
$sunday_price 		= get_post_meta( $id, 'ovabrw_daily_sunday', true );

if ( $monday_price || $tuesday_price || $wednesday_price || $thursday_price || $friday_price || $saturday_price || $sunday_price ) {
	$has_daily_price = true;
}

$rental_type    = get_post_meta( $id, 'ovabrw_price_type', true ) 	? get_post_meta( $id, 'ovabrw_price_type', true ) 	: 'day' ;
$define_1_day   = get_post_meta( $id, 'ovabrw_define_1_day', true ) ? get_post_meta( $id, 'ovabrw_define_1_day', true ) : 'day' ;
$price_hour     = get_post_meta( $id, 'ovabrw_regul_price_hour', true );
$price_day      = get_post_meta( $id, '_regular_price', true );

// Get price
$petime_price   = get_post_meta( $id, 'ovabrw_petime_price', true );
$price_location = get_post_meta( $id, 'ovabrw_price_location', true );

$min = $max = 0;
if ( $rental_type == 'period_time' && $petime_price && is_array( $petime_price ) ) {
    $min = min( $petime_price );
    $max = max( $petime_price );
}

if ( $rental_type == 'transportation' && $price_location && is_array( $price_location ) ) {
    $min = min( $price_location );
    $max = max( $price_location );
}

$pickup_date    = isset( $_GET['ovabrw_pickup_date'] ) 	? sanitize_text_field( $_GET['ovabrw_pickup_date'] ) 	: '';
$pickoff_date   = isset( $_GET['ovabrw_pickoff_date'] ) ? sanitize_text_field( $_GET['ovabrw_pickoff_date'] ) 	: '';
$pickup_loc     = isset( $_GET['ovabrw_pickup_loc'] ) 	? sanitize_text_field( $_GET['ovabrw_pickup_loc'] ) 	: '';
$pickoff_loc 	= isset( $_GET['ovabrw_pickoff_loc'] ) 	? sanitize_text_field( $_GET['ovabrw_pickoff_loc'] ) 	: '';

$show_price_table = ovabrw_get_setting( get_option( 'ova_brw_template_show_table_price', 'yes' ) );

?>
<div class="top-right">
	<div class="entox-product-price">
		<?php if ( $rental_type == 'day' ): ?>
			<span class="product-amount"><?php echo wc_price( $price_day ); ?></span>
			<?php if ( $define_1_day == 'hotel' ): ?>
				<span class="define_1_day"><?php esc_html_e( '/pernight', 'entox' ); ?></span>
			<?php else: ?>
				<span class="define_1_day"><?php esc_html_e( '/perday', 'entox' ); ?></span>
			<?php endif; ?>
		<?php elseif ( $rental_type == 'hour' ): ?>
			<span class="product-amount"><?php echo wc_price( $price_hour ); ?></span>
			<span class="define_1_day"><?php esc_html_e( '/perhour', 'entox' ); ?></span>
		<?php elseif ( $rental_type == 'mixed' ): ?>
			<div class="mixed-hour">
				<span class="product-amount"><?php echo wc_price( $price_hour ); ?></span>
				<span class="define_1_day"><?php esc_html_e( '/perhour', 'entox' ); ?></span>
			</div>
			<div class="mixed-day">
				<span class="product-amount"><?php echo wc_price( $price_day ); ?></span>
				<span class="define_1_day"><?php esc_html_e( '/perday', 'entox' ); ?></span>
			</div>
		<?php elseif ( $rental_type == 'period_time' || $rental_type == 'transportation' ): ?>
			<?php if ( $min && $max && $min == $max ): ?>
	            <span class="product-amount"><?php printf( __( '%s', 'entox' ), wc_price( $min ) ); ?></span>
	        <?php elseif ( $min && $max ): ?>
	            <span class="product-amount"><?php printf( __( '%s - %s', 'entox' ), wc_price( $min ), wc_price( $max ) ); ?></span>
	        <?php else: ?>
	            <span class="product-amount"><?php esc_html_e( 'No price', 'entox' ); ?></span>
	        <?php endif; ?>
	    <?php elseif( ( $rental_type == 'day' || $rental_type == 'hour' || $rental_type == 'mixed' ) 
	    				&& $pickup_date != '' && $pickoff_date != '' && apply_filters( 'ova_brw_finish_price_search', true ) ): 
				$total_price 	=  get_price_by_date( $id, strtotime( $pickup_date ), strtotime( $pickoff_date )); 
	    		$new_input_date = ovabrw_new_input_date( $id, strtotime( $pickup_date ), strtotime( $pickoff_date ) );
	    		$real_quantity 	= get_real_quantity( 1, $id, $new_input_date['pickup_date_new'], $new_input_date['pickoff_date_new'] ); ?>
	    	<span class="product-amount"><?php echo wc_price( $total_price['line_total'] ); ?></span>
	        <span class="define_1_day"><?php echo esc_html__( '/', 'entox' ) . $real_quantity; ?> </span>
	    <?php else: ?>
	    	<span class="product-amount"><?php esc_html_e( 'No price', 'entox' ); ?></span>
		<?php endif; ?>
	</div>
	<?php if ( 'yes' === $show_price_table ): ?>
		<div class="special-time">
			<?php if ( $has_special_offers ): ?>
				<div class="special-offers">
					<span class="discount-icon">
						<i class="flaticon-discount"></i>
					</span>
					<span class="open-special-offers"><?php esc_html_e( 'Special Offers', 'entox' ); ?></span>
					<?php do_action( 'entox_wc_template_single_special_price'); ?>
				</div>
			<?php endif; ?>
			<?php if ( ( $rental_type == 'day' || $rental_type == 'mixed' ) && $has_daily_price ): ?>
				<div class="weekdays">
					<span class="discount-icon">
						<i class="flaticon-event"></i>
					</span>
					<span class="open-weekdays-offers"><?php esc_html_e( 'Weekdays', 'entox' ); ?></span>
					<?php do_action( 'entox_wc_template_single_weekdays_price'); ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>