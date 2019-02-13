<?php
/*
 * Support for the Attachment's media folders
 */



// Register custom taxonomy
if (!function_exists('trx_utils_support_attachment_taxonomy')) {
	add_action( 'trx_utils_custom_taxonomy', 'trx_utils_support_attachment_taxonomy', 10, 2 );
	function trx_utils_support_attachment_taxonomy($name, $args=false) {
		if ($name=='media_folder') {
			if ($args===false) {
				$args = array(
					'post_type' 		=> 'attachment',
					'hierarchical' 		=> true,
					'labels' 			=> array(
						'name'              => esc_html__('Media Folders', 'trx_utils'),
						'singular_name'     => esc_html__('Media Folder', 'trx_utils'),
						'search_items'      => esc_html__('Search Media Folders', 'trx_utils'),
						'all_items'         => esc_html__('All Media Folders', 'trx_utils'),
						'parent_item'       => esc_html__('Parent Media Folder', 'trx_utils'),
						'parent_item_colon' => esc_html__('Parent Media Folder:', 'trx_utils'),
						'edit_item'         => esc_html__('Edit Media Folder', 'trx_utils'),
						'update_item'       => esc_html__('Update Media Folder', 'trx_utils'),
						'add_new_item'      => esc_html__('Add New Media Folder', 'trx_utils'),
						'new_item_name'     => esc_html__('New Media Folder Name', 'trx_utils'),
						'menu_name'         => esc_html__('Media Folders', 'trx_utils'),
					),
					'show_ui'           => true,
					'show_admin_column'	=> true,
					'query_var'			=> true,
					'rewrite' 			=> array( 'slug' => 'media_folder' )
					);
			}
			register_taxonomy( $name, $args['post_type'], $args);
		}
	}
}
?>