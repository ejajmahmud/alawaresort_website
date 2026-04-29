<?php if( ! defined( 'ABSPATH' ) ) exit();

$id = get_the_id();

$date_format = ovabrw_get_date_format();
$time_format = ovabrw_get_time_format_php();

$date_time_format = $date_format . ' ' . $time_format;

$petime_label = get_post_meta( $id, 'ovabrw_petime_label', true ); 
$petime_price = get_post_meta( $id, 'ovabrw_petime_price', true );

$price_text 		= esc_html__( 'Price', 'entox' );
$start_time_text 	= esc_html__( 'Start Time', 'entox' );
$end_time_text 		= esc_html__( 'End Time', 'entox' );

?>

<?php if ( ! empty( $petime_label ) ): ?>
	<?php foreach( $petime_label as $k => $value ): 
		$petime_discount 		= get_post_meta( $id, 'ovabrw_petime_discount', true );
		$discount_price			= isset( $petime_discount[$k]['price'] ) ? $petime_discount[$k]['price'] : '';
		$discount_start_time 	= isset( $petime_discount[$k]['start_time'] ) ? $petime_discount[$k]['start_time'] : '';
		$discount_end_time 		= isset( $petime_discount[$k]['end_time'] ) ? $petime_discount[$k]['end_time'] : '';
	?>
	<h2 class="package-name"><?php echo esc_html( $petime_label[$k] ).' : '.wc_price($petime_price[$k]); ?></h2>
		<?php if ( $discount_price && is_array( $discount_price ) ): ?>
			<div class="table-price">
				<div class="head-label">
					<h3 class="label"><?php esc_html_e( 'Start Time', 'entox' ); ?></h3>
					<h3 class="label"><?php esc_html_e( 'End Time', 'entox' ); ?></h3>
					<h3 class="label"><?php esc_html_e( 'Price', 'entox' ); ?></h3>
				</div>
				<div class="boby-price">
					<?php foreach( $discount_price as $i => $value_price ): ?>
						<div class="content-price <?php echo intval( $i%2 ) ? 'even' : 'odd'; ?>">
							<p class="value-price">
								<?php echo esc_html( $discount_start_time[$i] != '' ? date($date_time_format, strtotime( $discount_start_time[$i] )) : '' ); ?>
							</p>
							<p class="value-price">
								<?php echo esc_html( $discount_end_time[$i] != '' ? date($date_time_format, strtotime( $discount_end_time[$i] )) : '' ); ?>
							</p>
							<p class="value-price"><?php echo wc_price( $value_price ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php else: ?>
			<div class="no-discount"><?php esc_html_e( 'No Discount in this time', 'entox' ); ?></div>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>