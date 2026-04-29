<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Entox_Elementor_Product_Search2 extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_product_search2';
	}

	public function get_title() {
		return esc_html__( 'Product Search 2', 'entox' );
	}

	public function get_icon() {
		return 'eicon-site-search';
	}

	public function get_categories() {
		return [ 'entox' ];
	}

	public function get_script_depends() {
		/* Google Maps */
		if ( get_option( 'ova_brw_google_key_map', false ) ) {
			wp_enqueue_script( 'google_map','https://maps.googleapis.com/maps/api/js?key='.get_option( 'ova_brw_google_key_map', '' ).'&libraries=places', false, true );
		} else {
			wp_enqueue_script( 'google_map','https://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places', array('jquery'), false, true);
		}
		return [ 'entox-elementor-product-search2' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'entox' ),
			]
		);

			$search_fields = array(
				'' 					=> esc_html__('Select Search', 'entox'),
				'name' 				=> esc_html__('Name', 'entox'),
				'category' 			=> esc_html__('Category', 'entox'),
				'location' 			=> esc_html__('Location', 'entox'),
				'pickup_location' 	=> esc_html__('Pick-up Location', 'entox'),
				'dropoff_location' 	=> esc_html__('Drop-off Location', 'entox'),
				'pickup_date' 		=> esc_html__('Pick-up Date', 'entox'),
				'dropoff_date' 		=> esc_html__('Drop-off Date', 'entox'),
				'tags' 				=> esc_html__('Tags', 'entox'),
			);

			$this->add_control(
				'show_time',
				[
					'label' 	=> esc_html__( 'Show Time', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 	=> esc_html__( 'Show', 'entox' ),
					'label_off' => esc_html__( 'Hide', 'entox' ),
				]
			);

			$this->add_control(
				'field_1',
				[
					'label'   	=> esc_html__( 'Field 1', 'entox' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
				]
			);

			$this->add_control(
				'field_2',
				[
					'label'   	=> esc_html__( 'Field 2', 'entox' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
				]
			);

			$this->add_control(
				'field_3',
				[
					'label'   	=> esc_html__( 'Field 3', 'entox' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
				]
			);

			$this->add_control(
				'field_4',
				[
					'label'   	=> esc_html__( 'Field 4', 'entox' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
				]
			);

		$this->end_controls_section();

		/* Section Search Result */
		$this->start_controls_section(
			'section_search_result',
			[
				'label' => esc_html__( 'Search Result', 'entox' ),
			]
		);

			$this->add_control(
				'search_result',
				[
					'label' => esc_html__( 'Pages', 'entox' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'default',
					'options' => [
						'default'  	=> esc_html__( 'Default', 'entox' ),
						'new_page' 	=> esc_html__( 'New Page', 'entox' ),
					],
				]
			);

			$this->add_control(
				'search_result_url',
				[
					'label' 		=> esc_html__( 'Link', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::URL,
					'placeholder' 	=> esc_html__( 'https://your-link.com', 'entox' ),
					'dynamic' 		=> [
						'active' => true,
					],
					'default' => [
						'url' 			=> '#',
						'is_external' 	=> false,
						'nofollow' 		=> false,
					],
					'condition' => [
						'search_result' => 'new_page',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_label_field',
			[
				'label' => esc_html__( 'Label Field', 'entox' ),
			]
		);

			$this->add_control(
				'field_name',
				[
					'label' 		=> esc_html__( 'Label Name', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::TEXT,
					'default' 		=> esc_html__( 'Product Name', 'entox' ),
				]
			);

			$this->add_control(
				'field_category',
				[
					'label' 		=> esc_html__( 'Label Category', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::TEXT,
					'default' 		=> esc_html__( 'Select Category', 'entox' ),
				]
			);

			$this->add_control(
				'field_location',
				[
					'label' 		=> esc_html__( 'Label Location', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::TEXT,
					'default' 		=> esc_html__( 'Location', 'entox' ),
				]
			);

			$this->add_control(
				'field_pickup_location',
				[
					'label' 		=> esc_html__( 'Label Pick-up Location', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::TEXT,
					'default' 		=> esc_html__( 'Pick-up Location', 'entox' ),
				]
			);

			$this->add_control(
				'field_dropoff_location',
				[
					'label' 		=> esc_html__( 'Label Drop-off Location', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::TEXT,
					'default' 		=> esc_html__( 'Drop-off Location', 'entox' ),
				]
			);

			$this->add_control(
				'field_pickup_date',
				[
					'label' 		=> esc_html__( 'Label Pick-up Date', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::TEXT,
					'default' 		=> esc_html__( 'Pick-up Date', 'entox' ),
				]
			);

			$this->add_control(
				'field_dropoff_date',
				[
					'label' 		=> esc_html__( 'Label Drop-off Date', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::TEXT,
					'default' 		=> esc_html__( 'Drop-off Date', 'entox' ),
				]
			);

			$this->add_control(
				'field_tags',
				[
					'label' 		=> esc_html__( 'Label Tags', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::TEXT,
					'default' 		=> esc_html__( 'Product Tag', 'entox' ),
				]
			);

			$this->add_control(
				'field_button',
				[
					'label' 		=> esc_html__( 'Search Icon', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::TEXT,
					'default' 		=> esc_html__( 'ovaicon-loupe', 'entox' ),
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_exclude',
			[
				'label' => esc_html__( 'Exclude', 'entox' ),
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
			'section_product_search_style',
			[
				'label' => esc_html__( 'Content', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'content_max_width',
				[
					'label' 		=> esc_html__( 'Max Width', 'entox' ),
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
						'{{WRAPPER}} .ova-product-search2.ovabrw_wd_search' => 'max-width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'align',
				[
					'label' 	=> esc_html__( 'Alignment', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::CHOOSE,
					'options' 	=> [
						'left' => [
							'title' => esc_html__( 'Left', 'entox' ),
							'icon' => 'eicon-h-align-left',
						],
						'center' => [
							'title' => esc_html__( 'Center', 'entox' ),
							'icon' => 'eicon-h-align-center',
						],
						'right' => [
							'title' => esc_html__( 'Right', 'entox' ),
							'icon' => 'eicon-h-align-right',
						],
					],
					'default' => 'left',
					'selectors' => [
						'{{WRAPPER}}' => 'text-align: {{VALUE}}',
					]
				]
			);

			$this->add_control(
				'content_background',
				[
					'label' 	=> esc_html__( 'Background', 'entox' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'content_border',
					'label' 	=> esc_html__( 'Border', 'entox' ),
					'selector' 	=> '{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form',
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
						'{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
						'{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_fields_style',
			[
				'label' => esc_html__( 'Fields', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'fields_typography',
					'selector' 	=> '{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-content .search-label input[type=text], {{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-content .search-label select, {{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-content .label_search input[type=text], {{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-content .label_search select',
				]
			);


			$this->start_controls_tabs(
				'style_fields_tabs',
			);

				$this->start_controls_tab(
					'style_field_normal_tab',
					[
						'label' => esc_html__( 'Normal', 'entox' ),
					]
				);

					$this->add_control(
						'filed_color_normal',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-content .search-label input[type=text]' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-content .search-label input[type=text]::placeholder' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-content .search-label select' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-content .label_search input[type=text]' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-content .label_search input[type=text]::placeholder' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-content .label_search select' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'style_field_focus_tab',
					[
						'label' => esc_html__( 'Focus', 'entox' ),
					]
				);

					$this->add_control(
						'filed_color_focus',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-content .search-label input[type=text]:focus' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-content .search-label select:focus' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-content .label_search input[type=text]:focus' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-content .label_search select:focus' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();
			$this->end_controls_tabs();
		$this->end_controls_section();

		$this->start_controls_section(
			'section_button_style',
			[
				'label' => esc_html__( 'Button', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'button_typography',
					'selector' 	=> '{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-submit .ovabrw_btn_submit i',
				]
			);

			$this->start_controls_tabs(
				'style_button_tabs',
			);

				$this->start_controls_tab(
					'style_button_normal_tab',
					[
						'label' => esc_html__( 'Normal', 'entox' ),
					]
				);

					$this->add_control(
						'button_color_normal',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-submit .ovabrw_btn_submit i' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'button_background_normal',
						[
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-submit .ovabrw_btn_submit' => 'background-color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'style_button_hover_tab',
					[
						'label' => esc_html__( 'Hover', 'entox' ),
					]
				);

					$this->add_control(
						'button_color_hover',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-submit .ovabrw_btn_submit:hover i' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'button_background_hover',
						[
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-submit .ovabrw_btn_submit:hover' => 'background-color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();
			$this->end_controls_tabs();

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'button_border',
					'label' 	=> esc_html__( 'Border', 'entox' ),
					'selector' 	=> '{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-submit .ovabrw_btn_submit',
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'button_border_radius',
				[
					'label' 		=> esc_html__( 'Border Radius', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-submit .ovabrw_btn_submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'button_padding',
				[
					'label' 		=> esc_html__( 'Padding', 'entox' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ova-product-search2.ovabrw_wd_search .product-search-form .product-search-submit .ovabrw_btn_submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		// Get first day in week
		$first_day = get_option( 'ova_brw_calendar_first_day', '0' );

		if ( empty( $first_day ) ) {
			$first_day = 0;
		}

		$show_time = $settings['show_time'] ? ' timepicker="true"' : '';

		$exclude_id = '';
		if ( $settings['category_not_in'] ) {
			$exclude_id = explode( '|', $settings['category_not_in'] );
		}

		// Label
		$field_name 			= $settings['field_name'] ? $settings['field_name'] : esc_html__( 'Product Name', 'entox' );
		$field_category 		= $settings['field_category'] ? $settings['field_category'] : esc_html__( 'Select Category', 'entox' );
		$field_location 		= $settings['field_location'] ? $settings['field_location'] : esc_html__( 'Location', 'entox' );
		$field_pickup_location 	= $settings['field_pickup_location'] ? $settings['field_pickup_location'] : esc_html__( 'Pick-up Location', 'entox' );
		$field_dropoff_location = $settings['field_dropoff_location'] ? $settings['field_dropoff_location'] : esc_html__( 'Drop-off Location', 'entox' );
		$field_pickup_date 		= $settings['field_pickup_date'] ? $settings['field_pickup_date'] : esc_html__( 'Pick-up Date', 'entox' );
		$field_dropoff_date 	= $settings['field_dropoff_date'] ? $settings['field_dropoff_date'] : esc_html__( 'Drop-off Date', 'entox' );
		$field_tags 			= $settings['field_tags'] ? $settings['field_tags'] : esc_html__( 'Product Tag', 'entox' );
		$field_button 			= $settings['field_button'] ? $settings['field_button'] : esc_html__( 'ovaicon-loupe', 'entox' );

		$date_format 	= ovabrw_get_date_format();
		$hour_default 	= ovabrw_get_setting( get_option( 'ova_brw_booking_form_default_hour', '07:00' ) );
		$time_step 		= ovabrw_get_setting( get_option( 'ova_brw_booking_form_step_time', '30' ) );

		$action = home_url();

		if ( 'default' != $settings['search_result'] ) {
			$action = $settings['search_result_url']['url'];
		}

		$class_column = 'column1';

		for ( $c = 1; $c <= 4; $c++ ) {
			$field = 'field_'.$c;
			if ( $settings[$field] ) {
				$class_column = 'column'.$c;
			}
		}

		?>
		<div class="ova-product-search2 ovabrw_wd_search">
			<form class="product-search-form" action="<?php echo esc_url( $action ); ?>" autocomplete="off" autocapitalize="none">
				<div class="product-search-content wrap_content <?php echo esc_attr( $class_column ); ?>">
					<?php for ( $i = 1; $i <= 4; $i++ ):
						$key = 'field_'.$i;

						switch ( $settings[$key] ) {
							case 'name': ?>
								<div class="search-label ova-product-name">
									<input type="text" name="ovabrw_name_product" autocomplete="off" autocapitalize="none" 
											placeholder="<?php echo esc_attr( $field_name ); ?>" />
								</div>
								<?php break;

							case 'category': ?>
								<div class="search-label ova-category">
									<?php echo ovabrw_cat_rental( '', '', $exclude_id, $field_category ); ?>
								</div>
							<?php break;

							case 'location': ?>
								<div class="search-label wrap_search_location">
									<input type="hidden" name="map_lat" id="map_lat" autocomplete="off" autocapitalize="none" />
									<input type="hidden" name="map_lng" id="map_lng" autocomplete="off" autocapitalize="none" />
									<input type="text" id="pac-input" name="map_address" value="" class="controls" 
											placeholder="<?php echo esc_attr( $field_location ); ?>" 
											autocomplete="off" autocapitalize="none" />
									<i class="locate_me ovaicon-maps-and-flags" id="locate_me"></i>
									<input type="hidden" value="" name="map_name" id="map_name"  
											autocomplete="off" autocapitalize="none" />
								</div>
							<?php break;

							case 'pickup_location': ?>
								<div class="search-label ova-pickup-location">
									<?php echo ovabrw_get_locations_html( 'ovabrw_pickup_loc', 'ovabrw_pickup_loc', '', '', '', $field_pickup_location ); ?>
								</div>
							<?php break;

							case 'dropoff_location': ?>
								<div class="search-label ova-dropoff-location">
									<?php echo ovabrw_get_locations_html( 'ovabrw_dropoff_loc', 'ovabrw_dropoff_loc', '', '', '', $field_dropoff_location ); ?>
								</div>
							<?php break;

							case 'pickup_date': ?>
								<div class="search-label ova-pickup-date">
									<input type="text" name="ovabrw_pickup_date" value="" onkeydown="return false" 
											class=" ovabrw_datetimepicker ovabrw_start_date" 
											placeholder="<?php echo esc_attr( $field_pickup_date ); ?>" autocomplete="off" 
											data-hour_default="<?php echo esc_attr( $hour_default ); ?>" 
											data-time_step="<?php echo esc_attr( $time_step ); ?>" 
											data-dateformat="<?php echo esc_attr( $date_format ); ?>" 
											data-firstday="<?php echo esc_attr( $first_day ); ?>" 
											onfocus="blur();" 
											<?php printf( $show_time ); ?>/>
									<i class="ova-calendar ovaicon-calendar"></i>
								</div>
							<?php break;

							case 'dropoff_date': ?>
								<div class="search-label ova-dropoff-date">
									<input type="text" name="ovabrw_pickoff_date" value="" onkeydown="return false" 
										class=" ovabrw_datetimepicker ovabrw_end_date" 
										placeholder="<?php echo esc_attr( $field_dropoff_date ); ?>" autocomplete="off" 
										data-hour_default="<?php echo esc_attr( $hour_default ); ?>" 
										data-time_step="<?php echo esc_attr( $time_step ); ?>" 
										data-dateformat="<?php echo esc_attr( $date_format ); ?>" 
										data-firstday="<?php echo esc_attr( $first_day ); ?>" 
										onfocus="blur();" 
										<?php printf( $show_time ); ?> />
									<i class="ova-calendar ovaicon-calendar"></i>
								</div>
							<?php break;

							case 'tags': ?>
								<div class="search-label ova-tags">
									<input type="text" name="ovabrw_tag_product" autocomplete="off" 
											autocapitalize="none" 
											placeholder="<?php echo esc_attr( $field_tags ); ?>" />
								</div>
							<?php break;

							default:
								break;
						}
					?>
					<?php endfor; ?>
				</div>
				<?php if ( 'default' != $settings['search_result'] ): ?>
					<div class="product-search-submit">
	                    <button class="ovabrw_btn_submit" type="submit">
	                    	<i class="<?php echo esc_attr( $field_button ); ?>"></i>
	                    </button>
	                </div>
				<?php else: ?>
					<div class="product-search-submit">
	                    <button class="ovabrw_btn_submit" type="submit">
	                    	<i class="<?php echo esc_attr( $field_button ); ?>"></i>
	                    </button>
	                </div>
	                <input type="hidden" name="ovabrw_search_product" value="ovabrw_search_product" />
	                <input type="hidden" name="ovabrw_search" value="search_item" />
	                <input type="hidden" name="post_type" value="product" />
	            <?php endif; ?>

	            <?php if ( defined( 'ICL_LANGUAGE_CODE' ) ): ?>
                	<input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
                <?php endif; ?>
			</form>
		</div>
		<?php
	}
}

$widgets_manager->register( new Entox_Elementor_Product_Search2() );
