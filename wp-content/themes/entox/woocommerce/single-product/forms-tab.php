<?php defined( 'ABSPATH' ) || exit;

global $product;

$id = $product->get_id();

if ( $product->get_type() !== 'ovabrw_car_rental' ) return;

$show_booking 			= ovabrw_get_setting( get_option( 'ova_brw_template_show_booking_form', 'yes' ) );
$show_request_booking 	= ovabrw_get_setting( get_option( 'ova_brw_template_show_request_booking', 'yes' ) );

?>
<?php if ( 'yes' === $show_booking && 'yes' === $show_request_booking ): ?>
	<div class="forms-tab" id="ova-purchase">
		<ul class="tabs">
			<li class="item booking active" data-form="ovabrw_booking_form"><?php echo esc_html__( 'Booking', 'entox' ); ?></li>
			<li class="item request-booking" data-form="request_booking"><?php echo esc_html__( 'Request Booking', 'entox' ); ?></li>
		</ul>
		<?php do_action( 'entox_wc_template_product_booking_form' ); ?>
		<?php do_action( 'entox_wc_template_product_request_booking' ); ?>
	</div>
<?php elseif ( 'yes' === $show_booking && 'yes' != $show_request_booking ): ?>
	<div class="forms-tab just-booking" id="ova-purchase">
		<ul class="tabs">
			<li class="item booking active" data-form="ovabrw_booking_form"><?php echo esc_html__( 'Booking', 'entox' ); ?></li>
		</ul>
		<?php do_action( 'entox_wc_template_product_booking_form' ); ?>
	</div>
<?php elseif ( 'yes' != $show_booking && 'yes' === $show_request_booking ): ?>
	<div class="forms-tab just-request-booking" id="ova-purchase">
		<ul class="tabs">
			<li class="item request-booking active" data-form="request_booking"><?php echo esc_html__( 'Request Booking', 'entox' ); ?></li>
		</ul>
		<?php do_action( 'entox_wc_template_product_request_booking' ); ?>
	</div>
<?php endif; ?>