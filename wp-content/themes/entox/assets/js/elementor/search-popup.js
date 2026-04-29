(function($){
	"use strict";
	

	$(window).on('elementor/frontend/init', function () {
        
		
		/* Search Popup */
		elementorFrontend.hooks.addAction('frontend/element_ready/entox_elementor_search_popup.default', function(){
			$( '.ova_wrap_search_popup i' ).on( 'click', function(){
				$( this ).closest( '.ova_wrap_search_popup' ).addClass( 'show' );
			});

			$( '.btn_close' ).on( 'click', function(){
				$( this ).closest( '.ova_wrap_search_popup' ).removeClass( 'show' );

			});
		});
		/* end Search Popup */



   });

})(jQuery);
