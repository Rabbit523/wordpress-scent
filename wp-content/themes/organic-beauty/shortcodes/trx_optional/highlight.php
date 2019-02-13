<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_highlight_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_highlight_theme_setup' );
	function organic_beauty_sc_highlight_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_highlight_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_highlight_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_highlight id="unique_id" color="fore_color's_name_or_#rrggbb" backcolor="back_color's_name_or_#rrggbb" style="custom_style"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_highlight]
*/

if (!function_exists('organic_beauty_sc_highlight')) {	
	function organic_beauty_sc_highlight($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"color" => "",
			"bg_color" => "",
			"font_size" => "",
			"type" => "1",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts)));
		$css .= ($color != '' ? 'color:' . esc_attr($color) . ';' : '')
			.($bg_color != '' ? 'background-color:' . esc_attr($bg_color) . ';' : '')
			.($font_size != '' ? 'font-size:' . esc_attr(organic_beauty_prepare_css_value($font_size)) . '; line-height: 1em;' : '');
		$output = '<span' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_highlight'.($type>0 ? ' sc_highlight_style_'.esc_attr($type) : ''). (!empty($class) ? ' '.esc_attr($class) : '').'"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. '>' 
				. do_shortcode($content) 
				. '</span>';
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_highlight', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_highlight', 'organic_beauty_sc_highlight');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_highlight_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_highlight_reg_shortcodes');
	function organic_beauty_sc_highlight_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_highlight", array(
			"title" => esc_html__("Highlight text", 'organic-beauty'),
			"desc" => wp_kses_data( __("Highlight text with selected color, background color and other styles", 'organic-beauty') ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"type" => array(
					"title" => esc_html__("Type", 'organic-beauty'),
					"desc" => wp_kses_data( __("Highlight type", 'organic-beauty') ),
					"value" => "1",
					"type" => "checklist",
					"options" => array(
						0 => esc_html__('Custom', 'organic-beauty'),
						1 => esc_html__('Type 1', 'organic-beauty'),
						2 => esc_html__('Type 2', 'organic-beauty'),
						3 => esc_html__('Type 3', 'organic-beauty')
					)
				),
				"color" => array(
					"title" => esc_html__("Color", 'organic-beauty'),
					"desc" => wp_kses_data( __("Color for the highlighted text", 'organic-beauty') ),
					"divider" => true,
					"value" => "",
					"type" => "color"
				),
				"bg_color" => array(
					"title" => esc_html__("Background color", 'organic-beauty'),
					"desc" => wp_kses_data( __("Background color for the highlighted text", 'organic-beauty') ),
					"value" => "",
					"type" => "color"
				),
				"font_size" => array(
					"title" => esc_html__("Font size", 'organic-beauty'),
					"desc" => wp_kses_data( __("Font size of the highlighted text (default - in pixels, allows any CSS units of measure)", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"_content_" => array(
					"title" => esc_html__("Highlighting content", 'organic-beauty'),
					"desc" => wp_kses_data( __("Content for highlight", 'organic-beauty') ),
					"divider" => true,
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				),
				"id" => organic_beauty_get_sc_param('id'),
				"class" => organic_beauty_get_sc_param('class'),
				"css" => organic_beauty_get_sc_param('css')
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_highlight_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_highlight_reg_shortcodes_vc');
	function organic_beauty_sc_highlight_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_highlight",
			"name" => esc_html__("Highlight text", 'organic-beauty'),
			"description" => wp_kses_data( __("Highlight text with selected color, background color and other styles", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_highlight',
			"class" => "trx_sc_single trx_sc_highlight",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "type",
					"heading" => esc_html__("Type", 'organic-beauty'),
					"description" => wp_kses_data( __("Highlight type", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
							esc_html__('Custom', 'organic-beauty') => 0,
							esc_html__('Type 1', 'organic-beauty') => 1,
							esc_html__('Type 2', 'organic-beauty') => 2,
							esc_html__('Type 3', 'organic-beauty') => 3
						),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Text color", 'organic-beauty'),
					"description" => wp_kses_data( __("Color for the highlighted text", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", 'organic-beauty'),
					"description" => wp_kses_data( __("Background color for the highlighted text", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "font_size",
					"heading" => esc_html__("Font size", 'organic-beauty'),
					"description" => wp_kses_data( __("Font size for the highlighted text (default - in pixels, allows any CSS units of measure)", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "content",
					"heading" => esc_html__("Highlight text", 'organic-beauty'),
					"description" => wp_kses_data( __("Content for highlight", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
				organic_beauty_get_vc_param('id'),
				organic_beauty_get_vc_param('class'),
				organic_beauty_get_vc_param('css')
			),
			'js_view' => 'VcTrxTextView'
		) );
		
		class WPBakeryShortCode_Trx_Highlight extends ORGANIC_BEAUTY_VC_ShortCodeSingle {}
	}
}
?>