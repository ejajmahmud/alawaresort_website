<div class="ovabrw_global_discount">
	<table class="widefat">

		<thead>
			<tr>
				<th><?php esc_html_e( 'Price', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Duration (Min Max): Number', 'ova-brw' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<!-- Append html here -->

			<?php if( $ovabrw_global_discount_price = get_post_meta( $post_id, 'ovabrw_global_discount_price', 'false' ) ){

				$ovabrw_global_discount_duration_val_min = get_post_meta( $post_id, 'ovabrw_global_discount_duration_val_min', 'false' );
				$ovabrw_global_discount_duration_val_max = get_post_meta( $post_id, 'ovabrw_global_discount_duration_val_max', 'false' );
				$ovabrw_global_discount_duration_type = get_post_meta( $post_id, 'ovabrw_global_discount_duration_type', 'false' );

				for( $i =0 ; $i < count( $ovabrw_global_discount_price ); $i++ ) { ?>
					<tr class="row_discount">
							
					    <td width="11%">
					        <input type="text" class="input_text" placeholder="<?php esc_html_e('10.5', 'ova-brw'); ?>"
					        name="ovabrw_global_discount_price[]"
					        value="<?php echo esc_attr( $ovabrw_global_discount_price[$i] ); ?>"/>
					    </td>

					    <td width="13%">
						    
						    <input type="text" class="input_text ovabrw-global-duration short"
						    	placeholder="1"
						        name="ovabrw_global_discount_duration_val_min[]"
						        value="<?php echo esc_attr( $ovabrw_global_discount_duration_val_min[$i] ); ?>"/>

						    <input type="text" class="input_text ovabrw-global-duration short"
						    	placeholder="2"
						        name="ovabrw_global_discount_duration_val_max[]"
						        value="<?php echo esc_attr( $ovabrw_global_discount_duration_val_max[$i] ); ?>"/>


						    <select class="short ovabrw-global-duration" id="mySelect" name="ovabrw_global_discount_duration_type[]">
									
								<?php if ( $ovabrw_global_discount_duration_type[$i] == 'hours' ) : ?>
									<option value="hours" selected ><?php esc_html_e('Hour(s)', 'ova-brw'); ?></option>
								<?php endif ?>

								<?php if ( $ovabrw_global_discount_duration_type[$i] == 'days' ) : ?>
									<option value="days" selected ><?php esc_html_e('Day(s)', 'ova-brw'); ?></option>
								<?php endif ?>
						          
						    </select>
						    
					    </td>
					    <td width="1%"><a href="#" class="delete">x</a></td>
					    
					</tr>
				<?php }

			} ?>
		</tbody>

		<tfoot>
			<tr>
				<th colspan="6">
					<a href="#" class="button insert_discount" data-row="<tr class=&quot;row_discount&quot;>
					    <td width=&quot;11%&quot;>
					        <input type=&quot;text&quot; class=&quot;input_text&quot; placeholder=&quot;<?php esc_html_e('10.5', 'ova-brw'); ?>&quot;
					               name=&quot;ovabrw_global_discount_price[]&quot; value=&quot;&quot;/>
					    </td>

					    <td width=&quot;13%&quot;>
					    
					      <input type=&quot;text&quot; class=&quot;input_text ovabrw-global-duration short&quot; placeholder=&quot;<?php esc_html_e('1', 'ova-brw'); ?>&quot; name=&quot;ovabrw_global_discount_duration_val_min[]&quot; value=&quot;&quot;/>

					      <input type=&quot;text&quot; class=&quot;input_text ovabrw-global-duration short&quot; placeholder=&quot;<?php esc_html_e('2', 'ova-brw'); ?>&quot; name=&quot;ovabrw_global_discount_duration_val_max[]&quot; value=&quot;&quot;/>

					      <select class=&quot;short ovabrw-global-duration&quot; name=&quot;ovabrw_global_discount_duration_type[]&quot;>
					          <option value=&quot;hours&quot;><?php esc_html_e('Hour(s)', 'ova-brw'); ?></option>
					          <option value=&quot;days&quot;><?php esc_html_e('Day(s)', 'ova-brw'); ?></option>
					         
					      </select>
					    
					    </td>

					    <td width=&quot;1%&quot;><a href=&quot;#&quot; class=&quot;delete&quot;>x</a></td>

					</tr>


					"><?php esc_html_e( 'Add GD', 'ova-brw' ); ?></a>
				</th>
			</tr>
		</tfoot>

	</table>
</div>