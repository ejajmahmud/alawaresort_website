<?php if ( !defined( 'ABSPATH' ) ) exit();

class Ovabrw_Setting_Tab extends WC_Settings_Page {

    /**
     * Constructor
     */
    public function __construct() {

        $this->id    = 'ova_brw';

        add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 50 );
        add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
        add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
        add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );

        parent::__construct();

    }

    /**
     * Add plugin options tab
     *
     * @return array
     */
    public function add_settings_tab( $settings_tabs ) {
        $settings_tabs[$this->id] = esc_html__( 'Booking & Rental', 'ova-brw' );
        return $settings_tabs;
    }

    /**
     * Get sections
     *
     * @return array
     */
    public function get_sections() {

        $sections = array(
            ''                          => esc_html__( 'General', 'ova-brw' ),
            // 'archive_rental_setting'    => esc_html__( 'Product Archive', 'ova-brw' ),
            'detail_rental_setting'     => esc_html__( 'Product Details', 'ova-brw' ),
            'booking_form'              => esc_html__( 'Booking Form', 'ova-brw' ),
            'request_booking_form'      => esc_html__( 'Request Booking Form', 'ova-brw' ),
            // 'extra_tab'                 => esc_html__( 'Extra Tab', 'ova-brw' ),
            // 'search_setting'            => esc_html__( 'Search', 'ova-brw' ),
            'cancel_setting'            => esc_html__( 'Cancel Order', 'ova-brw' ),
            'reminder_setting'          => esc_html__( 'Reminder', 'ova-brw' ),
            'manage_order'              => esc_html__( 'Manage Order', 'ova-brw' ),
        );

        return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
    }


    /**
     * Output the settings
     */
    public function output() {
        global $current_section;
        $settings = $this->get_settings( $current_section );
        WC_Admin_Settings::output_fields( $settings );
    }


    /**
     * Save settings
     */
    public function save() {
        global $current_section;
        $settings = $this->get_settings( $current_section );
        WC_Admin_Settings::save_fields( $settings );
    }


    /**
     * Get sections
     *
     * @return array
     */
    public function get_settings( $section = null ) {

        if( apply_filters( 'ovabrw_add_custom_tax_by_code', false ) == true ){
            $total_custom_tax = array(
                        'name' => esc_html__( 'Total Custom Taxonomy', 'ova-brw' ),
                        'type' => 'number',
                        'default' => 0,
                        'desc' => esc_html__('Read documentation to know more', 'ova-brw'),
                        'id'   => 'ova_brw_number_taxonomy'
                    );    
        }else{
            $total_custom_tax = array();
        }
        

        switch( $section ){

            case '' :

                $settings = array(

                    array(
                        'title' => esc_html__( 'General', 'ova-brw' ),
                        'type'  => 'title',
                        'desc'  => '',
                        'id'    => 'ova_brw_general',
                    ),
                   
                    $total_custom_tax,
                    array(
                        'name' => esc_html__( 'Date Format', 'ova-brw' ),
                        'type' => 'select',
                        'options' => array(
                            'd-m-Y' => esc_html__( 'd-m-Y', 'ova-brw' ).' ('.date_i18n( 'd-m-Y', current_time('timestamp') ).')',
                            'm/d/Y' => esc_html__( 'm/d/Y', 'ova-brw' ).' ('.date_i18n( 'm/d/Y', current_time('timestamp') ).')',
                            'Y/m/d' => esc_html__( 'Y/m/d', 'ova-brw' ).' ('.date_i18n( 'Y/m/d', current_time('timestamp') ).')',
                            'Y-m-d' => esc_html__( 'Y-m-d', 'ova-brw' ).' ('.date_i18n( 'Y-m-d', current_time('timestamp') ).')',
                        ),
                        'default' => 'd-m-Y',
                        'id'   => 'ova_brw_booking_form_date_format'
                    ),

                    array(
                        'name' => esc_html__( 'Time Format', 'ova-brw' ),
                        'type' => 'select',
                        'options' => array(
                            '12' => esc_html__( '12 Hour (06:PM)', 'ova-brw' ),
                            '24' => esc_html__( '24 Hour (18:00)', 'ova-brw' ),
                        ),
                        'default' => '12',
                        'id'   => 'ova_brw_calendar_time_format',
                    ),


                    array(
                        'name' => esc_html__( 'Step Time', 'ova-brw' ),
                        'type' => 'number',
                        'class' => ' ',
                        'default' => '30',
                        'custom_attributes' => [
                            'min'   => '1'
                        ],
                        'desc' => esc_html__( 'Insert number like 15. Example: If you insert 15, time List for customers to choose will display: 07:15, 07:30, 07:45, 08:00', 'ova-brw' ),
                        'id'   => 'ova_brw_booking_form_step_time',
                    ),


                    array(
                        'name' => esc_html__( 'Language', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'ar' => esc_html__('Arabic', 'ova-brw'),
                            'az' => esc_html__('Azerbaijanian (Azeri)', 'ova-brw'),
                            'bg' => esc_html__('Bulgarian','ova-brw'),
                            'bs' => esc_html__('Bosanski','ova-brw'),
                            'ca' => esc_html__('Català','ova-brw'),
                            'ch' => esc_html__('Simplified Chinese','ova-brw'),
                            'cs' => esc_html__('Čeština','ova-brw'),
                            'da' => esc_html__('Dansk','ova-brw'),
                            'de' => esc_html__('German','ova-brw'),
                            'el' => esc_html__('Ελληνικά','ova-brw'),
                            'en' => esc_html__('English','ova-brw'),
                            'en-GB' => esc_html__('English (British) ','ova-brw'),
                            'es' => esc_html__('Spanish','ova-brw'),
                            'et' => esc_html__('Eesti','ova-brw'),
                            'eu' => esc_html__('Euskara','ova-brw'),
                            'fa' => esc_html__('Persian','ova-brw'),
                            'fi' => esc_html__('Finnish (Suomi)','ova-brw'),
                            'fr' => esc_html__('French','ova-brw'),
                            'gl' => esc_html__('Galego','ova-brw'),
                            'he' => esc_html__('Hebrew (עברית)','ova-brw'),
                            'hr' => esc_html__('Hrvatski','ova-brw'),
                            'hu' => esc_html__('Hungarian','ova-brw'),
                            'id' => esc_html__('Indonesian','ova-brw'),
                            'it' => esc_html__('Italian','ova-brw'),
                            'ja' => esc_html__('Japanese','ova-brw'),
                            'ko' => esc_html__('Korean (한국어)','ova-brw'),
                            'kr' => esc_html__('Korean','ova-brw'),
                            'lt' => esc_html__('Lithuanian (lietuvių) ','ova-brw'),
                            'lv' => esc_html__('Latvian (Latviešu)','ova-brw'),
                            'mk' => esc_html__('Macedonian (Македонски)','ova-brw'),
                            'mn' => esc_html__('Mongolian (Монгол)','ova-brw'),
                            'nl' => esc_html__('Dutch','ova-brw'),
                            'no' => esc_html__('Norwegian','ova-brw'),
                            'pl' => esc_html__('Polish','ova-brw'),
                            'pt' => esc_html__('Portuguese','ova-brw'),
                            'pt-BR' => esc_html__('Português(Brasil)','ova-brw'),
                            'ro' => esc_html__('Romanian','ova-brw'),
                            'ru' => esc_html__('Russian','ova-brw'),
                            'se' => esc_html__('Swedish','ova-brw'),
                            'sk' => esc_html__('Slovenčina','ova-brw'),
                            'sl' => esc_html__('Slovenščina','ova-brw'),
                            'sq' => esc_html__('Albanian (Shqip)','ova-brw'),
                            'sr' => esc_html__('Serbian Cyrillic (Српски)','ova-brw'),
                            'sr-YU' => esc_html__('Serbian (Srpski)','ova-brw'),
                            'sv' => esc_html__('Svenska','ova-brw'),
                            'th' => esc_html__('Thai','ova-brw'),
                            'tr' => esc_html__('Turkish','ova-brw'),
                            'uk' => esc_html__('Ukrainian','ova-brw'),
                            'vi' => esc_html__('Vietnamese','ova-brw'),
                            'zh' => esc_html__('Simplified Chinese (简体中文)','ova-brw'),
                            'zh-TW' => esc_html__('Traditional Chinese (繁體中文)','ova-brw'),
                        ],
                        'default' => 'en',
                        'desc' => esc_html__('Display in Calendar','ova-brw'),
                        'id'   => 'ova_brw_calendar_language_general'
                    ),

                    array(
                        'name' => esc_html__( 'Group of start time for booking', 'ova-brw' ),
                        'type' => 'text',
                        'class' => ' ',
                        'default' => '07:00, 07:30, 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 11:00, 11:30, 12:00, 12:30, 13:00, 13:30, 14:00, 14:30, 15:00, 15:30, 16:00, 16:30, 17:00, 17:30, 18:00',
                        'desc' => esc_html__('Insert time format: 24hour. Like 07:00, 07:30, 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 11:00, 11:30, 12:00, 12:30, 13:00, 13:30, 14:00, 14:30, 15:00, 15:30, 16:00, 16:30, 17:00, 17:30, 18:00','ova-brw'),
                        'id'   => 'ova_brw_calendar_time_to_book'
                    ),

                    array(
                        'name' => esc_html__( 'Default hour of start time', 'ova-brw' ),
                        'type' => 'text',
                        'class' => ' ',
                        'default' => '07:00',
                        'desc' => esc_html__('Insert time format: 24hour. Example: 06:00, 10:30, 14:00, 18:00', 'ova-brw'),
                        'id'   => 'ova_brw_booking_form_default_hour'
                    ),

                    array(
                        'name' => esc_html__( 'Group of end time for booking', 'ova-brw' ),
                        'type' => 'text',
                        'class' => ' ',
                        'default' => '07:00, 07:30, 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 11:00, 11:30, 12:00, 12:30, 13:00, 13:30, 14:00, 14:30, 15:00, 15:30, 16:00, 16:30, 17:00, 17:30, 18:00',
                        'desc' => esc_html__('Insert time format: 24hour. Like 07:00, 07:30, 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 11:00, 11:30, 12:00, 12:30, 13:00, 13:30, 14:00, 14:30, 15:00, 15:30, 16:00, 16:30, 17:00, 17:30, 18:00','ova-brw'),
                        'id'   => 'ova_brw_calendar_time_to_book_for_end_date'
                    ),

                    

                    array(
                        'name' => esc_html__( 'Default hour of end time', 'ova-brw' ),
                        'type' => 'text',
                        'class' => ' ',
                        'default' => '07:00',
                        'desc' => esc_html__('Insert time format: 24hour. Example: 06:00, 10:30, 14:00, 18:00', 'ova-brw'),
                        'id'   => 'ova_brw_booking_form_default_hour_end_date'
                    ),

                    array(
                        'name' => esc_html__( 'Unavailable Date for booking', 'ova-brw' ),
                        'type' => 'text',
                        'class' => ' ',
                        'default' => '',
                        'desc' => esc_html__('0: Sunday, 1: Monday, 2: Tuesday, 3: Wednesday, 4: Thursday, 5: Friday, 6: Saturday . Example: 0,6','ova-brw'),
                        'id'   => 'ova_brw_calendar_disable_week_day'
                    ),

                    array(
                        'name' => esc_html__( 'The day that each week begins', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            '1' => esc_html__('Monday', 'ova-brw'),
                            '2' => esc_html__('Tuesday', 'ova-brw'),
                            '3' => esc_html__('Wednesday', 'ova-brw'),
                            '4' => esc_html__('Thursday', 'ova-brw'),
                            '5' => esc_html__('Friday', 'ova-brw'),
                            '6' => esc_html__('Saturday ', 'ova-brw'),
                            '0' => esc_html__('Sunday', 'ova-brw'),
                        ],
                        'default' => '0',
                        'desc' => esc_html__('0: Sunday, 1: Monday, 2: Tuesday, 3: Wednesday, 4: Thursday, 5: Friday, 6: Saturday . Example: 0','ova-brw'),
                        'id'   => 'ova_brw_calendar_first_day'
                    ),

                    array(
                        'name' => esc_html__( 'Show Taxonomy depend Category', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'default' => 'yes',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'   => 'ova_brw_search_show_tax_depend_cat',
                    ),

                    array(
                        'name' => esc_html__( 'Google Key Map', 'ova-brw' ),
                        'type' => 'text',
                        'class' => '',
                        'default' => '',
                        'desc' => esc_html__('You can get here: https://developers.google.com/maps/documentation/javascript/get-api-key','ova-brw'),
                        'id'   => 'ova_brw_google_key_map'
                    ),

                    array(
                        'name' => esc_html__( 'Latitude Map default', 'ova-brw' ),
                        'type' => 'text',
                        'class' => 'ova_brw_latitude_map_default',
                        'default' => 39.177972,
                        'desc' => esc_html__('The default latitude of map when the event do not exist','ova-brw'),
                        'id'   => 'ova_brw_latitude_map_default'
                    ),

                    array(
                        'name' => esc_html__( 'Longitude Map default', 'ova-brw' ),
                        'type' => 'text',
                        'class' => 'ova_brw_longitude_map_default',
                        'default' => -100.363750,
                        'desc' => esc_html__('The default longitude of map when the event do not exist','ova-brw'),
                        'id'   => 'ova_brw_longitude_map_default'
                    ),

                    'section_end' => array(
                        'type' => 'sectionend',
                        'id' => 'ova_brw_end_general'
                    )
                );

                break;

            case 'detail_rental_setting' :

                // Get templates from elementor
                $templates = get_posts( array('post_type' => 'elementor_library', 'meta_key' => '_elementor_template_type', 'meta_value' => 'page' ) );
                
                $list_templates = array( 'default' => 'Default' );

                if( ! empty( $templates ) ) {
                    foreach( $templates as $template ) {
                        $id_template    = $template->ID;
                        $title_template = $template->post_title;
                        $list_templates[$id_template] = $title_template;
                    }
                }

                $settings = array(

                    array(
                        'title' => esc_html__( 'Product Details', 'ova-brw' ),
                        'type'  => 'title',
                        'desc'  => '',
                        'id'    => 'ova_brw_style_template',
                    ),

                    array(
                        'name' => esc_html__( 'Product Templates', 'ova-brw' ),
                        'type' => 'select',
                        'desc' => esc_html__( 'Default or Other (made in Templates of Elementor )', 'ova-brw' ),
                        'class' => '',
                        'options' => $list_templates,
                        'default' => 'default',
                        'id'   => 'ova_brw_template_elementor_template'
                    ),

                    array(
                        'name' => esc_html__( 'Show Booking Form', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_template_show_booking_form'
                    ),

                    array(
                        'name' => esc_html__( 'Show Request Booking Form', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_template_show_request_booking'
                    ),

                    array(
                        'name' => esc_html__( 'Show Extra Tab', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_template_show_extra_tab'
                    ),

                    array(
                        'name' => esc_html__( 'Show Feature', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_template_show_feature'
                    ),

                    array(
                        'name' => esc_html__( 'Show Price Table', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_template_show_table_price'
                    ),

                    array(
                        'name' => esc_html__( 'Always Open Price Table', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_template_show_open_table_price'
                    ),

                    array(
                        'name' => esc_html__( 'Show Unavailable Time', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_template_show_maintenance'
                    ),

                    array(
                        'name' => esc_html__( 'Show Calendar', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_template_show_calendar'
                    ),
                  

                    array(
                        'name' => esc_html__( 'Show Navigation Month (Calendar Full)', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_calendar_show_nav_month'
                    ),

                    array(
                        'name' => esc_html__( 'Show Navigation Week (Calendar Full)', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_calendar_show_nav_week'
                    ),

                    array(
                        'name' => esc_html__( 'Show Navigation Day (Calendar Full)', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_calendar_show_nav_day'
                    ),

                    array(
                        'name' => esc_html__( 'Show Navigation List (Calendar Full)', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_calendar_show_nav_list'
                    ),

                    array(
                        'name' => esc_html__( 'Default View (Calendar Full)', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'dayGridMonth' => esc_html__( 'Month', 'ova-brw' ),
                            'timeGridWeek' => esc_html__( 'Week', 'ova-brw' ),
                            'timeGridDay' => esc_html__( 'Day', 'ova-brw' ),
                            'listWeek' => esc_html__( 'List', 'ova-brw' ),
                        ],
                        'default' => 'dayGridMonth',
                        'id'   => 'ova_brw_calendar_default_view'
                    ),

                    array(
                        'name' => esc_html__( 'Show Booked Time in Calendar', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_template_show_time_in_calendar'
                    ),

                    array(
                        'name' => esc_html__( 'Color of unavailable date in calendar', 'ova-brw' ),
                        'type' => 'color',
                        'id'   => 'ova_brw_bg_calendar',
                        'default'   => '#f70707'
                    ),

                    'section_end' => array(
                        'type' => 'sectionend',
                        'id' => 'ova_brw_end_style_template'
                    )
                );            

                break;

            case 'archive_rental_setting':

                 $settings = array(

                    array(
                        'title' => esc_html__( 'Archive Product', 'ova-brw' ),
                        'type'  => 'title',
                        'desc'  => '',
                        'id'    => 'ova_brw_archive_product',
                    ),

                    array(
                        'name' => esc_html__( 'Show Features', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_archive_product_show_features'
                    ),

                    array(
                        'name' => esc_html__( 'Show Attributes', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_archive_product_show_attribute'
                    ),

                    
                    'section_end' => array(
                        'type' => 'sectionend',
                        'id' => 'ova_brw_end_archive_product'
                    )
                );   
                break;

            case 'booking_form':

                $settings = array(
                   
                    array(
                        'title' => esc_html__( 'Booking Form', 'ova-brw' ),
                        'type'  => 'title',
                        'id'    => 'ova_brw_booking_form',
                    ),

                    array(
                        'name' => esc_html__( 'Show Quantity', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_booking_form_show_number_vehicle'
                    ),

                    array(
                        'name' => esc_html__( 'Show Pick-up Location', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'no' => esc_html__( 'No', 'ova-brw' ),
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            
                        ],
                        'default' => 'no',
                        'id'   => 'ova_brw_booking_form_show_pickup_location'
                    ),

                    array(
                        'name' => esc_html__( 'Show Drop-off Location', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'no' => esc_html__( 'No', 'ova-brw' ),
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            
                        ],
                        'default' => 'no',
                        'id'   => 'ova_brw_booking_form_show_pickoff_location'
                    ),

                    array(
                        'name' => esc_html__( 'Show Pick-up Date', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_booking_form_show_pickup_date'
                    ),

                    array(
                        'name' => esc_html__( 'Show Drop-off Date', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_booking_form_show_dropoff_date'
                    ),

                    array(
                        'name' => esc_html__( 'Show Service', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_booking_form_show_extra_resource'
                    ),

                    array(
                        'name' => esc_html__( 'Show Extra Service', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_booking_form_show_extra_service'
                    ),

                    array(
                        'name' => esc_html__( 'Show Extra Service, Resource in Cart, Checkout, Order Detail', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'no',
                        'id'   => 'ova_brw_booking_form_show_extra'
                    ),

                    array(
                        'name' => esc_html__( 'Show Availables Vehicle', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_booking_form_show_availables_vehicle'
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'ova_brw_end_booking_form',
                    ),

                );
                break;

            case 'request_booking_form':

                $all_pages = get_pages();
                $list_page[''] = esc_html__( 'Select Page', 'ova-brw' );
                if( ! empty( $all_pages ) ) {
                    foreach( $all_pages as $page ) {
                    $id_page = $page->ID;
                    $title_page = $page->post_title;
                    $link_page = get_page_link( $id_page );

                    $list_page[$link_page] = $title_page;
                    }
                }

                $settings = array(
                   
                    array(
                        'title' => esc_html__( 'Request Booking Form in Tab', 'ova-brw' ),
                        'type'  => 'title',
                        'id'    => 'ova_brw_request_booking_form',
                    ),

                    

                    array(
                        'name' => esc_html__( 'Display Tab after Description or Review Tab', 'ova-brw' ),
                        'type' => 'text',
                        'class' => ' ',
                        'default' => '9',
                        'desc' => esc_html__( 'You can insert 9, 11, 31', 'ova-brw' ),
                        'id'   => 'ova_brw_request_booking_form_order_tab',
                    ),

                    array(
                        'name' => esc_html__( 'Thank Page', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => $list_page,
                        'id'   => 'ova_brw_request_booking_form_thank_page',
                    ),

                    array(
                        'name' => esc_html__( 'Error Page', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => $list_page,
                        'id'   => 'ova_brw_request_booking_form_error_page',
                    ),

                    array(
                        'name' => esc_html__( 'Show Number Phone', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_request_booking_form_show_number'
                    ),

                    array(
                        'name' => esc_html__( 'Show Address', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_request_booking_form_show_address'
                    ),

                    array(
                        'name' => esc_html__( 'Show Pick-up Location', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'no' => esc_html__( 'No', 'ova-brw' ),
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            
                        ],
                        'default' => 'no',
                        'id'   => 'ova_brw_request_booking_form_show_pickup_location'
                    ),

                    array(
                        'name' => esc_html__( 'Show Drop-off Location', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'no' => esc_html__( 'No', 'ova-brw' ),
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            
                        ],
                        'default' => 'no',
                        'id'   => 'ova_brw_request_booking_form_show_pickoff_location'
                    ),

                    array(
                        'name' => esc_html__( 'Show Quantity', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'no',
                        'id'   => 'ova_brw_request_booking_form_show_number_vehicle'
                    ),

                    array(
                        'name' => esc_html__( 'Show Pick-up Date', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_request_booking_form_show_pickup_date'
                    ),

                    array(
                        'name' => esc_html__( 'Show Drop-off Date', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_request_booking_form_show_pickoff_date'
                    ),

                    array(
                        'name' => esc_html__( 'Show Resource', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_request_booking_form_show_extra_service'
                    ),

                    array(
                        'name' => esc_html__( 'Show Service', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_request_booking_form_show_service'
                    ),

                    array(
                        'name' => esc_html__( 'Show Extra Info', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default' => 'yes',
                        'id'   => 'ova_brw_request_booking_form_show_extra_info'
                    ),

                   
                    array(
                        'type' => 'sectionend',
                        'id'   => 'ova_brw_request_booking_form',
                    ),
                  
                    array(
                        'title' => esc_html__( 'Email Settings', 'ova-brw' ),
                        'type'  => 'title',
                        'id'    => 'ova_brw_request_booking_mail_setting',
                    ),

                    array(
                        'name' => esc_html__( 'Subject', 'ova-brw' ),
                        'type' => 'text',
                        'desc' => esc_html__( 'The subject displays in the email list', 'ova-brw' ),
                        'default' => esc_html__( 'Request For Booking', 'ova-brw' ) ,
                        'id'   => 'ova_brw_request_booking_mail_subject',
                    ),

                    array(
                        'name' => esc_html__( 'From name', 'ova-brw' ),
                        'type' => 'text',
                        'desc' => esc_html__( 'The subject displays in mail detail', 'ova-brw' ),
                        'default' => esc_html__( 'Request For Booking', 'ova-brw' ) ,
                        'id'   => 'ova_brw_request_booking_mail_from_name',
                    ),

                    array(
                        'name' => esc_html__( 'Send from email', 'ova-brw' ),
                        'type' => 'text',
                        'desc' => esc_html__( 'The customer will know them to receive mail from which email address is', 'ova-brw' ),
                        'default' => get_option( 'admin_email' ),
                        'id'   => 'ova_brw_request_booking_mail_from_email',
                    ),

                    array(
                        'name' => esc_html__( 'Email Content', 'ova-brw' ),
                        'type' => 'textarea',
                        'desc' => esc_html__( 'Use tags to generate email template. For example: You have hired a vehicle: [ovabrw_vehicle_name] at [ovabrw_order_pickup_date] to [ovabrw_order_pickoff_date]. [ovabrw_order_details]', 'ova-brw' ),
                        'default' => esc_html__( 'You have hired a vehicle: [ovabrw_vehicle_name] at [ovabrw_order_pickup_date] to [ovabrw_order_pickoff_date]. [ovabrw_order_details]', 'ova-brw' ),
                        'id'   => 'ova_brw_request_booking_mail_content',
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'ova_brw_request_booking_mail_setting',
                    ),
                );
                break;

            case 'extra_tab':

                $settings = array(
                   
                    array(
                        'title' => esc_html__( 'Extra Tab in WooCommerce', 'ova-brw' ),
                        'type'  => 'title',
                        'id'    => 'ova_brw_extra_tab',
                    ),

                    
                   

                    array(
                        'name' => esc_html__( 'Display Tab after Description or Review Tab', 'ova-brw' ),
                        'type' => 'text',
                        'class' => ' ',
                        'default' => '30',
                        'desc' => esc_html__( 'You can insert 9, 11, 21, 31', 'ova-brw' ),
                        'id'   => 'ova_brw_extra_tab_order_tab',
                    ),

                    array(
                        'name' => esc_html__( 'Display Shortcode ', 'ova-brw' ),
                        'type' => 'textarea',
                        'class' => ' ',
                        'desc' => esc_html__( 'You can insert any shortcode here.', 'ova-brw' ),
                        'id'   => 'ova_brw_extra_tab_shortcode_form',
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'ova_brw_end_extra_tab',
                    ),

                );
                break;

            case 'search_setting':

                $settings = array(
                   
                    array(
                        'title' => esc_html__( 'Search Setting', 'ova-brw' ),
                        'type'  => 'title',
                        'desc' => esc_html__( 'When you use [ovabrw_search /] and don\'t insert params, the shortcode will use value here ', 'ova-brw' ),
                        'id'    => 'ova_brw_search_setting',
                    ),

                    
                    array(
                        'name' => esc_html__( 'Column', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'default' => 'one-column',
                        'options' => [
                            'one-column' => esc_html__( 'One Column', 'ova-brw' ),
                            'two-column' => esc_html__( 'Two Column', 'ova-brw' ),
                            'three-column' => esc_html__( 'Three Column', 'ova-brw' ),
                            'four-column' => esc_html__( 'Four Column', 'ova-brw' ),
                            'five-column' => esc_html__( 'Five Column', 'ova-brw' ),
                        ],
                        'id'   => 'ova_brw_search_column',
                    ),

                    array(
                        'name' => esc_html__( 'Show Product Name', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'default' => 'yes',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'   => 'ova_brw_search_show_name_product',
                    ),

                    array(
                        'name' => esc_html__( 'Show Attribute', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'default' => 'yes',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'   => 'ova_brw_search_show_attribute',
                    ),

                    array(
                        'name' => esc_html__( 'Show Product Tag', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'default' => 'yes',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'   => 'ova_brw_search_show_tag_product',
                    ),

                    array(
                        'name' => esc_html__( 'Show Pick-up Location', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'default' => 'yes',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'   => 'ova_brw_search_show_pick_up_location',
                    ),


                    array(
                        'name' => esc_html__( 'Show Drop-off Location', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'default' => 'yes',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'   => 'ova_brw_search_show_drop_off_location',
                    ),


                    array(
                        'name' => esc_html__( 'Show Pick-up Date', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'default' => 'yes',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'   => 'ova_brw_search_show_pick_up_date',
                    ),

                    array(
                        'name' => esc_html__( 'Show Drop-off Date', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'default' => 'yes',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'   => 'ova_brw_search_show_drop_off_date',
                    ),

                    array(
                        'name' => esc_html__( 'Show Category', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'default' => 'yes',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'   => 'ova_brw_search_show_category',
                    ),

                    

                    array(
                        'name' => esc_html__( 'Remove Categories in dropdown', 'ova-brw' ),
                        'type' => 'text',
                        'default' => '',
                        'desc' => esc_html__( 'Insert ID of category. Example 42, 15', 'ova-brw' ),
                        'id'   => 'ova_brw_search_cat_remove'
                    ),

                    array(
                        'name' => esc_html__( 'Show Taxonomy', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'default' => 'yes',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'   => 'ova_brw_search_show_taxonomy',
                    ),


                    array(
                        'name' => esc_html__( 'Hide Taxonomy List', 'ova-brw' ),
                        'type' => 'textarea',
                        'class' => ' ',
                        'desc'  => esc_html__( 'Insert slug here and separated by ","', 'ova-brw' ),
                        'id'   => 'ova_brw_search_hide_taxonomy_slug'
                    ),

                    

                    array(
                        'name' => esc_html__( 'Require Product Name', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'default' => 'no',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'   => 'ova_brw_search_require_name_product',
                    ),

                    array(
                        'name' => esc_html__( 'Require Attribute', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'default' => 'no',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'   => 'ova_brw_search_require_attribute',
                    ),

                    array(
                        'name' => esc_html__( 'Require Product Tag', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'default' => 'no',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'   => 'ova_brw_search_require_tag_product',
                    ),


                    array(
                        'name' => esc_html__( 'Require Pick-up Location', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'default' => 'no',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'   => 'ova_brw_search_require_pick_up_location',
                    ),


                    array(
                        'name' => esc_html__( 'Require Drop-off Location', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'default' => 'no',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'   => 'ova_brw_search_require_drop_off_location',
                    ),

                    array(
                        'name' => esc_html__( 'Require Pick-up Date', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'default' => 'no',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'   => 'ova_brw_search_require_pick_up_date',
                    ),

                    array(
                        'name' => esc_html__( 'Require Drop-off Date', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'default' => 'no',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'   => 'ova_brw_search_require_drop_off_date',
                    ),

                    array(
                        'name' => esc_html__( 'Require Category', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'default' => 'no',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'   => 'ova_brw_search_require_category',
                    ),

                    array(
                        'name' => esc_html__( 'Require Taxonomy List', 'ova-brw' ),
                        'type' => 'textarea',
                        'class' => ' ',
                        'desc'  => esc_html__( 'Insert slug here and separated by "," ','ova-brw' ),
                        'id'   => 'ova_brw_search_require_taxonomy_slug'
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'ova_brw_end_search_setting',
                    ),

                );
                break;
        
            case 'cancel_setting':

                $settings = array(
                   
                    array(
                        'title' => esc_html__( 'Cancel Order', 'ova-brw' ),
                        'type'  => 'title',
                        'id'    => 'ova_brw_cancel_setting',
                    ),

                    array(
                        'name' => esc_html__( 'Cancellation is accepted before x hours', 'ova-brw' ),
                        'type' => 'number',
                        'class' => ' ',
                        'default' => '0',
                        'id'   => 'ova_brw_cancel_before_x_hours',
                    ),

                    array(
                        'name' => esc_html__( 'Cancellation is accepted if the total order is less than x amount', 'ova-brw' ),
                        'type' => 'text',
                        'class' => '',
                        'default' => 1,
                        'id'   => 'ova_brw_cancel_condition_total_order',
                    ),
                     array(
                        'type' => 'sectionend',
                        'id'   => 'ova_brw_end_cancel_setting',
                    ),

                    
                );
                break;

            case 'reminder_setting':

                $settings = array(
                   
                    array(
                        'title' => esc_html__( 'Reminder', 'ova-brw' ),
                        'type'  => 'title',
                        'id'    => 'ova_brw_reminder_setting',
                    ),

                     array(
                        'name' => esc_html__( 'Enable', 'ova-brw' ),
                        'type' => 'select',
                        'class' => ' ',
                        'desc'  => esc_html__( 'Allow to send mail to customer', 'ova-brw' ),
                        'default' => 'yes',
                        'options' => [
                            'yes' => esc_html__( 'Yes', 'ova-brw' ),
                            'no' => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'   => 'remind_mail_enable',
                    ),

                    array(
                        'name' => esc_html__( 'Before x day', 'ova-brw' ),
                        'type' => 'text',
                        'class' => '',
                        'default' => 1,
                        'id'   => 'remind_mail_before_xday',
                    ),

                    array(
                        'name' => esc_html__( 'Send a mail every x seconds', 'ova-brw' ),
                        'type' => 'number',
                        'class' => '',
                        'default' => 86400,
                        'id'   => 'remind_mail_send_per_seconds',
                    ),

                    array(
                        'name' => esc_html__( 'Subject', 'ova-brw' ),
                        'type' => 'text',
                        'desc' => esc_html__( 'The subject displays in the email list', 'ova-brw' ),
                        'default' => esc_html__( 'Remind Pick-up date', 'ova-brw' ) ,
                        'id'   => 'reminder_mail_subject',
                    ),

                    array(
                        'name' => esc_html__( 'From name', 'ova-brw' ),
                        'type' => 'text',
                        'desc' => esc_html__( 'The subject displays in mail detail', 'ova-brw' ),
                        'default' => esc_html__( 'Remind Pick-up date', 'ova-brw' ) ,
                        'id'   => 'reminder_mail_from_name',
                    ),

                    array(
                        'name' => esc_html__( 'Send from email', 'ova-brw' ),
                        'type' => 'text',
                        'desc' => esc_html__( 'The customer will know them to receive mail from which email address is', 'ova-brw' ),
                        'default' => get_option( 'admin_email' ),
                        'id'   => 'reminder_mail_from_email',
                    ),

                    array(
                        'name' => esc_html__( 'Email Content', 'ova-brw' ),
                        'type' => 'textarea',
                        'desc' => esc_html__( 'Use tags to generate email template. For example: You have hired a vehicle: [ovabrw_vehicle_name] at [ovabrw_order_pickup_date]', 'ova-brw' ),
                        'default' => esc_html__( 'You have hired a vehicle: [ovabrw_vehicle_name] at [ovabrw_order_pickup_date]', 'ova-brw' ),
                        'id'   => 'reminder_mail_content',
                    ),
                    
                     array(
                        'type' => 'sectionend',
                        'id'   => 'reminder_end_setting',
                    ),

                    
                );
                break;

            case 'manage_order':

                $settings = array(
                   
                    array(
                        'title' => esc_html__( 'Admin Settings', 'ova-brw' ),
                        'type'  => 'title',
                        'id'    => 'ova_brw_admin_manage_order_setting',
                        'desc'  => esc_html__( 'The fields are sorted ascending. To hide the field, enter the number: 0 or empty', 'ova-brw' ),
                    ),

                    array(
                        'name'      => esc_html__( 'Show ID', 'ova-brw' ),
                        'type'      => 'number',
                        'class'     => '',
                        'default'   => 1,
                        'id'        => 'admin_manage_order_show_id',
                    ),

                    array(
                        'name'      => esc_html__( 'Show Customer', 'ova-brw' ),
                        'type'      => 'number',
                        'class'     => '',
                        'default'   => 2,
                        'id'        => 'admin_manage_order_show_customer',
                    ),

                    array(
                        'name'      => esc_html__( 'Show Time', 'ova-brw' ),
                        'type'      => 'number',
                        'class'     => '',
                        'default'   => 3,
                        'id'        => 'admin_manage_order_show_time',
                    ),

                    array(
                        'name'      => esc_html__( 'Show Location', 'ova-brw' ),
                        'type'      => 'number',
                        'class'     => '',
                        'default'   => 4,
                        'id'        => 'admin_manage_order_show_location',
                    ),

                    array(
                        'name'      => esc_html__( 'Show Deposit Status', 'ova-brw' ),
                        'type'      => 'number',
                        'class'     => '',
                        'default'   => 5,
                        'id'        => 'admin_manage_order_show_deposit',
                    ),

                    array(
                        'name'      => esc_html__( 'Show Insurance Status', 'ova-brw' ),
                        'type'      => 'number',
                        'class'     => '',
                        'default'   => 6,
                        'id'        => 'admin_manage_order_show_insurance',
                    ),

                    array(
                        'name'      => esc_html__( 'Show Vehicle', 'ova-brw' ),
                        'type'      => 'number',
                        'class'     => '',
                        'default'   => 7,
                        'id'        => 'admin_manage_order_show_vehicle',
                    ),
                    
                    array(
                        'name'      => esc_html__( 'Show Product', 'ova-brw' ),
                        'type'      => 'number',
                        'class'     => '',
                        'default'   => 8,
                        'id'        => 'admin_manage_order_show_product',
                    ),

                    array(
                        'name'      => esc_html__( 'Show Order Status', 'ova-brw' ),
                        'type'      => 'number',
                        'class'     => '',
                        'default'   => 9,
                        'id'        => 'admin_manage_order_show_order_status',
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'ova_brw_admin_manage_order_setting',
                    ),  
                ); 
                break;

        }

        return apply_filters( 'wc_settings_tab_ova-brw_settings', $settings, $section );
    }
}
return new Ovabrw_Setting_Tab();
