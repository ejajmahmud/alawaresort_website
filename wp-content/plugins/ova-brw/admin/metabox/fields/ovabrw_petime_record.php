<?php
$time_format = ovabrw_get_time_format();
?>
<tr class="row_petime_record" data-pos="">

    <td width="15%">
        <input type="text"  placeholder="<?php esc_attr_e('not space', 'ova-brw'); ?>" name="ovabrw_petime_id[]" value="" class="ovabrw_petime_id"/>
    </td>

    <td width="15%">
        <input type="text"  placeholder="<?php esc_attr_e('10.5', 'ova-brw'); ?>" name="ovabrw_petime_price[]" value="" class="ovabrw_petime_price"/>
    </td>

    <td width="15%">
				    		
		<select name="ovabrw_package_type[]" class="ovabrw_package_type">
			<option value="inday" selected ><?php esc_html_e( 'Period of Fixed Hours in day', 'ova-brw' ); ?></option>
			<option value="other"><?php esc_html_e( 'Period of Fixed Days', 'ova-brw' ); ?></option>
		</select>
    	<br>
    	<br>
        <input type="number"  placeholder="<?php esc_attr_e('Total Day', 'ova-brw'); ?>" name="ovabrw_petime_days[]" value=""  class="ovabrw_petime_days" />
        
        <div class="period_times">
        	
        	<input type="text"  placeholder="<?php esc_attr_e('Start Hour', 'ova-brw'); ?>" name="ovabrw_pehour_start_time[]" value="" data-time="<?php echo esc_attr($time_format); ?>"  class="ovabrw_pehour_start_time ovabrw_pehour_picker" autocomplete="off"/>

        	 <input type="text"  placeholder="<?php esc_attr_e('End Hour', 'ova-brw'); ?>" name="ovabrw_pehour_end_time[]" value=""  data-time="<?php echo esc_attr($time_format); ?>" class="ovabrw_pehour_end_time ovabrw_pehour_picker" autocomplete="off"/>

        </div>

        <div class="period_times_unfixed">
        	<input type="text"  placeholder="<?php esc_attr_e('Total Hour', 'ova-brw'); ?>" name="ovabrw_pehour_unfixed[]" value=""  autocomplete="off"/>
        </div>

    </td>



    <td width="15%">
        <input type="text"  placeholder="<?php esc_attr_e('Text', 'ova-brw'); ?>" name="ovabrw_petime_label[]" value="" class="ovabrw_petime_label"/>
    </td>


    <td width="39%">
    	<table width="100%" class="ovabrw_petime_discount">
	      	<thead>
				<tr>
					<th><?php esc_html_e( 'Price', 'ova-brw' ); ?></th>
					<th><?php esc_html_e( 'Start Time', 'ova-brw' ); ?></th>
					<th><?php esc_html_e( 'End Time', 'ova-brw' ); ?></th>
				</tr>
			</thead>

			<tbody class="real">

			</tbody>

			<tfoot>
				
					<tr>
						<th colspan="6">
							<a href="#" class="button insert_petime_discount">
								<?php esc_html_e( 'Add Discount', 'ova-brw' ); ?>
								<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_petime_discount.php' ); ?>
							</a>
						</th>
					</tr>
				
				
			</tfoot>

      	
      	</table>
    </td>


    <td width="1%"><a href="#" class="delete_petime">x</a></td>
</tr>