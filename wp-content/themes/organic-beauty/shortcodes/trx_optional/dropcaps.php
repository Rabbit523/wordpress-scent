<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_dropcaps_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_dropcaps_theme_setup' );
	function organic_beauty_sc_dropcaps_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_dropcaps_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_dropcaps_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

//[trx_dropcaps id="unique_id" style="1-6"]paragraph text[/trx_dropcaps]

if (!function_exists('organic_beauty_sc_dropcaps')) {	
	function organic_beauty_sc_dropcaps($atts, $content=null){
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "1",
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
		$css .= organic_beauty_get_css_dimensions_from_values($width, $height);
		$style = min(4, max(1, $style));
		$content = do_shortcode(str_replace(array('[vc_column_text]', '[/vc_column_text]'), array('', ''), $content));
		$output = organic_beauty_substr($content, 0, 1) == '<' 
			? $content 
			: '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_dropcaps sc_dropcaps_style_' . esc_attr($style) . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
				. ($css ? ' style="'.esc_attr($css).'"' : '')
				. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
				. '>' 
					. '<span class="sc_dropcaps_item">' . trim(organic_beauty_substr($content, 0, 1)) . '</span>' . trim(organic_beauty_substr($content, 1))
			. '</div>';
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_dropcaps', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_dropcaps', 'organic_beauty_sc_dropcaps');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_dropcaps_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_dropcaps_reg_shortcodes');
	function organic_beauty_sc_dropcaps_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_dropcaps", array(
			"title" => esc_html__("Dropcaps", 'organic-beauty'),
			"desc" => wp_kses_data( __("Make first letter as dropcaps", 'organic-beauty') ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Style", 'organic-beauty'),
					"desc" => wp_kses_data( __("Dropcaps style", 'organic-beauty') ),
					"value" => "1",
					"type" => "checklist",
					"options" => organic_beauty_get_list_styles(1, 2)
				),
				"_content_" => array(
					"title" => esc_html__("Paragraph content", 'organic-beauty'),
					"desc" => wp_kses_data( __("Paragraph with dropcaps content", 'organic-beauty') ),
					"divider" => true,
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
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
if ( !function_exists( 'organic_beauty_sc_dropcaps_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_dropcaps_reg_shortcodes_vc');
	function organic_beauty_sc_dropcaps_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_dropcaps",
			"name" => esc_html__("Dropcaps", 'organic-beauty'),
			"description" => wp_kses_data( __("Make first letter of the text as dropcaps", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_dropcaps',
			"class" => "trx_sc_container trx_sc_dropcaps",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Style", 'organic-beauty'),
					"description" => wp_kses_data( __("Dropcaps style", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(organic_beauty_get_list_styles(1, 2)),
					"type" => "dropdown"
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
			)
		
		) );
		
		class WPBakeryShortCode_Trx_Dropcaps extends ORGANIC_BEAUTY_VC_ShortCodeContainer {}
	}
}
?>