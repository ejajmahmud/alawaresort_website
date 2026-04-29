<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Entox_Elementor_Pricing_Tables extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_pricing_tables';
	}

	public function get_title() {
		return esc_html__( 'Pricing Tables', 'entox' );
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	public function get_categories() {
		return [ 'entox' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_pricing_content',
			[
				'label' => esc_html__( 'Content', 'entox' ),
			]
		);

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'price',
				[
					'label'   	=> esc_html__( 'Price', 'entox' ),
					'type'    	=> Controls_Manager::NUMBER,
					'default' 	=> 59,
				]
			);

			$repeater->add_control(
				'currencies',
				[
					'label' 	=> esc_html__( 'Currency', 'entox' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'USD',
					'options' 	=> $this->ova_get_currencies(),
				]
			);

			$repeater->add_control(
				'per_time',
				[
					'label'   	=> esc_html__( 'Per Time', 'entox' ),
					'type'    	=> Controls_Manager::TEXT,
					'default' 	=> esc_html__( '/ Monthly', 'entox' ),
				]
			);

			$repeater->add_control(
				'title',
				[
					'label'   	=> esc_html__( 'Title', 'entox' ),
					'type'    	=> Controls_Manager::TEXT,
					'default' 	=> esc_html__( 'Trial Package', 'entox' ),
				]
			);

			$repeater->add_control(
				'package_id',
				[
					'label'   		=> esc_html__( 'Package ID', 'entox' ),
					'type'    		=> Controls_Manager::TEXT,
					'default' 		=> esc_html__( 'package_id', 'entox' ),
					'description' 	=> esc_html__( 'Package ID is unique', 'entox' ),
				]
			);

			$repeater->add_control(
				'package_item',
				[
					'label' 		=> esc_html__( 'Package Item', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::TEXTAREA,
					'rows' 			=> 5,
					'default' 		=> esc_html__( '1 Ad allowed | 7 Days per submission | 4 Free Images | No free videos | No free documents', 'entox' ),
					'description' 	=> esc_html__( 'Items separated by the character | (pipe)', 'entox' ),
				]
			);

			$repeater->add_control(
				'text_button',
				[
					'label'   	=> esc_html__( 'Text Button', 'entox' ),
					'type'    	=> Controls_Manager::TEXT,
					'default' 	=> esc_html__( 'Buy Package', 'entox' ),
				]
			);

			$repeater->add_control(
				'link',
				[
					'label' 		=> esc_html__( 'Link', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::URL,
					'placeholder' 	=> esc_html__( 'https://your-link.com', 'entox' ),
					'default' 		=> [
						'url' => '#',
					],
				]
			);

			$this->add_control(
				'package',
				[
					'label' 	=> esc_html__( 'Package', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::REPEATER,
					'fields' 	=> $repeater->get_controls(),
					'default' 	=> [
						[
							'price' 			=> esc_html__( '49', 'entox' ),
							'currencies' 		=> esc_html__( 'USD', 'entox' ),
							'per_time' 			=> esc_html__( '/ Monthly', 'entox' ),
							'title' 			=> esc_html__( 'Trial Package', 'entox' ),
							'package_id' 		=> esc_html__( 'trial-package', 'entox' ),
							'package_item' 		=> esc_html__( '1 Ad allowed | 7 Days per submission | 4 Free Images | No free videos | No free documents', 'entox' ),
							'text_button' 		=> esc_html__( 'Buy Package', 'entox' ),
							'link' 				=> [ 'url' => '#' ],
						],
						[
							'price' 			=> esc_html__( '89', 'entox' ),
							'currencies' 		=> esc_html__( 'USD', 'entox' ),
							'per_time' 			=> esc_html__( '/ Monthly', 'entox' ),
							'title' 			=> esc_html__( 'Basic Package', 'entox' ),
							'package_id' 		=> esc_html__( 'basic-package', 'entox' ),
							'package_item' 		=> esc_html__( '2 Ads allowed | 10 Days per submission | 3 Free Images | 1 Free Videos | 1 Free Documents', 'entox' ),
							'text_button' 		=> esc_html__( 'Buy Package', 'entox' ),
							'link' 				=> [ 'url' => '#' ],
						],
					],
				]
			);

		$this->end_controls_section();

		/* Section Content Style */
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => esc_html__( 'Content', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs( 'section_content_tabs' );
				$this->start_controls_tab(
					'section_content_normal',
					[
						'label' => esc_html__( 'Normal', 'entox' ),
					]
				);

					$this->add_control(
						'content_normal_background',
						[
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tables .pricing-item' => 'background-color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'section_content_active',
					[
						'label' => esc_html__( 'Active', 'entox' ),
					]
				);

					$this->add_control(
						'content_active_background',
						[
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tables .pricing-item.active_package' => 'background-color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();
			$this->end_controls_tabs();

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'content_border',
					'label' 	=> esc_html__( 'Border', 'entox' ),
					'selector' 	=> '{{WRAPPER}} .ova-pricing-tables .pricing-item',
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'content_border_radius',
				[
					'label' 		=> esc_html__( 'Border Radius', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-pricing-tables .pricing-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'content_padding',
				[
					'label' 		=> esc_html__( 'Padding', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-pricing-tables .pricing-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		/* Section Price Style */
		$this->start_controls_section(
			'section_price_style',
			[
				'label' => esc_html__( 'Price', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'price_typography',
					'selector' 	=> '{{WRAPPER}} .ova-pricing-tables .pricing-item .price-package .price',
				]
			);

			$this->start_controls_tabs( 'section_price_tabs' );
				$this->start_controls_tab(
					'section_price_normal',
					[
						'label' => esc_html__( 'Normal', 'entox' ),
					]
				);

					$this->add_control(
						'price_normal_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tables .pricing-item .price-package .price' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'section_price_active',
					[
						'label' => esc_html__( 'Active', 'entox' ),
					]
				);

					$this->add_control(
						'price_active_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tables .pricing-item.active_package .price-package .price' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();
			$this->end_controls_tabs();

			$this->add_responsive_control(
				'price_margin',
				[
					'label' 		=> esc_html__( 'Margin', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-pricing-tables .pricing-item .price-package' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		/* Section Currency Style */
		$this->start_controls_section(
			'section_currency_style',
			[
				'label' => esc_html__( 'Currency', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'currency_typography',
					'selector' 	=> '{{WRAPPER}} .ova-pricing-tables .pricing-item .price-package .symbols',
				]
			);

			$this->start_controls_tabs( 'section_currency_tabs' );
				$this->start_controls_tab(
					'section_currency_normal',
					[
						'label' => esc_html__( 'Normal', 'entox' ),
					]
				);

					$this->add_control(
						'currency_normal_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tables .pricing-item .price-package .symbols' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'section_currency_active',
					[
						'label' => esc_html__( 'Active', 'entox' ),
					]
				);

					$this->add_control(
						'currency_active_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tables .pricing-item.active_package .price-package .symbols' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();
			$this->end_controls_tabs();

			$this->add_control(
				'currency_position',
				[
					'label' 	=> esc_html__( 'Currency Position', 'entox' ),
					'type' 		=> Controls_Manager::CHOOSE,
					'default' 	=> 'before',
					'toggle' 	=> false,
					'options' 	=> [
						'before' => [
							'title' => esc_html__( 'Before', 'entox' ),
							'icon' => 'eicon-h-align-left',
						],
						'after' => [
							'title' => esc_html__( 'After', 'entox' ),
							'icon' => 'eicon-h-align-right',
						],
					],
				]
			);

			$this->add_control(
				'currency_vertical_align',
				[
					'label' 	=> esc_html__( 'Vertical Align', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'top',
					'options' 	=> [
						'top'  		=> esc_html__( 'Top', 'entox' ),
						'bottom'  	=> esc_html__( 'Bottom', 'entox' ),
						'inherit'  	=> esc_html__( 'Inherit', 'entox' ),
						'super'  	=> esc_html__( 'Super', 'entox' ),
					],
					'selectors' => [
						'{{WRAPPER}} .ova-pricing-tables .pricing-item.active_package .price-package .symbols' => 'vertical-align: {{VALUE}}',
					]
				]
			);

			$this->add_responsive_control(
				'currency_margin',
				[
					'label' 		=> esc_html__( 'Margin', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-pricing-tables .pricing-item.active_package .price-package .symbols' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		/* Section Per Time Style */
		$this->start_controls_section(
			'section_time_style',
			[
				'label' => esc_html__( 'Per Time', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'time_typography',
					'selector' 	=> '{{WRAPPER}} .ova-pricing-tables .pricing-item .price-package .per-time',
				]
			);

			$this->start_controls_tabs( 'section_time_tabs' );
				$this->start_controls_tab(
					'section_time_normal',
					[
						'label' => esc_html__( 'Normal', 'entox' ),
					]
				);

					$this->add_control(
						'time_normal_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tables .pricing-item .price-package .per-time' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'section_time_active',
					[
						'label' => esc_html__( 'Active', 'entox' ),
					]
				);

					$this->add_control(
						'time_active_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tables .pricing-item.active_package .price-package .per-time' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();
			$this->end_controls_tabs();

			$this->add_responsive_control(
				'time_margin',
				[
					'label' 		=> esc_html__( 'Margin', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-pricing-tables .pricing-item .price-package .per-time' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		/* Section Line Style */
		$this->start_controls_section(
			'section_line_style',
			[
				'label' => esc_html__( 'Line', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'weight',
				[
					'label' => __( 'Weight', 'entox' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 1,
					],
					'selectors' => [
						'{{WRAPPER}} .ova-pricing-tables .pricing-item .line' => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->start_controls_tabs( 'section_line_tabs' );
				$this->start_controls_tab(
					'section_line_normal',
					[
						'label' => esc_html__( 'Normal', 'entox' ),
					]
				);

					$this->add_control(
						'line_normal_background',
						[
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tables .pricing-item .line' => 'background-color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'section_line_active',
					[
						'label' => esc_html__( 'Active', 'entox' ),
					]
				);

					$this->add_control(
						'line_active_background',
						[
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tables .pricing-item.active_package .line' => 'background-color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();
			$this->end_controls_tabs();

			$this->add_responsive_control(
				'line_margin',
				[
					'label' 		=> esc_html__( 'Margin', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-pricing-tables .pricing-item .line' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		/* Section Title Style */
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__( 'Title', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'title_typography',
					'selector' 	=> '{{WRAPPER}} .ova-pricing-tables .pricing-item .title',
				]
			);

			$this->start_controls_tabs( 'section_title_tabs' );
				$this->start_controls_tab(
					'section_title_normal',
					[
						'label' => esc_html__( 'Normal', 'entox' ),
					]
				);

					$this->add_control(
						'title_normal_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tables .pricing-item .title' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'section_title_active',
					[
						'label' => esc_html__( 'Active', 'entox' ),
					]
				);

					$this->add_control(
						'title_active_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tables .pricing-item.active_package .title' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();
			$this->end_controls_tabs();

			$this->add_responsive_control(
				'title_margin',
				[
					'label' 		=> esc_html__( 'Margin', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-pricing-tables .pricing-item .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		/* Section Item Style */
		$this->start_controls_section(
			'section_item_style',
			[
				'label' => esc_html__( 'Item', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'item_typography',
					'selector' 	=> '{{WRAPPER}} .ova-pricing-tables .pricing-item .package-item .item',
				]
			);

			$this->start_controls_tabs( 'section_item_tabs' );
				$this->start_controls_tab(
					'section_item_normal',
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
								'{{WRAPPER}} .ova-pricing-tables .pricing-item .package-item .item' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'section_item_active',
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
								'{{WRAPPER}} .ova-pricing-tables .pricing-item.active_package .package-item .item' => 'color: {{VALUE}}',
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
					'selector' 	=> '{{WRAPPER}} .ova-pricing-tables .pricing-item .package-item .item:not(:last-child)',
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'item_gap',
				[
					'label' 		=> esc_html__( 'Grap', 'entox' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-pricing-tables .pricing-item .package-item .item' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'item_margin',
				[
					'label' 		=> esc_html__( 'Margin', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-pricing-tables .pricing-item .package-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		/* Section Button Style */
		$this->start_controls_section(
			'section_button_style',
			[
				'label' => esc_html__( 'Button', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'button_typography',
					'selector' 	=> '{{WRAPPER}} .ova-pricing-tables .pricing-item .package-btn a',
				]
			);

			$this->start_controls_tabs( 'section_button_tabs' );
				$this->start_controls_tab(
					'section_button_normal',
					[
						'label' => esc_html__( 'Normal', 'entox' ),
					]
				);

					$this->add_control(
						'button_normal_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tables .pricing-item .package-btn a' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'button_normal_background',
						[
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tables .pricing-item .package-btn a' => 'background-color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'section_button_hover',
					[
						'label' => esc_html__( 'Hover', 'entox' ),
					]
				);

					$this->add_control(
						'button_hover_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tables .pricing-item .package-btn a:hover' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'button_hover_background',
						[
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tables .pricing-item .package-btn a:hover' => 'background-color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'section_button_active_hover',
					[
						'label' => esc_html__( 'Active Hover', 'entox' ),
					]
				);

					$this->add_control(
						'button_active_hover_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tables .pricing-item.active_package .package-btn a:hover' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'button_active_hover_background',
						[
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-pricing-tables .pricing-item.active_package .package-btn a:hover' => 'background-color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();
			$this->end_controls_tabs();

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'button_border',
					'label' 	=> esc_html__( 'Border', 'entox' ),
					'selector' 	=> '{{WRAPPER}} .ova-pricing-tables .pricing-item .package-btn a',
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'button_border_radius',
				[
					'label' 		=> esc_html__( 'Border Radius', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-pricing-tables .pricing-item .package-btn a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'button_padding',
				[
					'label' 		=> esc_html__( 'Padding', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-pricing-tables .pricing-item .package-btn a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'button_margin',
				[
					'label' 		=> esc_html__( 'Margin', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-pricing-tables .pricing-item .package-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();
	}

	protected function ova_get_currencies() {
		$currencies = array_unique(
			apply_filters(
				'ova_counter_up_currencies',
				array(
					''	  => esc_html__( 'None', 'entox' ),
					'AED' => esc_html__( 'United Arab Emirates dirham', 'entox' ),
					'AFN' => esc_html__( 'Afghan afghani', 'entox' ),
					'ALL' => esc_html__( 'Albanian lek', 'entox' ),
					'AMD' => esc_html__( 'Armenian dram', 'entox' ),
					'ANG' => esc_html__( 'Netherlands Antillean guilder', 'entox' ),
					'AOA' => esc_html__( 'Angolan kwanza', 'entox' ),
					'ARS' => esc_html__( 'Argentine peso', 'entox' ),
					'AUD' => esc_html__( 'Australian dollar', 'entox' ),
					'AWG' => esc_html__( 'Aruban florin', 'entox' ),
					'AZN' => esc_html__( 'Azerbaijani manat', 'entox' ),
					'BAM' => esc_html__( 'Bosnia and Herzegovina convertible mark', 'entox' ),
					'BBD' => esc_html__( 'Barbadian dollar', 'entox' ),
					'BDT' => esc_html__( 'Bangladeshi taka', 'entox' ),
					'BGN' => esc_html__( 'Bulgarian lev', 'entox' ),
					'BHD' => esc_html__( 'Bahraini dinar', 'entox' ),
					'BIF' => esc_html__( 'Burundian franc', 'entox' ),
					'BMD' => esc_html__( 'Bermudian dollar', 'entox' ),
					'BND' => esc_html__( 'Brunei dollar', 'entox' ),
					'BOB' => esc_html__( 'Bolivian boliviano', 'entox' ),
					'BRL' => esc_html__( 'Brazilian real', 'entox' ),
					'BSD' => esc_html__( 'Bahamian dollar', 'entox' ),
					'BTC' => esc_html__( 'Bitcoin', 'entox' ),
					'BTN' => esc_html__( 'Bhutanese ngultrum', 'entox' ),
					'BWP' => esc_html__( 'Botswana pula', 'entox' ),
					'BYR' => esc_html__( 'Belarusian ruble (old)', 'entox' ),
					'BYN' => esc_html__( 'Belarusian ruble', 'entox' ),
					'BZD' => esc_html__( 'Belize dollar', 'entox' ),
					'CAD' => esc_html__( 'Canadian dollar', 'entox' ),
					'CDF' => esc_html__( 'Congolese franc', 'entox' ),
					'CHF' => esc_html__( 'Swiss franc', 'entox' ),
					'CLP' => esc_html__( 'Chilean peso', 'entox' ),
					'CNY' => esc_html__( 'Chinese yuan', 'entox' ),
					'COP' => esc_html__( 'Colombian peso', 'entox' ),
					'CRC' => esc_html__( 'Costa Rican col&oacute;n', 'entox' ),
					'CUC' => esc_html__( 'Cuban convertible peso', 'entox' ),
					'CUP' => esc_html__( 'Cuban peso', 'entox' ),
					'CVE' => esc_html__( 'Cape Verdean escudo', 'entox' ),
					'CZK' => esc_html__( 'Czech koruna', 'entox' ),
					'DJF' => esc_html__( 'Djiboutian franc', 'entox' ),
					'DKK' => esc_html__( 'Danish krone', 'entox' ),
					'DOP' => esc_html__( 'Dominican peso', 'entox' ),
					'DZD' => esc_html__( 'Algerian dinar', 'entox' ),
					'EGP' => esc_html__( 'Egyptian pound', 'entox' ),
					'ERN' => esc_html__( 'Eritrean nakfa', 'entox' ),
					'ETB' => esc_html__( 'Ethiopian birr', 'entox' ),
					'EUR' => esc_html__( 'Euro', 'entox' ),
					'FJD' => esc_html__( 'Fijian dollar', 'entox' ),
					'FKP' => esc_html__( 'Falkland Islands pound', 'entox' ),
					'GBP' => esc_html__( 'Pound sterling', 'entox' ),
					'GEL' => esc_html__( 'Georgian lari', 'entox' ),
					'GGP' => esc_html__( 'Guernsey pound', 'entox' ),
					'GHS' => esc_html__( 'Ghana cedi', 'entox' ),
					'GIP' => esc_html__( 'Gibraltar pound', 'entox' ),
					'GMD' => esc_html__( 'Gambian dalasi', 'entox' ),
					'GNF' => esc_html__( 'Guinean franc', 'entox' ),
					'GTQ' => esc_html__( 'Guatemalan quetzal', 'entox' ),
					'GYD' => esc_html__( 'Guyanese dollar', 'entox' ),
					'HKD' => esc_html__( 'Hong Kong dollar', 'entox' ),
					'HNL' => esc_html__( 'Honduran lempira', 'entox' ),
					'HRK' => esc_html__( 'Croatian kuna', 'entox' ),
					'HTG' => esc_html__( 'Haitian gourde', 'entox' ),
					'HUF' => esc_html__( 'Hungarian forint', 'entox' ),
					'IDR' => esc_html__( 'Indonesian rupiah', 'entox' ),
					'ILS' => esc_html__( 'Israeli new shekel', 'entox' ),
					'IMP' => esc_html__( 'Manx pound', 'entox' ),
					'INR' => esc_html__( 'Indian rupee', 'entox' ),
					'IQD' => esc_html__( 'Iraqi dinar', 'entox' ),
					'IRR' => esc_html__( 'Iranian rial', 'entox' ),
					'IRT' => esc_html__( 'Iranian toman', 'entox' ),
					'ISK' => esc_html__( 'Icelandic kr&oacute;na', 'entox' ),
					'JEP' => esc_html__( 'Jersey pound', 'entox' ),
					'JMD' => esc_html__( 'Jamaican dollar', 'entox' ),
					'JOD' => esc_html__( 'Jordanian dinar', 'entox' ),
					'JPY' => esc_html__( 'Japanese yen', 'entox' ),
					'KES' => esc_html__( 'Kenyan shilling', 'entox' ),
					'KGS' => esc_html__( 'Kyrgyzstani som', 'entox' ),
					'KHR' => esc_html__( 'Cambodian riel', 'entox' ),
					'KMF' => esc_html__( 'Comorian franc', 'entox' ),
					'KPW' => esc_html__( 'North Korean won', 'entox' ),
					'KRW' => esc_html__( 'South Korean won', 'entox' ),
					'KWD' => esc_html__( 'Kuwaiti dinar', 'entox' ),
					'KYD' => esc_html__( 'Cayman Islands dollar', 'entox' ),
					'KZT' => esc_html__( 'Kazakhstani tenge', 'entox' ),
					'LAK' => esc_html__( 'Lao kip', 'entox' ),
					'LBP' => esc_html__( 'Lebanese pound', 'entox' ),
					'LKR' => esc_html__( 'Sri Lankan rupee', 'entox' ),
					'LRD' => esc_html__( 'Liberian dollar', 'entox' ),
					'LSL' => esc_html__( 'Lesotho loti', 'entox' ),
					'LYD' => esc_html__( 'Libyan dinar', 'entox' ),
					'MAD' => esc_html__( 'Moroccan dirham', 'entox' ),
					'MDL' => esc_html__( 'Moldovan leu', 'entox' ),
					'MGA' => esc_html__( 'Malagasy ariary', 'entox' ),
					'MKD' => esc_html__( 'Macedonian denar', 'entox' ),
					'MMK' => esc_html__( 'Burmese kyat', 'entox' ),
					'MNT' => esc_html__( 'Mongolian t&ouml;gr&ouml;g', 'entox' ),
					'MOP' => esc_html__( 'Macanese pataca', 'entox' ),
					'MRU' => esc_html__( 'Mauritanian ouguiya', 'entox' ),
					'MUR' => esc_html__( 'Mauritian rupee', 'entox' ),
					'MVR' => esc_html__( 'Maldivian rufiyaa', 'entox' ),
					'MWK' => esc_html__( 'Malawian kwacha', 'entox' ),
					'MXN' => esc_html__( 'Mexican peso', 'entox' ),
					'MYR' => esc_html__( 'Malaysian ringgit', 'entox' ),
					'MZN' => esc_html__( 'Mozambican metical', 'entox' ),
					'NAD' => esc_html__( 'Namibian dollar', 'entox' ),
					'NGN' => esc_html__( 'Nigerian naira', 'entox' ),
					'NIO' => esc_html__( 'Nicaraguan c&oacute;rdoba', 'entox' ),
					'NOK' => esc_html__( 'Norwegian krone', 'entox' ),
					'NPR' => esc_html__( 'Nepalese rupee', 'entox' ),
					'NZD' => esc_html__( 'New Zealand dollar', 'entox' ),
					'OMR' => esc_html__( 'Omani rial', 'entox' ),
					'PAB' => esc_html__( 'Panamanian balboa', 'entox' ),
					'PEN' => esc_html__( 'Sol', 'entox' ),
					'PGK' => esc_html__( 'Papua New Guinean kina', 'entox' ),
					'PHP' => esc_html__( 'Philippine peso', 'entox' ),
					'PKR' => esc_html__( 'Pakistani rupee', 'entox' ),
					'PLN' => esc_html__( 'Polish z&#x142;oty', 'entox' ),
					'PRB' => esc_html__( 'Transnistrian ruble', 'entox' ),
					'PYG' => esc_html__( 'Paraguayan guaran&iacute;', 'entox' ),
					'QAR' => esc_html__( 'Qatari riyal', 'entox' ),
					'RON' => esc_html__( 'Romanian leu', 'entox' ),
					'RSD' => esc_html__( 'Serbian dinar', 'entox' ),
					'RUB' => esc_html__( 'Russian ruble', 'entox' ),
					'RWF' => esc_html__( 'Rwandan franc', 'entox' ),
					'SAR' => esc_html__( 'Saudi riyal', 'entox' ),
					'SBD' => esc_html__( 'Solomon Islands dollar', 'entox' ),
					'SCR' => esc_html__( 'Seychellois rupee', 'entox' ),
					'SDG' => esc_html__( 'Sudanese pound', 'entox' ),
					'SEK' => esc_html__( 'Swedish krona', 'entox' ),
					'SGD' => esc_html__( 'Singapore dollar', 'entox' ),
					'SHP' => esc_html__( 'Saint Helena pound', 'entox' ),
					'SLL' => esc_html__( 'Sierra Leonean leone', 'entox' ),
					'SOS' => esc_html__( 'Somali shilling', 'entox' ),
					'SRD' => esc_html__( 'Surinamese dollar', 'entox' ),
					'SSP' => esc_html__( 'South Sudanese pound', 'entox' ),
					'STN' => esc_html__( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'entox' ),
					'SYP' => esc_html__( 'Syrian pound', 'entox' ),
					'SZL' => esc_html__( 'Swazi lilangeni', 'entox' ),
					'THB' => esc_html__( 'Thai baht', 'entox' ),
					'TJS' => esc_html__( 'Tajikistani somoni', 'entox' ),
					'TMT' => esc_html__( 'Turkmenistan manat', 'entox' ),
					'TND' => esc_html__( 'Tunisian dinar', 'entox' ),
					'TOP' => esc_html__( 'Tongan pa&#x2bb;anga', 'entox' ),
					'TRY' => esc_html__( 'Turkish lira', 'entox' ),
					'TTD' => esc_html__( 'Trinidad and Tobago dollar', 'entox' ),
					'TWD' => esc_html__( 'New Taiwan dollar', 'entox' ),
					'TZS' => esc_html__( 'Tanzanian shilling', 'entox' ),
					'UAH' => esc_html__( 'Ukrainian hryvnia', 'entox' ),
					'UGX' => esc_html__( 'Ugandan shilling', 'entox' ),
					'USD' => esc_html__( 'United States (US) dollar', 'entox' ),
					'UYU' => esc_html__( 'Uruguayan peso', 'entox' ),
					'UZS' => esc_html__( 'Uzbekistani som', 'entox' ),
					'VEF' => esc_html__( 'Venezuelan bol&iacute;var', 'entox' ),
					'VES' => esc_html__( 'Bol&iacute;var soberano', 'entox' ),
					'VND' => esc_html__( 'Vietnamese &#x111;&#x1ed3;ng', 'entox' ),
					'VUV' => esc_html__( 'Vanuatu vatu', 'entox' ),
					'WST' => esc_html__( 'Samoan t&#x101;l&#x101;', 'entox' ),
					'XAF' => esc_html__( 'Central African CFA franc', 'entox' ),
					'XCD' => esc_html__( 'East Caribbean dollar', 'entox' ),
					'XOF' => esc_html__( 'West African CFA franc', 'entox' ),
					'XPF' => esc_html__( 'CFP franc', 'entox' ),
					'YER' => esc_html__( 'Yemeni rial', 'entox' ),
					'ZAR' => esc_html__( 'South African rand', 'entox' ),
					'ZMW' => esc_html__( 'Zambian kwacha', 'entox' ),
				)
			)
		);

		return $currencies;
	}

	protected function ova_get_currency_symbols() {

		$symbols = apply_filters(
			'ova_counter_up_currency_symbols',
			array(
				'AED' => '&#x62f;.&#x625;',
				'AFN' => '&#x60b;',
				'ALL' => 'L',
				'AMD' => 'AMD',
				'ANG' => '&fnof;',
				'AOA' => 'Kz',
				'ARS' => '&#36;',
				'AUD' => '&#36;',
				'AWG' => 'Afl.',
				'AZN' => 'AZN',
				'BAM' => 'KM',
				'BBD' => '&#36;',
				'BDT' => '&#2547;&nbsp;',
				'BGN' => '&#1083;&#1074;.',
				'BHD' => '.&#x62f;.&#x628;',
				'BIF' => 'Fr',
				'BMD' => '&#36;',
				'BND' => '&#36;',
				'BOB' => 'Bs.',
				'BRL' => '&#82;&#36;',
				'BSD' => '&#36;',
				'BTC' => '&#3647;',
				'BTN' => 'Nu.',
				'BWP' => 'P',
				'BYR' => 'Br',
				'BYN' => 'Br',
				'BZD' => '&#36;',
				'CAD' => '&#36;',
				'CDF' => 'Fr',
				'CHF' => '&#67;&#72;&#70;',
				'CLP' => '&#36;',
				'CNY' => '&yen;',
				'COP' => '&#36;',
				'CRC' => '&#x20a1;',
				'CUC' => '&#36;',
				'CUP' => '&#36;',
				'CVE' => '&#36;',
				'CZK' => '&#75;&#269;',
				'DJF' => 'Fr',
				'DKK' => 'DKK',
				'DOP' => 'RD&#36;',
				'DZD' => '&#x62f;.&#x62c;',
				'EGP' => 'EGP',
				'ERN' => 'Nfk',
				'ETB' => 'Br',
				'EUR' => '&euro;',
				'FJD' => '&#36;',
				'FKP' => '&pound;',
				'GBP' => '&pound;',
				'GEL' => '&#x20be;',
				'GGP' => '&pound;',
				'GHS' => '&#x20b5;',
				'GIP' => '&pound;',
				'GMD' => 'D',
				'GNF' => 'Fr',
				'GTQ' => 'Q',
				'GYD' => '&#36;',
				'HKD' => '&#36;',
				'HNL' => 'L',
				'HRK' => 'kn',
				'HTG' => 'G',
				'HUF' => '&#70;&#116;',
				'IDR' => 'Rp',
				'ILS' => '&#8362;',
				'IMP' => '&pound;',
				'INR' => '&#8377;',
				'IQD' => '&#x639;.&#x62f;',
				'IRR' => '&#xfdfc;',
				'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
				'ISK' => 'kr.',
				'JEP' => '&pound;',
				'JMD' => '&#36;',
				'JOD' => '&#x62f;.&#x627;',
				'JPY' => '&yen;',
				'KES' => 'KSh',
				'KGS' => '&#x441;&#x43e;&#x43c;',
				'KHR' => '&#x17db;',
				'KMF' => 'Fr',
				'KPW' => '&#x20a9;',
				'KRW' => '&#8361;',
				'KWD' => '&#x62f;.&#x643;',
				'KYD' => '&#36;',
				'KZT' => '&#8376;',
				'LAK' => '&#8365;',
				'LBP' => '&#x644;.&#x644;',
				'LKR' => '&#xdbb;&#xdd4;',
				'LRD' => '&#36;',
				'LSL' => 'L',
				'LYD' => '&#x644;.&#x62f;',
				'MAD' => '&#x62f;.&#x645;.',
				'MDL' => 'MDL',
				'MGA' => 'Ar',
				'MKD' => '&#x434;&#x435;&#x43d;',
				'MMK' => 'Ks',
				'MNT' => '&#x20ae;',
				'MOP' => 'P',
				'MRU' => 'UM',
				'MUR' => '&#x20a8;',
				'MVR' => '.&#x783;',
				'MWK' => 'MK',
				'MXN' => '&#36;',
				'MYR' => '&#82;&#77;',
				'MZN' => 'MT',
				'NAD' => 'N&#36;',
				'NGN' => '&#8358;',
				'NIO' => 'C&#36;',
				'NOK' => '&#107;&#114;',
				'NPR' => '&#8360;',
				'NZD' => '&#36;',
				'OMR' => '&#x631;.&#x639;.',
				'PAB' => 'B/.',
				'PEN' => 'S/',
				'PGK' => 'K',
				'PHP' => '&#8369;',
				'PKR' => '&#8360;',
				'PLN' => '&#122;&#322;',
				'PRB' => '&#x440;.',
				'PYG' => '&#8370;',
				'QAR' => '&#x631;.&#x642;',
				'RMB' => '&yen;',
				'RON' => 'lei',
				'RSD' => '&#1088;&#1089;&#1076;',
				'RUB' => '&#8381;',
				'RWF' => 'Fr',
				'SAR' => '&#x631;.&#x633;',
				'SBD' => '&#36;',
				'SCR' => '&#x20a8;',
				'SDG' => '&#x62c;.&#x633;.',
				'SEK' => '&#107;&#114;',
				'SGD' => '&#36;',
				'SHP' => '&pound;',
				'SLL' => 'Le',
				'SOS' => 'Sh',
				'SRD' => '&#36;',
				'SSP' => '&pound;',
				'STN' => 'Db',
				'SYP' => '&#x644;.&#x633;',
				'SZL' => 'L',
				'THB' => '&#3647;',
				'TJS' => '&#x405;&#x41c;',
				'TMT' => 'm',
				'TND' => '&#x62f;.&#x62a;',
				'TOP' => 'T&#36;',
				'TRY' => '&#8378;',
				'TTD' => '&#36;',
				'TWD' => '&#78;&#84;&#36;',
				'TZS' => 'Sh',
				'UAH' => '&#8372;',
				'UGX' => 'UGX',
				'USD' => '&#36;',
				'UYU' => '&#36;',
				'UZS' => 'UZS',
				'VEF' => 'Bs F',
				'VES' => 'Bs.S',
				'VND' => '&#8363;',
				'VUV' => 'Vt',
				'WST' => 'T',
				'XAF' => 'CFA',
				'XCD' => '&#36;',
				'XOF' => 'CFA',
				'XPF' => 'Fr',
				'YER' => '&#xfdfc;',
				'ZAR' => '&#82;',
				'ZMW' => 'ZK',
			)
		);

		return $symbols;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$package_items 		= $settings['package'];
		$currency_position 	= $settings['currency_position'];
		?>

		<?php if ( $package_items && is_array( $package_items ) ): ?>
			<div class="ova-pricing-tables">
				<?php foreach( $package_items as $k => $package ): 
					$price 				= $package['price'];
					$currency 			= $package['currencies'];
					$currency_symbols 	= $this->ova_get_currency_symbols();
					$symbols 			= isset( $currency_symbols[$currency] ) ? $currency_symbols[$currency]: '';
					$per_time 			= $package['per_time'];
					$title 				= $package['title'];
					$package_id 		= $package['package_id'];
					$package_item 		= explode( '|' , $package['package_item'] );
					$text_button 		= $package['text_button'];
					$link 				= $package['link']['url'];
					$target 			= $package['link']['is_external'] ? ' target="_blank"' : '';
					$active 			= ( $k == 0 ) ? ' active_package' : '';
				?>
					<div class="pricing-item<?php echo esc_attr( $active ); ?>" id="<?php echo esc_attr( $package_id ); ?>">
						<h2 class="price-package">
							<?php if ( $symbols && 'before' === $currency_position ): ?>
								<span class="symbols"><?php printf( $symbols ); ?></span>
							<?php endif; ?>
							<span class="price"><?php echo esc_html( $price ); ?></span>
							<?php if ( $symbols && 'after' === $currency_position ): ?>
								<span class="symbols"><?php printf( $symbols ); ?></span>
							<?php endif; ?>
							<span class="per-time"><?php echo esc_html( $per_time ); ?></span>
						</h2>
						<div class="line"></div>
						<h3 class="title"><?php echo esc_html( $title ); ?></h3>
						<?php if ( $package_item && is_array( $package_item ) ): ?>
							<ul class="package-item">
								<?php foreach( $package_item as $item ): ?>
									<li class="item"><?php echo esc_html( trim( $item ) ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
						<?php if ( $text_button ): ?>
							<div class="package-btn">
								<a href="<?php echo esc_url( $link ); ?>"<?php printf( $target ); ?>>
									<?php echo esc_html( $text_button ); ?>
								</a>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		
		<?php
	}
}

$widgets_manager->register( new Entox_Elementor_Pricing_Tables() );
