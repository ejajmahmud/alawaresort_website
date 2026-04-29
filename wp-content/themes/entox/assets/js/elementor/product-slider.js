(function($){
	"use strict";
	

	$(window).on('elementor/frontend/init', function () {
        
		
		/* Product Slider */
		elementorFrontend.hooks.addAction('frontend/element_ready/entox_elementor_product_slider.default', function(){
			$(".ova-product-slider .content-product-slider").each(function(){
		        var owlsl = $(this) ;
		        var owlsl_ops = owlsl.data('options') ? owlsl.data('options') : {};

		        var responsive_value = {
		            0:{
		              items:1,
		              nav:false
		            },
		            768:{
		              items:2
		            },
		            1024:{
		              items:3
		            },
		            1400:{
		              items:owlsl_ops.items
		            }
		        };
		        
		        owlsl.owlCarousel({
					autoWidth: owlsl_ops.autoWidth,
					margin: owlsl_ops.margin,
					items: owlsl_ops.items,
					loop: owlsl_ops.loop,
					autoplay: owlsl_ops.autoplay,
					autoplayTimeout: owlsl_ops.autoplayTimeout,
					center: owlsl_ops.center,
					nav: owlsl_ops.nav,
					dots: owlsl_ops.dots,
					thumbs: owlsl_ops.thumbs,
					autoplayHoverPause: owlsl_ops.autoplayHoverPause,
					slideBy: owlsl_ops.slideBy,
					smartSpeed: owlsl_ops.smartSpeed,
					navText:[
						'<i class="' + owlsl_ops.previous + '" ></i>',
		          		'<i class="' + owlsl_ops.next + '" ></i>'
		          	],
		          	responsive: responsive_value,
		        });
		    });

		});
		/* end Product Slider */
   });

})(jQuery);
