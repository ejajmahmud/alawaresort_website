<?php
/**
 * The template for displaying weekdays content within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/table-price/weekdays.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

	if( isset( $args['product_id'] ) && $args['product_id'] ){
		$pid = $args['product_id'];
	}else{
		$pid = get_the_id();
	}

	$daily = ovabrw_p_weekdays( $pid );
	if( !empty( $daily ) ){
?>

	<div class="price_table">

		<label><?php esc_html_e( 'Price by day of the week', 'ova-brw' ); ?></label>

		<table>

			<thead>
				<tr>
					<th><?php esc_html_e( 'Weekdays', 'ova-brw' ); ?></th>
					<th><?php esc_html_e( 'Price', 'ova-brw' ); ?></th>
				</tr>
			</thead>

			<tbody>
				
				<?php foreach ($daily as $key => $value) { ?>

					<?php

						switch ($key) {
							case 'monday':
								$day = esc_html__( 'Monday', 'ova-brw' );
								$class = 'eve';
								break;
							case 'tuesday':
								$day = esc_html__( 'Tuesday', 'ova-brw' );
								$class = 'odd';
								break;
							case 'wednesday':
								$day = esc_html__( 'Wednesday', 'ova-brw' );
								$class = 'eve';
								break;
							case 'thursday':
								$day = esc_html__( 'Thursday', 'ova-brw' );
								$class = 'odd';
								break;
							case 'friday':
								$day = esc_html__( 'Friday', 'ova-brw' );
								$class = 'eve';
								break;
							case 'saturday':
								$day = esc_html__( 'Saturday', 'ova-brw' );
								$class = 'odd';
								break;	
							case 'sunday':
								$day = esc_html__( 'Sunday', 'ova-brw' );
								$class = 'eve';
								break;		
							
							default:
								$day = '';
								break;
						}
					?>

					<tr class="<?php echo esc_attr( $class ); ?>">

						<td class="bold">
							
							<?php echo esc_html($day); ?>

						</td>

						<td data-title="<?php echo esc_attr($day); ?>">

							<?php echo wp_kses_post(wc_price( $value )); ?>

						</td>

					</tr>
		
				<?php } ?>
				
			</tbody>
		</table>
	</div>

<?php } ?>

