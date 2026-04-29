<?php 
	$ovabrw_location = get_post_meta( $post_id, 'ovabrw_location', true );
	$args = array(
		'post_type' => 'location',
		'posts_per_page' => '-1',
		'post_status' => 'publish'
	);
	$loc = new WP_Query( $args );
?>

<div style="display: inline-block;">
<br/><strong class="ovabrw_heading_section" style="float: left; margin-right: 20px;"><?php esc_html_e('Avaiable in', 'ova-brw'); ?></strong>

<?php if( $loc->have_posts() ): ?>
<select multiple name="ovabrw_location[]">
	<?php while( $loc->have_posts() ): $loc->the_post();
		$selected = in_array(get_the_title(), $ovabrw_location ) ? 'selected="selected"' : '';
	?>

	<option <?php echo wp_kses($selected, true); ?> value="<?php echo esc_attr( get_the_title() ); ?>"> <?php the_title(); ?> </option>
	<?php endwhile; ?>
</select>
<?php endif; wp_reset_postdata(); ?>
<br>

</div>