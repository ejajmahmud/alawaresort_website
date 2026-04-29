
<table class="wrap_rt_discount">
	<tbody>
		<tr class="tr_rt_discount">
									
		    <td width="49%">
		        <input type="text" class="ovabrw_rt_discount_price input_per_100" placeholder="<?php esc_html_e('10.5', 'ova-brw'); ?>"
		               name="ovabrw_rt_discount[ovabrw_key][price][]" value="" />
		    </td>

		    <td width="49%">

			    <input type="text" class="input_text short ovabrw_rt_discount_duration" placeholder="<?php esc_html_e('1', 'ova-brw'); ?>"
			             name="ovabrw_rt_discount[ovabrw_key][min][]" value=""/>

			    <input type="text"  class="input_text short ovabrw_rt_discount_duration" placeholder="<?php esc_html_e('2', 'ova-brw'); ?>"
			             name="ovabrw_rt_discount[ovabrw_key][max][]" value=""/>

			      <select class="short ovabrw_rt_discount_duration_type" name="ovabrw_rt_discount[ovabrw_key][duration_type][]">
			          <option value="hours"><?php esc_html_e('Hour(s)', 'ova-brw'); ?></option>
			          <option value="days"><?php esc_html_e('Day(s)', 'ova-brw'); ?></option>
			      </select>
			    
		    </td>
		    <td width="1%"><a href="#" class="delete_discount">x</a></td>
		    
		</tr>
	</tbody>
</table>
