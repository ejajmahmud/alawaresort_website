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


class ovabrw_product_table_price extends Widget_Base {


	public function get_name() {		
		return 'ovabrw_product_table_price';
	}

	public function get_title() {
		return __( 'Product Price Table', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	public function get_categories() {
		return [ 'ovatheme' ];
	}

	protected function register_controls() {
		
		$this->start_controls_section(
			'section_price_table_style',
			[
				'label' => __( 'Title', 'ova-brw' ),
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
				'title_color',
				[
					'label'  => __( 'Color', 'ova-brw' ),
					'type' 	 => Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_PRIMARY,
					],
					'selectors' => [
						'.woocommerce {{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw-according' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'title_typography',
					'global' 	=> [
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
					],
					'selector' 	=> '.woocommerce {{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw-according',
				]
			);

			$this->add_responsive_control(
				'title_padding',
				[
					'label' 	 => __( 'Padding', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw-according' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'title_margin',
				[
					'label' 	 => __( 'Margin', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw-according' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Content', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'wc_style_warning_content',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => __( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

			$this->add_control(
				'content_title_color',
				[
					'label'  => __( 'Color Title', 'ova-brw' ),
					'type' 	 => Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_PRIMARY,
					],
					'selectors' => [
						'.woocommerce {{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table label' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'content_title_typography',
					'global' 	=> [
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
					],
					'selector' 	=> '.woocommerce {{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table label',
				]
			);

			$this->add_responsive_control(
				'content_title_padding',
				[
					'label' 	 => __( 'Padding', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'content_title_margin',
				[
					'label' 	 => __( 'Margin', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_table_value_style',
			[
				'label' => __( 'Table', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'wc_style_warning_table',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => __( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

			$this->add_control(
				'content_title_table_bg',
				[
					'label'  => __( 'Background', 'ova-brw' ),
					'type' 	 => Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_PRIMARY,
					],
					'selectors' => [
						'.woocommerce {{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table thead' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'content_title_table_color',
				[
					'label'  => __( 'Color Title', 'ova-brw' ),
					'type' 	 => Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_PRIMARY,
					],
					'selectors' => [
						'.woocommerce {{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table thead tr th' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'content_title_table_typography',
					'global' 	=> [
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
					],
					'selector' 	=> '.woocommerce {{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table thead tr th',
				]
			);

			$this->add_responsive_control(
				'content_title_table_padding',
				[
					'label' 	 => __( 'Padding', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table thead tr th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'content_title_table_margin',
				[
					'label' 	 => __( 'Margin', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table thead tr th' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_table_style',
			[
				'label' => __( 'Table Value', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'wc_style_warning_table_value',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => __( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

			$this->add_control(
				'table_value_color',
				[
					'label'  => __( 'Color', 'ova-brw' ),
					'type' 	 => Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_PRIMARY,
					],
					'selectors' => [
						'.woocommerce {{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table tbody tr td' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'table_value_typography',
					'global' 	=> [
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
					],
					'selector' 	=> '.woocommerce {{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table tbody tr td',
				]
			);

			$this->add_responsive_control(
				'table_value_padding',
				[
					'label' 	 => __( 'Padding', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table tbody tr td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'table_value_margin',
				[
					'label' 	 => __( 'Margin', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table tbody tr td' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
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

		<div class="elementor-table-price">
			<?php ovabrw_get_template( 'single/table_price.php' ); ?>
		</div>

		<?php
	}
}