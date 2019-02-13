<?php

// Check if shortcodes settings are now used
if ( !function_exists( 'organic_beauty_shortcodes_is_used' ) ) {
	function organic_beauty_shortcodes_is_used() {
		return organic_beauty_options_is_used() 															// All modes when Theme Options are used
			|| (is_admin() && isset($_POST['action']) 
					&& in_array($_POST['action'], array('vc_edit_form', 'wpb_show_edit_form')))		// AJAX query when save post/page
			|| (is_admin() && !empty($_REQUEST['page']) && $_REQUEST['page']=='vc-roles')			// VC Role Manager
			|| (function_exists('organic_beauty_vc_is_frontend') && organic_beauty_vc_is_frontend());			// VC Frontend editor mode
	}
}

// Width and height params
if ( !function_exists( 'organic_beauty_shortcodes_width' ) ) {
	function organic_beauty_shortcodes_width($w="") {
		return array(
			"title" => esc_html__("Width", 'organic-beauty'),
			"divider" => true,
			"value" => $w,
			"type" => "text"
		);
	}
}
if ( !function_exists( 'organic_beauty_shortcodes_height' ) ) {
	function organic_beauty_shortcodes_height($h='') {
		return array(
			"title" => esc_html__("Height", 'organic-beauty'),
			"desc" => wp_kses_data( __("Width and height of the element", 'organic-beauty') ),
			"value" => $h,
			"type" => "text"
		);
	}
}

// Return sc_param value
if ( !function_exists( 'organic_beauty_get_sc_param' ) ) {
	function organic_beauty_get_sc_param($prm) {
		return organic_beauty_storage_get_array('sc_params', $prm);
	}
}

// Set sc_param value
if ( !function_exists( 'organic_beauty_set_sc_param' ) ) {
	function organic_beauty_set_sc_param($prm, $val) {
		organic_beauty_storage_set_array('sc_params', $prm, $val);
	}
}

// Add sc settings in the sc list
if ( !function_exists( 'organic_beauty_sc_map' ) ) {
	function organic_beauty_sc_map($sc_name, $sc_settings) {
		organic_beauty_storage_set_array('shortcodes', $sc_name, $sc_settings);
	}
}

// Add sc settings in the sc list after the key
if ( !function_exists( 'organic_beauty_sc_map_after' ) ) {
	function organic_beauty_sc_map_after($after, $sc_name, $sc_settings='') {
		organic_beauty_storage_set_array_after('shortcodes', $after, $sc_name, $sc_settings);
	}
}

// Add sc settings in the sc list before the key
if ( !function_exists( 'organic_beauty_sc_map_before' ) ) {
	function organic_beauty_sc_map_before($before, $sc_name, $sc_settings='') {
		organic_beauty_storage_set_array_before('shortcodes', $before, $sc_name, $sc_settings);
	}
}

// Compare two shortcodes by title
if ( !function_exists( 'organic_beauty_compare_sc_title' ) ) {
	function organic_beauty_compare_sc_title($a, $b) {
		return strcmp($a['title'], $b['title']);
	}
}



/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'organic_beauty_shortcodes_settings_theme_setup' ) ) {
//	if ( organic_beauty_vc_is_frontend() )
	if ( (isset($_GET['vc_editable']) && $_GET['vc_editable']=='true') || (isset($_GET['vc_action']) && $_GET['vc_action']=='vc_inline') )
		add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_shortcodes_settings_theme_setup', 20 );
	else
		add_action( 'organic_beauty_action_after_init_theme', 'organic_beauty_shortcodes_settings_theme_setup' );
	function organic_beauty_shortcodes_settings_theme_setup() {
		if (organic_beauty_shortcodes_is_used()) {

			// Sort templates alphabetically
			$tmp = organic_beauty_storage_get('registered_templates');
			ksort($tmp);
			organic_beauty_storage_set('registered_templates', $tmp);

			// Prepare arrays 
			organic_beauty_storage_set('sc_params', array(
			
				// Current element id
				'id' => array(
					"title" => esc_html__("Element ID", 'organic-beauty'),
					"desc" => wp_kses_data( __("ID for current element", 'organic-beauty') ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
			
				// Current element class
				'class' => array(
					"title" => esc_html__("Element CSS class", 'organic-beauty'),
					"desc" => wp_kses_data( __("CSS class for current element (optional)", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
			
				// Current element style
				'css' => array(
					"title" => esc_html__("CSS styles", 'organic-beauty'),
					"desc" => wp_kses_data( __("Any additional CSS rules (if need)", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
			
			
				// Switcher choises
				'list_styles' => array(
					'ul'	=> esc_html__('Unordered', 'organic-beauty'),
					'ol'	=> esc_html__('Ordered', 'organic-beauty'),
					'iconed'=> esc_html__('Iconed', 'organic-beauty')
				),

				'yes_no'	=> organic_beauty_get_list_yesno(),
				'on_off'	=> organic_beauty_get_list_onoff(),
				'dir' 		=> organic_beauty_get_list_directions(),
				'align'		=> organic_beauty_get_list_alignments(),
				'float'		=> organic_beauty_get_list_floats(),
				'hpos'		=> organic_beauty_get_list_hpos(),
				'show_hide'	=> organic_beauty_get_list_showhide(),
				'sorting' 	=> organic_beauty_get_list_sortings(),
				'ordering' 	=> organic_beauty_get_list_orderings(),
				'shapes'	=> organic_beauty_get_list_shapes(),
				'sizes'		=> organic_beauty_get_list_sizes(),
				'sliders'	=> organic_beauty_get_list_sliders(),
				'controls'	=> organic_beauty_get_list_controls(),
				'categories'=> organic_beauty_get_list_categories(),
				'columns'	=> organic_beauty_get_list_columns(),
				'images'	=> array_merge(array('none'=>"none"), organic_beauty_get_list_images("images/icons", "png")),
				'icons'		=> array_merge(array("inherit", "none"), organic_beauty_get_list_icons()),
				'locations'	=> organic_beauty_get_list_dedicated_locations(),
				'filters'	=> organic_beauty_get_list_portfolio_filters(),
				'formats'	=> organic_beauty_get_list_post_formats_filters(),
				'hovers'	=> organic_beauty_get_list_hovers(true),
				'hovers_dir'=> organic_beauty_get_list_hovers_directions(true),
				'schemes'	=> organic_beauty_get_list_color_schemes(true),
				'animations'		=> organic_beauty_get_list_animations_in(),
				'margins' 			=> organic_beauty_get_list_margins(true),
				'blogger_styles'	=> organic_beauty_get_list_templates_blogger(),
				'forms'				=> organic_beauty_get_list_templates_forms(),
				'posts_types'		=> organic_beauty_get_list_posts_types(),
				'googlemap_styles'	=> organic_beauty_get_list_googlemap_styles(),
				'field_types'		=> organic_beauty_get_list_field_types(),
				'label_positions'	=> organic_beauty_get_list_label_positions()
				)
			);

			// Common params
			organic_beauty_set_sc_param('animation', array(
				"title" => esc_html__("Animation",  'organic-beauty'),
				"desc" => wp_kses_data( __('Select animation while object enter in the visible area of page',  'organic-beauty') ),
				"value" => "none",
				"type" => "select",
				"options" => organic_beauty_get_sc_param('animations')
				)
			);
			organic_beauty_set_sc_param('top', array(
				"title" => esc_html__("Top margin",  'organic-beauty'),
				"divider" => true,
				"value" => "inherit",
				"type" => "select",
				"options" => organic_beauty_get_sc_param('margins')
				)
			);
			organic_beauty_set_sc_param('bottom', array(
				"title" => esc_html__("Bottom margin",  'organic-beauty'),
				"value" => "inherit",
				"type" => "select",
				"options" => organic_beauty_get_sc_param('margins')
				)
			);
			organic_beauty_set_sc_param('left', array(
				"title" => esc_html__("Left margin",  'organic-beauty'),
				"value" => "inherit",
				"type" => "select",
				"options" => organic_beauty_get_sc_param('margins')
				)
			);
			organic_beauty_set_sc_param('right', array(
				"title" => esc_html__("Right margin",  'organic-beauty'),
				"desc" => wp_kses_data( __("Margins around this shortcode", 'organic-beauty') ),
				"value" => "inherit",
				"type" => "select",
				"options" => organic_beauty_get_sc_param('margins')
				)
			);

			organic_beauty_storage_set('sc_params', apply_filters('organic_beauty_filter_shortcodes_params', organic_beauty_storage_get('sc_params')));

			// Shortcodes list
			//------------------------------------------------------------------
			organic_beauty_storage_set('shortcodes', array());
			
			// Register shortcodes
			do_action('organic_beauty_action_shortcodes_list');

			// Sort shortcodes list
			$tmp = organic_beauty_storage_get('shortcodes');
			uasort($tmp, 'organic_beauty_compare_sc_title');
			organic_beauty_storage_set('shortcodes', $tmp);
		}
	}
}
?>