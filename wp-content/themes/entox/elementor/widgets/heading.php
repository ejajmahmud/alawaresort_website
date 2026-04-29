<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Entox_Elementor_Heading extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_heading';
	}

	public function get_title() {
		return esc_html__( 'Ova Heading', 'entox' );
	}

	public function get_icon() {
		return 'eicon-heading';
	}

	public function get_categories() {
		return [ 'entox' ];
	}


	protected function register_controls() {


		$this->start_controls_section(
			'section_heading_content',
			[
				'label' => esc_html__( 'Content', 'entox' ),
			]
		);

		$this->add_control(
			'sub_title',
			[
				'label' => esc_html__( 'Sub Title', 'entox' ),
				'type' => Controls_Manager::TEXTAREA,
				'row' => 2,
				'default' => esc_html__('HOW IT WORKS','entox'),
			]
		);

		$this->add_control(
			'html_tag_sub_title',
			[
				'label' => esc_html__( 'HTML Sub title Tag', 'entox' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'h4',
				'options' => [
					'h1' => "H1",
					'h2' => "H2",
					'h3' => "H3",
					'h4' => "H4",
					'h5' => "H5",
					'h6' => "H6",
					'div' => "div",
					'span' => "Span",
					'p' => "p",
				]
			]
		);



		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Heading Title', 'entox' ),
				'type' => Controls_Manager::TEXTAREA,
				'row' => 5,
				'default' => esc_html__('Our Work Process ','entox'),
			]
		);

		$this->add_control(
			'html_tag_title',
			[
				'label' => esc_html__( 'HTML Title Tag', 'entox' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => [
					'h1' => "H1",
					'h2' => "H2",
					'h3' => "H3",
					'h4' => "H4",
					'h5' => "H5",
					'h6' => "H6",
					'div' => "div",
					'span' => "Span",
					'p' => "p",
				]
			]
		);

		$this->add_control(
			'desc',
			[
				'label' => esc_html__( 'Description', 'entox' ),
				'type' => Controls_Manager::TEXTAREA,
				'row' => 2,
				'default' => esc_html__('Book online. We’ll provide you with a trusted, excellent services most accurate and fair-price service Estimate.','entox'),
			]
		);

		$this->add_control(
			'html_tag_desc',
			[
				'label' => esc_html__( 'HTML Description Tag', 'entox' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'p',
				'options' => [
					'h1' => "H1",
					'h2' => "H2",
					'h3' => "H3",
					'h4' => "H4",
					'h5' => "H5",
					'h6' => "H6",
					'div' => "div",
					'span' => "Span",
					'p' => "p",
				]
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
					'{{WRAPPER}} .ova-heading' => 'text-align: {{VALUE}}',
				]
			]
		);



		$this->end_controls_section();


		$this->start_controls_section(
			'section_sub_title',
			[
				'label' => esc_html__( 'Sub Title', 'entox' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_control(
			'height_before',
			[
				'label'   => esc_html__( 'Height Before Sub Title', 'entox' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 4,
				'selectors' => [

					'{{WRAPPER}} .ova-heading .sub_title::before' => 'height: {{VALUE}}px;',
				],
			]
		);

		$this->add_control(
			'color_before_title',
			[
				'label' => esc_html__( 'Color Before Sub Title', 'entox' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-heading .sub_title::before' => 'background : {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sub_title_typography',
				'selector' => '{{WRAPPER}} .ova-heading .sub_title',
			]
		);

		$this->add_control(
			'color_sub_title',
			[
				'label' => esc_html__( 'Color ', 'entox' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-heading .sub_title' => 'color : {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'margin_sub_title',
			[
				'label' => esc_html__( 'Margin', 'entox' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ova-heading .sub_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
		

		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Title', 'entox' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .ova-heading .title',
			]
		);

		$this->add_control(
			'color_title',
			[
				'label' => esc_html__( 'Color ', 'entox' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-heading .title' => 'color : {{VALUE}};',
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
					'{{WRAPPER}} .ova-heading .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_desc',
			[
				'label' => esc_html__( 'Description', 'entox' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'desc_typography',
				'selector' => '{{WRAPPER}} .ova-heading .desc',
			]
		);

		$this->add_control(
			'color_desc',
			[
				'label' => esc_html__( 'Color', 'entox' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-heading .desc' => 'color : {{VALUE}};',
				],
			]
		);


		$this->add_responsive_control(
			'margin_desc',
			[
				'label' => esc_html__( 'Margin', 'entox' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ova-heading .desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings 		= $this->get_settings();
		$sub_title 		= $settings['sub_title'];
		$title 			= $settings['title'];
		$desc 			= $settings['desc'];
		$tag_sub_title 	= $settings['html_tag_sub_title'];
		$tag_title 		= $settings['html_tag_title'];
		$tag_desc 		= $settings['html_tag_desc'];
		?>
		<div class="ova-heading">
			<?php if ( $sub_title ): ?>
				<<?php echo esc_attr( $tag_sub_title ); ?> class="sub_title second_font">
					<?php echo wp_kses_post( $sub_title ); ?>
				</<?php echo esc_attr( $tag_sub_title ); ?>>
			<?php endif; ?>
			<?php if ( $title ): ?>
				<<?php echo esc_attr( $tag_title ); ?> class="title second_font">
					<?php echo wp_kses_post( $title ); ?>
				</<?php echo esc_attr( $tag_title ); ?>>
			<?php endif; ?>
			<?php if ( $desc ): ?>
				<<?php echo esc_attr( $tag_desc ); ?> class="desc second_font">
					<?php echo wp_kses_post( $desc ); ?>
				</<?php echo esc_attr( $tag_desc ); ?>>
			<?php endif; ?>
		</div>
		<?php
	}
}

$widgets_manager->register( new Entox_Elementor_Heading() );