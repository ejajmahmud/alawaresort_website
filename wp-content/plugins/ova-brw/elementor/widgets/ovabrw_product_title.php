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


class ovabrw_product_title extends Widget_Base {


	public function get_name() {		
		return 'ovabrw_product_title';
	}

	public function get_title() {
		return __( 'Product Title', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-t-letter';
	}

	public function get_categories() {
		return [ 'ovatheme' ];
	}

	protected function register_controls() {
		
		$this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Title', 'ova-brw' ),
			]
		);
		
			$this->add_control(
				'link',
				[
					'label' => __( 'Link', 'ova-brw' ),
					'type' 	=> Controls_Manager::URL,
					'dynamic' => [
						'active' => true,
					],
					'default' => [
						'url' => '',
					],
					'separator' => 'before',
				]
			);

			$this->add_control(
				'header_size',
				[
					'label' => __( 'HTML Tag', 'ova-brw' ),
					'type' 	=> Controls_Manager::SELECT,
					'options' => [
						'h1' 	=> 'H1',
						'h2' 	=> 'H2',
						'h3' 	=> 'H3',
						'h4' 	=> 'H4',
						'h5' 	=> 'H5',
						'h6' 	=> 'H6',
						'div' 	=> 'div',
						'span' 	=> 'span',
						'p' 	=> 'p',
					],
					'default' => 'h2',
				]
			);

			$this->add_responsive_control(
				'align',
				[
					'label' => __( 'Alignment', 'ova-brw' ),
					'type' 	=> Controls_Manager::CHOOSE,
					'options' => [
						'left' => [
							'title' => __( 'Left', 'ova-brw' ),
							'icon' 	=> 'eicon-text-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'ova-brw' ),
							'icon' 	=> 'eicon-text-align-center',
						],
						'right' => [
							'title' => __( 'Right', 'ova-brw' ),
							'icon' 	=> 'eicon-text-align-right',
						],
						'justify' => [
							'title' => __( 'Justified', 'ova-brw' ),
							'icon' 	=> 'eicon-text-align-justify',
						],
					],
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .ovabrw_product_title' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_title_style',
				[
					'label' => __( 'Title', 'ova-brw' ),
					'tab' 	=> Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'title_color',
				[
					'label' => __( 'Color', 'ova-brw' ),
					'type' 	=> Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_PRIMARY,
					],
					'selectors' => [
						'{{WRAPPER}} .ovabrw_product_title .ovabrw_title' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 	 => 'typography',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
					],
					'selector' => '{{WRAPPER}} .ovabrw_product_title .ovabrw_title',
				]
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				[
					'name' 		=> 'text_shadow',
					'selector' 	=> '{{WRAPPER}} .ovabrw_product_title .ovabrw_title',
				]
			);

			$this->add_control(
				'blend_mode',
				[
					'label' => __( 'Blend Mode', 'ova-brw' ),
					'type' 	=> Controls_Manager::SELECT,
					'options' => [
						'' => __( 'Normal', 'ova-brw' ),
						'multiply' 	  => 'Multiply',
						'screen' 	  => 'Screen',
						'overlay' 	  => 'Overlay',
						'darken' 	  => 'Darken',
						'lighten' 	  => 'Lighten',
						'color-dodge' => 'Color Dodge',
						'saturation'  => 'Saturation',
						'color' 	  => 'Color',
						'difference'  => 'Difference',
						'exclusion'   => 'Exclusion',
						'hue' 		  => 'Hue',
						'luminosity'  => 'Luminosity',
					],
					'selectors' => [
						'{{WRAPPER}} .ovabrw_product_title .ovabrw_title' => 'mix-blend-mode: {{VALUE}}',
					],
					'separator' => 'none',
				]
			);

		$this->end_controls_section();
	}

	protected function render() {

		$settings 	= $this->get_settings();

		// Get link
		$link 	  	= $settings['link']['url'];
		$blank 		= '_blank';
		$target_url = $settings['link']['is_external'];
		if ( empty( $target_url ) ) {
			$blank = '';
		}

		// Get header_size
		$header_size = $settings['header_size'];

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

		$title = $product->get_title();
		if ( $title === '' ) {
			?>
			<div class="ovabrw_elementor_no_product">
				<span><?php echo esc_html( $this->get_title() ); ?></span>
			</div>
			<?php
			return;
		}

		?>

		<div class="ovabrw_product_title">

			<?php if ( !empty( $link ) ): ?>
				<a href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $blank ); ?>">
					<<?php echo esc_attr( $header_size ); ?> class="ovabrw_title"><?php echo esc_html( $title ); ?>
					</<?php echo esc_attr($header_size); ?>>
				</a>
			<?php else: ?>
				<<?php echo esc_attr($header_size); ?> class="ovabrw_title"><?php echo esc_html( $title ); ?></<?php echo esc_attr($header_size); ?>>
			<?php endif; ?>

		</div>

		<?php
	}

	
}