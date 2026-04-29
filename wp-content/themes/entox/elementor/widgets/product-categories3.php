<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Entox_Elementor_Product_Categories3 extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_product_categories3';
	}

	public function get_title() {
		return esc_html__( 'Product Categories 3', 'entox' );
	}

	public function get_icon() {
		return 'eicon-product-categories';
	}

	public function get_categories() {
		return [ 'entox' ];
	}

	protected function register_controls() {
		
		$this->start_controls_section(
			'section_product_categories',
			[
				'label' => esc_html__( 'Content', 'entox' ),
			]
		);

			$this->add_control(
				'column',
				[
					'label'   => esc_html__( 'Column', 'entox' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'column3',
					'options' => [
						'column1' => esc_html__( 'Column 1', 'entox' ),
						'column2' => esc_html__( 'Column 2', 'entox' ),
						'column3' => esc_html__( 'Column 3', 'entox' ),
						'column4' => esc_html__( 'Column 4', 'entox' ),
						'column5' => esc_html__( 'Column 5', 'entox' ),
					],
				]
			);

			$this->add_control(
				'total_categories',
				[
					'label'   => esc_html__( 'Total Categories', 'entox' ),
					'type'    => Controls_Manager::NUMBER,
					'min'     => 0,
					'default' => 3
				]
			);

			$this->add_control(
				'order',
				[
					'label'   => esc_html__( 'Order', 'entox' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'DESC',
					'options' => [
						'DESC' => esc_html__( 'Descending', 'entox' ),
						'ASC'  => esc_html__( 'Ascending', 'entox' ),
					],
				]
			);

			$this->add_control(
				'order_by',
				[
					'label'   => esc_html__( 'Category Order By', 'entox' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'name',
					'options' => [
						'name' 	=> esc_html__( 'Name', 'entox' ),
						'order' => esc_html__( 'Order', 'entox' ),
					],
				]
			);

			$this->add_control(
				'category_in',
				[
					'label'   		=> esc_html__( 'Category In', 'entox' ),
					'type'    		=> Controls_Manager::TEXT,
					'description' 	=> esc_html__( 'Enter the category ID of Case Study. IDs are separated by "|". Ex: 1|2|3.', 'entox' ),
				]
			);

			$this->add_control(
				'category_not_in',
				[
					'label'   		=> esc_html__( 'Category Not In', 'entox' ),
					'type'    		=> Controls_Manager::TEXT,
					'description' 	=> esc_html__( 'Enter the category ID of Case Study. IDs are separated by "|". Ex: 1|2|3.', 'entox' ),
				]
			);

			$this->add_control(
				'search_result',
				[
					'label' 	=> esc_html__( 'Search Result', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'category',
					'options' 	=> [
						'category' 	=> esc_html__( 'Category Page', 'entox' ),
						'search' 	=> esc_html__( 'Search Page', 'entox' ),
						'custom' 	=> esc_html__( 'Custom Page', 'entox' ),
					],
				]
			);

			$this->add_control(
				'url_search_result',
				[
					'label' 		=> esc_html__( 'URL Seach Result', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::URL,
					'placeholder' 	=> esc_html__( 'https://your-link.com', 'entox' ),
					'default' 		=> [
						'url' => '#',
					],
					'condition' 	=> [
						'search_result' => 'search',
					],
				]
			);

			$this->add_control(
				'url_custom_result',
				[
					'label' 		=> esc_html__( 'URL Custom Result', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::URL,
					'placeholder' 	=> esc_html__( 'https://your-link.com', 'entox' ),
					'default' 		=> [
						'url' => '#',
					],
					'condition' 	=> [
						'search_result' => 'custom',
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
			'section_product_categories_style',
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
						'{{WRAPPER}} .ova-product-categories3 .items' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],
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

			$this->add_group_control(
				\Elementor\Group_Control_Background::get_type(),
				[
					'name' => 'items_background',
					'label' => esc_html__( 'Background', 'entox' ),
					'types' => [ 'gradient', ],
					'selector' => '{{WRAPPER}} .ova-product-categories3 .items a:before',
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'items_border',
					'label' 	=> esc_html__( 'Border', 'entox' ),
					'selector' 	=> '{{WRAPPER}} .ova-product-categories3 .items a',
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
						'{{WRAPPER}} .ova-product-categories3 .items a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .ova-product-categories3 .items a:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'items_height',
				[
					'label' 		=> esc_html__( 'Height', 'entox' ),
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
						'size' => 365,
					],
					'selectors' => [
						'{{WRAPPER}} .ova-product-categories3 .items a' => 'height: {{SIZE}}{{UNIT}};',
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
					'selector' 	=> '{{WRAPPER}} .ova-product-categories3 .items a .title',
				]
			);

			$this->add_control(
				'title_color',
				[
					'label' 	=> esc_html__( 'Color', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-categories3 .items a .title' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'title_color_hover',
				[
					'label' 	=> esc_html__( 'Hover Color', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-categories3 .items a:hover .title' => 'color: {{VALUE}}',
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
						'{{WRAPPER}} .ova-product-categories3 .items a .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
						'{{WRAPPER}} .ova-product-categories3 .items a .title' => 'left: {{SIZE}}{{UNIT}};',
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
						'{{WRAPPER}} .ova-product-categories3 .items a .title' => 'bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

	}

	/**
	 * Get product categories
	 */
	protected function entox_get_product_categories( $args ) {

		$args_query	= [
			'taxonomy' 	=> 'product_cat',
        	'order'   	=> $args['order'],
        	'number' 	=> $args['total_categories']
		];

		if ( $args['order_by'] && 'order' == $args['order_by'] ) {
			$args_query['orderby'] 		= 'meta_value_num';
			$args_query['meta_type'] 	= 'NUMERIC';
			$args_query['meta_key'] 	= 'order';
		} else {
			$args_query['orderby'] 	= 'name';
		}

		if ( $args['category_in'] ) {
			$args_query['include'] = explode( '|', $args['category_in'] );
		}

		if ( $args['category_not_in'] ) {
			$args_query['exclude'] = explode( '|', $args['category_not_in'] );
		}

		$categories = get_categories( $args_query );
		$result 	= [];

		if ( $categories ) {
			foreach ( $categories as $category ) {
				$args_category = [
					'term_id' 	=> $category->term_id,
					'slug' 		=> $category->slug,
					'name' 		=> $category->cat_name,
					'count' 	=> $category->category_count
				];
				array_push( $result, $args_category );
			}
		}

		return $result;
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

		$args = [
			'order' 			=> $settings['order'],
			'order_by' 			=> $settings['order_by'],
			'category_in' 		=> $settings['category_in'],
			'category_not_in' 	=> $settings['category_not_in'],
			'total_categories' 	=> $settings['total_categories']
		];

		$categories 	= $this->entox_get_product_categories( $args );
		$target 		= $settings['target'] ? ' target="_blank"' : '';
		$search_result 	= $settings['search_result'];

		?>

		<?php if ( $categories && is_array( $categories ) ): ?>
			<div class="ova-product-categories3">
				<div class="items <?php echo esc_attr( $settings['column'] ); ?>">
					<?php foreach( $categories as $category ):
						$thumbnail_id 	= get_term_meta( $category['term_id'], 'thumbnail_id', true );
						$image_url 		= wp_get_attachment_url( $thumbnail_id );
						$alt 			= get_post_meta ( $thumbnail_id, '_wp_attachment_image_alt', true );

						if ( ! $image_url ) {
							$image_url = \Elementor\Utils::get_placeholder_image_src();
						}

						$term_link = get_term_link( $category['term_id'] );
						
						if ( 'search' === $search_result ) {
							$url_result = $settings['url_search_result']['url'];
							$term_link 	= add_query_arg( array( 'cat' => $category['slug'] ), $url_result );
						}

						if ( 'custom' === $search_result ) {
							$term_link = $settings['url_custom_result']['url'];
						}
					?>
						<a href="<?php echo esc_url( $term_link ); ?>"<?php printf( $target ); ?>>
							<div class="item">
								<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
								<h2 class="title">
									<?php echo esc_html( $category['name'] ); ?>
								</h2>
							</div>
						</a>
					<?php endforeach; ?>
				</div>
			</div>	
		<?php endif; ?>
		
		<?php
	}
}

$widgets_manager->register( new Entox_Elementor_Product_Categories3() );
