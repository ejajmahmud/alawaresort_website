<?php
// Display Manage Booking
function ovabrw_display_order(){
	//Create an instance of our package class...
    $manage_booking = new List_Booking();
    //Fetch, prepare, sort, and filter our data...
    $manage_booking->prepare_items();

    $all_rooms              = get_all_rooms();

    $page = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : 1;

    $order_number           = isset( $_POST['order_number'] ) ? sanitize_text_field( $_POST['order_number'] ) : '';
    $name_customer          = isset( $_POST['name_customer'] ) ? sanitize_text_field( $_POST['name_customer'] ) : '';
    $current_room_id        = isset( $_POST['room_id'] ) ? sanitize_text_field( $_POST['room_id'] ) : '';
    $room_code              = isset( $_POST['room_code'] ) ? sanitize_text_field( $_POST['room_code'] ) : '';
    
    $check_in_out           = isset($_POST['check_in_out'] ) ? sanitize_text_field( $_POST['check_in_out'] ) : '';
    $filter_order_status    = isset($_POST['filter_order_status'] ) ? sanitize_text_field( $_POST['filter_order_status'] ) : '';
    
    $from_day               = isset( $_POST['from_day'] ) ? sanitize_text_field( $_POST['from_day'] ) : '';
    $to_day                 = isset( $_POST['to_day'] ) ? sanitize_text_field( $_POST['to_day'] ) : '';

    $all_locations          = ovabrw_get_locations();
    $current_pickup_loc     = isset( $_POST['pickup_loc'] ) ? sanitize_text_field( $_POST['pickup_loc'] ) : '';
    $current_pickoff_loc    = isset( $_POST['pickoff_loc'] ) ? sanitize_text_field( $_POST['pickoff_loc'] ) : '';
    $all_vehicles           = ovabrw_get_all_id_vehicles();

    $id         = get_option( 'admin_manage_order_show_id', 1 );
    $customer   = get_option( 'admin_manage_order_show_customer', 2 );
    $time       = get_option( 'admin_manage_order_show_time', 3 );
    $location   = get_option( 'admin_manage_order_show_location', 4 );
    $deposit    = get_option( 'admin_manage_order_show_deposit', 5 );
    $insurance  = get_option( 'admin_manage_order_show_insurance', 6 );
    $vehicle    = get_option( 'admin_manage_order_show_vehicle', 7 );
    $product    = get_option( 'admin_manage_order_show_product', 8 );
    $status     = get_option( 'admin_manage_order_show_order_status', 9 );

    // Get first day in week
    $first_day = get_option( 'ova_brw_calendar_first_day', '0' );

    if ( empty( $first_day ) ) {
      $first_day = 0;
    }

    ?>
    
    <div class="wrap">
        <form id="booking-filter" method="POST" action="<?php echo esc_url( admin_url('/edit.php?post_type=product&page=ovabrw-manage-order') ); ?>">
        	<h2><?php esc_html_e( 'Manage Order', 'ova-brw' ); ?></h2>
        	<div class="booking_filter">
            <?php if ( $id ): ?>
                <input type="text" name="order_number" value="<?php echo esc_attr( $order_number ); ?>" placeholder="<?php esc_html_e('Order ID', 'ova-brw'); ?>" class="" autocomplete="off"/>
            <?php endif; ?>

            <?php if ( $customer ): ?>
                <input type="text" name="name_customer" value="<?php echo esc_attr( $name_customer ); ?>" placeholder="<?php esc_attr_e('Customer Name', 'ova-brw'); ?>" class="" autocomplete="off"/>
            <?php endif; ?>
            
            <?php if ( $time ): ?>
                <select name="check_in_out">
                    <option value=""><?php esc_html_e( '-- All --', 'ova-brw' ); ?></option>
                    <option value="check_in" <?php echo ( $check_in_out == 'check_in' ) ? 'selected' : ''; ?> ><?php esc_html_e( 'Check-in date', 'ova-brw' ); ?></option>
                    <option value="check_out" <?php echo ( $check_in_out == 'check_out' ) ? 'selected' : ''; ?>><?php esc_html_e( 'Check-out date', 'ova-brw' ); ?></option>
                </select>
            <?php endif; ?>

        		<input type="text" name="from_day" value="<?php echo esc_attr( $from_day ); ?>" placeholder="<?php esc_attr_e('From date', 'ova-brw'); ?>" class="ovabrw_datetimepicker ova-date-search date_book" autocomplete="off" data-firstday="<?php echo esc_attr( $first_day ); ?>"/>
        		
        		
        		
        		<input type="text" name="to_day" value="<?php echo esc_attr( $to_day ); ?>" placeholder="<?php esc_html_e('To date', 'ova-brw'); ?>" class="ovabrw_datetimepicker ova-date-search date_book" autocomplete="off" data-firstday="<?php echo esc_attr( $first_day ); ?>"/>
              
            <?php if ( $vehicle ): ?>
                <select name="room_code">
                    <option value="" <?php selected( '', $room_code, 'selected'); ?>><?php esc_html_e( '-- Vehicle --', 'ova-brw' ); ?></option>
                    <?php 
                        if ( $all_vehicles->have_posts() ) : while ( $all_vehicles->have_posts() ) : $all_vehicles->the_post(); ?>
                            <?php $id_vehicle = get_post_meta( get_the_id(), 'ovabrw_id_vehicle', true ); ?>
                            <option value="<?php echo esc_attr( $id_vehicle ); ?>" <?php selected( $id_vehicle, $room_code, 'selected'); ?>><?php the_title(); ?></option>
                        <?php endwhile;endif;wp_reset_postdata();
                    ?>
                    
                </select>
        	<?php endif; ?>

            <?php if ( $location ): ?>
                <select name="pickup_loc">
                    <option value="" <?php selected( '', $current_pickup_loc, 'selected'); ?>><?php esc_html_e( '-- Pick-up Location --', 'ova-brw' ); ?></option>
                    <?php 
                        if ( $all_locations->have_posts() ) : while ( $all_locations->have_posts() ) : $all_locations->the_post(); ?>
                            <option value="<?php the_title(); ?>" <?php selected( get_the_title(), $current_pickup_loc, 'selected'); ?>><?php the_title(); ?></option>
                        <?php endwhile;endif;wp_reset_postdata();
                    ?>
                    
                </select> 
            <?php endif; ?>

            <?php if ( $location ): ?>
                <select name="pickoff_loc">
                    <option value="" <?php selected( '', $current_pickoff_loc, 'selected'); ?>><?php esc_html_e( '-- Drop-off Location --', 'ova-brw' ); ?></option>
                    <?php 
                        if ( $all_locations->have_posts() ) : while ( $all_locations->have_posts() ) : $all_locations->the_post(); ?>
                            <option value="<?php the_title(); ?>" <?php selected( get_the_title(), $current_pickoff_loc, 'selected'); ?>><?php the_title(); ?></option>
                        <?php endwhile;endif;wp_reset_postdata();
                    ?>
                    
                </select>
            <?php endif; ?>

            <?php if ( $product ): ?>
        		<select name="room_id">
        			<option value="" <?php selected( '', $current_room_id, 'selected'); ?>><?php esc_html_e( '-- Choose Product --', 'ova-brw' ); ?></option>
        			<?php 
        				if ( $all_rooms->have_posts() ) : while ( $all_rooms->have_posts() ) : $all_rooms->the_post(); ?>
        					<option value="<?php the_id(); ?>" <?php selected( get_the_id(), $current_room_id, 'selected'); ?>><?php the_title(); ?></option>
        				<?php endwhile;endif;wp_reset_postdata();
        			?>
        			
        		</select>
            <?php endif; ?>

            <?php if ( $status ): ?>
                <select name="filter_order_status" >
                    <option value=""><?php esc_html_e( '--Order Status--', 'ova-brw' ) ?></option>
                    <option value="wc-completed" <?php echo ( ( 'wc-completed' == $filter_order_status ) ) ? 'selected' : ''; ?> ><?php esc_html_e( 'Completed', 'ova-brw' ); ?></option>
                    <option value="wc-processing" <?php echo ( ( 'wc-processing' == $filter_order_status ) ) ? 'selected' : ''; ?>><?php esc_html_e( 'Processing', 'ova-brw' ); ?></option>
                    <option value="wc-on-hold" <?php echo ( ( 'wc-on-hold' == $filter_order_status ) ) ? 'selected' : ''; ?>><?php esc_html_e( 'On hold', 'ova-brw' ); ?></option>
                    <option value="wc-cancelled" <?php echo ( ( 'wc-cancelled' == $filter_order_status ) ) ? 'selected' : ''; ?>><?php esc_html_e( 'Cancel', 'ova-brw' ); ?></option>
                    <option value="wc-closed" <?php echo ( ( 'wc-closed' == $filter_order_status ) ) ? 'selected' : ''; ?>><?php esc_html_e( 'Closed', 'ova-brw' ); ?></option>
                    
                </select>
            <?php endif; ?>

    			&nbsp;&nbsp;&nbsp;
    			<button type="submit" class="button"><?php esc_html_e( 'Filter', 'ova-brw' ); ?></button>

        	</div>
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="post_type" value="product" />
            <input type="hidden" name="page" value="<?php echo esc_attr( $page );?>" />
            <!-- Now we can render the completed list table -->
            <?php $manage_booking->display() ?>
        </form>
    </div>

<?php }
