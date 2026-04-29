<?php defined( 'ABSPATH' ) || exit;

$id = get_the_id();

$product = wc_get_product( $id );
if ( $product->get_type() !== 'ovabrw_car_rental' ) return;

// Check product type: rental
$product = wc_get_product( $id );
if ( $product->get_type() !== 'ovabrw_car_rental' ) return;

$ovabrw_rental_type 	= get_post_meta( $id, 'ovabrw_price_type', true );

$show_pickup_location 	= ovabrw_rq_show_pick_location_product( $id, $type = 'pickup' );
$show_pickoff_location 	= ovabrw_rq_show_pick_location_product( $id, $type = 'dropoff' );

$show_pickup_date 		= ovabrw_show_rq_pick_date_product( $id, $type = 'pickup' );
$show_pickoff_date 		= ovabrw_show_rq_pick_date_product( $id, $type = 'dropoff' );

$defined_one_day 		= defined_one_day( $id );

$class_no_time_picker = '';
if( $defined_one_day == 'hotel' ) {
	$class_no_time_picker = 'no_time_picker';
}

$dateformat = ovabrw_get_date_format();

// Get booked time
$statuses 	= brw_list_order_status();
$order_time = get_order_rent_time( $id, $statuses );

$default_hour_start = ovabrw_get_default_time( $id, 'start' );
$default_hour_end 	= ovabrw_get_default_time( $id, 'end' );

$timepicker_start 	= ovabrw_timepicker_product( $id, 'start' );
$timepicker_end 	= ovabrw_timepicker_product( $id, 'end' );

$time_to_book_start = ovabrw_time_to_book( $id, 'start' );
$time_to_book_end 	= ovabrw_time_to_book( $id, 'end' );

$class_date_picker_period = ($ovabrw_rental_type == 'period_time') ? 'no_time_picker' : '';

$ovabrw_unfixed_time = get_post_meta( $id, 'ovabrw_unfixed_time', true );

if ( ( $ovabrw_rental_type == 'period_time' ) ) {
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

$show_number_vehicle 	= ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_number_vehicle', 'no' ) );

?>

<div class="request_booking entox-booking">
	<form 
		class="form ovabrw-booking" 
		id="request_booking" 
		action="<?php echo esc_url(home_url('/')); ?>" 
		method="post" 
		enctype="multipart/form-data" 
		data-mesg_required="<?php esc_html_e( 'This field is required.', 'entox' ); ?>">
		<div class="ovabrw-container wrap_fields">
			<div class="ovabrw-row">
				<div class="wrap-item">
					<div class="rental_item">
						<div class="error_item">
							<label><?php esc_html_e( 'This field is required', 'entox' ); ?></label>
						</div>
						<input type="text" class="required" name="name" placeholder="<?php esc_html_e( 'Name', 'entox' ); ?>" />	
					</div>
					
					<div class="rental_item">
						<div class="error_item">
							<label><?php esc_html_e( 'This field is required', 'entox' ); ?></label>
						</div>
						<input type="text" class="required" name="email" placeholder="<?php esc_html_e( 'Email', 'entox' ); ?>" />	
					</div>
					
					<?php if ( ovabrw_get_setting( get_option('ova_brw_request_booking_form_show_number', 'yes') ) === 'yes' ): ?>
						<div class="rental_item">
							<div class="error_item">
								<label><?php esc_html_e( 'This field is required', 'entox' ); ?></label>
							</div>
							<input type="text" class="required" name="number" placeholder="<?php esc_html_e( 'Phone', 'entox' ); ?>" />
						</div>
					<?php endif; ?>

					<?php if ( ovabrw_get_setting( get_option('ova_brw_request_booking_form_show_address', 'yes') ) === 'yes' ): ?>
						<div class="rental_item">
							<div class="error_item">
							<label><?php esc_html_e( 'This field is required', 'entox' ); ?></label>
						</div>
							<input type="text" class="required" name="address" placeholder="<?php esc_html_e( 'Address', 'entox' ); ?>" />	
						</div>
					<?php endif; ?>

					<?php if ( $show_number_vehicle === 'yes' ): ?>
						<div class="rental_item">
							<div class="error_item">
								<label><?php esc_html_e( 'This field is required', 'entox' ); ?></label>
							</div>
							<input 
								type="number" 
								class="required" 
								placeholder="<?php esc_html_e( 'Quantity', 'entox' ); ?>" 
								name="ovabrw_number_vehicle" 
								value="1" 
								min="1" />
						</div>
					<?php endif; ?>

					<?php if ( $show_pickup_location ): 
						$label_pickup_location = esc_html__( 'Pick-up Location', 'entox' );
					?>
						<div class="rental_item">
							<div class="error_item">
								<label><?php esc_html_e( 'This field is required', 'entox' ); ?></label>
							</div>
							<?php
								if ( $ovabrw_rental_type !== 'transportation' ) {
									echo ovabrw_get_locations_html( 'ovabrw_pickup_loc', 'required', $pickup_loc, $id, 'pickup', $label_pickup_location ); 
								} else {
									echo ovabrw_get_locations_transport_html( 'ovabrw_pickup_loc', 'required', $pickup_loc, $id, 'pickup', $label_pickup_location );
								}
							?>
							<div class="ovabrw-other-location"></div>
						</div>
					<?php endif; ?>

					<?php if ( $show_pickoff_location ): 
						$label_dropoff_location = esc_html__( 'Drop-off Location', 'entox' );
					?>
						<div class="rental_item">
							<div class="error_item">
								<label><?php esc_html_e( 'This field is required', 'entox' ); ?></label>
							</div>
							<?php
								if ( $ovabrw_rental_type !== 'transportation' ) {
									echo ovabrw_get_locations_html( 'ovabrw_pickoff_loc', 'required', $pickoff_loc, $id, 'dropoff', $label_dropoff_location ); 
								} else {
									echo ovabrw_get_locations_transport_html( 'ovabrw_pickoff_loc', 'required', $pickoff_loc, $id, 'dropoff', $label_dropoff_location );
								}
							?>
							<div class="ovabrw-other-location"></div>
						</div>
					<?php endif; ?>

					<?php if ( $show_pickup_date ): 
						$label_pickup_date = esc_html__( 'Pick-up Date', 'entox' );
					?>
						<div class="rental_item">
							<div class="error_item">
								<label><?php esc_html_e( 'This field is required', 'entox' ); ?></label>
							</div>
							<input 
								type="text" 
								onkeydown="return false" 
								name="pickup_date" 
								default_hour="<?php echo esc_attr( $default_hour_start ); ?>" 
								time_to_book="<?php echo esc_attr( $time_to_book_start ); ?>" 
								class="required ovabrw_start_date ovabrw_datetimepicker <?php echo esc_attr( $class_date_picker_period ); ?> <?php echo esc_attr( $class_no_time_picker ); ?>" 
								placeholder="<?php echo esc_attr( $label_pickup_date ); ?> " 
								autocomplete="off" 
								value="<?php echo esc_attr( $pickup_date ); ?>" 
								order_time='<?php echo esc_attr( $order_time ); ?>'
								timepicker='<?php echo esc_attr( $timepicker_start ); ?>' 
								onfocus="blur();" />
							<i class="ovaicon-calendar"></i>
						</div>
					<?php endif; ?>

					<?php if ( $ovabrw_rental_type == 'period_time' ): 
						$ovabrw_petime_id 		= get_post_meta( $id, 'ovabrw_petime_id', true );
						$ovabrw_petime_label 	= get_post_meta( $id, 'ovabrw_petime_label', true );
					?>
						<div class="rental_item">
							<div class="error_item">
								<label><?php esc_html_e( 'This field is required', 'entox' ); ?></label>
							</div>
							<div class="period_package">
								<select name="ovabrw_period_package_id" class="required">
									<option value=""><?php esc_html_e( 'Select Package', 'entox' ); ?></option>
									<?php if ( $ovabrw_petime_id ): ?>
										<?php foreach ( $ovabrw_petime_id as $key => $value ): ?>
											<option value="<?php echo esc_attr( $ovabrw_petime_label[$key] ); ?>" >
												<?php echo esc_html( $ovabrw_petime_label[$key] ); ?>
											</option>
									<?php endforeach; endif; ?>
								</select>
							</div>
						</div>
						
					<?php elseif ( $ovabrw_rental_type != 'transportation' ): ?>
						<?php if( $show_pickoff_date ): 
							$label_pickoff_date = esc_html__( 'Drop-off Date', 'entox' );
						?>
							<div class="rental_item">
								<div class="error_item">
									<label><?php esc_html_e( 'This field is required', 'entox' ); ?></label>
								</div>
								<input 
									type="text" 
									name="pickoff_date" 
									onkeydown="return false" 
									default_hour="<?php echo esc_attr( $default_hour_end ); ?>"  
									time_to_book="<?php echo esc_attr( $time_to_book_end ); ?>"  
									class="required ovabrw_end_date ovabrw_datetimepicker <?php echo esc_attr( $class_no_time_picker ); ?>" 
									placeholder="<?php echo esc_attr( $label_pickoff_date ); ?>" 
									autocomplete="off" 
									value="<?php echo esc_attr( $dropoff_date ); ?>" 
									order_time='<?php echo esc_attr( $order_time ); ?>'
									timepicker='<?php echo esc_attr( $timepicker_end ); ?>' 
									onfocus="blur();" />
								<i class="ovaicon-calendar"></i>
							</div>
						<?php endif; ?>
					<?php endif; ?>
					
					<?php
						$list_ckf_output = [];

						$ovabrw_manage_custom_checkout_field 	= get_post_meta( $id, 'ovabrw_manage_custom_checkout_field', true );
						$list_field_checkout 					= get_option( 'ovabrw_booking_form', array() );

						// Get custom checkout field by Category
						$product_cats 					= wp_get_post_terms( $id, 'product_cat' );
						$cat_id 						= isset( $product_cats[0] ) ? $product_cats[0]->term_id : '';
						$ovabrw_custom_checkout_field 	= $cat_id ? get_term_meta($cat_id, 'ovabrw_custom_checkout_field', true) : '';

						$ovabrw_choose_custom_checkout_field = $cat_id ? get_term_meta( $cat_id, 'ovabrw_choose_custom_checkout_field', true) : '';
						
						if ( $ovabrw_manage_custom_checkout_field === 'new' ) {

							$list_field_checkout_in_product 	= get_post_meta( $id, 'ovabrw_product_custom_checkout_field', true );
							$list_field_checkout_in_product_arr = explode( ',', $list_field_checkout_in_product );
							$list_field_checkout_in_product_arr = array_map( 'trim', $list_field_checkout_in_product_arr );

							$list_ckf_output = [];
							if ( ! empty( $list_field_checkout_in_product_arr ) && is_array( $list_field_checkout_in_product_arr ) ) {
								foreach ( $list_field_checkout_in_product_arr as $field_name ) {
									if ( array_key_exists( $field_name, $list_field_checkout ) ) {
										$list_ckf_output[$field_name] = $list_field_checkout[$field_name];
									}
								}
							} 
						} else if ( $ovabrw_choose_custom_checkout_field == 'all' ){
							$list_ckf_output = $list_field_checkout;
						} else if ( $ovabrw_choose_custom_checkout_field == 'special' ){
							if ( $ovabrw_custom_checkout_field ) {
								foreach ( $ovabrw_custom_checkout_field as $field_name ) {
									if ( array_key_exists( $field_name, $list_field_checkout ) ) {
										$list_ckf_output[$field_name] = $list_field_checkout[$field_name];
									}
								}
							} else {
								$list_ckf_output = [];
							}
						} else {
							$list_ckf_output = $list_field_checkout;
						}
					?>

					<!-- Checkout Fields -->
					<?php if ( is_array( $list_ckf_output ) && ! empty( $list_ckf_output ) ): ?>
						<?php foreach ( $list_ckf_output as $key => $field ): ?>
							<?php if ( array_key_exists('enabled', $field) &&  $field['enabled'] == 'on' ): 
								if ( array_key_exists('required', $field) &&  $field['required'] == 'on' ) {
									$class_required = 'required';
								} else {
									$class_required = '';
								}
							?>
								<div class="rental_item">
									<div class="error_item">
										<label><?php esc_html_e( 'This field is required', 'entox' ) ?></label>
									</div>

									<?php if( $field['type'] !== 'textarea' && $field['type'] !== 'select' ) { ?>
										<input type="<?php echo esc_attr( $field['type'] ) ?>" name="<?php echo esc_attr( $key ) ?>"  class=" <?php echo esc_attr( $field['class'] ) . ' ' . $class_required ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"   value="<?php echo esc_attr( $field['default'] ); ?>"  />
									<?php } ?>

									<?php if( $field['type'] === 'textarea' ) { ?>
										<textarea name="<?php echo esc_attr( $key ) ?>"  class=" <?php echo esc_attr( $field['class'] ) . ' ' . $class_required ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"   value="<?php echo esc_attr( $field['default'] ); ?>" cols="10" rows="5"></textarea>
									<?php } ?>

									<?php if( $field['type'] === 'select' ):  
										$ova_options_key = $ova_options_text = [];

										if( array_key_exists( 'ova_options_key', $field ) ) {
											$ova_options_key = $field['ova_options_key'];
										}

										if( array_key_exists( 'ova_options_text', $field ) ) {
											$ova_options_text = $field['ova_options_text'];
										}
									?>
										<select 
											name="<?php echo esc_attr( $key ) ?>" 
											class=" <?php echo esc_attr( $field['class'] ) . ' ' . $class_required ?>">
											<?php if( ! empty( $ova_options_text ) && is_array( $ova_options_text ) ): ?>
												<?php foreach( $ova_options_text as $key => $value ): 
													$selected = '';

													if( $ova_options_key[$key] == $field['default'] ) {
														$selected = ' selected';
													}
												?>
													<option value="<?php echo esc_attr( $ova_options_key[$key] ); ?>"<?php printf( $selected ); ?>>
														<?php echo esc_html( $value ); ?>
													</option>
											<?php endforeach; endif; ?>
										</select>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>
					<!-- End Checkout Fields -->

					<!-- Services -->
					<?php if( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_service', 'yes' ) ) === 'yes' ):  
						$ovabrw_label_service 	= get_post_meta( $id, 'ovabrw_label_service', true );
						$ovabrw_service_id 		= get_post_meta( $id, 'ovabrw_service_id', true );
						$ovabrw_service_name 	= get_post_meta( $id, 'ovabrw_service_name', true );
						$ovabrw_service_price 	= get_post_meta( $id, 'ovabrw_service_price', true );
						$ovabrw_service_duration_type = get_post_meta( $id, 'ovabrw_service_duration_type', true );

						if ( $ovabrw_label_service ): 
					?>
							<div class="ovabrw_service_wrap">
								<div class="row ovabrw_service">
									<?php for( $i = 0; $i < count( $ovabrw_label_service ); $i++ ): 
										$label_group_service = $ovabrw_label_service[$i];
									?>
										<div class="ovabrw_service_select">
											<select name="ovabrw_service[]" id="">
												<option value=""><?php printf( esc_html__( 'Select %s', 'entox' ), $label_group_service ); ?></option>
												<?php if( isset( $ovabrw_service_id[$i] ) && is_array( $ovabrw_service_id[$i] ) && ! empty( $ovabrw_service_id ) ): ?>
													<?php foreach( $ovabrw_service_id[$i] as $key => $val ): ?>
															<option value="<?php echo esc_attr( $val ); ?>">
																<?php echo esc_html( $ovabrw_service_name[$i][$key] ); ?>
															</option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>
										</div>
									<?php endfor; ?>
								</div>
							</div>
						<?php endif; ?>
					<?php endif; ?>
					<!-- End Services -->

					<?php if( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_extra_info', 'yes' ) ) === 'yes' ): ?>
						<div class="extra">
							<textarea name="extra" placeholder="<?php esc_html_e( 'Extra Information', 'entox' ); ?>"></textarea>
						</div>
					<?php endif; ?>

					<!-- Resource -->
					<?php if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_extra_service', 'yes' ) ) === 'yes' ): 
						$ovabrw_resource_name = get_post_meta( $id, 'ovabrw_resource_name', true );

						if ( $ovabrw_resource_name ): 
							$ovabrw_resource_price = get_post_meta( $id, 'ovabrw_resource_price', true ); 
							$ovabrw_resource_duration_val = get_post_meta( $id, 'ovabrw_resource_duration_val', true ); 
							$ovabrw_resource_duration_type = get_post_meta( $id, 'ovabrw_resource_duration_type', true ); 
							$ovabrw_resource_id = get_post_meta( $id, 'ovabrw_resource_id', true ); 
					?>
							<div class="ovabrw_extra_service">
								<div class="row-fluid ovabrw_resource">
									<div class="row">
										<?php foreach ( $ovabrw_resource_name as $key => $value ): 
											$ovabrw_resource_key = $ovabrw_resource_id[$key];
										?>
											<div class="item">
												<div class="left">
													<input 
														type="checkbox" 
														id="ovabrw_resource_checkboxs_<?php echo esc_html($key); ?>" 
														name="ovabrw_resource_checkboxs[]" 
														value="<?php echo esc_attr( $ovabrw_resource_name[$key] ); ?>" />
													<span class="checkmark"></span>
													<label for="ovabrw_resource_checkboxs_<?php echo esc_html($key); ?>">
														<?php echo esc_html( $ovabrw_resource_name[$key] ); ?>
													</label>
												</div>
												<div class="right">
													<div class="resource">
														<span class="dur_price"><?php echo wc_price( $ovabrw_resource_price[$key] ); ?></span>
														<span class="slash">/</span>
														<?php if( isset( $ovabrw_resource_duration_val[$key] ) ) : ?>
														<span class="dur_val"><?php echo esc_html( $ovabrw_resource_duration_val[$key] ) ?></span>
														<?php endif ?>
														<span class="dur_type">
															<?php
																if( $ovabrw_resource_duration_type[$key] == 'hours' ){
																	esc_html_e( 'perhour', 'entox' );
																}else if( $ovabrw_resource_duration_type[$key] == 'days' ){
																	esc_html_e( 'perday', 'entox' );
																}if( $ovabrw_resource_duration_type[$key] == 'total' ){
																	esc_html_e( 'total', 'entox' );
																}
															?>
														</span>
													</div>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
						<?php endif; ?>
					<?php endif; ?>
					<!-- End Resource -->
				</div>
			</div>
		</div>

		<input type="hidden" name="product_name" value="<?php echo esc_attr( get_the_title() ); ?>" />
		<input type="hidden" name="product_id" value="<?php echo esc_attr( $id ); ?>" />
		<input type="hidden" name="request_booking" value="request_booking" />
		<button type="submit" class="submit btn_tran"><?php esc_html_e( 'Send', 'entox' ); ?> </button>
	</form>
</div>