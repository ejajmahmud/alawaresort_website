<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Entox_Elementor_Menu_Nav extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_menu_nav';
	}

	public function get_title() {
		return esc_html__( 'Menu', 'entox' );
	}

	public function get_icon() {
		return 'eicon-menu-bar';
	}

	public function get_categories() {
		return [ 'hf' ];
	}
	

	protected function register_controls() {


		/* Global Section *******************************/
		/***********************************************/
		$this->start_controls_section(
			'section_menu_type',
			[
				'label' => esc_html__( 'Global', 'entox' ),
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
					'prefix_class' => 'elementor-view-',
				]
			);
			
			
		$this->end_controls_section();	


		/* Parent Menu Section *******************************/
		/***********************************************/
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Parent Menu', 'entox' ),
			]
		);
		

			$this->add_control(
				'link_color',
				[
					'label' => esc_html__( 'Link', 'entox' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} ul.menu > li > a' => 'color: {{VALUE}};',
					],
					'global' => [
						'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_TEXT,
					],
					'separator' => 'before'
				]
			);

			$this->add_control(
				'link_color_hover',
				[
					'label' => esc_html__( 'Link Hover', 'entox' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} ul.menu > li > a:hover' => 'color: {{VALUE}};',
					]
					
				]
			);

			$this->add_control(
				'link_color_active',
				[
					'label' => esc_html__( 'Link Active', 'entox' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} ul.menu > li.current-menu-item > a' => 'color: {{VALUE}};',
					]
					
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'menu_typography',
					'global' => [
						'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector'	=> '{{WRAPPER}} ul li a'
				]
			);

			

		$this->end_controls_section();


		/* Sub Menu Section *******************************/
		/***********************************************/
		$this->start_controls_section(
			'section_submenu_content',
			[
				'label' => esc_html__( 'Sub Menu', 'entox' ),
			]
		);	


			$this->add_control(
				'submenu_bg_color',
				[
					'label' => esc_html__( 'Background', 'entox' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} ul.sub-menu' => 'background-color: {{VALUE}};',
					]
					
				]
			);

			$this->add_control(
				'submenu_bg_item_hover_color',
				[
					'label' => esc_html__( 'Background Item Hover', 'entox' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} ul.sub-menu li a:hover' => 'background-color: {{VALUE}};',
					],
					'separator' => 'after'
					
				]
			);

			$this->add_control(
				'submenu_link_color',
				[
					'label' => esc_html__( 'Link', 'entox' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} ul.sub-menu li a' => 'color: {{VALUE}};',
					]
					
				]
			);

			$this->add_control(
				'submenu_link_color_hover',
				[
					'label' => esc_html__( 'Link Hover', 'entox' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} ul.sub-menu li a:hover' => 'color: {{VALUE}};',
					]
					
				]
			);

			$this->add_control(
				'submenu_link_color_active',
				[
					'label' => esc_html__( 'Link Active', 'entox' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} ul.sub-menu li.current-menu-item > a' => 'color: {{VALUE}};',
					]
					
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'submenu_typography',
					'global' => [
						'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector'	=> '{{WRAPPER}} ul.sub-menu li a'
				]
			);


		$this->end_controls_section();


		
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		
		$settings = $this->get_settings();
		
		?>

		<nav class="main-navigation" role="navigation">
            <button class="menu-toggle">
            	<span>
            		<?php echo esc_html__( 'Menu', 'entox' ); ?>
            	</span>
            </button>
			<?php
				wp_nav_menu( [
					'theme_location'  => $settings['menu_slug'],
					'container_class' => 'primary-navigation',
					'menu'              => $settings['menu_slug'],
				] );
			?>
        </nav>
		

	<?php }



	
}


$widgets_manager->register( new Entox_Elementor_Menu_Nav() );


