<?php if ( !defined( 'ABSPATH' ) ) exit();


if( !class_exists( 'Ovabrw_Metabox' ) ){

     class Ovabrw_Metabox{

       
        // Constructor
        public function __construct(){
            
                add_action( 'save_post', array( $this, 'ovabrw_save_meta_box' ) );
                
                add_action( 'save_post', array( $this, 'ovabrw_save_meta_box_vehicle') );

                // Render vehicle
                add_action( 'add_meta_boxes_vehicle', array( $this, 'ovabrw_register_meta_boxes_vehicle' ) );

                // Render metabox of Product
                add_action( 'woocommerce_product_options_general_product_data', array( $this, 'ovabrw_render_fields_metabox' ) );


                // Save metabox of Product
                add_action( 'after_wcfm_products_manage_meta_save', array( $this, 'ovabrw_save_fields_metabox' ), 10, 2 );
                add_action( 'woocommerce_process_product_meta', array( $this, 'ovabrw_save_fields_metabox' ), 10, 2 );
                
                
        }

      
        // Save metabox fields
        public function ovabrw_save_meta_box() {

            $product_id = get_the_id();

            if ( isset( $_POST ) && ! empty( $_POST ) ) {

                $ovabrw_metabox['ovabrw_key'] = isset( $_POST['ovabrw_key'] ) ? sanitize_text_field( $_POST['ovabrw_key'] ) : '';
                $ovabrw_metabox['ovabrw_value'] = isset( $_POST['ovabrw_value'] ) ? sanitize_text_field( $_POST['ovabrw_value'] ) : '';

                foreach ( $ovabrw_metabox as $key => $value ) {
                    update_post_meta($product_id, $key, $value);
                    if( get_post_meta( $product_id, $key, FALSE ) ) { 
                        update_post_meta($product_id, $key, $value);
                    } else {
                        add_post_meta( $product_id, $key, $value );
                    }
                }
            }
        }


        public function ovabrw_register_meta_boxes_vehicle() {
            add_meta_box( 'meta-box-id-vehicle', esc_html__( 'Vehicle Setting', 'ova-brw' ), array( $this, 'ovabrw_display_vehicle_callback' ), 'vehicle' );
        }


        public function ovabrw_display_vehicle_callback( ) {
            // global $product;
            $id_vehicle = get_the_id();

            $meta_post_title = get_post_meta( $id_vehicle, 'meta_post_title', true );
            $post_desc = get_post_meta( $id_vehicle, 'post_desc', true );

            $ovabrw_id_vehicle = get_post_meta( $id_vehicle, 'ovabrw_id_vehicle', true );

            $ovabrw_vehicle_require_location = get_post_meta( $id_vehicle, 'ovabrw_vehicle_require_location', true );

            $ovabrw_id_vehicle_location = get_post_meta( $id_vehicle, 'ovabrw_id_vehicle_location', true );
            $ovabrw_id_vehicle_untime_from_day = get_post_meta( $id_vehicle, 'ovabrw_id_vehicle_untime_from_day', true );

            $date_format =  ovabrw_get_date_format();
            $set_time_format = ovabrw_get_time_format_php();

            

            $startdate = ( is_array($ovabrw_id_vehicle_untime_from_day) && array_key_exists( 'startdate', $ovabrw_id_vehicle_untime_from_day ) ) ? $ovabrw_id_vehicle_untime_from_day['startdate'] : '' ;
            $enddate = ( is_array($ovabrw_id_vehicle_untime_from_day) && array_key_exists( 'enddate', $ovabrw_id_vehicle_untime_from_day ) ) ? $ovabrw_id_vehicle_untime_from_day['enddate'] : '' ;

            if( $startdate != '' ) {
                $startdate = wp_date( $date_format . ' '.$set_time_format, strtotime( $startdate ) );
            }

            if( $enddate != '' ) {
                $enddate = wp_date( $date_format . ' '.$set_time_format, strtotime( $enddate ) );
            }

            if( class_exists('OVABRW') && class_exists('woocommerce') ){
                $id_vehicle_location = ovabrw_get_locations_array();
            }else{
                $id_vehicle_location = array();
            }

            ?>

            <div>
                <div class="ovabrw-row">
                    <label for="ovabrw-id_vehicle"><?php esc_html_e( 'Id Vehicle (Unique)', 'ova-brw' ) ?></label>
                    <input id="ovabrw-id_vehicle" type="text" name="ovabrw_id_vehicle" placeholder="<?php esc_attr_e( 'Id Vehicle', 'ova-brw' ) ?>" value="<?php echo esc_attr( $ovabrw_id_vehicle ) ?>" >
                </div>

                <div class="ovabrw-row require_location">
                    <label for="ovabrw-require_location"><?php esc_html_e( 'Require Location', 'ova-brw' ) ?></label>

                    <label class="label_radio" for="ovabrw_loc_yes"><?php esc_html_e('Yes', 'ova-brw') ?></label>
                    <input type="radio" id="ovabrw_loc_yes" name="ovabrw_vehicle_require_location" value="yes" <?php echo ( $ovabrw_vehicle_require_location === 'yes' ? 'checked' : '' ) ?>>
                    

                    <label class="label_radio" for="ovabrw_loc_no"><?php esc_html_e('No', 'ova-brw') ?></label>
                    <input type="radio" id="ovabrw_loc_no" name="ovabrw_vehicle_require_location" value="no" <?php echo ( $ovabrw_vehicle_require_location !== 'yes' ? 'checked' : '' ) ?>>
                    
                </div>

                <div class="ovabrw-row location_vehicle">
                    <label class="loc" for="ovabrw-location"><?php esc_html_e( 'Vehicle location', 'ova-brw' ) ?></label>
                    <select name="ovabrw_id_vehicle_location" id="ovabrw-location">
                        <?php foreach( $id_vehicle_location as $location ) : ?>
                            <?php
                            $checked = $ovabrw_id_vehicle_location === $location ? 'selected' : '';

                            ?>
                            <option value="<?php echo esc_attr( $location ); ?>" <?php checked( $checked, 'selected' ); ?> ><?php echo esc_html( $location ); ?></option>
                        <?php endforeach ?>
                    </select>
                    
                </div>

                <div class="ovabrw-row">
                    <label for="ovabrw-unavailable"><?php esc_html_e( 'Unavailable Time', 'ova-brw' ) ?></label>
                    <span class="from"><?php esc_html_e( 'From: ', 'ova-brw' ) ?></span>
                    <input  class=" unavailable_time start_date " type="text" name="ovabrw_id_vehicle_untime_from_day[startdate]" value="<?php echo esc_attr( $startdate ) ?>" placeholder="<?php echo esc_attr( $date_format ) ?>" >
                    <span class="to"><?php esc_html_e( 'To: ', 'ova-brw' ) ?></span>
                    <input  class=" unavailable_time end_date " type="text" name="ovabrw_id_vehicle_untime_from_day[enddate]" value="<?php echo esc_attr( $enddate ) ?>" placeholder="<?php echo esc_attr( $date_format ) ?>" >
                </div>
                
            </div>
            

            
            <?php
        }

        // Save metabox of vehicle
        public function ovabrw_save_meta_box_vehicle() {
            $id_vehicle = get_the_id();

            $ovabrw_metabox_vehicle = [];
            if ( isset( $_POST ) && ! empty( $_POST ) ) {

                $ovabrw_metabox_vehicle['ovabrw_vehicle_require_location'] = isset( $_POST['ovabrw_vehicle_require_location'] ) ? sanitize_text_field( $_POST['ovabrw_vehicle_require_location'] ) : '';

                $ovabrw_metabox_vehicle['ovabrw_id_vehicle'] = isset( $_POST['ovabrw_id_vehicle'] ) ? sanitize_text_field( $_POST['ovabrw_id_vehicle'] ) : '' ;
                $ovabrw_metabox_vehicle['ovabrw_id_vehicle_location'] = isset( $_POST['ovabrw_id_vehicle_location'] ) ? sanitize_text_field( $_POST['ovabrw_id_vehicle_location'] ) : '' ;
                $ovabrw_metabox_vehicle['ovabrw_id_vehicle_untime_from_day'] = [
                    'startdate' => isset( $_POST['ovabrw_id_vehicle_untime_from_day']['startdate'] ) ? sanitize_text_field( $_POST['ovabrw_id_vehicle_untime_from_day']['startdate'] ) : '' ,
                    'enddate' => isset( $_POST['ovabrw_id_vehicle_untime_from_day']['enddate'] ) ? sanitize_text_field( $_POST['ovabrw_id_vehicle_untime_from_day']['enddate'] ) : '' ,
                ];


                foreach ( $ovabrw_metabox_vehicle as $key => $value ) {
                    if( get_post_meta( $id_vehicle, $key, FALSE ) ) { 
                        update_post_meta($id_vehicle, $key, $value);
                    } else {
                        add_post_meta( $id_vehicle, $key, $value );
                    }
                }
            }

           
        }

        
        public function ovabrw_render_fields_metabox() {
            include( OVABRW_PLUGIN_PATH.'/admin/metabox/ovabrw-custom-fields.php' );
        }


        
        public function ovabrw_save_fields_metabox( $post_id, $data ){


          if( ! is_array( $data ) ){
            $data = $_POST;
          }


          
          $ovabrw_regul_price_hour = isset($data['ovabrw_regul_price_hour']) ? $data['ovabrw_regul_price_hour'] : '';
          update_post_meta( $post_id, 'ovabrw_regul_price_hour', esc_attr( $ovabrw_regul_price_hour) );

          // save custom field
          $ovabrw_manage_store = isset($data['ovabrw_manage_store']) ? $data['ovabrw_manage_store'] : '';
          update_post_meta( $post_id, 'ovabrw_manage_store', esc_attr( $ovabrw_manage_store) ); 
          
          $ovabrw_car_count = isset($data['ovabrw_car_count']) ? $data['ovabrw_car_count'] : '';
          update_post_meta( $post_id, 'ovabrw_car_count', esc_attr( $ovabrw_car_count) );

          $ovabrw_amount_insurance = isset($data['ovabrw_amount_insurance']) ? $data['ovabrw_amount_insurance'] : '';
          update_post_meta( $post_id, 'ovabrw_amount_insurance', esc_attr( $ovabrw_amount_insurance) );

          $ovabrw_car_order = isset($data['ovabrw_car_order']) ? $data['ovabrw_car_order'] : 1;
          update_post_meta( $post_id, 'ovabrw_car_order', esc_attr( $ovabrw_car_order) );

          $ovabrw_enable_deposit = isset($data['ovabrw_enable_deposit']) ? $data['ovabrw_enable_deposit'] : 'no';
          update_post_meta( $post_id, 'ovabrw_enable_deposit', esc_attr( $ovabrw_enable_deposit) );

          $ovabrw_force_deposit = isset($data['ovabrw_force_deposit']) ? $data['ovabrw_force_deposit'] : 'no';
          update_post_meta( $post_id, 'ovabrw_force_deposit', esc_attr( $ovabrw_force_deposit) );

          $ovabrw_type_deposit = isset($data['ovabrw_type_deposit']) ? $data['ovabrw_type_deposit'] : 'percent';
          update_post_meta( $post_id, 'ovabrw_type_deposit', esc_attr( $ovabrw_type_deposit) );

          $ovabrw_amount_deposit = isset($data['ovabrw_amount_deposit']) ? $data['ovabrw_amount_deposit'] : 0;
          update_post_meta( $post_id, 'ovabrw_amount_deposit', esc_attr( $ovabrw_amount_deposit) );

          
          $ovabrw_global_discount_price = isset($data['ovabrw_global_discount_price']) ? $data['ovabrw_global_discount_price'] : '';
          update_post_meta( $post_id, 'ovabrw_global_discount_price', $ovabrw_global_discount_price );


          $ovabrw_global_discount_duration_val_min = isset($data['ovabrw_global_discount_duration_val_min']) ? $data['ovabrw_global_discount_duration_val_min'] : '';
          update_post_meta( $post_id, 'ovabrw_global_discount_duration_val_min', $ovabrw_global_discount_duration_val_min );

          $ovabrw_global_discount_duration_val_max = isset($data['ovabrw_global_discount_duration_val_max']) ? $data['ovabrw_global_discount_duration_val_max'] : '';
          update_post_meta( $post_id, 'ovabrw_global_discount_duration_val_max', $ovabrw_global_discount_duration_val_max );

          $ovabrw_global_discount_duration_type = isset($data['ovabrw_global_discount_duration_type']) ? $data['ovabrw_global_discount_duration_type'] : '';
          update_post_meta( $post_id, 'ovabrw_global_discount_duration_type', $ovabrw_global_discount_duration_type );

          // Price/Day
          $ovabrw_rt_price = isset($data['ovabrw_rt_price']) ? $data['ovabrw_rt_price'] : '';
          update_post_meta( $post_id, 'ovabrw_rt_price', $ovabrw_rt_price );

          // Price/Hour


          $ovabrw_rt_price_hour = isset($data['ovabrw_rt_price_hour']) ? $data['ovabrw_rt_price_hour'] : '';
          update_post_meta( $post_id, 'ovabrw_rt_price_hour', $ovabrw_rt_price_hour );

            
          $ovabrw_rt_startdate = isset($data['ovabrw_rt_startdate']) ? $data['ovabrw_rt_startdate'] : '';
          update_post_meta( $post_id, 'ovabrw_rt_startdate', $ovabrw_rt_startdate );

          $ovabrw_rt_starttime = isset($data['ovabrw_rt_starttime']) ? $data['ovabrw_rt_starttime'] : '';
          update_post_meta( $post_id, 'ovabrw_rt_starttime', $ovabrw_rt_starttime );


          $time_current = current_time( 'timestamp' );
          

          $ovabrw_rt_timestamp_start = $ovabrw_rt_string_start = [];
          if ( ! empty( $ovabrw_rt_startdate ) && ! empty( $ovabrw_rt_starttime ) && is_array( $ovabrw_rt_startdate ) && is_array( $ovabrw_rt_starttime ) ) {
            foreach( $ovabrw_rt_startdate as $key => $start_date ) {

              $start_timestamp = ovabrw_get_timestamp_by_date_and_hour($start_date, $ovabrw_rt_starttime[$key]);
              $start_string = 
              $ovabrw_rt_timestamp_start[] = $start_timestamp;
              $ovabrw_rt_string_start[] = $start_date . ' ' . $ovabrw_rt_starttime[$key];
            }
          }
          update_post_meta( $post_id, 'ovabrw_start_timestamp', $ovabrw_rt_timestamp_start );
          update_post_meta( $post_id, 'ovabrw_rt_string_start', $ovabrw_rt_string_start );


          $ovabrw_rt_enddate = isset($data['ovabrw_rt_enddate']) ? $data['ovabrw_rt_enddate'] : '';
          update_post_meta( $post_id, 'ovabrw_rt_enddate', $ovabrw_rt_enddate );

          $ovabrw_rt_endtime = isset($data['ovabrw_rt_endtime']) ? $data['ovabrw_rt_endtime'] : '';
          update_post_meta( $post_id, 'ovabrw_rt_endtime', $ovabrw_rt_endtime );


          $ovabrw_rt_timestamp_end = $ovabrw_rt_string_end = [];
          if ( ! empty( $ovabrw_rt_enddate ) && ! empty( $ovabrw_rt_endtime ) && is_array( $ovabrw_rt_enddate ) && is_array( $ovabrw_rt_endtime ) ) {
            foreach( $ovabrw_rt_enddate as $key => $end_date ) {

              $end_timestamp = ovabrw_get_timestamp_by_date_and_hour($end_date , $ovabrw_rt_endtime[$key]);
              $ovabrw_rt_timestamp_end[] = $end_timestamp;
              $ovabrw_rt_string_end[] = $end_date . ' ' . $ovabrw_rt_endtime[$key];
            }
          }
          update_post_meta( $post_id, 'ovabrw_end_timestamp', $ovabrw_rt_timestamp_end );
          update_post_meta( $post_id, 'ovabrw_rt_string_end', $ovabrw_rt_string_end );


          $ovabrw_rt_discount = isset($data['ovabrw_rt_discount']) ? $data['ovabrw_rt_discount'] : '';
          if ( is_array($ovabrw_rt_discount) && array_key_exists('ovabrw_key', $ovabrw_rt_discount)) {
            unset($ovabrw_rt_discount['ovabrw_key']);// Remove array has key is ovabrw_key 
          }
          
          update_post_meta( $post_id, 'ovabrw_rt_discount', $ovabrw_rt_discount );

          $ovabrw_untime_startdate = isset($data['ovabrw_untime_startdate']) ? $data['ovabrw_untime_startdate'] : '';
          update_post_meta( $post_id, 'ovabrw_untime_startdate', $ovabrw_untime_startdate );

          $ovabrw_untime_enddate = isset($data['ovabrw_untime_enddate']) ? $data['ovabrw_untime_enddate'] : '';
          update_post_meta( $post_id, 'ovabrw_untime_enddate', $ovabrw_untime_enddate );


          $ovabrw_untime_starttime = isset($data['ovabrw_untime_starttime']) ? $data['ovabrw_untime_starttime'] : '';
          update_post_meta( $post_id, 'ovabrw_untime_starttime', $ovabrw_untime_starttime );

          $ovabrw_untime_endtime = isset($data['ovabrw_untime_endtime']) ? $data['ovabrw_untime_endtime'] : '';
          update_post_meta( $post_id, 'ovabrw_untime_endtime', $ovabrw_untime_endtime );

          
          $ovabrw_pickup_location = isset($data['ovabrw_pickup_location']) ? $data['ovabrw_pickup_location'] : '';
          update_post_meta( $post_id, 'ovabrw_pickup_location', $ovabrw_pickup_location );

          $ovabrw_dropoff_location = isset($data['ovabrw_dropoff_location']) ? $data['ovabrw_dropoff_location'] : '';
          update_post_meta( $post_id, 'ovabrw_dropoff_location', $ovabrw_dropoff_location );

          $ovabrw_location_time = isset($data['ovabrw_location_time']) ? $data['ovabrw_location_time'] : '';
          update_post_meta( $post_id, 'ovabrw_location_time', $ovabrw_location_time );

          $ovabrw_price_location = isset($data['ovabrw_price_location']) ? $data['ovabrw_price_location'] : '';
          update_post_meta( $post_id, 'ovabrw_price_location', $ovabrw_price_location );

          $ovabrw_resource_name = isset($data['ovabrw_resource_name']) ? $data['ovabrw_resource_name'] : '';
          update_post_meta( $post_id, 'ovabrw_resource_name', $ovabrw_resource_name );
          
          $ovabrw_resource_id = isset($data['ovabrw_resource_id']) ? $data['ovabrw_resource_id'] : '';
          update_post_meta( $post_id, 'ovabrw_resource_id', $ovabrw_resource_id );
          
          $ovabrw_resource_price = isset($data['ovabrw_resource_price']) ? $data['ovabrw_resource_price'] : 0;
          update_post_meta( $post_id, 'ovabrw_resource_price', $ovabrw_resource_price );



          $ovabrw_resource_duration_type = isset($data['ovabrw_resource_duration_type']) ? $data['ovabrw_resource_duration_type'] : '';
          update_post_meta( $post_id, 'ovabrw_resource_duration_type', $ovabrw_resource_duration_type );

          $ovabrw_label_service = isset($data['ovabrw_label_service']) ? $data['ovabrw_label_service'] : [];
          update_post_meta( $post_id, 'ovabrw_label_service', $ovabrw_label_service );

          $ovabrw_service_required = isset($data['ovabrw_service_required']) ? $data['ovabrw_service_required'] : [];
          update_post_meta( $post_id, 'ovabrw_service_required', $ovabrw_service_required );

          $ovabrw_service_id = isset($data['ovabrw_service_id']) ? $data['ovabrw_service_id'] : [];
          update_post_meta( $post_id, 'ovabrw_service_id', $ovabrw_service_id );

          $ovabrw_service_name = isset($data['ovabrw_service_name']) ? $data['ovabrw_service_name'] : [];
          update_post_meta( $post_id, 'ovabrw_service_name', $ovabrw_service_name );

          $ovabrw_service_price = isset($data['ovabrw_service_price']) ? $data['ovabrw_service_price'] : [];
          update_post_meta( $post_id, 'ovabrw_service_price', $ovabrw_service_price );

          $ovabrw_service_duration_type = isset($data['ovabrw_service_duration_type']) ? $data['ovabrw_service_duration_type'] : [];
          update_post_meta( $post_id, 'ovabrw_service_duration_type', $ovabrw_service_duration_type );


          $ovabrw_rent_day_min = isset($data['ovabrw_rent_day_min']) ? $data['ovabrw_rent_day_min'] : '';
          if( !empty( $ovabrw_rent_day_min ) ){
            update_post_meta( $post_id, 'ovabrw_rent_day_min', $ovabrw_rent_day_min );  
          }else{
            update_post_meta( $post_id, 'ovabrw_rent_day_min', 1 );  
          }

          $ovabrw_rent_hour_min = isset($data['ovabrw_rent_hour_min']) ? $data['ovabrw_rent_hour_min'] : '';
          if( !empty( $ovabrw_rent_hour_min ) ){
            update_post_meta( $post_id, 'ovabrw_rent_hour_min', $ovabrw_rent_hour_min );  
          }else{
            update_post_meta( $post_id, 'ovabrw_rent_hour_min', 1 );  
          }

          $ovabrw_price_type = isset($data['ovabrw_price_type']) ? $data['ovabrw_price_type'] : '';
          if( !empty( $ovabrw_price_type ) ){
            update_post_meta( $post_id, 'ovabrw_price_type', $ovabrw_price_type );  
          }else{
            update_post_meta( $post_id, 'ovabrw_price_type', 'mixed' );  
          }

          $ovabrw_unfixed_time = isset($data['ovabrw_unfixed_time']) ? $data['ovabrw_unfixed_time'] : '';
          update_post_meta( $post_id, 'ovabrw_unfixed_time', $ovabrw_unfixed_time );

          $ovabrw_define_1_day = isset($data['ovabrw_define_1_day']) ? $data['ovabrw_define_1_day'] : '';
          update_post_meta( $post_id, 'ovabrw_define_1_day', $ovabrw_define_1_day );

          $ovabrw_features_desc = isset($data['ovabrw_features_desc']) ? $data['ovabrw_features_desc'] : '';
          update_post_meta( $post_id, 'ovabrw_features_desc', $ovabrw_features_desc );

          $ovabrw_features_label = isset($data['ovabrw_features_label']) ? $data['ovabrw_features_label'] : '';
          update_post_meta( $post_id, 'ovabrw_features_label', $ovabrw_features_label );

          $ovabrw_features_icons = isset($data['ovabrw_features_icons']) ? $data['ovabrw_features_icons'] : '';
          update_post_meta( $post_id, 'ovabrw_features_icons', $ovabrw_features_icons );

          $ovabrw_features_special = isset($data['ovabrw_features_special']) ? $data['ovabrw_features_special'] : '';
          update_post_meta( $post_id, 'ovabrw_features_special', $ovabrw_features_special );





          $ovabrw_video_link = isset($data['ovabrw_video_link']) ? $data['ovabrw_video_link'] : '';
          update_post_meta( $post_id, 'ovabrw_video_link', $ovabrw_video_link );
          
          $ovabrw_location = isset($data['ovabrw_location']) ? $data['ovabrw_location'] : '';
          update_post_meta( $post_id, 'ovabrw_location', $ovabrw_location );  


          $ovabrw_id_vehicles = isset($data['ovabrw_id_vehicles']) ? $data['ovabrw_id_vehicles'] : '';
          update_post_meta( $post_id, 'ovabrw_id_vehicles', $ovabrw_id_vehicles );  


          $ovabrw_petime_price = isset($data['ovabrw_petime_price']) ? $data['ovabrw_petime_price'] : '';
          update_post_meta( $post_id, 'ovabrw_petime_price', $ovabrw_petime_price );

          $ovabrw_petime_days = isset($data['ovabrw_petime_days']) ? $data['ovabrw_petime_days'] : '';
          update_post_meta( $post_id, 'ovabrw_petime_days', $ovabrw_petime_days );

          $ovabrw_petime_id  = isset($data['ovabrw_petime_id']) ? $data['ovabrw_petime_id'] : '';
          update_post_meta( $post_id, 'ovabrw_petime_id', $ovabrw_petime_id );

          $ovabrw_petime_label  = isset($data['ovabrw_petime_label']) ? $data['ovabrw_petime_label'] : '';
          update_post_meta( $post_id, 'ovabrw_petime_label', $ovabrw_petime_label );

          $ovabrw_petime_discount = isset($data['ovabrw_petime_discount']) ? $data['ovabrw_petime_discount'] :'';

          if ( is_array($ovabrw_petime_discount) && array_key_exists('ovabrw_key', $ovabrw_petime_discount)) {
            unset($ovabrw_petime_discount['ovabrw_key']);// Remove array has key is ovabrw_key 
          }
          update_post_meta( $post_id, 'ovabrw_petime_discount', $ovabrw_petime_discount );

          $ovabrw_package_type = isset($data['ovabrw_package_type']) ? $data['ovabrw_package_type'] : '';
          update_post_meta( $post_id, 'ovabrw_package_type', $ovabrw_package_type );

          $ovabrw_pehour_start_time = isset($data['ovabrw_pehour_start_time']) ? $data['ovabrw_pehour_start_time'] : '';
          update_post_meta( $post_id, 'ovabrw_pehour_start_time', $ovabrw_pehour_start_time );

          $ovabrw_pehour_end_time = isset($data['ovabrw_pehour_end_time']) ? $data['ovabrw_pehour_end_time'] : '';
          update_post_meta( $post_id, 'ovabrw_pehour_end_time', $ovabrw_pehour_end_time );

          $ovabrw_pehour_unfixed = isset($data['ovabrw_pehour_unfixed']) ? $data['ovabrw_pehour_unfixed'] : '';
          update_post_meta( $post_id, 'ovabrw_pehour_unfixed', $ovabrw_pehour_unfixed );

          $ovabrw_prepare_vehicle = isset($data['ovabrw_prepare_vehicle']) ? $data['ovabrw_prepare_vehicle'] : '';
          update_post_meta( $post_id, 'ovabrw_prepare_vehicle', $ovabrw_prepare_vehicle );

          $ovabrw_prepare_vehicle_day = isset($data['ovabrw_prepare_vehicle_day']) ? $data['ovabrw_prepare_vehicle_day'] : '';
          update_post_meta( $post_id, 'ovabrw_prepare_vehicle_day', $ovabrw_prepare_vehicle_day );

          $ovabrw_extra_tab_shortcode = isset($data['ovabrw_extra_tab_shortcode']) ? $data['ovabrw_extra_tab_shortcode'] : '';
          update_post_meta( $post_id, 'ovabrw_extra_tab_shortcode', $ovabrw_extra_tab_shortcode );

          $ovabrw_manage_extra_tab = isset($data['ovabrw_manage_extra_tab']) ? $data['ovabrw_manage_extra_tab'] : '';
          update_post_meta( $post_id, 'ovabrw_manage_extra_tab', $ovabrw_manage_extra_tab );

          $ovabrw_manage_time_book_start = isset($data['ovabrw_manage_time_book_start']) ? $data['ovabrw_manage_time_book_start'] : '';
          update_post_meta( $post_id, 'ovabrw_manage_time_book_start', $ovabrw_manage_time_book_start );

          $ovabrw_product_time_to_book_start = isset($data['ovabrw_product_time_to_book_start']) ? $data['ovabrw_product_time_to_book_start'] : '';
          update_post_meta( $post_id, 'ovabrw_product_time_to_book_start', $ovabrw_product_time_to_book_start );

          $ovabrw_manage_time_book_end = isset($data['ovabrw_manage_time_book_end']) ? $data['ovabrw_manage_time_book_end'] : '';
          update_post_meta( $post_id, 'ovabrw_manage_time_book_end', $ovabrw_manage_time_book_end );

          $ovabrw_product_time_to_book_end = isset($data['ovabrw_product_time_to_book_end']) ? $data['ovabrw_product_time_to_book_end'] : '';
          update_post_meta( $post_id, 'ovabrw_product_time_to_book_end', $ovabrw_product_time_to_book_end );

          $ovabrw_manage_default_hour_start = isset($data['ovabrw_manage_default_hour_start']) ? $data['ovabrw_manage_default_hour_start'] : '';
          update_post_meta( $post_id, 'ovabrw_manage_default_hour_start', $ovabrw_manage_default_hour_start );

          $ovabrw_product_default_hour_start = isset($data['ovabrw_product_default_hour_start']) ? $data['ovabrw_product_default_hour_start'] : '';
          update_post_meta( $post_id, 'ovabrw_product_default_hour_start', $ovabrw_product_default_hour_start );

          $ovabrw_manage_default_hour_end = isset($data['ovabrw_manage_default_hour_end']) ? $data['ovabrw_manage_default_hour_end'] : '';
          update_post_meta( $post_id, 'ovabrw_manage_default_hour_end', $ovabrw_manage_default_hour_end );

          $ovabrw_product_default_hour_end = isset($data['ovabrw_product_default_hour_end']) ? $data['ovabrw_product_default_hour_end'] : '';
          update_post_meta( $post_id, 'ovabrw_product_default_hour_end', $ovabrw_product_default_hour_end );

          $ovabrw_manage_custom_checkout_field = isset($data['ovabrw_manage_custom_checkout_field']) ? $data['ovabrw_manage_custom_checkout_field'] : '';
          update_post_meta( $post_id, 'ovabrw_manage_custom_checkout_field', $ovabrw_manage_custom_checkout_field );

          $ovabrw_product_custom_checkout_field = isset($data['ovabrw_product_custom_checkout_field']) ? $data['ovabrw_product_custom_checkout_field'] : '';
          update_post_meta( $post_id, 'ovabrw_product_custom_checkout_field', $ovabrw_product_custom_checkout_field );

          $ovabrw_show_pickup_location_product = isset($data['ovabrw_show_pickup_location_product']) ? $data['ovabrw_show_pickup_location_product'] : '';
          update_post_meta( $post_id, 'ovabrw_show_pickup_location_product', $ovabrw_show_pickup_location_product );

          $ovabrw_show_other_location_pickup_product = isset($data['ovabrw_show_other_location_pickup_product']) ? $data['ovabrw_show_other_location_pickup_product'] : '';
          update_post_meta( $post_id, 'ovabrw_show_other_location_pickup_product', $ovabrw_show_other_location_pickup_product );

          $ovabrw_show_pickoff_location_product = isset($data['ovabrw_show_pickoff_location_product']) ? $data['ovabrw_show_pickoff_location_product'] : '';
          update_post_meta( $post_id, 'ovabrw_show_pickoff_location_product', $ovabrw_show_pickoff_location_product );

          $ovabrw_show_other_location_dropoff_product = isset($data['ovabrw_show_other_location_dropoff_product']) ? $data['ovabrw_show_other_location_dropoff_product'] : '';
          update_post_meta( $post_id, 'ovabrw_show_other_location_dropoff_product', $ovabrw_show_other_location_dropoff_product );

          $ovabrw_show_pickup_date_product = isset($data['ovabrw_show_pickup_date_product']) ? $data['ovabrw_show_pickup_date_product'] : '';
          update_post_meta( $post_id, 'ovabrw_show_pickup_date_product', $ovabrw_show_pickup_date_product );

          $ovabrw_label_pickup_date_product = isset($data['ovabrw_label_pickup_date_product']) ? $data['ovabrw_label_pickup_date_product'] : '';
          update_post_meta( $post_id, 'ovabrw_label_pickup_date_product', $ovabrw_label_pickup_date_product );

          $ovabrw_new_pickup_date_product = isset($data['ovabrw_new_pickup_date_product']) ? $data['ovabrw_new_pickup_date_product'] : '';
          update_post_meta( $post_id, 'ovabrw_new_pickup_date_product', $ovabrw_new_pickup_date_product );

          $ovabrw_show_pickoff_date_product = isset($data['ovabrw_show_pickoff_date_product']) ? $data['ovabrw_show_pickoff_date_product'] : '';
          update_post_meta( $post_id, 'ovabrw_show_pickoff_date_product', $ovabrw_show_pickoff_date_product );

          $ovabrw_label_dropoff_date_product = isset($data['ovabrw_label_dropoff_date_product']) ? $data['ovabrw_label_dropoff_date_product'] : '';
          update_post_meta( $post_id, 'ovabrw_label_dropoff_date_product', $ovabrw_label_dropoff_date_product );

          $ovabrw_new_dropoff_date_product = isset($data['ovabrw_new_dropoff_date_product']) ? $data['ovabrw_new_dropoff_date_product'] : '';
          update_post_meta( $post_id, 'ovabrw_new_dropoff_date_product', $ovabrw_new_dropoff_date_product );

          $ovabrw_show_number_vehicle = isset($data['ovabrw_show_number_vehicle']) ? $data['ovabrw_show_number_vehicle'] : '';
          update_post_meta( $post_id, 'ovabrw_show_number_vehicle', $ovabrw_show_number_vehicle );
          

          $ovabrw_daily_monday = isset($data['ovabrw_daily_monday']) ? $data['ovabrw_daily_monday'] : '';
          update_post_meta( $post_id, 'ovabrw_daily_monday', $ovabrw_daily_monday );

          $ovabrw_daily_tuesday = isset($data['ovabrw_daily_tuesday']) ? $data['ovabrw_daily_tuesday'] : '';
          update_post_meta( $post_id, 'ovabrw_daily_tuesday', $ovabrw_daily_tuesday );

          $ovabrw_daily_wednesday = isset($data['ovabrw_daily_wednesday']) ? $data['ovabrw_daily_wednesday'] : '';
          update_post_meta( $post_id, 'ovabrw_daily_wednesday', $ovabrw_daily_wednesday );

          $ovabrw_daily_thursday = isset($data['ovabrw_daily_thursday']) ? $data['ovabrw_daily_thursday'] : '';
          update_post_meta( $post_id, 'ovabrw_daily_thursday', $ovabrw_daily_thursday );

          $ovabrw_daily_friday = isset($data['ovabrw_daily_friday']) ? $data['ovabrw_daily_friday'] : '';
          update_post_meta( $post_id, 'ovabrw_daily_friday', $ovabrw_daily_friday );

          $ovabrw_daily_saturday = isset($data['ovabrw_daily_saturday']) ? $data['ovabrw_daily_saturday'] : '';
          update_post_meta( $post_id, 'ovabrw_daily_saturday', $ovabrw_daily_saturday );

          $ovabrw_daily_sunday = isset($data['ovabrw_daily_sunday']) ? $data['ovabrw_daily_sunday'] : '';
          update_post_meta( $post_id, 'ovabrw_daily_sunday', $ovabrw_daily_sunday );

          // Google map
          $ovabrw_map_name  = isset($data['ovabrw_map_name']) ? $data['ovabrw_map_name'] : '';
          update_post_meta( $post_id, 'ovabrw_map_name', $ovabrw_map_name );

          $ovabrw_address   = isset($data['ovabrw_address']) ? $data['ovabrw_address'] : '';
          update_post_meta( $post_id, 'ovabrw_address', $ovabrw_address );

          $ovabrw_latitude  = isset($data['ovabrw_latitude']) ? $data['ovabrw_latitude'] : '';
          update_post_meta( $post_id, 'ovabrw_latitude', $ovabrw_latitude );

          $ovabrw_longitude = isset($data['ovabrw_longitude']) ? $data['ovabrw_longitude'] : '';
          update_post_meta( $post_id, 'ovabrw_longitude', $ovabrw_longitude );

        }


    

    }
}

new Ovabrw_Metabox();
