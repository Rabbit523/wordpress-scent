<?php
/**
 * Organic Beauty Framework: Theme options custom fields
 *
 * @package	organic_beauty
 * @since	organic_beauty 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'organic_beauty_options_custom_theme_setup' ) ) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_options_custom_theme_setup' );
	function organic_beauty_options_custom_theme_setup() {

		if ( is_admin() ) {
			add_action("admin_enqueue_scripts",	'organic_beauty_options_custom_load_scripts');
		}
		
	}
}

// Load required styles and scripts for custom options fields
if ( !function_exists( 'organic_beauty_options_custom_load_scripts' ) ) {
	//add_action("admin_enqueue_scripts", 'organic_beauty_options_custom_load_scripts');
	function organic_beauty_options_custom_load_scripts() {
		organic_beauty_enqueue_script( 'organic_beauty-options-custom-script',	organic_beauty_get_file_url('core/core.options/js/core.options-custom.js'), array(), null, true );	
	}
}


// Show theme specific fields in Post (and Page) options
if ( !function_exists( 'organic_beauty_show_custom_field' ) ) {
	function organic_beauty_show_custom_field($id, $field, $value) {
		$output = '';
		switch ($field['type']) {
			case 'reviews':
				$output .= '<div class="reviews_block">' . trim(organic_beauty_reviews_get_markup($field, $value, true)) . '</div>';
				break;
	
			case 'mediamanager':
				wp_enqueue_media( );
				$output .= '<a id="'.esc_attr($id).'" class="button mediamanager organic_beauty_media_selector"
					data-param="' . esc_attr($id) . '"
					data-choose="'.esc_attr(isset($field['multiple']) && $field['multiple'] ? esc_html__( 'Choose Images', 'organic-beauty') : esc_html__( 'Choose Image', 'organic-beauty')).'"
					data-update="'.esc_attr(isset($field['multiple']) && $field['multiple'] ? esc_html__( 'Add to Gallery', 'organic-beauty') : esc_html__( 'Choose Image', 'organic-beauty')).'"
					data-multiple="'.esc_attr(isset($field['multiple']) && $field['multiple'] ? 'true' : 'false').'"
					data-linked-field="'.esc_attr($field['media_field_id']).'"
					>' . (isset($field['multiple']) && $field['multiple'] ? esc_html__( 'Choose Images', 'organic-beauty') : esc_html__( 'Choose Image', 'organic-beauty')) . '</a>';
				break;
		}
		return apply_filters('organic_beauty_filter_show_custom_field', $output, $id, $field, $value);
	}
}
?>