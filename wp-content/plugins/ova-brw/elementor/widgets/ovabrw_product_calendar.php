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


class ovabrw_product_calendar extends Widget_Base {


	public function get_name() {		
		return 'ovabrw_product_calendar';
	}

	public function get_title() {
		return __( 'Product Calendar', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-calendar';
	}

	public function get_categories() {
		return [ 'ovatheme' ];
	}

	protected function register_controls() {
		
		$this->start_controls_section(
			'section_calendar_style',
			[
				'label' => __( 'Calendar', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'wc_style_warning',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => __( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

			$this->add_control(
				'calendar_bg',
				[
					'label'  	=> __( 'Background', 'ova-brw' ),
					'type' 	 	=> Controls_Manager::COLOR,
					'selectors' => [
						'.woocommerce {{WRAPPER}} .wrap_calendar' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'calendar_padding',
				[
					'label' 	 => __( 'Padding', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .wrap_calendar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'calendar_margin',
				[
					'label' 	 => __( 'Margin', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .wrap_calendar' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		// Title + Arrow Calendar
		$this->start_controls_section(
			'section_title_calendar_style',
			[
				'label' => __( 'Title', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'title_calendar_align',
				[
					'label' => __( 'Alignment', 'ova-brw' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'flex-start' => [
							'title' => __( 'Left', 'ova-brw' ),
							'icon' => 'eicon-text-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'ova-brw' ),
							'icon' => 'eicon-text-align-center',
						],
						'flex-end' => [
							'title' => __( 'Right', 'ova-brw' ),
							'icon' => 'eicon-text-align-right',
						],
					],
					'selectors' => [
						'.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar' => 'align-items: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'title_calendar_typography',
					'selector' 	=> '.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar .fc-button-group .fc-button',
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'title_button_border',
					'selector' 	=> '.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar .fc-button-group .fc-button',
				]
			);

			$this->add_responsive_control(
				'title_buttton_border_radius',
				[
					'label' 	 => __( 'Border Radius', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar .fc-button-group .fc-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					],
				]
			);

			$this->add_control(
				'button_action_color',
				[
					'label'  	=> __( 'Background Button Active', 'ova-brw' ),
					'type' 	 	=> Controls_Manager::COLOR,
					'selectors' => [
						'.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar .fc-button-group .fc-button-active' => 'background-color: {{VALUE}};',
						'.fc .fc-button-primary:focus, .fc .fc-button-primary:not(:disabled).fc-button-active:focus, .fc .fc-button-primary:not(:disabled):active:focus' => 'box-shadow: unset;',
					],
				]
			);

			$this->start_controls_tabs( 'tabs_title_button_style' );

				$this->start_controls_tab(
		            'title_calendar_style_normal',
		            [
		                'label' => __( 'Normal', 'ova-brw' ),
		            ]
		        );

		        	$this->add_control(
			            'title_button_color',
			            [
			                'label' => __( 'Color', 'ova-brw' ),
			                'type' => Controls_Manager::COLOR,
			                'default' => '',
			                'selectors' => [
			                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar .fc-button-group .fc-button' => 'color: {{VALUE}};',
			                ],
			            ]
			        );

			        $this->add_control(
			            'title_button_bg',
			            [
			                'label' => __( 'Background', 'ova-brw' ),
			                'type' => Controls_Manager::COLOR,
			                'default' => '',
			                'selectors' => [
			                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar .fc-button-group .fc-button' => 'background-color: {{VALUE}};',
			                ],
			            ]
			        );

		        $this->end_controls_tab();

		        $this->start_controls_tab(
		            'title_calendar_style_hover',
		            [
		                'label' => __( 'Hover', 'ova-brw' ),
		            ]
		        );

		        	$this->add_control(
			            'title_button_color_hover',
			            [
			                'label' => __( 'Color', 'ova-brw' ),
			                'type' => Controls_Manager::COLOR,
			                'default' => '',
			                'selectors' => [
			                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar .fc-button-group .fc-button:hover' => 'color: {{VALUE}};',
			                ],
			            ]
			        );

			        $this->add_control(
			            'title_button_bg_hover',
			            [
			                'label' => __( 'Background', 'ova-brw' ),
			                'type' => Controls_Manager::COLOR,
			                'default' => '',
			                'selectors' => [
			                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar .fc-button-group .fc-button:hover' => 'background-color: {{VALUE}};',
			                ],
			            ]
			        );

		        $this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_responsive_control(
				'title_button_padding',
				[
					'label' 	 => __( 'Padding', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar .fc-button-group .fc-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'title_button_margin',
				[
					'label' 	 => __( 'Margin', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar .fc-button-group .fc-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();
		//	End

		// Month -Year
		$this->start_controls_section(
			'section_month_year_style',
			[
				'label' => __( 'Month Year', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'my_calendar_typography',
					'selector' 	=> '.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar h2',
				]
			);

			$this->add_control(
	            'my_color',
	            [
	                'label' => __( 'Color', 'ova-brw' ),
	                'type' => Controls_Manager::COLOR,
	                'default' => '',
	                'selectors' => [
	                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar h2' => 'color: {{VALUE}};',
	                ],
	            ]
	        );

	        $this->add_responsive_control(
				'my_padding',
				[
					'label' 	 => __( 'Padding', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar h2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'my_margin',
				[
					'label' 	 => __( 'Margin', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar h2' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		// Day
		$this->start_controls_section(
			'section_day_style',
			[
				'label' => __( 'Day', 'ova-brw' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
	            'today_bg',
	            [
	                'label' => __( 'Today Background', 'ova-brw' ),
	                'type' => Controls_Manager::COLOR,
	                'default' => '',
	                'selectors' => [
	                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-day-today' => 'background-color: {{VALUE}} !important;',
	                ],
	            ]
	        );

	        $this->add_control(
	            'day_past_bg',
	            [
	                'label' => __( 'Day Past Background', 'ova-brw' ),
	                'type' => Controls_Manager::COLOR,
	                'default' => '',
	                'selectors' => [
	                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-day-past' => 'background-color: {{VALUE}} !important;',
	                ],
	            ]
	        );

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'day_calendar_typography',
					'selector' 	=> '.woocommerce {{WRAPPER}} .wrap_calendar .fc-col-header-cell-cushion',
				]
			);

			$this->add_control(
	            'header_bg',
	            [
	                'label' => __( 'Header Background', 'ova-brw' ),
	                'type' => Controls_Manager::COLOR,
	                'default' => '',
	                'selectors' => [
	                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-col-header-cell' => 'background-color: {{VALUE}};',
	                ],
	            ]
	        );

			$this->add_control(
	            'header_color',
	            [
	                'label' => __( 'Header Color', 'ova-brw' ),
	                'type' => Controls_Manager::COLOR,
	                'default' => '',
	                'selectors' => [
	                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-col-header-cell-cushion' => 'color: {{VALUE}};',
	                ],
	            ]
	        );

	        $this->add_responsive_control(
				'header_padding',
				[
					'label' 	 => __( 'Header Padding', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .wrap_calendar .fc-col-header-cell-cushion' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
	            'body_bg',
	            [
	                'label' => __( 'Body Background', 'ova-brw' ),
	                'type' => Controls_Manager::COLOR,
	                'default' => '',
	                'selectors' => [
	                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-daygrid-day' => 'background-color: {{VALUE}};',
	                ],
	            ]
	        );

			$this->add_control(
	            'body_color',
	            [
	                'label' => __( 'Body Color', 'ova-brw' ),
	                'type' => Controls_Manager::COLOR,
	                'default' => '',
	                'selectors' => [
	                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-daygrid-day-number' => 'color: {{VALUE}};',
	                ],
	            ]
	        );

	        $this->add_control(
	            'price_color',
	            [
	                'label' => __( 'Price Color', 'ova-brw' ),
	                'type' => Controls_Manager::COLOR,
	                'default' => '',
	                'selectors' => [
	                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-daygrid-day-bg span' => 'color: {{VALUE}};',
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
		?>

		<div class="elementor-calendar">
			<?php ovabrw_get_template( 'single/calendar.php' ); ?>
		</div>

		<?php
	}
}