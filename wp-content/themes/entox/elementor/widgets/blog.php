<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Entox_Elementor_Blog extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_blog';
	}

	public function get_title() {
		return esc_html__( 'Blog', 'entox' );
	}

	public function get_icon() {
		return 'eicon-posts-ticker';
	}

	public function get_categories() {
		return [ 'entox' ];
	}


	protected function register_controls() {

		$args = array(
		  'orderby' => 'name',
		  'order' => 'ASC'
		  );

		$categories=get_categories($args);
		$cate_array = array();
		$arrayCateAll = array( 'all' => 'All categories ' );
		if ($categories) {
			foreach ( $categories as $cate ) {
				$cate_array[$cate->cat_name] = $cate->slug;
			}
		} else {
			$cate_array["No content Category found"] = 0;
		}



		//SECTION CONTENT
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'entox' ),
			]
		);

			$this->add_control(
				'category',
				[
					'label' => esc_html__( 'Category', 'entox' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'all',
					'options' => array_merge($arrayCateAll,$cate_array),
				]
			);

			$this->add_control(
				'total_count',
				[
					'label' => esc_html__( 'Post Total', 'entox' ),
					'type' => Controls_Manager::NUMBER,
					'default' => 3,
				]
			);

			$this->add_control(
				'number_column',
				[
					'label' => esc_html__( 'Number Of Columns', 'entox' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'grid_sidebar',
					'options' => [
						'grid_sidebar' => esc_html__( '2 Columns', 'entox' ),
						'grid_medium' => esc_html__( '3 Columns', 'entox' ),
						'grid_small' => esc_html__( '4 Columns', 'entox' ),

						
					]
				]
			);


			$this->add_control(
				'order_by',
				[
					'label' => esc_html__('Order', 'entox'),
					'type' => Controls_Manager::SELECT,
					'default' => 'desc',
					'options' => [
						'asc' => esc_html__('Ascending', 'entox'),
						'desc' => esc_html__('Descending', 'entox'),
					]
				]
			);

		
	

	

		

		$this->end_controls_section();
		//END SECTION CONTENT


		$this->start_controls_section(
			'section_meta',
			[
				'label' => esc_html__( 'Meta', 'entox' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

	

		$this->add_control(
			'color_meta',
			[
				'label' => esc_html__( 'Color', 'entox' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} article.post-wrap.type-grid .post-date .top ' => 'color : {{VALUE}};',
					'{{WRAPPER}} article.post-wrap.type-grid .post-date .bottom ' => 'color : {{VALUE}};',
				],
			]
		);

			$this->add_control(
			'back_color_meta',
			[
				'label' => esc_html__( 'Background Color', 'entox' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} article.post-wrap.type-grid .post-date' => 'background-color: {{VALUE}}; border-color : {{VALUE}};',
				],
			]
		);


	

		$this->end_controls_section();


		//SECTION TAB STYLE TITLE
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
				'selector' => '{{WRAPPER}} .ova-blog-element .ova-content .post-title a',
			]
		);

		$this->add_control(
			'color_title',
			[
				'label' => esc_html__( 'Color Title', 'entox' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-blog-element .ova-content .post-title a' => 'color : {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'color_title_hover',
			[
				'label' => esc_html__( 'Color Title Hover', 'entox' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ova-blog-element .ova-content .post-title a:hover' => 'color : {{VALUE}};',
				],
			]
		);

	

		$this->end_controls_section();
		//END SECTION TAB STYLE TITLE


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
		
		$category = $settings['category'];
		$total_count = $settings['total_count'];
		$order = $settings['order_by'];
		$number_column = $settings['number_column'];


		$args = [];
		if ($category == 'all') {
			$args=[
				'post_type' => 'post',
				'posts_per_page' => $total_count,
				'order' => $order,
			];
		} else {
			$args=[
				'post_type' => 'post', 
				'category_name'=>$category,
				'posts_per_page' => $total_count,
				'order' => $order,
			];
		}

		$blog = new \WP_Query($args);

		?>
		<div class="ova-blog-element">
			<div class="ova-blog grid <?php echo esc_attr( $number_column ) ?>">

				<?php
				if($blog->have_posts()) : while($blog->have_posts()) : $blog->the_post();
					?>

					<article class="post-wrap type-grid">
						<div class="wrap-article">
							<?php if(has_post_thumbnail()){ ?>

								<div class="post-media">
									<?php
									if ( has_post_thumbnail()  && ! post_password_required() || has_post_format( 'image') )  :
										the_post_thumbnail( apply_filters( 'entox_blog_thumbnail_size','full' ), array('class'=> 'img-responsive' ));
								endif;
								?>

									<div class=" post-date">
										<span class="top"><?php the_time( 'j')?></span>
										<span class="bottom"><?php the_time( 'M')?></span>
									</div>
								</div>

							<?php } ?>
							<div class="ova-content">

								<span class=" post-meta">

									<?php if(has_category( )) { ?>
										<span class="wp-categories">
											<i class="ovaicon-folder-1"></i>
											<span class=" categories">
												<span class="right"><?php the_category('&sbquo;&nbsp;'); ?></span><!-- end right -->             
											</span><!-- end categories -->
										</span><!-- end wp-category -->
									<?php } ?>


									<span class="comment">
										<i class="ovaicon-speech-bubble-1"></i>
										<a href="<?php the_permalink();?>">
											<?php comments_popup_link(
												esc_html__(' 0 comments', 'entox'), 
												esc_html__(' 1 comment', 'entox'), 
												' % '.esc_html__('comments', 'entox'),
												'',
												esc_html__( 'comment off', 'entox' )
											); ?>
										</a>
									</span>
								</span>


								<h2 class="post-title">
									<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
										<?php the_title(); ?>
									</a>
								</h2>


								<div class="author">
									<?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?>
									<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
										<?php the_author_meta( 'display_name' ); ?>
									</a>
								</div>

							</div>
						</div>

					</article>

					<?php
				endwhile; endif; wp_reset_postdata();
				?>
			</div>
		</div>
		
		<?php
	}
}

$widgets_manager->register( new Entox_Elementor_Blog() );
