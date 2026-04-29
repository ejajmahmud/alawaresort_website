<?php

if ( ! defined( 'ABSPATH' ) ) exit();

global $product;

$id = $product->get_id();

if ( $product->get_type() !== 'ovabrw_car_rental' ) return;

$list_ckf_output = ovabrw_get_list_field_checkout( $id );

?>

<?php if ( is_array( $list_ckf_output ) && ! empty( $list_ckf_output ) ): ?>

	<?php foreach( $list_ckf_output as $key => $field ): ?>

		<?php if( array_key_exists('enabled', $field) &&  $field['enabled'] == 'on' ): 

			if( array_key_exists('required', $field) &&  $field['required'] == 'on' ) {
				$class_required = 'required';
			} else {
				$class_required = '';
			}
		?>
		<div class="rental_item">
			<div class="error_item">
				<label><?php esc_html_e( 'This field is required', 'entox' ) ?></label>
			</div>

			<?php if ( $field['type'] !== 'textarea' && $field['type'] !== 'select' ): ?>
				<input 
					type="<?php echo esc_attr( $field['type'] ) ?>" 
					name="<?php echo esc_attr( $key ) ?>" 
					class=" <?php echo esc_attr( $field['class'] ) . ' ' . $class_required ?>" 
					placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" 
					value="<?php echo esc_attr( $field['default'] ); ?>" />
			<?php endif; ?>

			<?php if ( $field['type'] === 'textarea' ): ?>
				<textarea 
					name="<?php echo esc_attr( $key ) ?>" 
					class=" <?php echo esc_attr( $field['class'] ) . ' ' . $class_required ?>" 
					placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" 
					value="<?php echo esc_attr( $field['default'] ); ?>" cols="10" rows="5"></textarea>
			<?php endif; ?>

			<?php if ( $field['type'] === 'select' ):  
				$ova_options_key = $ova_options_text = [];

				if ( array_key_exists( 'ova_options_key', $field ) ) {
					$ova_options_key = $field['ova_options_key'];
				}

				if ( array_key_exists( 'ova_options_text', $field ) ) {
					$ova_options_text = $field['ova_options_text'];
				}
			?>
				<select name="<?php echo esc_attr( $key ); ?>" class=" <?php echo esc_attr( $field['class'] ) . ' ' . $class_required; ?>">
					<?php if( ! empty( $ova_options_text ) && is_array( $ova_options_text ) ): ?>
						<?php foreach( $ova_options_text as $key => $value ): 
							$selected = '';
							if( $ova_options_key[$key] == $field['default'] ) {
								$selected = 'selected';
							}
						?>
							<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $ova_options_key[$key] ); ?>">
								<?php echo esc_html( $value ); ?>
							</option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>