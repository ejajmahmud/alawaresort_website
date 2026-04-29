<?php if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Entox_Customize_WCFM' ) ) {

	class Entox_Customize_WCFM {

		public function __construct() {

	        if ( apply_filters( 'is_active_wcfm_plugin', true ) ) {

			    //  Remove wcfm filter
			    $entox_ft_false = array(
			        'wcfm_product_manage_fields_content',           // ft - Remove Description and Short product
			        'wcfm_is_category_checklist',                   // ft - Remove Category product
			        'wcfm_is_allow_category',                       // ft - Remove Category product
			        'wcfm_is_allow_product_category',               // ft - Remove Category product
			        'wcfm_is_allow_custom_taxonomy',                // ft - Remove Taxonomy product
			        'wcfm_product_manage_fields_pricing',           // ft - Remove product manage fields general
			        'wcfm_is_allow_inventory',                      // ft - Remove product inventory
			        'wcfmu_is_allow_downloadable',                  // ft - Remove product downloadable
			        'wcfm_is_allow_shipping',                       // ft - Remove product shipping
			        'wcfm_is_allow_tax',                            // ft - Remove product tax
			        'wcfm_is_allow_attribute',                      // ft - Remove product attribute
			        'wcfm_is_allow_variable',                       // ft - Remove product variable
			        'wcfm_is_allow_featured',						// ft - Remove product thumbnail
			    );

			    foreach ( $entox_ft_false as $ft_false ) {
			        add_filter( $ft_false, '__return_false' );
			    }
			    //End: Remove wcfm filter

			    // Save taxonomy
			    // add_filter( 'wcfm_product_data_factory', array( $this, 'entox_wcfm_product_data_factory' ), 10, 4 );

			    // ft - Product manage fields general
			    add_filter( 'wcfm_product_manage_fields_general', array( $this, 'entox_ft_product_manage_fields_general' ), 20, 5 ); 

			    // hook - Categories
			    add_action( 'after_wcfm_products_manage_pricing_fields', array( $this, 'entox_hook_product_thumbnail' ), 10, 1 );

			    // hook - Categories
			    add_action( 'after_wcfm_products_manage_pricing_fields', array( $this, 'entox_hook_categories' ), 10, 1 );

			    // hook - Product Description and Short Description      
			    add_action( 'after_wcfm_products_manage_pricing_fields', array( $this, 'entox_hook_products_content' ), 30, 1 );

			    // hook - Save Categories & Taxonomy
			    add_action( 'after_wcfm_ajax_controller', array( $this, 'entox_ft_return_true' ) );

			    // Add begin class ova-wrapper-wcfm-product
			    add_action( 'before_wcfm_product_simple', array( $this, 'entox_before_wrapper_wcfm_product' ) );

			    // Add end class ova-wrapper-wcfm-product
			    add_action( 'after_wcfm_products_manage', array( $this, 'entox_after_wrapper_wcfm_product' ) );
			}
	    }

	    function entox_ft_product_manage_fields_general( $data, $products, $product_type, $wcfm_is_translated_product, $wcfm_wpml_edit_disable_element ) {

		    global $wp, $WCFM, $wc_product_attributes;

		    $title = $is_downloadable = '';

		    if ( isset( $wp->query_vars['wcfm-products-manage'] ) && ! empty( $wp->query_vars['wcfm-products-manage'] ) ) {

		        $product = wc_get_product( $wp->query_vars['wcfm-products-manage'] );

		        if ( $product && ! empty( $product ) ) {

		            $product_id     = $product->get_id();
		            $title          = $product->get_title( 'edit' );
		            $product_type   = $product->get_type();

		            $wcfm_downloadable_product_types = apply_filters( 'wcfm_downloadable_product_types', array( 'simple', 'subscription' ) );
		            $is_downloadable = ( get_post_meta( $product_id, '_downloadable', true) == 'yes' ) ? 'enable' : '';
		            if ( !in_array( $product_type, $wcfm_downloadable_product_types ) ) $is_downloadable = '';
		        }
		    }

		    $product_types = apply_filters( 'wcfm_product_types', array(
		        'simple'    => esc_html__('Simple Product', 'entox'), 
		        'variable'  => esc_html__('Variable Product', 'entox'), 
		        'grouped'   => esc_html__('Grouped Product', 'entox'), 
		        'external'  => esc_html__('External/Affiliate Product', 'entox') 
		    ) );

		    $product_type_class = '';
		    if ( count( $product_types ) == 0 ) {
		        $product_types = array('simple' => esc_html__('Simple Product', 'entox') );
		        $product_type_class = 'wcfm_custom_hide';
		    } elseif ( count( $product_types ) == 1 ) {
		        $product_type_class = 'wcfm_custom_hide';
		    }

		    if ( apply_filters( 'entox_set_product_types', true ) ) {
		        $product_types = array( 'ovabrw_car_rental' => esc_html__( 'Rental', 'entox' ) );
		    }

		    $args = array(

		        "product_type" => array(
		            'type'    => 'select', 
		            'options' => $product_types, 
		            'class'   => 'wcfm-select wcfm_ele wcfm_product_type wcfm_full_ele simple variable external grouped booking ' . $product_type_class . ' ' . $wcfm_wpml_edit_disable_element, 
		            'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $product_type 
		        ),

		        "is_downloadable" => array(
		            'desc'  => esc_html__('Downloadable', 'entox') , 
		            'type'  => 'checkbox', 
		            'class' => 'wcfm-checkbox wcfm_ele wcfm_half_ele_checkbox simple appointment non-variable-subscription non-job_package non-resume_package non-redq_rental non-accommodation-booking non-pw-gift-card' . ' ' . $wcfm_wpml_edit_disable_element, 
		            'desc_class' => 'wcfm_title wcfm_ele downloadable_ele_title checkbox_title simple appointment non-variable-subscription non-job_package non-resume_package non-redq_rental non-accommodation-booking non-pw-gift-card' . ' ' . $wcfm_wpml_edit_disable_element, 
		            'value' => 'enable', 
		            'dfvalue' => $is_downloadable
		        ),

		        "pro_title" => array( 
		            'placeholder' => esc_html__('Product Title', 'entox') , 
		            'type'  => 'text', 
		            'class' => 'wcfm-text wcfm_ele wcfm_product_title wcfm_full_ele simple variable external grouped booking', 
		            'value' => $title
		        ),
		    );

		    return $args;
		}

		function entox_ft_return_true() {

		    $entox_ft_true = array(
		        'wcfm_is_allow_category',
		        'wcfm_is_allow_product_category',
		        'wcfm_is_allow_custom_taxonomy',
		        'wcfm_is_allow_featured'
		    );

		    foreach ( $entox_ft_true as $ft_true ) {
		        add_filter( $ft_true, '__return_true' );
		    }
		}

		function entox_hook_product_thumbnail( $product_id ) {
			global $wp, $WCFM, $wc_product_attributes;

			$product = false;
			if ( isset( $wp->query_vars['wcfm-products-manage'] ) && $wp->query_vars['wcfm-products-manage'] ) {
				$product = wc_get_product( $wp->query_vars['wcfm-products-manage'] );
			}

			$gallery_img_ids 	= array();
			$gallery_img_urls 	= array();
			$featured_img 		= '';
			$gallerylimit 		= apply_filters( 'wcfm_gallerylimit', -1 );

			if ( $product && !empty( $product ) ) {
				$featured_img 		= ($product->get_image_id()) ? $product->get_image_id() : '';
				$gallery_img_ids 	= $product->get_gallery_image_ids();

				if ( ! empty( $gallery_img_ids ) ) {
					foreach( $gallery_img_ids as $gallery_img_id ) {
						$gallery_img_urls[]['gimage'] = $gallery_img_id;
					}
				}
			}

			$WCFM->wcfm_fields->wcfm_generate_form_field( 
				apply_filters( 
					'wcfm_product_manage_fields_images', 
					array(  
						"featured_img" => array( 
							'type' 			=> 'upload', 
							'class' 		=> 'wcfm-product-feature-upload wcfm_ele simple variable external grouped booking', 
							'label_class' 	=> 'wcfm_title', 
							'prwidth' 		=> 250, 
							'value' 		=> $featured_img ),
						"gallery_img"  => array( 
							'type' 				=> 'multiinput', 
							'class' 			=> 'wcfm-text wcfm-gallery_image_upload wcfm_ele simple variable external grouped booking', 
							'label_class' 		=> 'wcfm_title', 
							'custom_attributes' => array( 'limit' => $gallerylimit ), 
							'value' 			=> $gallery_img_urls, 
							'options' 			=> array( 
								"gimage" => array( 
									'type' => 'upload', 
									'class' => 'wcfm_gallery_upload', 
									'prwidth' => 75 ),
							) 
						) 
					), 
				$gallery_img_urls )
			);
		}

		function entox_hook_categories( $product_id ) {

		    global $wp, $WCFM, $wc_product_attributes;

		    // Product Categories
		    $product_categories = get_terms( 'product_cat', 'orderby=name&hide_empty=0&parent=0' );

		    $categories  = array();
		    $pcategories = get_the_terms( $product_id, 'product_cat' );
		    $catlimit    = apply_filters( 'wcfm_catlimit', -1 );

		    if ( ! empty( $pcategories ) ) {
		        foreach ( $pcategories as $pkey => $pcategory ) {
		            $categories[] = $pcategory->term_id;
		        }
		    } else {
		        $categories = array();
		    }

		    ?>
		    <div class="wcfm_clearfix"></div>
		    <div class="entox_product_manager_cats_checklist_fields">
		        <div class="wcfm_product_manager_cats_checklist_fields">
		            <p class="wcfm_title wcfm_full_ele">
		                <strong>
		                    <?php echo apply_filters( 'wcfm_taxonomy_custom_label', esc_html__( 'Categories', 'entox' ), 'product_cat' ); ?>
		                </strong>
		            </p>
		            <label class="screen-reader-text" for="product_cats">
		                <?php echo apply_filters( 'wcfm_taxonomy_custom_label', esc_html__( 'Categories', 'entox' ), 'product_cat' ); ?>
		            </label>
		            <ul id="product_cats_checklist" 
		                class="product_taxonomy_checklist product_taxonomy_checklist_product_cat wcfm_ele simple variable external grouped booking" 
		                data-catlimit="<?php echo esc_attr( $catlimit ); ?>">
		                <?php
		                    if ( $product_categories ) {
		                        $WCFM->library->generateTaxonomyHTML( 'product_cat', $product_categories, $categories, '', true );
		                    }
		                ?>
		            </ul>
		        </div>
		    <div class="wcfm_clearfix"></div>
		    <?php

		    if ( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
		        if ( apply_filters( 'wcfm_is_allow_add_category', true ) && apply_filters( 'wcfm_is_allow_add_taxonomy', true ) ) {
		            ?>
		            <div class="wcfm_add_new_category_box wcfm_add_new_taxonomy_box">
		                <p class="description wcfm_full_ele wcfm_side_add_new_category wcfm_add_new_category wcfm_add_new_taxonomy">+
		                    <?php _e( 'Add new category', 'entox' ); ?>
		                </p>
		                <div class="wcfm_add_new_taxonomy_form wcfm_add_new_taxonomy_form_hide">
		                    <?php 

		                    $WCFM->wcfm_fields->wcfm_generate_form_field( 
		                        array( 
		                            "wcfm_new_cat" => array( 
		                                'placeholder' => esc_html__( 'Category Name', 'entox' ), 
		                                'type' => 'text', 
		                                'class' => 'wcfm-text wcfm_new_tax_ele wcfm_full_ele' 
		                            ) 
		                        ) 
		                    );

		                    if ( apply_filters( 'wcfm_is_allow_add_new_product_cat_parent', true ) ) {
		                        $args = apply_filters( 'wcfm_wp_dropdown_categories_args', array(
		                            'show_option_all'    => '',
		                            'show_option_none'   => esc_html__( '-- Parent category --', 'entox' ),
		                            'option_none_value'  => '0',
		                            'hide_empty'         => 0,
		                            'hierarchical'       => 1,
		                            'name'               => 'wcfm_new_parent_cat',
		                            'class'              => 'wcfm-select wcfm_new_parent_taxt_ele wcfm_full_ele',
		                            'taxonomy'           => 'product_cat',
		                            ), 
		                            'product_cat' 
		                        );
		                        wp_dropdown_categories( $args );
		                    }
		                    ?>
		                    <button type="button" data-taxonomy="product_cat" class="button wcfm_add_category_bt wcfm_add_taxonomy_bt">
		                        <?php _e( 'Add', 'entox' ); ?>
		                    </button>
		                    <div class="wcfm_clearfix"></div>
		                </div>
		            </div>
		            <div class="wcfm_clearfix"></div>
		            <?php
		        }
		    }

		    $product_taxonomies = get_object_taxonomies( 'product', 'objects' );
		    $product = false;
		    if ( isset( $wp->query_vars['wcfm-products-manage'] ) && $wp->query_vars['wcfm-products-manage'] ) {
		    	$product = wc_get_product( $wp->query_vars['wcfm-products-manage'] );
		    }

		    $product_taxonomies = get_object_taxonomies( 'product', 'objects' );

		    ?>
		    <div class="entox_taxonomy">
		    <?php

		    if ( !empty( $product_taxonomies ) ) {
		        foreach ( $product_taxonomies as $product_taxonomy ) {
		            if ( ! in_array( $product_taxonomy->name, array( 'product_cat', 'product_tag', 'wcpv_product_vendors' ) ) && apply_filters( 'wcfm_is_allow_product_taxonomy', true, $product_taxonomy->name ) && apply_filters( 'wcfm_is_allow_taxonomy_'.$product_taxonomy->name, true ) ) {
		                if ( $product_taxonomy->public && $product_taxonomy->show_ui && $product_taxonomy->meta_box_cb && $product_taxonomy->hierarchical ) {
		                    if ( apply_filters( 'wcfm_is_allow_custom_taxonomy_'.$product_taxonomy->name, true ) ) {
		                        // Fetching Saved Values
		                        $taxonomy_values_arr = array();
		                        if ( $product && ! empty( $product ) ) {
		                            $taxonomy_values = get_the_terms( $product_id, $product_taxonomy->name );
		                            if ( ! empty( $taxonomy_values ) ) {
		                                foreach ( $taxonomy_values as $pkey => $ptaxonomy ) {
		                                    $taxonomy_values_arr[] = $ptaxonomy->term_id;
		                                }
		                            }
		                        }
		                        
		                        $taxonomy_limit = apply_filters( 'wcfm_taxonomy_limit', -1, $product_taxonomy->name );
		                        ?>

		                        <div class="wcfm_product_manager_cats_checklist_fields wcfm_product_taxonomy_<?php echo esc_attr( $product_taxonomy->name ); ?>">
		                            <p class="wcfm_title wcfm_full_ele">
		                                <strong>
		                                    <?php echo apply_filters( 'wcfm_taxonomy_custom_label', $product_taxonomy->label, $product_taxonomy->name ); ?>
		                                </strong>
		                            </p>
		                            <label class="screen-reader-text" for="<?php echo esc_attr( $product_taxonomy->name ); ?>"><?php echo apply_filters( 'wcfm_taxonomy_custom_label', $product_taxonomy->label, $product_taxonomy->name ); ?>
		                            </label>
		                            <ul id="<?php echo esc_attr( $product_taxonomy->name ); ?>" 
		                                class="product_taxonomy_checklist product_custom_taxonomy_checklist product_taxonomy_checklist_<?php echo esc_attr( $product_taxonomy->name ); ?> wcfm_ele simple variable external grouped booking" 
		                                data-catlimit="<?php echo esc_attr( $taxonomy_limit ); ?>">

		                                <?php
		                                    $product_taxonomy_terms = get_terms( $product_taxonomy->name, 'orderby=name&hide_empty=0&parent=0' );
		                                    if ( $product_taxonomy_terms ) {
		                                        $WCFM->library->generateTaxonomyHTML( $product_taxonomy->name, $product_taxonomy_terms, $taxonomy_values_arr, '', true, true );
		                                    }
		                                ?>
		                            </ul>
		                        </div>

		                        <?php

		                        if ( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
		                            if ( apply_filters( 'wcfm_is_allow_add_taxonomy', true ) && apply_filters( 'wcfm_is_allow_product_add_taxonomy', true, $product_taxonomy->name ) && apply_filters( 'wcfm_is_allow_add_'.$product_taxonomy->name, true ) ) {
		                                ?>
		                                <div class="wcfm_add_new_category_box wcfm_add_new_taxonomy_box">
		                                    <p class="description wcfm_full_ele wcfm_side_add_new_category wcfm_add_new_category wcfm_add_new_taxonomy">+
		                                        <?php echo esc_html__( 'Add new', 'entox' ) . ' ' . apply_filters( 'wcfm_taxonomy_custom_label', $product_taxonomy->label, $product_taxonomy->name ); ?>
		                                    </p>
		                                    <div class="wcfm_add_new_taxonomy_form wcfm_add_new_taxonomy_form_hide">
		                                        <?php 

		                                        $WCFM->wcfm_fields->wcfm_generate_form_field( 
		                                            array( 
		                                                "wcfm_new_".$product_taxonomy->name => array( 
		                                                    'placeholder'   =>  apply_filters( 'wcfm_add_taxonomy_custom_label', $product_taxonomy->label, $product_taxonomy->name ) . ' ' . esc_html__( 'Name', 'entox' ), 
		                                                    'type'          => 'text', 
		                                                    'class'         => 'wcfm-text wcfm_new_tax_ele wcfm_full_ele' 
		                                                ) 
		                                            ) 
		                                        );

		                                        if ( apply_filters( 'wcfm_is_allow_add_new_'.$product_taxonomy->name.'_parent', true ) ) {
		                                            $args = apply_filters( 'wcfm_wp_dropdown_'.$product_taxonomy->name.'_args', 
		                                                array(
		                                                    'show_option_all'    => '',
		                                                    'show_option_none'   => esc_html__( '-- Parent taxonomy --', 'entox' ),
		                                                    'option_none_value'  => '0',
		                                                    'hide_empty'         => 0,
		                                                    'hierarchical'       => 1,
		                                                    'name'               => 'wcfm_new_parent_'.$product_taxonomy->name,
		                                                    'class'              => 'wcfm-select wcfm_new_parent_taxt_ele wcfm_full_ele',
		                                                    'taxonomy'           => $product_taxonomy->name,
		                                                ), 
		                                                $product_taxonomy->name );
		                                            wp_dropdown_categories( $args );
		                                        }
		                                        ?>
		                                        <button type="button" 
		                                                data-taxonomy="<?php echo esc_attr( $product_taxonomy->name ); ?>" 
		                                                class="button wcfm_add_category_bt wcfm_add_taxonomy_bt">
		                                                <?php _e( 'Add', 'entox' ); ?>
		                                        </button>
		                                        <div class="wcfm_clearfix"></div>
		                                    </div>
		                                </div>
		                                <div class="wcfm_clearfix"></div>
		                                <?php
		                            }
		                        }
		                    }
		                }
		            }
		        }
		    }
		    ?>
		    </div></div>
		    <?php
		}

		function entox_hook_products_content( $product_id ) {

		    global $wp, $WCFM, $wc_product_attributes;

		    $product_type = apply_filters( 'wcfm_default_product_type', '' );

		    $excerpt = $description = '';
		    if ( isset( $wp->query_vars['wcfm-products-manage'] ) && ! empty( $wp->query_vars['wcfm-products-manage'] ) ) {

		        $product = wc_get_product( $wp->query_vars['wcfm-products-manage'] );

		        if ( $product && ! empty( $product ) ) {
		            $excerpt        = wpautop( $product->get_short_description( 'edit' ) );
		            $description    = wpautop( $product->get_description( 'edit' ) );
		            $product_type   = $product->get_type();
		        }
		    }

		    $rich_editor = apply_filters( 'wcfm_is_allow_rich_editor', 'rich_editor' );
		    $wpeditor = apply_filters( 'wcfm_is_allow_product_wpeditor', 'wpeditor' );

		    if ( $wpeditor && $rich_editor ) {
		        $rich_editor = 'wcfm_wpeditor';
		    } else {
		        $wpeditor = 'textarea';
		    }

		    $WCFM->wcfm_fields->wcfm_generate_form_field( 
		        apply_filters( 'ovabrw_customize_product_manage_fields_content', 
		            array(
		                "description" => array(
		                    'label' => esc_html__('Description', 'entox') , 
		                    'type' => $wpeditor, 
		                    'class' => 'wcfm-textarea wcfm_ele wcfm_full_ele simple variable external grouped booking ' . $rich_editor, 
		                    'label_class' => 'wcfm_title wcfm_full_ele ' . $rich_editor, 
		                    'rows' => 10, 
		                    'value' => $description
		                ),

		                "pro_id" => array(
		                    'type' => 'hidden', 
		                    'value' => $product_id
		                )
		            ), $product_id, $product_type 
		        ) 
		    );
		}

		function entox_wcfm_product_data_factory( $value, $product_id, $product, $data ) {

		    $taxonomies = get_option( 'ovabrw_custom_taxonomy', array() );
		    $product_custom_taxonomies = '';

		    if ( ! empty( $taxonomies ) && $product_id ) {

		        if ( isset( $data['product_custom_taxonomies'] ) && ! empty ( $data['product_custom_taxonomies'] ) ) {
		            $product_custom_taxonomies = $data['product_custom_taxonomies'];

		            foreach ( $taxonomies as $key => $taxonomy ) {
		                if ( ! array_key_exists( $key, $product_custom_taxonomies ) ) {
		                    wp_delete_object_term_relationships( $product_id, $key );
		                }
		            }
		        } else {
		            foreach ( $taxonomies as $key => $taxonomy ) {
		                wp_delete_object_term_relationships( $product_id, $key );
		            }
		        }
		    }

		    return $data;
		}

		function entox_before_wrapper_wcfm_product() {
			echo '<div class="ova-wrapper-wcfm-product">';
		}

		function entox_after_wrapper_wcfm_product() {
			echo '</div>';
		}
	}
}

new Entox_Customize_WCFM();