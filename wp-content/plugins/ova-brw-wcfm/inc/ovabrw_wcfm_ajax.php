<?php

add_action( 'wp_ajax_ovabrw_wcfm_get_custom_tax_in_cat', 'ovabrw_wcfm_get_custom_tax_in_cat' );
add_action( 'wp_ajax_nopriv_ovabrw_wcfm_get_custom_tax_in_cat', 'ovabrw_wcfm_get_custom_tax_in_cat' );
function ovabrw_wcfm_get_custom_tax_in_cat() {

	$checked_tax = isset( $_POST['checked_tax'] ) ?  $_POST['checked_tax'] : '';
			

	$list_tax_values = array();
	
	if( $checked_tax ){
		foreach ($checked_tax as $key => $term_id) {

			$ovabrw_custom_tax = get_term_meta($term_id, 'ovabrw_custom_tax', true);
			
			if( $ovabrw_custom_tax ){
				foreach ($ovabrw_custom_tax as $key => $value) {
					if( !in_array($value, $list_tax_values) ){
						if( $value ){
							array_push( $list_tax_values, $value);		

						}
						
					}
					
				}
			}
			
		}
		
	}
	
	echo implode(",", $list_tax_values ); 
	
	wp_die();
}

add_action( 'wp_ajax_ovabrw_wcfm_update_manage_order_status', 'ovabrw_wcfm_update_manage_order_status' );
add_action( 'wp_ajax_nopriv_ovabrw_wcfm_update_manage_order_status', 'ovabrw_wcfm_update_manage_order_status' );
function ovabrw_wcfm_update_manage_order_status(){

	$order_id 			= isset( $_POST['order_id'] ) 			? sanitize_text_field( $_POST['order_id'] ) 		: '';
	$new_order_status 	= isset( $_POST['new_order_status'] )	? sanitize_text_field( $_POST['new_order_status'] ) : '' ;

	if ( $order_id && $new_order_status ) {

		$order = new WC_Order( $order_id );

		if ( ! current_user_can( apply_filters( 'ovabrw_wcfm_update_order_status' ,'publish_shop_orders' ) ) ){

			echo 'error_permission';	

		} else if ( $order->update_status( $new_order_status ) ) {

			echo 'true';

		} else {

			echo 'false';

		}

	}else{
		echo 'false';
	}
	
	wp_die();
}

add_action( 'wp_ajax_ovabrw_wcfm_pagination_manage_order', 'ovabrw_wcfm_pagination_manage_order' );
add_action( 'wp_ajax_nopriv_ovabrw_wcfm_pagination_manage_order', 'ovabrw_wcfm_pagination_manage_order' );
function ovabrw_wcfm_pagination_manage_order(){
	$new_paged  = isset( $_POST['new_paged'] ) ? floatval( $_POST['new_paged'] ) : '';
	$user_id 	= get_current_user_id();
	$order_ids 	= get_order_vendors( $user_id, $_POST );
	$per_page 	= apply_filters( 'ovabrw_wcfm_get_posts_per_page', 10 );

	if ( ! empty( $order_ids ) ) {
		$args_query = apply_filters( 'wcfm_query_get_orders', array(
			'limit' 	=> $per_page,
			'status' 	=> array_keys( wc_get_order_statuses() ),
			'paged' 	=> $new_paged,
			'post__in' 	=> $order_ids,
			'orderby' 	=> 'date',
			'order' 	=> 'DESC',
			'offset' 	=> ( $new_paged - 1 ) * $per_page,
			'return' 	=> 'ids',
		));

		// Get orders
		$order_ids = wc_get_orders( $args_query );

		if ( ! empty( $order_ids ) && is_array( $order_ids ) ) {
			foreach ( $order_ids as $order_id ) {
				// Get an instance of the WC_Order Object
				$order 	= wc_get_order( $order_id );

				// Get order status
				$status = ovabrw_wcfm_get_order_status( $order->get_status() );

				// Get order total
				$total 	= '<span class="order_total">' . wc_price( $order->get_total() ) . '</span>';

				if ( $order->get_payment_method_title() ) {
					$total .= '<br /><small class="meta">' . __( 'Via', 'ova-brw-wcfm' ) . ' ' . esc_html( $order->get_payment_method_title() ) . '</small>';
				}

				// Get username
				if ( apply_filters( 'wcfm_allow_view_customer_name', true ) ) {
					$user_info = array();

					if ( $order->get_user_id() ) {
						$user_info = get_userdata( $order->get_user_id() );
					}

					if ( ! empty( $user_info ) ) {
						$username = '';

						if ( $user_info->first_name || $user_info->last_name ) {
							$username .= esc_html( sprintf( _x( '%1$s %2$s', 'full name', 'ova-brw-wcfm' ), ucfirst( $user_info->first_name ), ucfirst( $user_info->last_name ) ) );
						} else {
							$username .= esc_html( ucfirst( $user_info->display_name ) );
						}

						$username = '<a href="' . get_wcfm_customers_details_url( $user_info->ID ) . '" class="wcfm_dashboard_item_title">' . $username . '</a>';
					} else {
						if ( $order->get_billing_first_name() || $order->get_billing_last_name() ) {
							$username = trim( sprintf( _x( '%1$s %2$s', 'full name', 'ova-brw-wcfm' ), $order->get_billing_first_name(), $order->get_billing_last_name() ) );
						} else if ( $order->get_billing_company() ) {
							$username = trim( $order->get_billing_company() );
						} else {
							$username = esc_html__( 'Guest', 'ova-brw-wcfm' );
						}
					}
					
					$username = apply_filters( 'wcfm_order_by_user', $username, $order->get_id() );
				} else {
					$username = esc_html__( 'Guest', 'ova-brw-wcfm' );
				}
				
				$username = '<span class="wcfm_order_by_customer">' . $username . '</span>';

				// Get insurance status
				$amount_insurance = $order->get_meta( '_ova_insurance_amount' );

		        if ( $amount_insurance == 0 ) {
		            $status_insurance = '<div class="status_insurance status_paid"><span>'.esc_html__("Paid for Customers", "ova-brw").'</span></div>';
		        } else {
		            $status_insurance = '<div class="status_insurance status_pending"><span>'.esc_html__("Received", "ova-brw").'</span></div>';
		        }

				// Get order product name
				$items = $order->get_items();

				$product_name 	= $item_time = $item_loc = '';
				$number_item 	= 0;
				$item_popup 	= $item_popup_name = $item_popup_time = $item_popup_location = $item_popup_status_insurance = '';

				if ( ! empty( $items ) ) {
					$number_item = count( $items );
					
					foreach ( $items as $item ) {
						$item_id 		= $item->get_id();
						$product_id 	= $item->get_product_id();
						$product_name  .= 	'<div class="ovabrw_wcfm_item_name">
												<span class="name">
													<a href="' . get_wcfm_edit_product_url( $product_id ) .'" class="wcfm_product_title">'
													 . $item->get_name() . 
													 '</a>
												</span>
											</div>';

						$item_popup_name = '<li>
												<a href="' . get_wcfm_edit_product_url( $product_id ) .'" class="wcfm_product_title">' 
													. $item->get_name() . 
												'</a></li>';

						// Get time
						$item_pickup_date 	= $item->get_meta( 'ovabrw_pickup_date' );
						$item_pickoff_date 	= $item->get_meta( 'ovabrw_pickoff_date' );
						
						if ( $item_pickup_date && $item_pickoff_date ) {
							$item_time .= 	'<div class="ovabrw_wcfm_item_time">
												<span class="ovabrw_pickup_date">' . $item_pickup_date . '</span>
												 @ 
												<span class="ovabrw_pickoff_date">' . $item_pickoff_date . '</span>
											</div>';

							$item_popup_time = '<li class="item_date">
													<span class="ovabrw_pickup_date">' . $item_pickup_date . ' @ ' . $item_pickoff_date . 
												'</span></li>';
						} else {
							$item_popup_time = '<li></li>';
						}

						// Get location
						$item_pickup_loc 	= $item->get_meta( 'ovabrw_pickup_loc' );
						$item_pickoff_loc 	= $item->get_meta( 'ovabrw_pickoff_loc' );

						if ( $item_pickup_loc && $item_pickoff_loc ) {

							$item_loc .= 	'<div class="ovabrw_wcfm_item_loc">
												<span class="ovabrw_pickup_loc">' . $item_pickup_loc . '</span>
												 @ 
												<span class="ovabrw_pickoff_loc">' . $item_pickoff_loc . '</span>
											</div>';

							$item_popup_location = '<li><span class="ovabrw_pickup_loc">' . $item_pickup_loc . ' @ ' . $item_pickoff_loc . '</span></li>';
						} else {
							$item_popup_location = '<li></li>';
						}

						// Get 
						$item_amount_insurance = $item->get_meta( 'ovabrw_amount_insurance_product' );

						if ( $item_amount_insurance ) {
							if ( $item_amount_insurance == 0 ) {
								$item_popup_status_insurance = '<li><div class="status_insurance status_paid"><span>'.esc_html__("Paid for Customers", "ova-brw").'</span></div></li>';
							} else {
								$item_popup_status_insurance = '<li><div class="status_insurance status_pending"><span>'.esc_html__("Received", "ova-brw").'</span></div></li>';
							}
						} else {
							$item_popup_status_insurance = '<li></li>';
						}

						$item_popup .= '<ul class="item_product">' . $item_popup_name . $item_popup_time . $item_popup_location . $item_popup_status_insurance . '</ul>';
					}

				} else {
					$product_name = esc_html__( 'Product no name', 'ova-brw-wcfm' );
				}

				$selected_action = sprintf( 
					'<select 
						name="update_manage_order_status" 
						class="update_manage_order_status" 
						data-order_id="'.$order_id.'" 
						data-error-per-msg="'.esc_html__( 'You don\'t have permission to update', 'ova-brw' ).'" 
						data-error-update-msg="'.esc_html__( 'Error Update', 'ova-brw' ).'"
					>
			            <option value="">'.esc_html__( 'Update Status', 'ova-brw' ).'</option>
			            <option value="wc-completed">'.esc_html__( 'Completed', 'ova-brw' ).'</option>
			            <option value="wc-processing">'.esc_html__( 'Processing', 'ova-brw' ).'</option>
			            <option value="wc-on-hold">'.esc_html__( 'On hold', 'ova-brw' ).'</option>
			            <option value="wc-closed">'.esc_html__( 'Closed', 'ova-brw' ).'</option>
		        	</select>'
		        );

				$fileds = ovabrw_wcfm_get_fields_order();
				$filed 	= '';
				?>
				<tr role="row" class="odd">
				<?php foreach ( $fileds as $value ) {
					switch ( $value ) {
						case 'id':
							$filed = '<th><a href="'. get_wcfm_view_order_url( $order_id ) . '" class="wcfm_dashboard_item_title">#' . $order_id . '</a></th>';
							break;
						case 'customer':
							$filed = '<th>' . $username . '</th>';
							break;
						case 'product':
							$filed = 	'<th class="items_name">
											<div class="items_popup">
												<div class="items_content">
													<div class="items_title">
														<ul class="title">
															<li>' . esc_html__( 'Name', 'ova-brw-wcfm' ) . '</li>
															<li class="item_date">' . esc_html__( 'Time', 'ova-brw-wcfm' ) . '</li>
															<li>' . esc_html__( 'Location', 'ova-brw-wcfm' ) . '</li>
															<li>' . esc_html__( 'Insurance', 'ova-brw-wcfm' ) . '</li>
														</ul>'
														. $item_popup .
													'</div>
													<span class="close">&times;</span>
												</div>
											</div>
											<p class="show_order_items">' . $number_item . esc_html__( ' items', 'ova-brw-wcfm' ) . '</p>
											<div class="order_items order_items_visible">'
												. $product_name . 
											'</div>
										</th>';
							break;
						case 'total':
							$filed = '<th>' . $total . '</th>';
							break;
						case 'time':
							$filed = '<th>' . $item_time . '</th>';
							break;
						case 'location':
							$filed = '<th>' . $item_loc . '</th>';
							break;
						case 'insurance':
							$filed = '<th>' . $status_insurance . '</th>';
							break;
						case 'status':
							$filed = 	'<th>
											<div class="order-status status-' . strtolower( str_replace( ' ', '-', $status ) ) . '">
												<span>' . $status . '</span>
											</div>
										</th>';
							break;
						case 'actions':
							$filed = 	'<th class="wcfm_actions_order"><div class="order_status_action">' . $selected_action . '</div></th>';
							break;
						default:
							$filed = '';
							break;
					}
					echo $filed;
				} ?>                                                                                  
				</tr>
				<?php
			}
		} else {
			echo esc_html__( 'Not found order', 'ova-brw-wcfm' );
		}
	} else {
		echo esc_html__( 'Not found order', 'ova-brw-wcfm' );
	}
	
	wp_die();
}