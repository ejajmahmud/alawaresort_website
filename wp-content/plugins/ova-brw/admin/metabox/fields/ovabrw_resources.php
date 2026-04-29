<div class="ovabrw_resources">
	<table class="widefat">

		<thead>
			<tr>
				<th><?php esc_html_e( 'Unique ID *', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Name *', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Price *', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Duration', 'ova-brw' ); ?></th>
			</tr>
		</thead>

		<tbody class="wrap_resources">
			<!-- Append html here -->
			<?php
				$ovabrw_rental_type = get_post_meta( $post_id, 'ovabrw_price_type', true );

				if( $ovabrw_resource_name = get_post_meta( $post_id, 'ovabrw_resource_name', 'false' ) ){ 
					
					$ovabrw_resource_price = get_post_meta( $post_id, 'ovabrw_resource_price', 'false' );

					
					$ovabrw_resource_duration_type = get_post_meta( $post_id, 'ovabrw_resource_duration_type', 'false' ) ? get_post_meta( $post_id, 'ovabrw_resource_duration_type', 'false' ) : array();

					$ovabrw_resource_id = get_post_meta( $post_id, 'ovabrw_resource_id', 'false' ) ? get_post_meta( $post_id, 'ovabrw_resource_id', 'false' ) : array();

					for( $i = 0 ; $i < count( $ovabrw_resource_name ); $i++ ) {
			?>

				<tr class="tr_rt_resource">

					<td width="15%">
				      <input type="text"  name="ovabrw_resource_id[]"
				      	value="<?php echo esc_attr($ovabrw_resource_id[$i]); ?>"
				      	placeholder="<?php esc_html_e( 'Not space', 'ova-brw' ); ?> " />
				    </td>

				    <td width="39%">
				      <input type="text" name="ovabrw_resource_name[]"
				      value="<?php echo esc_attr($ovabrw_resource_name[$i]); ?>" />
				    </td>

				    <td width="20%">
				    	<input type="text"  name="ovabrw_resource_price[]"
				    	value="<?php echo esc_attr($ovabrw_resource_price[$i]); ?>"
				    	placeholder="10.5" />
				    </td>

				    <td width="25%">
				    	
				    	<select  name="ovabrw_resource_duration_type[]" class="short_dura">
							
							<?php 
							$selected_hour = $ovabrw_resource_duration_type[$i] == 'hours' ? 'selected' : '';
							$selected_day = $ovabrw_resource_duration_type[$i] == 'days' ? 'selected' : '';
							$selected_total = $ovabrw_resource_duration_type[$i] == 'total' ? 'selected' : '';

							if( $ovabrw_rental_type == 'day' ) { 

								?>
								<option value="days" <?php echo esc_attr( $selected_day ) ?> ><?php esc_html_e( '/Days', 'ova-brw' ); ?></option>
					    		<option value="total" <?php echo esc_attr( $selected_total ) ?> ><?php esc_html_e( '/Total', 'ova-brw' ); ?></option>
							
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

				    <td width="1%"><a href="#" class="delete_resource">x</a></td>
				    
				</tr>

			<?php } } ?>
		</tbody>

		<tfoot>
			<tr>
				<th colspan="6">
					<a href="#" class="button insert_resources" data-row="
						<?php
							ob_start();
							include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_resources_field.php' );
							echo esc_attr( ob_get_clean() );
						?>

					">
					<?php esc_html_e( 'Add Resources', 'ova-brw' ); ?></a>
					</a>

					
				</th>
			</tr>
		</tfoot>

	</table>
</div>


