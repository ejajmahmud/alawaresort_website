<?php

if ( ! defined( 'ABSPATH' ) ) exit();

global $product;

$id = $product->get_id();

if ( $product->get_type() !== 'ovabrw_car_rental' ) return;

$ovabrw_rental_type 	= get_post_meta( $id, 'ovabrw_price_type', true ); 
$defined_one_day 		= defined_one_day( $id );
$class_no_time_picker 	= ( $defined_one_day == 'hotel' ) ? 'no_time_picker' : '';

$time_to_book_start 	= ovabrw_time_to_book( $id, 'start' );
$time_to_book_end 		= ovabrw_time_to_book( $id, 'end' );

$default_hour_start 	= ovabrw_get_default_time( $id, 'start' );
$default_hour_end 		= ovabrw_get_default_time( $id, 'end' );

$timepicker_start 		= ovabrw_timepicker_product( $id, 'start' );
$timepicker_end 		= ovabrw_timepicker_product( $id, 'end' );

$show_pickup_location 	= ovabrw_show_pick_location_product( $id, $type = 'pickup' );
$show_pickoff_location 	= ovabrw_show_pick_location_product( $id, $type = 'dropoff' );

$show_pickup_date 		= ovabrw_show_pick_date_product( $id, $type = 'pickup' );
$show_pickoff_date 		= ovabrw_show_pick_date_product( $id, $type = 'dropoff' );

$show_number_vehicle 	= ovabrw_show_number_vehicle( $id );

// Get booked time
$statuses 	= brw_list_order_status();
$order_time = get_order_rent_time( $id, $statuses );
$dateformat = ovabrw_get_date_format();

$class_date_picker_period 	= ($ovabrw_rental_type == 'period_time') ? 'no_time_picker' : '';
$ovabrw_unfixed_time 		= get_post_meta( $id, 'ovabrw_unfixed_time', true );
$startdate_perido_time 		= '';

if ( ( $ovabrw_rental_type == 'period_time' ) ) {
	$startdate_perido_time = 'startdate_perido_time';

	if ( $ovabrw_unfixed_time == 'yes' ) {
		$class_date_picker_period = '';
	}
}

// Get Date, Location from URl
$choose_hour_pickup 	= ( $class_date_picker_period == 'no_time_picker' || $class_no_time_picker == 'no_time_picker' ) ? 'no' : 'yes';
$choose_hour_dropoff 	= ( $class_no_time_picker == 'no_time_picker' ) ? 'no' : 'yes';

$pickup_date 			= ovabrw_get_current_date_from_search( $choose_hour_pickup, 'pickup_date', $id );
$dropoff_date 			= ovabrw_get_current_date_from_search( $choose_hour_dropoff, 'dropoff_date', $id );

$pickup_loc 			= isset( $_GET['pickup_loc'] ) ? $_GET['pickup_loc'] : '';
$pickoff_loc 			= isset( $_GET['pickoff_loc'] ) ? $_GET['pickoff_loc'] : '';

// Get first day in week
$first_day = get_option( 'ova_brw_calendar_first_day', '0' );

if ( empty( $first_day ) ) {
	$first_day = 0;
}

// Get categories
$terms = wp_get_post_terms( $id,'product_cat',array('fields'=>'ids') );

if ( $terms && is_array( $terms ) ) {
	$term_id = reset($terms);
}

$label_pickup_location 	= esc_html__( 'Pick-up Location', 'entox');
$label_dropoff_location = esc_html__( 'Drop-off Location', 'entox');

// Get label pick-up and pick-off date
$label_pickup_date 			= esc_html__( 'Pick-up Date', 'entox');
$setting_lable_pickup_date 	= get_post_meta( $id, 'ovabrw_label_pickup_date_product', true );

if ( $setting_lable_pickup_date == 'new' ) {
	$label_pickup_date = get_post_meta( $id, 'ovabrw_new_pickup_date_product', true );
} elseif ( $setting_lable_pickup_date == 'category' ) {
	$label_pickup_date 	= isset( $term_id ) ? get_term_meta( $term_id, 'ovabrw_lable_pickup_date', true ) : esc_html__( 'Pick-up Date', 'entox');
} else {
	$label_pickup_date = esc_html__( 'Pick-up Date', 'entox');
}

$label_dropoff_date 		= esc_html__( 'Drop-off Date', 'entox');
$setting_lable_dropoff_date = get_post_meta( $id, 'ovabrw_label_dropoff_date_product', true );

if ( $setting_lable_dropoff_date == 'new' ) {
	$label_dropoff_date = get_post_meta( $id, 'ovabrw_new_dropoff_date_product', true );
} elseif ( $setting_lable_dropoff_date == 'category' ) {
	$label_dropoff_date = isset( $term_id ) ? get_term_meta( $term_id, 'ovabrw_lable_dropoff_date', true ) : esc_html__( 'Drop-off Date', 'entox');
} else {
	$label_dropoff_date = esc_html__( 'Drop-off Date', 'entox');
}

if ( $label_pickup_date == '' ) {
	$label_pickup_date = esc_html__( 'Pick-up Date', 'entox');
}
if ( $label_dropoff_date == '' ) {
	$label_dropoff_date = esc_html__( 'Drop-off Date', 'entox');
}

?>

<?php if ( $show_pickup_location ): ?>
	<div class="rental_item">
		<div class="error_item">
			<label><?php esc_html_e( 'This field is required', 'entox' ) ?></label>
		</div>
		<?php
			if( $ovabrw_rental_type !== 'transportation' ) {
				echo ovabrw_get_locations_html( 'ovabrw_pickup_loc', 'required', $pickup_loc, $id, 'pickup', $label_pickup_location ); 
			} else {
				echo ovabrw_get_locations_transport_html( 'ovabrw_pickup_loc', 'required', $pickup_loc, $id, 'pickup', $label_pickup_location );
			}
		?>
		<div class="ovabrw-other-location"></div>
	</div>
<?php endif; ?>


<?php if ( $show_pickoff_location ): ?>
	<div class="rental_item">
		<div class="error_item">
			<label><?php esc_html_e( 'This field is required', 'entox' ); ?></label>
		</div>
		<?php
			if( $ovabrw_rental_type !== 'transportation' ) {
				echo ovabrw_get_locations_html( 'ovabrw_pickoff_loc', 'required', $pickoff_loc, $id, 'dropoff', $label_dropoff_location ); 
			} else {
				echo ovabrw_get_locations_transport_html( 'ovabrw_pickoff_loc', 'required', $pickoff_loc, $id, 'dropoff', $label_dropoff_location );
			}
		?>
		<div class="ovabrw-other-location"></div>
	</div>
<?php endif; ?>

<?php if ( $show_pickup_date ): ?>
	<div class="rental_item">
		<div class="error_item">
			<label>
				<?php esc_html_e( 'This field is required', 'entox' ); ?>
			</label>
		</div>
	
		<input 
			type="text" 
			name="ovabrw_pickup_date"  
			default_hour="<?php echo esc_attr( $default_hour_start ); ?>"  
			time_to_book="<?php echo esc_attr( $time_to_book_start ); ?>" 
			class="required ovabrw_datetimepicker ovabrw_start_date <?php echo esc_attr( $class_date_picker_period ); ?> <?php echo esc_attr( $class_no_time_picker ); ?> <?php echo esc_attr($startdate_perido_time); ?>" 
			placeholder="<?php echo esc_attr( $label_pickup_date ); ?>" 
			autocomplete="off" 
			value="<?php echo esc_attr( $pickup_date ); ?>" 
			order_time='<?php echo esc_attr( $order_time ); ?>' 
			data-pid="<?php echo esc_attr( $id ); ?>"
			timepicker='<?php echo esc_attr( $timepicker_start ); ?>' 
			data-firstday='<?php echo esc_attr( $first_day ); ?>' 
			onfocus="blur();" />
		<i class="ovaicon-calendar"></i>
	</div>
<?php endif; ?>

<!-- Check Rental type -->
<?php if ( $ovabrw_rental_type == 'period_time'): 
	$ovabrw_petime_id 		= get_post_meta( $id, 'ovabrw_petime_id', true );
	$ovabrw_petime_label 	= get_post_meta( $id, 'ovabrw_petime_label', true );
?>
	<div class="rental_item">
		<div class="error_item">
			<label>
				<?php esc_html_e( 'This field is required', 'entox' ); ?>
			</label>
		</div>
		<div class="period_package">
			<select name="ovabrw_period_package_id" class="required">
				<option value=""><?php esc_html_e( 'Select Package', 'entox' ); ?></option>
				<?php if ( $ovabrw_petime_id ): ?>
					<?php foreach ( $ovabrw_petime_id as $key => $value ): ?>
						<?php if ( isset( $ovabrw_petime_id[$key] ) && isset( $ovabrw_petime_label[$key] ) ): ?>
							<option value="<?php echo esc_attr(trim( $ovabrw_petime_id[$key] ) ); ?>" > 
								<?php echo esc_html( $ovabrw_petime_label[$key] ); ?> 
							</option>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</div>
	</div>
<?php elseif ($ovabrw_rental_type != 'transportation' ): ?>
	<?php if ( $show_pickoff_date ): ?>
		<div class="rental_item">
			<div class="error_item">
				<label>
					<?php esc_html_e( 'This field is required', 'entox' ); ?>
				</label>
			</div>
			<input 
				type="text" 
				name="ovabrw_pickoff_date" 
				default_hour="<?php echo esc_attr( $default_hour_end ); ?>"  
				time_to_book="<?php echo esc_attr( $time_to_book_end ); ?>"  
				class="required ovabrw_datetimepicker ovabrw_end_date <?php echo esc_attr( $class_no_time_picker ); ?>" 
				placeholder="<?php echo esc_attr( $label_dropoff_date ); ?>"   
				autocomplete="off" 
				value="<?php echo esc_attr( $dropoff_date ); ?>"   
				order_time='<?php echo esc_attr( $order_time ); ?>' 
				timepicker='<?php echo esc_attr( $timepicker_end ); ?>' 
				data-firstday='<?php echo esc_attr( $first_day ); ?>' 
				onfocus="blur();" />
			<i class="ovaicon-calendar"></i>
		</div>
	<?php endif; ?>
<?php endif; ?>

<?php if ( $show_number_vehicle === 'yes' ): 
	$total_number_vehicle = ovabrw_get_total_stock( $id );
?>
	<div class="rental_item">
		<div class="error_item">
			<label><?php esc_html_e( 'This field is required', 'entox' ); ?></label>
		</div>
		<input 
			type="number" 
			class="required" 
			name="ovabrw_number_vehicle" 
			placeholder="<?php esc_attr_e( 'Quantity', 'entox' ); ?>" 
			value="1"
			min="1" 
			max="<?php echo esc_attr( $total_number_vehicle ); ?>" />
	</div>
<?php endif; ?>