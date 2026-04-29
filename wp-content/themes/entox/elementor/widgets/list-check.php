<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Entox_Elementor_List_Check extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_list_check';
	}

	public function get_title() {
		return esc_html__( 'List Checked', 'entox' );
	}

	public function get_icon() {
		return 'eicon-editor-list-ul';
	}

	public function get_categories() {
		return [ 'entox' ];
	}


	protected function register_controls() {

		//SECTION CONTENT
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'entox' ),
			]
		);



		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'class_icon',
			[
				'label' => esc_html__( 'Class Icon', 'entox' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'ovaicon-checked-1', 'entox' ),
				
			]
		);


		$repeater->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'entox' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);



		$repeater->add_control(
			'sub_title',
			[
				'label' => esc_html__( 'Sub Title', 'entox' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
			]
		);


		$this->add_control(
			'tabs',
			[
				'label' => esc_html__( 'Tabs', 'entox' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => esc_html__( 'Wide Range of Cars', 'entox' ),
					],
					[
						'title' => esc_html__( 'Trusted of Thousands', 'entox' ),
					],
					[
						'title' => esc_html__( 'Best Car Rental Services', 'entox' ),
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon',
			[
				'label' => esc_html__( 'Icon', 'entox' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_control(
			'size_circle',
			[
				'label' => esc_html__( 'Size Circle', 'entox' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ova_list_checked ul li .icon ' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				],
			]
		);



		$this->add_control(
			'width_icon',
			[
				'label' => esc_html__( 'Size Icon', 'entox' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ova_list_checked ul li .icon  i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'color_icon',
			[
				'label' => esc_html__( 'Color ', 'entox' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova_list_checked ul li .icon i' => 'color : {{VALUE}};',
				],
			]
		);
			$this->add_control(
			'background_color_icon',
			[
				'label' => esc_html__( 'Background Color', 'entox' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova_list_checked ul li .icon' => 'background-color : {{VALUE}};',
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
					'{{WRAPPER}} .ova_list_checked ul li .icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .ova_list_checked ul li .text .title',
			]
		);

		$this->add_control(
			'color_title',
			[
				'label' => esc_html__( 'Color ', 'entox' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova_list_checked ul li .text .title' => 'color : {{VALUE}};',
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
					'{{WRAPPER}} .ova_list_checked ul li .text .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_sub_title',
			[
				'label' => esc_html__( ' Sub Title', 'entox' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_sub__typography',
				'selector' => '{{WRAPPER}} .ova_list_checked ul li .text span',
			]
		);

		$this->add_control(
			'color_sub_title',
			[
				'label' => esc_html__( 'Color ', 'entox' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova_list_checked ul li .text span' => 'color : {{VALUE}};',
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
					'{{WRAPPER}} .ova_list_checked ul li .text span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_item',
			[
				'label' => esc_html__( 'Item', 'entox' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'width_title',
			[
				'label' => esc_html__( 'Width title', 'entox' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ova_list_checked ul li .text' => 'width: {{SIZE}}%;',
				],
			]
		);


		$this->add_control(
			'margin_item',
			[
				'label' => esc_html__( 'Margin item', 'entox' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ova_list_checked ul li:not(:last-child) ' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
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
		$settings = $this->get_settings_for_display();
		$tabs = $settings['tabs'];
		?>
		
		<div class="ova_list_checked" >
			<?php if( !empty( $tabs ) ) : ?>
				<ul>
				<?php 
					foreach( $tabs as $item ) { 
						?>
						<li class="item">
							<?php if ( $item['class_icon'] ): ?>
								<div class="icon">
									<i class="<?php echo esc_html( $item['class_icon'] ); ?>"></i>
								</div>
							<?php endif; ?>
							<div class="text">
								<?php if ( $item['title'] ): ?>
									<h2 class="title"><?php echo esc_html( $item['title'] ); ?></h2>
								<?php endif; ?>
								<?php if ( $item['sub_title'] ): ?>
									<span><?php echo esc_html( $item['sub_title'] ); ?></span>
								<?php endif; ?>
							</div>
						</li>
					<?php } ?>
				</ul>
			<?php endif; ?>
		</div>

		<?php
	}
}

$widgets_manager->register( new Entox_Elementor_List_Check() );
