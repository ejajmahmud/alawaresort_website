<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Entox_Elementor_Button extends Widget_Base {

	public function get_name() {
		return 'entox_elementor_button';
	}

	public function get_title() {
		return esc_html__( 'Button Ad', 'entox' );
	}

	public function get_icon() {
		return 'eicon-button';
	}

	public function get_categories() {
		return [ 'entox' ];
	}

	protected function register_controls() {
		
		$this->start_controls_section(
			'section_button',
			[
				'label' => esc_html__( 'Button', 'entox' ),
			]
		);

			$this->add_control(
				'btn_type',
				[
					'label' 	=> esc_html__( 'Type', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'default',
					'options' 	=> [
						'default' 	=> esc_html__( 'Default', 'entox' ),
						'custom' 	=> esc_html__( 'Custom', 'entox' ),
					],
				]
			);

			$this->add_control(
				'custom_link',
				[
					'label' 		=> esc_html__( 'Link', 'entox' ),
					'type' 			=> \Elementor\Controls_Manager::URL,
					'placeholder' 	=> esc_html__( 'https://your-link.com', 'entox' ),
					'default' 		=> [
						'url' => '#',
					],
					'condition' 	=> [
						'btn_type' => 'custom',
					],
				]
			);

			$this->add_control(
				'btn_text',
				[
					'label'   	=> esc_html__( 'Text', 'entox' ),
					'type'    	=> Controls_Manager::TEXT,
					'default' 	=> esc_html__( 'Submit Ad', 'entox' ),
				]
			);

			$this->add_responsive_control(
				'btn_align',
				[
					'label' => esc_html__( 'Alignment', 'entox' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'left'    => [
							'title' => esc_html__( 'Left', 'entox' ),
							'icon' => 'eicon-text-align-left',
						],
						'center' => [
							'title' => esc_html__( 'Center', 'entox' ),
							'icon' => 'eicon-text-align-center',
						],
						'right' => [
							'title' => esc_html__( 'Right', 'entox' ),
							'icon' => 'eicon-text-align-right',
						],
					],
					'default' => 'left',
					'selectors' => [
						'{{WRAPPER}} .ova_button_ad' => 'text-align: {{VALUE}}',
					]
				]
			);

			$this->add_control(
				'target',
				[
					'label' 	=> esc_html__( 'Open in new window', 'entox' ),
					'type' 		=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 	=> esc_html__( 'Yes', 'entox' ),
					'label_off' => esc_html__( 'No', 'entox' ),
					'default' 	=> 'yes',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_btn_style',
			[
				'label' => esc_html__( 'Button', 'entox' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'style_btn_tabs'
			);

				$this->start_controls_tab(
					'style_btn_normal_tab',
					[
						'label' => esc_html__( 'Normal', 'entox' ),
					]
				);

					$this->add_control(
						'btn_color_normal',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova_button_ad a' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'btn_background_normal',
						[
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova_button_ad a' => 'background-color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'style_btn_hover_tab',
					[
						'label' => esc_html__( 'Hover', 'entox' ),
					]
				);

					$this->add_control(
						'btn_color_hover',
						[
							'label' 	=> esc_html__( 'Color', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova_button_ad a:hover' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'btn_background_hover',
						[
							'label' 	=> esc_html__( 'Background', 'entox' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ova_button_ad a:hover' => 'background-color: {{VALUE}}',
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
					'selector' => '{{WRAPPER}} .button_ad a',
				]
			);

			$this->add_control(
				'btn_border_color_hover',
				[
					'label' => esc_html__( 'Border Color Hover', 'entox' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ova_button_ad a:hover' => 'border-color: {{VALUE}}',
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
						'{{WRAPPER}} .ova_button_ad a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
						'{{WRAPPER}} .ova_button_ad a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
						'{{WRAPPER}} .ova_button_ad a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$url 	= home_url();
		$type 	= $settings['btn_type'];
		$text 	= $settings['btn_text'];

		if ( 'default' === $type ) {
			$url .= '/store-manager/products-manage/';
		} else {
			$url = $settings['custom_link']['url'];
		}

		$target = $settings['target'] ? ' target="_blank"' : '';

		?>
			<?php if ( is_user_logged_in() ): ?>
				<div class="ova_button_ad">
					<a href="<?php echo esc_url( $url ); ?>"<?php printf( $target ); ?>>
						<?php echo esc_html( $text ); ?>
					</a>
				</div>
			<?php endif; ?>
		<?php
	}
}

$widgets_manager->register( new Entox_Elementor_Button() );
