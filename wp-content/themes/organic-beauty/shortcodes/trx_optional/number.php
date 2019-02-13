<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_number_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_number_theme_setup' );
	function organic_beauty_sc_number_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_number_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_number_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_number id="unique_id" value="400"]
*/

if (!function_exists('organic_beauty_sc_number')) {	
	function organic_beauty_sc_number($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"value" => "",
			"align" => "",
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
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_number' 
					. (!empty($align) ? ' align'.esc_attr($align) : '') 
					. (!empty($class) ? ' '.esc_attr($class) : '') 
					. '"'
				. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. '>';
		for ($i=0; $i < organic_beauty_strlen($value); $i++) {
			$output .= '<span class="sc_number_item">' . trim(organic_beauty_substr($value, $i, 1)) . '</span>';
		}
		$output .= '</div>';
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_number', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_number', 'organic_beauty_sc_number');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_number_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_number_reg_shortcodes');
	function organic_beauty_sc_number_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_number", array(
			"title" => esc_html__("Number", 'organic-beauty'),
			"desc" => wp_kses_data( __("Insert number or any word as set separate characters", 'organic-beauty') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"value" => array(
					"title" => esc_html__("Value", 'organic-beauty'),
					"desc" => wp_kses_data( __("Number or any word", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"align" => array(
					"title" => esc_html__("Align", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select block alignment", 'organic-beauty') ),
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => organic_beauty_get_sc_param('align')
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
if ( !function_exists( 'organic_beauty_sc_number_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_number_reg_shortcodes_vc');
	function organic_beauty_sc_number_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_number",
			"name" => esc_html__("Number", 'organic-beauty'),
			"description" => wp_kses_data( __("Insert number or any word as set of separated characters", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			"class" => "trx_sc_single trx_sc_number",
			'icon' => 'icon_trx_number',
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "value",
					"heading" => esc_html__("Value", 'organic-beauty'),
					"description" => wp_kses_data( __("Number or any word to separate", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", 'organic-beauty'),
					"description" => wp_kses_data( __("Select block alignment", 'organic-beauty') ),
					"class" => "",
					"value" => array_flip(organic_beauty_get_sc_param('align')),
					"type" => "dropdown"
				),
				organic_beauty_get_vc_param('id'),
				organic_beauty_get_vc_param('class'),
				organic_beauty_get_vc_param('animation'),
				organic_beauty_get_vc_param('css'),
				organic_beauty_get_vc_param('margin_top'),
				organic_beauty_get_vc_param('margin_bottom'),
				organic_beauty_get_vc_param('margin_left'),
				organic_beauty_get_vc_param('margin_right')
			)
		) );
		
		class WPBakeryShortCode_Trx_Number extends ORGANIC_BEAUTY_VC_ShortCodeSingle {}
	}
}
?>