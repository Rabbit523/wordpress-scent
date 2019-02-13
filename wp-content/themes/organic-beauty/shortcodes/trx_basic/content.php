<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_content_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_content_theme_setup' );
	function organic_beauty_sc_content_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_content_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_content_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_content id="unique_id" class="class_name" style="css-styles"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_content]
*/

if (!function_exists('organic_beauty_sc_content')) {	
	function organic_beauty_sc_content($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			"scheme" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"top" => "",
			"bottom" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . organic_beauty_get_css_position_as_classes($top, '', $bottom);
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_content content_wrap' 
				. ($scheme && !organic_beauty_param_is_off($scheme) && !organic_beauty_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
				. ($class ? ' '.esc_attr($class) : '') 
				. '"'
			. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '').'>' 
			. do_shortcode($content) 
			. '</div>';
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_content', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_content', 'organic_beauty_sc_content');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_content_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_content_reg_shortcodes');
	function organic_beauty_sc_content_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_content", array(
			"title" => esc_html__("Content block", 'organic-beauty'),
			"desc" => wp_kses_data( __("Container for main content block with desired class and style (use it only on fullscreen pages)", 'organic-beauty') ),
			"decorate" => true,
			"container" => true,
			"params" => array(
				"scheme" => array(
					"title" => esc_html__("Color scheme", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select color scheme for this block", 'organic-beauty') ),
					"value" => "",
					"type" => "checklist",
					"options" => organic_beauty_get_sc_param('schemes')
				),
				"_content_" => array(
					"title" => esc_html__("Container content", 'organic-beauty'),
					"desc" => wp_kses_data( __("Content for section container", 'organic-beauty') ),
					"divider" => true,
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				),
				"top" => organic_beauty_get_sc_param('top'),
				"bottom" => organic_beauty_get_sc_param('bottom'),
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
if ( !function_exists( 'organic_beauty_sc_content_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_content_reg_shortcodes_vc');
	function organic_beauty_sc_content_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_content",
			"name" => esc_html__("Content block", 'organic-beauty'),
			"description" => wp_kses_data( __("Container for main content block (use it only on fullscreen pages)", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_content',
			"class" => "trx_sc_collection trx_sc_content",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "scheme",
					"heading" => esc_html__("Color scheme", 'organic-beauty'),
					"description" => wp_kses_data( __("Select color scheme for this block", 'organic-beauty') ),
					"group" => esc_html__('Colors and Images', 'organic-beauty'),
					"class" => "",
					"value" => array_flip(organic_beauty_get_sc_param('schemes')),
					"type" => "dropdown"
				),
				organic_beauty_get_vc_param('id'),
				organic_beauty_get_vc_param('class'),
				organic_beauty_get_vc_param('animation'),
				organic_beauty_get_vc_param('css'),
				organic_beauty_get_vc_param('margin_top'),
				organic_beauty_get_vc_param('margin_bottom')
			)
		) );
		
		class WPBakeryShortCode_Trx_Content extends ORGANIC_BEAUTY_VC_ShortCodeCollection {}
	}
}
?>