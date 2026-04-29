<div class="ovabrw_service_group">
		
		<div class="service_input">
			<div class="ovabrw_label_service">

				<span class="ovabrw_label_service">
					<?php esc_html_e( 'Label *', 'ova-brw' ) ?>
				</span>
				<input type="text" class="ovabrw_input_label" name="ovabrw_label_service[]" value="">

				<span>
					<?php esc_html_e( 'Required', 'ova-brw' ); ?>
				</span>
				<select name="ovabrw_service_required[]">
					<option value="yes">
						<?php esc_html_e( 'Yes', 'ova-brw' ); ?>
					</option>
					<option value="no">
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
			
		</tbody>

		<tfoot>
			<tr>
				<th colspan="6">
					<a href="#" class="button insert_service_option">

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