<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Entox_Elementor_Menu_Page extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_menu_page';
	}

	public function get_title() {
		return esc_html__( 'Menu Page', 'entox' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_categories() {
		return [ 'hf' ];
	}

	public function get_script_depends() {
		return [ 'entox-elementor-menu-page' ];
	}

	public function get_keywords() {
		return [ 'menu', 'foter' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_menu',
			[
				'label' => esc_html__( 'Menu', 'entox' ),
			]
		);


		$menus = \wp_get_nav_menus();
		$list_menu = array();
		foreach ($menus as $menu) {
			$list_menu[$menu->slug] = $menu->name;
		}

		$this->add_control(
			'menu_slug',
			[
				'label' => esc_html__( 'Select Menu', 'entox' ),
				'type' => Controls_Manager::SELECT,
				'options' => $list_menu,
				'default' => '',
			]
		);

		$this->add_control(
			'menu_type',
			[
				'label' => esc_html__( 'Menu Type', 'entox' ),
				'type' => Controls_Manager::SELECT,
				'options' => array( 
					'type1' => esc_html__( 'Type 1', 'entox' ),
					'type2' => esc_html__( 'Type 2', 'entox' ),
				),
				'default' => 'type1',
			]
		);


		$this->add_control(
			'heading_style',
			[
				'label' => esc_html__( 'Style', 'entox' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typo_menu',
				'selector' => '{{WRAPPER}} .ova_menu_page .menu li,{{WRAPPER}} .ova_menu_page .menu li a',
			]
		);

		$this->add_responsive_control(
			'padding_menu',
			[
				'label' => esc_html__( 'Padding', 'entox' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ova_menu_page .menu li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'margin_menu',
			[
				'label' => esc_html__( 'Margin', 'entox' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ova_menu_page .menu li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'entox' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ova_menu_page .menu li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'color_menu',
			[
				'label' => esc_html__( 'Text Color', 'entox' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova_menu_page .menu li a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'color_menu_hover',
			[
				'label' => esc_html__( 'Text Hover', 'entox' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova_menu_page .menu li:hover a' => 'color: {{VALUE}}',
				],
			]
		);


		$this->add_control(
			'color_icon',
			[
				'label' => esc_html__( 'Color icon', 'entox' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova_menu_page.type1 a:before' => 'background-color: {{VALUE}} ',
				],
			]
		);

		


		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();
		?>
		<div class="ova_menu_page <?php echo esc_attr( $settings['menu_type'] ); ?>">
			<?php
			wp_nav_menu( array(
				'menu'              => $settings['menu_slug'],
				'depth'             => 3,
				'container'         => '',
				'container_class'   => '',
				'container_id'      => '',
			));
			?>
		</div>
		<?php 
	}
}

$widgets_manager->register( new Entox_Elementor_Menu_Page() );