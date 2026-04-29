<?php
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class Entox_Elementor_Team extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_team';
	}

	public function get_title() {
		return esc_html__( 'Team', 'entox' );
	}

	public function get_icon() {
		return 'eicon-user-circle-o';
	}

	public function get_categories() {
		return [ 'entox' ];
	}
	public function get_keywords() {
		return [ 'social', 'icon', 'link' ];
	}

	// }
	protected function register_controls() {

		$this->start_controls_section(
			'section_settings',
			[
				'label' => esc_html__( 'Content', 'entox' ),
			]
		);

		$this->add_control(
			'image',
			[
				'label'  => esc_html__( 'Choose Image', 'entox' ),
				'type'   => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);
		
        $this->add_control(
			'name',
			[
				'label'   => esc_html__( 'Name', 'entox' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Jessica Brown', 'entox' ),
				'placeholder' => esc_html__( 'Enter your name', 'entox' ),
			]
		);

		$this->add_control(
			'job',
			[
				'label'   => esc_html__( 'Job', 'entox' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Team member', 'entox' ),
				'placeholder' => esc_html__( 'Enter your job', 'entox' ),
			]
		);

	    // list icons control
	    $repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'class_icon',
			[
				'label' => esc_html__( 'Class Icon', 'entox' ),
				'type' => Controls_Manager::TEXT,
				'default' =>  esc_html__( 'fa fa-twitter', 'entox' ),
				
			]
		);
         
        $repeater->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'entox' ),
				'type' => Controls_Manager::URL,
				'default' => [
					'url' => '#',
					'is_external' => 'on',
				],
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'entox' ),
			]
		);

        $repeater->add_control(
			'list_title_icon', [
				'label' => esc_html__( 'Title', 'entox' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'List Title Icon' , 'entox' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'tabs',
			[
				'label' => esc_html__( 'Social Icons', 'entox' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[	
						'list_title_icon' => esc_html__( 'Twitter', 'entox' ),
						'class_icon' => esc_html__( 'fa fa-twitter', 'entox' ),
						'link' => esc_html__( 'https://your-link.com', 'entox' ),
					],
					[	
						'list_title_icon' => esc_html__( 'Facebook', 'entox' ),
						'class_icon' => esc_html__( 'fa fa-facebook-square', 'entox' ),
						'link' => esc_html__( 'https://your-link.com', 'entox' ),
					],
					[	
						'list_title_icon' => esc_html__( 'Printest', 'entox' ),
						'class_icon' => esc_html__( 'fa fa-pinterest-p', 'entox' ),
						'link' => esc_html__( 'https://your-link.com', 'entox' ),
					],
					[	
						'list_title_icon' => esc_html__( 'Instagram', 'entox' ),
						'class_icon' => esc_html__( 'fa fa-instagram', 'entox' ),
						'link' => esc_html__( 'https://your-link.com', 'entox' ),
					],

				],
				'title_field' => '{{{ list_title_icon }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__( 'Title', 'entox' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'title_typography',
					'selector' 	=> '{{WRAPPER}} .ova-team .name',
				]
			);

			$this->add_control(
				'color_title',
				[
					'label' => esc_html__( 'Color ', 'entox' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-team .name' => 'color : {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'margin_title',
				[
					'label' => esc_html__( 'Margin', 'entox' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .ova-team .name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_job_style',
			[
				'label' => esc_html__( 'Job', 'entox' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'job_typography',
					'selector' 	=> '{{WRAPPER}} .ova-team .job',
				]
			);

			$this->add_control(
				'color_job',
				[
					'label' => esc_html__( 'Color ', 'entox' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-team .job' => 'color : {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'margin_job',
				[
					'label' => esc_html__( 'Margin', 'entox' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .ova-team .job' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		// start tab style controls system
		$this->start_controls_section(
			'section_social_icons_style',
			[
				'label' => esc_html__( ' Social Icons', 'entox' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'icon_typography',
					'selector' 	=> '{{WRAPPER}} .ova-team .list_icon ul .item .icon i',
				]
			);

			$this->start_controls_tabs( 'social_icons_colors' );

				$this->start_controls_tab(
					'social_icons_colors_normal',
					[
						'label' => esc_html__( 'Normal', 'entox' ),
					]
				);
     
					$this->add_control(
						'color_icon',
						[
							'label' => esc_html__( 'Color ', 'entox' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-team .list_icon ul .item .icon i' => 'color : {{VALUE}};',
							],
						]
					);

					$this->add_control(
						'background_color_icon',
						[
							'label' => esc_html__( 'Background Color', 'entox' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-team .list_icon ul .item .icon' => 'background-color : {{VALUE}};',
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
								'{{WRAPPER}} .ova-team .list_icon ul' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);
			         
			        $this->add_responsive_control(
						'distance_icon',
						[
							'label' => esc_html__( 'Distance between icons', 'entox' ),
							'type' => Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', 'em', '%' ],
							'selectors' => [
								'{{WRAPPER}} .ova-team .list_icon ul .item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
								'{{WRAPPER}} .ova-team .list_icon ul .item .icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'icon_size',
						[
							'label' => esc_html__( 'Size', 'entox' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
									'step' => 1,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ova-team .list_icon ul .item .icon' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

        		$this->end_controls_tab();
		
				$this->start_controls_tab(
					'social_icons_colors_hover',
					[
						'label' => esc_html__( 'Hover', 'entox' ),
					]
				);

			        $this->add_control(
						'color_social_icons_hover',
						[
							'label' => esc_html__( 'Color', 'entox' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-team .list_icon ul .item .icon:hover i' => 'color : {{VALUE}};',
							],
						]
					);
					$this->add_control(
						'bg_color_social_icons_hover',
						[
							'label' => esc_html__( 'Background Color', 'entox' ),
							'type' => Controls_Manager::COLOR,
							
							'selectors' => [
								'{{WRAPPER}} .ova-team .list_icon ul .item .icon:hover' => 'background-color : {{VALUE}};',
							],
						]
					);

				$this->end_controls_tab();
       		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function render(){
		$settings = $this->get_settings_for_display();
        
		// get url image
		$url 	= isset( $settings['image']['url'] ) ? $settings['image']['url'] : '';
		$alt 	= isset( $settings['image']['alt'] ) ? $settings['image']['alt'] : '';
		if ( empty( $url ) ) {
			return;
		}

		// get name
		$name 	= $settings['name'];

		$job 	= $settings['job'];
        // tabs list social icons
		$tabs = $settings['tabs'];
       ?>
       <div class="ova-team">

       		<div class="picture">
       			<img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
       		</div>
         
       		<div class="info">
       			<?php if ( !empty ($name)) : ?>
       			    <h2 class="name">
       				  <?php echo esc_html( $name ); ?>
       			    </h2>
       		    <?php endif ?>

       		    <?php if ( !empty ($job)) : ?>
       		      <p  class="job">
       		    	<?php echo esc_html( $job ); ?>
       		      </p>
                <?php endif ?>
       		</div>
            
            <div class="list_icon" >
				<?php if( !empty( $tabs ) ) : ?>
					<ul>
						<?php 
						foreach( $tabs as $item ) { 

							$link = $item['link']['url'];
							$is_external = $item['link']['is_external'];
							$target = ( $is_external == 'on' ) ? 'target="_blank"' : '';

							?>
							<li class="item">
								<?php if ( $link ): ?>
									<a href="<?php echo esc_url( $link ); ?>" <?php printf( $target ); ?>>
										<div class="icon">
											<i class="<?php echo esc_attr( $item['class_icon']) ?>"></i>
										</div>
									</a>
								<?php else: ?>
									<div class="icon">
										<i class="<?php echo esc_attr( $item['class_icon']) ?>"></i>
									</div>
								<?php endif; ?>
							</li>
						<?php } ?>
					</ul>
				<?php endif ?>

		    </div>
       </div>
       <?php
   }
   
}

$widgets_manager->register( new Entox_Elementor_Team() );