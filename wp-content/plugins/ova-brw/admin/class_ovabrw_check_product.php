<?php
// Check Product
function ovabrw_check_product(){
    $all_rooms = get_all_rooms();
    $all_vehicles = ovabrw_get_all_id_vehicles();
    $current_product_id= isset( $_GET['product_id'] ) ? sanitize_text_field( $_GET['product_id'] ) : '';
    $product_id = isset( $_GET['product_id'] ) ? sanitize_text_field( $_GET['product_id'] ) : '';
    $price_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

    $page = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : 1;

    $car_count = get_post_meta( $current_product_id, 'ovabrw_car_count', true );

    // Get first day in week
    $first_day = get_option( 'ova_brw_calendar_first_day', '0' );

    if ( empty( $first_day ) ) {
      $first_day = 0;
    }

    ?>
    
    <div class="wrap"><div class="booking_filter">


        <form id="booking-filter" method="GET" action="<?php echo esc_url( admin_url('/edit.php?post_type=product&page=ovabrw-check-product') ); ?>">

        	
                <h2><?php esc_html_e( 'Check Product', 'ova-brw' ); ?></h2>

        		<select name="product_id">
        			<option value="" <?php selected( '', $current_product_id, 'selected'); ?>><?php esc_html_e( '-- Choose Product --', 'ova-brw' ); ?></option>
        			<?php 
        				if ( $all_rooms->have_posts() ) : while ( $all_rooms->have_posts() ) : $all_rooms->the_post(); ?>
        					<option value="<?php the_id(); ?>" <?php selected( get_the_id(), $current_product_id, 'selected'); ?>><?php the_title(); ?></option>
        				<?php endwhile;endif;wp_reset_postdata();
        			?>
        			
        		</select>
                
    			<button type="submit" class="button"><?php esc_html_e( 'Display Schedule', 'ova-brw' ); ?></button>
                <div class="total_vehicle">
                    <?php esc_html_e( 'Total Vehicle','ova-brw' ); ?>:
                    <?php echo esc_html( $car_count ); ?>
                </div>

        	
                <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                <input type="hidden" name="post_type" value="product" />
                <input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>" />



                <?php
                if( $current_product_id ){
                    $statuses = brw_list_order_status();
                    $order_date = get_order_rent_time( $current_product_id, $statuses );

                    if( $order_date ){
                        wp_localize_script( 'calendar_booking', 'order_time', [ $order_date ] );
                        wp_enqueue_script( 'calendar_booking' );
                    }

                    $toolbar_nav[] = ovabrw_get_setting( get_option( 'ova_brw_calendar_show_nav_month', 'yes' ) ) == 'yes' ? 'dayGridMonth' : '';
                    $toolbar_nav[] = ovabrw_get_setting( get_option( 'ova_brw_calendar_show_nav_week', 'yes' ) ) == 'yes' ? 'timeGridWeek' : '';
                    $toolbar_nav[] = ovabrw_get_setting( get_option( 'ova_brw_calendar_show_nav_day', 'yes' ) ) == 'yes' ? 'timeGridDay' : '';
                    $toolbar_nav[] = ovabrw_get_setting( get_option( 'ova_brw_calendar_show_nav_list', 'yes' ) ) == 'yes' ? 'listWeek' : '';
                    $show_time = ovabrw_get_setting( get_option( 'ova_brw_template_show_time_in_calendar', 'yes' ) ) == 'yes' ? '' : 'ova-hide-time-calendar';

                    $nav =  implode(',', array_filter( $toolbar_nav ) );


                    $lang = ovabrw_get_setting( get_option( 'ova_brw_calendar_language_general', 'en' ) ); 
                    $default_view = ( ovabrw_get_setting( get_option( 'ova_brw_calendar_default_view', 'dayGridMonth' ) ) != '' ) ? ovabrw_get_setting( get_option( 'ova_brw_calendar_default_view', 'dayGridMonth' ) ) : 'dayGridMonth';

                    ?>
                    
                    <div class="wrap_calendar">
                        <div id="<?php echo esc_attr( 'calendar'.$product_id ); ?>" 
                            data-id="<?php echo esc_attr( 'calendar'.$product_id ); ?>"
                            class="ovabrw__product_calendar <?php echo esc_attr( $show_time ); ?>"  
                            data-lang="<?php echo esc_attr( $lang ); ?>" 
                            data-nav="<?php echo esc_attr( $nav ); ?>" 
                            data-default_view="<?php echo esc_attr( $default_view ); ?>" 
                            data_event_number="<?php echo esc_attr( apply_filters( 'ovabrw_event_number_cell', 2 ) ); ?>"
                            >
                        
                            <ul class="intruction">
                                <li>
                                    <span class="pink"></span>
                                    <span class="white"></span>
                                    <span><?php esc_html_e( 'Available','ova-brw' ) ?></span>     
                                </li>
                                
                                <li>
                                    <?php $background_color_calendar = ovabrw_get_setting( get_option( 'ova_brw_bg_calendar', '#f70707' ) ); ?>
                                    <span class="yellow" style="background: <?php echo esc_attr( $background_color_calendar ); ?>" ></span>
                                    <?php if( $price_type == 'day' || $price_type == 'mixed' ) { ?>
                                        <span><?php esc_html_e( 'Unavailable', 'ova-brw' ) ?></span>   
                                    <?php } else if( $price_type == 'hour' || $price_type == 'period_time' ) { ?>
                                        <span><?php esc_html_e( 'Unavailable','ova-brw' ) ?></span>  
                                    <?php } ?>       
                                </li>
                            </ul>

                        </div>
                    </div>

                <?php } ?>
            
            
        </form>
       
       <div style="clear:both;"></div><br>

        <!-- Find Vehicle ID -->
        <form id="available-vehicle" method="GET" action="<?php echo esc_url( admin_url('/edit.php?post_type=product&page=check-product') ); ?>" >
            
            <?php 
                
                $all_locations = ovabrw_get_locations();

                $from_day = isset( $_GET['from_day'] ) ? sanitize_text_field( $_GET['from_day'] ) : '';
                $to_day = isset( $_GET['to_day'] ) ? sanitize_text_field( $_GET['to_day'] ) : '';
                $current_pickup_loc = isset( $_GET['pickup_loc'] ) ? sanitize_text_field( $_GET['pickup_loc'] ) : '';

                $from_day_new = $from_day ? strtotime( $from_day ) : '';
                $to_day_new = $to_day ? strtotime( $to_day ) : '';
                
                $number_vehicles = 1;
                
                $vehicles_available = array();

                if( $product_id ){

                    // Get Manage Vehicles
                    $manage_vehicles = get_post_meta( $product_id, 'ovabrw_manage_store', true );

                     // Set Pick-up, Drop-off Date again
                    $new_input_date   = ovabrw_new_input_date( $product_id, $from_day_new, $to_day_new, '', $current_pickup_loc, '' );

                    $pickup_date_new  = $new_input_date['pickup_date_new'];
                    $pickoff_date_new = $new_input_date['pickoff_date_new'];

                    // Check Count Product in Order
                    $store_vehicle_rented  = ovabrw_vehicle_rented_in_order( $product_id, $from_day_new, $to_day_new );

                    if ( $manage_vehicles == 'store' ) {
                        $total_vehicle = get_post_meta( $product_id, 'ovabrw_car_count', true );
                        $number_vehicles = $total_vehicle - $store_vehicle_rented;
                    } else {
                        $ids_vehicle_available = ovabrw_get_ids_vehicle_available( $product_id, $store_vehicle_rented, array() );
                        $number_vehicles       = count( $ids_vehicle_available );
                    }


                    $ova_validate_manage_store = ova_validate_manage_store( $product_id, $pickup_date_new, $pickoff_date_new, $current_pickup_loc, '', $passed = false, $validate = 'search', $number_vehicles ) ;

                    if( $ova_validate_manage_store && $ova_validate_manage_store['status'] == true ){
                        if ( $manage_vehicles == 'store' ) {
                            $vehicles_available = $ova_validate_manage_store['number_vehicle_available'];
                        } else {
                            $vehicles_available = array_unique( $ova_validate_manage_store['vehicle_availables'] );
                        }
                    }

                }

            ?>
            <h3><?php esc_html_e( 'The Available Vehicle','ova-brw' ); ?></h3>


                 <select name="pickup_loc">
                    <option value="" <?php selected( '', $current_pickup_loc, 'selected'); ?>><?php esc_html_e( '-- Pick-up Location --', 'ova-brw' ); ?></option>
                    <?php 
                        if ( $all_locations->have_posts() ) : while ( $all_locations->have_posts() ) : $all_locations->the_post(); ?>
                            <option value="<?php the_title(); ?>" <?php selected( get_the_title(), $current_pickup_loc, 'selected'); ?>><?php the_title(); ?></option>
                        <?php endwhile;endif;wp_reset_postdata();
                    ?>
                    
                </select>

                <input type="text" name="from_day" value="<?php echo esc_attr( $from_day ); ?>" placeholder="<?php esc_attr_e('From date', 'ova-brw'); ?>" class="ovabrw_datetimepicker ovabrw_start_date" data-firstday="<?php echo esc_attr( $first_day ); ?>" autocomplete="off"/>
                
                <?php esc_html_e('to','ova-brw'); ?>
                
                <input type="text" name="to_day" value="<?php echo esc_attr( $to_day ); ?>" placeholder="<?php esc_attr_e('To date', 'ova-brw'); ?>" class="ovabrw_datetimepicker ovabrw_end_date" data-firstday="<?php echo esc_attr( $first_day ); ?>" autocomplete="off" />

                <select name="product_id">
                    <option value="" <?php selected( '', $product_id, 'selected'); ?>><?php esc_html_e( '-- Choose Product --', 'ova-brw' ); ?></option>
                    <?php 
                        if ( $all_rooms->have_posts() ) : while ( $all_rooms->have_posts() ) : $all_rooms->the_post(); ?>
                            <option value="<?php the_id(); ?>" <?php selected( get_the_id(), $product_id, 'selected'); ?>><?php the_title(); ?></option>
                        <?php endwhile;endif;wp_reset_postdata();
                    ?>
                    
                </select>
                
                <button type="submit" class="button"><?php esc_html_e( 'Find Vehicle', 'ova-brw' ); ?></button>

                
                <?php if( $vehicles_available ){ ?>
                <table class="vehicle_id_available">
                    <thead>
                        <tr>
                            <td> <strong> <?php esc_html_e( 'Vehicle ID', 'ova-brw' ); ?></strong></td>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( $manage_vehicles == 'store' ): ?>
                            <tr>
                                <td>
                                    <?php
                                    esc_html_e('Available: ', 'ova-brw');
                                    echo esc_html( $vehicles_available );
                                    ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ( $vehicles_available as $value ): ?>
                                <tr>
                                    <td><?php echo esc_html( $value ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php }else{ esc_html_e( 'Not Found Vehicle','ova-brw' ); } ?>


            
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="post_type" value="product" />
            <input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>" />

        </form>
        
    </div></div>

<?php }
