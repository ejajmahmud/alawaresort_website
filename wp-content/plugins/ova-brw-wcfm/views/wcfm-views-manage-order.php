<?php
global $WCFM, $wp_query;

?>

<div class="collapse wcfm-collapse" id="wcfm_manage_order_listing">
	
	<div class="wcfm-page-headig">

		<span class="fa fa-cubes"></span>
		
		<span class="wcfm-page-heading-text">
			<?php esc_html_e( 'Manage Booking', 'ova-brw-wcfm' ); ?>
		</span>

		<?php do_action( 'wcfm_page_heading' ); ?>

	</div>

	<div class="wcfm-collapse-content">

		<div id="wcfm_page_load"></div>
		<?php do_action( 'before_wcfm_brw_manage_order' ); ?>
		
		

		<div class="wcfm-container">
			<div id="wcfm_manage_order_listing" class="wcfm-content">
			
				<?php ovabrw_wcfm_get_template( 'manager-order-form.php' ); ?>

				<div class="wcfm-container">
					<div id="wwcfm_orders_listing_expander" class="wcfm-content">
						<table id="wcfm-orders" class="display" cellspacing="0" width="100%">
							<thead>
								<tr>
									<?php

										$fileds = ovabrw_wcfm_get_fields_order();

										foreach ( $fileds as $value ) {
											$filed = ovabrw_wcfm_get_field_text( $value );
											echo $filed;
										}

									?>
								</tr>
							</thead>
							<tbody class="wcfm_order_list">
								<?php ovabrw_wcfm_get_template( 'manager-order-list.php' ); ?>
							</tbody>

							<tfoot>
								<tr>
									<?php
										foreach ( $fileds as $value ) {
											$filed = ovabrw_wcfm_get_field_text( $value );
											echo $filed;
										}
									?>
								</tr>
							</tfoot>
						</table>
						
						<div class="wcfm-clearfix"></div>
					</div>
				</div>
			
				<div class="wcfm-clearfix"></div>
			</div>
			<div class="wcfm-clearfix"></div>
		</div>
	
		<div class="wcfm-clearfix"></div>
		<?php
		do_action( 'after_wcfm_manage_order' );
		?>
	</div>
</div>




