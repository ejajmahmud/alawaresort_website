<div class="ajax_show_total">

	<div class="ajax_loading"></div>

	<div class="show_ajax_content">
		<?php esc_html_e( 'Total:', 'ova-brw' ); ?>&nbsp;
		<span class="show_total"></span>
		<?php if ( get_option( 'ova_brw_booking_form_show_availables_vehicle', 'yes' ) == 'yes' ): ?>
			<?php esc_html_e( 'Available: ', 'ova-brw' ); ?>
			<span class="show_availables_vehicle"></span>
		<?php endif; ?>
	</div>

</div>