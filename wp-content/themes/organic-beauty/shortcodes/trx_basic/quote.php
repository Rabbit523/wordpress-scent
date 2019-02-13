<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_quote_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_quote_theme_setup' );
	function organic_beauty_sc_quote_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_quote_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_quote_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_quote id="unique_id" cite="url" title=""]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/quote]
*/

if (!function_exists('organic_beauty_sc_quote')) {	
	function organic_beauty_sc_quote($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"cite" => "",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"width" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . organic_beauty_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= organic_beauty_get_css_dimensions_from_values($width);
		$cite_param = $cite != '' ? ' cite="'.esc_attr($cite).'"' : '';
		$title = $title=='' ? $cite : $title;
		$content = do_shortcode($content);
		if (organic_beauty_substr($content, 0, 2)!='<p') $content = '<p>' . ($content) . '</p>';
		$output = '<blockquote' 
			. ($id ? ' id="'.esc_attr($id).'"' : '') . ($cite_param) 
			. ' class="sc_quote'. (!empty($class) ? ' '.esc_attr($class) : '').'"' 
			. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
			. '>'
				. ($content)
				. ($title == '' ? '' : ('<p class="sc_quote_title">' . ($cite!='' ? '<a href="'.esc_url($cite).'">' : '') . ($title) . ($cite!='' ? '</a>' : '') . '</p>'))
			.'</blockquote>';
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_quote', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_quote', 'organic_beauty_sc_quote');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_quote_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_quote_reg_shortcodes');
	function organic_beauty_sc_quote_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_quote", array(
			"title" => esc_html__("Quote", 'organic-beauty'),
			"desc" => wp_kses_data( __("Quote text", 'organic-beauty') ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"cite" => array(
					"title" => esc_html__("Quote cite", 'organic-beauty'),
					"desc" => wp_kses_data( __("URL for quote cite", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"title" => array(
					"title" => esc_html__("Title (author)", 'organic-beauty'),
					"desc" => wp_kses_data( __("Quote title (author name)", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"_content_" => array(
					"title" => esc_html__("Quote content", 'organic-beauty'),
					"desc" => wp_kses_data( __("Quote content", 'organic-beauty') ),
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				),
				"width" => organic_beauty_shortcodes_width(),
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
if ( !function_exists( 'organic_beauty_sc_quote_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_quote_reg_shortcodes_vc');
	function organic_beauty_sc_quote_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_quote",
			"name" => esc_html__("Quote", 'organic-beauty'),
			"description" => wp_kses_data( __("Quote text", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_quote',
			"class" => "trx_sc_single trx_sc_quote",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "cite",
					"heading" => esc_html__("Quote cite", 'organic-beauty'),
					"description" => wp_kses_data( __("URL for the quote cite link", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title (author)", 'organic-beauty'),
					"description" => wp_kses_data( __("Quote title (author name)", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "content",
					"heading" => esc_html__("Quote content", 'organic-beauty'),
					"description" => wp_kses_data( __("Quote content", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
				organic_beauty_get_vc_param('id'),
				organic_beauty_get_vc_param('class'),
				organic_beauty_get_vc_param('animation'),
				organic_beauty_get_vc_param('css'),
				organic_beauty_vc_width(),
				organic_beauty_get_vc_param('margin_top'),
				organic_beauty_get_vc_param('margin_bottom'),
				organic_beauty_get_vc_param('margin_left'),
				organic_beauty_get_vc_param('margin_right')
			),
			'js_view' => 'VcTrxTextView'
		) );
		
		class WPBakeryShortCode_Trx_Quote extends ORGANIC_BEAUTY_VC_ShortCodeSingle {}
	}
}
?>