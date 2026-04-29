<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Entox_Elementor_Contact_Boxes extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_contact_boxes';
	}

	public function get_title() {
		return esc_html__( 'Contact Boxes', 'entox' );
	}

	public function get_icon() {
		return 'eicon-info';
	}

	public function get_categories() {
		return [ 'entox' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_contact_boxes',
			[
				'label' => esc_html__( 'Content', 'entox' ),
			]
		);

			$this->add_control(
				'class_icon',
				[
					'label'   => esc_html__( 'Class Icon', 'entox' ),
					'type'    => Controls_Manager::TEXT,
					'default' => esc_html__( 'fas fa-envelope', 'entox' ),
				]
			);

			$this->add_control(
				'title',
				[
					'label'   => esc_html__( 'Title', 'entox' ),
					'type'    => Controls_Manager::TEXT,
					'default' => esc_html__( 'Send a Email', 'entox' ),
				]
			);

			$this->add_control(
				'type_link',
				[
					'label' => esc_html__( 'Type Link', 'entox' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'email',
					'options' => [
						'email' 	=> esc_html__('Email', 'entox'),
						'tell' 		=> esc_html__('Tell', 'entox'),
						'domain' 	=> esc_html__('Domain', 'entox'),
						'none' 		=> esc_html__('None', 'entox'),
					]
				]
			);

			$this->add_control(
				'link',
				[
					'label' 		=> esc_html__( 'Link', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::URL,
					'placeholder' 	=> esc_html__( 'https://your-link.com', 'entox' ),
					'show_external' => false,
					'default' 		=> [
						'url' => '#',
					],
				]
			);

			$this->add_control(
				'address',
				[
					'label' 	=> esc_html__( 'Address', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::TEXT,
					'default' 	=> esc_html__('needhelp@company.com', 'entox'),
				]
			);

			$this->add_responsive_control(
				'align',
				[
					'label' 	=> esc_html__( 'Alignment', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::CHOOSE,
					'options' 	=> [
						'left' => [
							'title' => esc_html__( 'Left', 'entox' ),
							'icon' 	=> 'eicon-h-align-left',
						],
						'center' => [
							'title' => esc_html__( 'Center', 'entox' ),
							'icon' 	=> 'eicon-h-align-center',
						],
						'right' => [
							'title' => esc_html__( 'Right', 'entox' ),
							'icon' 	=> 'eicon-h-align-right',
						],
					],
					'default' 	=> 'center',
					'selectors' => [
						'{{WRAPPER}}' => 'text-align: {{VALUE}}',
					]
				]
			);

		$this->end_controls_section();

		/* Section Icon Style */
		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => esc_html__( 'Icon', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'icon_typography',
					'selector' 	=> '{{WRAPPER}} .ova-contact-boxes .boxes .icon i',
				]
			);

			$this->add_control(
				'icon_color',
				[
					'label' 	=> esc_html__( 'Color', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-contact-boxes .boxes .icon i' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'icon_background',
				[
					'label' 	=> esc_html__( 'Backgorund', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-contact-boxes .boxes .icon' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'icon_size',
				[
					'label' 		=> esc_html__( 'Size', 'entox' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 500,
							'step' => 5,
						],
					],
					'default' => [
						'size' => 105,
						'unit' => 'px',
					],
					'selectors' => [
						'{{WRAPPER}} .ova-contact-boxes .boxes .icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'icon_top',
				[
					'label' 		=> esc_html__( 'Top', 'entox' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => -500,
							'max' => 500,
							'step' => 5,
						],
						'%' => [
							'min' => -100,
							'max' => 100,
						],
					],
					'default' => [
						'size' => -52.5,
						'unit' => 'px',
					],
					'selectors' => [
						'{{WRAPPER}} .ova-contact-boxes .boxes .icon' => 'top: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'icon_border',
					'label' 	=> esc_html__( 'Border', 'entox' ),
					'selector' 	=> '{{WRAPPER}} .ova-contact-boxes .boxes .icon',
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'icon_border_radius',
				[
					'label' 		=> esc_html__( 'Border Radius', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-contact-boxes .boxes .icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		/* Section Boxes Style */
		$this->start_controls_section(
			'section_boxes_style',
			[
				'label' => esc_html__( 'Boxes', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'boxes_width',
				[
					'label' 		=> esc_html__( 'Width', 'entox' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1000,
							'step' => 5,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'default' => [
						'size' => 370,
						'unit' => 'px',
					],
					'selectors' => [
						'{{WRAPPER}} .ova-contact-boxes' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'boxes_background',
				[
					'label' 	=> esc_html__( 'Backgorund', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-contact-boxes .boxes' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'boxes_border',
					'label' 	=> esc_html__( 'Border', 'entox' ),
					'selector' 	=> '{{WRAPPER}} .ova-contact-boxes .boxes',
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'boxes_border_radius',
				[
					'label' 		=> esc_html__( 'Border Radius', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-contact-boxes .boxes' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'boxes_padding',
				[
					'label' 		=> esc_html__( 'Padding', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-contact-boxes .boxes' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'boxes_margin',
				[
					'label' 		=> esc_html__( 'Margin', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-contact-boxes .boxes' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'boxes_box_shadow',
					'label' 	=> esc_html__( 'Box Shadow', 'entox' ),
					'selector' 	=> '{{WRAPPER}} .ova-contact-boxes .boxes',
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
					'selector' 	=> '{{WRAPPER}} .ova-contact-boxes .boxes .title',
				]
			);

			$this->add_control(
				'title_color',
				[
					'label' 	=> esc_html__( 'Color', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-contact-boxes .boxes .title' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'title_margin',
				[
					'label' 		=> esc_html__( 'Margin', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-contact-boxes .boxes .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		/* Section Address Style */
		$this->start_controls_section(
			'section_address_style',
			[
				'label' => esc_html__( 'Address', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'address_typography',
					'selector' 	=> '{{WRAPPER}} .ova-contact-boxes .boxes .address, {{WRAPPER}} .ova-contact-boxes .boxes .address a',
				]
			);

			$this->start_controls_tabs( 'address_tabs' );

				$this->start_controls_tab(
					'address_normal_tab',
					[
						'label' => esc_html__( 'Normal', 'entox' ),
					]
				);

					$this->add_control(
						'address_normal_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-contact-boxes .boxes .address' 	=> 'color: {{VALUE}}',
								'{{WRAPPER}} .ova-contact-boxes .boxes .address a' 	=> 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'address_hover_tab',
					[
						'label' => esc_html__( 'Hover', 'entox' ),
					]
				);

					$this->add_control(
						'address_hover_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-contact-boxes .boxes .address a:hover' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();
			$this->end_controls_tabs();

			$this->add_responsive_control(
				'address_margin',
				[
					'label' 		=> esc_html__( 'Margin', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-contact-boxes .boxes .address' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$type_link 	= $settings['type_link'];
		$link 		= $settings['link']['url'];
		$target 	= $settings['link']['is_external'] ? ' target="_blank"' : '';
		$address 	= $settings['address'];
		$class_icon = $settings['class_icon'];
		$title 		= $settings['title'];

		switch ( $type_link ) {
			case "email" : {
				$address = "<a href='mailto:". $link ."'". $target .">". $address ."</a>";
				break;
			}
			case "tell" : {
				$address = "<a href='tel:". $link ."'". $target .">". $address ."</a>";
				break;
			}
			case "domain" : {
				$address = "<a href='". $link ."'". $target .">". $address ."</a>";
				break;
			}
			case "none" : {
				$address = $link ? "<a href='". $link ."'". $target .">". $address ."</a>" : $address;
				break;
			}
		}

		?>

		<div class="ova-contact-boxes">
			<div class="boxes">
				<div class="icon">
					<i class="<?php echo esc_attr( $class_icon ); ?>"></i>
				</div>
				<h2 class="title"><?php echo esc_html( $title ); ?></h2>
				<?php if ( $address ): ?>
					<div class="address"><?php printf( $address ); ?></div>
				<?php endif; ?>
			</div>
		</div>
		
		<?php
	}
}

$widgets_manager->register( new Entox_Elementor_Contact_Boxes() );
