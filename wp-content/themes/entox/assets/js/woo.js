(function($){
	"use strict";

	$('.entox-single-product').each( function() {
		var that = $(this);

		var special_offers 	= that.find('.special-offers');
		var special_price 	= that.find('.special-price');
		var close_popup 	= that.find('.close-popup');

		special_offers.on('click', function() {
			$(this).find('.special-price').css('display', 'block');
		});

		var view_discount 	= that.find('.view-discount-price');
		view_discount.on('click', function() {
			var discount_class = $(this).data('discount')
			var table_discount 	= $(this).closest('.boby-price').find('.'+discount_class);
			table_discount.slideToggle("slow");
		});

		var weekdays 		= that.find('.weekdays');
		var weekdays_price 	= that.find('.table-week-wrapper');
		weekdays.on('click', function() {
			$(this).find('.table-week-wrapper').css('display', 'block');
		});

		// window click
    	$(window).click( function(e) {
    		if ( e.target.className == 'special-price' || e.target.className == 'close-popup' ) {
    			special_price.hide();
    		}

    		if ( e.target.className == 'table-week-wrapper' || e.target.className == 'close-popup' ) {
    			weekdays_price.hide();
    		}
		});
	});

	// Product Thumbnail
	if ( $('.entox-thumbnails-product').length > 0 ) {
		$('.entox-thumbnails-product').each(function() {
			$(this).owlCarousel({
				autoplay: true,
				autoplayHoverPause: true,
				loop: false,
				margin: 30,
				dots: false,
				nav: true,
				responsive: {
					0:    {items: 1},
					350:  {items: 2},
					768:  {items: 3},
					900: {items: 4}
				}
			});
		});
	}

	// Product Location
	if ( $('.entox-product-location').length > 0 ) {
        // wait google map loaded
        let intervalId;
        if ( typeof google !== 'undefined' && typeof google.maps.places !== 'object' ) {
            intervalId = setInterval( entoxProductMap, 1000 );
        } else {
            entoxProductMap();
        }
        
        // Product Map
        function entoxProductMap() {
            if ( typeof google === 'object' && typeof google.maps.places === 'object' ) {
                clearInterval(intervalId);

                $('.entox-product-location').each(function() {
                    var that        = $(this);
                    var input       = $('#pac-input')[0];
                    var address     = that.find('.address');
                    var latitude    = address.attr('latitude');
                    var longitude   = address.attr('longitude');

                    
                    if ( typeof google !== 'undefined' && latitude && longitude ) {
                        var map = new google.maps.Map( $('#product-show-map')[0], {
                           center: {
                              lat: parseFloat(latitude),
                              lng: parseFloat(longitude)
                           },
                           zoom: 17,
                           gestureHandling: 'cooperative'
                        });

                        var autocomplete = new google.maps.places.Autocomplete(input);

                        autocomplete.bindTo('bounds', map);

                        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

                        var mapIWcontent = $('#pac-input').val();
                        var infowindow = new google.maps.InfoWindow({
                           content: mapIWcontent,
                        });

                        var marker = new google.maps.Marker({
                           map: map,
                           position: map.getCenter(),
                        });

                        marker.addListener('click', function() {
                           infowindow.open(map, marker);
                        });
                    }
                });
            }
        }
	}

	// Product Calendar
	$('.entox-calendar').each( function() {
		var that = $(this);

		var calendar = that.find('.product-calendar')[0];
      if( calendar === null ) return;

      var nav 				= calendar.getAttribute('data-nav');
      var default_view 	= calendar.getAttribute('data-default_view');
      var first_day 		= calendar.getAttribute('data-first-day');

      if ( ! first_day ) {
         first_day = 0;
      }

      if( default_view == 'month' ){
         default_view = 'dayGridMonth';
      }

      var calendar_lang 		= calendar.getAttribute( 'data-lang' ).replace(/\s/g, '');
      var define_day 			= calendar.getAttribute('data-define_day');
      var data_event_number 	= parseInt( calendar.getAttribute('data_event_number') );
      var default_hour_start 	= calendar.getAttribute( 'default_hour_start' );
      var price_calendar 		= calendar.getAttribute('price_calendar');
      var special_time 			= calendar.getAttribute('data-special-time');
      var special_time_value 	= JSON.parse( special_time );
      var price_calendar 		= calendar.getAttribute('price_calendar');
     	var price_full_calendar_value = JSON.parse( price_calendar );
      var background_day 		= calendar.getAttribute('data-background-day');
      var disable_week_day 	= calendar.getAttribute('data-disable_week_day');
      var disable_week_day_value = '';
      var event_display = calendar.getAttribute('data-event-display');

      if( disable_week_day ) {
         disable_week_day_value = JSON.parse( disable_week_day );
      }

      var events = '';
      var date_rent_full = [];
      var order_time = calendar.getAttribute('order_time');
      if ( order_time && order_time.length > 0 ) {
         events = JSON.parse( order_time );
      }

      if (typeof events !== 'undefined' && events.length > 0) {
         events.forEach(function(item, index){
		      if( item.hasOwnProperty('rendering') ) {
					date_rent_full.push( item.start );
		      }
         });
      }

      var srcCalendar = new FullCalendar.Calendar(calendar, {
         editable: false,
         events: events,
         firstDay: first_day,
         height: '100%',
         displayEventTime : false,
         eventDisplay: event_display,
         headerToolbar: {
            left: 'prev',
            center: 'title',
            right: 'next',
         },
         initialView: default_view,
         locale: calendar_lang,
         dayMaxEventRows: true,
         views: {
            dayGrid: {
               dayMaxEventRows: data_event_number
            },
            timeGrid: {
               dayMaxEventRows: data_event_number
            },
            week: {
               dayMaxEventRows: data_event_number
            },
            day: {
               dayMaxEventRows: data_event_number
            }
         },

         dayCellDidMount: function( e ){

            var new_date    = new Date( e.date );
            var time_stamp  = Date.UTC( new_date.getFullYear(), new_date.getMonth(), new_date.getDate() )/1000;
                
            if( price_full_calendar_value != '' ) {
               var type_price = price_full_calendar_value[0].type_price;
               if( type_price == 'day' ) {
                  var ovabrw_daily_monday = price_full_calendar_value[1].ovabrw_daily_monday;
                  var ovabrw_daily_tuesday = price_full_calendar_value[1].ovabrw_daily_tuesday;
                  var ovabrw_daily_wednesday = price_full_calendar_value[1].ovabrw_daily_wednesday;
                  var ovabrw_daily_thursday = price_full_calendar_value[1].ovabrw_daily_thursday;
                  var ovabrw_daily_friday = price_full_calendar_value[1].ovabrw_daily_friday;
                  var ovabrw_daily_saturday = price_full_calendar_value[1].ovabrw_daily_saturday;
                  var ovabrw_daily_sunday = price_full_calendar_value[1].ovabrw_daily_sunday;

                  switch( new_date.getDay() ) {
                     case 0 : {
                          // check disable week day in settings
                          	if ( disable_week_day_value ) {
                              $.each( disable_week_day_value, function( key, day_value ) {
                                  if( day_value == new_date.getDay() ) {
                                      e.el.children[0].className = e.el.children[0].className + ' unavailable_date';
                                      // set background day
                                      $('.unavailable_date').css('background-color', background_day);
                                  }
                              });
                          	}
                          
                          	let el = e.el.querySelectorAll(".fc-daygrid-day-bg")[0];
                          	if( el ){
                              if ( special_time_value ) {
                                  	el.innerHTML = ovabrw_daily_sunday;
                                  	$.each( special_time_value, function( price, special_timestamp ) {
                                      	if ( time_stamp >= special_timestamp[0] && time_stamp <= special_timestamp[1] ) {
                                          el.innerHTML = price;
                                      	}
                                  });
                              } 	else {
                                  	el.innerHTML = ovabrw_daily_sunday;
                              }
                              
                              return e;    
                          	}
                          
                          	break;
                     }
                     case 1 : {

                          // check disable week day in settings
                          if ( disable_week_day_value ) {
                              $.each( disable_week_day_value, function( key, day_value ) {
                                 if( day_value == new_date.getDay() ) {
                                    e.el.children[0].className = e.el.children[0].className + ' unavailable_date';
                                    // set background day
                                    $('.unavailable_date').css('background-color', background_day);
                                 }
                              });
                          }

                          let el = e.el.querySelectorAll(".fc-daygrid-day-bg")[0];
                          if( el ){
                              
                              if ( special_time_value ) {
                                  el.innerHTML = ovabrw_daily_monday;
                                  $.each( special_time_value, function( price, special_timestamp ) {
                                      if ( time_stamp >= special_timestamp[0] && time_stamp <= special_timestamp[1] ) {
                                          el.innerHTML = price;
                                      }
                                  });
                              } else {
                                  el.innerHTML = ovabrw_daily_monday;
                              }

                              return e;
                          }
                          
                          break;
                      }
                      case 2 : {

                          // check disable week day in settings
                          if ( disable_week_day_value ) {
                              $.each( disable_week_day_value, function( key, day_value ) {
                                  if( day_value == new_date.getDay() ) {
                                      e.el.children[0].className = e.el.children[0].className + ' unavailable_date';
                                      // set background day
                                      $('.unavailable_date').css('background-color', background_day);
                                  }
                              });
                          }

                          let el = e.el.querySelectorAll(".fc-daygrid-day-bg")[0];
                          if( el ){

                              if ( special_time_value ) {
                                  el.innerHTML = ovabrw_daily_tuesday;
                                  $.each( special_time_value, function( price, special_timestamp ) {
                                      if ( time_stamp >= special_timestamp[0] && time_stamp <= special_timestamp[1] ) {
                                          el.innerHTML = price;
                                      }
                                  });
                              } else {
                                  el.innerHTML = ovabrw_daily_tuesday;
                              }
                              
                              return e;
                          }
                          break;
                      }
                      case 3 : {

                          // check disable week day in settings
                          if ( disable_week_day_value ) {
                              $.each( disable_week_day_value, function( key, day_value ) {
                                  if( day_value == new_date.getDay() ) {
                                      e.el.children[0].className = e.el.children[0].className + ' unavailable_date';
                                      // set background day
                                      $('.unavailable_date').css('background-color', background_day);
                                  }
                              });
                          }

                          let el = e.el.querySelectorAll(".fc-daygrid-day-bg")[0];
                          if( el ){

                              if ( special_time_value ) {
                                  el.innerHTML = ovabrw_daily_wednesday;
                                  $.each( special_time_value, function( price, special_timestamp ) {
                                      if ( time_stamp >= special_timestamp[0] && time_stamp <= special_timestamp[1] ) {
                                          el.innerHTML = price;
                                      }
                                  });
                              } else {
                                  el.innerHTML = ovabrw_daily_wednesday;
                              }
                              
                              return e;
                          }
                          break;
                      }
                      case 4 : {

                          // check disable week day in settings
                          if ( disable_week_day_value ) {
                              $.each( disable_week_day_value, function( key, day_value ) {
                                  if( day_value == new_date.getDay() ) {
                                      e.el.children[0].className = e.el.children[0].className + ' unavailable_date';
                                      // set background day
                                      $('.unavailable_date').css('background-color', background_day);
                                  }
                              });
                          }

                          let el = e.el.querySelectorAll(".fc-daygrid-day-bg")[0];
                          if( el ){

                              if ( special_time_value ) {
                                  el.innerHTML = ovabrw_daily_thursday;
                                  $.each( special_time_value, function( price, special_timestamp ) {
                                      if ( time_stamp >= special_timestamp[0] && time_stamp <= special_timestamp[1] ) {
                                          el.innerHTML = price;
                                      }
                                  });
                              } else {
                                  el.innerHTML = ovabrw_daily_thursday;
                              }

                              return e;
                          }
                          break;
                      }
                      case 5 : {

                          // check disable week day in settings
                          if ( disable_week_day_value ) {
                              $.each( disable_week_day_value, function( key, day_value ) {
                                  if( day_value == new_date.getDay() ) {
                                      e.el.children[0].className = e.el.children[0].className + ' unavailable_date';
                                      // set background day
                                      $('.unavailable_date').css('background-color', background_day);
                                  }
                              });
                          }

                          let el = e.el.querySelectorAll(".fc-daygrid-day-bg")[0];
                          if( el ){

                              if ( special_time_value ) {
                                  el.innerHTML = ovabrw_daily_friday;
                                  $.each( special_time_value, function( price, special_timestamp ) {
                                      if ( time_stamp >= special_timestamp[0] && time_stamp <= special_timestamp[1] ) {
                                          el.innerHTML = price;;
                                          return e;
                                      }
                                  });
                              } else {
                                  el.innerHTML = ovabrw_daily_friday;
                              }
                              
                              return e;
                          }
                          break;
                      }
                      case 6 : {

                          // check disable week day in settings
                          if ( disable_week_day_value ) {
                              $.each( disable_week_day_value, function( key, day_value ) {
                                  if( day_value == new_date.getDay() ) {
                                      e.el.children[0].className = e.el.children[0].className + ' unavailable_date';
                                      // set background day
                                      $('.unavailable_date').css('background-color', background_day);
                                  }
                              });
                          }

                          let el = e.el.querySelectorAll(".fc-daygrid-day-bg")[0];
                          if( el ){

                              if ( special_time_value ) {
                                  el.innerHTML = ovabrw_daily_saturday;
                                  $.each( special_time_value, function( price, special_timestamp ) {
                                      if ( time_stamp >= special_timestamp[0] && time_stamp <= special_timestamp[1] ) {
                                          el.innerHTML = price;
                                      }
                                  });
                              } else {
                                  el.innerHTML = ovabrw_daily_saturday;
                              }
                              
                              return e;
                          }
                          break;
                      }
                  }

               } else if( type_price == 'hour' ) {

	               // check disable week day in settings
	               if ( disable_week_day_value ) {
	                   $.each( disable_week_day_value, function( key, day_value ) {
	                       if( day_value == new_date.getDay() ) {
	                           e.el.children[0].className = e.el.children[0].className + ' unavailable_date';
	                           // set background day
	                           $('.unavailable_date').css('background-color', background_day);
	                       }
	                   });
	               }

                  var ovabrw_price_hour = price_full_calendar_value[1].ovabrw_price_hour;
                  let el = e.el.querySelectorAll(".fc-daygrid-day-bg")[0];
                  if( el ){
                      if ( special_time_value ) {
                          el.innerHTML = ovabrw_price_hour;
                          $.each( special_time_value, function( price, special_timestamp ) {
                              if ( time_stamp >= special_timestamp[0] && time_stamp <= special_timestamp[1] ) {
                                  el.innerHTML = price;
                              }
                          });
                      } else {
                          el.innerHTML = ovabrw_price_hour;
                      }
                      
                      return e;
                  }
              	} else if( type_price == 'mixed' ) {
                  var ovabrw_daily_monday = price_full_calendar_value[1].ovabrw_daily_monday;
                  var ovabrw_daily_tuesday = price_full_calendar_value[1].ovabrw_daily_tuesday;
                  var ovabrw_daily_wednesday = price_full_calendar_value[1].ovabrw_daily_wednesday;
                  var ovabrw_daily_thursday = price_full_calendar_value[1].ovabrw_daily_thursday;
                  var ovabrw_daily_friday = price_full_calendar_value[1].ovabrw_daily_friday;
                  var ovabrw_daily_saturday = price_full_calendar_value[1].ovabrw_daily_saturday;
                  var ovabrw_daily_sunday = price_full_calendar_value[1].ovabrw_daily_sunday;

                  switch( new_date.getDay() ) {
                      case 0 : {

                          // check disable week day in settings
                          if ( disable_week_day_value ) {
                              $.each( disable_week_day_value, function( key, day_value ) {
                                  if( day_value == new_date.getDay() ) {
                                      e.el.children[0].className = e.el.children[0].className + ' unavailable_date';
                                      // set background day
                                      $('.unavailable_date').css('background-color', background_day);
                                  }
                              });
                          }
                          
                          let el = e.el.querySelectorAll(".fc-daygrid-day-bg")[0];
                          if( el ){
                              if ( special_time_value ) {
                                  el.innerHTML = ovabrw_daily_sunday;
                                  $.each( special_time_value, function( price, special_timestamp ) {
                                      if ( time_stamp >= special_timestamp[0] && time_stamp <= special_timestamp[1] ) {
                                          el.innerHTML = price;
                                      }
                                  });
                              } else {
                                  el.innerHTML = ovabrw_daily_sunday;
                              }
                              
                              return e;    
                          }
                          
                          break;
                      }
                      case 1 : {

                          // check disable week day in settings
                          if ( disable_week_day_value ) {
                              $.each( disable_week_day_value, function( key, day_value ) {
                                  if( day_value == new_date.getDay() ) {
                                      e.el.children[0].className = e.el.children[0].className + ' unavailable_date';
                                      // set background day
                                      $('.unavailable_date').css('background-color', background_day);
                                  }
                              });
                          }

                          let el = e.el.querySelectorAll(".fc-daygrid-day-bg")[0];
                          if( el ){
                              
                              if ( special_time_value ) {
                                  el.innerHTML = ovabrw_daily_monday;
                                  $.each( special_time_value, function( price, special_timestamp ) {
                                      if ( time_stamp >= special_timestamp[0] && time_stamp <= special_timestamp[1] ) {
                                          el.innerHTML = price;
                                      }
                                  });
                              } else {
                                  el.innerHTML = ovabrw_daily_monday;
                              }

                              return e;
                          }
                          
                          break;
                      }
                      case 2 : {

                          // check disable week day in settings
                          if ( disable_week_day_value ) {
                              $.each( disable_week_day_value, function( key, day_value ) {
                                  if( day_value == new_date.getDay() ) {
                                      e.el.children[0].className = e.el.children[0].className + ' unavailable_date';
                                      // set background day
                                      $('.unavailable_date').css('background-color', background_day);
                                  }
                              });
                          }

                          let el = e.el.querySelectorAll(".fc-daygrid-day-bg")[0];
                          if( el ){

                              if ( special_time_value ) {
                                  el.innerHTML = ovabrw_daily_tuesday;
                                  $.each( special_time_value, function( price, special_timestamp ) {
                                      if ( time_stamp >= special_timestamp[0] && time_stamp <= special_timestamp[1] ) {
                                          el.innerHTML = price;
                                      }
                                  });
                              } else {
                                  el.innerHTML = ovabrw_daily_tuesday;
                              }
                              
                              return e;
                          }
                          break;
                      }
                      case 3 : {

                          // check disable week day in settings
                          if ( disable_week_day_value ) {
                              $.each( disable_week_day_value, function( key, day_value ) {
                                  if( day_value == new_date.getDay() ) {
                                      e.el.children[0].className = e.el.children[0].className + ' unavailable_date';
                                      // set background day
                                      $('.unavailable_date').css('background-color', background_day);
                                  }
                              });
                          }

                          let el = e.el.querySelectorAll(".fc-daygrid-day-bg")[0];
                          if( el ){

                              if ( special_time_value ) {
                                  el.innerHTML = ovabrw_daily_wednesday;
                                  $.each( special_time_value, function( price, special_timestamp ) {
                                      if ( time_stamp >= special_timestamp[0] && time_stamp <= special_timestamp[1] ) {
                                          el.innerHTML = price;
                                      }
                                  });
                              } else {
                                  el.innerHTML = ovabrw_daily_wednesday;
                              }
                              
                              return e;
                          }
                          break;
                      }
                      case 4 : {

                          // check disable week day in settings
                          if ( disable_week_day_value ) {
                              $.each( disable_week_day_value, function( key, day_value ) {
                                  if( day_value == new_date.getDay() ) {
                                      e.el.children[0].className = e.el.children[0].className + ' unavailable_date';
                                      // set background day
                                      $('.unavailable_date').css('background-color', background_day);
                                  }
                              });
                          }

                          let el = e.el.querySelectorAll(".fc-daygrid-day-bg")[0];
                          if( el ){

                              if ( special_time_value ) {
                                  el.innerHTML = ovabrw_daily_thursday;
                                  $.each( special_time_value, function( price, special_timestamp ) {
                                      if ( time_stamp >= special_timestamp[0] && time_stamp <= special_timestamp[1] ) {
                                          el.innerHTML = price;
                                      }
                                  });
                              } else {
                                  el.innerHTML = ovabrw_daily_thursday;
                              }

                              return e;
                          }
                          break;
                      }
                      case 5 : {

                          // check disable week day in settings
                          if ( disable_week_day_value ) {
                              $.each( disable_week_day_value, function( key, day_value ) {
                                  if( day_value == new_date.getDay() ) {
                                      e.el.children[0].className = e.el.children[0].className + ' unavailable_date';
                                      // set background day
                                      $('.unavailable_date').css('background-color', background_day);
                                  }
                              });
                          }

                          let el = e.el.querySelectorAll(".fc-daygrid-day-bg")[0];
                          if( el ){

                              if ( special_time_value ) {
                                  el.innerHTML = ovabrw_daily_friday;
                                  $.each( special_time_value, function( price, special_timestamp ) {
                                      if ( time_stamp >= special_timestamp[0] && time_stamp <= special_timestamp[1] ) {
                                          el.innerHTML = price;;
                                          return e;
                                      }
                                  });
                              } else {
                                  el.innerHTML = ovabrw_daily_friday;
                              }
                              
                              return e;
                          }
                          break;
                      }
                      case 6 : {

                          // check disable week day in settings
                          if ( disable_week_day_value ) {
                              $.each( disable_week_day_value, function( key, day_value ) {
                                  if( day_value == new_date.getDay() ) {
                                      e.el.children[0].className = e.el.children[0].className + ' unavailable_date';
                                      // set background day
                                      $('.unavailable_date').css('background-color', background_day);
                                  }
                              });
                          }

                          let el = e.el.querySelectorAll(".fc-daygrid-day-bg")[0];
                          if( el ){

                              if ( special_time_value ) {
                                  el.innerHTML = ovabrw_daily_saturday;
                                  $.each( special_time_value, function( price, special_timestamp ) {
                                      if ( time_stamp >= special_timestamp[0] && time_stamp <= special_timestamp[1] ) {
                                          el.innerHTML = price;
                                      }
                                  });
                              } else {
                                  el.innerHTML = ovabrw_daily_saturday;
                              }
                              
                              return e;
                          }
                          break;
                      }
                  }
             	}
            } else {
               var type_price = calendar.getAttribute('type_price');
               // period_time
               if( type_price == 'period_time' || type_price == 'transportation' ) {
                  // check disable week day in settings
                  if ( disable_week_day_value ) {
                      $.each( disable_week_day_value, function( key, day_value ) {
                          if( day_value == new_date.getDay() ) {
                              e.el.children[0].className = e.el.children[0].className + ' unavailable_date';
                              // set background day
                              $('.unavailable_date').css('background-color', background_day);
                          }
                      });
                  }
                  
                  return e;
               }
             }
      	},

     	});

      srcCalendar.render();

	});

	// Tabs
	$('.forms-tab').each( function() {
		var that = $(this);

		var item    = that.find('.tabs .item');
    var booking = that.find('.entox-booking');

		item.on( 'click', function() {
			if ( ! $(this).hasClass('active') ) {
				item.removeClass('active');
				$(this).addClass('active');
        booking.hide();
        var form = $(this).data('form');
        that.find('.'+$(this).data('form')).show();
			}
		});
	});

  // My Account
  $('.entox-login-register-woo li a').on('click', function(){
    var type = $(this).data('type');
    $('.entox-login-register-woo li').removeClass('active');
    $(this).parent('li').addClass('active');
    if( type === 'login' ){
      $('.woocommerce #customer_login .woocommerce-form.woocommerce-form-login').css('display', 'block');
      $('.woocommerce #customer_login .woocommerce-form.woocommerce-form-register').css('display', 'none');
    } else if( type === 'register' ){
      $('.woocommerce #customer_login .woocommerce-form.woocommerce-form-register').css('display', 'block');
      $('.woocommerce #customer_login .woocommerce-form.woocommerce-form-login').css('display', 'none');
    }
  });


})(jQuery);