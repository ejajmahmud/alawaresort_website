<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Entox_Elementor_Featured_Products extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_featured_products';
	}

	public function get_title() {
		return esc_html__( 'Featured Products', 'entox' );
	}

	public function get_icon() {
		return 'eicon-products';
	}

	public function get_categories() {
		return [ 'entox' ];
	}


	protected function register_controls() {


		$this->start_controls_section(
			'section_featured_content',
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
				'posts_per_page',
				[
					'label' 	=> esc_html__( 'Posts Per Page', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::NUMBER,
					'default' 	=> 5,
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

			$this->add_control(
				'featured_product_id',
				[
					'label'   		=> esc_html__( 'First Product ID', 'entox' ),
					'type'    		=> Controls_Manager::TEXT,
					'description' 	=> esc_html__( 'First product id. Ex: 123', 'entox' ),
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
			'section_featured_style',
			[
				'label' => esc_html__( 'Content', 'entox' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'content_grap',
				[
					'label' 		=> esc_html__( 'Grap', 'entox' ),
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
						'size' => 30,
					],
					'selectors' => [
						'{{WRAPPER}} .ova-featured-product' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_product_title_style',
			[
				'label' => esc_html__( 'Product Title', 'entox' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'product_title_typography',
					'selector' 	=> '{{WRAPPER}} .ova-featured-product .product-content.featured-before .product-meta .product-title a',
				]
			);

			$this->start_controls_tabs(
				'style_product_title_tabs',
			);

				$this->start_controls_tab(
					'style_product_title_normal_tab',
					[
						'label' => esc_html__( 'Normal', 'entox' ),
					]
				);

					$this->add_control(
						'product_title_color_normal',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-featured-product .product-content .product-meta .product-title a' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Background::get_type(),
						[
							'name' 		=> 'product_title_background',
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'types' 	=> ['gradient' ],
							'selector' 	=> '{{WRAPPER}} .ova-featured-product .product-content.featured-before .product-image:before',
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'style_product_title_hover_tab',
					[
						'label' => esc_html__( 'Hover', 'entox' ),
					]
				);

					$this->add_control(
						'product_title_color_hover',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-featured-product .product-content .product-meta .product-title a:hover' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();
			$this->end_controls_tabs();

			$this->add_control(
				'first_product',
				[
					'label' 	=> esc_html__( 'The First Product', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name' 		=> 'first_product_typography',
						'selector' 	=> '{{WRAPPER}} .ova-featured-product .product-content.grid_span .product-meta .product-title a',
					]
				);

				$this->add_control(
					'first_product_background',
					[
						'label' 	=> esc_html__( 'Background', 'entox' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ova-featured-product .product-content.grid_span .product-meta' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_responsive_control(
					'first_product_margin',
					[
						'label' 		=> esc_html__( 'Margin', 'entox' ),
						'type' 			=> Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ova-featured-product .product-content.grid_span .product-meta .product-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'product_border',
					'label' 	=> esc_html__( 'Border', 'entox' ),
					'selector' 	=> '{{WRAPPER}} .ova-featured-product .product-content',
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'product_border_radius',
				[
					'label' 		=> esc_html__( 'Border Radius', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-featured-product .product-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'product_padding',
				[
					'label' 		=> esc_html__( 'Padding', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-featured-product .product-content .product-meta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'product_margin',
				[
					'label' 		=> esc_html__( 'Margin', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-featured-product .product-content .product-meta .product-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_product_price_style',
			[
				'label' => esc_html__( 'Product Price', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'product_price_typography',
					'selector' 	=> '{{WRAPPER}} .ova-featured-product .product-content .product-meta .product-price .amount',
				]
			);

			$this->add_control(
				'product_price_color',
				[
					'label' 	=> esc_html__( 'Color', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-featured-product .product-content .product-meta .product-price .amount' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'product_price_label',
				[
					'label' 	=> esc_html__( 'Label', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name' 		=> 'product_price_label_typography',
						'selector' 	=> '{{WRAPPER}} .ova-featured-product .product-content .product-meta .product-price',
					]
				);

				$this->add_control(
					'product_price_label_color',
					[
						'label' 	=> esc_html__( 'Color', 'entox' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ova-featured-product .product-content .product-meta .product-price' => 'color: {{VALUE}}',
						],
					]
				);

			$this->add_responsive_control(
				'product_price_margin',
				[
					'label' 		=> esc_html__( 'Margin', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-featured-product .product-content .product-meta .product-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();
	}

	protected function entox_get_featured_product( $args ) {

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

		if ( $args['category_in'] ) {
			$category_in = [
				[
					'taxonomy' 	=> 'product_cat',
	            	'field' 	=> 'term_id',
	            	'terms'     => explode( '|', $args['category_in'] ),
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

	protected function extox_get_featered_product_price( $id ) {
		$html = '';
		if ( $id ) {
			$min = $max = 0;
	        $rental_type    = get_post_meta( $id, 'ovabrw_price_type', true ) ? get_post_meta( $id, 'ovabrw_price_type', true ) : 'day' ;
	        $price_hour     = get_post_meta( $id, 'ovabrw_regul_price_hour', true );
	        $price_day      = get_post_meta( $id, '_regular_price', true );

	        // Get price
	        $petime_price   = get_post_meta( $id, 'ovabrw_petime_price', true );
	        $price_location = get_post_meta( $id, 'ovabrw_price_location', true );

	        if ( $rental_type == 'period_time' && $petime_price && is_array( $petime_price ) ) {
	            $min = min( $petime_price );
	            $max = max( $petime_price );
	        }

	        if ( $rental_type == 'transportation' && $price_location && is_array( $price_location ) ) {
	            $min = min( $price_location );
	            $max = max( $price_location );
	        }

	        if ( $rental_type == 'period_time' || $rental_type == 'transportation' ) {
	            if ( $min && $max && $min == $max ) {
	                $html .= '<span class="amount">'. wc_price( $min ) .'</span>';
	            } elseif ( $min && $max ) {
	                $html .= '<span class="amount">'. wc_price( $min ) .' - '. wc_price( $max ) .'</span>';
	            } else {
	                $html .= '';
	            }
	        } elseif ( $rental_type == 'hour' ) {
	            $html .= '<span class="amount">'. wc_price( $price_hour ) .'</span>';
	            $html .= '<span class="label">'. esc_html__(' /perhour', 'entox') . '</span>';
	        } elseif ( $rental_type == 'day' ) {
	            $html .= '<span class="amount">'. wc_price( $price_day ) .'</span>';
	            $html .= '<span class="label">'. esc_html__(' /perday', 'entox') . '</span>';
	        } elseif ( $rental_type == 'mixed' ) {
	            $html .= '<span class="amount">'. wc_price( $price_hour ) . '</span>';
	            $html .= '<span class="label">'. esc_html__(' /perhour - ', 'entox') . '</span>';
	            $html .= '<span class="amount">'. wc_price( $price_day ) . '</span>';
	            $html .= '<span class="label">'. esc_html__(' /perday', 'entox') . '</span>';
	        } else {
	            $html = '';
	        }
		}

		return apply_filters( 'extox_get_featered_product_price', $html, $id );
	}

	protected function render() {
		$settings 	= $this->get_settings();
		$target 	= $settings['target'] ? ' target="_blank"' : '';

		$args = [
			'posts_per_page' 	=> $settings['posts_per_page'],
			'orderby' 			=> $settings['orderby'],
			'order' 			=> $settings['order'],
			'category_in' 		=> $settings['category_in'],
			'category_not_in' 	=> $settings['category_not_in'],
			'show_featured' 	=> $settings['show_featured']
		];
		
		$products = $this->entox_get_featured_product( $args );
	
		?>

		<?php if ( $products->have_posts() ):
			$i = 0;
			$featured 	 = false;
			$featured_id = $settings['featured_product_id'];
			$product_ids = $products->posts;
			
			if ( $featured_id && in_array( $featured_id, $product_ids ) ) {
				$featured = true;
			}
		?>
			<div class="ova-featured-product">
				<?php while( $products->have_posts() ) : $products->the_post();
					$id 		= get_the_id();
					$title 		= get_the_title();
					$link 		= get_permalink( $id );
					$image_url 	= get_the_post_thumbnail_url( $id, 'single-post-thumbnail' );
					$image_id 	= get_post_thumbnail_id();
					$image_alt 	= '';

					if ( $image_id ) {
						$image_alt 		= get_post_meta( $image_id, '_wp_attachment_image_alt', true );
						if ( empty( $image_alt ) ) {
							$image_alt = get_the_title( $image_id );
						}
					}

					$class_grid = '';

					if ( $featured ) {
						if ( $featured_id == $id ) {
							$class_grid = ' grid_span';
						}
					} else {
						if ( 0 == $i ) {
							$class_grid = ' grid_span';
						}
					}

					$class_before = ' featured-before';
					if ( $class_grid ) {
						$class_before = '';
					}

					$html_price = $this->extox_get_featered_product_price( $id );
				?>

				<div class="product-content<?php echo esc_attr( $class_grid . $class_before ); ?>">
					<a class="product-image" href="<?php echo esc_url( $link ); ?>"<?php printf( $target ); ?>>
						<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
					</a>
					<div class="product-meta">
						<h2 class="product-title">
							<a href="<?php echo esc_url( $link ); ?>"<?php printf( $target ); ?>>
								<?php echo esc_html( $title ); ?>
							</a>
						</h2>
						<p class="product-price">
							<?php printf( $html_price ); ?>
						</p>
					</div>
				</div>

				<?php $i++; endwhile; ?>
			</div>
		<?php else: ?>
			<div class="ova-no-products-found">
				<?php echo esc_html( 'No products found', 'entox' ); ?>
			</div>
		<?php endif; wp_reset_postdata(); ?>

		<?php

	}
}

$widgets_manager->register( new Entox_Elementor_Featured_Products() );


