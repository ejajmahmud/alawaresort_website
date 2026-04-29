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


class ovabrw_product_features extends Widget_Base {


	public function get_name() {		
		return 'ovabrw_product_features';
	}

	public function get_title() {
		return __( 'Product Features', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-product-meta';
	}

	public function get_categories() {
		return [ 'ovatheme' ];
	}

	protected function register_controls() {
		
		$this->start_controls_section(
			'section_price_style',
			[
				'label' => __( 'Features', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'wc_style_warning',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => __( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

		$this->add_control(
			'feature_color',
			[
				'label'  => __( 'Color Title', 'ova-brw' ),
				'type' 	 => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'.woocommerce {{WRAPPER}} .ovabrw_woo_features li label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 		=> 'features_typography',
				'global' 	=> [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' 	=> '.woocommerce {{WRAPPER}} .ovabrw_woo_features li label',
			]
		);

		$this->add_responsive_control(
			'features_padding',
			[
				'label' 	 => __( 'Padding', 'ova-brw' ),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'.woocommerce {{WRAPPER}} .ovabrw_woo_features' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'features_margin',
			[
				'label' 	 => __( 'Margin', 'ova-brw' ),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'.woocommerce {{WRAPPER}} .ovabrw_woo_features' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'feature_value_color',
			[
				'label'  => __( 'Color Value', 'ova-brw' ),
				'type' 	 => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'.woocommerce {{WRAPPER}} .ovabrw_woo_features li span' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 	 => 'feature_value_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '.woocommerce {{WRAPPER}} .ovabrw_woo_features li span',
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

		?>

		<div class="elementor-features">
			<?php ovabrw_get_template( 'single/features.php' ); ?>
		</div>

		<?php
	}
}