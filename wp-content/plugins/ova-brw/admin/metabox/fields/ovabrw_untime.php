<?php
$time_format = ovabrw_get_time_format();
$date_format = ovabrw_get_date_format();
$set_time_format = ovabrw_get_time_format_php();
// Get first day in week
$first_day = get_option( 'ova_brw_calendar_first_day', '0' );

if ( empty( $first_day ) ) {
	$first_day = 0;
}

?>
<div class="ovabrw_rt_untime">
	<table class="widefat">

		<thead>
			<tr>
				<th><?php esc_html_e( 'Start Date', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'End Date', 'ova-brw' ); ?></th>
			</tr>
		</thead>

		<tbody class="wrap_rt_untime">
			<!-- Append html here -->
			<?php if( $ovabrw_untime_startdate = get_post_meta( $post_id, 'ovabrw_untime_startdate', 'false' ) ){ 
					$ovabrw_untime_enddate = get_post_meta( $post_id, 'ovabrw_untime_enddate', 'false' );
					$ovabrw_untime_starttime = get_post_meta( $post_id, 'ovabrw_untime_starttime', 'false' );
					$ovabrw_untime_endtime = get_post_meta( $post_id, 'ovabrw_untime_endtime', 'false' );

					for( $i = 0 ; $i < count( $ovabrw_untime_startdate ); $i++ ) {
					
			?>

				<tr class="tr_rt_untime">

				    <td width="20%">
				      <input data-time="<?php echo esc_attr( $time_format ); ?>"  type="text" name="ovabrw_untime_startdate[]" placeholder="<?php echo esc_attr( $date_format ); ?>" value="<?php echo esc_attr( wp_date( $date_format . ' '.$set_time_format, strtotime($ovabrw_untime_startdate[$i]) ) ); ?>" class="unavailable_time start_date" autocomplete="off" data-firstday="<?php echo esc_attr( $first_day ); ?>"/>
				      <input type="hidden" name="ovabrw_untime_starttime[]" value="<?php echo esc_attr( strtotime($ovabrw_untime_startdate[$i]) ); ?>" />
				    </td>

				    <td width="20%">
				      <input data-time="<?php echo esc_attr( $time_format ); ?>"  type="text" name="ovabrw_untime_enddate[]" placeholder="<?php echo esc_attr( $date_format ); ?>"
				      value="<?php echo esc_attr( wp_date( $date_format . ' '.$set_time_format, strtotime($ovabrw_untime_enddate[$i]) ) ); ?>" class="unavailable_time end_date" autocomplete="off" data-firstday="<?php echo esc_attr( $first_day ); ?>"/>
				      <input type="hidden" name="ovabrw_untime_endtime[]" value="<?php echo esc_attr( strtotime($ovabrw_untime_enddate[$i]) ); ?>" />
				    </td>

				    <td width="1%"><a href="#" class="delete_untime">x</a></td>
				    
				</tr>

			<?php } } ?>
		</tbody>

		<tfoot>
			<tr>
				<th colspan="6">
					<a href="#" class="button insert_rt_untime" data-row="
						<?php
							ob_start();
							include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_untime_field.php' );
							echo esc_attr( ob_get_clean() );
						?>

					">
					<?php esc_html_e( 'Add UT', 'ova-brw' ); ?></a>
					</a>
				</th>
			</tr>
		</tfoot>

	</table>
</div>


