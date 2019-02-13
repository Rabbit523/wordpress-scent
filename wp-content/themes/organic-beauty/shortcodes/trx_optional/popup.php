<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_popup_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_popup_theme_setup' );
	function organic_beauty_sc_popup_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_popup_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_popup_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_popup id="unique_id" class="class_name" style="css_styles"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_popup]
*/

if (!function_exists('organic_beauty_sc_popup')) {	
	function organic_beauty_sc_popup($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
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
		organic_beauty_enqueue_popup('magnific');
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_popup mfp-with-anim mfp-hide' . ($class ? ' '.esc_attr($class) : '') . '"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. '>' 
				. do_shortcode($content) 
				. '</div>';
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_popup', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_popup', 'organic_beauty_sc_popup');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_popup_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_popup_reg_shortcodes');
	function organic_beauty_sc_popup_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_popup", array(
			"title" => esc_html__("Popup window", 'organic-beauty'),
			"desc" => wp_kses_data( __("Container for any html-block with desired class and style for popup window", 'organic-beauty') ),
			"decorate" => true,
			"container" => true,
			"params" => array(
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
if ( !function_exists( 'organic_beauty_sc_popup_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_popup_reg_shortcodes_vc');
	function organic_beauty_sc_popup_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_popup",
			"name" => esc_html__("Popup window", 'organic-beauty'),
			"description" => wp_kses_data( __("Container for any html-block with desired class and style for popup window", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_popup',
			"class" => "trx_sc_collection trx_sc_popup",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"params" => array(
				organic_beauty_get_vc_param('id'),
				organic_beauty_get_vc_param('class'),
				organic_beauty_get_vc_param('css'),
				organic_beauty_get_vc_param('margin_top'),
				organic_beauty_get_vc_param('margin_bottom'),
				organic_beauty_get_vc_param('margin_left'),
				organic_beauty_get_vc_param('margin_right')
			)
		) );
		
		class WPBakeryShortCode_Trx_Popup extends ORGANIC_BEAUTY_VC_ShortCodeCollection {}
	}
}
?>