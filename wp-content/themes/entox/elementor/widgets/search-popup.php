<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Entox_Elementor_Search_Popup extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_search_popup';
	}

	public function get_title() {
		return esc_html__( 'Search Popup', 'entox' );
	}

	public function get_icon() {
		return 'eicon-search';
	}

	public function get_categories() {
		return [ 'hf' ];
	}

	public function get_keywords() {
		return [ 'search' ];
	}

	public function get_script_depends() {
		return [ 'entox-elementor-search-popup' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_search',
			[
				'label' => esc_html__( 'Search', 'entox' ),
			]
		);

			$this->add_control(
				'search_result',
				[
					'label' 	=> esc_html__( 'Search Result', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'default',
					'options' 	=> [
						'default' 		=> esc_html__( 'Default', 'entox' ),
						'search_map' 	=> esc_html__( 'Search Page', 'entox' ),
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
						'search_result' => 'search_map',
					],
				]
			);

			$this->add_control(
				'show_time',
				[
					'label' 		=> esc_html__( 'Show Time', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 		=> esc_html__( 'Show', 'entox' ),
					'label_off' 	=> esc_html__( 'Hide', 'entox' ),
					'return_value' 	=> 'yes',
					'default' 		=> '',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_search_style',
			[
				'label' => esc_html__( 'Search', 'entox' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'color_icon_search',
				[
					'label' => esc_html__( 'Icon Color', 'entox' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova_wrap_search_popup i' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'color_hover_icon_search',
				[
					'label' => esc_html__( 'Icon Hover Color', 'entox' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova_wrap_search_popup i:hover' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'size_icon',
				[
					'label' => esc_html__( 'Size Icon', 'entox' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 30,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 14,
					],
					'selectors' => [
						'{{WRAPPER}} .ova_wrap_search_popup i' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'border_style',
				[
					'label' => esc_html__( 'Border Style', 'entox' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'none',
					'options' => [
						'solid'  => esc_html__( 'Solid', 'entox' ),
						'dashed' => esc_html__( 'Dashed', 'entox' ),
						'dotted' => esc_html__( 'Dotted', 'entox' ),
						'double' => esc_html__( 'Double', 'entox' ),
						'none' 	 => esc_html__( 'None', 'entox' ),
					],
					'separator' => 'before',
					'selectors' => [
						'{{WRAPPER}} .ova_wrap_search_popup i' => 'border-style: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'button_border_width',
				[
					'label' => esc_html__( 'Border Width', 'entox' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em'],
					'selectors' => [
						'{{WRAPPER}} .ova_wrap_search_popup i' => 'border-width:  {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'border_style',
								'operator' => '!=',
								'value' => 'none',
							],
						],
					],
				]
			);

			$this->add_control(
				'color_border',
				[
					'label' => esc_html__( 'Border Color', 'entox' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova_wrap_search_popup i' => 'border-color: {{VALUE}}',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'border_style',
								'operator' => '!=',
								'value' => 'none',
							],
						],
					],
				]
			);

			$this->add_responsive_control(
				'margin',
				[
					'label' => esc_html__( 'Margin', 'entox' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .ova_wrap_search_popup i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'padding',
				[
					'label' => esc_html__( 'Padding', 'entox' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'align',
				[
					'label' => esc_html__( 'Alignment', 'entox' ),
					'type' => Controls_Manager::CHOOSE,
					'default' => 'center',
					'options' => [
						'flex-start' => [
							'title' => esc_html__( 'Left', 'entox' ),
							'icon' => 'eicon-h-align-left',
						],
						'center' => [
							'title' => esc_html__( 'Center', 'entox' ),
							'icon' => 'eicon-h-align-center',
						],
						'flex-end' => [
							'title' => esc_html__( 'Right', 'entox' ),
							'icon' => 'eicon-h-align-right',
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ova_wrap_search_popup' => 'justify-content: {{VALUE}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_search_form_style',
			[
				'label' => esc_html__( 'From', 'entox' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'background_search_form',
				[
					'label' => esc_html__( 'Background', 'entox' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova_wrap_search_popup .ova_search_popup' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'input_style_options',
				[
					'label' => esc_html__( 'Input Options', 'entox' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'background_input',
				[
					'label' => esc_html__( 'Background', 'entox' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova_wrap_search_popup .ova_search_popup .container .search_popup_form .s_field input' => 'background-color: {{VALUE}}',
						'{{WRAPPER}} .ova_wrap_search_popup .ova_search_popup .container .search_popup_form .s_field select' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'color_input',
				[
					'label' => esc_html__( 'Color', 'entox' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova_wrap_search_popup .ova_search_popup .container .search_popup_form .s_field input' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'input_border',
					'label' => esc_html__( 'Border', 'entox' ),
					'selector' => '{{WRAPPER}} .ova_wrap_search_popup .ova_search_popup .container .search_popup_form .s_field input, {{WRAPPER}} .ova_wrap_search_popup .ova_search_popup .container .search_popup_form .s_field select',
				]
			);

			$this->add_responsive_control(
				'input_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'entox' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .ova_wrap_search_popup .ova_search_popup .container .search_popup_form .s_field input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .ova_wrap_search_popup .ova_search_popup .container .search_popup_form .s_field select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'input_margin',
				[
					'label' => esc_html__( 'Margin', 'entox' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .ova_wrap_search_popup .ova_search_popup .container .search_popup_form .s_field' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_submit_style',
			[
				'label' => esc_html__( 'Button', 'entox' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'button_style_tabs'
			);

				$this->start_controls_tab(
					'style_normal_tab',
					[
						'label' => esc_html__( 'Normal', 'entox' ),
					]
				);

					$this->add_control(
						'btn_background',
						[
							'label' => esc_html__( 'Background', 'entox' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova_wrap_search_popup .ova_search_popup .container .search_popup_form .s_submit .ovabrw_btn_submit' => 'background-color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'btn_color',
						[
							'label' => esc_html__( 'Color', 'entox' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova_wrap_search_popup .ova_search_popup .container .search_popup_form .s_submit .ovabrw_btn_submit' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'style_hover_tab',
					[
						'label' => esc_html__( 'Hover', 'entox' ),
					]
				);

					$this->add_control(
						'btn_background_hover',
						[
							'label' => esc_html__( 'Background', 'entox' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova_wrap_search_popup .ova_search_popup .container .search_popup_form .s_submit .ovabrw_btn_submit:hover' => 'background-color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'btn_color_hover',
						[
							'label' => esc_html__( 'Color', 'entox' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova_wrap_search_popup .ova_search_popup .container .search_popup_form .s_submit .ovabrw_btn_submit:hover' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'btn_border',
					'label' => esc_html__( 'Border', 'entox' ),
					'selector' => '{{WRAPPER}} .ova_wrap_search_popup .ova_search_popup .container .search_popup_form .s_submit .ovabrw_btn_submit',
				]
			);

			$this->add_control(
				'btn_border_color_hover',
				[
					'label' => esc_html__( 'Border Color Hover', 'entox' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova_wrap_search_popup .ova_search_popup .container .search_popup_form .s_submit .ovabrw_btn_submit:hover' => 'border-color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'btn_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'entox' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .ova_wrap_search_popup .ova_search_popup .container .search_popup_form .s_submit .ovabrw_btn_submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'btn_padding',
				[
					'label' => esc_html__( 'Padding', 'entox' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .ova_wrap_search_popup .ova_search_popup .container .search_popup_form .s_submit .ovabrw_btn_submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'btn_margin',
				[
					'label' => esc_html__( 'Margin', 'entox' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .ova_wrap_search_popup .ova_search_popup .container .search_popup_form .s_submit .ovabrw_btn_submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();
	}

	protected function render() {
		$settings 		= $this->get_settings_for_display();
		$search_result 	= $settings['search_result'];
		$url_search 	= home_url();

		if ( 'search_map' === $search_result ) {
			$url_search = $settings['url_search_result']['url'];
		}

		$dateformat 	= ovabrw_get_date_format();
    	$hour_default 	= ovabrw_get_setting( get_option( 'ova_brw_booking_form_default_hour', '07:00' ) );
    	$time_step 		= ovabrw_get_setting( get_option( 'ova_brw_booking_form_step_time', '30' ) );
    	// Get first day in week
		$first_day = get_option( 'ova_brw_calendar_first_day', '0' );

		if ( empty( $first_day ) ) {
		    $first_day = 0;
		}

		$timepicker = 'false';
		if ( $settings['show_time'] ==='yes' ) $timepicker = 'true';

		?>
		<div class="ova_wrap_search_popup">
			<i class="icon_search"></i>
			<div class="ova_search_popup">
				<span class="btn_close icon_close"></span>
				<div class="container">
					<form action="<?php echo esc_url( $url_search ); ?>" method="GET" class="search_popup_form">
						<div class="s_field">
							<input 
								type="text" 
								class="name" 
								name="ovabrw_name_product" 
								placeholder="<?php echo esc_attr__('Product Name', 'entox'); ?>"
							/>
						</div>
						<div class="s_field">
							<?php echo ovabrw_search_popup_cat_rental( '', '', '' ); ?>
						</div>
						<div class="s_field">
							<input 
								type="text" 
								name="ovabrw_pickup_date" 
								value="" 
								onkeydown="return false" 
								class="ovabrw_datetimepicker ovabrw_start_date" 
								placeholder="<?php echo esc_attr__( 'Pick-up Date', 'entox' ); ?>" 
								autocomplete="off" 
								data-hour_default="<?php echo esc_attr( $hour_default ); ?>" 
								data-time_step="<?php echo esc_attr( $time_step ); ?>" 
								data-dateformat="<?php echo esc_attr( $dateformat ); ?>" 
								data-firstday="<?php echo esc_attr( $first_day ); ?>"
								timepicker="<?php echo esc_attr( $timepicker ); ?>"
								data-error=".ovabrw_pickup_date" 
								onfocus="blur();"
							/>
						</div>
						<div class="s_field">
							<input 
								type="text" 
								name="ovabrw_pickoff_date" 
								value="" 
								onkeydown="return false" 
								class="ovabrw_datetimepicker ovabrw_end_date" 
								placeholder="<?php echo esc_attr__( 'Drop-off Date', 'entox' ); ?>" 
								autocomplete="off" 
								data-hour_default="<?php echo esc_attr( $hour_default ); ?>" 
								data-time_step="<?php echo esc_attr( esc_attr( $time_step ) ); ?>" 
								data-dateformat="<?php echo esc_attr( $dateformat ); ?>" 
								data-firstday="<?php echo esc_attr( $first_day ); ?>"
								timepicker="<?php echo esc_attr( $timepicker ); ?>"
								data-error=".ovabrw_pickoff_date" 
								onfocus="blur();"
							/>
						</div>
						<div class="s_submit">
	                        <button class="ovabrw_btn_submit" type="submit">
	                        	<?php echo esc_html__( 'Search', 'entox' ); ?>
	                        </button>
	                    </div>
	                    <?php if ( 'default' === $search_result ): ?>
	                    	<input type="hidden" name="ovabrw_search_product" value="ovabrw_search_product" />
	                    	<input type="hidden" name="ovabrw_search" value="search_item" />
	                    	<input type="hidden" name="post_type" value="product" />
	                    <?php endif; ?>
	                    <?php if ( defined( 'ICL_LANGUAGE_CODE' ) ): ?>
                			<input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
            			<?php endif; ?>
					</form>
				</div>
			</div>
		</div>
		<?php
	}

}

$widgets_manager->register( new  Entox_Elementor_Search_Popup() );

