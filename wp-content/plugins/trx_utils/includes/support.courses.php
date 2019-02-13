<?php
/*
 * Support for the Courses and Lessons
 */



// Register custom post type
if (!function_exists('trx_utils_support_courses_post_type')) {
	add_action( 'trx_utils_custom_post_type', 'trx_utils_support_courses_post_type', 10, 2 );
	function trx_utils_support_courses_post_type($name, $args=false) {
		
		if ($name=='courses') {

			if ($args===false) {
				$args = array(
					'label'               => __( 'Course item', 'trx_utils' ),
					'description'         => __( 'Course Description', 'trx_utils' ),
					'labels'              => array(
						'name'                => _x( 'Courses', 'Post Type General Name', 'trx_utils' ),
						'singular_name'       => _x( 'Course item', 'Post Type Singular Name', 'trx_utils' ),
						'menu_name'           => __( 'Courses', 'trx_utils' ),
						'parent_item_colon'   => __( 'Parent Item:', 'trx_utils' ),
						'all_items'           => __( 'All Courses', 'trx_utils' ),
						'view_item'           => __( 'View Item', 'trx_utils' ),
						'add_new_item'        => __( 'Add New Course item', 'trx_utils' ),
						'add_new'             => __( 'Add New', 'trx_utils' ),
						'edit_item'           => __( 'Edit Item', 'trx_utils' ),
						'update_item'         => __( 'Update Item', 'trx_utils' ),
						'search_items'        => __( 'Search Item', 'trx_utils' ),
						'not_found'           => __( 'Not found', 'trx_utils' ),
						'not_found_in_trash'  => __( 'Not found in Trash', 'trx_utils' ),
					),
					'supports'            => array( 'title', 'excerpt', 'editor', 'author', 'thumbnail', 'comments', 'custom-fields'),
					'hierarchical'        => false,
					'public'              => true,
					'show_ui'             => true,
					'menu_icon'			  => 'dashicons-format-chat',
					'show_in_menu'        => true,
					'show_in_nav_menus'   => true,
					'show_in_admin_bar'   => true,
					'menu_position'       => '52.5',
					'can_export'          => true,
					'has_archive'         => false,
					'exclude_from_search' => false,
					'publicly_queryable'  => true,
					'query_var'           => true,
					'capability_type'     => 'page',
					'rewrite'             => true
					);
			}
			register_post_type( $name, $args );
			trx_utils_add_rewrite_rules($name);

		} else if ($name=='lesson') {

			if ($args===false) {
				$args = array(
					'label'               => __( 'Lesson', 'trx_utils' ),
					'description'         => __( 'Lesson Description', 'trx_utils' ),
					'labels'              => array(
						'name'                => _x( 'Lessons', 'Post Type General Name', 'trx_utils' ),
						'singular_name'       => _x( 'Lesson', 'Post Type Singular Name', 'trx_utils' ),
						'menu_name'           => __( 'Lessons', 'trx_utils' ),
						'parent_item_colon'   => __( 'Parent Item:', 'trx_utils' ),
						'all_items'           => __( 'All lessons', 'trx_utils' ),
						'view_item'           => __( 'View Item', 'trx_utils' ),
						'add_new_item'        => __( 'Add New lesson', 'trx_utils' ),
						'add_new'             => __( 'Add New', 'trx_utils' ),
						'edit_item'           => __( 'Edit Item', 'trx_utils' ),
						'update_item'         => __( 'Update Item', 'trx_utils' ),
						'search_items'        => __( 'Search Item', 'trx_utils' ),
						'not_found'           => __( 'Not found', 'trx_utils' ),
						'not_found_in_trash'  => __( 'Not found in Trash', 'trx_utils' ),
					),
					'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt'),
					'hierarchical'        => false,
					'public'              => true,
					'show_ui'             => true,
					'menu_icon'			  => 'dashicons-format-chat',
					'show_in_menu'        => true,
					'show_in_nav_menus'   => true,
					'show_in_admin_bar'   => true,
					'menu_position'       => '52.6',
					'can_export'          => true,
					'has_archive'         => false,
					'exclude_from_search' => true,
					'publicly_queryable'  => true,
					'capability_type'     => 'page'
					);
			}
			register_post_type( $name, $args );
		}
	}
}
		

// Register custom taxonomy
if (!function_exists('trx_utils_support_courses_taxonomy')) {
	add_action( 'trx_utils_custom_taxonomy', 'trx_utils_support_courses_taxonomy', 10, 2 );
	function trx_utils_support_courses_taxonomy($name, $args=false) {
		
		if ($name=='courses_group') {

			if ($args===false) {
				$args = array(
					'post_type' 		=> 'courses',
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => _x( 'Courses Groups', 'taxonomy general name', 'trx_utils' ),
						'singular_name'     => _x( 'Courses Group', 'taxonomy singular name', 'trx_utils' ),
						'search_items'      => __( 'Search Groups', 'trx_utils' ),
						'all_items'         => __( 'All Groups', 'trx_utils' ),
						'parent_item'       => __( 'Parent Group', 'trx_utils' ),
						'parent_item_colon' => __( 'Parent Group:', 'trx_utils' ),
						'edit_item'         => __( 'Edit Group', 'trx_utils' ),
						'update_item'       => __( 'Update Group', 'trx_utils' ),
						'add_new_item'      => __( 'Add New Group', 'trx_utils' ),
						'new_item_name'     => __( 'New Group Name', 'trx_utils' ),
						'menu_name'         => __( 'Courses Groups', 'trx_utils' ),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => 'courses_group' )
					);
			}
			register_taxonomy( $name, $args['post_type'], $args);

		} else if ($name=='courses_tag') {

			if ($args===false) {
				$args = array(
					'post_type' 		=> 'courses',
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => _x( 'Courses Tags', 'taxonomy general name', 'trx_utils' ),
						'singular_name'     => _x( 'Courses Tag', 'taxonomy singular name', 'trx_utils' ),
						'search_items'      => __( 'Search Tags', 'trx_utils' ),
						'all_items'         => __( 'All Tags', 'trx_utils' ),
						'parent_item'       => __( 'Parent Tag', 'trx_utils' ),
						'parent_item_colon' => __( 'Parent Tag:', 'trx_utils' ),
						'edit_item'         => __( 'Edit Tag', 'trx_utils' ),
						'update_item'       => __( 'Update Tag', 'trx_utils' ),
						'add_new_item'      => __( 'Add New Tag', 'trx_utils' ),
						'new_item_name'     => __( 'New Tag Name', 'trx_utils' ),
						'menu_name'         => __( 'Courses Tags', 'trx_utils' ),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => 'courses_tag' )
				);
			}
			register_taxonomy( $name, $args['post_type'], $args);
		}
	}
}
?>