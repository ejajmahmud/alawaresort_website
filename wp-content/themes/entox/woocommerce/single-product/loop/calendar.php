<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;

$id = $product->get_id();

if ( $product->get_type() !== 'ovabrw_car_rental' ) return;

// get background color calendar in settings
$background_color_calendar = ovabrw_get_setting( get_option( 'ova_brw_bg_calendar', '#c4c4c4' ) );

// get unavailable date for booking in settings
$disable_week_day 		= get_option( 'ova_brw_calendar_disable_week_day', '' );
$data_disable_week_day 	= $disable_week_day != '' ? json_encode( explode( ',', $disable_week_day ) ) : '';
$price_type 			= get_post_meta( $id, 'ovabrw_price_type', true );
$order_time_calendar 	= ovabrw_create_order_time_calendar( $id );

$toolbar_nav[] 	= ovabrw_get_setting( get_option( 'ova_brw_calendar_show_nav_month', 'yes' ) ) == 'yes' ? 'dayGridMonth' : '';
$toolbar_nav[] 	= ovabrw_get_setting( get_option( 'ova_brw_calendar_show_nav_week', 'yes' ) ) == 'yes' ? 'timeGridWeek' : '';
$toolbar_nav[] 	= ovabrw_get_setting( get_option( 'ova_brw_calendar_show_nav_day', 'yes' ) ) == 'yes' ? 'timeGridDay' : '';
$toolbar_nav[] 	= ovabrw_get_setting( get_option( 'ova_brw_calendar_show_nav_list', 'yes' ) ) == 'yes' ? 'listWeek' : '';
$show_time 		= ovabrw_get_setting( get_option( 'ova_brw_template_show_time_in_calendar', 'yes' ) ) == 'yes' ? '' : 'ova-hide-time-calendar';

$nav 			=  implode(',', array_filter( $toolbar_nav ) );
$language 		= ovabrw_get_setting( get_option( 'ova_brw_calendar_language_general', 'en' ) );

$lang = apply_filters( 'wpml_current_language', NULL );

if ( $lang ) {
    $language = $lang;
}

$default_view 	= ovabrw_get_setting( get_option( 'ova_brw_calendar_default_view', 'month' ) );
$event_display 	= apply_filters( 'ova_ft_calendar_event_display', 'none' );

// Get first day in week
$first_day 			= ovabrw_get_setting( get_option( 'ova_brw_calendar_first_day', '0' ) );
$manage_store 		= get_post_meta( $id, 'ovabrw_manage_store', true );
$total_car_store 	= (int)get_post_meta( $id, 'ovabrw_car_count', true );
$number_limit 		= ( $manage_store === 'store' ) ? $total_car_store : 1;
$define_day 		= defined_one_day( $id );
$data_define_day 	= ( $define_day ) ? 'data-define_day='.$define_day : '';
$default_hour_start = ovabrw_get_default_time( $id, 'start' );
$time_to_book_start = ovabrw_time_to_book( $id, 'start' );
$special_time 		= ovabrw_get_special_time( $id, $price_type );
$data_special_time 	= json_encode( $special_time );

$show_calendar 		= ovabrw_get_setting( get_option( 'ova_brw_template_show_calendar', 'yes' ) );

?>
<?php if ( 'yes' === $show_calendar ): ?>
	<div class="entox-calendar-wrapper">
		<div class="entox-calendar">
			<div <?php echo esc_attr( $data_define_day ) ?> 
				class="product-calendar <?php echo esc_attr( $show_time ) ?>" 
				data-number_limit="<?php echo esc_attr( $number_limit ) ?>" 
				data-lang="<?php echo esc_attr($language) ?>" 
				data-nav="<?php echo esc_attr( $nav ); ?>" 
				data-event-display="<?php echo esc_attr( $event_display ); ?>" 
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
				data_event_number="<?php echo apply_filters( 'ovabrw_event_number_cell', 2 ); ?>" >
			</div>
		</div>
		<ul class="entox-calendar-tutorial">
			<li class="today">
				<span class="color"></span>
				<span class="label"><?php esc_html_e( 'Today', 'entox' ); ?></span>
			</li>
			<li class="available">
				<span class="color"></span>
				<span class="label"><?php esc_html_e( 'Available', 'entox' ); ?></span>
			</li>
			<li class="unavailable">
				<span class="color" style="background-color: <?php echo esc_attr( $background_color_calendar ); ?>"></span>
				<span class="label"><?php esc_html_e( 'Unavailable', 'entox' ); ?></span>
			</li>
		</ul>
	</div>
	<div class="entox-line"></div>
<?php endif; ?>
