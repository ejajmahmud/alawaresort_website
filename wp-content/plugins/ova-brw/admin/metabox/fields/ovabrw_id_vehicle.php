<?php global $post; $temp_post = $post;

	$ovabrw_id_vehicles = get_post_meta( $post_id, 'ovabrw_id_vehicles', true );

	$all_id_vehicles = ovabrw_get_all_id_vehicles();
	$html_option = '';
?>

<div style="display: inline-block;" class="select_id_vehicle">

	<br/>
	<strong class="ovabrw_heading_section" style="float: left; margin-right: 20px;">
		<?php esc_html_e('Choose Vehicles', 'ova-brw'); ?>
	</strong>

	<?php if( $all_id_vehicles->have_posts() ): ?>

		<select multiple name="ovabrw_id_vehicles[]" style="width: 200px; height: 200px; margin-left: 15px;">
			
			<?php while( $all_id_vehicles->have_posts() ): $all_id_vehicles->the_post(); 

				$id_vehicle = get_post_meta( get_the_id(), 'ovabrw_id_vehicle', true ) ? get_post_meta( get_the_id(), 'ovabrw_id_vehicle', true ) : '';

				$selected = is_array($ovabrw_id_vehicles) && in_array( $id_vehicle, $ovabrw_id_vehicles ) ? 'selected="selected"' : '';

				$selected_option = is_array($ovabrw_id_vehicles) && in_array( $id_vehicle, $ovabrw_id_vehicles ) ? "selected='selected'" : "";

				$html_option .= "<option ".wp_kses($selected_option, true)." value='".esc_attr( $id_vehicle )."'' >".get_the_title()."</option>";

			?>

				<option <?php echo wp_kses($selected, true); ?> value="<?php echo esc_attr( $id_vehicle ); ?>"> <?php the_title(); ?> </option>

			<?php endwhile; ?>

		</select>

	<?php else : ?>
		<select style="display: none" multiple name="ovabrw_id_vehicles[]" style="width: 200px; height: 200px; margin-left: 15px;"></select>

	<?php endif; wp_reset_postdata(); $post = $temp_post; ?>
	<div style="display: none" class="ovabrw_html_option" data-html_all_id_vehicle="<?php echo esc_attr( $html_option ); ?>"></div>
	<br><br>
	
	<div style="clear:both;"></div>
	<p><?php esc_html_e('You must select at least one vehicle. If you want to add a Vehicle, you need to go to: Manage Vehicle >> Add Vehicles', 'ova-brw'); ?></p>

</div>