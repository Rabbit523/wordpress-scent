<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_infobox_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_infobox_theme_setup' );
	function organic_beauty_sc_infobox_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_infobox_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_infobox_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_infobox id="unique_id" style="regular|info|success|error|result" static="0|1"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_infobox]
*/

if (!function_exists('organic_beauty_sc_infobox')) {	
	function organic_beauty_sc_infobox($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "regular",
			"closeable" => "no",
			"icon" => "",
			"color" => "",
			"bg_color" => "",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . organic_beauty_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= ($color !== '' ? 'color:' . esc_attr($color) .';' : '')
			. ($bg_color !== '' ? 'background-color:' . esc_attr($bg_color) .';' : '');
		if (empty($icon)) {
			if ($style=='regular')
				$icon = 'icon-cog';
			else if ($style=='success')
				$icon = 'icon-check';
			else if ($style=='error')
				$icon = 'icon-attention';
			else if ($style=='info')
				$icon = 'icon-info';
		} else if ($icon=='none')
			$icon = '';

		$content = do_shortcode($content);
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_infobox sc_infobox_style_' . esc_attr($style) 
					. (organic_beauty_param_is_on($closeable) ? ' sc_infobox_closeable' : '') 
					. (!empty($class) ? ' '.esc_attr($class) : '') 
					. ($icon!='' && !organic_beauty_param_is_inherit($icon) ? ' sc_infobox_iconed '. esc_attr($icon) : '') 
					. '"'
				. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. '>'
				. trim($content)
				. '</div>';
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_infobox', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_infobox', 'organic_beauty_sc_infobox');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_infobox_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_infobox_reg_shortcodes');
	function organic_beauty_sc_infobox_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_infobox", array(
			"title" => esc_html__("Infobox", 'organic-beauty'),
			"desc" => wp_kses_data( __("Insert infobox into your post (page)", 'organic-beauty') ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Style", 'organic-beauty'),
					"desc" => wp_kses_data( __("Infobox style", 'organic-beauty') ),
					"value" => "regular",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => array(
						'regular' => esc_html__('Regular', 'organic-beauty'),
						'info' => esc_html__('Info', 'organic-beauty'),
						'success' => esc_html__('Success', 'organic-beauty'),
						'error' => esc_html__('Error', 'organic-beauty')
					)
				),
				"closeable" => array(
					"title" => esc_html__("Closeable box", 'organic-beauty'),
					"desc" => wp_kses_data( __("Create closeable box (with close button)", 'organic-beauty') ),
					"value" => "no",
					"type" => "switch",
					"options" => organic_beauty_get_sc_param('yes_no')
				),
				"icon" => array(
					"title" => esc_html__("Custom icon",  'organic-beauty'),
					"desc" => wp_kses_data( __('Select icon for the infobox from Fontello icons set. If empty - use default icon',  'organic-beauty') ),
					"value" => "",
					"type" => "icons",
					"options" => organic_beauty_get_sc_param('icons')
				),
				"color" => array(
					"title" => esc_html__("Text color", 'organic-beauty'),
					"desc" => wp_kses_data( __("Any color for text and headers", 'organic-beauty') ),
					"value" => "",
					"type" => "color"
				),
				"bg_color" => array(
					"title" => esc_html__("Background color", 'organic-beauty'),
					"desc" => wp_kses_data( __("Any background color for this infobox", 'organic-beauty') ),
					"value" => "",
					"type" => "color"
				),
				"_content_" => array(
					"title" => esc_html__("Infobox content", 'organic-beauty'),
					"desc" => wp_kses_data( __("Content for infobox", 'organic-beauty') ),
					"divider" => true,
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				),
				"top" => organic_beauty_get_sc_param('top'),
				"bottom" => organic_beauty_get_sc_param('bottom'),
				"left" => organic_beauty_get_sc_param('left'),
				"right" => organic_beauty_get_sc_param('right'),
				"id" => organic_beauty_get_sc_param('id'),
				"class" => organic_beauty_get_sc_param('class'),
				"animation" => organic_beauty_get_sc_param('animation'),
				"css" => organic_beauty_get_sc_param('css')
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_infobox_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_infobox_reg_shortcodes_vc');
	function organic_beauty_sc_infobox_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_infobox",
			"name" => esc_html__("Infobox", 'organic-beauty'),
			"description" => wp_kses_data( __("Box with info or error message", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_infobox',
			"class" => "trx_sc_container trx_sc_infobox",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Style", 'organic-beauty'),
					"description" => wp_kses_data( __("Infobox style", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
							esc_html__('Regular', 'organic-beauty') => 'regular',
							esc_html__('Info', 'organic-beauty') => 'info',
							esc_html__('Success', 'organic-beauty') => 'success',
							esc_html__('Error', 'organic-beauty') => 'error',
							esc_html__('Result', 'organic-beauty') => 'result'
						),
					"type" => "dropdown"
				),
				array(
					"param_name" => "closeable",
					"heading" => esc_html__("Closeable", 'organic-beauty'),
					"description" => wp_kses_data( __("Create closeable box (with close button)", 'organic-beauty') ),
					"class" => "",
					"value" => array(esc_html__('Close button', 'organic-beauty') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Custom icon", 'organic-beauty'),
					"description" => wp_kses_data( __("Select icon for the infobox from Fontello icons set. If empty - use default icon", 'organic-beauty') ),
					"class" => "",
					"value" => organic_beauty_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Text color", 'organic-beauty'),
					"description" => wp_kses_data( __("Any color for the text and headers", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", 'organic-beauty'),
					"description" => wp_kses_data( __("Any background color for this infobox", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				organic_beauty_get_vc_param('id'),
				organic_beauty_get_vc_param('class'),
				organic_beauty_get_vc_param('animation'),
				organic_beauty_get_vc_param('css'),
				organic_beauty_get_vc_param('margin_top'),
				organic_beauty_get_vc_param('margin_bottom'),
				organic_beauty_get_vc_param('margin_left'),
				organic_beauty_get_vc_param('margin_right')
			),
			'js_view' => 'VcTrxTextContainerView'
		) );
		
		class WPBakeryShortCode_Trx_Infobox extends ORGANIC_BEAUTY_VC_ShortCodeContainer {}
	}
}
?>