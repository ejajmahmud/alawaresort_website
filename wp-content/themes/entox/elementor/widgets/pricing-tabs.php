<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Entox_Elementor_Pricing_Tabs extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_pricing_tabs';
	}

	public function get_title() {
		return esc_html__( 'Pricing Tabs', 'entox' );
	}

	public function get_icon() {
		return 'eicon-tabs';
	}

	public function get_categories() {
		return [ 'entox' ];
	}

	public function get_script_depends() {
		return [ 'entox-elementor-pricing-tabs' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_pricing_tabs_content',
			[
				'label' => esc_html__( 'Content', 'entox' ),
			]
		);

			$repeater_tabs = new \Elementor\Repeater();

			$repeater_tabs->add_control(
				'text',
				[
					'label'   	=> esc_html__( 'Text', 'entox' ),
					'type'    	=> Controls_Manager::TEXT,
					'default' 	=> esc_html__( 'Trial Package', 'entox' ),
				]
			);

			$repeater_tabs->add_control(
				'tabs_id',
				[
					'label'   		=> esc_html__( 'Package ID', 'entox' ),
					'type'    		=> Controls_Manager::TEXT,
					'default' 		=> esc_html__( 'package_id', 'entox' ),
					'description' 	=> esc_html__( 'Package ID is unique', 'entox' ),
				]
			);

			$this->add_control(
				'tabs',
				[
					'label' 	=> esc_html__( 'Tabs', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::REPEATER,
					'fields' 	=> $repeater_tabs->get_controls(),
					'default' 	=> [
						[
							'text' 		=> esc_html__( 'Trial Package', 'entox' ),
							'tabs_id' 	=> esc_html__( 'trial-package', 'entox' ),
						],
						[
							'text' 		=> esc_html__( 'Basic Package', 'entox' ),
							'tabs_id' 	=> esc_html__( 'basic-package', 'entox' ),
						],
					],
				]
			);

			$this->add_responsive_control(
				'align',
				[
					'label' => esc_html__( 'Alignment', 'entox' ),
					'type' => \Elementor\Controls_Manager::CHOOSE,
					'options' => [
						'flex-start' => [
							'title' => esc_html__( 'Left', 'entox' ),
							'icon' => 'eicon-h-align-left',
						],
						'center' => [
							'title' => esc_html__( 'Center', 'entox' ),
							'icon' => 'eicon-h-align-center',
						],
						'flex-end' => [
							'title' => esc_html__( 'Right', 'entox' ),
							'icon' => 'eicon-h-align-right',
						],
					],
					'default' => 'flex-start',
					'selectors' => [
						'{{WRAPPER}} .ova-pricing-tabs' => 'justify-content: {{VALUE}}',
					]
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pricing_tabs_style',
			[
				'label' => esc_html__( 'Content', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'item_typography',
					'selector' 	=> '{{WRAPPER}} .ova-pricing-tabs .item',
				]
			);

			$this->start_controls_tabs('item_tabs');
				$this->start_controls_tab(
					'item_normal_tab',
					[
						'label' => esc_html__( 'Normal', 'entox' ),
					]
				);

					$this->add_control(
						'item_normal_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tabs .item' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'item_normal_background',
						[
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tabs .item' => 'background-color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'item_hover_tab',
					[
						'label' => esc_html__( 'Hover', 'entox' ),
					]
				);

					$this->add_control(
						'item_hover_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tabs .item:hover' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'item_hover_background',
						[
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tabs .item:hover' => 'background-color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'item_active_tab',
					[
						'label' => esc_html__( 'Active', 'entox' ),
					]
				);

					$this->add_control(
						'item_active_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tabs .item.active-tabs' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'item_active_background',
						[
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tabs .item.active-tabs' => 'background-color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();
			$this->end_controls_tabs();

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'item_border',
					'label' 	=> esc_html__( 'Border', 'entox' ),
					'selector' 	=> '{{WRAPPER}} .ova-pricing-tabs .item',
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'item_border_radius_first',
				[
					'label' 		=> esc_html__( 'Border Radius (First)', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-pricing-tabs .item:first-child' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'item_border_radius_last',
				[
					'label' 		=> esc_html__( 'Border Radius (Last)', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-pricing-tabs .item:last-child' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'item_padding',
				[
					'label' 		=> esc_html__( 'Padding', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-pricing-tabs .item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();
	}

	protected function render() {
		$settings 	= $this->get_settings_for_display();
		$tabs 		= $settings['tabs'];
		?>
		
		<?php if ( $tabs && is_array( $tabs ) ): ?>
			<ul class="ova-pricing-tabs">
				<?php foreach( $tabs as $k => $item ): 
					$active = ( $k == 0 ) ? ' active-tabs' : '';
				?>
					<li class="item<?php echo esc_attr( $active ); ?>" data-id="<?php echo esc_attr( $item['tabs_id'] ); ?>">
						<?php echo esc_html( $item['text'] ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php
	}
}

$widgets_manager->register( new Entox_Elementor_Pricing_Tabs() );
