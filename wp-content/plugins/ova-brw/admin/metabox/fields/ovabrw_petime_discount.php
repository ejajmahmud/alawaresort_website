<?php
$time_format = ovabrw_get_time_format();
// Get first day in week
$first_day = get_option( 'ova_brw_calendar_first_day', '0' );

if ( empty( $first_day ) ) {
  $first_day = 0;
}

?>
<table class="wrap_petime_discount">
	<tbody>
		<tr class="tr_petime_discount">
																
			<td width="30%">
			    <input type="text" class="ovabrw_petime_discount_price " placeholder="<?php esc_attr_e('10.5', 'ova-brw') ?>" 
			    	name="ovabrw_petime_discount[ovabrw_key][price][]" value="" />
			</td>
			<td width="30%">
			    <input type="text" data-time="<?php echo esc_attr( $time_format ); ?>" data-firstday="<?php echo esc_attr( $first_day ); ?>" class="ovabrw_petime_discount_start_time ovabrw_start_date ovabrw_datetimepicker" placeholder="<?php esc_attr_e('Start Time', 'ova-brw') ?>" 
			    	name="ovabrw_petime_discount[ovabrw_key][start_time][]" value="" autocomplete="off" />
			</td>
			<td width="30%">
			    <input type="text" data-time="<?php echo esc_attr( $time_format ); ?>" data-firstday="<?php echo esc_attr( $first_day ); ?>" class="ovabrw_petime_discount_end_time ovabrw_end_date ovabrw_datetimepicker" placeholder="<?php esc_attr_e('End Time', 'ova-brw') ?>" 
			    	name="ovabrw_petime_discount[ovabrw_key][end_time][]" value="" autocomplete="off" />
			</td>

			
		<td width="9%"><a href="#" class="delete_petime_discount">x</a></td>

		</tr> 



	</tbody>
</table>
