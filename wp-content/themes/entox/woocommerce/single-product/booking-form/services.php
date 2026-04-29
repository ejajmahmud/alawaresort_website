<?php

if ( ! defined( 'ABSPATH' ) ) exit();

global $product;

$id = $product->get_id();

if ( $product->get_type() !== 'ovabrw_car_rental' ) return;
	
$ovabrw_label_service 			= get_post_meta( $id, 'ovabrw_label_service', true );
$ovabrw_service_required 		= get_post_meta( $id, 'ovabrw_service_required', true );
$ovabrw_service_id 				= get_post_meta( $id, 'ovabrw_service_id', true );
$ovabrw_service_name 			= get_post_meta( $id, 'ovabrw_service_name', true );
$ovabrw_service_price 			= get_post_meta( $id, 'ovabrw_service_price', true );
$ovabrw_service_duration_type 	= get_post_meta( $id, 'ovabrw_service_duration_type', true );

?>

<?php if( $ovabrw_label_service ): ?>
	<div class="ovabrw_service_wrap">
		<div class="row ovabrw_service">
			<?php for( $i = 0; $i < count( $ovabrw_label_service ); $i++ ): 
				$label_group_service 			= $ovabrw_label_service[$i];
				$ovabrw_service_required_item 	= isset( $ovabrw_service_required[$i] ) ? $ovabrw_service_required[$i] : '';
			?>
				<div class="ovabrw_service_select rental_item">
					<div class="error_item">
						<label><?php esc_html_e( 'This field is required', 'entox' ) ?></label>
					</div>
					<select name="ovabrw_service[]" id="" <?php if( $ovabrw_service_required_item == 'yes' ) echo 'class="required"'; ?> >
						<option value=""><?php printf( esc_html__( 'Select %s', 'entox' ), $label_group_service )  ?></option>
						<?php if( isset( $ovabrw_service_id[$i] ) && is_array( $ovabrw_service_id[$i] ) && ! empty( $ovabrw_service_id ) ): ?>
							<?php foreach( $ovabrw_service_id[$i] as $key => $val ): ?>
								<option value="<?php echo esc_attr( $val ) ?>">
									<?php echo esc_html( $ovabrw_service_name[$i][$key] ); ?>
								</option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>
			<?php endfor; ?>
		</div>
	</div>
<?php endif; ?>