<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_reviews_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_reviews_theme_setup' );
	function organic_beauty_sc_reviews_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_reviews_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_reviews_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_reviews]
*/

if (!function_exists('organic_beauty_sc_reviews')) {	
	function organic_beauty_sc_reviews($atts, $content = null) {
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"align" => "right",
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
		$output = organic_beauty_param_is_off(organic_beauty_get_custom_option('show_sidebar_main'))
			? '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
						. ' class="sc_reviews'
							. ($align && $align!='none' ? ' align'.esc_attr($align) : '')
							. ($class ? ' '.esc_attr($class) : '')
							. '"'
						. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
						. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
						. '>'
					. trim(organic_beauty_get_reviews_placeholder())
					. '</div>'
			: '';
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_reviews', $atts, $content);
	}
	organic_beauty_require_shortcode("trx_reviews", "organic_beauty_sc_reviews");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_reviews_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_reviews_reg_shortcodes');
	function organic_beauty_sc_reviews_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_reviews", array(
			"title" => esc_html__("Reviews", 'organic-beauty'),
			"desc" => wp_kses_data( __("Insert reviews block in the single post", 'organic-beauty') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"align" => array(
					"title" => esc_html__("Alignment", 'organic-beauty'),
					"desc" => wp_kses_data( __("Align counter to left, center or right", 'organic-beauty') ),
					"divider" => true,
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
if ( !function_exists( 'organic_beauty_sc_reviews_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_reviews_reg_shortcodes_vc');
	function organic_beauty_sc_reviews_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_reviews",
			"name" => esc_html__("Reviews", 'organic-beauty'),
			"description" => wp_kses_data( __("Insert reviews block in the single post", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_reviews',
			"class" => "trx_sc_single trx_sc_reviews",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", 'organic-beauty'),
					"description" => wp_kses_data( __("Align counter to left, center or right", 'organic-beauty') ),
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
		
		class WPBakeryShortCode_Trx_Reviews extends ORGANIC_BEAUTY_VC_ShortCodeSingle {}
	}
}
?>