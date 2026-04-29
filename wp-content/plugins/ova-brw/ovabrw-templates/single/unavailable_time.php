<?php 
/**
 * The template for displaying unavailable time content within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/unavailable_time.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

global $product; 

// if the product type isn't ovabrw_car_rental
if( $product->get_type() !== 'ovabrw_car_rental' ) return;

$pid = $product->get_id();

$ovabrw_date_format = ovabrw_get_date_format();
$ovabrw_time_format = ovabrw_get_time_format_php();

$ovabrw_date_time_format = $ovabrw_date_format . ' ' . $ovabrw_time_format;

$start_untime = get_post_meta( $pid, 'ovabrw_untime_startdate', true );
$end_entime = get_post_meta( $pid, 'ovabrw_untime_enddate', true );

	if( $start_untime ){ ?>

		<div class="ovacrs_single_untime">
			
			<h3><?php esc_html_e( 'You can\'t rent product in this time', 'ova-brw' ); ?></h3>
					
			<ul>
				<?php foreach ($start_untime as $key => $value) { ?>
					<li>
						<?php echo esc_html( date_i18n( $ovabrw_date_time_format, strtotime( $start_untime[$key] ) ).' - '.date_i18n( $ovabrw_date_time_format, strtotime( $end_entime[$key] ) ) ); ?>
					</li>						
				<?php } ?>
			</ul>

		</div>
		
	<?php } ?>
