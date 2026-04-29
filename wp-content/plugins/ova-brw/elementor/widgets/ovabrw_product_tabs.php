<?php
namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class ovabrw_product_tabs extends Widget_Base {


	public function get_name() {		
		return 'ovabrw_product_tabs';
	}

	public function get_title() {
		return __( 'Product Tabs', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-product-tabs';
	}

	public function get_categories() {
		return [ 'ovatheme' ];
	}

	protected function register_controls() {
		
		$this->start_controls_section(
			'section_product_tabs_style',
			[
				'label' => __( 'Tabs', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings();

		// Get Product
		$product  = wc_get_product();
		if ( empty( $product ) ) {
			?>
			<div class="ovabrw_elementor_no_product">
				<span><?php echo esc_html( $this->get_title() ); ?></span>
			</div>
			<?php
			return;
		}

		setup_postdata( $product->get_id() );

		?>

		<div class="elementor-tabs">
			<?php wc_get_template( 'single-product/tabs/tabs.php' ); ?>
		</div>
		
		<?php

		// On render widget from Editor - trigger the init manually.
		if ( wp_doing_ajax() ) {
			?>
			<script>
				jQuery( '.wc-tabs-wrapper, .woocommerce-tabs, #rating' ).trigger( 'init' );
			</script>
			<?php
		}

	}
}