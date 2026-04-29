<?php
/**
 * The template for displaying seasons content within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/table-price/seasons_hour.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

if( isset( $args['product_id'] ) && $args['product_id'] ){
	$pid = $args['product_id'];
}else{
	$pid = get_the_id();
}


$rt_startdate = get_post_meta( $pid, 'ovabrw_rt_startdate', true ); 
$rt_enddate = get_post_meta( $pid, 'ovabrw_rt_enddate', true );
$rt_price_hour = get_post_meta( $pid, 'ovabrw_rt_price_hour', true );
$rt_discount = get_post_meta( $pid, 'ovabrw_rt_discount', true );



$rt_starttime = get_post_meta( $pid, 'ovabrw_rt_starttime', true );
$rt_endtime = get_post_meta( $pid, 'ovabrw_rt_endtime', true) ;

$ovabrw_date_format = ovabrw_get_date_format();

if( empty( $rt_price_hour ) ) return;

?>

<div class="price_table">
	<label><?php esc_html_e( 'Special Time', 'ova-brw' ); ?></label>

	<table>
		<thead>
			<tr>
				<th><?php esc_html_e( 'Start Date', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'End Date', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Price/Hour', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Special Discount', 'ova-brw' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php $s = 0; foreach ($rt_price_hour as $key => $value) {
				if($rt_price_hour[$key] ) {

					$date_start = $rt_startdate[$key] ? date_i18n( $ovabrw_date_format, strtotime( $rt_startdate[$key] ) ).' '.$rt_starttime[$key] : '';

					$date_end = $rt_enddate[$key] ? date_i18n( $ovabrw_date_format, strtotime( $rt_enddate[$key] ) ).' '.$rt_endtime[$key] : '';

					?>
					<tr class="<?php echo intval($s%2) ? esc_attr('eve') : esc_attr('odd'); $s++; ?>">

						<td class="bold" data-title="<?php esc_html_e( 'Start Date', 'ova-brw' ); ?>">
							<?php echo esc_html( $date_start ); ?>
						</td>

						<td class="bold" data-title="<?php esc_html_e( 'End Date', 'ova-brw' ); ?>">
							<?php echo esc_html( $date_end ); ?>
						</td>

						<td data-title="<?php echo sprintf( esc_attr__( 'Price/Hour from %1$s - %2$s', 'ova-brw' ), esc_attr($date_start), esc_attr($date_end) ); ?>">
							<?php echo wp_kses_post( wc_price( $rt_price_hour[$key] ) ); ?>
						</td>

						<td data-title="<?php esc_attr_e( 'Special Discount', 'ova-brw' ); ?>">

							<a href="#" class="ovabrw_open_popup" data-popup-open="popup-ovacrs-rt-discount-<?php echo esc_attr( $key ); ?>">
								
								<?php esc_html_e( 'View Discount', 'ova-brw' ); ?>

								<div class="ovacrs_rt_discount popup" data-popup="popup-ovacrs-rt-discount-<?php echo esc_attr( $key ); ?>">
									<div class="popup-inner">

										<div class="price_table">

											<div class="time_discount">
												
												<span><?php esc_html_e( 'Time Discount: ', 'ova-brw' ); ?></span>
												
												<span class="time">
													<?php echo esc_html( $date_start.' - '.$date_end ); ?> 
														
												</span>

											</div>
											<?php
												$rt_discount_price = isset( $rt_discount[$key]['price'] ) ? $rt_discount[$key]['price']: '';
												$rt_discount_duration_min = isset( $rt_discount[$key]['min'] ) ? $rt_discount[$key]['min'] : '';
												$rt_discount_duration_max = isset( $rt_discount[$key]['max'] ) ? $rt_discount[$key]['max'] : '';

												$rt_discount_duration_type = isset( $rt_discount[$key]['duration_type'] ) ? $rt_discount[$key]['duration_type'] : '';
											?>
											<?php if( $rt_discount_duration_min || $rt_discount_duration_max ){ 
												asort($rt_discount_duration_min); 
												asort($rt_discount_duration_max); 
											?>
											<table>
												<thead>
													<tr>
														<th><?php esc_html_e( 'Min - Max (Hours)', 'ova-brw' ); ?></th>
														<th><?php esc_html_e( 'Price/Hour', 'ova-brw' ); ?></th>
													</tr>
												</thead>

												<tbody>
													<?php $n = 0;
														foreach ($rt_discount_duration_min as $k => $v) {
															if( $rt_discount_duration_type[$k] == 'hours' ){ ?>
																<tr class="<?php echo intval($n%2) ? esc_attr('eve') : esc_attr('odd'); $n++; ?>">

																	<td class="bold" data-title="<?php esc_attr_e( 'Min Duration (Hours)', 'ova-brw' ); ?>">

																		<?php echo esc_html( $rt_discount_duration_min[$k]. ' - '. $rt_discount_duration_max[$k] ); ?>
																		
																	</td>
																	

																	<td data-title="<?php echo sprintf( esc_attr__( 'Price/Hour from %1$s - %2$s hours', 'ova-brw' ), esc_attr($rt_discount_duration_min[$k]), esc_attr($rt_discount_duration_max[$k]) ); ?>">

																		<?php echo wp_kses_post(wc_price( $rt_discount_price[$k] )); ?>
																		
																	</td>

																</tr>
															<?php }
														} ?>
												</tbody>
											</table>

											<?php }else{ ?>

												<div class="no_discount">

													<?php esc_html_e( 'No Discount in this time', 'ova-brw' ); ?>

												</div>

											<?php } ?>

										</div>

										<div  class="close_discount">
											<a class="popup-close-2" data-popup-close="popup-ovacrs-rt-discount-<?php echo esc_attr( $key ); ?>" href="#">
												<?php esc_html_e( 'Close', 'ova-brw' ); ?>
													
											</a>
										</div>

										<a class="popup-close" data-popup-close="popup-ovacrs-rt-discount-<?php echo esc_attr( $key ); ?>" href="#">x</a>

									</div>
								</div>
							</a>
						</td>
					</tr>			
			<?php } } ?>
		</tbody>
	</table>
</div>
