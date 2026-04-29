<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Entox_Elementor_Product_List extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_product_list';
	}

	public function get_title() {
		return esc_html__( 'Product List', 'entox' );
	}

	public function get_icon() {
		return 'eicon-products';
	}

	public function get_categories() {
		return [ 'entox' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_product_list_content',
			[
				'label' => esc_html__( 'Content', 'entox' ),
			]
		);

			$this->add_control(
				'show_featured',
				[
					'label' 		=> __( 'Just Show Featured', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 		=> __( 'Show', 'entox' ),
					'label_off' 	=> __( 'Hide', 'entox' ),
					'default' 		=> 'no',
				]
			);

			$this->add_control(
				'columns',
				[
					'label' 	=> esc_html__( 'Columns', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'column3',
					'options' 	=> [
						'column1' => esc_html__( 'Column 1', 'entox' ),
						'column2' => esc_html__( 'Column 2', 'entox' ),
						'column3' => esc_html__( 'Column 3', 'entox' ),
						'column4' => esc_html__( 'Column 4', 'entox' ),
					],
				]
			);

			$args_query	= [
				'taxonomy' 	=> 'product_cat',
				'orderby' 	=> 'name',
	        	'order'   	=> 'ASC'
			];

			$categories 		= get_categories( $args_query );
			$defautl_category 	= array( 'all' => esc_html__( 'All', 'entox' ) );
			$args_category 		= array();
			$result 			= array();

			if ( $categories && is_array( $categories ) ) {
				foreach ( $categories as $category ) {
					$args_category[$category->slug] = $category->cat_name;
				}
			}

			$result = array_merge( $defautl_category, $args_category );

			$this->add_control(
				'categories',
				[
					'label' 	=> esc_html__( 'Select Category', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'all',
					'options' 	=> $result,
				]
			);

			$this->add_control(
				'posts_per_page',
				[
					'label' 	=> esc_html__( 'Posts Per Page', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::NUMBER,
					'default' 	=> 4,
				]
			);

			$this->add_control(
				'orderby',
				[
					'label' 	=> esc_html__( 'Order By', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'title',
					'options' 	=> [
						'title' => esc_html__( 'Title', 'entox' ),
						'ID' 	=> esc_html__( 'ID', 'entox' ),
						'date' 	=> esc_html__( 'Date', 'entox' ),
					],
				]
			);

			$this->add_control(
				'order',
				[
					'label' 	=> esc_html__( 'Order', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'ASC',
					'options' 	=> [
						'ASC' 	=> esc_html__( 'Ascending', 'entox' ),
						'DESC' 	=> esc_html__( 'Descending', 'entox' ),
					],
				]
			);

			$this->add_control(
				'category_in',
				[
					'label'   		=> esc_html__( 'Category In', 'entox' ),
					'type'    		=> Controls_Manager::TEXT,
					'description' 	=> esc_html__( 'Enter the product category IDs. IDs are separated by "|". Ex: 1|2|3.', 'entox' ),
				]
			);

			$this->add_control(
				'category_not_in',
				[
					'label'   		=> esc_html__( 'Category Not In', 'entox' ),
					'type'    		=> Controls_Manager::TEXT,
					'description' 	=> esc_html__( 'Enter the product category IDs. IDs are separated by "|". Ex: 1|2|3.', 'entox' ),
				]
			);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_product_list_style',
			[
				'label' => esc_html__( 'Content', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'content_grap',
				[
					'label' 		=> esc_html__( 'Grap', 'entox' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 500,
							'step' => 5,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'default' => [
						'size' => 30,
						'unit' => 'px',
					],
					'selectors' => [
						'{{WRAPPER}} .ova-product-list' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'content_background',
				[
					'label' 	=> esc_html__( 'Background', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-list .entox_product' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'content_box_shadow',
					'label' 	=> esc_html__( 'Box Shadow', 'entox' ),
					'selector' 	=> '{{WRAPPER}} .ova-product-list .entox_product',
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'content_border',
					'label' 	=> esc_html__( 'Border', 'entox' ),
					'selector' 	=> '{{WRAPPER}} .ova-product-list .entox_product',
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
						'{{WRAPPER}} .ova-product-list .entox_product' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
						'{{WRAPPER}} .ova-product-list .entox_product .entox_foot_product' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

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
					'selector' 	=> '{{WRAPPER}} .ova-product-list .entox_product .entox_head_product .entox-product-price .product-amount .amount',
				]
			);

			$this->add_control(
				'price_background',
				[
					'label' 	=> esc_html__( 'Background', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-list .entox_product .entox_head_product .entox-product-price' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'price_color',
				[
					'label' 	=> esc_html__( 'Color', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-list .entox_product .entox_head_product .entox-product-price .product-amount .amount' => 'color: {{VALUE}}',
						'{{WRAPPER}} .ova-product-list .entox_product .entox_head_product .entox-product-price .product-amount' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'price_border',
					'label' 	=> esc_html__( 'Border', 'entox' ),
					'selector' 	=> '{{WRAPPER}} .ova-product-list .entox_product .entox_head_product .entox-product-price',
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'price_border_radius',
				[
					'label' 		=> esc_html__( 'Border Radius', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-product-list .entox_product .entox_head_product .entox-product-price' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'price_padding',
				[
					'label' 		=> esc_html__( 'Padding', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-product-list .entox_product .entox_head_product .entox-product-price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'day_options',
				[
					'label' => esc_html__( 'Day Options', 'entox' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'day_typography',
					'selector' 	=> '{{WRAPPER}} .ova-product-list .entox_product .entox_head_product .entox-product-price .define_1_day',
				]
			);

			$this->add_control(
				'day_color',
				[
					'label' 	=> esc_html__( 'Day Color', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-list .entox_product .entox_head_product .entox-product-price .define_1_day' => 'color: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();

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
					'selector' 	=> '{{WRAPPER}} .ova-product-list .entox_product .entox_foot_product .entox-product-title a',
				]
			);

			$this->start_controls_tabs( 'title_tabs' );
				$this->start_controls_tab(
					'title_normal_tab',
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
								'{{WRAPPER}} .ova-product-list .entox_product .entox_foot_product .entox-product-title a' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'title_hover_tab',
					[
						'label' => esc_html__( 'Hover', 'entox' ),
					]
				);

					$this->add_control(
						'title_hover_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-product-list .entox_product .entox_foot_product .entox-product-title a:hover' => 'color: {{VALUE}}',
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
						'{{WRAPPER}} .ova-product-list .entox_product .entox_foot_product .entox-product-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_review_style',
			[
				'label' => esc_html__( 'Review', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'star_color',
				[
					'label' 	=> esc_html__( 'Star Color', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-list .entox_product .entox_foot_product .entox-product-review .star-rating:before' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'rating_color',
				[
					'label' 	=> esc_html__( 'Rating Color', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-list .entox_product .entox_foot_product .entox-product-review .star-rating' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'count_color',
				[
					'label' 	=> esc_html__( 'Count Color', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-list .entox_product .entox_foot_product .entox-product-review .count' => 'color: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_features_style',
			[
				'label' => esc_html__( 'Features', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'feature_typography',
					'selector' 	=> '{{WRAPPER}} .ova-product-list .entox_product .entox_foot_product .entox-product-features .feature-list .label',
				]
			);

			$this->add_control(
				'feature_background',
				[
					'label' 	=> esc_html__( 'Background', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-list .entox_product .entox_foot_product .entox-product-features .feature-list .label' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'feature_color',
				[
					'label' 	=> esc_html__( 'Color', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-list .entox_product .entox_foot_product .entox-product-features .feature-list .label' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'feature_padding',
				[
					'label' 		=> esc_html__( 'Padding', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-product-list .entox_product .entox_foot_product .entox-product-features .feature-list .label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'feature_border',
					'label' 	=> esc_html__( 'Border', 'entox' ),
					'selector' 	=> '{{WRAPPER}} .ova-product-list .entox_product .entox_foot_product .entox-product-features .feature-list .label',
					'separator' => 'before',
				]
			);

			$this->add_control(
				'first_item_options',
				[
					'label' => esc_html__( 'First Item Options', 'entox' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'first_item_border_radius',
				[
					'label' 		=> esc_html__( 'Border Radius', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-product-list .entox_product .entox_foot_product .entox-product-features .feature-list .label:first-child' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'last_item_options',
				[
					'label' => esc_html__( 'Last Item Options', 'entox' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'last_item_border_radius',
				[
					'label' 		=> esc_html__( 'Border Radius', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-product-list .entox_product .entox_foot_product .entox-product-features .feature-list .label:last-child' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_favourite_style',
			[
				'label' => esc_html__( 'Favourite', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'favourite_typography',
					'selector' 	=> '{{WRAPPER}} .ova-product-list .entox_product .entox_head_product .entox-product-favourite',
				]
			);

			$this->add_responsive_control(
				'favourite_size',
				[
					'label' 		=> esc_html__( 'Width', 'entox' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'default' => [
						'size' => 44,
					],
					'selectors' => [
						'{{WRAPPER}} .ova-product-list .entox_product .entox_head_product .entox-product-favourite' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->start_controls_tabs( 'favourite_tabs' );

				$this->start_controls_tab(
					'favourite_normal_tab',
					[
						'label' => esc_html__( 'Normal', 'entox' ),
					]
				);

					$this->add_control(
						'favourite_normal_background',
						[
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-product-list .entox_product .entox_head_product .entox-product-favourite' => 'background-color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'favourite_normal_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-product-list .entox_product .entox_head_product .entox-product-favourite i' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_responsive_control(
						'favourite_normal_opacity',
						[
							'label' 		=> esc_html__( 'Opacity', 'entox' ),
							'type' 			=> Controls_Manager::SLIDER,
							'size_units' 	=> [ 'px' ],
							'range' => [
								'px' => [
									'min' 	=> 0,
									'max' 	=> 1,
									'step' 	=> 0.1
								],
							],
							'default' => [
								'size' => 0.3,
							],
							'selectors' => [
								'{{WRAPPER}} .ova-product-list .entox_product .entox_head_product .entox-product-favourite' => 'opacity: {{SIZE}};',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'favourite_hover_tab',
					[
						'label' => esc_html__( 'Hover', 'entox' ),
					]
				);

					$this->add_control(
						'favourite_hover_background',
						[
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-product-list .entox_product:hover .entox_head_product .entox-product-favourite' => 'background-color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'favourite_hover_color',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-product-list .entox_product:hover .entox_head_product .entox-product-favourite i' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_responsive_control(
						'favourite_hover_opacity',
						[
							'label' 		=> esc_html__( 'Opacity', 'entox' ),
							'type' 			=> Controls_Manager::SLIDER,
							'size_units' 	=> [ 'px' ],
							'range' => [
								'px' => [
									'min' 	=> 0,
									'max' 	=> 1,
									'step' 	=> 0.1
								],
							],
							'default' => [
								'size' => 1,
							],
							'selectors' => [
								'{{WRAPPER}} .ova-product-list .entox_product:hover .entox_head_product .entox-product-favourite' => 'opacity: {{SIZE}};',
							],
						]
					);

				$this->end_controls_tab();
			$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function entox_get_product_list( $args ) {

		$args_query = [
			'post_type' 		=> 'product',
		    'post_status' 		=> 'publish',
		    'posts_per_page' 	=> $args['posts_per_page'],
		    'orderby' 			=> $args['orderby'],
		    'order'				=> $args['order'],
		    'fields' 			=> 'ids',
		    'tax_query' 		=> [],
		];

		if ( 'yes' === $args['show_featured'] ) {
	        $featured = [
	        	'taxonomy' => 'product_visibility',
                'field'    => 'name',
                'terms'    => 'featured',
                'operator' => 'IN'
	        ];

	        array_push( $args_query['tax_query'], $featured );
	    }

		if ( 'all' != $args['category_slug'] ) {
			$category_args = [
				'taxonomy' 	=> 'product_cat',
            	'field' 	=> 'slug',
            	'terms'     => $args['category_slug'],
            	'operator'  => 'IN',
			];
			array_push( $args_query['tax_query'], $category_args );
		}

		if ( $args['category_in'] ) {
			$category_in = [
				[
					'taxonomy' 	=> 'product_cat',
					'field'    	=> 'term_id',
					'terms'    	=> explode( '|', $args['category_in'] ),
					'operator'  => 'IN',
				],
			];
			array_push( $args_query['tax_query'], $category_in );
		}

		if ( $args['category_not_in'] ) {
			$category_not_in = [
				[
					'taxonomy' 	=> 'product_cat',
					'field'    	=> 'term_id',
					'terms'    	=> explode( '|', $args['category_not_in'] ),
					'operator' 	=> 'NOT IN',
				],
			];
			array_push( $args_query['tax_query'], $category_not_in );
		}

		$result  = new \WP_Query( $args_query );

		return $result;
	}

	protected function render() {
		$settings 	= $this->get_settings();
		$column 	= $settings['columns'];

		$args = [
			'posts_per_page' 	=> $settings['posts_per_page'],
			'orderby' 			=> $settings['orderby'],
			'order' 			=> $settings['order'],
			'category_slug'		=> $settings['categories'],
			'category_in' 		=> $settings['category_in'],
			'category_not_in' 	=> $settings['category_not_in'],
			'show_featured'		=> $settings['show_featured']
		];
		
		$products = $this->entox_get_product_list( $args );

		?>

		<?php if ( $products->have_posts() ): ?>
			<div class="ova-product-list <?php echo esc_attr( $column ); ?>">
				<?php while( $products->have_posts() ) : $products->the_post(); ?>
					<?php wc_get_template_part( 'content', 'product' ); ?>
				<?php endwhile; ?>
			</div>
		<?php else: ?>
			<div class="ova-no-products-found">
				<?php echo esc_html( 'No products found', 'entox' ); ?>
			</div>
		<?php endif; wp_reset_postdata(); ?>

		<?php

	}
}

$widgets_manager->register( new Entox_Elementor_Product_List() );


