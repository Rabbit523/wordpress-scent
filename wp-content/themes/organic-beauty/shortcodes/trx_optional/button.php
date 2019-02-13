<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_button_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_button_theme_setup' );
	function organic_beauty_sc_button_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_button_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_button_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_button id="unique_id" type="square|round" fullsize="0|1" style="global|light|dark" size="mini|medium|big|huge|banner" icon="icon-name" link='#' target='']Button caption[/trx_button]
*/

if (!function_exists('organic_beauty_sc_button')) {	
	function organic_beauty_sc_button($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"type" => "square",
			"style" => "filled",
			"size" => "small",
			"icon" => "",
			"color" => "",
			"bg_color" => "",
			"link" => "",
			"target" => "",
			"align" => "",
			"rel" => "",
			"popup" => "no",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"width" => "",
			"height" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . organic_beauty_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= organic_beauty_get_css_dimensions_from_values($width, $height)
			. ($color !== '' ? 'color:' . esc_attr($color) .';' : '')
			. ($bg_color !== '' ? 'background-color:' . esc_attr($bg_color) . '; border-color:'. esc_attr($bg_color) .';' : '');
		if (organic_beauty_param_is_on($popup)) organic_beauty_enqueue_popup('magnific');
		$output = '<a href="' . (empty($link) ? '#' : $link) . '"'
			. (!empty($target) ? ' target="'.esc_attr($target).'"' : '')
			. (!empty($rel) ? ' rel="'.esc_attr($rel).'"' : '')
			. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
			. ' class="sc_button sc_button_' . esc_attr($type) 
					. ' sc_button_style_' . esc_attr($style) 
					. ' sc_button_size_' . esc_attr($size)
					. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
					. (!empty($class) ? ' '.esc_attr($class) : '')
					. ($icon!='' ? '  sc_button_iconed '. esc_attr($icon) : '') 
					. (organic_beauty_param_is_on($popup) ? ' sc_popup_link' : '') 
					. '"'
			. ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
			. '>'
			. do_shortcode($content)
			. '</a>';
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_button', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_button', 'organic_beauty_sc_button');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_button_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_button_reg_shortcodes');
	function organic_beauty_sc_button_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_button", array(
			"title" => esc_html__("Button", 'organic-beauty'),
			"desc" => wp_kses_data( __("Button with link", 'organic-beauty') ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"_content_" => array(
					"title" => esc_html__("Caption", 'organic-beauty'),
					"desc" => wp_kses_data( __("Button caption", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"type" => array(
					"title" => esc_html__("Button's shape", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select button's shape", 'organic-beauty') ),
					"value" => "square",
					"size" => "medium",
					"options" => array(
						'square' => esc_html__('Square', 'organic-beauty'),
						'round' => esc_html__('Round', 'organic-beauty')
					),
					"type" => "switch"
				), 
				"style" => array(
					"title" => esc_html__("Button's style", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select button's style", 'organic-beauty') ),
					"value" => "default",
					"dir" => "horizontal",
					"options" => array(
						'filled' => esc_html__('Filled', 'organic-beauty'),
						'filled2' => esc_html__('Filled 2', 'organic-beauty')
					),
					"type" => "checklist"
				), 
				"size" => array(
					"title" => esc_html__("Button's size", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select button's size", 'organic-beauty') ),
					"value" => "small",
					"dir" => "horizontal",
					"options" => array(
						'small' => esc_html__('Small', 'organic-beauty'),
						'medium' => esc_html__('Medium', 'organic-beauty'),
						'large' => esc_html__('Large', 'organic-beauty')
					),
					"type" => "checklist"
				), 
				"icon" => array(
					"title" => esc_html__("Button's icon",  'organic-beauty'),
					"desc" => wp_kses_data( __('Select icon for the title from Fontello icons set',  'organic-beauty') ),
					"value" => "",
					"type" => "icons",
					"options" => organic_beauty_get_sc_param('icons')
				),
				"color" => array(
					"title" => esc_html__("Button's text color", 'organic-beauty'),
					"desc" => wp_kses_data( __("Any color for button's caption", 'organic-beauty') ),
					"std" => "",
					"value" => "",
					"type" => "color"
				),
				"bg_color" => array(
					"title" => esc_html__("Button's backcolor", 'organic-beauty'),
					"desc" => wp_kses_data( __("Any color for button's background", 'organic-beauty') ),
					"value" => "",
					"type" => "color"
				),
				"align" => array(
					"title" => esc_html__("Button's alignment", 'organic-beauty'),
					"desc" => wp_kses_data( __("Align button to left, center or right", 'organic-beauty') ),
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => organic_beauty_get_sc_param('align')
				), 
				"link" => array(
					"title" => esc_html__("Link URL", 'organic-beauty'),
					"desc" => wp_kses_data( __("URL for link on button click", 'organic-beauty') ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"target" => array(
					"title" => esc_html__("Link target", 'organic-beauty'),
					"desc" => wp_kses_data( __("Target for link on button click", 'organic-beauty') ),
					"dependency" => array(
						'link' => array('not_empty')
					),
					"value" => "",
					"type" => "text"
				),
				"popup" => array(
					"title" => esc_html__("Open link in popup", 'organic-beauty'),
					"desc" => wp_kses_data( __("Open link target in popup window", 'organic-beauty') ),
					"dependency" => array(
						'link' => array('not_empty')
					),
					"value" => "no",
					"type" => "switch",
					"options" => organic_beauty_get_sc_param('yes_no')
				), 
				"rel" => array(
					"title" => esc_html__("Rel attribute", 'organic-beauty'),
					"desc" => wp_kses_data( __("Rel attribute for button's link (if need)", 'organic-beauty') ),
					"dependency" => array(
						'link' => array('not_empty')
					),
					"value" => "",
					"type" => "text"
				),
				"width" => organic_beauty_shortcodes_width(),
				"height" => organic_beauty_shortcodes_height(),
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
if ( !function_exists( 'organic_beauty_sc_button_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_button_reg_shortcodes_vc');
	function organic_beauty_sc_button_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_button",
			"name" => esc_html__("Button", 'organic-beauty'),
			"description" => wp_kses_data( __("Button with link", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_button',
			"class" => "trx_sc_single trx_sc_button",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "content",
					"heading" => esc_html__("Caption", 'organic-beauty'),
					"description" => wp_kses_data( __("Button caption", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "type",
					"heading" => esc_html__("Button's shape", 'organic-beauty'),
					"description" => wp_kses_data( __("Select button's shape", 'organic-beauty') ),
					"class" => "",
					"value" => array(
						esc_html__('Square', 'organic-beauty') => 'square',
						esc_html__('Round', 'organic-beauty') => 'round'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "style",
					"heading" => esc_html__("Button's style", 'organic-beauty'),
					"description" => wp_kses_data( __("Select button's style", 'organic-beauty') ),
					"class" => "",
					"value" => array(
						esc_html__('Filled', 'organic-beauty') => 'filled',
						esc_html__('Filled 2', 'organic-beauty') => 'filled2'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "size",
					"heading" => esc_html__("Button's size", 'organic-beauty'),
					"description" => wp_kses_data( __("Select button's size", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('Small', 'organic-beauty') => 'small',
						esc_html__('Medium', 'organic-beauty') => 'medium',
						esc_html__('Large', 'organic-beauty') => 'large'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Button's icon", 'organic-beauty'),
					"description" => wp_kses_data( __("Select icon for the title from Fontello icons set", 'organic-beauty') ),
					"class" => "",
					"value" => organic_beauty_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Button's text color", 'organic-beauty'),
					"description" => wp_kses_data( __("Any color for button's caption", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Button's backcolor", 'organic-beauty'),
					"description" => wp_kses_data( __("Any color for button's background", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Button's alignment", 'organic-beauty'),
					"description" => wp_kses_data( __("Align button to left, center or right", 'organic-beauty') ),
					"class" => "",
					"value" => array_flip(organic_beauty_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link URL", 'organic-beauty'),
					"description" => wp_kses_data( __("URL for the link on button click", 'organic-beauty') ),
					"class" => "",
					"group" => esc_html__('Link', 'organic-beauty'),
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "target",
					"heading" => esc_html__("Link target", 'organic-beauty'),
					"description" => wp_kses_data( __("Target for the link on button click", 'organic-beauty') ),
					"class" => "",
					"group" => esc_html__('Link', 'organic-beauty'),
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "popup",
					"heading" => esc_html__("Open link in popup", 'organic-beauty'),
					"description" => wp_kses_data( __("Open link target in popup window", 'organic-beauty') ),
					"class" => "",
					"group" => esc_html__('Link', 'organic-beauty'),
					"value" => array(esc_html__('Open in popup', 'organic-beauty') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "rel",
					"heading" => esc_html__("Rel attribute", 'organic-beauty'),
					"description" => wp_kses_data( __("Rel attribute for the button's link (if need", 'organic-beauty') ),
					"class" => "",
					"group" => esc_html__('Link', 'organic-beauty'),
					"value" => "",
					"type" => "textfield"
				),
				organic_beauty_get_vc_param('id'),
				organic_beauty_get_vc_param('class'),
				organic_beauty_get_vc_param('animation'),
				organic_beauty_get_vc_param('css'),
				organic_beauty_vc_width(),
				organic_beauty_vc_height(),
				organic_beauty_get_vc_param('margin_top'),
				organic_beauty_get_vc_param('margin_bottom'),
				organic_beauty_get_vc_param('margin_left'),
				organic_beauty_get_vc_param('margin_right')
			),
			'js_view' => 'VcTrxTextView'
		) );
		
		class WPBakeryShortCode_Trx_Button extends ORGANIC_BEAUTY_VC_ShortCodeSingle {}
	}
}
?>