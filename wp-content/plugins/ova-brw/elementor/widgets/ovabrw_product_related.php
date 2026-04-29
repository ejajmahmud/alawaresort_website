<?php
namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class ovabrw_product_related extends Widget_Base {


	public function get_name() {		
		return 'ovabrw_product_related';
	}

	public function get_title() {
		return __( 'Product Related', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-product-related';
	}

	public function get_categories() {
		return [ 'ovatheme' ];
	}

	protected function register_controls() {
		
		$this->start_controls_section(
			'section_product_related_style',
			[
				'label' => __( 'Content', 'ova-brw' ),
			]
		);

			$this->add_control(
				'posts_per_page',
				[
					'label' => __( 'Products Per Page', 'ova-brw' ),
					'type' 	=> Controls_Manager::NUMBER,
					'default' => 3,
					'range' => [
						'px' => [
							'max' => 20,
						],
					],
				]
			);

			$this->add_responsive_control(
				'columns',
				[
					'label' => __( 'Columns', 'ova-brw' ),
					'type' 	=> Controls_Manager::NUMBER,
					'default' => 3,
					'min' => 1,
					'max' => 12,
				]
			);

			$this->add_control(
				'orderby',
				[
					'label' => __( 'Order By', 'ova-brw' ),
					'type' 	=> Controls_Manager::SELECT,
					'default' => 'date',
					'options' => [
						'date' 			=> __( 'Date', 'ova-brw' ),
						'title' 		=> __( 'Title', 'ova-brw' ),
						'price' 		=> __( 'Price', 'ova-brw' ),
						'popularity' 	=> __( 'Popularity', 'ova-brw' ),
						'rating' 		=> __( 'Rating', 'ova-brw' ),
						'rand' 			=> __( 'Random', 'ova-brw' ),
						'menu_order' 	=> __( 'Menu Order', 'ova-brw' ),
					],
				]
			);

			$this->add_control(
				'order',
				[
					'label' => __( 'Order', 'ova-brw' ),
					'type' 	=> Controls_Manager::SELECT,
					'default' => 'desc',
					'options' => [
						'asc' 	=> __( 'ASC', 'ova-brw' ),
						'desc' 	=> __( 'DESC', 'ova-brw' ),
					],
				]
			);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings();

		// Get Product
		$product  = wc_get_product();
		if ( empty( $product ) ) {
			?>
			<div class="ovabrw_elementor_no_product">
				<span><?php echo esc_html( $this->get_title() ); ?></span>
			</div>
			<?php
			return;
		}

		setup_postdata( $product->get_id() );

		$args = [
			'posts_per_page' => 3,
			'columns' => 3,
			'orderby' => $settings['orderby'],
			'order' => $settings['order'],
		];

		if ( ! empty( $settings['posts_per_page'] ) ) {
			$args['posts_per_page'] = $settings['posts_per_page'];
		}

		if ( ! empty( $settings['columns'] ) ) {
			$args['columns'] = $settings['columns'];
		}

		// Get visible related products then sort them at random.
		$args['related_products'] = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), $args['posts_per_page'], $product->get_upsell_ids() ) ), 'wc_products_array_filter_visible' );

		// Handle orderby.
		$args['related_products'] = wc_products_array_orderby( $args['related_products'], $args['orderby'], $args['order'] );

		?>

		<div class="elementor-ralated">
			<?php wc_get_template( 'single-product/related.php', $args ); ?>
		</div>

		<?php
	}
}