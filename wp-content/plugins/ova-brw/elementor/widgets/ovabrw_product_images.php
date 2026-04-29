<?php
namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class ovabrw_product_images extends Widget_Base {


	public function get_name() {		
		return 'ovabrw_product_images';
	}

	public function get_title() {
		return __( 'Product Images', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-product-images';
	}

	public function get_categories() {
		return [ 'ovatheme' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_product_gallery_style',
			[
				'label' => __( 'Style', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'wc_style_warning',
				[
					'type' 	=> Controls_Manager::RAW_HTML,
					'raw' 	=> __( 'The style of this widget is often affected by your theme and <p></p>lugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'image_border',
					'selector' 	=> '.woocommerce {{WRAPPER}} .woocommerce-product-gallery .flex-viewport',
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'image_border_radius',
				[
					'label' 	 => __( 'Border Radius', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .woocommerce-product-gallery .flex-viewport' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					],
				]
			);

			$this->add_control(
				'spacing',
				[
					'label' 	 => __( 'Spacing Image', 'ova-brw' ),
					'type' 		 => Controls_Manager::SLIDER,
					'size_units' => [ 'px', 'em' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .woocommerce-product-gallery .flex-viewport' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->add_control(
				'heading_thumbs_style',
				[
					'label' 	=> __( 'Thumbnails', 'ova-brw' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'thumbs_border',
					'selector' 	=> '.woocommerce {{WRAPPER}} .flex-control-thumbs img',
				]
			);

			$this->add_responsive_control(
				'thumbs_border_radius',
				[
					'label' 	 => __( 'Border Radius', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .flex-control-thumbs img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					],
				]
			);

			$this->add_control(
				'spacing_thumbs',
				[
					'label' 	 => __( 'Spacing Thumbnails', 'ova-brw' ),
					'type' 		 => Controls_Manager::SLIDER,
					'size_units' => [ 'px', 'em' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .flex-control-thumbs li' => 'padding-right: calc({{SIZE}}{{UNIT}} / 2); padding-left: calc({{SIZE}}{{UNIT}} / 2); padding-bottom: {{SIZE}}{{UNIT}}',
						'.woocommerce {{WRAPPER}} .flex-control-thumbs' => 'margin-right: calc(-{{SIZE}}{{UNIT}} / 2); margin-left: calc(-{{SIZE}}{{UNIT}} / 2)',
					],
				]
			);

			$this->add_responsive_control(
				'thumbnails_align',
				[
					'label' => __( 'Alignment', 'ova-brw' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'flex-start' => [
							'title' => __( 'Left', 'ova-brw' ),
							'icon' => 'eicon-text-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'ova-brw' ),
							'icon' => 'eicon-text-align-center',
						],
						'flex-end' => [
							'title' => __( 'Right', 'ova-brw' ),
							'icon' => 'eicon-text-align-right',
						],
						'space-between' => [
							'title' => __( 'Justified', 'ova-brw' ),
							'icon' 	=> 'eicon-text-align-justify',
						],
					],
					'selectors' => [
						'.woocommerce {{WRAPPER}} .woocommerce-product-gallery .flex-control-nav.flex-control-thumbs' => 'justify-content: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();
	}

	protected function render() {

		$settings = $this->get_settings();

		global $product;

		$product = wc_get_product();

		if ( empty( $product ) ) {
			?>
			<div class="ovabrw_elementor_no_product">
				<span><?php echo esc_html( $this->get_title() ); ?></span>
			</div>
			<?php
			return;
		}

		?>

		<div class="elementor-product-image">
			<?php wc_get_template( 'single-product/product-image.php' ); ?>
		</div>

		<?php

		// On render widget from Editor - trigger the init manually.
		if ( wp_doing_ajax() ) {
			?>
			<script>
				jQuery( '.woocommerce-product-gallery' ).each( function() {
					jQuery( this ).wc_product_gallery();
				} );
			</script>
			<?php
		}
	}
}