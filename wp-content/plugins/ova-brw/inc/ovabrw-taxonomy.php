<?php if ( !defined( 'ABSPATH' ) ) exit();

// hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'ovabrw_create_type_taxonomies', 0 );


function ovabrw_create_type_taxonomies() {
	
	
	// Get Custom Taxonomy from Database
	$ovabrw_custom_taxonomy =  get_option( 'ovabrw_custom_taxonomy', '' );


	$name_taxonomy = array();

	$ovabrw_custom_tax =  array();

	if( $ovabrw_custom_taxonomy ){
		$i = 1;
		foreach ($ovabrw_custom_taxonomy as $slug => $value) {

			$labels = array(
				'name'              => $value['name'],
				'singular_name'     => $value['singular_name'],
				'search_items'      => sprintf( esc_html__( 'Search %s', 'ova-brw' ), $value['name'] ),
				'all_items'         => sprintf( esc_html__( 'All %s', 'ova-brw' ), $value['name'] ) ,
				'parent_item'       => sprintf( esc_html__( 'Parent %s', 'ova-brw' ), $value['name'] ),
				'parent_item_colon' => sprintf( esc_html__( 'Parent %s: ', 'ova-brw' ), $value['name'] ),
				'edit_item'         => sprintf( esc_html__( 'Edit %s' , 'ova-brw' ), $value['name'] ),
				'update_item'       => sprintf( esc_html__( 'Update %s' , 'ova-brw' ), $value['name'] ),
				'add_new_item'      => sprintf( esc_html__( 'Add New %s' , 'ova-brw' ), $value['name'] ),
				'new_item_name'     => sprintf( esc_html__( 'New %s Name', 'ova-brw' ), $value['name'] ),
				'menu_name'         => sprintf( esc_html__( 'Custom %s' , 'ova-brw' ), $value['name'] ),

				
			);

			$args = array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => apply_filters( 'ovabrw_show_ui_custom_tax', true),
				'show_admin_column' => false,
				'query_var'         => true,
				'show_in_nav_menus' => false,
				'show_in_menu'	=> false,
				'rewrite'           => array( 'slug' => $slug ),

			);

			if( $value['enabled'] == 'on' ){
				register_taxonomy( $slug, array( 'product' ), $args );
			}
			

			$ovabrw_custom_tax[$i]['slug'] = $slug;
			$ovabrw_custom_tax[$i]['name'] = $value['name'];
			if( isset( $value['label_frontend'] ) && $value['label_frontend'] ){
				$ovabrw_custom_tax[$i]['name'] = $value['label_frontend'];
			}else{
				$ovabrw_custom_tax[$i]['name'] = $value['name'];
			}
			
			$i++;

		}
		

	}

	// Get Custom Taxonomy from Code
	// Add new taxonomy, make it hierarchical (like categories)
	$number_taxonomy = ovabrw_get_setting( get_option( 'ova_brw_number_taxonomy', 0 ) );
	if ( $number_taxonomy > 0 ) {
		for ( $i = 1; $number_taxonomy >= $i; $i++ ) {

			$param_arr = [];
			$param_arr = apply_filters( 'register_taxonomy_ovabrw_' . $i, $param_arr ) ;


			if ( empty( $param_arr ) || ! is_array( $param_arr ) ) {
				$labels = array(
					'name'              => sprintf( esc_html__( 'Custom Taxonomy %s', 'ova-brw' ), $i ),
					'singular_name'     => sprintf( esc_html__( 'taxonomy %s', 'ova-brw' ), $i ),
					'search_items'      => sprintf( esc_html__( 'Search Taxonomy %s', 'ova-brw' ), $i ),
					'all_items'         => sprintf( esc_html__( 'All Taxonomy %s', 'ova-brw' ), $i ),
					'parent_item'       => sprintf( esc_html__( 'Parent Taxonomy %s', 'ova-brw' ), $i ),
					'parent_item_colon' => sprintf( esc_html__( 'Parent Taxonomy %s: ', 'ova-brw' ), $i ),
					'edit_item'         => sprintf( esc_html__( 'Edit Taxonomy %s', 'ova-brw' ), $i ),
					'update_item'       => sprintf( esc_html__( 'Update Taxonomy %s', 'ova-brw' ), $i ),
					'add_new_item'      => sprintf( esc_html__( 'Add New Taxonomy %s', 'ova-brw' ), $i ),
					'new_item_name'     => sprintf( esc_html__( 'New Taxonomy %s Name', 'ova-brw' ), $i ),
					'menu_name'         => sprintf( esc_html__( 'Custom Taxonomy %s', 'ova-brw' ), $i ),
					'type'         		=> 'taxonomy_default' . $i,
				);

				$args = array(
					'hierarchical'      => true,
					'labels'            => $labels,
					'show_ui'           => true,
					'show_admin_column' => false,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => 'taxonomy_default' . $i ),

				);
			} else {
				$labels = array(
					'name'              => $param_arr['name'],
					'singular_name'     => $param_arr['slug'],
					'search_items'      => sprintf( esc_html__( 'Search %s', 'ova-brw' ), $param_arr['name'] ),
					'all_items'         => sprintf( esc_html__( 'All %s', 'ova-brw' ), $param_arr['name'] ),
					'parent_item'       => sprintf( esc_html__( 'Parent %s', 'ova-brw' ), $param_arr['name'] ),
					'parent_item_colon' => sprintf( esc_html__( 'Parent %s: ', 'ova-brw' ), $param_arr['name'] ),
					'edit_item'         => sprintf( esc_html__( 'Edit %s', 'ova-brw' ), $param_arr['name'] ),
					'update_item'       => sprintf( esc_html__( 'Update %s', 'ova-brw' ), $param_arr['name'] ),
					'add_new_item'      => sprintf( esc_html__( 'Add New %s', 'ova-brw' ), $param_arr['name'] ),
					'new_item_name'     => sprintf( esc_html__( 'New %s', 'ova-brw' ), $param_arr['name'] ),
					'menu_name'         => $param_arr['name'],
					'type'         		=> $param_arr['slug'],
				);

				$args = array(
					'hierarchical'      => true,
					'labels'            => $labels,
					'show_ui'           => true,
					'show_admin_column' => false,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => $param_arr['slug'] ),

				);
			}

			$name_taxonomy[$i]['slug'] = $args['labels']['type'];
			$name_taxonomy[$i]['name'] = $args['labels']['name'];

			register_taxonomy( $args['labels']['type'], array( 'product' ), $args );
		}

		
	}

	$name_taxonomy = array_merge_recursive( $name_taxonomy, $ovabrw_custom_tax);

	return $name_taxonomy;

	

	
}
