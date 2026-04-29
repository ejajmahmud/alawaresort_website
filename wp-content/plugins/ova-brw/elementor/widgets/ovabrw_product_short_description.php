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


class ovabrw_product_short_description extends Widget_Base {


	public function get_name() {		
		return 'ovabrw_product_short_description';
	}

	public function get_title() {
		return __( 'Short Description', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-product-description';
	}

	public function get_categories() {
		return [ 'ovatheme' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_product_short_description_style',
			[
				'label' => __( 'Style', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'wc_style_warning',
				[
					'type' 	=> Controls_Manager::RAW_HTML,
					'raw' 	=> __( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
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
						'{{WRAPPER}}' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'text_color',
				[
					'label'  => __( 'Color', 'ova-brw' ),
					'type' 	 => Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_PRIMARY,
					],
					'selectors' => [
						'.woocommerce {{WRAPPER}} .woocommerce-product-details__short-description' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 	 => 'text_typography',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
					],
					'selector' => '.woocommerce {{WRAPPER}} .woocommerce-product-details__short-description',
				]
			);

		$this->end_controls_section();
	}

	protected function render() {

		$settings 	= $this->get_settings();

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

		<div class="elementor-short-description">
			<?php wc_get_template( 'single-product/short-description.php' ); ?>
		</div>
		
		<?php
	}
}