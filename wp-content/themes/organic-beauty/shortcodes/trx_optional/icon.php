<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_icon_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_icon_theme_setup' );
	function organic_beauty_sc_icon_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_icon_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_icon_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_icon id="unique_id" style='round|square' icon='' color="" bg_color="" size="" weight=""]
*/

if (!function_exists('organic_beauty_sc_icon')) {	
	function organic_beauty_sc_icon($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"icon" => "",
			"color" => "",
			"bg_color" => "",
			"bg_shape" => "",
			"font_size" => "",
			"font_weight" => "",
			"align" => "",
			"link" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . organic_beauty_get_css_position_as_classes($top, $right, $bottom, $left);
		$css2 = ($font_weight != '' && !organic_beauty_is_inherit_option($font_weight) ? 'font-weight:'. esc_attr($font_weight).';' : '')
			. ($font_size != '' ? 'font-size:' . esc_attr(organic_beauty_prepare_css_value($font_size)) . '; line-height: ' . (!$bg_shape || organic_beauty_param_is_inherit($bg_shape) ? '1' : '1.2') . 'em;' : '')
			. ($color != '' ? 'color:'.esc_attr($color).';' : '')
			. ($bg_color != '' ? 'background-color:'.esc_attr($bg_color).';border-color:'.esc_attr($bg_color).';' : '')
		;
		$output = $icon!='' 
			? ($link ? '<a href="'.esc_url($link).'"' : '<span') . ($id ? ' id="'.esc_attr($id).'"' : '')
				. ' class="sc_icon '.esc_attr($icon)
					. ($bg_shape && !organic_beauty_param_is_inherit($bg_shape) ? ' sc_icon_shape_'.esc_attr($bg_shape) : '')
					. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
					. (!empty($class) ? ' '.esc_attr($class) : '')
				.'"'
				.($css || $css2 ? ' style="'.($class ? 'display:block;' : '') . ($css) . ($css2) . '"' : '')
				.'>'
				.($link ? '</a>' : '</span>')
			: '';
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_icon', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_icon', 'organic_beauty_sc_icon');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_icon_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_icon_reg_shortcodes');
	function organic_beauty_sc_icon_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_icon", array(
			"title" => esc_html__("Icon", 'organic-beauty'),
			"desc" => wp_kses_data( __("Insert icon", 'organic-beauty') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"icon" => array(
					"title" => esc_html__('Icon',  'organic-beauty'),
					"desc" => wp_kses_data( __('Select font icon from the Fontello icons set',  'organic-beauty') ),
					"value" => "",
					"type" => "icons",
					"options" => organic_beauty_get_sc_param('icons')
				),
				"color" => array(
					"title" => esc_html__("Icon's color", 'organic-beauty'),
					"desc" => wp_kses_data( __("Icon's color", 'organic-beauty') ),
					"dependency" => array(
						'icon' => array('not_empty')
					),
					"value" => "",
					"type" => "color"
				),
				"bg_shape" => array(
					"title" => esc_html__("Background shape", 'organic-beauty'),
					"desc" => wp_kses_data( __("Shape of the icon background", 'organic-beauty') ),
					"dependency" => array(
						'icon' => array('not_empty')
					),
					"value" => "none",
					"type" => "radio",
					"options" => array(
						'none' => esc_html__('None', 'organic-beauty'),
						'round' => esc_html__('Round', 'organic-beauty'),
						'square' => esc_html__('Square', 'organic-beauty')
					)
				),
				"bg_color" => array(
					"title" => esc_html__("Icon's background color", 'organic-beauty'),
					"desc" => wp_kses_data( __("Icon's background color", 'organic-beauty') ),
					"dependency" => array(
						'icon' => array('not_empty'),
						'background' => array('round','square')
					),
					"value" => "",
					"type" => "color"
				),
				"font_size" => array(
					"title" => esc_html__("Font size", 'organic-beauty'),
					"desc" => wp_kses_data( __("Icon's font size", 'organic-beauty') ),
					"dependency" => array(
						'icon' => array('not_empty')
					),
					"value" => "",
					"type" => "spinner",
					"min" => 8,
					"max" => 240
				),
				"font_weight" => array(
					"title" => esc_html__("Font weight", 'organic-beauty'),
					"desc" => wp_kses_data( __("Icon font weight", 'organic-beauty') ),
					"dependency" => array(
						'icon' => array('not_empty')
					),
					"value" => "",
					"type" => "select",
					"size" => "medium",
					"options" => array(
						'100' => esc_html__('Thin (100)', 'organic-beauty'),
						'300' => esc_html__('Light (300)', 'organic-beauty'),
						'400' => esc_html__('Normal (400)', 'organic-beauty'),
						'700' => esc_html__('Bold (700)', 'organic-beauty')
					)
				),
				"align" => array(
					"title" => esc_html__("Alignment", 'organic-beauty'),
					"desc" => wp_kses_data( __("Icon text alignment", 'organic-beauty') ),
					"dependency" => array(
						'icon' => array('not_empty')
					),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => organic_beauty_get_sc_param('align')
				), 
				"link" => array(
					"title" => esc_html__("Link URL", 'organic-beauty'),
					"desc" => wp_kses_data( __("Link URL from this icon (if not empty)", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"top" => organic_beauty_get_sc_param('top'),
				"bottom" => organic_beauty_get_sc_param('bottom'),
				"left" => organic_beauty_get_sc_param('left'),
				"right" => organic_beauty_get_sc_param('right'),
				"id" => organic_beauty_get_sc_param('id'),
				"class" => organic_beauty_get_sc_param('class'),
				"css" => organic_beauty_get_sc_param('css')
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_icon_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_icon_reg_shortcodes_vc');
	function organic_beauty_sc_icon_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_icon",
			"name" => esc_html__("Icon", 'organic-beauty'),
			"description" => wp_kses_data( __("Insert the icon", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_icon',
			"class" => "trx_sc_single trx_sc_icon",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Icon", 'organic-beauty'),
					"description" => wp_kses_data( __("Select icon class from Fontello icons set", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => organic_beauty_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Text color", 'organic-beauty'),
					"description" => wp_kses_data( __("Icon's color", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", 'organic-beauty'),
					"description" => wp_kses_data( __("Background color for the icon", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_shape",
					"heading" => esc_html__("Background shape", 'organic-beauty'),
					"description" => wp_kses_data( __("Shape of the icon background", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('None', 'organic-beauty') => 'none',
						esc_html__('Round', 'organic-beauty') => 'round',
						esc_html__('Square', 'organic-beauty') => 'square'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "font_size",
					"heading" => esc_html__("Font size", 'organic-beauty'),
					"description" => wp_kses_data( __("Icon's font size", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "font_weight",
					"heading" => esc_html__("Font weight", 'organic-beauty'),
					"description" => wp_kses_data( __("Icon's font weight", 'organic-beauty') ),
					"class" => "",
					"value" => array(
						esc_html__('Default', 'organic-beauty') => 'inherit',
						esc_html__('Thin (100)', 'organic-beauty') => '100',
						esc_html__('Light (300)', 'organic-beauty') => '300',
						esc_html__('Normal (400)', 'organic-beauty') => '400',
						esc_html__('Bold (700)', 'organic-beauty') => '700'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Icon's alignment", 'organic-beauty'),
					"description" => wp_kses_data( __("Align icon to left, center or right", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(organic_beauty_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link URL", 'organic-beauty'),
					"description" => wp_kses_data( __("Link URL from this icon (if not empty)", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				organic_beauty_get_vc_param('id'),
				organic_beauty_get_vc_param('class'),
				organic_beauty_get_vc_param('css'),
				organic_beauty_get_vc_param('margin_top'),
				organic_beauty_get_vc_param('margin_bottom'),
				organic_beauty_get_vc_param('margin_left'),
				organic_beauty_get_vc_param('margin_right')
			),
		) );
		
		class WPBakeryShortCode_Trx_Icon extends ORGANIC_BEAUTY_VC_ShortCodeSingle {}
	}
}
?>