<div class="ovabrw_price_daily">
	<table class="widefat">

		<thead>
			<tr>
				<th><?php esc_html_e( 'Daily', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Price', 'ova-brw' ); ?></th>
			</tr>
		</thead>

		<tbody class="wrap_rt_features">
			<!-- Append html here -->
				<?php
					$ovabrw_daily_monday = get_post_meta( $post_id, 'ovabrw_daily_monday', true );
					$ovabrw_daily_tuesday = get_post_meta( $post_id, 'ovabrw_daily_tuesday', true );
					$ovabrw_daily_wednesday = get_post_meta( $post_id, 'ovabrw_daily_wednesday', true );
					$ovabrw_daily_thursday = get_post_meta( $post_id, 'ovabrw_daily_thursday', true );
					$ovabrw_daily_friday = get_post_meta( $post_id, 'ovabrw_daily_friday', true );
					$ovabrw_daily_saturday = get_post_meta( $post_id, 'ovabrw_daily_saturday', true );
					$ovabrw_daily_sunday = get_post_meta( $post_id, 'ovabrw_daily_sunday', true );
			?>

				<tr class="tr_rt_feature">

				    <td width="30%">
				     <?php echo esc_html__( 'Monday', 'ova-brw' ) ?>
				    </td>

				    <td width="80%">
				      <input type="text" name="ovabrw_daily_monday" placeholder="10.5"
				      value="<?php echo esc_attr( $ovabrw_daily_monday ); ?>" />
				    </td>

				</tr>

				<tr class="tr_rt_feature">

				    <td width="30%">
				     <?php echo esc_html__( 'Tuesday', 'ova-brw' ) ?>
				    </td>

				    <td width="80%">
				      <input type="text" name="ovabrw_daily_tuesday" placeholder="10.5"
				      value="<?php echo esc_attr( $ovabrw_daily_tuesday ); ?>" />
				    </td>

				</tr>

				<tr class="tr_rt_feature">

				    <td width="30%">
				     <?php echo esc_html__( 'Wednesday', 'ova-brw' ) ?>
				    </td>

				    <td width="80%">
				      <input type="text" name="ovabrw_daily_wednesday" placeholder="10.5"
				      value="<?php echo esc_attr( $ovabrw_daily_wednesday ); ?>" />
				    </td>

				</tr>

				<tr class="tr_rt_feature">

				    <td width="30%">
				     <?php echo esc_html__( 'Thursday', 'ova-brw' ) ?>
				    </td>

				    <td width="80%">
				      <input type="text" name="ovabrw_daily_thursday" placeholder="10.5"
				      value="<?php echo esc_attr( $ovabrw_daily_thursday ); ?>" />
				    </td>

				</tr>

				<tr class="tr_rt_feature">

				    <td width="30%">
				     <?php echo esc_html__( 'Friday', 'ova-brw' ) ?>
				    </td>

				    <td width="80%">
				      <input type="text" name="ovabrw_daily_friday" placeholder="10.5"
				      value="<?php echo esc_attr( $ovabrw_daily_friday ); ?>" />
				    </td>

				</tr>

				<tr class="tr_rt_feature">

				    <td width="30%">
				     <?php echo esc_html__( 'Saturday', 'ova-brw' ) ?>
				    </td>

				    <td width="80%">
				      <input type="text" name="ovabrw_daily_saturday" placeholder="10.5"
				      value="<?php echo esc_attr( $ovabrw_daily_saturday ); ?>" />
				    </td>

				</tr>

				<tr class="tr_rt_feature">

				    <td width="30%">
				     <?php echo esc_html__( 'Sunday', 'ova-brw' ) ?>
				    </td>

				    <td width="80%">
				      <input type="text" name="ovabrw_daily_sunday" placeholder="10.5"
				      value="<?php echo esc_attr( $ovabrw_daily_sunday ); ?>" />
				    </td>

				</tr>

		</tbody>

	</table>
</div>


