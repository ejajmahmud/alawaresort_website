<?php
/**
 * The template for displaying service in booking form within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/booking-form/services.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

	// Get product_id from do_action - use when insert shortcode
	if( isset( $args['product_id'] ) && $args['product_id'] ){
		$pid = $args['product_id'];
	}else{
		$pid = get_the_id();
	}
	

	$ovabrw_label_service = get_post_meta( $pid, 'ovabrw_label_service', true );
	$ovabrw_service_required = get_post_meta( $pid, 'ovabrw_service_required', true );

	$ovabrw_service_id = get_post_meta( $pid, 'ovabrw_service_id', true );
	$ovabrw_service_name = get_post_meta( $pid, 'ovabrw_service_name', true );
	$ovabrw_service_price = get_post_meta( $pid, 'ovabrw_service_price', true );
	$ovabrw_service_duration_type = get_post_meta( $pid, 'ovabrw_service_duration_type', true );
	?>
	<?php
		if( $ovabrw_label_service ) {
	?>
	<div class="ovabrw_service_wrap">
		<label><?php esc_html_e( 'Services', 'ova-brw' ); ?></label>
		<div class="row ovabrw_service">
			<?php
					for( $i = 0; $i < count( $ovabrw_label_service ); $i++ ){
						$label_group_service = $ovabrw_label_service[$i];
						$ovabrw_service_required_item = isset( $ovabrw_service_required[$i] ) ? $ovabrw_service_required[$i] : '';
						$required_class = $ovabrw_service_required_item == 'yes' ? 'required' : '';
						?>
						<div class="ovabrw_service_select rental_item">
							<div class="error_item">
								<label><?php esc_html_e( 'This field is required', 'ova-brw' ) ?></label>
							</div>
							<select name="ovabrw_service[]" id="" class="<?php echo esc_attr( $required_class ); ?>" >
								<option value="">
									<?php printf( esc_html__( 'Select %s', 'ova-brw' ), esc_html( $label_group_service ) ); ?>
								</option>
								<?php
								if( isset( $ovabrw_service_id[$i] ) && is_array( $ovabrw_service_id[$i] ) && ! empty( $ovabrw_service_id ) ){
									foreach( $ovabrw_service_id[$i] as $key => $val ) {
										?>
											<option value="<?php echo esc_attr( $val ); ?>">
												<?php echo esc_html( $ovabrw_service_name[$i][$key] ); ?>
											</option>
										<?php
									}
								}
								?>
							</select>
						</div>
						<?php
					}
					?>

		</div>
	</div>
	<?php
	}
?>