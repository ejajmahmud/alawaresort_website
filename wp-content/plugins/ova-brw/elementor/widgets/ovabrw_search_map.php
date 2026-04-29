<?php
namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class ovabrw_search_map extends Widget_Base {


	public function get_name() {		
		return 'ovabrw_search_map';
	}

	public function get_title() {
		return __( 'Search Map', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-google-maps';
	}

	public function get_categories() {
		return [ 'ovatheme' ];
	}

	public function get_script_depends() {

		/* Google Maps */
		if ( get_option( 'ova_brw_google_key_map', false ) ) {
			wp_enqueue_script( 'google_map','https://maps.googleapis.com/maps/api/js?key='.get_option( 'ova_brw_google_key_map', '' ).'&loading=async&callback=Function.prototype&libraries=places', false, true );
		} else {
			wp_enqueue_script( 'google_map','https://maps.googleapis.com/maps/api/js?sensor=false&loading=async&callback=Function.prototype&libraries=places', array('jquery'), false, true);
		}
		wp_enqueue_script( 'google-marker', OVABRW_PLUGIN_URI.'assets/libs/google_map/markerclusterer.js', array('jquery'), false, true);

		/* Override market google map when more product the same location*/
		wp_enqueue_script('oms', OVABRW_PLUGIN_URI.'assets/libs/google_map/oms.js', array('jquery'), false, true);

		/* Jquery ui */
		wp_enqueue_script('jquery-ui', OVABRW_PLUGIN_URI.'assets/libs/jquery-ui/jquery-ui.js', array('jquery'), false, true);
		wp_enqueue_style('jquery-ui', OVABRW_PLUGIN_URI.'assets/libs/jquery-ui/jquery-ui.css', array(), null);

		/* Select2 */
		wp_enqueue_style('select2', OVABRW_PLUGIN_URI.'assets/libs/select2/select2.min.css', array(), null);
		wp_enqueue_script('select2', OVABRW_PLUGIN_URI.'assets/libs/select2/select2.min.js', array('jquery'),null,true);
		
		return [ 'script-elementor' ];
	}

	protected function register_controls() {

		$search_fields = array(
			'' 					=> __('Select Search', 'ova-brw'),
			'name' 				=> __('Name', 'ova-brw'),
			'category' 			=> __('Category', 'ova-brw'),
			'location' 			=> __('Location', 'ova-brw'),
			'start_location' 	=> __('Pick-up Location', 'ova-brw'),
			'end_location' 		=> __('Drop-off Location', 'ova-brw'),
			'start_date' 		=> __('Pick-up Date', 'ova-brw'),
			'end_date' 			=> __('Drop-off Date', 'ova-brw'),
			'attribute' 		=> __('Name Attribute', 'ova-brw'),
			'tag' 				=> __('Tag', 'ova-brw'),
		);
		
		$this->start_controls_section(
			'section_setting',
			[
				'label' => esc_html__( 'Settings', 'ova-brw' ),
			]
		);

			$this->add_control(
				'type',
				[
					'label'   => __( 'Type', 'ova-brw' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'type1',
					'options' => [
						'type1' => __('Type 1', 'ova-brw'),
					],
				]
			);

			$this->add_control(
				'posts_per_page',
				[
					'label' 	=> __( 'Per Page', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::NUMBER,
					'min' 		=> 1,
					'max' 		=> 50,
					'step' 		=> 1,
					'default' 	=> 12,
				]
			);

			$this->add_control(
				'column',
				[
					'label'   => __( 'Column', 'ova-brw' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'two-column',
					'options' => [
						'one-column' 	=> __('1 Column', 'ova-brw'),
						'two-column' 	=> __('2 Columns', 'ova-brw'),
						'three-column' 	=> __('3 Columns', 'ova-brw'),
					],
				]
			);

			$this->add_control(
				'orderby',
				[
					'label'   => __( 'Order By', 'ova-brw' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'featured',
					'options' => [
						'featured'  => __('Featured', 'ova-brw'),
						'title' 	=> __('Title', 'ova-brw'),
						'ID' 		=> __('ID', 'ova-brw'),
						'name' 		=> __('Name', 'ova-brw'),
						'date' 		=> __('Date', 'ova-brw'),
						'modified' 	=> __('Modified', 'ova-brw'),
						'rand' 		=> __('Random', 'ova-brw'),
					],
				]
			);

			$this->add_control(
				'order',
				[
					'label'   => __( 'Order', 'ova-brw' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'DESC',
					'options' => [
						'ASC' 	=> __('Ascending', 'ova-brw'),
						'DESC' 	=> __('Descending', 'ova-brw'),
					],
				]
			);

			$this->add_control(
				'show_filter',
				[
					'label' 		=> __( 'Show Filters', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 		=> __( 'Show', 'ova-brw' ),
					'label_off' 	=> __( 'Hide', 'ova-brw' ),
					'default' 		=> 'yes',
				]
			);

			$this->add_control(
				'show_featured',
				[
					'label' 		=> __( 'Just Show Featured', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 		=> __( 'Show', 'ova-brw' ),
					'label_off' 	=> __( 'Hide', 'ova-brw' ),
					'default' 		=> 'no',
				]
			);

			$this->add_control(
				'show_time',
				[
					'label' 		=> __( 'Show time', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 		=> __( 'Show', 'ova-brw' ),
					'label_off' 	=> __( 'Hide', 'ova-brw' ),
					'default' 		=> 'no',
				]
			);

			$this->add_control(
				'show_map',
				[
					'label' 		=> __( 'Show Map', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 		=> __( 'Show', 'ova-brw' ),
					'label_off' 	=> __( 'Hide', 'ova-brw' ),
					'default' 		=> 'yes',
				]
			);

			$this->add_control(
				'zoom',
				[
					'label' 	=> __( 'Zoom Map', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::NUMBER,
					'min' 		=> 1,
					'max' 		=> 20,
					'step' 		=> 1,
					'default' 	=> 4,
					'condition' => [
						'show_map' => 'yes'
					]
				]
			);

			$this->add_control(
				'marker_option',
				[
					'label'   		=> __( 'Marker Select', 'ova-brw' ),
					'description' 	=> __( 'You should use Icon to display exactly position', 'ova-brw' ),
					'type'    		=> Controls_Manager::SELECT,
					'default' 		=> 'icon',
					'options' 		=> [
						'icon' 		=> __('Icon', 'ova-brw'),
						'price' 	=> __('Price', 'ova-brw'),
						'date' 		=> __('Start Date', 'ova-brw'),
					],
				]
			);

			$this->add_control(
				'radius',
				[
					'label'   		=> __( 'Radius', 'ova-brw' ),
					'type'    		=> Controls_Manager::SELECT,
					'default' 		=> 'km',
					'options' 		=> [
						'km' => __('Kilometers', 'ova-brw'),
						'mi' => __('Mile', 'ova-brw'),
					],
				]
			);

			$this->add_control(
				'marker_icon',
				[
					'label' 	=> __( 'Choose Image', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::MEDIA,
					'condition' => [
						'marker_option' => 'icon'
					]
				]
			);

			$this->add_control(
				'field_1',
				[
					'label'   	=> __( 'Field 1', 'ova-brw' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

			$this->add_control(
				'field_2',
				[
					'label'   	=> __( 'Field 2', 'ova-brw' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

			$this->add_control(
				'field_3',
				[
					'label'   	=> __( 'Field 3', 'ova-brw' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

			$this->add_control(
				'field_4',
				[
					'label'   	=> __( 'Field 4', 'ova-brw' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

			$this->add_control(
				'field_5',
				[
					'label'   	=> __( 'Field 5', 'ova-brw' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

			$this->add_control(
				'field_6',
				[
					'label'   	=> __( 'Field 6', 'ova-brw' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

			$this->add_control(
				'field_7',
				[
					'label'   	=> __( 'Field 7', 'ova-brw' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

			$this->add_control(
				'field_8',
				[
					'label'   	=> __( 'Field 8', 'ova-brw' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

			$this->add_control(
				'field_9',
				[
					'label'   	=> __( 'Field 9', 'ova-brw' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

			$taxonomies 		= get_option( 'ovabrw_custom_taxonomy', array() );
			$data_taxonomy[''] 	= esc_html__( 'Select Taxonomy', 'ova-brw' );

			if( ! empty( $taxonomies ) && is_array( $taxonomies ) ) {
				foreach( $taxonomies as $key => $value ) {
					$data_taxonomy[$key] = $value['name'];
				}
			}

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'taxonomy_custom', [
					'label' 		=> __( 'Taxonomy Custom', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SELECT,
					'label_block' 	=> true,
					'options' 		=> $data_taxonomy,
				]
			);

			$repeater->add_control(
				'taxonomy_custom_label', [
					'label' => __( 'Label', 'ova-brw' ),
					'type' 	=> \Elementor\Controls_Manager::TEXT,
				]
			);

			$this->add_control(
				'list_taxonomy_custom',
				[
					'label' 	=> __( 'List Taxonomy Custom', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::REPEATER,
					'fields' 	=> $repeater->get_controls(),
					'condition' => [
						'show_filter' => 'yes'
					],
					'default' 	=> [
						'' => esc_html__( 'Select Taxonomy', 'ova-brw' ),
					],
					'title_field' => '{{{ taxonomy_custom }}}',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_map_style',
			[
				'label' => esc_html__( 'Map', 'ova-brw' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs( 'map_filter' );

				$this->start_controls_tab( 'normal',
					[
						'label' => esc_html__( 'Normal', 'ova-brw' ),
					]
				);

					$this->add_group_control(
						Group_Control_Css_Filter::get_type(),
						[
							'name' => 'css_filters',
							'selector' => '{{WRAPPER}} .elementor_search_map .wrap_search_map .wrap_map #show_map',
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab( 'hover',
					[
						'label' => esc_html__( 'Hover', 'ova-brw' ),
					]
				);

					$this->add_group_control(
						Group_Control_Css_Filter::get_type(),
						[
							'name' 		=> 'css_filters_hover',
							'selector' 	=> '{{WRAPPER}} .elementor_search_map .wrap_search_map .wrap_map #show_map:hover',
						]
					);

					$this->add_control(
						'hover_transition',
						[
							'label' => esc_html__( 'Transition Duration', 'ova-brw' ),
							'type' => Controls_Manager::SLIDER,
							'range' => [
								'px' => [
									'max' => 3,
									'step' => 0.1,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .elementor_search_map .wrap_search_map .wrap_map #show_map' => 'transition-duration: {{SIZE}}s',
							],
						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_filters_style',
			[
				'label' => esc_html__( 'Filters', 'ova-brw' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'width_filters',
				[
					'label' => esc_html__( 'Width', 'ova-brw' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ '%' ],
					'range' => [
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'default' => [
						'unit' => '%',
						'size' => 100,
					],
					'selectors' => [
						'{{WRAPPER}} .elementor_search_map .wrap_search_map .wrap_search .fields_search .form_search_map .wrap_content' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'text_align_filters',
				[
					'label' => esc_html__( 'Alignment', 'ova-brw' ),
					'type' => \Elementor\Controls_Manager::CHOOSE,
					'options' => [
						'left' => [
							'title' => esc_html__( 'Left', 'ova-brw' ),
							'icon' => 'eicon-text-align-left',
						],
						'center' => [
							'title' => esc_html__( 'Center', 'ova-brw' ),
							'icon' => 'eicon-text-align-center',
						],
						'right' => [
							'title' => esc_html__( 'Right', 'ova-brw' ),
							'icon' => 'eicon-text-align-right',
						],
					],
					'default' => 'center',
					'selectors' => [
						'{{WRAPPER}} .elementor_search_map .wrap_search_map .wrap_search .fields_search .form_search_map' => 'text-align: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings();

		$template = apply_filters( 'ovabrw_ft_element_search_map', 'single/search_map.php' );
		ovabrw_get_template( $template, $settings );
	}
}