<?php

if ( ! defined( 'ABSPATH' ) ) exit();

global $product;

$id = $product->get_id();

if ( $product->get_type() !== 'ovabrw_car_rental' ) return;

$deposit_force 	= get_post_meta ( $id, 'ovabrw_force_deposit', true );
$deposit_enable = get_post_meta ( $id, 'ovabrw_enable_deposit', true );

$deposit_type = get_post_meta ( $id, 'ovabrw_type_deposit', true );
$deposit_value = get_post_meta ( $id, 'ovabrw_amount_deposit', true );

?>

<?php if ( 'yes' === $deposit_enable ): ?>
	<div class="ovabrw-deposit">
		<div class="title-deposite">
			<span class=""><?php esc_html_e('Deposit Option', 'entox') ?></span>
				<?php if ( 'percent' === $deposit_type ): ?>
					<span><?php echo esc_html($deposit_value).'%'; ?></span>
				<?php else: ?>
					<span><?php printf(wc_price($deposit_value)).'%'; ?></span>
				<?php endif; ?>
			<span><?php esc_html_e('Per item', 'entox') ?></span>
		</div>
		<div class="ovabrw-type-deposit">
			<input type="radio" id="ovabrw-pay-deposit" class="ovabrw-pay-deposit" name="ova_type_deposit" value="deposit" checked />
			<label class="ovabrw-pay-deposit" for="ovabrw-pay-deposit">
				<?php esc_html_e('Pay Deposit', 'entox') ?>
			</label>
			<?php if ( $deposit_force == 'yes' ): ?>
				<input type="radio" id="ovabrw-pay-full" class="ovabrw-pay-full" name="ova_type_deposit" value="full" />
				<label class="ovabrw-pay-full" for="ovabrw-pay-full">
					<?php esc_html_e('Full Payment', 'entox') ?>
				</label>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>