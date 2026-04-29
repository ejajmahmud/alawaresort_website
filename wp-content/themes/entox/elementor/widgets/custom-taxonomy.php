<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Entox_Elementor_Custom_Taxonomy extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_custom_taxonomy';
	}

	public function get_title() {
		return esc_html__( 'Custom Taxonomy', 'entox' );
	}

	public function get_icon() {
		return 'eicon-product-categories';
	}

	public function get_categories() {
		return [ 'entox' ];
	}

	protected function register_controls() {
		
		$this->start_controls_section(
			'section_custom_taxonomy',
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
			$category_taxonomy 	= array();
			$args_category 		= array();
			$result 			= array();

			if ( $categories && is_array( $categories ) ) {
				foreach ( $categories as $category ) {
					$args_category[$category->slug] = $category->cat_name;

					$ovabrw_custom_tax = get_term_meta( $category->term_id, 'ovabrw_custom_tax', true );

					if ( $ovabrw_custom_tax ) {
						$category_taxonomy[$category->slug] = $ovabrw_custom_tax;
					}
				}
			}

			$result = array_merge( array( 'all' => esc_html__( 'All', 'entox' ) ), $args_category );

			$this->add_control(
				'categories',
				[
					'label' 	=> esc_html__( 'Select Category', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'all',
					'options' 	=> $result,
				]
			);

			$terms_tax_value = array();

			$all_custom_taxonomy 	= get_option( 'ovabrw_custom_taxonomy', array() );

			if ( $category_taxonomy ) {
				foreach( $category_taxonomy as $term_slug => $custom_taxonomy ) {

					$taxonomy = array(
						'' => esc_html__( 'Select Taxonomy', 'entox' ),
					);

					$terms_value = array();

					if ( $custom_taxonomy ) {
						foreach ( $custom_taxonomy as $value ) {
							$terms = get_terms( array(
							    'taxonomy' => $value,
							));

							if ( $terms && is_array( $terms ) && isset($all_custom_taxonomy[$value]['name']) ) {
								$taxonomy[$value] = $all_custom_taxonomy[$value]['name'];
								$terms_value[$value] = array(
									'name' 		=> $all_custom_taxonomy[$value]['name'],
									'value' 	=> array()
								);

								foreach ( $terms as $term ) {
									if ( is_object( $term ) ) {
										$terms_value[$value]['value'][$term->slug] = $term->name;
									}
								}
							}
						}
					}

					if ( $terms_value ) {
						$terms_tax_value[$term_slug] = $terms_value;
					}

					$this->add_control(
						'taxonomy_'.esc_html( $term_slug ),
						[
							'label' 	=> esc_html__( 'Select Taxonomy', 'entox' ),
							'type' 		=> \Elementor\Controls_Manager::SELECT,
							'default' 	=> '',
							'options' 	=> $taxonomy,
							'condition' => [
								'categories' => $term_slug,
							]
						]
					);
				}
			}

			if ( $terms_tax_value && is_array( $terms_tax_value ) ) {
				foreach ( $terms_tax_value as $t => $args_tax ) {
					if ( $args_tax ) {
						foreach( $args_tax as $tax_slug => $tax_value ) {
							$terms_default 	= array( '' => esc_html( 'Select ', 'entox' ) . esc_html( $tax_value['name'] ) );
							$terms_result 	= array_merge( $terms_default, $tax_value['value'] );
							$this->add_control(
								'taxonomy_value_'.esc_html( $t ).'_'.esc_html( $tax_slug ),
								[
									'label' 	=> esc_html__( 'Select ', 'entox' ). esc_html( $tax_value['name'] ),
									'type' 		=> \Elementor\Controls_Manager::SELECT,
									'default' 	=> '',
									'options' 	=> $terms_result,
									'condition' => [
										'categories' 				=> $t,
										'taxonomy_'.esc_html( $t ) 	=> $tax_slug,
									],
								]
							);
						}
					}
				}
			}

			$this->add_control(
				'label',
				[
					'label' 	=> esc_html__( 'Label', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::TEXT,
					'default' 	=> esc_html__( ' Properties', 'entox' ),
					'condition' => [
						'taxonomy!' => '',
					],
				]
			);

			$this->add_control(
				'title',
				[
					'label' 	=> esc_html__( 'Title', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::TEXT,
					'default' 	=> esc_html__( 'New York', 'entox' ),
				]
			);

			$this->add_control(
				'image',
				[
					'label' 	=> esc_html__( 'Choose Image', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::MEDIA,
					'default' 	=> [
						'url' => \Elementor\Utils::get_placeholder_image_src(),
					],
				]
			);

			$this->add_control(
				'search_result',
				[
					'label' => esc_html__( 'Link', 'entox' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'search_page',
					'options' => [
						'search_page' 	=> esc_html__( 'Search Page', 'entox' ),
						'custom_link' 	=> esc_html__( 'Custom Link', 'entox' ),
					],
				]
			);

			$this->add_control(
				'search_page',
				[
					'label' 		=> esc_html__( 'URL Seach Result', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::URL,
					'placeholder' 	=> esc_html__( 'https://your-link.com', 'entox' ),
					'default' 		=> [
						'url' => '#',
					],
					'condition' 	=> [
						'search_result' => 'search_page',
					],
				]
			);

			$this->add_control(
				'custom_link',
				[
					'label' 		=> esc_html__( 'Custom Link', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::URL,
					'placeholder' 	=> esc_html__( 'https://your-link.com', 'entox' ),
					'default' 		=> [
						'url' => '#',
					],
					'condition' 	=> [
						'search_result' => 'custom_link',
					],
				]
			);

			$this->add_control(
				'target',
				[
					'label' 	=> esc_html__( 'Open in new window', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 	=> esc_html__( 'Yes', 'entox' ),
					'label_off' => esc_html__( 'No', 'entox' ),
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_items_style',
			[
				'label' => esc_html__( 'Items', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'items_background',
				[
					'label' 	=> esc_html__( 'Background', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-custom-taxonomy' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'items_border',
					'label' 	=> esc_html__( 'Border', 'entox' ),
					'selector' 	=> '{{WRAPPER}} .ova-custom-taxonomy',
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'items_border_radius',
				[
					'label' 		=> esc_html__( 'Border Radius', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-custom-taxonomy' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'items_width',
				[
					'label' 		=> esc_html__( 'Width', 'entox' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px', '%', 'vw' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1000,
							'step' => 5,
						],
						'%' => [
							'min' 	=> 0,
							'max' 	=> 100,
							'step' 	=> 1,
						],
						'vw' => [
							'min' 	=> 0,
							'max' 	=> 100,
							'step' 	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-custom-taxonomy' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'items_height',
				[
					'label' 		=> esc_html__( 'Height', 'entox' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px', '%','vh' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1000,
							'step' => 5,
						],
						'%' => [
							'min' 	=> 0,
							'max' 	=> 100,
							'step' 	=> 1,
						],
						'vh' => [
							'min' 	=> 0,
							'max' 	=> 100,
							'step' 	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova-custom-taxonomy' => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_style',
			[
				'label' => esc_html__( 'Image', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'image_opacity',
				[
					'label' 		=> esc_html__( 'Opacity', 'entox' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px' ],
					'range' => [
						'px' => [
							'min' 	=> 0,
							'max' 	=> 1,
							'step' 	=> 0.1,
						],
					],
					'default' => [
						'size' => 0.6,
					],
					'selectors' => [
						'{{WRAPPER}} .ova-custom-taxonomy .taxonomy-item img' => 'opacity: {{SIZE}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_count_style',
			[
				'label' => esc_html__( 'Count', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'count_typography',
					'selector' 	=> '{{WRAPPER}} .ova-custom-taxonomy .taxonomy-item .count-product',
				]
			);

			$this->start_controls_tabs(
				'style_count_tabs',
			);

				$this->start_controls_tab(
					'style_count_normal_tab',
					[
						'label' => esc_html__( 'Normal', 'entox' ),
					]
				);

					$this->add_control(
						'count_color_normal',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-custom-taxonomy .taxonomy-item .count-product' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'count_background_normal',
						[
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-custom-taxonomy .taxonomy-item .count-product' => 'background-color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'style_count_hover_tab',
					[
						'label' => esc_html__( 'Hover', 'entox' ),
					]
				);

					$this->add_control(
						'count_color_hover',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-custom-taxonomy:hover .taxonomy-item .count-product' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'count_background_hover',
						[
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-custom-taxonomy:hover .taxonomy-item .count-product' => 'background-color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();
			$this->end_controls_tabs();

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'count_border',
					'label' 	=> esc_html__( 'Border', 'entox' ),
					'selector' 	=> '{{WRAPPER}} .ova-custom-taxonomy .taxonomy-item .count-product',
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'count_border_radius',
				[
					'label' 		=> esc_html__( 'Border Radius', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-custom-taxonomy .taxonomy-item .count-product' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'count_padding',
				[
					'label' 		=> esc_html__( 'Padding', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-custom-taxonomy .taxonomy-item .count-product' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'count_top',
				[
					'label' 		=> esc_html__( 'Top', 'entox' ),
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
						'unit' => 'px',
						'size' => 30,
					],
					'selectors' => [
						'{{WRAPPER}} .ova-custom-taxonomy .taxonomy-item .count-product' => 'top: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'count_right',
				[
					'label' 		=> esc_html__( 'Right', 'entox' ),
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
						'unit' => 'px',
						'size' => 30,
					],
					'selectors' => [
						'{{WRAPPER}} .ova-custom-taxonomy .taxonomy-item .count-product' => 'right: {{SIZE}}{{UNIT}};',
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
					'selector' 	=> '{{WRAPPER}} .ova-custom-taxonomy .taxonomy-item .title',
				]
			);

			$this->add_control(
				'title_color',
				[
					'label' 	=> esc_html__( 'Color', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-custom-taxonomy .taxonomy-item .title' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'title_color_hover',
				[
					'label' 	=> esc_html__( 'Hover Color', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-custom-taxonomy:hover .taxonomy-item .title' => 'color: {{VALUE}}',
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
						'{{WRAPPER}} .ova-custom-taxonomy .taxonomy-item .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'title_left',
				[
					'label' 		=> esc_html__( 'Left', 'entox' ),
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
					'selectors' => [
						'{{WRAPPER}} .ova-custom-taxonomy .taxonomy-item .title' => 'left: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'title_bottom',
				[
					'label' 		=> esc_html__( 'Bottom', 'entox' ),
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
					'selectors' => [
						'{{WRAPPER}} .ova-custom-taxonomy .taxonomy-item .title' => 'bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

	}

	protected function get_count_product_by_taxonomy( $agrs ) {
		$count = 0;

		$args_query = [
			'post_type' 		=> 'product',
			'posts_per_page' 	=> -1,
			'fields' 			=> 'ids'
		];

		$args_tax_attr = array();

		if ( $agrs['category'] && 'all' != $agrs['category'] ){
			$args_tax_attr[] = [
	            'taxonomy' 	=> 'product_cat',
	            'field' 	=> 'slug',
	            'terms' 	=> $agrs['category']
	        ];
		}

		if ( $agrs['taxonomy'] && $agrs['taxonomy_slug'] ) {
			$args_tax_attr[] = [
	            'taxonomy' 	=> $agrs['taxonomy'],
	            'field' 	=> 'slug',
	            'terms' 	=> $agrs['taxonomy_slug']
	        ];
		}

		if ( ! empty( $args_tax_attr ) ) {
			$args_query['tax_query'] = array(
                'relation'  => 'AND',
                $args_tax_attr
            );
		}

		$result = new \WP_Query( $args_query );
		$count 	= $result->found_posts;

		return $count;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$label 		= $settings['label'];
		$image_url 	= $settings['image']['url'];
		$image_alt 	= isset( $settings['image']['alt'] ) ? $settings['image']['alt'] : '';
		$title 		= $settings['title'];
		$categories = $settings['categories'];

		$args = array(
			'category' 		=> $categories,
			'taxonomy'  	=> '',
			'taxonomy_slug' => ''
		);

		if ( 'all' != $categories ) {
			$taxonomy = isset($settings['taxonomy_'.esc_html( $categories )]) ? $settings['taxonomy_'.esc_html( $categories )] : '';
			
			if ( $taxonomy ) {
				$args['taxonomy'] = $taxonomy;

				$taxonomy_slug = isset($settings['taxonomy_value_'.esc_html( $categories ).'_'.$taxonomy]) ? $settings['taxonomy_value_'.esc_html( $categories ).'_'.$taxonomy] : '';

				if ( $taxonomy_slug ) {
					$args['taxonomy_slug'] = $taxonomy_slug;
				}
			}
		}

		$count_product = $this->get_count_product_by_taxonomy( $args );

		// Search Result
		$search_result = $settings['search_result'];

		$link = '#';
		if ( 'search_page' === $search_result ) {
			$link = $settings['search_page']['url'];

			if ( 'all' != $args['category'] ) {
				$link = add_query_arg( array( 'cat' => trim($categories) ), $settings['search_page']['url'] );
			}

			if ( $args['taxonomy'] && $args['taxonomy_slug'] ) {
				$link = add_query_arg( array( 'cat' => trim($categories), $args['taxonomy'].'_name' => $args['taxonomy_slug'] ), $settings['search_page']['url'] );
			}
		} else {
			$link = $settings['custom_link']['url'];
		}

		$target = $settings['target'] ? ' target="_blank"' : '';

		?>
		<div class="ova-custom-taxonomy">
			<a href="<?php echo esc_url( $link ); ?>"<?php printf( $target ); ?> class="taxonomy-item">
				<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
				<?php if ( $count_product ): ?>
					<div class="count-product">
						<span class="count"><?php echo absint( $count_product ); ?></span>
						<span class="label"><?php echo esc_html( $label ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( $title ): ?>
					<h2 class="title">
						<?php echo esc_html( $title ); ?>
					</h2>
				<?php endif; ?>
			</a>

		</div>
		<?php
	}
}

$widgets_manager->register( new Entox_Elementor_Custom_Taxonomy() );
