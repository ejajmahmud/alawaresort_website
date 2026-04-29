<?php defined( 'ABSPATH' ) || exit;

$id = get_the_id();

$product = wc_get_product( $id );
if ( $product->get_type() !== 'ovabrw_car_rental' ) return;

$ovabrw_rental_type = get_post_meta( $id, 'ovabrw_price_type', true );
$defined_one_day 	= defined_one_day( $id );

?>

<div class="ovabrw_booking_form ovabrw-booking entox-booking" id="ovabrw_booking_form">
	<form class="form booking_form" id="booking_form" action="<?php home_url('/'); ?>" method="post" enctype="multipart/form-data" data-mesg_required="<?php esc_html_e( 'This field is required.', 'entox' ); ?>">
		<div class="ovabrw-container wrap_fields">
			<div class="ovabrw-row">
				<div class="wrap-item">
					<?php
					/**
					 * Hook: entox_wc_booking_form
					 * @hooked: entox_wc_booking_form_fields - 5
					 * @hooked: entox_wc_booking_form_extra_fields - 10
					 * @hooked: ovabrw_booking_form_resource - 15
					 * @hooked: ovabrw_booking_form_services - 20
					 * @hooked: ovabrw_booking_form_deposit - 25
					 * @hooked: ovabrw_booking_form_ajax_total - 30
					 */
						do_action( 'entox_wc_booking_form' );
					?>
				</div>
			</div>
		</div>
		<button type="submit" class="submit btn_tran">
			<?php esc_html_e( 'Book Now', 'entox' ); ?>
		</button>
		<input type="hidden" name="ovabrw_rental_type" value="<?php echo esc_attr( $ovabrw_rental_type ); ?>" />
		<input type="hidden" name="car_id" value="<?php echo esc_attr( $id ); ?>" />
		<input type="hidden" name="defined_one_day" value="<?php echo esc_attr( $defined_one_day ); ?>" />
		<input type="hidden" name="custom_product_type" value="ovabrw_car_rental" />
		<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $id ); ?>" />
		<input type="hidden" name="quantity" value="1" />
	</form>
</div>



