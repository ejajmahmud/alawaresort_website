<?php 
/**
 * The template for displaying calendar content within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/calendar.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

	// Get product_id from do_action - use when insert shortcode
	if( isset( $args['id'] ) && $args['id'] ){
		$pid = $args['id'];
	}else{
		$pid = get_the_id();
	}

	// Check product type: rental
    $product = wc_get_product( $pid );
    if ( $product->get_type() !== 'ovabrw_car_rental' ) return;
    
	// get background color calendar in settings
	$background_color_calendar = ovabrw_get_setting( get_option( 'ova_brw_bg_calendar', '#f70707' ) );

	// get unavailable date for booking in settings
	$disable_week_day = get_option( 'ova_brw_calendar_disable_week_day', '' );
	
	$data_disable_week_day = $disable_week_day != '' ? json_encode( explode( ',', $disable_week_day ) ) : '';

	$price_type = get_post_meta( $pid, 'ovabrw_price_type', true );
	
	$order_time_calendar = ovabrw_create_order_time_calendar( $pid );

	$toolbar_nav[] = ovabrw_get_setting( get_option( 'ova_brw_calendar_show_nav_month', 'yes' ) ) == 'yes' ? 'dayGridMonth' : '';
	$toolbar_nav[] = ovabrw_get_setting( get_option( 'ova_brw_calendar_show_nav_week', 'yes' ) ) == 'yes' ? 'timeGridWeek' : '';
	$toolbar_nav[] = ovabrw_get_setting( get_option( 'ova_brw_calendar_show_nav_day', 'yes' ) ) == 'yes' ? 'timeGridDay' : '';
	$toolbar_nav[] = ovabrw_get_setting( get_option( 'ova_brw_calendar_show_nav_list', 'yes' ) ) == 'yes' ? 'listWeek' : '';
	$show_time = ovabrw_get_setting( get_option( 'ova_brw_template_show_time_in_calendar', 'yes' ) ) == 'yes' ? '' : 'ova-hide-time-calendar';

	$nav =  implode(',', array_filter( $toolbar_nav ) );

	$language = ovabrw_get_setting( get_option( 'ova_brw_calendar_language_general', 'en' ) );

	$default_view = ovabrw_get_setting( get_option( 'ova_brw_calendar_default_view', 'month' ) );

	// Get first day in week
	$first_day = ovabrw_get_setting( get_option( 'ova_brw_calendar_first_day', '0' ) );

	$manage_store = get_post_meta( $pid, 'ovabrw_manage_store', true );
	$total_car_store = (int)get_post_meta( $pid, 'ovabrw_car_count', true );

	$number_limit = ( $manage_store === 'store' ) ? $total_car_store : 1;

	$define_day = defined_one_day( $pid );
	$data_define_day = ( $define_day ) ? 'data-define_day='.$define_day : '';

	$default_hour_start = ovabrw_get_default_time( $pid, 'start' );

	$time_to_book_start = ovabrw_time_to_book( $pid, 'start' );

	$special_time = ovabrw_get_special_time( $pid, $price_type );
	$data_special_time = json_encode( $special_time );

	?>
	
	<div class="wrap_calendar">
		<div <?php echo esc_attr( $data_define_day ); ?> 
			class="ovabrw__product_calendar <?php echo esc_attr( $show_time ); ?>" 
			data-number_limit="<?php echo esc_attr( $number_limit ); ?>" 
			data-lang="<?php echo esc_attr($language); ?>" 
			data-nav="<?php echo esc_attr( $nav ); ?>" 
			data-default_view="<?php echo esc_attr( $default_view ); ?>" 
			data-first-day="<?php echo esc_attr( $first_day ); ?>" 
			data-special-time='<?php echo esc_attr($data_special_time); ?>' 
			data-background-day='<?php echo esc_attr($background_color_calendar); ?>' 
			data-disable_week_day='<?php echo esc_attr($data_disable_week_day); ?>' 
			default_hour_start="<?php echo esc_attr( $default_hour_start ); ?>"  
			time_to_book_start="<?php echo esc_attr( $time_to_book_start ); ?>" 
			price_calendar='<?php echo esc_attr( $order_time_calendar['price_calendar'] ); ?>' 
			type_price='<?php echo esc_attr($price_type); ?>' 
			order_time='<?php echo esc_attr( $order_time_calendar['order_time'] ); ?>'
			data_event_number="<?php echo esc_attr( apply_filters( 'ovabrw_event_number_cell', 2 ) ); ?>"
			
		>
		</div>
	</div>

	<ul class="intruction_calendar">
		<li>
			<span class="pink"></span>
			<span class="white"></span>
			<span><?php esc_html_e( 'Available','ova-brw' ) ?></span>		
		</li>

		<?php if( $define_day == 'hotel' ){ ?>
			<li>
				<span class="maybe"></span>
				<span><?php esc_html_e( 'Maybe available', 'ova-brw' ); ?></span>
			</li>
		<?php } ?>
		
		<li>
			<span class="yellow" style="background: <?php echo esc_attr( $background_color_calendar ) ?>; border-color: <?php echo esc_attr( $background_color_calendar ) ?>; " ></span>

		<?php if( $price_type == 'day' || $price_type == 'mixed' || $price_type == 'transportation' ) {  ?>
				
				<span><?php esc_html_e( 'Unavailable', 'ova-brw' ) ?></span>

		<?php } else if( $price_type == 'hour' || $price_type == 'period_time' ) { ?>

			<span><?php esc_html_e( 'Unavailable','ova-brw' ); ?></span>	

		<?php } ?>
		
		</li>
		

	</ul>

