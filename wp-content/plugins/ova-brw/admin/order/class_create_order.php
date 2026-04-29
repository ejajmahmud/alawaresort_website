<?php
if ( ! defined( 'ABSPATH' ) ) exit();


// Display Manage Booking
function ovabrw_create_order(){


	$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : 1;
	// Get all Products has Product Data: Rental
	$all_products = ovabrw_get_all_products();

    $date_format = ovabrw_get_date_format();
    $set_time_format = ovabrw_get_time_format_php();

    // Defautl country
    $country_setting = get_option( 'woocommerce_default_country', 'US:CA' );

	if ( strstr( $country_setting, ':' ) ) {
		$country_setting = explode( ':', $country_setting );
		$country         = current( $country_setting );
		$state           = end( $country_setting );
	} else {
		$country = $country_setting;
		$state   = '*';
	}

	// Get first day in week
	$first_day = get_option( 'ova_brw_calendar_first_day', '0' );

	if ( empty( $first_day ) ) {
	  $first_day = 0;
	}

	?>
	<div class="wrap">
	    <form id="booking-filter" method="POST" action="<?php echo esc_url( admin_url('/edit.php?post_type=product&page=ovabrw-create-order') ); ?>">
	    	<h2><?php esc_html_e( 'Create Order', 'ova-brw' ); ?></h2>

	    	<div class="ovabrw-wrap">

	    		<div class="ovabrw-row">
	    			<label for="stattus-order"><?php esc_html_e( 'Status', 'ova-brw' ) ?></label>
	    			<select name="status_order" id="stattus-order">
	    				<option value="completed" selected ><?php esc_html_e( 'Completed', 'ova-brw' ) ?></option>
	    				<option value="processing"><?php esc_html_e( 'Processing', 'ova-brw' ) ?></option>
	    				<option value="pending"><?php esc_html_e( 'Pending payment', 'ova-brw' ) ?></option>
	    				<option value="on-hold"><?php esc_html_e( 'On hold', 'ova-brw' ) ?></option>
	    				<option value="cancelled"><?php esc_html_e( 'Cancelled', 'ova-brw' ) ?></option>
	    				<option value="refunded"><?php esc_html_e( 'Refunded', 'ova-brw' ) ?></option>
	    				<option value="failed"><?php esc_html_e( 'Failed', 'ova-brw' ) ?></option>
	    			</select>
	    		</div>
	    		
	            <div class="ovabrw-row ova-column-3">
	            	<div class="item">
	            		<input type="text" name="ovabrw_first_name" placeholder="<?php esc_html_e( 'First Name', 'ova-brw' ) ?>">
	            	</div>
	            	
	            	<div class="item">
	            		<input type="text" name="ovabrw_last_name" placeholder="<?php esc_html_e( 'Last Name', 'ova-brw' ) ?>">
	            	</div>


	            	<div class="item">
	            		<input type="text" name="ovabrw_company" placeholder="<?php esc_html_e( 'Company', 'ova-brw' ) ?>">
	            	</div>

	            	<div class="item">
	            		<input type="email" name="ovabrw_email" placeholder="<?php esc_html_e( 'Email', 'ova-brw' ) ?>">
	            	</div>

	            	<div class="item">
	            		<input type="text" name="ovabrw_phone" placeholder="<?php esc_html_e( 'Phone', 'ova-brw' ) ?>">
	            	</div>
	            	
	            	<div class="item">
	            		<input type="text" name="ovabrw_address_1" placeholder="<?php esc_html_e( 'Address 1', 'ova-brw' ) ?>">
	            	</div>


	            	<div class="item">
	            		<input type="text" name="ovabrw_address_2" placeholder="<?php esc_html_e( 'Address 2', 'ova-brw' ) ?>">
	            	</div>


	            	<div class="item">
	            		<input type="text" name="ovabrw_city" placeholder="<?php esc_html_e( 'City', 'ova-brw' ) ?>">
	            	</div>


	            	<div class="item">
	            		<select name="ovabrw_country" class="ovabrw_country" style="width: 100%;">
							<?php WC()->countries->country_dropdown_options( $country, $state ); ?>
						</select>
	            	</div>
	            	
	            </div>

	            <div class="wrap_item">
	            	<div class="ovabrw-order">
		            	<div class="item">
		            		<div class="sub-item">
		            			<h3 class="title"><?php esc_html_e('Product', 'ova-brw') ?></h3>
		            			<div class="rental_item">
		            				<label for="ovabrw-name-product"><?php esc_html_e( 'Product Name', 'ova-brw' ); ?></label>
		            				<select id="ovabrw-name-product" class="ovabrw_name_product" name="ovabrw_name_product[]"
		            				data-symbol="<?php echo esc_attr(get_woocommerce_currency_symbol()); ?>" data-date_format="<?php echo esc_attr($date_format.' '.$set_time_format); ?>" data-short_date_format="<?php echo esc_attr($date_format); ?>" >
		            					<option value=""><?php esc_html_e("Select Product", "ova-brw" ); ?></option>
		            					<?php
									    while ( $all_products->have_posts() ) : $all_products->the_post();
									        global $product;
									        ?>
									        <option value="<?php echo esc_attr( get_the_id() ); ?>">
									        	<?php echo esc_html( get_the_title() ); ?>
									        </option>
									    <?php endwhile; wp_reset_postdata(); wp_reset_query(); ?>
		            				</select>
		            			</div>
		            		</div>
		            		<div class="sub-item ovabrw-meta">
		            			<h3 class="title"><?php esc_html_e('Add Meta', 'ova-brw') ?></h3>

		            			<div class="rental_item ovabrw-price-detial">
									<label for="ovabrw-price-detail"><?php esc_html_e( 'Price detail', 'ova-brw' ); ?></label>
									<input id="ovabrw-price-detail" type="text" name="ovabrw_price_detail[]" class="required ovabrw_price_detail" readonly />
								</div>
		            			

		            			<div class="rental_item show_pickup_loc">
									<label ><?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?></label>
									<?php ovabrw_get_locations_html( $name = 'ovabrw_pickup_loc[]', $required = 'required ovabrw_pickup_loc', $selected = '' ); ?>
								</div>
								
								<div class="rental_item show_pickoff_loc">
									<label><?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?></label>
									<?php ovabrw_get_locations_html( $name = 'ovabrw_pickoff_loc[]', $required = 'required ovabrw_pickoff_loc', $selected = '' ); ?>
								</div>

								<div class="rental_item ovabrw-pickup">
									<label for="ovabrw-pickup-date"><?php esc_html_e( 'Pick-up Date *', 'ova-brw' ); ?></label>
									<input id="ovabrw-pickup-date" type="text" name="ovabrw_pickup_date[]" class="required ovabrw_start_date ovabrw_datetimepicker" autocomplete="off" placeholder="<?php echo esc_attr( $date_format.' '.$set_time_format ); ?>" value=""  date_rent_full="" data-firstday="<?php echo esc_attr( $first_day ); ?>" />
								</div>

								<div class="rental_item ovabrw-dropoff">
									<label><?php esc_html_e( 'Drop-off Date *', 'ova-brw' ); ?></label>
									<input type="text" name="ovabrw_pickoff_date[]" class="required ovabrw_end_date ovabrw_datetimepicker" autocomplete="off"
									placeholder="<?php echo esc_attr( $date_format.' '.$set_time_format ); ?>"
									value=""  date_rent_full="" data-firstday="<?php echo esc_attr( $first_day ); ?>" />
								</div>

								<div class="rental_item show_number_vehicle">
									<label for="ovabrw_number_vehicle"><?php esc_html_e( 'Quantity', 'ova-brw' ); ?></label>
									<input type="number" name="ovabrw_number_vehicle[]" class="required ovabrw_number_vehicle" autocomplete="off" value="1" min="1" max="1" />
									<label class="ovabrw_number_available_vehicle" style="color:<?php echo esc_attr( ovabrw_get_setting( get_option( 'ova_brw_bg_calendar', '#f70707' ) ) ); ?>; font-size:1.em; width: 180px;"></label>
								</div>

								<div class="rental_item rental_type">
									<label for="ovabrw-rental-type"><?php esc_html_e( 'Rental Type', 'ova-brw' ); ?></label>
									<select id="ovabrw-rental-type" name="ovabrw_rental_type[]" >
										<option value="day"><?php esc_html_e( 'Day', 'ova-brw' ); ?></option>
										<option value="hour"><?php esc_html_e( 'Hour', 'ova-brw' ); ?></option>
										<option value="mixed"><?php esc_html_e( 'Mixed ', 'ova-brw' ); ?></option>
										<option value="period_time"><?php esc_html_e( 'Period of Time', 'ova-brw' ); ?></option>
										<option value="transportation"><?php esc_html_e( 'Transportation', 'ova-brw' ); ?></option>
									</select>
								</div>

								<div class="rental_item rental_define_day">
									<label for="ovabrw_define_1_day"><?php esc_html_e( 'Charged by', 'ova-brw' ); ?></label>
									<select id="ovabrw_define_1_day" name="ovabrw_define_1_day[]" >
										<option value="day"><?php esc_html_e( 'Day', 'ova-brw' ); ?></option>
										<option value="hotel"><?php esc_html_e( 'Hotel', 'ova-brw' ); ?></option>
										<option value="hour"><?php esc_html_e( 'Hour ', 'ova-brw' ); ?></option>
									</select>
								</div>

								<div class="rental_item ovabrw-package">
									<label for="ovabrw-package"><?php esc_html_e( 'Package', 'ova-brw' ); ?></label>
									<span class="ovabrw-package-span"></span>
								</div>

								<div class="rental_item ovabrw-resources">
									<label for="ovabrw-resources"><?php esc_html_e( 'Resources', 'ova-brw' ); ?></label>
									<span class="ovabrw-resources-span"></span>
								</div>

								<div class="rental_item ovabrw-services">
									<label for="ovabrw-services"><?php esc_html_e( 'Services', 'ova-brw' ); ?></label>
									<span class="ovabrw-services-span"></span>
								</div>

								<div class="rental_item ovabrw-id-vehicle">
									<label for="ovabrw-id-vehicle"><?php esc_html_e( 'ID Vehicle', 'ova-brw' ); ?></label>
									<span class="ovabrw-id-vehicle-span"></span>
								</div>

								<div class="rental_item">
									<label for="ovabrw-amount-insurance"><?php esc_html_e( 'Amount of insurance', 'ova-brw' ); ?></label>
									<input id="ovabrw-amount-insurance" readonly type="text" name="ovabrw_amount_insurance[]" class="required ovabrw_amoun_insurance" placeholder="0" />
								</div>

								<div class="rental_item">
									<label for="ovabrw-amount-deposite"><?php esc_html_e( 'Deposit Amount', 'ova-brw' ); ?></label>
									<input id="ovabrw-amount-deposite" type="text" name="ovabrw_amount_deposite[]" class="required ovabrw_amoun_deposite" placeholder="0" />
								</div>

								<div class="rental_item ovabrw-amount-remaining">
									<label for="ovabrw-amount-remaining"><?php esc_html_e( 'Remaining Amount', 'ova-brw' ); ?></label>
									<input id="ovabrw-amount-remaining" type="text" name="ovabrw_amount_remaining[]" class="required ovabrw_amount_remaining" placeholder="0" <?php echo esc_attr( apply_filters( 'ovabrw_create_order_edit_view_time', 'readonly' ) ); ?> />
								</div>

								<div class="rental_item ovabrw-error">
									<span class="ovabrw-error-span"></span>
								</div>

								<div class="rental_item ovabrw-total-time">
									<label for="ovabrw-total-time"><?php esc_html_e( 'Total time', 'ova-brw' ); ?></label>
									<input id="ovabrw-total-time" type="text" name="ovabrw_total_time[]" class="required ovabrw_total_time" <?php echo esc_attr( apply_filters( 'ovabrw_create_order_edit_view_time', 'readonly' ) ); ?> />
								</div>

								<div class="rental_item ovabrw-total-cost">
		            				<label for="ovabrw-total-product"><?php esc_html_e( 'Cost', 'ova-brw' ); ?></label>
		            				<input id="ovabrw-total-product" type="text" name="ovabrw_total_product[]" class="required ovabrw_total_product"  <?php echo esc_attr( apply_filters( 'ovabrw_create_order_edit_cost', 'readonly' ) ); ?>  />
		            				<span><?php echo esc_attr( get_woocommerce_currency_symbol() ); ?></span>
		            			</div>



		            		</div>
		            	</div>
		            	<a href="#" class="delete_order">x</a>
		            </div>

	            </div>

				<div class="ovabrw-row">
					<a href="#" class="button insert_wrap_item" data-row="
						<?php
							ob_start();
							?>
								<div class="ovabrw-order">
					            	<div class="item">
					            		<div class="sub-item">
					            			<h3 class="title"><?php esc_html_e('Product', 'ova-brw') ?></h3>
					            			<div class="rental_item">
					            				<label for="ovabrw-name-product"><?php esc_html_e( 'Product Name', 'ova-brw' ); ?></label>
					            				<select id="ovabrw-name-product" class="ovabrw_name_product" name="ovabrw_name_product[]"
					            				data-symbol="<?php echo esc_attr( get_woocommerce_currency_symbol() ); ?>"
					            				data-date_format="<?php echo esc_attr( $date_format.' '.$set_time_format ); ?>"
					            				data-short_date_format="<?php echo esc_attr( $date_format ); ?>">
					            					<option value="">
					            						<?php esc_html_e("Select Product", "ova-brw" ); ?>
					            					</option>
					            					<?php
												    while ( $all_products->have_posts() ) : $all_products->the_post();
												        global $product;
												        ?>
												        <option value="<?php echo esc_attr( get_the_id() ); ?>">
												        	<?php echo esc_html( get_the_title() ); ?>
												        </option>
												    <?php endwhile; wp_reset_postdata(); wp_reset_query(); ?>
					            				</select>
					            			</div>
					            		</div>
					            		<div class="sub-item ovabrw-meta">
					            			<h3 class="title"><?php esc_html_e('Add Meta', 'ova-brw') ?></h3>

					            			<div class="rental_item ovabrw-price-detial">
												<label for="ovabrw-price-detail"><?php esc_html_e( 'Price detail', 'ova-brw' ); ?></label>
												<input id="ovabrw-price-detail" type="text" name="ovabrw_price_detail[]" class="required ovabrw_price_detail" readonly />
											</div>
					            			

					            			<div class="rental_item show_pickup_loc">
												<label ><?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?></label>
												<?php ovabrw_get_locations_html( $name = 'ovabrw_pickup_loc[]', $required = 'required ovabrw_pickup_loc', $selected = '' ); ?>
											</div>
											
											<div class="rental_item show_pickoff_loc">
												<label><?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?></label>
												<?php ovabrw_get_locations_html( $name = 'ovabrw_pickoff_loc[]', $required = 'required ovabrw_pickoff_loc', $selected = '' ); ?>
											</div>

											<div class="rental_item ovabrw-pickup">
												<label for="ovabrw-pickup-date"><?php esc_html_e( 'Pick-up Date', 'ova-brw' ); ?></label>
												<input id="ovabrw-pickup-date" type="text" name="ovabrw_pickup_date[]" class="required ovabrw_start_date ovabrw_datetimepicker" autocomplete="off" placeholder="<?php echo esc_attr( $date_format.' '.$set_time_format ); ?>" value="" date_rent_full="" data-firstday="<?php echo esc_attr( $first_day ); ?>" />
											</div>

											<div class="rental_item ovabrw-dropoff">
												<label><?php esc_html_e( 'Drop-off Date', 'ova-brw' ); ?></label>
												<input type="text" name="ovabrw_pickoff_date[]" class="required ovabrw_end_date ovabrw_datetimepicker" autocomplete="off" placeholder="<?php echo esc_attr( $date_format.' '.$set_time_format ); ?>" value="" date_rent_full="" data-firstday="<?php echo esc_attr( $first_day ); ?>" />
											</div>

											<div class="rental_item show_number_vehicle">
												<label for="ovabrw_number_vehicle"><?php esc_html_e( 'Quantity', 'ova-brw' ); ?></label>
												<input type="number" name="ovabrw_number_vehicle[]" class="required ovabrw_number_vehicle" autocomplete="off" value="1" min="1" max="1" />
												<label class="ovabrw_number_available_vehicle" style="color:<?php echo esc_attr( ovabrw_get_setting( get_option( 'ova_brw_bg_calendar', '#f70707' ) ) ); ?>; font-size:1.em; width: 180px;"></label>
											</div>

											<div class="rental_item rental_type">
												<label for="ovabrw-rental-type"><?php esc_html_e( 'Rental Type', 'ova-brw' ); ?></label>
												<select id="ovabrw-rental-type" name="ovabrw_rental_type[]" >
													<option value="day"><?php esc_html_e( 'Day', 'ova-brw' ); ?></option>
													<option value="hour"><?php esc_html_e( 'Hour', 'ova-brw' ); ?></option>
													<option value="mixed"><?php esc_html_e( 'Mixed ', 'ova-brw' ); ?></option>
													<option value="period_time"><?php esc_html_e( 'Period of Time', 'ova-brw' ); ?></option>
													<option value="transportation"><?php esc_html_e( 'Transportation', 'ova-brw' ); ?></option>
												</select>
											</div>

											<div class="rental_item rental_define_day">
												<label for="ovabrw_define_1_day"><?php esc_html_e( 'Charged by', 'ova-brw' ); ?></label>
												<select id="ovabrw_define_1_day" name="ovabrw_define_1_day[]" >
													<option value="day"><?php esc_html_e( 'Day', 'ova-brw' ); ?></option>
													<option value="hotel"><?php esc_html_e( 'Hotel', 'ova-brw' ); ?></option>
													<option value="hour"><?php esc_html_e( 'Hour ', 'ova-brw' ); ?></option>
												</select>
											</div>

											<div class="rental_item ovabrw-package">
												<label for="ovabrw-package"><?php esc_html_e( 'Package', 'ova-brw' ); ?></label>
												<span class="ovabrw-package-span"></span>
											</div>

											<div class="rental_item ovabrw-resources">
												<label for="ovabrw-resources"><?php esc_html_e( 'Resources', 'ova-brw' ); ?></label>
												<span class="ovabrw-resources-span"></span>
											</div>

											<div class="rental_item ovabrw-services">
												<label for="ovabrw-services"><?php esc_html_e( 'Services', 'ova-brw' ); ?></label>
												<span class="ovabrw-services-span"></span>
											</div>

											<div class="rental_item ovabrw-id-vehicle">
												<label for="ovabrw-id-vehicle"><?php esc_html_e( 'ID Vehicle', 'ova-brw' ); ?></label>
												<span class="ovabrw-id-vehicle-span"></span>
											</div>

											<div class="rental_item">
												<label for="ovabrw-amount-insurance"><?php esc_html_e( 'Amount of insurance', 'ova-brw' ); ?></label>
												<input id="ovabrw-amount-insurance" readonly type="text" name="ovabrw_amount_insurance[]" class="required ovabrw_amoun_insurance" placeholder="0" />
											</div>

											<div class="rental_item">
												<label for="ovabrw-amount-deposite"><?php esc_html_e( 'Deposit Amount', 'ova-brw' ); ?></label>
												<input id="ovabrw-amount-deposite" type="text" name="ovabrw_amount_deposite[]" class="required ovabrw_amoun_deposite" placeholder="0" />
											</div>

											<div class="rental_item ovabrw-amount-remaining">
												<label for="ovabrw-amount-remaining"><?php esc_html_e( 'Remaining Amount', 'ova-brw' ); ?></label>
												<input id="ovabrw-amount-remaining" type="text" name="ovabrw_amount_remaining[]" class="required ovabrw_amount_remaining" placeholder="0" <?php echo esc_attr( apply_filters( 'ovabrw_create_order_edit_view_time', 'readonly' ) ); ?> />
											</div>

											<div class="rental_item ovabrw-error">
												<span class="ovabrw-error-span"></span>
											</div>

											<div class="rental_item ovabrw-total-time">
												<label for="ovabrw-total-time"><?php esc_html_e( 'Total time', 'ova-brw' ); ?></label>
												<input id="ovabrw-total-time" type="text" name="ovabrw_total_time[]" class="required ovabrw_total_time" <?php echo esc_attr( apply_filters( 'ovabrw_create_order_edit_view_time', 'readonly' ) ); ?> />
											</div>

											<div class="rental_item ovabrw-total-cost">
					            				<label for="ovabrw-total-product"><?php esc_html_e( 'Cost', 'ova-brw' ); ?></label>
					            				<input id="ovabrw-total-product" type="text" name="ovabrw_total_product[]" class="required ovabrw_total_product" <?php echo esc_attr( apply_filters( 'ovabrw_create_order_edit_cost', 'readonly' ) ); ?> />
					            				<span><?php echo esc_html( get_woocommerce_currency_symbol() ); ?></span>
					            			</div>
					            			
					            			

					            		</div>
					            	</div>
					            	<a href="#" class="delete_order">x</a>
					            </div>
							<?php
							echo esc_attr( ob_get_clean() );
						?>

					">
					<?php esc_html_e( 'Add Item', 'ova-brw' ); ?></a>
					</a>
				</div>

				
				<button type="submit" class="button"><?php esc_html_e( 'Create Order', 'ova-brw' ); ?></button>

	    	</div>

	        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
	        
	        <input type="hidden" name="post_type" value="product" />
	        <input type="hidden" name="ovabrw_create_order" value="create_order" />
	        <input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>" />
	        
	        <!-- Now we can render the completed list table -->
	       
	    </form>
	</div>
	
<?php

}