<?php
/**
 * The template for displaying extra fields in booking form within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/booking-form/extra_fields.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

// Get product_id from do_action - use when insert shortcode
if( isset( $args['product_id'] ) && $args['product_id'] ){
	$pid = $args['product_id'];
}else{
	$pid = get_the_id();
}


$list_ckf_output = ovabrw_get_list_field_checkout( $pid );

if( is_array( $list_ckf_output ) && ! empty( $list_ckf_output ) ) {

	foreach( $list_ckf_output as $key => $field ) {

		if( array_key_exists('enabled', $field) &&  $field['enabled'] == 'on' ) {

			if( array_key_exists('required', $field) &&  $field['required'] == 'on' ) {
				$class_required = 'required';
			} else {
				$class_required = '';
			}

	?>
		<div class="rental_item">
			<label><?php echo esc_html( $field['label'] ); ?></label>
			<div class="error_item">
				<label><?php esc_html_e( 'This field is required', 'ova-brw' ) ?></label>
			</div>

			<?php if( $field['type'] !== 'textarea' && $field['type'] !== 'select' ) { ?>
				<input type="<?php echo esc_attr( $field['type'] ); ?>"
				name="<?php echo esc_attr( $key ); ?>"
				class="<?php echo esc_attr( $field['class'].' '.$class_required ); ?>"
				placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
				value="<?php echo esc_attr( $field['default'] ); ?>"  />
			<?php } ?>

			<?php if( $field['type'] === 'textarea' ) { ?>
				<textarea name="<?php echo esc_attr( $key ); ?>"
					class="<?php echo esc_attr( $field['class'].' '.$class_required ); ?>"
					placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
					value="<?php echo esc_attr( $field['default'] ); ?>" cols="10" rows="5"></textarea>
			<?php } ?>

			<?php if( $field['type'] === 'select' ) { 

				$ova_options_key = $ova_options_text = [];
				if( array_key_exists( 'ova_options_key', $field ) ) {
					$ova_options_key = $field['ova_options_key'];
				}

				if( array_key_exists( 'ova_options_text', $field ) ) {
					$ova_options_text = $field['ova_options_text'];
				}
				

				?>
				<select name="<?php echo esc_attr( $key ); ?>" class="<?php echo esc_attr( $field['class'].' '.$class_required ); ?>" >
					<?php 

					if( ! empty( $ova_options_text ) && is_array( $ova_options_text ) ) { 
						foreach( $ova_options_text as $key => $value ) { 
							$selected = '';
							if( $ova_options_key[$key] == $field['default'] ) {
								$selected = 'selected';
							}
							?>
							<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $ova_options_key[$key] ); ?>"><?php echo esc_html( $value ); ?></option>
					<?php 

						} //end foreach
					}//end if
				?>
					
				</select>
			<?php } ?>

		</div>
	<?php
		}//endif
	}//end foreach
}//end if

?>