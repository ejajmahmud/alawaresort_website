(function($){
	"use strict";
	

	$(window).on('elementor/frontend/init', function () {
        
		elementorFrontend.hooks.addAction('frontend/element_ready/entox_elementor_pricing_tabs.default', function(){
			$(".ova-pricing-tabs").each( function() {
				var that 	= $(this);
				var item 	= that.find('.item');
				var active 	= that.find('.active-tabs');

				item.on( 'click', function() {
					item.removeClass('active-tabs');
					$(this).addClass('active-tabs');
					var id 	= $(this).data('id');
					$(".pricing-item").removeClass('active_package');
					$("#"+id).addClass('active_package');
				});
			});

		});
   });

})(jQuery);
