<?php
$time_format = ovabrw_get_time_format();
// Get first day in week
$first_day = get_option( 'ova_brw_calendar_first_day', '0' );

if ( empty( $first_day ) ) {
	$first_day = 0;
}

?>
<tr class="row_rt_record" data-pos="">

    <td width="15%"><input type="text"  placeholder="<?php esc_html_e('10.5', 'ova-brw'); ?>" name="ovabrw_rt_price[]" value="" class="ovabrw_rt_price"/></td>

    <td width="15%"><input type="text"  placeholder="<?php esc_html_e('10.5', 'ova-brw'); ?>" name="ovabrw_rt_price_hour[]" value="" class="ovabrw_rt_price_hour"/></td>

    <td width="20%">
      <input type="text" name="ovabrw_rt_startdate[]"  value="" class="ovabrw_rt_startdate ova_brw_datepicker_short datetimepicker" placeholder="<?php esc_attr_e( 'DD-MM-YY', 'ova-brw' ); ?>" autocomplete="off" data-firstday="<?php echo esc_attr( $first_day ); ?>"/>
      <input type="text"data-time="<?php echo esc_attr( $time_format ); ?>" name="ovabrw_rt_starttime[]"  value="" class="ovabrw_rt_starttime ova_brw_datepicker_time datetimepicker" placeholder="<?php esc_html_e( 'From Time', 'ova-brw' ); ?>" autocomplete="off"/>
    </td>

    <td width="20%">
      <input type="text" name="ovabrw_rt_enddate[]" value="" class="ovabrw_rt_enddate ova_brw_datepicker_short  datetimepicker" placeholder="<?php esc_attr_e( 'DD-MM-YY', 'ova-brw' ); ?>" autocomplete="off" data-firstday="<?php echo esc_attr( $first_day ); ?>"/>
      <input type="text" data-time="<?php echo esc_attr( $time_format ); ?>" name="ovabrw_rt_endtime[]" value="" class="ovabrw_rt_endtime ova_brw_datepicker_time  datetimepicker" placeholder="<?php esc_html_e( 'End Time', 'ova-brw' ); ?>" autocomplete="off"/>
    </td>


    <td width="39%">
    	<table width="100%" class="ovabrw_rt_discount">
	      	<thead>
				<tr>
					<th><?php esc_html_e( 'Price', 'ova-brw' ); ?></th>
					<th><?php esc_html_e( 'Min - Max: Number', 'ova-brw' ); ?></th>
				</tr>
			</thead>

			<tbody class="real">

			</tbody>

			<tfoot>
				<tr>
					<th colspan="6">
						<a href="#" class="button insert_rt_discount">
							<?php esc_html_e( 'Add PST', 'ova-brw' ); ?>
							<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_rt_discount.php' ); ?>
						</a>
					</th>
				</tr>
			</tfoot>
      	</table>
    </td>


    <td width="1%"><a href="#" class="delete_rt">x</a></td>
</tr>