<div class="ovabrw_features">
	<table class="widefat">

		<thead>
			<tr>
				<?php if( apply_filters( 'ovabrw_show_icon_features', 'true' ) ){ ?>
					<th><?php esc_html_e( 'Icon Class', 'ova-brw' ); ?></th>
				<?php } ?>

				<th><?php esc_html_e( 'Label', 'ova-brw' ); ?></th>
				<th class="description-features"><?php esc_html_e( 'Description', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Show In Category', 'ova-brw' ); ?></th>
			</tr>
		</thead>

		<tbody class="wrap_rt_features">
			<!-- Append html here -->
			<?php if( $ovabrw_features_desc = get_post_meta( $post_id, 'ovabrw_features_desc', 'false' ) ){
					$ovabrw_features_label = get_post_meta( $post_id, 'ovabrw_features_label', 'false' );
					$ovabrw_features_icons = get_post_meta( $post_id, 'ovabrw_features_icons', 'false' );
					$ovabrw_features_special = get_post_meta( $post_id, 'ovabrw_features_special', 'false' );

					for( $i = 0 ; $i < count( $ovabrw_features_desc ); $i++ ) {
			?>

				<tr class="tr_rt_feature">

					<?php if( apply_filters( 'ovabrw_show_icon_features', 'true' ) ){ ?>
					    <td width="20%">
					      <input type="text" name="ovabrw_features_icons[]" placeholder="<?php esc_attr_e( 'Icon-Class', 'ova-brw' ); ?>"
					      value="<?php echo isset( $ovabrw_features_icons[$i] ) ? esc_attr( $ovabrw_features_icons[$i] ) : esc_attr(''); ?>" />
					    </td>
					<?php } ?>


				    <td width="20%">
				      <input type="text" name="ovabrw_features_label[]" placeholder="<?php esc_html_e( 'Label', 'ova-brw' ); ?>"
				      value="<?php echo isset( $ovabrw_features_label[$i] ) ? esc_attr( $ovabrw_features_label[$i] ) : esc_attr(''); ?>" />
				    </td>

				    <?php $cols = apply_filters( 'ovabrw_show_icon_features', 'true' ) ? '29%' : '49%'; ?>
				    <td width="<?php echo esc_attr( $cols ); ?>" class="description-features">
				      <input type="text" name="ovabrw_features_desc[]" placeholder="<?php esc_html_e( 'Description', 'ova-brw' ); ?>"
				      value="<?php echo isset( $ovabrw_features_desc[$i] ) ? esc_attr( $ovabrw_features_desc[$i] ) : esc_attr( '' ) ; ?>" />
				    </td>


				    <td width="20%">
				      <select  name="ovabrw_features_special[]" class="short_dura">
				    		<option value="yes" <?php echo ( isset( $ovabrw_features_special[$i] ) && $ovabrw_features_special[$i] == 'yes' ) ? 'selected': ''; ?> ><?php esc_html_e( 'Yes', 'ova-brw' ); ?></option>
				    		<option value="no" <?php echo ( isset( $ovabrw_features_special[$i] ) && $ovabrw_features_special[$i] == 'no' ) ? 'selected': ''; ?> ><?php esc_html_e( 'No', 'ova-brw' ); ?></option>
				    	</select>
				    </td>

				    <td width="1%"><a href="#" class="delete_feature">x</a></td>
				    
				</tr>

			<?php } } ?>
		</tbody>

		<tfoot>
			<tr>
				<th colspan="6">
					<a href="#" class="button insert_rt_feature" data-row="
						<?php
							ob_start();
							include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_feature_field.php' );
							echo esc_attr( ob_get_clean() );
						?>

					">
					<?php esc_html_e( 'Add Feature', 'ova-brw' ); ?></a>
					</a>

					
				</th>
			</tr>
		</tfoot>

	</table>
</div>


