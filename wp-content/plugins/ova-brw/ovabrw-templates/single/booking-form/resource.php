<?php
/**
 * The template for displaying resource in booking form within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/booking-form/resource.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

	// Get product_id from do_action - use when insert shortcode
	if( isset( $args['product_id'] ) && $args['product_id'] ){
		$pid = $args['product_id'];
	}else{
		$pid = get_the_id();
	}
	

	$ovabrw_resource_name = get_post_meta( $pid, 'ovabrw_resource_name', true ); 
	if( $ovabrw_resource_name ){

		$ovabrw_resource_price = get_post_meta( $pid, 'ovabrw_resource_price', true ); 
		$ovabrw_resource_duration_val = get_post_meta( $pid, 'ovabrw_resource_duration_val', true ); 
		$ovabrw_resource_duration_type = get_post_meta( $pid, 'ovabrw_resource_duration_type', true ); 
		$ovabrw_resource_id = get_post_meta( $pid, 'ovabrw_resource_id', true ); 
?>
	<div class="ovabrw_extra_service">
		<label><?php esc_html_e( 'Resources', 'ova-brw' ); ?></label>
				
			<div class="ovabrw_resource">
				<?php foreach ($ovabrw_resource_name as $key => $value) { ?>
					<div class="item">
						
							<div class="left">
								<?php $ovabrw_resource_key = $ovabrw_resource_id[$key]; ?>
								<input type="checkbox" id="ovabrw_resource_checkboxs_bk_<?php echo esc_attr($key); ?>" data-resource_key="<?php echo esc_attr( $ovabrw_resource_key ); ?>" name="ovabrw_resource_checkboxs[<?php echo esc_attr( $ovabrw_resource_key ); ?>]" value="<?php echo esc_attr( $ovabrw_resource_name[$key] );  ?>" class="ovabrw_resource_checkboxs">
								<label for="ovabrw_resource_checkboxs_bk_<?php echo esc_attr($key); ?>"><?php echo esc_html( $ovabrw_resource_name[$key] ); ?></label>
							</div>
							<div class="right">
								<div class="resource">
									<span class="dur_price"><?php echo wp_kses_post( wc_price( $ovabrw_resource_price[$key] ) ); ?></span>
									<span class="slash">/</span>
									<span class="dur_val"><?php if ( $ovabrw_resource_duration_val != '' )echo esc_html( $ovabrw_resource_duration_val[$key] ); ?></span>
									<span class="dur_type">
										<?php
											if( $ovabrw_resource_duration_type[$key] == 'hours' ){
												esc_html_e( 'Hour', 'ova-brw' );
											}else if( $ovabrw_resource_duration_type[$key] == 'days' ){
												esc_html_e( 'Day', 'ova-brw' );
											}if( $ovabrw_resource_duration_type[$key] == 'total' ){
												esc_html_e( 'Total', 'ova-brw' );
											}
										?>
									</span>
								</div>
							</div>

					
					</div>
				<?php } ?>
			</div>
	</div>
<?php } ?>