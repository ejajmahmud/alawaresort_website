<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Entox_Elementor_Breadcrumbs extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_breadcrumbs';
	}

	public function get_title() {
		return esc_html__( 'Breadcrumbs', 'entox' );
	}

	public function get_icon() {
		return 'eicon-product-breadcrumbs';
	}

	public function get_categories() {
		return [ 'entox' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_breadcrumbs',
			[
				'label' => esc_html__( 'Breadcrumbs', 'entox' ),
			]
		);

			$this->add_control(
				'search_map_url',
				[
					'label' 		=> esc_html__( 'Search Map URL', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::URL,
					'placeholder' 	=> esc_html__( 'https://your-link.com', 'entox' ),
					'dynamic' 		=> [
						'active' => true,
					],
					'default' => [
						'url' => '#',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_breadcrumbs_style',
			[
				'label' => esc_html__( 'Breadcrumbs', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'breadcrumb_padding',
				[
					'label' 		=> esc_html__( 'Padding', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-breadcrumbs #breadcrumbs .breadcrumb' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'breadcrumb_margin',
				[
					'label' 		=> esc_html__( 'Margin', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-breadcrumbs #breadcrumbs .breadcrumb' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_item_style',
			[
				'label' => esc_html__( 'Items', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'breadcrumbs_typography',
					'selector' 	=> '{{WRAPPER}} .ova-breadcrumbs #breadcrumbs .breadcrumb li',
				]
			);

			$this->start_controls_tabs( 'items_tabs' );
				$this->start_controls_tab(
					'items_normal_tab',
					[
						'label' => esc_html__( 'Normal', 'entox' ),
					]
				);

					$this->add_control(
						'items_normal_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-breadcrumbs #breadcrumbs .breadcrumb li' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'items_link_tab',
					[
						'label' => esc_html__( 'Link', 'entox' ),
					]
				);

					$this->add_control(
						'items_link_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-breadcrumbs #breadcrumbs .breadcrumb li a' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'items_hover_tab',
					[
						'label' => esc_html__( 'Hover', 'entox' ),
					]
				);

					$this->add_control(
						'items_hover_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-breadcrumbs #breadcrumbs .breadcrumb li a:hover' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();
			$this->end_controls_tabs();

			$this->add_responsive_control(
				'items_padding',
				[
					'label' 		=> esc_html__( 'Padding', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-breadcrumbs #breadcrumbs .breadcrumb li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_separator_style',
			[
				'label' => esc_html__( 'Separator', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'separator_typography',
					'selector' 	=> '{{WRAPPER}} .ova-breadcrumbs #breadcrumbs .breadcrumb li .separator i',
				]
			);

			$this->add_control(
				'separator_color',
				[
					'label' 	=> esc_html__( 'Color', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-breadcrumbs #breadcrumbs .breadcrumb li .separator i' => 'color: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$search_map_url = $settings['search_map_url']['url'];
		
		?>
		<div class="ova-breadcrumbs">
			<?php echo get_template_part( 'template-parts/parts/breadcrumbs', '', [ 'search_map_url' => $search_map_url ] ); ?>
		</div>
		
		<?php
	}
}

$widgets_manager->register( new Entox_Elementor_Breadcrumbs() );
