<?php 
/**
 * The template for displaying request booking form content within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/request_booking.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

// Get id from do_action - use when insert shortcode
if( isset( $args['id'] ) && $args['id'] ){
	$pid = $args['id'];
}else{
	$pid = get_the_id();
}

// Check product type: rental
$product = wc_get_product( $pid );
if ( $product->get_type() !== 'ovabrw_car_rental' ) return;

$ovabrw_rental_type = get_post_meta( $pid, 'ovabrw_price_type', true );

$show_pickup_location = ovabrw_rq_show_pick_location_product( $pid, $type = 'pickup' );
$show_pickoff_location = ovabrw_rq_show_pick_location_product( $pid, $type = 'dropoff' );

$show_pickup_date = ovabrw_show_rq_pick_date_product( $pid, $type = 'pickup' );
$show_pickoff_date = ovabrw_show_rq_pick_date_product( $pid, $type = 'dropoff' );


$defined_one_day = defined_one_day( $pid );

$class_no_time_picker = '';
if( $defined_one_day == 'hotel' ) {
	$class_no_time_picker = 'no_time_picker';
}
$dateformat = ovabrw_get_date_format();

// Get booked time
$statuses = brw_list_order_status();
$order_time = get_order_rent_time( $pid, $statuses );


$default_hour_start = ovabrw_get_default_time( $pid, 'start' );
$default_hour_end = ovabrw_get_default_time( $pid, 'end' );

$timepicker_start = ovabrw_timepicker_product( $pid, 'start' );
$timepicker_end = ovabrw_timepicker_product( $pid, 'end' );

$time_to_book_start = ovabrw_time_to_book( $pid, 'start' );
$time_to_book_end = ovabrw_time_to_book( $pid, 'end' );

$class_date_picker_period = ($ovabrw_rental_type == 'period_time') ? 'no_time_picker' : '';

$ovabrw_unfixed_time = get_post_meta( $pid, 'ovabrw_unfixed_time', true );

if( ( $ovabrw_rental_type == 'period_time' ) ){
	if( $ovabrw_unfixed_time == 'yes' ) {
		$class_date_picker_period = '';
	} 
	
}


// Get Date, Location from URl
$choose_hour_pickup = ( $class_date_picker_period == 'no_time_picker' || $class_no_time_picker == 'no_time_picker' ) ? 'no' : 'yes';
$choose_hour_dropoff = ( $class_no_time_picker == 'no_time_picker' ) ? 'no' : 'yes';
$pickup_date = ovabrw_get_current_date_from_search( $choose_hour_pickup, 'pickup_date', $pid );
$dropoff_date = ovabrw_get_current_date_from_search( $choose_hour_dropoff, 'dropoff_date', $pid );

$pickup_loc = isset( $_GET['pickup_loc'] ) ? $_GET['pickup_loc'] : '';
$pickoff_loc = isset( $_GET['pickoff_loc'] ) ? $_GET['pickoff_loc'] : '';

?>
<div class="request_booking">

	<h3>
		<?php esc_html_e( 'Send your requirement to us. We will check email and contact you soon.', 'ova-brw' ); ?>
	</h3>

	<form class="form ovabrw-booking" id="request_booking "
	action="<?php echo esc_url( home_url('/') ); ?>" method="post" enctype="multipart/form-data" data-mesg_required="<?php esc_html_e( 'This field is required.', 'ova-brw' ); ?>">

	<div class="ovabrw-container">
		<div class="ovabrw-row">
			<div class="wrap-item four_cloumn">
				<div class="rental_item">
					<label><?php esc_html_e( 'Name', 'ova-brw' ); ?></label>
					<div class="error_item">
						<label><?php esc_html_e( 'This field is required', 'ova-brw' ) ?></label>
					</div>
					<input type="text" class="required" name="name" placeholder="<?php esc_html_e( 'John Doe', 'ova-brw' ); ?> ">	
				</div>
				
				<div class="rental_item">
					<label><?php esc_html_e( 'Email', 'ova-brw' ); ?></label>
					<div class="error_item">
						<label><?php esc_html_e( 'This field is required', 'ova-brw' ) ?></label>
					</div>
					<input type="text" class="required" name="email" placeholder="<?php esc_html_e( 'sample@yourcompany.com', 'ova-brw' ); ?> ">	
				</div>
				
				<?php if( ovabrw_get_setting( get_option('ova_brw_request_booking_form_show_number', 'yes') ) === 'yes' ){ ?>
				<div class="rental_item">
					<label><?php esc_html_e( 'Number', 'ova-brw' ); ?></label>
					<div class="error_item">
						<label><?php esc_html_e( 'This field is required', 'ova-brw' ) ?></label>
					</div>
					<input type="text" class="required" name="number" placeholder="<?php esc_html_e( '+66-4545688', 'ova-brw' ); ?> ">
				</div>
				<?php } ?>

				<?php if( ovabrw_get_setting( get_option('ova_brw_request_booking_form_show_address', 'yes') ) === 'yes' ){ ?>
					<div class="rental_item">
						<label><?php esc_html_e( 'Address', 'ova-brw' ); ?></label>
						<div class="error_item">
						<label><?php esc_html_e( 'This field is required', 'ova-brw' ) ?></label>
					</div>
						<input type="text" class="required" name="address" placeholder="<?php esc_html_e( 'Address', 'ova-brw' ); ?> ">	
					</div>
				<?php } ?>


				<?php
					$show_number_vehicle = ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_number_vehicle', 'no' ) );

					if( $show_number_vehicle === 'yes' ){ ?>
						<div class="rental_item">
							<label><?php esc_html_e( 'Quantity', 'ova-brw' ); ?></label>
							<div class="error_item">
								<label><?php esc_html_e( 'This field is required', 'ova-brw' ) ?></label>
							</div>
							<input type="number" class="required" name="ovabrw_number_vehicle" value="1" min="1" >
						</div>
					<?php }

				?>

				
				<?php if( $show_pickup_location ){ ?>
					<div class="rental_item">
						<label><?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?></label>
						<div class="error_item">
							<label><?php esc_html_e( 'This field is required', 'ova-brw' ) ?></label>
						</div>

						<?php
							if( $ovabrw_rental_type !== 'transportation' ) {
								ovabrw_get_locations_html( $class = 'ovabrw_pickup_loc', $required = 'required', $selected = $pickup_loc, $pid, 'pickup' ); 
							} else {
								ovabrw_get_locations_transport_html( $class = 'ovabrw_pickup_loc', $required = 'required', $selected = $pickup_loc, $pid, 'pickup' );
							}
						?>

						<div class="ovabrw-other-location"></div>
					</div>
				<?php } ?>

				<?php if( $show_pickoff_location ){ ?>

					<div class="rental_item">
						<label><?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?></label>
						<div class="error_item">
							<label><?php esc_html_e( 'This field is required', 'ova-brw' ) ?></label>
						</div>

						<?php
							if( $ovabrw_rental_type !== 'transportation' ) {
								ovabrw_get_locations_html( $class = 'ovabrw_pickoff_loc', $required = 'required', $selected = $pickoff_loc, $pid, 'dropoff' ); 
							} else {
								ovabrw_get_locations_transport_html( $class = 'ovabrw_pickoff_loc', $required = 'required', $selected = $pickoff_loc, $pid, 'dropoff' );
							}
						?>

						<div class="ovabrw-other-location"></div>
					</div>

				<?php  } ?>


				
				
				<?php if( $show_pickup_date ){ ?>
				<div class="rental_item">
					<label><?php esc_html_e( 'Pick-up Date', 'ova-brw' ); ?></label>
					<div class="error_item">
						<label><?php esc_html_e( 'This field is required', 'ova-brw' ) ?></label>
					</div>
					<input 
						type="text" 
						onkeydown="return false" 
						name="pickup_date" 
						default_hour="<?php echo esc_attr( $default_hour_start ); ?>" 
						time_to_book="<?php echo esc_attr( $time_to_book_start ); ?>" 
						class="required ovabrw_start_date ovabrw_datetimepicker <?php echo esc_attr( $class_date_picker_period ); ?> <?php echo esc_attr( $class_no_time_picker ); ?>" 
						placeholder="<?php echo esc_attr( $dateformat ); ?> " 
						autocomplete="off" 
						value="<?php echo esc_attr( $pickup_date ); ?>" 
						onfocus="blur();" 
						order_time='<?php echo esc_attr( $order_time ); ?>'
						timepicker='<?php echo esc_attr( $timepicker_start ); ?>'
					>	
				</div>
				<?php } ?>

				

				<?php if( $ovabrw_rental_type == 'period_time' ){ ?>

					<?php 
						$ovabrw_petime_id = get_post_meta( $pid, 'ovabrw_petime_id', true );
						$ovabrw_petime_label = get_post_meta( $pid, 'ovabrw_petime_label', true );
					?>
					<div class="rental_item">
						<label><?php esc_html_e( 'Choose Package', 'ova-brw' ); ?></label>
						<div class="error_item">
							<label><?php esc_html_e( 'This field is required', 'ova-brw' ) ?></label>
						</div>
						<div class="period_package">
							<select name="ovabrw_period_package_id" class="required">
								<option value=""><?php esc_html_e( 'Select Package', 'ova-brw' ); ?></option>

								<?php if( $ovabrw_petime_id ){ foreach ($ovabrw_petime_id as $key => $value) { ?>
									<option value="<?php echo esc_attr( $ovabrw_petime_label[$key] ); ?>" > <?php echo esc_html( $ovabrw_petime_label[$key] ); ?> </option>
								<?php } } ?>
							</select>
						</div>
					</div>
					
				<?php }else if($ovabrw_rental_type != 'transportation' ){ ?>
					<?php if( $show_pickoff_date ){ ?>
					<div class="rental_item">
						<label><?php esc_html_e( 'Drop-off Date', 'ova-brw' ); ?></label>
						<div class="error_item">
							<label><?php esc_html_e( 'This field is required', 'ova-brw' ) ?></label>
						</div>
						<input 
							type="text" 
							name="pickoff_date" 
							onkeydown="return false" 
							default_hour="<?php echo esc_attr( $default_hour_end ); ?>"  
							time_to_book="<?php echo esc_attr( $time_to_book_end ); ?>"  
							class="required ovabrw_end_date ovabrw_datetimepicker <?php echo esc_attr( $class_no_time_picker ) ?>" 
							placeholder="<?php echo esc_attr( $dateformat ); ?>" 
							autocomplete="off" 
							value="<?php echo esc_attr( $dropoff_date ); ?>" 
							onfocus="blur();" 
							order_time='<?php echo esc_attr( $order_time ); ?>'
							timepicker='<?php echo esc_attr( $timepicker_end ); ?>'
						>	
					</div>
					<?php } ?>

				<?php } ?>
				

				<?php

					$list_ckf_output = [];

					$ovabrw_manage_custom_checkout_field = get_post_meta( $pid, 'ovabrw_manage_custom_checkout_field', true );
					$list_field_checkout = get_option( 'ovabrw_booking_form', array() );

					// Get custom checkout field by Category
					$product_cats = wp_get_post_terms( $pid, 'product_cat' );
					$cat_id = isset( $product_cats[0] ) ? $product_cats[0]->term_id : '';
					$ovabrw_custom_checkout_field = $cat_id ? get_term_meta($cat_id, 'ovabrw_custom_checkout_field', true) : '';

					$ovabrw_choose_custom_checkout_field = $cat_id ? get_term_meta($cat_id, 'ovabrw_choose_custom_checkout_field', true) : '';
					

					if( $ovabrw_manage_custom_checkout_field === 'new' ) {

						$list_field_checkout_in_product = get_post_meta( $pid, 'ovabrw_product_custom_checkout_field', true );
						$list_field_checkout_in_product_arr = explode( ',', $list_field_checkout_in_product );
						$list_field_checkout_in_product_arr = array_map( 'trim', $list_field_checkout_in_product_arr );

						$list_ckf_output = [];
						if( ! empty( $list_field_checkout_in_product_arr ) && is_array( $list_field_checkout_in_product_arr ) ) {
							foreach( $list_field_checkout_in_product_arr as $field_name ) {
								if( array_key_exists( $field_name, $list_field_checkout ) ) {
									$list_ckf_output[$field_name] = $list_field_checkout[$field_name];
								}
							}
						} 
					}else if( $ovabrw_choose_custom_checkout_field == 'all' ){

						$list_ckf_output = $list_field_checkout;

					}else if( $ovabrw_choose_custom_checkout_field == 'special' ){

						if( $ovabrw_custom_checkout_field ){

							foreach( $ovabrw_custom_checkout_field as $field_name ) {
								if( array_key_exists( $field_name, $list_field_checkout ) ) {
									$list_ckf_output[$field_name] = $list_field_checkout[$field_name];
								}
							}
							

						}else{
							$list_ckf_output = [];
						}

					}else {

						$list_ckf_output = $list_field_checkout;
					}



					if( is_array( $list_ckf_output ) && ! empty( $list_ckf_output ) ) {
						foreach( $list_ckf_output as $key => $field ) {

							if( array_key_exists('enabled', $field) &&  $field['enabled'] == 'on' ) {

								if( array_key_exists('required', $field) &&  $field['required'] == 'on' ) {
									$class_required = 'required';
								} else {
									$class_required = '';
								}

						?>
							<div class="rental_item">
								<label><?php echo esc_html( $field['label'] ); ?></label>
								<div class="error_item">
									<label><?php esc_html_e( 'This field is required', 'ova-brw' ) ?></label>
								</div>

								<?php if( $field['type'] !== 'textarea' && $field['type'] !== 'select' ) { ?>
									<input type="<?php echo esc_attr( $field['type'] ); ?>"
									name="<?php echo esc_attr( $key ) ?>"
									class="<?php echo esc_attr( $field['class'].' '.$class_required ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
									value="<?php echo esc_attr( $field['default'] ); ?>"  />
								<?php } ?>

								<?php if( $field['type'] === 'textarea' ) { ?>
									<textarea name="<?php echo esc_attr( $key ); ?>"
										class="<?php echo esc_attr( $field['class'].' '.$class_required ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
										value="<?php echo esc_attr( $field['default'] ); ?>" cols="10" rows="5"></textarea>
								<?php } ?>

								<?php if( $field['type'] === 'select' ) { 

									$ova_options_key = $ova_options_text = [];
									if( array_key_exists( 'ova_options_key', $field ) ) {
										$ova_options_key = $field['ova_options_key'];
									}

									if( array_key_exists( 'ova_options_text', $field ) ) {
										$ova_options_text = $field['ova_options_text'];
									}
									

									?>
									<select name="<?php echo esc_attr( $key ); ?>"
										class="<?php echo esc_attr( $field['class'].' '.$class_required ); ?>" >
										<?php 
										if( ! empty( $ova_options_text ) && is_array( $ova_options_text ) ) { 
											foreach( $ova_options_text as $key => $value ) { 
												$selected = '';
												if( $ova_options_key[$key] == $field['default'] ) {
													$selected = 'selected';
												}
												?>
												<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $ova_options_key[$key] ); ?>">
													<?php echo esc_html( $value ); ?>
												</option>
										<?php 

											} //end foreach
										}//end if
									?>
										
									</select>
								<?php } ?>
							</div>
						<?php
							}//endif
						}//end foreach
					}//end if

				?>





			</div>
		</div>
	</div>


	<?php if( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_extra_service', 'yes' ) ) === 'yes' ){ ?>
		<?php $ovabrw_resource_name = get_post_meta( $pid, 'ovabrw_resource_name', true ); 
			if( $ovabrw_resource_name ){

				$ovabrw_resource_price = get_post_meta( $pid, 'ovabrw_resource_price', true ); 
				$ovabrw_resource_duration_val = get_post_meta( $pid, 'ovabrw_resource_duration_val', true ); 
				$ovabrw_resource_duration_type = get_post_meta( $pid, 'ovabrw_resource_duration_type', true ); 
				$ovabrw_resource_id = get_post_meta( $pid, 'ovabrw_resource_id', true ); 
		?>
		<div class="ovabrw_extra_service">
			<label><?php esc_html_e( 'Extra Service', 'ova-brw' ); ?></label>

			<div class="row-fluid ovabrw_resource"><div class="row">
				<?php foreach ($ovabrw_resource_name as $key => $value) { ?>
					<div class="item">
						
							<div class="left">
								<?php $ovabrw_resource_key = $ovabrw_resource_id[$key]; ?>
								<input type="checkbox" id="ovabrw_resource_checkboxs_<?php echo esc_attr($key); ?>" name="ovabrw_resource_checkboxs[]" value="<?php echo esc_attr( $ovabrw_resource_name[$key] );  ?>" >
								<label for="ovabrw_resource_checkboxs_<?php echo esc_attr($key); ?>"><?php echo esc_html( $ovabrw_resource_name[$key] ); ?></label>
							</div>
							<div class="right">
								<div class="resource">
									<span class="dur_price"><?php echo wp_kses_post( wc_price( $ovabrw_resource_price[$key] ) ); ?></span>
									<span class="slash">/</span>
									<?php if( isset( $ovabrw_resource_duration_val[$key] ) ) : ?>
									<span class="dur_val"><?php echo esc_html( $ovabrw_resource_duration_val[$key] ) ?></span>
									<?php endif ?>
									<span class="dur_type">
										<?php
											if( $ovabrw_resource_duration_type[$key] == 'hours' ){
												esc_html_e( 'Hour(s)', 'ova-brw' );
											}else if( $ovabrw_resource_duration_type[$key] == 'days' ){
												esc_html_e( 'Day(s)', 'ova-brw' );
											}if( $ovabrw_resource_duration_type[$key] == 'total' ){
												esc_html_e( 'Total', 'ova-brw' );
											}
										?>
									</span>
								</div>
							</div>

					
					</div>
				<?php } ?>
				
			</div></div>
		</div>

	<?php } } ?>

	<?php
		if( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_service', 'yes' ) ) === 'yes' ){ 
			$ovabrw_label_service = get_post_meta( $pid, 'ovabrw_label_service', true );

			$ovabrw_service_id = get_post_meta( $pid, 'ovabrw_service_id', true );
			$ovabrw_service_name = get_post_meta( $pid, 'ovabrw_service_name', true );
			$ovabrw_service_price = get_post_meta( $pid, 'ovabrw_service_price', true );
			$ovabrw_service_duration_type = get_post_meta( $pid, 'ovabrw_service_duration_type', true );
			?>
			<?php
				if( $ovabrw_label_service ) {
			?>
			<div class="ovabrw_service_wrap">
				<label><?php esc_html_e( 'Service', 'ova-brw' ); ?></label>
				<div class="row ovabrw_service">
					<?php
							for( $i = 0; $i < count( $ovabrw_label_service ); $i++ ){
								$label_group_service = $ovabrw_label_service[$i];
								?>
								<div class="ovabrw_service_select">
									<select name="ovabrw_service[]" id="">
										<option value=""><?php printf( esc_html__( 'Select %s', 'ova-brw' ), esc_html( $label_group_service ) );  ?></option>
										<?php
										if( isset( $ovabrw_service_id[$i] ) && is_array( $ovabrw_service_id[$i] ) && ! empty( $ovabrw_service_id ) ){
											foreach( $ovabrw_service_id[$i] as $key => $val ) {
												?>
													<option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $ovabrw_service_name[$i][$key] ) ?></option>
												<?php
											}
										}
										?>
									</select>
								</div>
								<?php
							}
							?>

				</div>
			</div>
			<?php
			}
		}
	?>



	<?php if( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_extra_info', 'yes' ) ) === 'yes' ){ ?>
		<div class="extra">
			<textarea name="extra" cols="50" rows="5" placeholder="<?php esc_html_e( 'Extra Information', 'ova-brw' ); ?>"></textarea>
		</div>
	<?php } ?>


	<input type="hidden" name="product_name" value="<?php echo esc_attr( get_the_title() ); ?>" />
	<input type="hidden" name="product_id" value="<?php echo esc_attr( $pid ); ?>" />

	<input type="hidden" name="request_booking" value="request_booking" />

	<button type="submit" class="submit btn_tran"><?php esc_html_e( 'Send', 'ova-brw' ); ?> </button>

	</form>
</div>