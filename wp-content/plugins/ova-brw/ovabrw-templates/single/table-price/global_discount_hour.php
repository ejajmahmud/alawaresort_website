<?php
/**
 * The template for displaying global discount content within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/table-price/global_discount.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

	// Get product_id from do_action - use when insert shortcode
	if( isset( $args['product_id'] ) && $args['product_id'] ){
		$pid = $args['product_id'];
	}else{
		$pid = get_the_id();
	}

	$gd_duration_type = get_post_meta( $pid, 'ovabrw_global_discount_duration_type', true );
	$gd_duration_val_min = get_post_meta( $pid, 'ovabrw_global_discount_duration_val_min', true );
	$gd_duration_val_max = get_post_meta( $pid, 'ovabrw_global_discount_duration_val_max', true );

	if( $gd_duration_val_min ) asort( $gd_duration_val_min );
	if( $gd_duration_val_max ) asort( $gd_duration_val_max );

	$gd_price = get_post_meta( $pid, 'ovabrw_global_discount_price', true );

	$title_text = esc_html__( 'Min - Max (Hours)', 'ova-brw' );
	$price_text = esc_html__( 'Price/Hour', 'ova-brw' ); 

	?>

	<?php if( !empty( $gd_duration_val_min ) || !empty( $gd_duration_val_max ) ){ ?>
		<div class="price_table">
			
			<label><?php esc_html_e( 'Global Discount', 'ova-brw' ); ?></label>

			<table>

				<thead>

					<tr>

						<th><?php echo esc_html( $title_text ); ?></th>

						<th><?php echo esc_html( $price_text ); ?></th>

					</tr>

				</thead>

				<tbody>

					<?php
						$k = 0;
						foreach ($gd_duration_val_min as $key => $value) {
							if( $gd_duration_type[$key] == 'hours' ){
						?>

								<tr class="<?php echo intval($k%2) ? 'eve' : 'odd'; $k++; ?>">

									<td class="bold" data-title="<?php echo esc_attr( $title_text ); ?>">

										<?php echo esc_html( $gd_duration_val_min[$key] ); ?>
										-
										<?php echo esc_html( $gd_duration_val_max[$key] ); ?>

									</td>
									
									<td data-title="<?php echo sprintf( '%1$s from %2$s - %3$s hours', esc_attr($price_text), esc_attr( $gd_duration_val_min[$key], esc_attr( $gd_duration_val_max[$key] ) ) ); ?>">

										<?php echo wp_kses_post( wc_price( $gd_price[$key] ) ); ?>
											
									</td>

									

								</tr>			

							<?php
						} }
					?>

				</tbody>

			</table>

		</div>
	<?php } ?>



