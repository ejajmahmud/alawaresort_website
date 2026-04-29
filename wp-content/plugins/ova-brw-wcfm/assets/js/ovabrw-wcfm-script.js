(function($){
	"use strict";

	 var Brw_WCFM_Admin = {

        init: function () {

        	// Display help
        	this.brw_wcfm_tip();

        	// Show/Hide custom taxonomy by category
        	this.brw_wcfm_cus_tax_show_hide();

        	// show/hide field with rental type: rental
        	this.brw_wcfm_field_add_product();

        	// Manage order: update order status
        	this.brw_wcfm_update_order_status();

        	// Popup items
        	this.brw_wcfm_popup_items();

        	// Pagination ajax manage order
        	this.brw_wcfm_pagination_manage_order();

        },

        brw_wcfm_tip: function(){

        	/* Customize Tip when hover help */
			$('.ovabrw_metabox_car_rental .woocommerce-help-tip').mouseover(function(e){
				var tip = $(this).data('tip');
				$(this).append('<span class="content">'+tip+'</span>');
			});

			$('.ovabrw_metabox_car_rental .woocommerce-help-tip').mouseout(function(e){
				$(this).html('');
			});

        },

        brw_wcfm_cus_tax_show_hide: function(){

        	/* Custom Taxonomy Show/Hide with Category In Backend when add new product */
	    	/**************************************************************************/
			/* Hide all custom taxonomy in add new product */
			

			
		    /* Hide all custom taxonomy in add new product */
		    ovabrw_wcfm_hide_cus_tax( 'loaded' );

		    /* Change category */
		    if( $( '#product_cats_checklist input' ).length ){
		        $( '#product_cats_checklist input' ).on( 'change', function( e ){

		            var that = $(this);
		            that.attr("disabled", 'disabled');

		            /* Hide all custom taxonomy in add new product */
		            ovabrw_wcfm_hide_cus_tax('changed');

		            var checked_tax = ovabrw_wcfm_getSelectedCheckboxValues('product_cats[]');

		            $.ajax({
		                url: ajax_object.ajax_url,
		                type: 'POST',
		                data: ({
		                    action: 'ovabrw_wcfm_get_custom_tax_in_cat',
		                    checked_tax: checked_tax,
		                   
		                }),
		                success: function(response){

		                    that.removeAttr("disabled");

		                    if( response ){

		                        var list_tax_values = response.split(",");

		                        if( list_tax_values.length ){
			                        for (var i = 0; i < list_tax_values.length; i++) {
			                            if( $( ".wcfm_product_taxonomy_"+list_tax_values[i] ).length > 0 ){
			                                $( ".wcfm_product_taxonomy_"+list_tax_values[i] ).show();
			                            }
			                            
			                        }
		                    	}

		                    }
		                    
		                    
		                },
		            });

		            

		        } );
		    }
        },

        brw_wcfm_field_add_product: function(){

        	/* Show/Hide field when change Rental Type *********************************/
	    	/**************************************************************************/

	    	var label_price_print = 'Price';
			 if( typeof label_price !== 'undefined' ){
		        label_price_print = label_price;
		    }


	    	if( $('.wcfm_product_type').length ){
				var value = $('.wcfm_product_type').val();

	    		if( value != 'ovabrw_car_rental' ){
	    			$('.wcfm_product_type').closest('form').find('.ovabrw_metabox_car_rental').hide();
	    			$( '.wcfm_product_manager_general_fields .regular_price strong' ).first().html('').append( '<span> '+label_price_print+'</span>' );
	    			
	    			$( '.wcfm_product_manager_general_fields .wcfm_ele.sales_schedule' ).removeClass('hide');
	    			$( '.wcfm_product_manager_general_fields #sale_price' ).show();
	    			$( '.wcfm_product_manager_general_fields .sale_price' ).show();

	    			// Show Inventory default in WooCommerce
	    			$( '#wcfm_products_manage_form_inventory_expander .manage_stock' ).show();
	    			$( '#wcfm_products_manage_form_inventory_expander #manage_stock' ).show();

	    			$( '#wcfm_products_manage_form_inventory_expander .stock_status' ).show();
	    			$( '#wcfm_products_manage_form_inventory_expander #stock_status' ).show();

	    			$( '#wcfm_products_manage_form_inventory_expander .sold_individually' ).show();
	    			$( '#wcfm_products_manage_form_inventory_expander #sold_individually' ).show();

	    			
	    			

	    		}else{

	    			$('.wcfm_product_type').closest('form').find('.ovabrw_metabox_car_rental').show();

	    			$( '.wcfm_product_manager_general_fields .wcfm_ele.sales_schedule' ).addClass('hide');
	    			$( '.wcfm_product_manager_general_fields #sale_price' ).hide();
	    			$( '.wcfm_product_manager_general_fields .sale_price' ).hide();

	    			// Hide Inventory default in WooCommerce
	    			$( '#wcfm_products_manage_form_inventory_expander .manage_stock' ).hide();
	    			$( '#wcfm_products_manage_form_inventory_expander #manage_stock' ).hide();

	    			$( '#wcfm_products_manage_form_inventory_expander .stock_status' ).hide();
	    			$( '#wcfm_products_manage_form_inventory_expander #stock_status' ).hide();

	    			$( '#wcfm_products_manage_form_inventory_expander .sold_individually' ).hide();
	    			$( '#wcfm_products_manage_form_inventory_expander #sold_individually' ).hide();

	    			
	    			

	    		}    		
	    	}

	    	$('.wcfm_product_type').on( 'change', function(){

	    		var val = $(this).val();
	    		var form = $(this).closest('form');

	    		

	    		if( val != 'ovabrw_car_rental' ){
	    			form.find('.ovabrw_metabox_car_rental').hide();

	    			if( val == 'simple'){

	    				form.find( '#is_catalog' ).css('display', 'inline-block');
		    			form.find( '.virtual_ele_title' ).css('display', 'inline-block');
		    			form.find( '#is_virtual' ).css('display', 'inline-block');
		    			form.find( '#is_downloadable' ).css('display', 'inline-block');
		    			form.find( '.downloadable_ele_title' ).css('display', 'inline-block');

		    			$( '.wcfm_product_manager_general_fields .regular_price strong' ).first().html('').append( '<span> '+label_price_print+'</span>' );

		    			$( '.wcfm_product_manager_general_fields .wcfm_ele.sales_schedule' ).removeClass('hide');
		    			$( '.wcfm_product_manager_general_fields #sale_price' ).show();
	    				$( '.wcfm_product_manager_general_fields .sale_price' ).show();

	    				$( '.wcfm_product_manager_general_fields .sales_schedule_ele_show' ).removeClass('hide');
		    			$( '.wcfm_product_manager_general_fields #sale_date_from' ).removeClass('hide');
		    			$( '.wcfm_product_manager_general_fields .sale_date_upto' ).removeClass('hide');
		    			$( '.wcfm_product_manager_general_fields #sale_date_upto' ).removeClass('hide');

		    			// Show Inventory default in WooCommerce
		    			$( '#wcfm_products_manage_form_inventory_expander .manage_stock' ).show();
		    			$( '#wcfm_products_manage_form_inventory_expander #manage_stock' ).show();

		    			$( '#wcfm_products_manage_form_inventory_expander .stock_status' ).show();
		    			$( '#wcfm_products_manage_form_inventory_expander #stock_status' ).show();

		    			$( '#wcfm_products_manage_form_inventory_expander .sold_individually' ).show();
		    			$( '#wcfm_products_manage_form_inventory_expander #sold_individually' ).show();

	    			}else if( val == 'variable' ){

	    				form.find( '#is_catalog' ).css('display', 'inline-block');
		    			form.find( '.virtual_ele_title' ).css('display', 'inline-block');
		    			form.find( '#is_virtual' ).hide();
		    			form.find( '#is_downloadable' ).hide();
		    			form.find( '.downloadable_ele_title' ).hide();

		    			// Show Inventory default in WooCommerce
		    			$( '#wcfm_products_manage_form_inventory_expander .manage_stock' ).show();
		    			$( '#wcfm_products_manage_form_inventory_expander #manage_stock' ).show();

		    			$( '#wcfm_products_manage_form_inventory_expander .stock_status' ).show();
		    			$( '#wcfm_products_manage_form_inventory_expander #stock_status' ).show();

		    			$( '#wcfm_products_manage_form_inventory_expander .sold_individually' ).show();
		    			$( '#wcfm_products_manage_form_inventory_expander #sold_individually' ).show();

	    			}else if( val == 'grouped' ){

	    				form.find( '#is_catalog' ).hide();
		    			form.find( '.virtual_ele_title' ).hide();
		    			form.find( '#is_virtual' ).hide();
		    			form.find( '#is_downloadable' ).hide();
		    			form.find( '.downloadable_ele_title' ).hide();

		    			// Show Inventory default in WooCommerce
		    			$( '#wcfm_products_manage_form_inventory_expander .stock_status' ).show();
		    			$( '#wcfm_products_manage_form_inventory_expander #stock_status' ).show();

		    			

	    			}else if( val == 'external' ){

	    				form.find( '#is_catalog' ).hide();
		    			form.find( '.virtual_ele_title' ).hide();
		    			form.find( '#is_virtual' ).hide();
		    			form.find( '#is_downloadable' ).hide();
		    			form.find( '.downloadable_ele_title' ).hide();
	    				
	    			}
	    		}else{
	    			form.find('.ovabrw_metabox_car_rental').show();

	    			form.find( '#is_catalog' ).hide();
	    			form.find( '.virtual_ele_title' ).hide();
	    			form.find( '#is_virtual' ).hide();
	    			form.find( '#is_downloadable' ).hide();
	    			form.find( '.downloadable_ele_title' ).hide();

	    			var label_regular_per_day_print = 'Regular Price / Day';
	    			 if( typeof label_regular_per_day !== 'undefined' ){
				        label_regular_per_day_print = label_regular_per_day;
				    }

	    			$( '.wcfm_product_manager_general_fields .regular_price strong' ).first().html('').append( '<span> '+label_regular_per_day_print+'</span>' );
	    			$( '.wcfm_product_manager_general_fields .wcfm_ele.sales_schedule' ).addClass('hide');
	    			$( '.wcfm_product_manager_general_fields #sale_price' ).hide();
	    			$( '.wcfm_product_manager_general_fields .sale_price' ).hide();

	    			$( '.wcfm_product_manager_general_fields .sales_schedule_ele_show' ).addClass('hide');
	    			$( '.wcfm_product_manager_general_fields #sale_date_from' ).addClass('hide');
	    			$( '.wcfm_product_manager_general_fields .sale_date_upto' ).addClass('hide');
	    			$( '.wcfm_product_manager_general_fields #sale_date_upto' ).addClass('hide');
	    			
	    			// Hide Inventory default in WooCommerce
	    			$( '#wcfm_products_manage_form_inventory_expander .manage_stock' ).hide();
	    			$( '#wcfm_products_manage_form_inventory_expander #manage_stock' ).hide();

	    			$( '#wcfm_products_manage_form_inventory_expander .stock_status' ).hide();
	    			$( '#wcfm_products_manage_form_inventory_expander #stock_status' ).hide();

	    			$( '#wcfm_products_manage_form_inventory_expander .sold_individually' ).hide();
	    			$( '#wcfm_products_manage_form_inventory_expander #sold_individually' ).hide();
	    			
	    		}

	    	} );
        },

        brw_wcfm_update_order_status: function(){

        	$('.update_manage_order_status').on('change', function(e){
        
                var order_status_text = $(this).children("option:selected").html();
                var new_order_status_val = $(this).children("option:selected").val();

                var prompt_ask = confirm("Do you want to "+order_status_text+' Order?');

                var that = $(this);

                if (prompt_ask == true && new_order_status_val != '') {

                    var order_id = that.data('order_id');

                    var user_permission 		= that.data('user_permistion');
                    var new_order_status_class 	= that.children("option:selected").val().replace( 'wc-', '' );
                    var new_order_status_text 	= that.children("option:selected").text();

                    var order_status_place 		= that.closest('.odd').find('.order-status');
                    var order_status_place_text = that.closest('.odd').find('.order-status span');
                    
                    var data = {
                        'action': 'ovabrw_wcfm_update_manage_order_status',
                        'order_id': order_id,
                        'new_order_status': new_order_status_val,
                        'user_permission': user_permission
                    };

                    $.ajax({
						url: ajax_object.ajax_url,
						type: 'POST',
						data: data,
						success: function( response ) {
							if( response === 'error_permission' ){

	                            alert( that.data('error-per-msg') );

	                        }else if( response === 'false' ){

	                            alert( that.data('error-update-msg') );

	                        }else{

	                            order_status_place.attr('class', '');
	                            order_status_place.addClass('order-status tips status-' + new_order_status_class);
	                            order_status_place_text.html(new_order_status_text);
	                        }
						},
					});  
                }
                e.preventDefault();
            });
        },

        brw_wcfm_popup_items: function(){

        	$( '.show_order_items' ).on( 'click', function(){
        		$( '.items_popup' ).css( 'display', 'none' );
        		$(this).closest('.items_name').children('.items_popup').toggle('show');
        	});

        	$( '.close' ).on( 'click', function(){
    			$(this).closest('.items_content').closest('.items_popup').css( 'display', 'none' );
    		});
        },

        brw_wcfm_pagination_manage_order: function() {

        	$( '.pagination_manage_order .page-numbers li .page-numbers' ).on( 'click', function(){

        		var that 	  = $(this);
        		
        		// Search
        		var order_id  = $('.booking_filter input[name="order_number"]').val();
        		var customer  = $('.booking_filter input[name="name_customer"]').val();
        		var from_day  = $('.booking_filter input[name="from_day"]').val();
        		var to_day 	  = $('.booking_filter input[name="to_day"]').val();

        		var check     = $('.booking_filter select[name="check_in_out"]').val();
    			var pickup 	  = $('.booking_filter select[name="pickup_loc"]').val();
    			var pickoff	  = $('.booking_filter select[name="pickoff_loc"]').val();
    			var room_id	  = $('.booking_filter select[name="room_id"]').val();
    			var status 	  = $('.booking_filter select[name="filter_order_status"]').val();


        		// Pagination

        		var old_paged = $('.pagination_manage_order .page-numbers li .current').text();
        		var new_paged = '';

        		// prev , next
        		var class_name 	= that.data('class_name');

        		if ( class_name == 'prev' ) {
        			new_paged 	= parseInt(old_paged) - 1;
        		} else if ( class_name == 'next' ) {
        			new_paged 	= parseInt(old_paged) + 1;
        		} else {
        			new_paged = that.text();
        		}

        		if ( old_paged != new_paged ) {

        			var old_current = $('.pagination_manage_order .page-numbers li.current');
        			var total_paged = $('.pagination_manage_order .page-numbers').data('total_pages');

        			if ( new_paged > 1 ) {
        				$('.pagination_manage_order .page-numbers li .prev').css('display', 'block');
        			} else {
        				$('.pagination_manage_order .page-numbers li .prev').css('display', 'none');
        			}

        			if ( new_paged == total_paged ) {
        				$('.pagination_manage_order .page-numbers li .next').css('display', 'none');
        			} else {
        				$('.pagination_manage_order .page-numbers li .next').css('display', 'block');
        			}

        			var page_numbers = $( '.pagination_manage_order .page-numbers li .page-numbers' );

        			page_numbers.removeClass( 'current' );

        			if ( class_name == 'next' ) {
        				old_current.next().children('.page-numbers').addClass( 'current' );
        				old_current.next().addClass( 'current' );
        				old_current.removeClass( 'current' );
        			} else if ( class_name == 'prev' ) {
        				old_current.prev().children('.page-numbers').addClass( 'current' );
        				old_current.prev().addClass( 'current' );
        				old_current.removeClass( 'current' );
        			} else {
        				old_current.next().children('.page-numbers').addClass( 'current' );
        				page_numbers.removeClass( 'current' );
        				that.addClass( 'current' );
        				that.closest('li').addClass( 'current' );
        				old_current.removeClass( 'current' );
        			}

        			var data = {
        				'action'			 : 'ovabrw_wcfm_pagination_manage_order',
        				'order_number'		 : order_id,
        				'name_customer'		 : customer,
        				'check_in_out'		 : check,
        				'from_day'			 : from_day,
        				'to_day'			 : to_day,
        				'pickup_loc'		 : pickup,
        				'pickoff_loc'		 : pickoff,
        				'room_id'			 : room_id,
        				'filter_order_status': status,
        				'new_paged'			 : new_paged,
        			};

        			$.ajax({
						url: ajax_object.ajax_url,
						type: 'POST',
						data: data,
						success: function( response ) {
							var items = $( '.wcfm_order_list' );
							items.html( response );
							Brw_WCFM_Admin.brw_wcfm_popup_items();
							Brw_WCFM_Admin.brw_wcfm_update_order_status();
						},
					});
        		}
        	});
        },

    };


	$(document).ready(function(){

		Brw_WCFM_Admin.init();

	});/* Doc ready */


	function ovabrw_wcfm_hide_cus_tax( action ){

		if( action == 'loaded' ){

			if( typeof cus_tax_hide_p_loaded != 'undefined' ){

                var array_tax_slug_new = cus_tax_hide_p_loaded.split(",");
                for (var i = 0; i < array_tax_slug_new.length; i++) {
                    if( $( '.wcfm_product_taxonomy_'+array_tax_slug_new[i] ).length ){
                        $( '.wcfm_product_taxonomy_'+array_tax_slug_new[i] ).hide();    
                    }
                    
                }
            }

		}else if( action == 'changed' ){

			if( typeof all_cus_tax != 'undefined' ){

	            var array_tax_slug_new = all_cus_tax.split(",");
	            for (var i = 0; i < array_tax_slug_new.length; i++) {
	                if( $( '.wcfm_product_taxonomy_'+array_tax_slug_new[i] ).length ){
	                    $( '.wcfm_product_taxonomy_'+array_tax_slug_new[i] ).hide();    
	                }
	                
	            }
	        }

		}
        

    }
    

    function ovabrw_wcfm_getSelectedCheckboxValues(name) {
        const checkboxes = document.querySelectorAll(`input[name="${name}"]:checked`);
        let values = [];
        checkboxes.forEach((checkbox) => {
            values.push(checkbox.value);
        });
        return values;
    }


}) (jQuery); /* /function($) */   