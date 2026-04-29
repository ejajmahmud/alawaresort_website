<?php if( ! defined( 'ABSPATH' ) ) exit();

$id = get_the_id();

$rt_startdate 	= get_post_meta( $id, 'ovabrw_rt_startdate', true ); 
$rt_enddate 	= get_post_meta( $id, 'ovabrw_rt_enddate', true );
$rt_price_hour 	= get_post_meta( $id, 'ovabrw_rt_price_hour', true );
$rt_discount 	= get_post_meta( $id, 'ovabrw_rt_discount', true );
$rt_starttime 	= get_post_meta( $id, 'ovabrw_rt_starttime', true );
$rt_endtime 	= get_post_meta( $id, 'ovabrw_rt_endtime', true) ;
$date_format 	= ovabrw_get_date_format();

if( empty( $rt_price_hour ) ) return;

?>

<?php if ( $rt_price_hour ): ?>
	<div class="table-price">
		<div class="head-label">
			<h3 class="label"><?php esc_html_e( 'Start Time', 'entox' ); ?></h3>
			<h3 class="label"><?php esc_html_e( 'End Time', 'entox' ); ?></h3>
			<h3 class="label"><?php esc_html_e( 'Price/Hour', 'entox' ); ?></h3>
			<h3 class="label"><?php esc_html_e( 'Special Discount', 'entox' ); ?></h3>
		</div>
		<div class="boby-price">
			<?php foreach( $rt_price_hour as $k => $value ): 
				$date_start = $rt_startdate[$k] ? date_i18n( $date_format, strtotime( $rt_startdate[$k] ) ).' '.$rt_starttime[$k] : '';
				$date_end 	= $rt_enddate[$k] ? date_i18n( $date_format, strtotime( $rt_enddate[$k] ) ).' '.$rt_endtime[$k] : '';

				$rt_discount_price 			= isset( $rt_discount[$k]['price'] ) ? $rt_discount[$k]['price']: '';
				$rt_discount_duration_min 	= isset( $rt_discount[$k]['min'] ) ? $rt_discount[$k]['min'] : '';
				$rt_discount_duration_max 	= isset( $rt_discount[$k]['max'] ) ? $rt_discount[$k]['max'] : '';
				$rt_discount_duration_type 	= isset( $rt_discount[$k]['duration_type'] ) ? $rt_discount[$k]['duration_type'] : '';

				$data_discount = 'discount-'. $k;
			?>
				<div class="content-price <?php echo intval( $k%2 ) ? 'even' : 'odd'; ?>">
					<p class="value-price">
						<?php echo esc_html( $date_start ); ?>
					</p>
					<p class="value-price">
						<?php echo esc_html( $date_end ); ?>
					</p>
					<p class="value-price">
						<?php printf( wc_price( $rt_price_hour[$k] ) ); ?>
					</p>
					<p class="value-price view-discount-price" data-discount="<?php echo esc_attr( $data_discount ); ?>">
						<?php esc_html_e( 'View Discount', 'entox' ); ?>
					</p>
				</div>
				<div class="table-discount <?php echo esc_attr( $data_discount ); ?>">
					<?php if ( $rt_discount_duration_min || $rt_discount_duration_max ): 
						asort($rt_discount_duration_min);
						asort($rt_discount_duration_max);
					?>
						<table class="head-discount">
							<thead>
								<tr>
									<th class="head-discount-label"><?php esc_html_e( 'Min - Max (Hours)', 'entox' ); ?></th>
									<th class="head-discount-label"><?php esc_html_e( 'Price/Hours', 'entox' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach( $rt_discount_duration_min as $i => $discount_duratio ): ?>
									<?php if ( $rt_discount_duration_type[$i] == 'hours' ): 
										$duration_min = $rt_discount_duration_min[$i];
										$duration_max = $rt_discount_duration_max[$i];
									?>
										<tr class="content-discount">
											<td class="value-discount-price">
												<?php echo esc_html( $duration_min ).' - '.esc_html( $duration_max ); ?>
											</td>
											<td class="value-discount-price">
												<?php echo wc_price( $rt_discount_price[$i] ); ?>
											</td>
										</tr>
									<?php endif; ?>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
<?php endif; ?>