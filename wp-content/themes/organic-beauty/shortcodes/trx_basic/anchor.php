<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_anchor_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_anchor_theme_setup' );
	function organic_beauty_sc_anchor_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_anchor_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_anchor_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_anchor id="unique_id" description="Anchor description" title="Short Caption" icon="icon-class"]
*/

if (!function_exists('organic_beauty_sc_anchor')) {	
	function organic_beauty_sc_anchor($atts, $content = null) {
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"description" => '',
			"icon" => '',
			"url" => "",
			"separator" => "no",
			// Common params
			"id" => ""
		), $atts)));
		$output = $id 
			? '<a id="'.esc_attr($id).'"'
				. ' class="sc_anchor"' 
				. ' title="' . ($title ? esc_attr($title) : '') . '"'
				. ' data-description="' . ($description ? esc_attr(organic_beauty_strmacros($description)) : ''). '"'
				. ' data-icon="' . ($icon ? $icon : '') . '"' 
				. ' data-url="' . ($url ? esc_attr($url) : '') . '"' 
				. ' data-separator="' . (organic_beauty_param_is_on($separator) ? 'yes' : 'no') . '"'
				. '></a>'
			: '';
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_anchor', $atts, $content);
	}
	organic_beauty_require_shortcode("trx_anchor", "organic_beauty_sc_anchor");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_anchor_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_anchor_reg_shortcodes');
	function organic_beauty_sc_anchor_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_anchor", array(
			"title" => esc_html__("Anchor", 'organic-beauty'),
			"desc" => wp_kses_data( __("Insert anchor for the TOC (table of content)", 'organic-beauty') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"icon" => array(
					"title" => esc_html__("Anchor's icon",  'organic-beauty'),
					"desc" => wp_kses_data( __('Select icon for the anchor from Fontello icons set',  'organic-beauty') ),
					"value" => "",
					"type" => "icons",
					"options" => organic_beauty_get_sc_param('icons')
				),
				"title" => array(
					"title" => esc_html__("Short title", 'organic-beauty'),
					"desc" => wp_kses_data( __("Short title of the anchor (for the table of content)", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"description" => array(
					"title" => esc_html__("Long description", 'organic-beauty'),
					"desc" => wp_kses_data( __("Description for the popup (then hover on the icon). You can use:<br>'{{' and '}}' - to make the text italic,<br>'((' and '))' - to make the text bold,<br>'||' - to insert line break", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"url" => array(
					"title" => esc_html__("External URL", 'organic-beauty'),
					"desc" => wp_kses_data( __("External URL for this TOC item", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"separator" => array(
					"title" => esc_html__("Add separator", 'organic-beauty'),
					"desc" => wp_kses_data( __("Add separator under item in the TOC", 'organic-beauty') ),
					"value" => "no",
					"type" => "switch",
					"options" => organic_beauty_get_sc_param('yes_no')
				),
				"id" => organic_beauty_get_sc_param('id')
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_anchor_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_anchor_reg_shortcodes_vc');
	function organic_beauty_sc_anchor_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_anchor",
			"name" => esc_html__("Anchor", 'organic-beauty'),
			"description" => wp_kses_data( __("Insert anchor for the TOC (table of content)", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_anchor',
			"class" => "trx_sc_single trx_sc_anchor",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Anchor's icon", 'organic-beauty'),
					"description" => wp_kses_data( __("Select icon for the anchor from Fontello icons set", 'organic-beauty') ),
					"class" => "",
					"value" => organic_beauty_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Short title", 'organic-beauty'),
					"description" => wp_kses_data( __("Short title of the anchor (for the table of content)", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "description",
					"heading" => esc_html__("Long description", 'organic-beauty'),
					"description" => wp_kses_data( __("Description for the popup (then hover on the icon). You can use:<br>'{{' and '}}' - to make the text italic,<br>'((' and '))' - to make the text bold,<br>'||' - to insert line break", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "url",
					"heading" => esc_html__("External URL", 'organic-beauty'),
					"description" => wp_kses_data( __("External URL for this TOC item", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "separator",
					"heading" => esc_html__("Add separator", 'organic-beauty'),
					"description" => wp_kses_data( __("Add separator under item in the TOC", 'organic-beauty') ),
					"class" => "",
					"value" => array("Add separator" => "yes" ),
					"type" => "checkbox"
				),
				organic_beauty_get_vc_param('id')
			),
		) );
		
		class WPBakeryShortCode_Trx_Anchor extends ORGANIC_BEAUTY_VC_ShortCodeSingle {}
	}
}
?>