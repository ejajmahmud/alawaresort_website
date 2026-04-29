<?php if ( !defined( 'ABSPATH' ) ) exit();

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class List_Booking extends WP_List_Table {

    function __construct(){

        global $page;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'bookings',     //singular name of the listed records
            'plural'    => 'bookings',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }

    function column_default($item, $column_name){
        switch($column_name){
            case 'id':
            case 'customer':
            case 'check-in-check-out':
            case 'pickup-pickoff-loc':
            case 'status-deposit':
            case 'status-insurance':
            case 'room-code':
            case 'room-name':
            case 'order_status':
            case 'order_id':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }

    function column_order_status($item){

        switch ($item['order_status']) {
            case 'processing':
                $order_status_text = esc_html__( 'Processing', 'ova-brw' );
                break;
            case 'completed':
                $order_status_text = esc_html__( 'Completed', 'ova-brw' );
                break;
            case 'on-hold':
                $order_status_text = esc_html__( 'On hold', 'ova-brw' );
                break;
            case 'cancelled':
                $order_status_text = esc_html__( 'Cancel', 'ova-brw' );
                break;    

            case 'closed':
                $order_status_text = esc_html__( 'Closed', 'ova-brw' );
                break;        
            
            default:
                $order_status_text = esc_html__( 'Update Order', 'ova-brw' );
                break;
        }
        
        //Build row actions
        $selected_action = sprintf( '<select ="update_order_status" class="update_order_status" data-order_id="'.$item['id'].'" data-error-per-msg="'.esc_html__( 'You don\'t have permission to update', 'ova-brw' ).'" data-error-update-msg="'.esc_html__( 'Error Update', 'ova-brw' ).'" >
            <option value="">'.esc_html__( 'Update Status', 'ova-brw' ).'</option>
            <option value="wc-completed">'.esc_html__( 'Completed', 'ova-brw' ).'</option>
            <option value="wc-processing">'.esc_html__( 'Processing', 'ova-brw' ).'</option>
            <option value="wc-on-hold">'.esc_html__( 'On hold', 'ova-brw' ).'</option>
            <option value="wc-cancelled">'.esc_html__( 'Cancel', 'ova-brw' ).'</option>
            <option value="wc-closed">'.esc_html__( 'Closed', 'ova-brw' ).'</option>
        </select>' );

        return sprintf('<span>%1$s</span>%2$s',
            '<mark class="order-status status-'.$item['order_status'].' tips"><span>'.$order_status_text.'</span></mark>',
            $selected_action
        );
    }

    function column_id($item){
    	//Build row actions
        if( current_user_can( apply_filters( 'ovabrw_edit_order_woo_cap' ,'manage_options' ) ) ){
            return sprintf('<span>%1$s</span>',
                '<a target="_blank" href="'.admin_url('/post.php?post='.$item['id'].'&action=edit').'">'.$item['id'].'</a>'
            );
        }else{
            return sprintf( '<span>%1$s</span>', $item['id'] );
        }
    	
    }

    function get_columns(){

        $options = $columns = array();

        $id         = get_option( 'admin_manage_order_show_id', 1 );
        if ( $id ) {
            $options['id'] = $id;
        }

        $customer   = get_option( 'admin_manage_order_show_customer', 2 );
        if ( $customer ) {
            $options['customer'] = $customer;
        }

        $time       = get_option( 'admin_manage_order_show_time', 3 );
        if ( $time ) {
            $options['check-in-check-out'] = $time;
        }

        $location   = get_option( 'admin_manage_order_show_location', 4 );
        if ( $location ) {
            $options['pickup-pickoff-loc'] = $location;
        }

        $deposit    = get_option( 'admin_manage_order_show_deposit', 5 );
        if ( $deposit ) {
            $options['status-deposit'] = $deposit;
        }

        $insurance  = get_option( 'admin_manage_order_show_insurance', 6 );
        if ( $insurance ) {
            $options['status-insurance'] = $insurance;
        }

        $vehicle    = get_option( 'admin_manage_order_show_vehicle', 7 );
        if ( $vehicle ) {
            $options['room-code'] = $vehicle;
        }

        $product    = get_option( 'admin_manage_order_show_product', 8 );
        if ( $product ) {
            $options['room-name'] = $product;
        }

        $status     = get_option( 'admin_manage_order_show_order_status', 9 );
        if ( $status ) {
            $options['order_status'] = $status;
        }

        if ( $options ) {
            asort( $options );
        }

        foreach ( $options as $key => $value ) {
            
            switch ( $key ) {

                case 'id':
                    $columns[$key] = esc_html__( 'Order ID','ova-brw' );
                    break;

                case 'customer':
                    $columns[$key] = esc_html__( 'Customer Name','ova-brw' );
                    break;

                case 'check-in-check-out':
                    $columns[$key] = esc_html__( 'Check-in - Check-out','ova-brw' );
                    break;
                case 'pickup-pickoff-loc':
                    $columns[$key] = esc_html__( 'Pick-up, Drop-off Location','ova-brw' );
                    break;

                case 'status-deposit':
                    $columns[$key] = esc_html__( 'Deposit Status','ova-brw' );
                    break;

                case 'status-insurance':
                    $columns[$key] = esc_html__( 'Insurance Status','ova-brw' );
                    break;

                case 'room-code':
                    $columns[$key] = esc_html__( 'Vehicle','ova-brw' );
                    break;

                case 'room-name':
                    $columns[$key] = esc_html__( 'Product','ova-brw' );
                    break;

                case 'order_status':
                    $columns[$key] = esc_html__( 'Order Status','ova-brw' );
                    break;
                
                default:
                    break;
            }
        }

        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'id'    		=> array('id',true),
            'customer' 		=> array('customer',false),
            'check-in-check-out'     	=> array('check-in-check-out',false),  //true means it's already sorted
            'pickup-pickoff-loc'      => array('pickup-pickoff-loc',false),  //true means it's already sorted
            'status-deposit'      => array('status-deposit',false),  
            'status-insurance'		=> array('status-insurance',false),  
            'room-code'     => array('room-code',false),  //true means it's already sorted
            'room-name'		=> array('room-name',false),  //true means it's already sorted
            'order_status'  		=> array('order_status',false),
            
        );
        return $sortable_columns;
    }

    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = '20';
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $data = array();

        $filter_order_status = isset( $_POST['filter_order_status'] ) ? sanitize_text_field( $_POST['filter_order_status'] ) : '';

        $order_status = array();

        if( ! empty( $filter_order_status ) ){
            $order_status = array($filter_order_status);
        }else{
            $order_status = array( 'wc-processing','wc-completed', 'wc-half-completed', 'wc-on-hold', 'wc-cancelled', 'wc-closed' );    
        }

        $order_number   = isset( $_POST['order_number'] ) ? (int)( $_POST['order_number'] ) : '';
        $name_customer  = isset( $_POST['name_customer'] ) ? sanitize_text_field( $_POST['name_customer'] ) : '';

        if ( ovabrw_wc_custom_orders_table_enabled() ) {
            $room_id = isset( $_POST['room_id'] ) ? sanitize_text_field( $_POST['room_id'] ) : '';
            $order_status_placeholders = implode( ', ', array_fill( 0, count( $order_status ), '%s' ) );
            

            $prepare_value = array_merge( array( 'line_item', '_product_id' ), $order_status );

            $result = $wpdb->get_col( $wpdb->prepare("
                SELECT DISTINCT o.id
                FROM {$wpdb->prefix}wc_orders AS o
                LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oitems
                ON o.id = oitems.order_id
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                ON oitems.order_item_id = oitem_meta.order_item_id
                WHERE oitems.order_item_type = %s
                AND oitem_meta.meta_key = %s
                AND o.status IN ( $order_status_placeholders )", $prepare_value ) );

            if ( $room_id ) {


                $prepare_value = array_merge(array('line_item', '_product_id'), $order_status_placeholders, array($room_id) );

                $result = $wpdb->get_col( $wpdb->prepare("
                SELECT DISTINCT o.id
                FROM {$wpdb->prefix}wc_orders AS o
                LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oitems
                ON o.id = oitems.order_id
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                ON oitems.order_item_id = oitem_meta.order_item_id
                WHERE oitems.order_item_type = %s
                AND oitem_meta.meta_key = %s
                AND o.status IN ( $order_status_placeholders ) AND oitem_meta.meta_value = %s", $prepare_value) );
            }
            

        } else {
            $room_id = isset( $_POST['room_id'] ) ? sanitize_text_field( $_POST['room_id'] ) : '';

            $order_status_placeholders = implode( ', ', array_fill( 0, count( $order_status ), '%s' ) );

            $prepare_value = array_merge( array( 'shop_order', 'line_item', '_product_id' ) ,$order_status );

            $result = $wpdb->get_col( $wpdb->prepare("
                SELECT DISTINCT oitems.order_id
                FROM {$wpdb->prefix}woocommerce_order_items AS oitems
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                ON oitems.order_item_id = oitem_meta.order_item_id
                LEFT JOIN {$wpdb->posts} AS posts ON oitems.order_id = posts.ID
                WHERE posts.post_type = %s
                AND oitems.order_item_type = %s
                AND oitem_meta.meta_key = %s
                AND posts.post_status IN ( $order_status_placeholders )", $prepare_value ) );
            if ( $room_id ) {

                $prepare_value = array_merge( array( 'shop_order', 'line_item', '_product_id' ), $order_status, array($room_id) );

                $result = $wpdb->get_col( $wpdb->prepare("
                SELECT DISTINCT oitems.order_id
                FROM {$wpdb->prefix}woocommerce_order_items AS oitems
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                ON oitems.order_item_id = oitem_meta.order_item_id
                LEFT JOIN {$wpdb->posts} AS posts ON oitems.order_id = posts.ID
                WHERE posts.post_type = %s
                AND oitems.order_item_type = %s
                AND oitem_meta.meta_key = %s
                AND posts.post_status IN ( $order_status_placeholders ) AND oitem_meta.meta_value = %s", $prepare_value ) );
            }

        }

        $room_code_filter = isset( $_POST['room_code'] ) ? sanitize_text_field( $_POST['room_code'] ) : '';

	    $from_day = isset( $_POST['from_day'] ) ? strtotime( $_POST['from_day'] ) : '';
    	$to_day = isset( $_POST['to_day'] ) ? strtotime( $_POST['to_day'] ) : '';
	    
	    $check_in_out = isset( $_POST['check_in_out'] ) ? sanitize_text_field( $_POST['check_in_out'] ) : '';
        $check = $check_in_flag = $check_out_flag = $pickup_loc_flag = $pickoff_loc_flag = true;

        $current_pickup_loc = isset( $_POST['pickup_loc'] ) ? sanitize_text_field( $_POST['pickup_loc'] ) : '';
        $current_pickoff_loc = isset( $_POST['pickoff_loc'] ) ? sanitize_text_field( $_POST['pickoff_loc'] ) : '';

        $ovabrw_date_format = ovabrw_get_date_format();
        $ovabrw_time_format = ovabrw_get_time_format_php();

        
        $ovabrw_date_time_format = $ovabrw_date_format . ' ' . $ovabrw_time_format;


	    foreach ($result as $key => $value) {
            $order = wc_get_order($value);
            $fullname = $order->get_formatted_billing_full_name();

            if( ( empty( $order_number ) || ( ! empty( $order_number ) && $order_number == $value  ) ) && ( empty( $name_customer ) || ( strpos($fullname, $name_customer) ) !== false ) ) {

    	        // Get Order Detail by Order ID
    	        $order = wc_get_order($value);
                $fullname = $order->get_formatted_billing_full_name();

    	        // Get Meta Data type line_item of Order
    	        $order_line_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );

    	        // For Meta Data
    	        foreach ( $order_line_items as $item_id => $item ) {

    	            $room_check_in = $room_check_out = $room_code = $pickup_loc = $pickoff_loc = '';
    	            $room_name = $item->get_name();
   	            
                    // Get value of check-in, check-out
    	            foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {

                        if( $meta->key == 'ovabrw_pickup_date' ){
                            $room_check_in = wp_date( $ovabrw_date_time_format, strtotime( $meta->value ) );
                        }
                        if( $meta->key == 'ovabrw_pickoff_date' ){
                            $room_check_out = wp_date( $ovabrw_date_time_format, strtotime( $meta->value ) );
                        }

    	                
    	                if( $meta->key == 'id_vehicle' ){
    	                    
    	                    if( $room_code_filter != '' && $room_code_filter == $meta->value ){
    	                    	$room_code = $meta->value;
    	                    }else if( $room_code_filter == '' ){
    	                    	$room_code = $meta->value;
    	                    }
    	                }

                        if( $meta->key == 'ovabrw_pickup_loc' ){
                            $pickup_loc = $meta->value;
                        }
                        if( $meta->key == 'ovabrw_pickoff_loc' ){
                            $pickoff_loc = $meta->value;
                        }

    	            }

                    $room_check_in_timep = strtotime( $room_check_in );
                    $room_check_out_timep = strtotime( $room_check_out );

                    if( $check_in_out == 'check_in' && $from_day != '' && $to_day != '' ){
                        $check = ( $from_day <=  $room_check_in_timep && $room_check_in_timep <= $to_day ) ? true : false;
                    }else if( $check_in_out == 'check_out' ){
                        $check = ( $from_day <=  $room_check_out_timep &&  $room_check_out_timep <= $to_day ) ? true : false;
                    }else if( $from_day != '' && $to_day != '' ){
                        $check_in_flag = ( $from_day <=  $room_check_in_timep && $room_check_in_timep <= $to_day ) ? true : false;
                        $check_out_flag = ( $from_day <=  $room_check_out_timep &&  $room_check_out_timep <= $to_day ) ? true : false;
                    }

                    if( $current_pickup_loc != '' && $pickup_loc != $current_pickup_loc ){
                        $pickup_loc_flag = false;
                    }else{
                        $pickup_loc_flag = true;
                    }

                    if( $current_pickoff_loc != '' && $pickoff_loc != $current_pickoff_loc ){
                        $pickoff_loc_flag = false;
                    }else{
                        $pickoff_loc_flag = true;
                    }

                    $order_amount_insurance = $item['ovabrw_amount_insurance_product'];
                    if ($order_amount_insurance == 0) {
                        $status_insurance = '<span class="ova_amount_status_insurance_column ova_paid">'.esc_html__("Paid for Customers", "ova-brw").'</span>';
                    } else {
                        $status_insurance = '<span class="ova_amount_status_insurance_column ova_pending">'.esc_html__("Received", "ova-brw").'</span>';
                    }
                    $order_amount_remaining = $item['ovabrw_remaining_amount_product'];
                    if ($order_amount_remaining == 0) {
                        $status_deposit = '<span class="ova_amount_status_insurance_column ova_paid">'.esc_html__("Full Payment", "ova-brw").'</span>';
                    } else {
                        $status_deposit = '<span class="ova_amount_status_insurance_column ova_pending">'.esc_html__("Partial Payment", "ova-brw").'</span>';
                    }

                    
                    if( empty( $pickup_loc ) && empty( $pickoff_loc ) ) {
                        $pickup_off_loc =  '';
                    } else {
                        $pickup_off_loc =  $pickup_loc . ' @ ' . $pickoff_loc;
                    }


    	            if(  $check && $check_in_flag && $check_out_flag && $pickup_loc_flag && $pickoff_loc_flag ){
    	            	$data[] = array(
    		                'id'            => $order->get_ID(),
    		                'customer'     	=> $order->get_formatted_billing_full_name(),
    		                'check-in-check-out'      => $room_check_in . ' @ ' . $room_check_out,
                            'pickup-pickoff-loc'      => $pickup_off_loc,
                            'status-deposit'      => $status_deposit,
                            'status-insurance'      => $status_insurance,
    		                'room-code'		=> $room_code,
    		                'room-name'		=> $room_name,
    		                'order_status'   => $order->get_status(),
    		                
    		            );
    	            }
    	            
    	            
    	        }
    	    }
        }


        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'id'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? sanitize_text_field( $_REQUEST['order'] ) : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? -$result : $result; //Send final sort direction to usort
        }    
        usort($data, 'usort_reorder');

        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
       
        $this->items = $data;
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }

}
