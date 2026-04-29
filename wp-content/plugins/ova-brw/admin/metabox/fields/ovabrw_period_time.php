<div class="ovabrw_petime">
	<table class="widefat">

		<thead>
			<tr>
				<th><?php esc_html_e( 'ID*', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Price*', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Package Type*', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Package*', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Discount', 'ova-brw' ); ?></th>
			</tr>
		</thead>

		<tbody class="wrap_petime">
			<!-- Append html here -->
			<?php if( $ovabrw_petime_id = get_post_meta( $post_id, 'ovabrw_petime_id', true ) ){
				
				$ovabrw_petime_price = get_post_meta( $post_id, 'ovabrw_petime_price', true );
				$ovabrw_petime_days = get_post_meta( $post_id, 'ovabrw_petime_days', true );
				$ovabrw_petime_label = get_post_meta( $post_id, 'ovabrw_petime_label', true );
				$ovabrw_petime_discount = get_post_meta( $post_id, 'ovabrw_petime_discount', true );
				$ovabrw_package_type = get_post_meta( $post_id, 'ovabrw_package_type', true );

				$ovabrw_pehour_start_time = get_post_meta( $post_id, 'ovabrw_pehour_start_time', true );
				$ovabrw_pehour_end_time = get_post_meta( $post_id, 'ovabrw_pehour_end_time', true );

				$ovabrw_pehour_unfixed = get_post_meta( $post_id, 'ovabrw_pehour_unfixed', true );

				// Get first day in week
				$first_day = get_option( 'ova_brw_calendar_first_day', '0' );

				if ( empty( $first_day ) ) {
				  $first_day = 0;
				}

				$time_format = ovabrw_get_time_format();
				$date_format = ovabrw_get_date_format();

				$set_time_format = ovabrw_get_time_format_php();
				

				for( $i = 0 ; $i < count( $ovabrw_petime_id ); $i++ ) {
					
			?>

				<tr class="row_petime_record" data-pos="<?php echo esc_attr($i); ?>">

				    <td width="10%">
				        <input type="text" placeholder="<?php esc_attr_e('not space', 'ova-brw'); ?>" name="ovabrw_petime_id[]" value="<?php echo esc_attr($ovabrw_petime_id[$i]); ?>" class="ovabrw_petime_id" >
				    </td>

				    <td width="10%">
				        <input type="text"  placeholder="<?php esc_attr_e('10.5', 'ova-brw'); ?>" name="ovabrw_petime_price[]" value="<?php echo esc_attr($ovabrw_petime_price[$i]); ?>"  class="ovabrw_petime_price"/>
				    </td>

				    <td width="25%">
				    	<div class="clearfix">

				    		<select name="ovabrw_package_type[]" class="ovabrw_package_type">
				    			<option value="inday" <?php echo ( isset( $ovabrw_package_type[$i] ) &&  $ovabrw_package_type[$i] == 'inday' ) ? 'selected' : ''; ?> >
				    				<?php esc_html_e( 'Period of Fixed Hours in day', 'ova-brw' ); ?>
				    			</option>
				    			<option value="other" <?php echo ( isset( $ovabrw_package_type[$i] ) && $ovabrw_package_type[$i] == 'other' ) ? 'selected' : ''; ?>>
				    				<?php esc_html_e( 'Period of Fixed Days', 'ova-brw' ); ?>
				    			</option>
				    		</select>

				    	</div>
				    	
				        <input type="number"  placeholder="<?php esc_attr_e('Total Day', 'ova-brw'); ?>" name="ovabrw_petime_days[]" value="<?php echo esc_attr($ovabrw_petime_days[$i]); ?>"  class="ovabrw_petime_days"/>
				       
				        <div class="period_times">
				        	
				        	<input type="text"  placeholder="<?php esc_attr_e('Start Hour', 'ova-brw'); ?>"
				        	data-time="<?php echo esc_attr( $time_format ); ?>" name="ovabrw_pehour_start_time[]" value="<?php echo ( isset( $ovabrw_pehour_start_time[$i] ) && !empty( $ovabrw_pehour_start_time[$i] ) ) ? esc_attr( $ovabrw_pehour_start_time[$i] ) : esc_attr( '' ); ?>"  class="ovabrw_pehour_start_time ovabrw_pehour_picker" autocomplete="off"/>

				        	<input type="text"
				        		placeholder="<?php esc_attr_e('End Hour', 'ova-brw'); ?>"
				        		data-time="<?php echo esc_attr( $time_format ); ?>" name="ovabrw_pehour_end_time[]" value="<?php echo ( isset( $ovabrw_pehour_end_time[$i] ) && !empty( $ovabrw_pehour_end_time[$i] ) ) ? esc_attr( $ovabrw_pehour_end_time[$i] ) : esc_attr(''); ?>"  class="ovabrw_pehour_end_time ovabrw_pehour_picker" autocomplete="off"/>

				        </div>

				        <div class="period_times_unfixed">
				        	<input type="text"  placeholder="<?php esc_attr_e('Total Hour', 'ova-brw'); ?>" name="ovabrw_pehour_unfixed[]" value="<?php echo isset( $ovabrw_pehour_unfixed[$i] ) ? esc_attr( $ovabrw_pehour_unfixed[$i] ) : esc_attr(''); ?>"  autocomplete="off"/>
				        </div>

				    </td>

				    <td width="10%">
				      <input type="text" placeholder="<?php esc_html_e( 'Text', 'ova-brw' ); ?>" name="ovabrw_petime_label[]" value="<?php echo esc_attr($ovabrw_petime_label[$i]); ?>" class="ovabrw_petime_label ">
				    </td>

				    <td width="24%" class="ovabrw_petime_dis">
				    	<table width="100%" class="ovabrw_petime_discount">

					      	<thead>
								<tr>
									<th><?php esc_html_e( 'Price', 'ova-brw' ); ?></th>
									<th><?php esc_html_e( 'Start Time', 'ova-brw' ); ?></th>
									<th><?php esc_html_e( 'End Time', 'ova-brw' ); ?></th>
								</tr>
							</thead>

							<tbody class="real">

								<?php if( isset( $ovabrw_petime_discount[$i]['price'] ) ){ ?>
								<?php for( $k = 0; $k < count( $ovabrw_petime_discount[$i]['price'] ); $k++ ){ ?>
									
									<tr class="tr_petime_discount">
																
										<td width="30%">
										    <input type="text" class="ovabrw_petime_discount_price "
										    	placeholder="10.5" 
										    	name="ovabrw_petime_discount[<?php echo esc_attr($i);?>][price][]"
										    	value="<?php echo esc_attr( $ovabrw_petime_discount[$i]['price'][$k] ); ?>" />
										</td>
										<td width="30%">
										    <input type="text" data-time="<?php echo esc_attr( $time_format ); ?>" data-firstday="<?php echo esc_attr( $first_day ); ?>" class="ovabrw_petime_discount_start_time ovabrw_start_date ovabrw_datetimepicker" placeholder="<?php esc_attr_e('Start Time', 'ova-brw'); ?>" 
										    	name="ovabrw_petime_discount[<?php echo esc_attr($i);?>][start_time][]" value="<?php echo esc_attr( wp_date( $date_format .' '.$set_time_format, strtotime( $ovabrw_petime_discount[$i]['start_time'][$k] ) ) ); ?>" autocomplete="off"/>
										</td>
										<td width="30%">
										    <input type="text" data-time="<?php echo esc_attr( $time_format ); ?>" data-firstday="<?php echo esc_attr( $first_day ); ?>" class="ovabrw_petime_discount_end_time ovabrw_end_date ovabrw_datetimepicker" placeholder="<?php esc_attr_e('End Time', 'ova-brw'); ?>" 
										    	name="ovabrw_petime_discount[<?php echo esc_attr( $i );?>][end_time][]" value="<?php echo esc_attr( wp_date( $date_format .' '.$set_time_format, strtotime( $ovabrw_petime_discount[$i]['end_time'][$k] ) ) ); ?>" 
										    	autocomplete="off"/>
										</td>

										
									<td width="9%"><a href="#" class="delete_petime_discount">x</a></td>

									</tr> 
									
								<?php } }?>
							
								
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
			<?php } } ?>
		</tbody>

		<tfoot>
			<tr>
				<th colspan="6">
					<a href="#" class="button insert_petime_record" data-row="
						<?php
							ob_start();
							include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_petime_record.php' );
							echo esc_attr( ob_get_clean() );
						?>

					">
					<?php esc_html_e( 'Add Period Time', 'ova-brw' ); ?></a>
					</a>

					
				</th>
			</tr>
		</tfoot>

	</table>
</div>


