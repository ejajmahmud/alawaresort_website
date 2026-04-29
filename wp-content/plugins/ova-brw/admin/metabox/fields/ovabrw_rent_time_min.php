<?php

$day_min    = get_post_meta( $post_id, 'ovabrw_rent_day_min', true );
$hour_min   = get_post_meta( $post_id, 'ovabrw_rent_hour_min', true );

// Rent Day min
woocommerce_wp_text_input(
    array(
        'id'            => 'ovabrw_rent_day_min',
        'class'         => 'short ',
        'label'         => esc_html__( 'Min Rental Dates', 'ova-brw' ),
        'placeholder'   => esc_html__( '1', 'ova-brw' ),
        'desc_tip'      => 'true',
        'type'          => 'text',
        'value'         => apply_filters( 'ft_default_ovabrw_rent_day_min', $day_min )

));

// Rent Hour min
woocommerce_wp_text_input(
    array(
        'id'            => 'ovabrw_rent_hour_min',
        'class'         => 'short ',
        'label'         => esc_html__( 'Min Rental Hours', 'ova-brw' ),
        'placeholder'   => esc_html__( '1', 'ova-brw' ),
        'desc_tip'      => 'true',
        'type'          => 'text',
        'value'         => apply_filters( 'ft_default_ovabrw_rent_hour_min', $hour_min )
));