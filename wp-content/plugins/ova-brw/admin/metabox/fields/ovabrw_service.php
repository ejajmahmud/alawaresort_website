
<div class="ovabrw_service">
	<div class="wrap_ovabrw_service_group">
		<?php
			
			$ovabrw_label_service = get_post_meta( $post_id, 'ovabrw_label_service', true );

			$ovabrw_service_required = get_post_meta( $post_id, 'ovabrw_service_required', true );

			$ovabrw_service_id = get_post_meta( $post_id, 'ovabrw_service_id', true );
			$ovabrw_service_name = get_post_meta( $post_id, 'ovabrw_service_name', true );
			$ovabrw_service_price = get_post_meta( $post_id, 'ovabrw_service_price', true );
			$ovabrw_service_duration_type = get_post_meta( $post_id, 'ovabrw_service_duration_type', true );
			
			
			if( $ovabrw_label_service ) {
				for( $i = 0; $i < count( $ovabrw_label_service ); $i++ ){

			?>
			<div class="ovabrw_service_group">

				<div class="service_input">
					<div class="ovabrw_label_service">

						<span class="ovabrw_label_service">
							<?php esc_html_e( 'Label *', 'ova-brw' ) ?>
						</span>
						<input type="text" class="ovabrw_input_label" name="ovabrw_label_service[]" value="<?php echo esc_attr( $ovabrw_label_service[$i] ) ?>">

						<span>
							<?php esc_html_e( 'Required', 'ova-brw' ); ?>
						</span>
						<select name="ovabrw_service_required[]">
							<option value="yes" <?php if( $ovabrw_service_required[$i] == 'yes' ) echo 'selected'; ?> >
								<?php esc_html_e( 'Yes', 'ova-brw' ); ?>
							</option>
							<option value="no" <?php if( $ovabrw_service_required[$i] == 'no' ) echo 'selected'; ?> >
								<?php esc_html_e( 'No', 'ova-brw' ); ?>
							</option>
						</select>

					</div>

					<a href="#" class="delete_service_group">x</a>
				</div>

				<table class="widefat">

					<thead>
						<tr>
							<th><?php esc_html_e( 'Unique ID *', 'ova-brw' ); ?></th>
							<th><?php esc_html_e( 'Name *', 'ova-brw' ); ?></th>
							<th><?php esc_html_e( 'Price *', 'ova-brw' ); ?></th>
							<th><?php esc_html_e( 'Duration', 'ova-brw' ); ?></th>
						</tr>
					</thead>

					<tbody class="wrap_service">
						<?php
						

							if( isset( $ovabrw_service_id[$i] ) && is_array( $ovabrw_service_id[$i] ) && ! empty( $ovabrw_service_id ) ){
								foreach( $ovabrw_service_id[$i] as $key => $val ) {
							
						?>
								<tr class="tr_rt_service">

								    <td width="15%">
								      <input type="text" name="ovabrw_service_id[<?php echo esc_attr($i); ?>][]" class="ovabrw_service_key" placeholder="<?php esc_attr_e( 'Not space', 'ova-brw' ); ?> " value="<?php echo esc_attr( $val ); ?>" />
								    </td>

								    <td width="39%">
								      <input type="text" name="ovabrw_service_name[<?php echo esc_attr($i); ?>][]" value="<?php echo esc_attr( $ovabrw_service_name[$i][$key] ); ?>" />
								    </td>

								  
								    <td width="20%">
								    	<input type="text" name="ovabrw_service_price[<?php echo esc_attr($i); ?>][]"	 value="<?php echo esc_attr( $ovabrw_service_price[$i][$key] ); ?>"
								    		placeholder="10.5" />
								    </td>

								    <td width="25%">
								      <select  name="ovabrw_service_duration_type[<?php echo esc_attr($i); ?>][]" class="short_dura">


										<?php 
											$selected_hour = $ovabrw_service_duration_type[$i][$key] == 'hours' ? 'selected' : '';
											$selected_day = $ovabrw_service_duration_type[$i][$key] == 'days' ? 'selected' : '';
											$selected_total = $ovabrw_service_duration_type[$i][$key] == 'total' ? 'selected' : '';

											if( $ovabrw_rental_type == 'day' ) { 

												?>
												<option value="days" <?php echo esc_attr( $selected_day ); ?> ><?php esc_html_e( '/Days', 'ova-brw' ); ?></option>
									    		<option value="total" <?php echo esc_attr( $selected_total ); ?> ><?php esc_html_e( '/Total', 'ova-brw' ); ?></option>
											
											<?php  } else if( $ovabrw_rental_type == 'hour' ) { 

												?> 
												<option value="hours"  <?php echo esc_attr( $selected_hour ) ?> ><?php esc_html_e( '/Hours', 'ova-brw' ); ?></option>
									    		<option value="total" <?php echo esc_attr( $selected_total ) ?> ><?php esc_html_e( '/Total', 'ova-brw' ); ?></option>
											<?php  } else if( $ovabrw_rental_type == 'mixed' || $ovabrw_rental_type == 'period_time' )  { 

													?> 
													<option value="hours" <?php echo esc_attr( $selected_hour ) ?> ><?php esc_html_e( '/Hours', 'ova-brw' ); ?></option>
													<option value="days" <?php echo esc_attr( $selected_day ) ?> ><?php esc_html_e( '/Days', 'ova-brw' ); ?></option>
										    		<option value="total" <?php echo esc_attr( $selected_total ) ?> ><?php esc_html_e( '/Total', 'ova-brw' ); ?></option>

											<?php } else if( $ovabrw_rental_type == 'transportation' ) {  ?> 
													<option value="total" selected ><?php esc_html_e( '/Total', 'ova-brw' ); ?></option>
											<?php } ?>



								        </select>
								    </td>

								    <td width="1%"><a href="#" class="delete_service">x</a></td>
								</tr>
						<?php
							}
						}
						?>
					</tbody>

					<tfoot>
						<tr>
							<th colspan="6">
								<a href="#" class="button insert_service_option" >
								<?php esc_html_e( 'Add Option', 'ova-brw' ); ?>
								</a>
								<div class="ovabrw_content_service" style="display: none">
									<table>
										<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_service_field.php' ); ?>
									</table>
								</div>
							</th>
						</tr>
					</tfoot>

				</table>
			</div>
		<?php
				}
			}
		?>
	</div>
	


	<a href="#" class="button insert_service_group" data-row="
		<?php
			ob_start();
			include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_service_group.php' );
			echo esc_attr( ob_get_clean() );
		?>

	">
	<?php esc_html_e( 'Add Service', 'ova-brw' ); ?></a>
	</a>
</div>


