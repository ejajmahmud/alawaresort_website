<?php

if ( ! defined( 'ABSPATH' ) ) exit();

global $product;

$id = $product->get_id();

if ( $product->get_type() !== 'ovabrw_car_rental' ) return;

$ovabrw_resource_name = get_post_meta( $id, 'ovabrw_resource_name', true );

?>

<?php if( $ovabrw_resource_name ): 
	$ovabrw_resource_price 			= get_post_meta( $id, 'ovabrw_resource_price', true ); 
	$ovabrw_resource_duration_val 	= get_post_meta( $id, 'ovabrw_resource_duration_val', true ); 
	$ovabrw_resource_duration_type 	= get_post_meta( $id, 'ovabrw_resource_duration_type', true ); 
	$ovabrw_resource_id 			= get_post_meta( $id, 'ovabrw_resource_id', true ); 
?>
	<div class="ovabrw_extra_service">	
		<div class="ovabrw_resource">
			<?php foreach ( $ovabrw_resource_name as $key => $value ): 
				$ovabrw_resource_key = $ovabrw_resource_id[$key];
			?>
				<div class="item">
					<div class="left">
						<input 
							type="checkbox" 
							id="ovabrw_resource_checkboxs_bk_<?php echo esc_attr($key); ?>" 
							data-resource_key="<?php echo esc_attr( $ovabrw_resource_key ); ?>" 
							name="ovabrw_resource_checkboxs[<?php echo esc_attr( $ovabrw_resource_key ); ?>]" 
							value="<?php echo esc_attr( $ovabrw_resource_name[$key] ); ?>" 
							class="ovabrw_resource_checkboxs" />
						<span class="checkmark"></span>
						<label for="ovabrw_resource_checkboxs_bk_<?php echo esc_attr($key); ?>">
							<?php echo esc_html( $ovabrw_resource_name[$key] ); ?>
						</label>
					</div>
					<div class="right">
						<div class="resource">
							<span class="dur_price"><?php echo wc_price( $ovabrw_resource_price[$key] ); ?></span>
							<span class="slash">/</span>
							<span class="dur_val">
								<?php if ( $ovabrw_resource_duration_val != '' ) echo esc_html( $ovabrw_resource_duration_val[$key] ); ?>
							</span>
							<span class="dur_type">
								<?php
									if ( $ovabrw_resource_duration_type[$key] == 'hours' ) {
										esc_html_e( 'perhour', 'entox' );
									} else if ( $ovabrw_resource_duration_type[$key] == 'days' ) {
										esc_html_e( 'perday', 'entox' );
									} if ( $ovabrw_resource_duration_type[$key] == 'total' ) {
										esc_html_e( 'total', 'entox' );
									}
								?>
							</span>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
<?php endif; ?>