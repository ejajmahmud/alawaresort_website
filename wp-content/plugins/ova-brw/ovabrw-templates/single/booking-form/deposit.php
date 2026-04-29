<?php
/**
 * The template for displaying deposit in booking form within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/booking-form/deposit.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

// Get product_id from do_action - use when insert shortcode
if( isset( $args['product_id'] ) && $args['product_id'] ){
	$pid = $args['product_id'];
}else{
	$pid = get_the_id();
}


$deposit_value = ovabrw_p_deposit_type( $pid );
$deposit_force 	= get_post_meta ( $pid, 'ovabrw_force_deposit', true );
$rand 			= wp_rand();

?>
<div class="ovabrw-deposit">

	<div class="title-deposite">

		<span class=""><?php esc_html_e('Deposit Option', 'ova-brw') ?></span>
		
		<?php echo wp_kses_post( $deposit_value ); ?>

		<span><?php esc_html_e('Per item', 'ova-brw') ?></span>

	</div>

	<div class="ovabrw-type-deposit">
		<input type="radio" id="ovabrw-pay-deposit-<?php echo esc_attr( $rand ); ?>" class="ovabrw-pay-deposit" name="ova_type_deposit" value="deposit" checked />

		<label class="ovabrw-pay-deposit" for="ovabrw-pay-deposit-<?php echo esc_attr( $rand ); ?>">
			<?php esc_html_e('Pay Deposit', 'ova-brw') ?>
		</label>
		
		<?php if( $deposit_force == 'yes' ){ ?>
			<input type="radio" id="ovabrw-pay-full-<?php echo esc_attr( $rand ); ?>" class="ovabrw-pay-full" name="ova_type_deposit" value="full" />
			<label class="ovabrw-pay-full" for="ovabrw-pay-full-<?php echo esc_attr( $rand ); ?>">
				<?php esc_html_e('Full Payment', 'ova-brw') ?>
			</label>
		<?php } ?>
		
	</div>
	
</div>