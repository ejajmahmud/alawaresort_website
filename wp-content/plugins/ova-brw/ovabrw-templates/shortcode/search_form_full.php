<?php

extract(  $args );
// Get first day in week
$first_day = get_option( 'ova_brw_calendar_first_day', '0' );

if ( empty( $first_day ) ) {
    $first_day = 0;
}
?>
<div class="ovabrw_wd_search">
    <form action="<?php echo esc_url( home_url() ); ?>" class="<?php echo esc_attr( $class ); ?> ovabrw_search form_ovabrw row" method="get" enctype="multipart/form-data" data-mesg_required="<?php esc_attr_e( 'This field is required.', 'ova-brw' ) ?>">
            <div class="wrap_content">

                <?php if ( $show_name_product == 'yes' ): ?>
                    <div class="s_field <?php echo esc_attr( $column ); ?>">
                        <div class="content">
                            <label><?php esc_html_e( 'Product Name', 'ova-brw' ); ?></label>
                            <div class="wrap-error"></div>
                            <input class="<?php echo esc_attr( $name_product_required ); ?>"
                            placeholder="<?php esc_attr_e('Product Name', 'ova-brw'); ?>"
                            name="ovabrw_name_product"
                            value="<?php echo esc_attr( $name_product ); ?>" autocomplete="off" />
                        </div>
                        <div class="<?php echo esc_attr( $class ); ?>"></div>
                    </div>
                <?php endif; ?>

                <?php if ( $show_cat == 'yes' ): ?>
                    <div class="s_field <?php echo esc_attr( $column ); ?>">
                        <div class="content">
                            <label><?php esc_html_e( 'Category', 'ova-brw' ); ?></label>
                            <div class="wrap-error"></div>
                            <?php echo wp_kses( ovabrw_cat_rental( $cat, $category_required, $remove_cats_id ), array('select' => array() ) ); ?>
                         </div>
                    </div>
                <?php endif; ?>

                <?php if ( $show_pickup_loc == 'yes' ): ?>
                    <div class="s_field <?php echo esc_attr( $column ); ?>">
                        <div class="content">
                            <label><?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?></label>
                            <div class="wrap-error"></div>
                            <?php
                            ovabrw_get_locations_html( $class = 'ovabrw_pickup_loc', $pickup_loc_required, $seleted = $pickup_loc );
                            ?>
                        </div>
                        <div class="<?php echo esc_attr( $class ); ?>"></div>
                    </div>
                <?php endif; ?>


                <?php if ( $show_dropoff_loc == 'yes' ): ?>
                    <div class="s_field <?php echo esc_attr( $column ); ?>">
                        <div class="content">
                            <label><?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?></label>
                            <div class="wrap-error"></div>
                            <?php ovabrw_get_locations_html( $class = 'ovabrw_pickoff_loc',$dropoff_loc_required, $seleted = $pickoff_loc ); ?>
                        </div>
                        <div class="<?php echo esc_attr( $class ); ?>"></div>
                    </div>
                <?php endif; ?>

                
                <?php if ( $show_pickup_date == 'yes' ): ?>
                    <div class="s_field <?php echo esc_attr( $column ); ?>">
                        <div class="content">
                        <label><?php esc_html_e( 'Pick-up Date', 'ova-brw' ); ?></label>
                        <div class="wrap-error"></div>
                        <input type="text" name="ovabrw_pickup_date"
                        value="<?php echo esc_attr( $pickup_date ); ?>"
                        onkeydown="return false"
                        class="<?php echo esc_attr( $pickup_date_required ); ?> ovabrw_datetimepicker ovabrw_start_date"
                        placeholder="<?php echo esc_attr( ovabrw_get_date_format() ); ?>"
                        autocomplete="off"
                        data-hour_default="<?php echo esc_attr( $hour_default  ); ?>"
                        data-time_step="<?php echo esc_attr( $time_step ); ?>"
                        data-dateformat="<?php echo esc_attr( $dateformat ); ?>"
                        data-error=".ovabrw_pickup_date" onfocus="blur();"
                        data-firstday="<?php echo esc_attr( $first_day ); ?>" />
                        </div>
                    </div>
                <?php endif; ?>

                
                <?php if ( $show_dropoff_date == 'yes' ): ?>
                    <div class="s_field <?php echo esc_attr( $column ); ?>"><div class="content">
                        <label><?php esc_html_e( 'Drop-off Date', 'ova-brw' ); ?></label>
                        <div class="wrap-error"></div>
                        <input type="text" name="ovabrw_pickoff_date"
                            value="<?php echo esc_attr( $pickoff_date ); ?>"
                            onkeydown="return false"
                            class="<?php echo esc_attr( $dropoff_date_required ); ?> ovabrw_datetimepicker ovabrw_end_date"
                            placeholder="<?php echo esc_attr( ovabrw_get_date_format() ); ?>"
                            autocomplete="off"
                            data-hour_default="<?php echo esc_attr( $hour_default ); ?>"
                            data-time_step="<?php echo esc_attr( $time_step ); ?>"
                            data-dateformat="<?php echo esc_attr( $dateformat ); ?>"
                            data-error=".ovabrw_pickoff_date" onfocus="blur();"
                            data-firstday="<?php echo esc_attr( $first_day ); ?>"/>
                        </div></div>
                <?php endif; ?>
                    
                <?php if ( $show_attribute == 'yes' ): ?>
                    <div class="s_field <?php echo esc_attr( $column ); ?>">
                        <div class="content">
                            <label><?php esc_html_e( 'Name Attribute', 'ova-brw' ); ?></label>
                            <div class="wrap-error"></div>
                            <?php echo wp_kses_post( $html_select_attribute ); ?>
                        </div>
                    <div class="<?php echo esc_attr( $class ); ?>"></div>
                </div>
                <?php endif; ?>

                <?php if ( $show_tag_product == 'yes' ): ?>
                    <div class="s_field <?php echo esc_attr( $column ); ?>">
                        <div class="content">
                            <label><?php esc_html_e( 'Product Tag', 'ova-brw' ); ?></label>
                            <div class="wrap-error"></div>
                            <input class="<?php echo esc_attr( $tag_product_required ); ?>"
                            placeholder="<?php esc_attr_e('Product Tag', 'ova-brw'); ?>" name="ovabrw_tag_product" value="<?php echo esc_attr( $tag_product ); ?>" autocomplete="off" />
                        </div>
                        <div class="<?php echo esc_attr( $class ); ?>"></div>
                    </div>
                <?php endif; ?>

                
                    
                    
                <?php
                $list_taxonomy = array_key_exists( 'taxonomy_list_all' , $taxonomy_list_wrap) ? $taxonomy_list_wrap['taxonomy_list_all'] : [];
                $arr_require_taxonomy_key = array_key_exists( 'taxonomy_require' , $taxonomy_list_wrap) ? $taxonomy_list_wrap['taxonomy_require'] : [];
                $arr_hide_taxonomy_key = array_key_exists( 'taxonomy_hide' , $taxonomy_list_wrap) ? $taxonomy_list_wrap['taxonomy_hide'] : [];
                $list_get_taxonomy = array_key_exists( 'taxonomy_get' , $taxonomy_list_wrap) ? $taxonomy_list_wrap['taxonomy_get'] : [];

                if ( ! empty( $list_taxonomy ) && $show_tax == 'yes' ) {
                    foreach( $list_taxonomy as $taxonomy ) {
                        
                        $required = '';
                        if ( is_array( $arr_require_taxonomy_key ) && array_key_exists( $taxonomy['slug'], $arr_require_taxonomy_key ) && $arr_require_taxonomy_key[$taxonomy['slug']] == 'require' ) {
                            $required = 'required';
                        } else {
                            $required = '';
                        }

                        if ( is_array( $arr_hide_taxonomy_key ) && array_key_exists( $taxonomy['slug'], $arr_hide_taxonomy_key ) && $arr_hide_taxonomy_key[$taxonomy['slug']] == 'hide' ) {
                    
                        } else {
                            ?>
                            <div class="s_field <?php echo esc_attr( $column ); ?> s_field_cus_tax <?php echo esc_attr( $taxonomy['slug'] ); ?>">
                                <div class="content">
                                    <label><?php echo esc_html( $taxonomy['name'] ); ?></label>
                                    <div class="wrap-error"></div>
                                    <?php
                                    ovabrw_taxonomy_dropdown( $list_get_taxonomy[$taxonomy['slug']], $required , '',$taxonomy['slug'], $taxonomy['name'] ); ?>
                                </div>
                            </div>
                            <?php
                        }
                    }
                }


                

                ?>
            </div>

            <div class="s_submit">
                <button class="ovabrw_btn_submit" type="submit">
                    <?php esc_html_e( 'Search', 'ova-brw' ); ?>
                </button>
            </div>

            <input type="hidden" name="ovabrw_search_product" value="ovabrw_search_product" />
                    <input type="hidden" name="ovabrw_search" value="search_item" />
                    <input type="hidden" name="post_type" value="product" />
                    <?php
            if( defined( 'ICL_LANGUAGE_CODE' ) ){ ?>
                <input type="hidden" name="lang" value="<?php echo esc_attr( ICL_LANGUAGE_CODE ); ?>" />
            <?php } ?>

    </form>
</div>