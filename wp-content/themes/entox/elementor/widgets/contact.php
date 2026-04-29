<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Entox_Elementor_Contact extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_contact';
	}

	public function get_title() {
		return esc_html__( 'Contact', 'entox' );
	}

	public function get_icon() {
		return 'eicon-envelope';
	}

	public function get_categories() {
		return [ 'entox' ];
	}

	public function get_script_depends() {
		return [ 'entox-elementor-contact' ];
	}

	protected function register_controls() {


		$this->start_controls_section(
			'section_heading_content',
			[
				'label' => esc_html__( 'Content', 'entox' ),
				
			]
		);

		$this->add_control(
			'type_link',
			[
				'label' => esc_html__( 'Type Link', 'entox' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'email',
				'options' => [
					'email' => esc_html__('Email', 'entox'),
					'tell' => esc_html__('Tell', 'entox'),
					'domain' => esc_html__('Domain', 'entox'),
					'none' => esc_html__('None', 'entox'),
				]
			]
		);

		$this->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'entox' ),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'entox' ),
				'show_external' => false,
				'default' => [
					'url' => '#',
					'is_external' => false,
					'nofollow' => false,
				],
			]
		);

		$this->add_control(
			'address',
			[
				'label' => esc_html__( 'Address', 'entox' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__('contact@domain.com', 'entox'),
			]
		);


		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'entox' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'entox' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'entox' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'entox' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .ova-contact' => 'text-align: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'class_icon',
			[
				'label' => esc_html__( 'Class icon', 'entox' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'flaticon-e-mail-envelope',
			]
		);

		$this->end_controls_section();


		//section style image
		$this->start_controls_section(
			'section_icon',
			[
				'label' => esc_html__( 'Icon', 'entox' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'font_size_icon',
			[
				'label' => esc_html__( 'Font Size', 'entox' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ova-contact .icon i:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'color_icon',
			[
				'label' => esc_html__( 'Color', 'entox' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-contact .icon i:before' => 'color : {{VALUE}};'
				],
			]
		);

		$this->add_responsive_control(
			'margin_icon',
			[
				'label' => esc_html__( 'Margin', 'entox' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ova-contact .icon i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_address',
			[
				'label' => esc_html__( 'Address', 'entox' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'address_typography',
				'selector' => '{{WRAPPER}} .ova-contact .address a, {{WRAPPER}} .ova-contact .address ',
			]
		);

		$this->add_control(
			'color_address',
			[
				'label' => esc_html__( 'Color', 'entox' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-contact .address a' => 'color : {{VALUE}};',
					'{{WRAPPER}} .ova-contact .address' => 'color : {{VALUE}};',
				],
			]
		);


		$this->add_control(
			'color_address_hover',
			[
				'label' => esc_html__( 'Color hover', 'entox' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-contact .address a:hover' => 'color : {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'margin_title',
			[
				'label' => esc_html__( 'Margin', 'entox' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ova-contact .address' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();


	}

	protected function render() {
		$settings = $this->get_settings();
		$type_link = $settings['type_link'];
		$link = $settings['link']['url'];
		$address = $settings['address'];

		$icon = $settings['class_icon'];
		switch($type_link) {
			case "email" : {
				$address = "<a href='mailto:".$link."'>".$address."</a>";
				break;
			}
			case "tell" : {
				$address = "<a href='tel:".$link."'>".$address."</a>";
				break;
			}

			case "domain" : {
				$address = "<a href='".$link."'>".$address."</a>";
				break;
			}

			case "none" : {
				$address = $link ? "<a href='".$link."'>".$address."</a>" : $address;
				break;
			}

		}
		
		?>
		<div class="ova-contact">
			<?php if (!empty($icon)) : ?>
				<div class="icon">
					<i class="<?php echo esc_attr($icon) ?>"></i>
				</div>
			<?php endif ?>
			<?php if (!empty($address)) : ?>
				<div class="address"><span><?php printf( $address ); ?></span></div>
			<?php endif ?>
			
		</div>
		<?php

	}
// end render
}

$widgets_manager->register( new Entox_Elementor_Contact() );


