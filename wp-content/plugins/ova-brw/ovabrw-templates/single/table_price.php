<?php 
/**
 * The template for displaying table price content within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/table_price.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

// Get id from do_action - use when insert shortcode
if( isset( $args['id'] ) && $args['id'] ){
	$pid = $args['id'];
}else{
	$pid = get_the_id();
}

// Check product type: rental
$product = wc_get_product( $pid );
if ( $product->get_type() !== 'ovabrw_car_rental' ) return;


$price_type = get_post_meta( $pid, 'ovabrw_price_type', true );

if( ovabrw_get_setting( get_option( 'ova_brw_template_show_open_table_price', 'yes' ) ) === 'yes' ){
	$tab_show = '';
	$content_show = 'show';
}else{
	$tab_show = 'ovabrw-collapsed';
	$content_show = '';
}

?>
<div class="product_table_price">

	<!-- Period time -->
	<?php if( $price_type == 'period_time' ){ ?>

		<div class="ovacrs_price_rent ovacrs_hourly_rent">

			<h3 class="ovabrw-according <?php echo esc_attr( $tab_show ); ?>"  href="#" role="button" >

				<?php esc_html_e( 'Table Price & Discount', 'ova-brw' ); ?>

				</h3>

				<div class="ovabrw_collapse_content <?php echo esc_attr($content_show); ?>" >
					<?php do_action( 'ovabrw_table_price_period_time', $pid ); ?>
				</div>

		</div>

	<?php } ?>

	<!-- Hourly Rent -->
	<?php if( $price_type == 'hour' || $price_type == 'mixed' ){ ?>

		<div class="ovacrs_price_rent ovacrs_hourly_rent">

			<h3 class="ovabrw-according <?php echo esc_attr($tab_show); ?>" >
				<?php esc_html_e( 'Table Price - Hour', 'ova-brw' ); ?>
			</h3>

			<!-- Regular Price Hour -->
			<div class="ovabrw_collapse_content <?php echo esc_attr($content_show); ?>" >
				
				<!-- Global Discount -->
				<?php do_action( 'ovabrw_table_price_global_discount_hour', $pid ); ?>

				<?php do_action( 'ovabrw_table_price_seasons_hour', $pid ); ?>

			</div>

		</div>

	<?php } ?>

	<!-- Daily Rent -->
	<?php if( $price_type == 'day' || $price_type == 'mixed' ){ ?>

		<div class="ovacrs_price_rent ovacrs_daily_rent">

			
			<h3 class="ovabrw-according <?php echo esc_attr($tab_show); ?>" >
				<?php esc_html_e( 'Table Price - Day', 'ova-brw' ); ?>
			</h3>

			<!-- Regular Price Hour -->
			<div class="ovabrw_collapse_content <?php echo esc_attr( $content_show ); ?>">
				

				<?php do_action( 'ovabrw_table_price_weekdays', $pid ); ?>
				
				<?php do_action( 'ovabrw_table_price_global_discount_day', $pid ); ?>

				<?php do_action( 'ovabrw_table_price_seasons_day', $pid ); ?>

				
			</div>
			
			
		</div>

	<?php } ?>
	
	
</div>
