<?php if ( !defined( 'ABSPATH' ) ) exit();

	// Check product if with wcfm plugin
	global $wp;
	
	if ( !empty( $wp->query_vars ) ) {
		if ( isset( $wp->query_vars['wcfm-products-manage'] ) && $wp->query_vars['wcfm-products-manage'] ) {
			$post_id = $wp->query_vars['wcfm-products-manage'];
		} else {
			$post_id = false;
		}
	} else {
		$post_id = false;
	}
		
	if( !$post_id ){
		$post_id = get_the_ID();
	}
	
	global $woocommerce, $post;

	if ( ! function_exists( 'woocommerce_wp_text_input' ) && ! is_admin() ) {
		include_once WC()->plugin_path() . '/includes/admin/wc-meta-box-functions.php';
	}
?>
	<div class="options_group show_if_ovabrw_car_rental ovabrw_metabox_car_rental">
		<!-- Price Type -->
		<?php

		$args_price_type = array(
			'day'				=> esc_html__( '1: Day', 'ova-brw' ),
	   		'hour'				=> esc_html__( '2: Hour', 'ova-brw' ),
	   		'mixed'				=> esc_html__( '3: Mixed (Day and Hour)', 'ova-brw' ),
	   		'period_time'		=> esc_html__( '4: Period of Time ( 05:00 am - 10:00 am, 1 day, 2 days, 1 month, 6 months, 1 year... )', 'ova-brw' ),
	   		'transportation' 	=> esc_html__( '5: Location', 'ova-brw' ),
		);

		$ovabrw_price_type = apply_filters( 'ovabrw_manage_price_type', $args_price_type );

		woocommerce_wp_select(
		  array(
		   'id'          	=> 'ovabrw_price_type',
		   'label' 			=> esc_html__( 'Rental Type', 'ova-brw' ),
		   'placeholder' 	=> '',
		   'desc_tip'    	=> 'true',
		   'description'    => esc_html__( 'Some fields will show/hide when change this field', 'ova-brw' ),
		   'options'		=> $ovabrw_price_type,
		   'value' 			=> $post_id ? get_post_meta( $post_id, 'ovabrw_price_type', true ) : 'day',
		   ));
		?>

		<!-- Price hour -->
		<?php  woocommerce_wp_text_input(
		  array(
		   'id'                => 'ovabrw_regul_price_hour',
		   'class'             => 'short wc_input_price',
		   'label'             => esc_html__( 'Regular price / Hour', 'ova-brw' ),
		   'placeholder'       => '',
		   'desc_tip'    => 'true',
		   'description'       => esc_html__( 'Regular price by hour', 'ova-brw' ),
		   'type'              => 'text',
		   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_regul_price_hour', true ) : '',
		   ));

		?>

		<!-- Unfixed time -->
		<?php  woocommerce_wp_select(
		  array(
		   'id'                => 'ovabrw_unfixed_time',
		   'label'             => __( 'Unfixed time', 'ova-brw' ),
		   'placeholder'       => '',
		   'options'			=> array(
		   		'no'	=> esc_html__( 'No', 'ova-brw' ),
		   		'yes'	=> esc_html__( 'Yes', 'ova-brw' ),
		   ),
		   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_unfixed_time', true ) : 'no',
		   ));
		?>

		<!-- Define 1 day -->
		<?php
		$ovabrw_define_1_day_arr = array(
			'day'	=> esc_html__( 'Day', 'ova-brw' ),
	   		'hotel'	=> esc_html__( 'Night', 'ova-brw' ),
	   		'hour'	=> esc_html__( 'Hour', 'ova-brw' ),
		);
		$ovabrw_define_1_day = apply_filters( 'ovabrw_manage_define_1_day', $ovabrw_define_1_day_arr );
		woocommerce_wp_select(
		  	array(
			   	'id' 			=> 'ovabrw_define_1_day',
			   	'label' 		=> esc_html__( 'Charged by', 'ova-brw' ),
			   	'placeholder' 	=> '',
			   	'desc_tip' 		=> 'true',
			   	'options' 		=> $ovabrw_define_1_day,
			   'description' 	=> esc_html__( 'Calculate rental period:<br/> <strong>- Day</strong>: (Drop-off date) - (Pick-up date) + 1 <br/> <strong>- Night</strong>: (Drop-off date) - (Pick-up date) <br/> <strong>- Hour</strong>: (Drop-off date) - (Pick-up date) + X <br/> X = 1:  if (Drop-off Time) - (Pick-up Time) > 0 <br/>X = 0:  if (Drop-off Time) - (Pick-up Time) < 0', 'ova-brw' ),
			   'value' 			=> $post_id && get_post_meta( $post_id, 'ovabrw_define_1_day', true ) ? get_post_meta( $post_id, 'ovabrw_define_1_day', true ) : apply_filters( 'ft_default_ovabrw_define_1_day', 'day' ),
	   	));
		?>

		<!-- Regular Price / Day -->
		<?php  
			// Edit product in backend and WCFM plugin
			if ( isset( $wp->query_vars['wcfm-products-manage'] ) && apply_filters( 'is_active_wcfm_plugin', true ) ) {
				woocommerce_wp_text_input(
					array(
					   'id'                => 'regular_price',
					   'class'             => 'short',
					   'label'             => esc_html__( 'Regular Price / Day', 'ova-brw' ),
					   'placeholder'       => '0.00',
					   'type'              => 'number',
					   'value' 				=> $post_id ? get_post_meta( $post_id, '_regular_price', true ) : '',
				   ));
			}
		?>

		<!-- Total Vehicle -->
		<?php  woocommerce_wp_text_input(
		  array(
		   'id'                => 'ovabrw_amount_insurance',
		   'class'             => 'short',
		   'label'             => esc_html__( 'Amount of insurance', 'ova-brw' ),
		   'desc_tip'    	   => 'true',
		   'description'       => esc_html__( 'This amount will be added to the cart. Decimal use dot (.)', 'ova-brw' ),
		   'placeholder'       => '10.5',
		   'type'              => 'text',
		   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_amount_insurance', true ) : '',
		   ));
		?>

		
		<?php if ( apply_filters( 'ovabrw_show_backend_deposit', true ) ) { ?>

			<!-- Enable deposit -->
			<?php  woocommerce_wp_select(
			  array(
			   'id'                => 'ovabrw_enable_deposit',
			   'label'             => esc_html__( 'Enable deposit', 'ova-brw' ),
			   'placeholder'       => '',
			   'options'			=> array(
			   		'no'	=> esc_html__( 'No', 'ova-brw' ),
			   		'yes'	=> esc_html__( 'Yes', 'ova-brw' ),
			   ),
			   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_enable_deposit', true ) : 'no',
			   ));
			?>

			<!-- Force deposit -->
			<?php  woocommerce_wp_select(
			  array(
			   'id'                => 'ovabrw_force_deposit',
			   'label'             => esc_html__( 'Require deposit', 'ova-brw' ),
			   'desc_tip'    	   => 'true',
			   'description'       => esc_html__( 'Yes: Allow pay Full Payment, No: Only Deposit', 'ova-brw' ),
			   'placeholder'       => '',
			   'options'			=> array(
			   		'no'	=> esc_html__( 'No', 'ova-brw' ),
			   		'yes'	=> esc_html__( 'Yes', 'ova-brw' ),
			   ),
			   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_force_deposit', true ) : 'no',
			   ));
			?>

			<!-- Type deposit -->
			<?php  woocommerce_wp_select(
			  array(
			   'id'                => 'ovabrw_type_deposit',
			   'label'             => esc_html__( 'Deposit type', 'ova-brw' ),
			   'placeholder'       => '',
			   'options'			=> array(
			   		'percent'	=> esc_html__( 'Percentage of price', 'ova-brw' ),
			   		'value'	=> esc_html__( 'Fixed value', 'ova-brw' ),
			   ),
			   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_type_deposit', true ) : 'percent',
			   ));
			?>
			
			<!-- amount deposit -->
			<?php  woocommerce_wp_text_input(
			  array(
			   'id'                => 'ovabrw_amount_deposit',
			   'class'             => 'short',
			   'label'             => esc_html__( 'Deposit amount', 'ova-brw' ),
			   'placeholder'       => '50',
			   'desc_tip'    	   => 'true',
			   'description'       => esc_html__( 'decimal use dot (.)', 'ova-brw' ),
			   'type'              => 'text',
			   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_amount_deposit', true ) : '',
			   ));
			?>

		<?php } ?>	

		<!-- Manage Vehicles -->
		<?php if( apply_filters( 'ovabrw_manage_store_show_manual_setting_product', true ) ){ ?>
		<?php  woocommerce_wp_select(
		  array(
		   'id'                => 'ovabrw_manage_store',
		   'label'             => esc_html__( 'Manage Vehicles', 'ova-brw' ),
		   'placeholder'       => '',
		   'options'			=> array(
		   		'store'	=> esc_html__( 'Automatic', 'ova-brw' ),
		   		'id_vehicle'	=> esc_html__( 'Manual', 'ova-brw' ),
		   ),
		   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_manage_store', true ) : 'store',
		   ));
		?>
		<?php }else{ ?>
			<?php  woocommerce_wp_select(
			  array(
			   'id'                => 'ovabrw_manage_store',
			   'label'             => esc_html__( 'Manage Vehicles', 'ova-brw' ),
			   'placeholder'       => '',
			   'options'			=> array(
			   		'store'	=> esc_html__( 'Automatic', 'ova-brw' )
			   ),
			   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_manage_store', true ) : 'store',
			   ));
			?>
		<?php } ?>


		<!-- Total Vehicle -->
		<?php  
		$value = ! empty( get_post_meta( $post_id, 'ovabrw_car_count', true ) ) ? get_post_meta( $post_id, 'ovabrw_car_count', true ) : 1;
		woocommerce_wp_text_input(
		  array(
		   'id'                => 'ovabrw_car_count',
		   'class'             => 'short',
		   'label'             => esc_html__( 'Stock Quantity', 'ova-brw' ),
		   'placeholder'       => '10',
		   'value'			   => $value,
		   'type'              => 'number'
		   ));
		?>

		<!-- ID Vehicles -->
		<!-- Feature -->
		<div class="ovabrw-form-field ">
	  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_id_vehicle.php' ); ?>
		</div>
		
		
		<?php  woocommerce_wp_text_input(
		  array(
		   'id'                => 'ovabrw_prepare_vehicle',
		   'class'             => 'short',
		   'label'             => esc_html__( 'Time between 2 leases (Minutes)', 'ova-brw' ),
		   'desc_tip'    => 'true',
		   'description'       => esc_html__( ' For example: if the car is delivered at 9:00, I set 60 minutes to prepare the car so the car is available again to book at 10:00', 'ova-brw' ),
		   'placeholder'       => '60',
		   'type'              => 'number',
		   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_prepare_vehicle', true ) : '',
		   ));
		?>

		<?php  woocommerce_wp_text_input(
		  array(
		   'id'                => 'ovabrw_prepare_vehicle_day',
		   'class'             => 'short',
		   'label'             => esc_html__( 'Time between 2 leases (Day)', 'ova-brw' ),
		   'desc_tip'    => 'true',
		   'description'       => esc_html__( ' For example: if the car is delivered on 01/01/2021, I set 1 day to prepare the car so the car is available again to book on 03/01/2021', 'ova-brw' ),
		   'placeholder'       => '0',
		   'type'              => 'number',
		   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_prepare_vehicle_day', true ) : '',
		   ));
		?>
		
		 <?php 
		 if( apply_filters( 'ovabrw_show_backend_custom_order', true ) ){
			 $value = ! empty( get_post_meta( $post_id, 'ovabrw_car_order', true ) ) ? get_post_meta( $post_id, 'ovabrw_car_order', true ) : 1;
			 woocommerce_wp_text_input(
			  array(
			   'id'                => 'ovabrw_car_order',
			   'class'             => 'short ',
			   'label'             => esc_html__( 'Order at frontend', 'ova-brw' ),
			   'placeholder'       => '1',
			   'desc_tip'    	   => 'true',
			   'value'			   => 1,
			   'description'       => esc_html__( 'Use in some elements', 'ova-brw' ),
			   'type'              => 'number',
			   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_car_order', true ) : '',
			   ));
		 }
		?>


		<!--  -->
		<div class="ovabrw-form-field ovabrw_price_daily ">
			<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Daily Price', 'ova-brw'); ?></strong>
			<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_dailys.php' ); ?>
		</div>

		<!--  -->
		<div class="ovabrw-form-field ovabrw_local_price_wrap ">
			<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Location Price', 'ova-brw'); ?></strong>
			<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_local_price.php' ); ?>
		</div>


		<!-- Price by Period Time -->
		<div class="ovabrw-form-field price_period_time ">
			<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Setup Price for Rental Type: Period of time', 'ova-brw'); ?></strong>
			<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_period_time.php' ); ?>
		</div>
		

		<!-- Feature -->
		<div class="ovabrw-form-field ovabrw_manage_features">
	  		<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Features', 'ova-brw'); ?></strong>
	  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_features.php' ); ?>
		</div>


		<!-- Global Discount -->
		<div class="ovabrw-form-field price_not_period_time ">
	  		<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Global Discount (GD)', 'ova-brw'); ?></strong>
	  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_global_discount.php' ); ?>
		</div>


		<!-- Price by range time -->
		<div class="ovabrw-form-field price_not_period_time ">
			<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Special Time (ST)', 'ova-brw'); ?></strong>
			<span class="ovabrw_right"><?php esc_html_e( 'Note: ST doesnt use GD, it will use DST', 'ova-brw' ); ?></span>
			<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_rt.php' ); ?>
		</div>
		

		<!-- Resources -->
		<div class="ovabrw-form-field ">
	  		<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Resources', 'ova-brw'); ?></strong>
	  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_resources.php' ); ?>
		</div>

		<!-- Service -->
		<div class="ovabrw-form-field ">
	  		<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Service', 'ova-brw'); ?></strong>
	  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_service.php' ); ?>
		</div>


		<!-- unavailable time -->
		<div class="ovabrw-form-field">
			<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Unavailable Time (UT)', 'ova-brw' ); ?></strong>
			<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_untime.php' ); ?>
		</div>

		<!-- Rent min -->
		<div class="ovabrw-form-field">
			<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Min Rental Period', 'ova-brw' ); ?></strong>
			<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_rent_time_min.php' ); ?>
		</div>	

		<!-- Contact form 7 shortcode -->
		<?php if( apply_filters( 'ovabrw_show_extra_tab_setting_product', true ) ){ ?>

			<div class="ovabrw-form-field">
				<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Extra Tab (shortcode)', 'ova-brw' ); ?></strong>
				<?php  woocommerce_wp_select(
				  array(
				   'id'                => 'ovabrw_manage_extra_tab',
				   'label'             => esc_html__( 'Extra Tab (shortcode)', 'ova-brw' ),
				   'placeholder'       => '',
				   'options'			=> array(
				   		'in_setting'	=> esc_html__( 'Default', 'ova-brw' ),
				   		'new_form'	=> esc_html__( 'New Form', 'ova-brw' ),
				   		'no'	=> esc_html__( 'No', 'ova-brw' ),
				   ),
				   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_manage_extra_tab', true ) : 'in_setting',
				   ));
				?>
				<?php
					woocommerce_wp_textarea_input(
					    array(
					        'id' => 'ovabrw_extra_tab_shortcode',
					        'placeholder' => esc_html__( '[contact-form-7 id="205" title="Contact form 1"]', 'ova-brw' ),
					        'label' => esc_html__('Shortcode', 'ova-brw'),
					        'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_extra_tab_shortcode', true ) : '',
					    )
					);
				?>
			</div>

		<?php } ?>

		<div class="ovabrw-form-field none-day-hotel">
			<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Start Date For Booking', 'ova-brw' ); ?></strong>
			<?php  woocommerce_wp_select(
			  array(
			   'id'                => 'ovabrw_manage_time_book_start',
			   'label'             => esc_html__( 'Group Time', 'ova-brw' ),
			   'placeholder'       => '',
			   'options'			=> array(
			   		'in_setting'	=> esc_html__( 'Default', 'ova-brw' ),
			   		'new_time'	=> esc_html__( 'New Time', 'ova-brw' ),
			   		'no'	=> esc_html__( 'No', 'ova-brw' ),
			   ),
			   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_manage_time_book_start', true ) : 'in_setting',
			   ));
			?>
			<?php
				woocommerce_wp_textarea_input(
				    array(
				        'id' => 'ovabrw_product_time_to_book_start',
				        'placeholder' => esc_html__( '07:00, 07:30, 13:00, 18:00', 'ova-brw' ),
				        'label' => '',
				        'description' => esc_html__('Insert time format: 24hour. Like 07:00, 07:30, 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 11:00, 11:30, 12:00, 12:30, 13:00, 13:30, 14:00, 14:30, 15:00, 15:30, 16:00, 16:30, 17:00, 17:30, 18:00', 'ova-brw'),
				        'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_product_time_to_book_start', true ) : '',
				    )
				);
			?>

			<?php  woocommerce_wp_select(
			  array(
			   'id'                => 'ovabrw_manage_default_hour_start',
			   'label'             => esc_html__( 'Defaul Time', 'ova-brw' ),
			   'placeholder'       => '',
			   'options'			=> array(
			   		'in_setting'	=> esc_html__( 'Default', 'ova-brw' ),
			   		'new_time'	=> esc_html__( 'New Time', 'ova-brw' ),
			   ),
			   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_manage_default_hour_start', true ) : 'in_setting',
			   ));
			?>
			<?php
				woocommerce_wp_text_input(
				    array(
				        'id' => 'ovabrw_product_default_hour_start',
				        'placeholder' => esc_html__( '09:00', 'ova-brw' ),
				        'label' => '',
				        'description' => esc_html__('Insert time format 24hour. Example: 09:00', 'ova-brw'),
				        'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_product_default_hour_start', true ) : '',
				    )
				);
			?>
		</div>

		<div class="ovabrw-form-field none-day-hotel">
			<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'End Date for booking', 'ova-brw' ); ?></strong>
			<?php  woocommerce_wp_select(
			  array(
			   'id'                => 'ovabrw_manage_time_book_end',
			   'label'             => esc_html__( 'Group Time', 'ova-brw' ),
			   'placeholder'       => '',
			   'options'			=> array(
			   		'in_setting'	=> esc_html__( 'Default', 'ova-brw' ),
			   		'new_time'	=> esc_html__( 'New Time', 'ova-brw' ),
			   		'no'	=> esc_html__( 'No', 'ova-brw' ),
			   ),
			   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_manage_time_book_end', true ) : 'in_setting',
			   ));
			?>
			<?php
				woocommerce_wp_textarea_input(
				    array(
				        'id' => 'ovabrw_product_time_to_book_end',
				        'placeholder' => esc_html__( '07:00, 07:30, 13:00, 18:00', 'ova-brw' ),
				        'label' => '',
				        'description' => esc_html__('Insert time format: 24hour. Like 07:00, 07:30, 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 11:00, 11:30, 12:00, 12:30, 13:00, 13:30, 14:00, 14:30, 15:00, 15:30, 16:00, 16:30, 17:00, 17:30, 18:00', 'ova-brw'),
				        'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_product_time_to_book_end', true ) : '',
				    )
				);
			?>

			<?php  woocommerce_wp_select(
			  array(
			   'id'                => 'ovabrw_manage_default_hour_end',
			   'label'             => esc_html__( 'Default Time', 'ova-brw' ),
			   'placeholder'       => '',
			   'options'			=> array(
			   		'in_setting'	=> esc_html__( 'Default', 'ova-brw' ),
			   		'new_time'	=> esc_html__( 'New Time', 'ova-brw' ),
			   ),
			   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_manage_default_hour_end', true ) : 'in_setting',
			   ));
			?>
			<?php
				woocommerce_wp_text_input(
				    array(
				        'id' => 'ovabrw_product_default_hour_end',
				        'placeholder' => esc_html__( '09:00', 'ova-brw' ),
				        'label' => '',
				        'description' => esc_html__('Insert time format 24hour. Example: 09:00', 'ova-brw'),
				        'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_product_default_hour_end', true ) : '',
				    )
				);
			?>
		</div>

		

		<?php if( apply_filters( 'ovabrw_show_checkout_field_setting_product', true ) ){ ?>
			<div class="ovabrw-form-field">
				<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Custom Checkout Field', 'ova-brw' ); ?></strong>
				<?php  woocommerce_wp_select(
				  array(
				   'id'                => 'ovabrw_manage_custom_checkout_field',
				   'label'             => esc_html__( 'Custom Checkout Field', 'ova-brw' ),
				   'placeholder'       => '',
				   'options'			=> array(
				   		'all'	=> esc_html__( 'All', 'ova-brw' ),
				   		'new'	=> esc_html__( 'New', 'ova-brw' ),
				   ),
				   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_manage_custom_checkout_field', true ) : 'all',
				   ));
				?>
				<br/>
				<?php
					woocommerce_wp_textarea_input(
					    array(
					        'id' => 'ovabrw_product_custom_checkout_field',
					        'placeholder' => '',
					        'label' => esc_html__('Custom Checkout Field', 'ova-brw'),
					        'description' => esc_html__('Insert name in general custom checkout field. Example: ova_email_field, ova_address_field', 'ova-brw'),
					        'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_product_custom_checkout_field', true ) : '',
					    )
					);
				?>
			</div>
		<?php } ?>

		<?php if( apply_filters( 'ovabrw_show_pickup_loc_setting_product', true ) ){ ?>
			<div class="ovabrw-form-field ">
				<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Show Pick-up Location', 'ova-brw' ); ?></strong>
				<?php  woocommerce_wp_select(
				  array(
				   'id'                => 'ovabrw_show_pickup_location_product',
				   'label'             => esc_html__( 'Show Pick-up Location', 'ova-brw' ),
				   'placeholder'       => '',
				   'options'			=> array(
				   		'in_setting'	=> esc_html__( 'Default', 'ova-brw' ),
				   		'yes'	=> esc_html__( 'Yes', 'ova-brw' ),
				   		'no'	=> esc_html__( 'No', 'ova-brw' ),
				   ),
				   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_show_pickup_location_product', true ) : 'in_setting',
				   ));
				?>

				<?php  woocommerce_wp_select(
				  array(
				   'id'                => 'ovabrw_show_other_location_pickup_product',
				   'label'             => esc_html__( 'Allow customers to enter the other location', 'ova-brw' ),
				   'placeholder'       => '',
				   'options'			=> array(
				   		'yes'	=> esc_html__( 'Yes', 'ova-brw' ),
				   		'no'	=> esc_html__( 'No', 'ova-brw' ),
				   ),
				   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_show_other_location_pickup_product', true ) : 'yes',
				   ));
				?>
			</div>
		<?php } ?>	

		

		<?php if( apply_filters( 'ovabrw_show_dropoff_loc_setting_product', true ) ){ ?>
			<div class="ovabrw-form-field">
				<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Show Drop-off Location', 'ova-brw' ); ?></strong>
				<?php  woocommerce_wp_select(
				  array(
				   'id'          	=> 'ovabrw_show_pickoff_location_product',
				   'label'       	=> esc_html__( 'Show Drop-off Location', 'ova-brw' ),
				   'placeholder'	=> '',
				   'options'		=> array(
				   		'in_setting'	=> esc_html__( 'Default', 'ova-brw' ),
				   		'yes'			=> esc_html__( 'Yes', 'ova-brw' ),
				   		'no'			=> esc_html__( 'No', 'ova-brw' ),
				   ),
				   'value' 			=> $post_id ? get_post_meta( $post_id, 'ovabrw_show_pickoff_location_product', true ) : 'in_setting',
				   ));
				?>
				<?php  woocommerce_wp_select(
				  array(
				   'id' 			=> 'ovabrw_show_other_location_dropoff_product',
				   'label' 			=> esc_html__( 'Allow customers to enter the other location', 'ova-brw' ),
				   'placeholder'	=> '',
				   'options' 		=> array(
				   		'yes'	=> esc_html__( 'Yes', 'ova-brw' ),
				   		'no'	=> esc_html__( 'No', 'ova-brw' ),
				   ),
				   'value' 			=> $post_id ? get_post_meta( $post_id, 'ovabrw_show_other_location_dropoff_product', true ) : 'yes',
				   ));
				?>
			</div>
		<?php } ?>

		
		<?php if( apply_filters( 'ovabrw_show_pickup_date_setting_product', true ) ){ ?>
			<div class="ovabrw-form-field ">
				<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Show Pick-up Date', 'ova-brw' ); ?></strong>
				<?php  woocommerce_wp_select(
				  array(
				   'id'             => 'ovabrw_show_pickup_date_product',
				   'label'          => esc_html__( 'Show Pick-up Date', 'ova-brw' ),
				   'placeholder'	=> '',
				   'options'		=> array(
				   		'in_setting' => esc_html__( 'Default', 'ova-brw' ),
				   		'yes'		 => esc_html__( 'Yes', 'ova-brw' ),
				   		'no'		 => esc_html__( 'No', 'ova-brw' ),
				   ),
				   'value' 			=> $post_id ? get_post_meta( $post_id, 'ovabrw_show_pickup_date_product', true ) : 'in_setting',
				   ));
				?>
				<?php  woocommerce_wp_select(
				  array(
				   'id'          => 'ovabrw_label_pickup_date_product',
				   'label'       => esc_html__( 'Get Label By ', 'ova-brw' ),
				   'placeholder' => '',
				   'options' 	 => array(
				   		'category' 	=> esc_html__( 'Category', 'ova-brw' ),
				   		'new' 		=> esc_html__( 'New', 'ova-brw' ),
				   ),
				   'description' => esc_html__('Show label Pick-up Date outside Frontend', 'ova-brw'),
				   'value' 		 => $post_id ? get_post_meta( $post_id, 'ovabrw_label_pickup_date_product', true ) : 'category',
				   ));
				?>
				<?php
					woocommerce_wp_text_input(
					    array(
					        'id' 			=> 'ovabrw_new_pickup_date_product',
					        'placeholder' 	=> esc_html__( 'Your Label', 'ova-brw' ),
					        'label' 		=> esc_html__('Add Label', 'ova-brw'),
					        'description' 	=> esc_html__('Insert label Pick-up Date. Example: Pick-up Date, Check-in', 'ova-brw'),
					        'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_new_pickup_date_product', true ) : '',
					    )
					);
				?>
			</div>
		<?php } ?>

		<?php if( apply_filters( 'ovabrw_show_dropoff_date_setting_product', true ) ){ ?>
			<div class="ovabrw-form-field">
				<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Show Drop-off Date', 'ova-brw' ); ?></strong>
				<?php  woocommerce_wp_select(
				  array(
				   'id' 			=> 'ovabrw_show_pickoff_date_product',
				   'label' 			=> esc_html__( 'Show Drop-off Date', 'ova-brw' ),
				   'placeholder'	=> '',
				   'options'		=> array(
				   		'in_setting'	=> esc_html__( 'Default', 'ova-brw' ),
				   		'yes'			=> esc_html__( 'Yes', 'ova-brw' ),
				   		'no'			=> esc_html__( 'No', 'ova-brw' ),
				   ),
				   'value' 			=> $post_id ? get_post_meta( $post_id, 'ovabrw_show_pickoff_date_product', true ) : 'in_setting',
				   ));
				?>
				<?php  woocommerce_wp_select(
				  array(
				   'id'          => 'ovabrw_label_dropoff_date_product',
				   'label'       => esc_html__( 'Get Label By ', 'ova-brw' ),
				   'placeholder' => '',
				   'options' 	 => array(
				   		'category' 	=> esc_html__( 'Category', 'ova-brw' ),
				   		'new' 		=> esc_html__( 'New', 'ova-brw' ),
				   ),
				   'description' => esc_html__('Show label Drop-off Date outside Frontend', 'ova-brw'),
				   'value' 		 => $post_id ? get_post_meta( $post_id, 'ovabrw_label_dropoff_date_product', true ) : 'category',
				   ));
				?>
				<?php
					woocommerce_wp_text_input(
					    array(
					        'id' 			=> 'ovabrw_new_dropoff_date_product',
					        'placeholder' 	=> esc_html__( 'Your Label', 'ova-brw' ),
					        'label' 		=> esc_html__('Add Label', 'ova-brw'),
					        'description' 	=> esc_html__('Insert label Drop-off Date. Example: Drop-off Date, Check-out', 'ova-brw'),
					        'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_new_dropoff_date_product', true ) : '',
					    )
					);
				?>
			</div>
		<?php } ?>

		<?php if( apply_filters( 'ovabrw_show_quantity_setting_product', true ) ){ ?>
			<div class="ovabrw-form-field">
				<br/>
				<strong class="ovabrw_heading_section">
					<?php esc_html_e( 'Show Quantity', 'ova-brw' ); ?>
				</strong>
				<?php  woocommerce_wp_select(
				  array(
				   'id'                => 'ovabrw_show_number_vehicle',
				   'label'             => esc_html__( 'Input quantity in booking form', 'ova-brw' ),
				   'placeholder'       => '',
				   'options'			=> array(
				   		'in_setting'	=> esc_html__( 'Default', 'ova-brw' ),
				   		'yes'	=> esc_html__( 'Yes', 'ova-brw' ),
				   		'no'	=> esc_html__( 'No', 'ova-brw' ),
				   ),
				   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_show_number_vehicle', true ) : 'in_setting',
				   ));
				?>
			</div>
		<?php } ?>

		<!-- Map -->
		<div class="ovabrw-form-field">
			<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Map', 'ova-brw' ); ?></strong>
			<?php
				$ovabrw_map_name = $post_id ? get_post_meta( $post->ID ,'ovaev_map_name', true ) : esc_html__('New York', 'ova-brw');
				$ovabrw_address  = $post_id ? get_post_meta( $post_id, 'ovabrw_address', true ) : esc_html__( 'New York, NY, USA', 'ova-brw' );
				if ( ! $ovabrw_address ) {
					$ovabrw_address = esc_html__( 'New York, NY, USA', 'ova-brw' );
				}
				// Address
				woocommerce_wp_text_input(
					array(
						'id' 				=> 'pac-input',
						'class' 			=> 'controls',
						'label'				=> '',
						'placeholder'		=> esc_html__( 'Enter a venue', 'ova-brw' ),
						'type' 				=> 'text',
						'value' 			=> $ovabrw_address,
						'custom_attributes' => array(
							'autocomplete' 	=> 'off',
							'autocorrect'	=> 'off',
							'autocapitalize'=> 'none'
						),
				));
			?>
			<div id="admin_show_map"></div>
			<div id="infowindow-content">
				<span id="place-name" class="title"><?php echo esc_attr( $ovabrw_map_name ); ?></span><br>
				<span id="place-address"><?php echo esc_attr( $ovabrw_address ); ?></span>
			</div>

			<div id="map_info">
				<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_product_map.php' ); ?>
			</div>
		</div>	

	</div>
