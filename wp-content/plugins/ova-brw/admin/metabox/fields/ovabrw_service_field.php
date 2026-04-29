<tr class="tr_rt_service">

    <td width="15%">
      <input type="text" name="ovabrw_service_id[ovabrw_key][]" class="ovabrw_service_key" placeholder="<?php esc_html_e( 'Not space', 'ova-brw' ); ?> " value="" />
    </td>

    <td width="39%">
      <input type="text" name="ovabrw_service_name[ovabrw_key][]" value="" />
    </td>

  
    <td width="20%">
      <input type="text" name="ovabrw_service_price[ovabrw_key][]" value="" placeholder="<?php esc_html_e( '10.5', 'ova-brw' ) ?>" />
    </td>

    <td width="25%">
      
      <select name="ovabrw_service_duration_type[ovabrw_key][]" class="short_dura">
          <option value="hours" ><?php esc_html_e( '/Hours', 'ova-brw' ); ?></option>
          <option value="days" ><?php esc_html_e( '/Days', 'ova-brw' ); ?></option>
          <option value="total" ><?php esc_html_e( '/Total', 'ova-brw' ); ?></option>
        </select>
    </td>

    <td width="1%"><a href="#" class="delete_service">x</a></td>
    
</tr>