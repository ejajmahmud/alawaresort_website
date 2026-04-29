<div class="ovabrw_local_price">
	<table class="widefat">

		<thead>
			<tr>
				<th><?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Price', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Time (Minute)', 'ova-brw' ); ?></th>
			</tr>
		</thead>

		<tbody class="wrap_resources">
			<!-- Append html here -->
			<?php 

			if( class_exists('OVABRW') && class_exists('woocommerce') ){
			  $location_arr = ovabrw_get_locations_array();
			}else{
			  $location_arr = array();
			}

			$ovabrw_pickup_location = get_post_meta( $post_id, 'ovabrw_pickup_location', 'false' );
			$ovabrw_dropoff_location = get_post_meta( $post_id, 'ovabrw_dropoff_location', 'false' );
			$ovabrw_price_location = get_post_meta( $post_id, 'ovabrw_price_location', 'false' );
			$ovabrw_location_time = get_post_meta( $post_id, 'ovabrw_location_time', 'false' );

			if( $ovabrw_pickup_location && ! empty( $location_arr ) ){ 

					for( $i = 0 ; $i < count( $ovabrw_pickup_location ); $i++ ) {
			?>

				<tr class="tr_rt_local_price">


				    <td width="30%">
				      <select  name="ovabrw_pickup_location[]" data-number_loc="<?php echo esc_attr( count( $location_arr ) ) ?>" >
				      	<option value="" ><?php echo esc_html__( 'Select Location', 'ova-brw' ); ?></option>
							<?php 
							if( $location_arr ) :
								foreach( $location_arr as $location ) :
									$selected = ( isset( $ovabrw_pickup_location[$i] ) && $ovabrw_pickup_location[$i] == $location ) ? 'selected' : '';
								?>
								<option value="<?php echo esc_attr( $location ) ?>"  <?php echo esc_attr( $selected ) ?> ><?php echo esc_html( $location ); ?></option>
							<?php endforeach; endif ?>
					    </select>
				    </td>

				    <td width="30%">
				      <select  name="ovabrw_dropoff_location[]" >
				      		<option value="" ><?php echo esc_html__( 'Select Location', 'ova-brw' ); ?></option>
							<?php 
							if( $location_arr ) :
								foreach( $location_arr as $location ) :
									$selected = ( isset( $ovabrw_dropoff_location[$i] ) && $ovabrw_dropoff_location[$i] == $location ) ? 'selected' : '';
								?>
								<option value="<?php echo esc_attr( $location ) ?>"  <?php echo esc_attr( $selected ) ?> ><?php echo esc_html( $location ); ?></option>
							<?php endforeach; endif ?>
					    </select>
				    </td>

				    <td width="20%">
				    	<input type="text" name="ovabrw_price_location[]" value="<?php echo isset( $ovabrw_price_location[$i] ) ?   esc_attr( $ovabrw_price_location[$i] ) : "" ?>" placeholder="<?php esc_html_e( '10', 'ova-brw' ) ?>" />
				    </td>

				    <td width="19%">
				    	<input type="text" name="ovabrw_location_time[]" value="<?php echo isset( $ovabrw_location_time[$i] ) ?  esc_attr( $ovabrw_location_time[$i] ) : "" ?>" placeholder="<?php esc_html_e( '60', 'ova-brw' ) ?>" />
				    </td>

				    <td width="1%"><a href="#" class="delete_local_price">x</a></td>
				    
				</tr>

			<?php } } ?>
		</tbody>

		<tfoot>
			<tr>
				<th colspan="6">
					<a href="#" class="button insert_local_price" data-row="
						<?php
							ob_start();
							include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_local_price_field.php' );
							echo esc_attr( ob_get_clean() );
						?>

					">
					<?php esc_html_e( 'Add Local Price', 'ova-brw' ); ?></a>
					</a>

					
				</th>
			</tr>
		</tfoot>

	</table>
</div>


