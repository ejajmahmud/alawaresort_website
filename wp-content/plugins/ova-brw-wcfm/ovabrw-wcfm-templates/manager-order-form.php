<!---- Add Content Here ----->
<?php

	$current_user_id        = get_current_user_id();
    $all_rooms              = get_all_rooms_vendors( $current_user_id );

    $order_number           = isset( $_POST['order_number'] ) ? sanitize_text_field( $_POST['order_number'] ) : '';
    $name_customer          = isset( $_POST['name_customer'] ) ? sanitize_text_field( $_POST['name_customer'] ) : '';
    $current_room_id        = isset( $_POST['room_id'] ) ? sanitize_text_field( $_POST['room_id'] ) : '';
    $room_code              = isset( $_POST['room_code'] ) ? sanitize_text_field( $_POST['room_code'] ) : '';
    
    $check_in_out           = isset( $_POST['check_in_out'] ) ? sanitize_text_field( $_POST['check_in_out'] ) : '';
    $filter_order_status    = isset( $_POST['filter_order_status'] ) ? sanitize_text_field( $_POST['filter_order_status'] ) : '';
    
    $from_day               = isset( $_POST['from_day'] ) ? sanitize_text_field( $_POST['from_day'] ) : '';
    $to_day                 = isset( $_POST['to_day'] ) ? sanitize_text_field( $_POST['to_day'] ) : '';
    
    $all_locations          = ovabrw_get_locations();
    $current_pickup_loc     = isset( $_POST['pickup_loc'] ) ? sanitize_text_field( $_POST['pickup_loc'] ) : '';
    $current_pickoff_loc    = isset( $_POST['pickoff_loc'] ) ? sanitize_text_field( $_POST['pickoff_loc'] ) : '';

    // Get option settings
    $id         = get_option( 'vendor_manage_order_show_id', 1 );
    $customer   = get_option( 'vendor_manage_order_show_customer', 2 );
    $product    = get_option( 'vendor_manage_order_show_product', 3 );
    $total      = get_option( 'vendor_manage_order_show_total', 4 );
    $time       = get_option( 'vendor_manage_order_show_time', 5 );
    $location   = get_option( 'vendor_manage_order_show_location', 6 );
    $insurance  = get_option( 'vendor_manage_order_show_insurance', 7 );
    $status     = get_option( 'vendor_manage_order_show_status', 8 );
    $actions    = get_option( 'vendor_manage_order_show_actions', 9 );


?>
    
    <div class="wrap">
        <form id="booking-filter" method="POST" action="<?php echo get_wcfm_custom_menus_url( 'wcfm-manage-order' ); ?>">
        	<h2><?php esc_html_e( 'Manage Booking', 'ova-brw-wcfm' ); ?></h2>
            <br>
        	<div class="booking_filter">
            <?php if ( $id ): ?>
                <input type="text" name="order_number" value="<?php echo esc_attr( $order_number ); ?>" placeholder="<?php esc_html_e('Order ID', 'ova-brw-wcfm'); ?>" class="" autocomplete="off"/>
            <?php endif; ?>

            <?php if ( $customer ): ?>
                <input type="text" name="name_customer" value="<?php echo esc_attr( $name_customer ); ?>" placeholder="<?php esc_html_e('Customer Name', 'ova-brw-wcfm'); ?>" class="" autocomplete="off"/>
            <?php endif; ?>
        		
            <?php if ( $time ): ?>
                <select name="check_in_out">
                    <option value=""><?php esc_html_e( '-- All --', 'ova-brw-wcfm' ); ?></option>
                    <option value="check_in" <?php echo ( $check_in_out == 'check_in' ) ? 'selected' : ''; ?> ><?php esc_html_e( 'Check-in date', 'ova-brw-wcfm' ); ?></option>
                    <option value="check_out" <?php echo ( $check_in_out == 'check_out' ) ? 'selected' : ''; ?>><?php esc_html_e( 'Check-out date', 'ova-brw-wcfm' ); ?></option>
                </select>
            <?php endif; ?>

        		<input type="text" name="from_day" value="<?php echo esc_attr( $from_day ); ?>" placeholder="<?php esc_html_e('From date', 'ova-brw-wcfm'); ?>" class="ovabrw_datetimepicker ova-date-search date_book" autocomplete="off"/>
        		
        		<input type="text" name="to_day" value="<?php echo esc_attr( $to_day ); ?>" placeholder="<?php esc_html_e('To date', 'ova-brw-wcfm'); ?>" class="ovabrw_datetimepicker ova-date-search date_book" autocomplete="off" />
            
            <?php if ( $location ): ?>
                <select name="pickup_loc">
                    <option value="" <?php selected( '', $current_pickup_loc, 'selected'); ?>><?php esc_html_e( '-- Pick-up Location --', 'ova-brw-wcfm' ); ?></option>
                    <?php 
                        if ( $all_locations->have_posts() ) : while ( $all_locations->have_posts() ) : $all_locations->the_post(); ?>
                            <option value="<?php the_title(); ?>" <?php selected( get_the_title(), $current_pickup_loc, 'selected'); ?>><?php the_title(); ?></option>
                        <?php endwhile;endif;wp_reset_postdata();
                    ?>
                    
                </select> 

                <select name="pickoff_loc">
                    <option value="" <?php selected( '', $current_pickoff_loc, 'selected'); ?>><?php esc_html_e( '-- Drop-off Location --', 'ova-brw-wcfm' ); ?></option>
                    <?php 
                        if ( $all_locations->have_posts() ) : while ( $all_locations->have_posts() ) : $all_locations->the_post(); ?>
                            <option value="<?php the_title(); ?>" <?php selected( get_the_title(), $current_pickoff_loc, 'selected'); ?>><?php the_title(); ?></option>
                        <?php endwhile;endif;wp_reset_postdata();
                    ?>
                    
                </select> 
            <?php endif; ?>

            <?php if ( $product ): ?>
        		<select name="room_id">
        			<option value="" <?php selected( '', $current_room_id, 'selected'); ?>><?php esc_html_e( '-- Choose Product --', 'ova-brw-wcfm' ); ?></option>
        			<?php 
        				if ( $all_rooms->have_posts() ) : while ( $all_rooms->have_posts() ) : $all_rooms->the_post(); ?>
        					<option value="<?php the_id(); ?>" <?php selected( get_the_id(), $current_room_id, 'selected'); ?>><?php the_title(); ?></option>
        				<?php endwhile;endif;wp_reset_postdata();
        			?>
        		</select>
            <?php endif; ?>

            <?php if ( $status ): ?>
                <select name="filter_order_status" >
                    <option value=""><?php esc_html_e( '--Order Status--', 'ova-brw-wcfm' ) ?></option>
                    <option value="wc-completed" <?php echo ( 'wc-completed' == $filter_order_status ) ? 'selected' : ''; ?> ><?php esc_html_e( 'Completed', 'ova-brw-wcfm' ); ?></option>
                    <option value="wc-processing" <?php echo ( 'wc-processing' == $filter_order_status ) ? 'selected' : ''; ?>><?php esc_html_e( 'Processing', 'ova-brw-wcfm' ); ?></option>
                    <option value="wc-on-hold" <?php echo ( 'wc-on-hold' == $filter_order_status ) ? 'selected' : ''; ?>><?php esc_html_e( 'On hold', 'ova-brw-wcfm' ); ?></option>
                    <option value="wc-cancelled" <?php echo ( 'wc-cancelled' == $filter_order_status ) ? 'selected' : ''; ?>><?php esc_html_e( 'Cancel', 'ova-brw-wcfm' ); ?></option>
                    <option value="wc-closed" <?php echo ( 'wc-closed' == $filter_order_status ) ? 'selected' : ''; ?>><?php esc_html_e( 'Closed', 'ova-brw-wcfm' ); ?></option>
                </select>
            <?php endif; ?>

    			<br><br>
    			<button type="submit" class="button"><?php esc_html_e( 'Filter', 'ova-brw-wcfm' ); ?></button>
                
        	</div>
            
        </form>
    </div>