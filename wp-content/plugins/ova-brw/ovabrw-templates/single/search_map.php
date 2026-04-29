<?php if( ! defined( 'ABSPATH' ) ) exit();

$data_get = $_GET;
$ovabrw_name_product 	= isset( $data_get['ovabrw_name_product'] ) ? sanitize_text_field( $data_get['ovabrw_name_product'] ) : '';
$cat 					= isset( $data_get['cat'] ) ? sanitize_text_field( $data_get['cat'] ) : '';
$ovabrw_pickup_loc 		= isset( $data_get['ovabrw_pickup_loc'] ) ? sanitize_text_field( $data_get['ovabrw_pickup_loc'] ) : '';
$ovabrw_dropoff_loc 	= isset( $data_get['ovabrw_dropoff_loc'] ) ? sanitize_text_field( $data_get['ovabrw_dropoff_loc'] ) : '';
$ovabrw_pickup_date 	= isset( $data_get['ovabrw_pickup_date'] ) ? sanitize_text_field( $data_get['ovabrw_pickup_date'] ) : '';
$ovabrw_pickoff_date 	= isset( $data_get['ovabrw_pickoff_date'] ) ? sanitize_text_field( $data_get['ovabrw_pickoff_date'] ) : '';
$ovabrw_attribute 		= isset( $data_get['ovabrw_attribute'] ) ? sanitize_text_field( $data_get['ovabrw_attribute'] ) : '';
$ovabrw_attribute_value = isset( $data_get['ovabrw_attribute_value'] ) ? sanitize_text_field( $data_get['ovabrw_attribute_value'] ) : '';
$ovabrw_tag_product 	= isset( $data_get['ovabrw_tag_product'] ) ? sanitize_text_field( $data_get['ovabrw_tag_product'] ) : '';
$map_lat 				= isset( $data_get['map_lat'] ) ? sanitize_text_field( $data_get['map_lat'] ) : '';
$map_lng 				= isset( $data_get['map_lng'] ) ? sanitize_text_field( $data_get['map_lng'] ) : '';
$map_address 			= isset( $data_get['map_address'] ) ? sanitize_text_field( $data_get['map_address'] ) : '';
$map_name 				= isset( $data_get['map_name'] ) ? sanitize_text_field( $data_get['map_name'] ) : '';
$show_featured 			= isset( $data_get['show_featured'] ) ? sanitize_text_field( $data_get['show_featured'] ) : '';

extract( $args );

$date_format 	= ovabrw_get_date_format();
$hour_default 	= ovabrw_get_setting( get_option( 'ova_brw_booking_form_default_hour', '07:00' ) );
$time_step 		= ovabrw_get_setting( get_option( 'ova_brw_booking_form_step_time', '30' ) );
$lat_default 	= ovabrw_get_setting( get_option( 'ova_brw_latitude_map_default', '39.177972' ) );
$lng_default 	= ovabrw_get_setting( get_option( 'ova_brw_longitude_map_default', '-100.36375' ) );

if ( ! empty( $lat_default ) ) {
	$lat_default = '39.177972';
}

if ( ! empty( $lng_default ) ) {
	$lng_default = '-100.36375';
}

$data = array(
	'orderby' 			=> $orderby,
	'order' 			=> $order,
	'posts_per_page' 	=> $posts_per_page,
	'featured' 			=> $show_featured
);

$products 		= ovabrw_search_products( $data );
$have_map 		= ( $show_map == 'yes' ) ? ' ova_have_map' : '';

$time_picker = 'false';
if ( 'yes' === $show_time ) {
	$time_picker = 'true';
}

// Get first day in week
$first_day = get_option( 'ova_brw_calendar_first_day', '0' );

if ( empty( $first_day ) ) {
	$first_day = 0;
}

?>

<div class="elementor_search_map<?php echo esc_attr( $have_map ); ?>">
	<?php if ( $show_map == 'yes'): ?>
		<div class="toggle_wrap">
			<span data-value="wrap_search" class="active"><?php esc_html_e( 'Results', 'ova-brw' ); ?></span>
			<span data-value="wrap_map"><?php esc_html_e( 'Map', 'ova-brw' ); ?></span>
		</div>
	<?php endif; ?>
	<div class="wrap_search_map">
		<!-- Search Map -->
		<div class="wrap_search">
			<?php if ( $show_filter == 'yes' ): ?>
				<div class="fields_search ovabrw_wd_search">
					<span class="toggle_filters ">
						<?php esc_html_e( 'Toggle Filters', 'ova-brw' ); ?>
						<i class="icon_down arrow_triangle-down"></i>
						<i class="icon_up arrow_triangle-up"></i>
					</span>
					<form class="form_search_map" autocomplete="off" autocapitalize="none">
						<div class="wrap_content field">
							<?php
								// Fields
								foreach ( $args as $key => $value) {
									if ( strpos( $key,'field_' ) !== false ) {
										switch ( $args[$key] ) {
											case 'name':
												?>
												<div class="label_search wrap_search_name">
													<input type="text" name="ovabrw_name_product" autocomplete="off" 
															value="<?php echo esc_attr( $ovabrw_name_product ); ?>" 
															autocapitalize="none" 
															placeholder="<?php esc_attr_e( 'Product Name', 'ova-brw' ); ?>" />
												</div>
												<?php
												break;

											case 'category':
												?>
												<div class="label_search wrap_search_category">
													<?php echo wp_kses( ovabrw_cat_rental( $cat, '', '' ), array(
														'select' => array()
													) ); ?>
												</div>
												<?php
												break;

											case 'location':
												?>
												<div class="label_search wrap_search_location">
													<input type="hidden" name="map_lat" id="map_lat" autocomplete="off" autocapitalize="none" value="<?php echo esc_attr( $map_lat ); ?>" />
													<input type="hidden" name="map_lng" id="map_lng" autocomplete="off" autocapitalize="none" value="<?php echo esc_attr( $map_lng ); ?>" />
													<input type="text" id="pac-input" name="map_address" class="controls" 
															placeholder="<?php esc_attr_e( 'Location', 'ova-brw' ); ?>" 
															autocomplete="off" autocapitalize="none" 
															value="<?php echo esc_attr( $map_address ); ?>" />
													<i class="locate_me icon_circle-slelected" id="locate_me" title="<?php esc_html_e('Find my location', 'ova-brw'); ?>"></i>
													<input type="hidden" value="" name="map_name" id="map_name"  
															autocomplete="off" autocapitalize="none" 
															value="<?php echo esc_attr( $map_name ); ?>" />
												</div>
												<?php
												break;

											case 'start_location':
												?>
												<div class="label_search wrap_search_start_location">
													<?php ovabrw_get_locations_html( 'ovabrw_pickup_loc', 'ovabrw_pickup_loc', $ovabrw_pickup_loc ); ?>
												</div>
												<?php
												break;

											case 'end_location':
												?>
												<div class="label_search wrap_search_end_location">
													<?php ovabrw_get_locations_html( 'ovabrw_dropoff_loc', 'ovabrw_pickup_loc', $ovabrw_dropoff_loc ); ?>
												</div>
												<?php
												break;

											case 'start_date':
												?>
												<div class="label_search wrap_search_start_date">
													<input type="text" name="ovabrw_pickup_date" 
															value="<?php echo esc_attr( $ovabrw_pickup_date ); ?>" 
															onkeydown="return false" 
															class="ovabrw_datetimepicker ovabrw_start_date" 
															placeholder="<?php esc_html_e( 'Pick-up date ...', 'ova-brw' ); ?>" autocomplete="off" 
															data-hour_default="<?php echo esc_attr( $hour_default ); ?>" 
															data-time_step="<?php echo esc_attr( $time_step ); ?>" 
															data-dateformat="<?php echo esc_attr( $date_format ); ?>" 
															data-firstday="<?php echo esc_attr( $first_day ); ?>" 
															timepicker="<?php echo esc_attr( $time_picker ); ?>" />
												</div>
												<?php
												break;

											case 'end_date':
												?>
												<div class="label_search wrap_search_end_date">
													<input type="text" name="ovabrw_pickoff_date" 
															value="<?php echo esc_attr( $ovabrw_pickoff_date ); ?>" 
															onkeydown="return false" 
															class="ovabrw_datetimepicker ovabrw_end_date" 
															placeholder="<?php esc_html_e( 'Drop-off date ...', 'ova-brw' ); ?>" autocomplete="off" 
															data-hour_default="<?php echo esc_attr( $hour_default ); ?>" 
															data-time_step="<?php echo esc_attr( $time_step ); ?>" 
															data-dateformat="<?php echo esc_attr( $date_format ); ?>" 
															data-firstday="<?php echo esc_attr( $first_day ); ?>" 
															timepicker="<?php echo esc_attr( $time_picker ); ?>" />
												</div>
												<?php
												break;
											
											case 'attribute':

												if ( ovabrw_dropdown_attributes_html('', $ovabrw_attribute, $ovabrw_attribute_value) ){ ?>
													<div class="label_search wrap_search_attribute ovabrw_search">
														<?php ovabrw_dropdown_attributes_html('', $ovabrw_attribute, $ovabrw_attribute_value); ?>
													</div>
												<?php }
										

												if ( ovabrw_dropdown_attributes_value_html('', $ovabrw_attribute, $ovabrw_attribute_value) ){
													ovabrw_dropdown_attributes_value_html('', $ovabrw_attribute, $ovabrw_attribute_value);
													}
							
												break;


											case 'tag':
												?>
												<div class="label_search wrap_search_tag ovabrw_wd_search">
													<input type="text" name="ovabrw_tag_product" autocomplete="off" 
															value="<?php echo esc_attr( $ovabrw_tag_product ); ?>" 
															autocapitalize="none" 
															placeholder="<?php esc_html_e('Product Tag', 'ova-brw'); ?>" />
												</div>
												<?php

											default:
												break;
										}
									}
								}
								// End Fields

								// Taxonomies
								$args_taxonomy 	= array();
								$taxonomies 	= get_option( 'ovabrw_custom_taxonomy', array() );
								$show_taxonomy 	= get_option( 'ova_brw_search_show_tax_depend_cat', 'yes' );
								if ( $list_taxonomy_custom && is_array( $list_taxonomy_custom ) ) {
									foreach ( $list_taxonomy_custom as $obj_taxonomy ) {
										$taxonomy_slug = $obj_taxonomy['taxonomy_custom'];
										$taxonomy_name = isset( $obj_taxonomy['taxonomy_custom_label'] ) ? $obj_taxonomy['taxonomy_custom_label'] : '';

										if ( isset( $taxonomies[$taxonomy_slug] ) && ! empty( $taxonomies[$taxonomy_slug] ) ) {
											if ( ! $taxonomy_name ) {
												$taxonomy_name = $taxonomies[$taxonomy_slug]['name'];
											}
											
											$taxonomy_selected 	= isset( $data_get[$taxonomy_slug.'_name'] ) ? sanitize_text_field( $data_get[$taxonomy_slug.'_name'] ) : '';
											$html_taxonomy = ovabrw_search_taxonomy_dropdown( $taxonomy_slug, $taxonomy_name, '', $taxonomy_selected );
											if ( ! empty( $taxonomy_name ) && $html_taxonomy ):
												$args_taxonomy[$taxonomy_slug] = $taxonomy_name;
											?>
												<div class="label_search wrap_search_taxonomies <?php echo esc_attr( $taxonomy_slug ); ?>">
													<?php ovabrw_search_taxonomy_dropdown( $taxonomy_slug, $taxonomy_name, '', $taxonomy_selected ); ?>
												</div>
											<?php
											endif;
										}
									}
									?>
									<div class="show_taxonomy" data-show_taxonomy="<?php echo esc_attr( $show_taxonomy ); ?>"></div>
									<?php
								}
								// End Taxonomies
							?>
							<input 	type="hidden" id="data_taxonomy_custom" name="data_taxonomy_custom" 
									value="<?php echo esc_attr( json_encode( $args_taxonomy ) ); ?>" />
						</div><!-- wrap_content -->

						<!-- Radius -->
						<div class="wrap_search_radius" 
							data-map_range_radius="<?php echo esc_attr( apply_filters( 'ovabrw_ft_map_range_radius', 50 ) ); ?>" 
							data-map_range_radius_min="<?php echo esc_attr( apply_filters( 'ovabrw_ft_map_range_radius_min', 0 ) ); ?>" 
							data-map_range_radius_max="<?php echo esc_attr( apply_filters( 'ovabrw_ft_map_range_radius_max', 100 ) ); ?>">
							<span><?php esc_html_e( 'Radius:', 'ova-brw' ); ?></span>
							<span class="result_radius"><?php echo esc_html( '50'.$radius, 'ova-brw' ); ?></span>
							<div id="wrap_pointer"></div>
							<input 	type="hidden" value="<?php echo esc_attr( apply_filters( 'ovabrw_ft_map_range_radius', 50 ) ); ?>" name="radius">
						</div>
						<!-- End Radius -->

						<!-- Filter title -->
						<div class="wrap_search_filter_title">
							<div class="results_found"></div>
							<div id="search_sort">
								<?php $search_sort_default = apply_filters( 'search_sort_default', '' ); ?>
								<select name="sort">
									<option value=""><?php esc_html_e( 'Sort By', 'ova-brw' ); ?></option>
									<option value="date-desc" <?php if( $search_sort_default == 'date-desc' ) echo 'selected'; ?>>
										<?php esc_html_e( 'Newest First', 'ova-brw' ); ?>
									</option>
									<option value="date-asc" <?php if( $search_sort_default == 'date-asc' ) echo 'selected'; ?>>
										<?php esc_html_e( 'Oldest First', 'ova-brw' ); ?>
									</option>
									<option value="a-z" <?php if( $search_sort_default == 'a-z' ) echo 'selected'; ?>>
										<?php esc_html_e( 'A-Z', 'ova-brw' ); ?>
									</option>
									<option value="z-a" <?php if( $search_sort_default == 'z-a' ) echo 'selected'; ?> >
										<?php esc_html_e( 'Z-A', 'ova-brw' ); ?>
									</option>
								</select>
							</div>
						</div><!-- End filter title -->
					</form>
				</div><!-- fields_search -->
			<?php endif; ?>

			<!-- Load more -->
			<div class="wrap_load_more" style="display: none;">
				<svg class="loader" width="50" height="50">
					<circle cx="25" cy="25" r="10" stroke="#e86c60"/>
					<circle cx="25" cy="25" r="20" stroke="#e86c60"/>
				</svg>
			</div>
			<!-- End load more -->

			<!-- Search result -->
			<div id="search_result" class="search_result" 
				 data-type="<?php echo esc_attr( $type ); ?>" 
				 data-column="<?php echo esc_attr( $column ); ?>" 
				 data-zoom="<?php echo esc_attr( $zoom ); ?>" 
				 data-order="<?php echo esc_attr( $order ); ?>" 
				 data-orderby="<?php echo esc_attr( $orderby ); ?>" 
				 data-per_page="<?php echo esc_attr( $posts_per_page ); ?>" 
				 data-lat="<?php echo esc_attr( $lat_default ); ?>" 
				 data-lng="<?php echo esc_attr( $lng_default ); ?>" 
				 data-marker_option="<?php echo esc_attr( $marker_option ); ?>" 
				 data-marker_icon="<?php echo esc_attr( $marker_icon['url'] ); ?>" 
				 data-show_featured="<?php echo esc_attr( $show_featured ); ?>" 
				 data-radius="<?php echo esc_attr( $radius ); ?>">


				<div class="ovabrw_product_archive <?php echo esc_attr( $type . ' '. $column ); ?>"></div><!-- ovabrw_product_archive -->
			</div><!-- search_result -->
			<!-- Search result -->
		</div><!-- wrap_search -->

		<?php if ( $show_map == 'yes' ): ?>
			<div class="wrap_map">
				<div id="show_map"></div>
			</div>
		<?php endif; ?>
	</div>
</div>
<!-- wc_set_loop_prop( 'columns', 1 ); -->