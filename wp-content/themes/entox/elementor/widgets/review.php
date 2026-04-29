<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Entox_Elementor_Review extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_review';
	}

	public function get_title() {
		return esc_html__( 'Review', 'entox' );
	}

	public function get_icon() {
		return 'eicon-review';
	}

	public function get_categories() {
		return [ 'entox' ];
	}


	protected function register_controls() {


		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'entox' ),
			]
		);

			$this->add_control(
				'name_author',
				[
					'label'   => 'Author Name',
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Donald Salvor', 'entox' ),
				]
			);

			$this->add_control(
				'job',
				[
					'label'   => 'Job',
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Citizen Of Omina', 'entox' ),
				]
			);

			$this->add_control(
				'image_author',
				[
					'label'   => 'Author Image',
					'type'    => \Elementor\Controls_Manager::MEDIA,
					'default' => [
						'url' => Utils::get_placeholder_image_src(),
					],
				]
			);

			$this->add_control(
				'description',
				[
					'label'   => esc_html__( 'Description ', 'entox' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'default' => esc_html__( '"Sed ullamcorper morbi tincidunt or massa eget egestas purus. Non nisi est sit amet facilisis magna etiam."', 'entox' ),
				]
			);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_author_des',
			[
				'label' => esc_html__( 'Description ', 'entox' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'des_typography',
					'selector' => '{{WRAPPER}} .ova-review .client_info .info p.evaluate',
				]
			);

			$this->add_control(
				'des_color',
				[
					'label'     => esc_html__( 'Color description', 'entox' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'
						{{WRAPPER}} .ova-review .client_info .info p.evaluate' => 'color : {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'des_margin',
				[
					'label'      => esc_html__( 'Margin', 'entox' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-review .client_info .info p.evaluate' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'des_padding',
				[
					'label'      => esc_html__( 'Padding', 'entox' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-review .client_info .info p.evaluate' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);


		$this->end_controls_section();


		$this->start_controls_section(
			'section_author_name',
			[
				'label' => esc_html__( 'Author Name', 'entox' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'author_name_typography',
					'selector' => '{{WRAPPER}} .ova-review .client_info .info .name',
				]
			);

			$this->add_control(
				'author_name_color',
				[
					'label'     => esc_html__( 'Color Author', 'entox' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'
						{{WRAPPER}} .ova-review .client_info .info .name' => 'color : {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'author_name_margin',
				[
					'label'      => esc_html__( 'Margin', 'entox' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-review .client_info .info .name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'author_name_padding',
				[
					'label'      => esc_html__( 'Padding', 'entox' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-review .client_info .info .name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);


		$this->end_controls_section();


		$this->start_controls_section(
			'section_job',
			[
				'label' => esc_html__( 'Job', 'entox' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'job_typography',
					'selector' => '{{WRAPPER}} .ova-review .client_info .info .job',
				]
			);

			$this->add_control(
				'job_color',
				[
					'label'     => esc_html__( 'Color Job', 'entox' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'
						{{WRAPPER}} .ova-review .client_info .info .job' => 'color : {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'job_margin',
				[
					'label'      => esc_html__( 'Margin', 'entox' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-review .client_info .info .job' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'job_padding',
				[
					'label'      => esc_html__( 'Padding', 'entox' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .ova-review .client_info .info .job' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings();	

		?>

		<div class="ova-review">
			<div class="client_info">
				<div class="client" style="background-image: url(<?php echo esc_attr($settings['image_author']['url'])?>);">
				</div>
				<div class="info">

					<?php if( $settings['description'] != '' ) : ?>
						<p class="evaluate"><?php echo esc_html($settings['description']) ?></p>
					<?php endif; ?>
				
						<?php if( $settings['name_author'] != '' ): ?>
							<div class="name "><?php echo esc_html($settings['name_author']) ?></div>
						<?php endif; ?>

						<?php if( $settings['job'] != '' ): ?>
							<div class="job"><?php echo esc_html($settings['job']) ?></div>
						<?php endif; ?>
				</div>
				<!-- end info -->
			</div>
		</div>
		
		<?php
	}
	// end render
}

$widgets_manager->register( new Entox_Elementor_Review() );


