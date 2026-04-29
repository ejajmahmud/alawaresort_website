<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Entox_Elementor_Card extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_card';
	}

	public function get_title() {
		return esc_html__( 'Ova Card', 'entox' );
	}

	public function get_icon() {
		return 'eicon-menu-card';
	}

	public function get_categories() {
		return [ 'entox' ];
	}


	protected function register_controls() {


		$this->start_controls_section(
			'section_card_content',
			[
				'label' => esc_html__( 'Content', 'entox' ),
			]
		);

		$this->add_control(
			'sub_title',
			[
				'label' => esc_html__( 'Sub Title', 'entox' ),
				'type' => Controls_Manager::TEXT,
				'row' => 2,
				'default' => esc_html__('01','entox'),
			]
		);

		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Heading Title', 'entox' ),
				'type' => Controls_Manager::TEXT,
				'row' => 5,
				'default' => esc_html__('Register Free','entox'),
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
				'default' => esc_html__('Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum..','entox'),
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
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .ova-card' => 'text-align: {{VALUE}}',
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sub_title_typography',
				'selector' => '{{WRAPPER}} .ova-card .sub_title',
			]
		);

		$this->add_control(
			'color_sub_title',
			[
				'label' => esc_html__( 'Color ', 'entox' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-card .sub_title' => 'color : {{VALUE}};',
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
					'{{WRAPPER}} .ova-card .sub_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->add_control(
			'background_color',
			[
				'label' => esc_html__( 'Background Color', 'entox' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-card .sub_title' => 'background-color : {{VALUE}};',
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
					'{{WRAPPER}} .ova-card .sub_title ' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .ova-card .title',
			]
		);

		$this->add_control(
			'color_title',
			[
				'label' => esc_html__( 'Color ', 'entox' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-card .title' => 'color : {{VALUE}};',
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
					'{{WRAPPER}} .ova-card .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .ova-card .desc',
			]
		);

		$this->add_control(
			'color_desc',
			[
				'label' => esc_html__( 'Color', 'entox' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-card .desc' => 'color : {{VALUE}};',
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
					'{{WRAPPER}} .ova-card .desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings();

		$sub_title = $settings['sub_title'];
		$title = $settings['title'];
		$desc = $settings['desc'];
		
		$tag_title = $settings['html_tag_title'];
		$tag_desc = $settings['html_tag_desc'];
		
	
		?>
		<div class="ova-card">

			<?php if (!empty($sub_title)) : ?>
				<div class="sub_title second_font">
					<?php printf($sub_title) ?>
			    </div>
			<?php endif ?>
			
			<?php if (!empty($title)) : ?>
				<<?php echo esc_attr($tag_title) ?> class="title second_font"><?php printf($title) ?></<?php echo esc_attr($tag_title) ?>>
			<?php endif ?>

			<?php if (!empty($desc)) : ?>
				<<?php echo esc_attr($tag_desc) ?> class="desc second_font"><?php printf($desc) ?></<?php echo esc_attr($tag_desc) ?>>
			<?php endif ?>

		</div>
		<?php

	}
}

$widgets_manager->register( new Entox_Elementor_Card() );


