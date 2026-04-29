<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Entox_Elementor_Image_List extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_image_list';
	}

	public function get_title() {
		return esc_html__( 'Category Icon', 'entox' );
	}

	public function get_icon() {
		return 'eicon-bullet-list';
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

			$args_query	= [
				'taxonomy' 	=> 'product_cat',
				'orderby' 	=> 'name',
	        	'order'   	=> 'ASC'
			];

			$categories 		= get_categories( $args_query );
			$defautl_category 	= array( '' => esc_html__( 'Select Category', 'entox' ) );
			$args_category 		= array();
			$result 			= array();

			if ( $categories && is_array( $categories ) ) {
				foreach ( $categories as $category ) {
					$args_category[$category->slug] = $category->cat_name;
				}
			}

			$result = array_merge( $defautl_category, $args_category );

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'image',
				[
					'label' 	=> esc_html__( 'Choose Image', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::MEDIA,
					'default' 	=> [
						'url' 	=> \Elementor\Utils::get_placeholder_image_src(),
					],
				]
			);

			$repeater->add_control(
				'search_result',
				[
					'label'   	=> esc_html__( 'Search Result', 'entox' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> 'default',
					'options' 	=> [
						'default' 	=> esc_html__( 'Default', 'entox' ),
						'search' 	=> esc_html__( 'Search', 'entox' ),
					],
				]
			);

			$repeater->add_control(
				'categories',
				[
					'label' 	=> esc_html__( 'Select Category', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> '',
					'options' 	=> $result,
					'condition' => [
						'search_result' => 'search'
					]
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
				'list',
				[
					'label' 	=> esc_html__( 'Image List', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::REPEATER,
					'fields' 	=> $repeater->get_controls(),
					'default' 	=> [
						[
							'image' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
							'link' 	=> [ 'url' => '#' ],
						],
						[
							'image' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
							'link' 	=> [ 'url' => '#' ],
						],
						[
							'image' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
							'link' 	=> [ 'url' => '#' ],
						],
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_style',
			[
				'label' => esc_html__( 'Content', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'content_max_width',
				[
					'label' 		=> esc_html__( 'Grap', 'entox' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1000,
							'step' => 5,
						],
					],
					'default' => [
						'size' => 110
					],
					'selectors' => [
						'{{WRAPPER}} .ova-image-list .image' => 'max-width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->start_controls_tabs( 'content_tabs' );
				$this->start_controls_tab(
					'content_normal_tab',
					[
						'label' => esc_html__( 'Normal', 'entox' ),
					],
				);

					$this->add_control(
						'content_normal_background',
						[
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-image-list .image' => 'background-color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'content_hover_tab',
					[
						'label' => esc_html__( 'Hover', 'entox' ),
					],
				);

					$this->add_control(
						'content_hover_background',
						[
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-image-list .image:hover' => 'background-color: {{VALUE}}',
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
					'selector' 	=> '{{WRAPPER}} .ova-image-list .image',
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
						'{{WRAPPER}} .ova-image-list .image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
						'{{WRAPPER}} .ova-image-list .image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'content_margin',
				[
					'label' 		=> esc_html__( 'Margin', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-image-list .image'=> 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'content_box_shadow',
					'label' 	=> esc_html__( 'Box Shadow', 'entox' ),
					'selector' 	=> '{{WRAPPER}} .ova-image-list .image',
				]
			);

		$this->end_controls_section();
	}

	protected function render() {
		$settings 	= $this->get_settings();

		$image_list = $settings['list'];

		?>

		<?php if ( $image_list && is_array( $image_list ) ): ?>
			<div class="ova-image-list">
				<?php foreach( $image_list as $images ):
					$image_url 	= isset( $images['image']['url'] ) ? $images['image']['url'] : '';
					$image_alt 	= isset( $images['image']['alt'] ) ? $images['image']['alt'] : '';
					$link 		= $images['link']['url'];
					$target 	= $images['link']['is_external'] ? ' target="_blank"' : '';

					if ( 'search' === $images['search_result'] ) {
						$category_slug = $images['categories'];

						if ( $category_slug ) {
							$link = add_query_arg( 'cat', $category_slug, $link );
						}
					}
				?>

				<?php if ( $image_url ): ?>
					<?php if ( $link ): ?>
						<a href="<?php echo esc_url( $link ); ?>" class="image-link"<?php printf( $target ); ?>>
							<div class="image">
								<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
							</div>
						</a>
					<?php else: ?>
						<div class="image">
							<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
						</div>
				<?php endif; endif; ?>

				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php
	}
}

$widgets_manager->register( new Entox_Elementor_Image_List() );


