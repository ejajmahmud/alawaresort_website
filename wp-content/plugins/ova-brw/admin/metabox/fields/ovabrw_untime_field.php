<?php
$time_format = ovabrw_get_time_format();
$date_format = ovabrw_get_date_format();

// Get first day in week
$first_day = get_option( 'ova_brw_calendar_first_day', '0' );

if ( empty( $first_day ) ) {
  $first_day = 0;
}

?>
<tr class="tr_rt_untime">

    <td width="20%">
      <input data-time="<?php echo esc_attr( $time_format ); ?>" type="text" name="ovabrw_untime_startdate[]" value="" placeholder="<?php echo esc_attr( $date_format ); ?>" class="unavailable_time start_date" autocomplete="off" data-firstday="<?php echo esc_attr( $first_day ); ?>"/>
    </td>

    <td width="20%">
      <input data-time="<?php echo esc_attr( $time_format ); ?>" type="text" name="ovabrw_untime_enddate[]" value="" placeholder="<?php echo esc_attr( $date_format ); ?>" class=" unavailable_time end_date" autocomplete="off" data-firstday="<?php echo esc_attr( $first_day ); ?>"/>
    </td>

    <td width="1%"><a href="#" class="delete_untime">x</a></td>
    
</tr>