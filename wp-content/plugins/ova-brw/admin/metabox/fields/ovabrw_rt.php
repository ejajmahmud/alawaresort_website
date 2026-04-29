<div class="ovabrw_rt">
	<table class="widefat">

		<thead>
			<tr>
				<th class="rt_day" ><?php esc_html_e( 'Price/Day', 'ova-brw' ); ?></th>
				<th class="rt_hour"><?php esc_html_e( 'Price/Hour', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Start Date (DD-MM)', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'End Date (DD-MM)', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Discount in Special Time (DST)', 'ova-brw' ); ?></th>
			</tr>
		</thead>

		<tbody class="wrap_rt">
			<!-- Append html here -->
			<?php
			$ovabrw_price_type = get_post_meta( $post_id, 'ovabrw_price_type', true );
			$ovabrw_rt_price_hour = get_post_meta( $post_id, 'ovabrw_rt_price_hour', true );
			$ovabrw_rt_price = get_post_meta( $post_id, 'ovabrw_rt_price', true );

			// Get first day in week
			$first_day = get_option( 'ova_brw_calendar_first_day', '0' );

			if ( empty( $first_day ) ) {
				$first_day = 0;
			}

			if( $ovabrw_rt_price || $ovabrw_rt_price_hour ){ 
				
				

				$ovabrw_rt_startdate = get_post_meta( $post_id, 'ovabrw_rt_startdate', true );
				$ovabrw_rt_starttime = get_post_meta( $post_id, 'ovabrw_rt_starttime', true );
				$ovabrw_start_timestamp = get_post_meta( $post_id, 'ovabrw_start_timestamp', true );
				$ovabrw_rt_string_start = get_post_meta( $post_id, 'ovabrw_rt_string_start', true );

				$ovabrw_rt_enddate = get_post_meta( $post_id, 'ovabrw_rt_enddate', true );
				$ovabrw_rt_endtime = get_post_meta( $post_id, 'ovabrw_rt_endtime', true );
				$ovabrw_end_timestamp = get_post_meta( $post_id, 'ovabrw_end_timestamp', true) ;
				$ovabrw_rt_string_end = get_post_meta( $post_id, 'ovabrw_rt_string_end', true) ;

				$ovabrw_rt_discount = get_post_meta( $post_id, 'ovabrw_rt_discount', true );

				$time_format = ovabrw_get_time_format();


				if ( $ovabrw_price_type !== 'hour' && ! empty( $ovabrw_rt_price ) ) {

					for( $i = 0 ; $i < count( $ovabrw_rt_price ); $i++ ) {
					
			?>

				<tr class="row_rt_record" data-pos="<?php echo esc_attr($i); ?>">
					
					
				    <td width="15%">
				    	<?php if ( ! empty( $ovabrw_rt_price[$i] ) ) : ?>
				        <input type="text" placeholder="<?php esc_attr_e('10.5', 'ova-brw'); ?>" name="ovabrw_rt_price[]" value="<?php echo esc_attr($ovabrw_rt_price[$i]); ?>" class="ovabrw_rt_price" >
				        <?php endif ?>
				    </td>
					
				    <td width="15%">
				    	<?php if ( ! empty( $ovabrw_rt_price_hour[$i] ) ) : ?>
				        <input type="text"  placeholder="<?php esc_attr_e('10.5', 'ova-brw'); ?>" value="<?php echo esc_attr($ovabrw_rt_price_hour[$i]); ?>" name="ovabrw_rt_price_hour[]"  class="ovabrw_rt_price_hour"/>
				        <?php endif ?>
				    </td>

				    <td width="15%">
				    	
						<input type="hidden" name="ovabrw_start_timestamp[]" value="<?php echo esc_attr($ovabrw_start_timestamp[$i]); ?>" >
						<input type="hidden" name="ovabrw_rt_string_start[]" value="<?php echo esc_attr($ovabrw_rt_string_start[$i]); ?>" >

					    <input type="text" name="ovabrw_rt_startdate[]" placeholder="<?php esc_attr_e( 'DD-MM-YY', 'ova-brw' ); ?>" value="<?php echo esc_attr($ovabrw_rt_startdate[$i]); ?>" class="ovabrw_rt_startdate ova_brw_datepicker_short ovabrw_start_date" autocomplete="off" data-firstday="<?php echo esc_attr( $first_day ); ?>">

					    <input type="text" data-time="<?php echo esc_attr($time_format); ?>" name="ovabrw_rt_starttime[]" placeholder="<?php esc_attr_e( 'Start Time', 'ova-brw' ); ?>" value="<?php echo esc_attr($ovabrw_rt_starttime[$i]); ?>" class="ovabrw_rt_starttime  ova_brw_datepicker_time " autocomplete="off">
				    </td>

				    <td width="15%">
						<input type="hidden" name="ovabrw_end_timestamp[]" value="<?php echo esc_attr($ovabrw_end_timestamp[$i]); ?>" >
						<input type="hidden" name="ovabrw_rt_string_end[]" value="<?php echo esc_attr($ovabrw_rt_string_end[$i]); ?>" >

				      	<input type="text" name="ovabrw_rt_enddate[]" placeholder="<?php esc_attr_e( 'DD-MM-YY', 'ova-brw' ); ?>" value="<?php echo esc_attr($ovabrw_rt_enddate[$i]); ?>" class="ovabrw_rt_enddate ova_brw_datepicker_short ovabrw_end_date" autocomplete="off" data-firstday="<?php echo esc_attr( $first_day ); ?>">

				     	<input type="text" data-time="<?php echo esc_attr($time_format); ?>" name="ovabrw_rt_endtime[]" placeholder="<?php esc_attr_e( 'End Time', 'ova-brw' ); ?>" value="<?php echo esc_attr($ovabrw_rt_endtime[$i]); ?>" class="ovabrw_rt_endtime  ova_brw_datepicker_time " autocomplete="off">
				    </td>


				    <td width="49%">
				    	<table width="100%" class="ovabrw_rt_discount">

					      	<thead>
								<tr>
									<th><?php esc_html_e( 'Price', 'ova-brw' ); ?></th>
									<th><?php esc_html_e( 'Min - Max: Number', 'ova-brw' ); ?></th>
								</tr>
							</thead>

							<tbody class="real">

								<?php if( isset( $ovabrw_rt_discount[$i]['price'] ) ){ ?>
								<?php 
								for( $k = 0; $k < count( $ovabrw_rt_discount[$i]['price'] ); $k++ ){ 
									$min = array_key_exists( 'min', $ovabrw_rt_discount[$i]) ? $ovabrw_rt_discount[$i]['min'][$k] : "";
									$max = array_key_exists( 'max', $ovabrw_rt_discount[$i]) ? $ovabrw_rt_discount[$i]['max'][$k] : "";

									?>


									<tr class="tr_rt_discount">
																
										<td width="49%">
										    <input type="text" class="ovabrw_rt_discount_price input_per_100" placeholder="<?php esc_html_e('10.5', 'ova-brw') ?>" 
										    	name="ovabrw_rt_discount[<?php echo esc_attr($i);?>][price][]" value="<?php echo esc_attr( $ovabrw_rt_discount[$i]['price'][$k] ); ?>" />
										</td>

										<td width="49%">
										    
	

										      <input type="text" class="input_text short ovabrw_rt_discount_duration" placeholder="<?php esc_html_e( '1', 'ova-brw' ) ?>" name="ovabrw_rt_discount[<?php echo esc_attr($i); ?>][min][]" value="<?php echo esc_attr( $min ); ?>" />

										      <input  type="text" class="input_text short ovabrw_rt_discount_duration" placeholder="2" name="ovabrw_rt_discount[<?php echo esc_attr($i); ?>][max][]" value="<?php echo esc_attr( $max ); ?>" />

										      <?php $ovabrw_rt_discount_duration_type =  $ovabrw_rt_discount[$i]['duration_type'][$k]; ?>
										      <select class="short ovabrw_rt_discount_duration_type" name="ovabrw_rt_discount[<?php echo esc_attr($i); ?>][duration_type][]">
										          <option value="hours" <?php echo $ovabrw_rt_discount_duration_type == 'hours' ? 'selected': ''; ?> ><?php esc_html_e( 'Hour(s)', 'ova-brw' ); ?></option>
										          <option value="days" <?php echo $ovabrw_rt_discount_duration_type == 'days' ? 'selected': ''; ?> ><?php esc_html_e( 'Day(s)', 'ova-brw' ); ?></option>
										      </select>
										    
										</td>
									<td width="1%"><a href="#" class="delete_discount">x</a></td>

									</tr> 
									
								<?php } }?>
							
								
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
			<?php } } ?>
			<!-- end if ! hour, end for $ovabrw_rt_price -->

			<?php


			if ( $ovabrw_price_type === 'hour' && ! empty( $ovabrw_rt_price_hour ) ) {
				for( $i = 0 ; $i < count( $ovabrw_rt_price_hour ); $i++ ) {
			?>
				<tr class="row_rt_record" data-pos="<?php echo esc_attr($i); ?>">
					
					
				    <td width="15%">
				    	
				    </td>
					
				    <td width="15%">
				    	<?php if ( ! empty( $ovabrw_rt_price_hour[$i] ) ) : ?>
				        <input type="text"  placeholder="<?php esc_attr_e('Price/Hour', 'ova-brw'); ?>" value="<?php echo esc_attr($ovabrw_rt_price_hour[$i]); ?>" name="ovabrw_rt_price_hour[]"  class="ovabrw_rt_price_hour"/>
				        <?php endif ?>
				    </td>

				    <td width="15%">
				    	
						<input type="hidden" name="ovabrw_start_timestamp[]" value="<?php echo esc_attr($ovabrw_start_timestamp[$i]); ?>" >

					    <input type="text" name="ovabrw_rt_startdate[]" placeholder="<?php esc_attr_e( 'DD-MM', 'ova-brw' ); ?>" value="<?php echo esc_attr($ovabrw_rt_startdate[$i]); ?>" class="ovabrw_rt_startdate ova_brw_datepicker_short" autocomplete="off" data-firstday="<?php echo esc_attr( $first_day ); ?>">

					    <input type="text" data-time="<?php echo esc_attr($time_format); ?>" name="ovabrw_rt_starttime[]" placeholder="<?php esc_attr_e( 'DD-MM', 'ova-brw' ); ?>" value="<?php echo esc_attr($ovabrw_rt_starttime[$i]); ?>" class="ovabrw_rt_starttime ova_brw_datepicker_time " autocomplete="off">
				    </td>

				    <td width="15%">
						<input type="hidden" name="ovabrw_end_timestamp[]" value="<?php echo esc_attr($ovabrw_end_timestamp[$i]); ?>" >

				      	<input type="text" name="ovabrw_rt_enddate[]" placeholder="<?php esc_attr_e( 'To DD-MM', 'ova-brw' ); ?>" value="<?php echo esc_attr($ovabrw_rt_enddate[$i]); ?>" class="ovabrw_rt_enddate ova_brw_datepicker_short" autocomplete="off" data-firstday="<?php echo esc_attr( $first_day ); ?>">

				     	<input type="text" data-time="<?php echo esc_attr($time_format); ?>" name="ovabrw_rt_endtime[]" placeholder="<?php esc_attr_e( 'To DD-MM', 'ova-brw' ); ?>" value="<?php echo esc_attr($ovabrw_rt_endtime[$i]); ?>" class="ovabrw_rt_endtime ova_brw_datepicker_time " autocomplete="off">
				    </td>


				    <td width="49%">
				    	<table width="100%" class="ovabrw_rt_discount">

					      	<thead>
								<tr>
									<th><?php esc_html_e( 'Price', 'ova-brw' ); ?></th>
									<th><?php esc_html_e( 'Min - Max: Number', 'ova-brw' ); ?></th>
								</tr>
							</thead>

							<tbody class="real">

								<?php if( isset( $ovabrw_rt_discount[$i]['price'] ) ){ ?>
								<?php for( $k = 0; $k < count( $ovabrw_rt_discount[$i]['price'] ); $k++ ){ ?>


									<tr class="tr_rt_discount">
																
										<td width="49%">
										    <input type="text" class="ovabrw_rt_discount_price input_per_100" placeholder="<?php esc_html_e('10.5', 'ova-brw') ?>" 
										    	name="ovabrw_rt_discount[<?php echo esc_attr($i);?>][price][]" value="<?php echo esc_attr( $ovabrw_rt_discount[$i]['price'][$k] ); ?>" />
										</td>

										<td width="49%">

										      <input type="text" class="input_text short ovabrw_rt_discount_duration" placeholder="<?php esc_html_e( '1', 'ova-brw' ) ?>" name="ovabrw_rt_discount[<?php echo esc_attr($i); ?>][min][]" value="<?php echo esc_attr( $ovabrw_rt_discount[$i]['min'][$k] ); ?>" />

										      <input  type="text" class="input_text short ovabrw_rt_discount_duration" placeholder="2" name="ovabrw_rt_discount[<?php echo esc_attr($i); ?>][max][]" value="<?php echo esc_attr( $ovabrw_rt_discount[$i]['max'][$k] ); ?>" />

										      <?php $ovabrw_rt_discount_duration_type = $ovabrw_rt_discount[$i]['duration_type'][$k]; ?>
										      <select class="short ovabrw_rt_discount_duration_type" name="ovabrw_rt_discount[<?php echo esc_attr($i); ?>][duration_type][]">
										          <option value="hours" <?php echo $ovabrw_rt_discount_duration_type == 'hours' ? 'selected': ''; ?> ><?php esc_html_e( 'Hour(s)', 'ova-brw' ); ?></option>
										          <option value="days" <?php echo $ovabrw_rt_discount_duration_type == 'days' ? 'selected': ''; ?> ><?php esc_html_e( 'Day(s)', 'ova-brw' ); ?></option>
										      </select>
										    
										</td>
									<td width="1%"><a href="#" class="delete_discount">x</a></td>

									</tr> 
									
								<?php } }?>
							
								
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
			<?php } } ?>
			<!-- end if hour, end for $ovabrw_rt_price_hour -->
	

			<?php } ?>
			<!-- $ovabrw_rt_price || $ovabrw_rt_price_hour -->
		</tbody>

		<tfoot>
			<tr>
				<th colspan="6">
					<a href="#" class="button insert_rt_record" data-row="
						<?php
							ob_start();
							include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_rt_record.php' );
							echo esc_attr( ob_get_clean() );
						?>

					">
					<?php esc_html_e( 'Add ST', 'ova-brw' ); ?></a>
					</a>

					
				</th>
			</tr>
		</tfoot>

	</table>
</div>


