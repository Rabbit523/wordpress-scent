<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_price_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_price_theme_setup' );
	function organic_beauty_sc_price_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_price_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_price_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_price id="unique_id" currency="$" money="29.99" period="monthly"]
*/

if (!function_exists('organic_beauty_sc_price')) {	
	function organic_beauty_sc_price($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"money" => "",
			"currency" => "$",
			"period" => "",
			"align" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$output = '';
		if (!empty($money)) {
			$class .= ($class ? ' ' : '') . organic_beauty_get_css_position_as_classes($top, $right, $bottom, $left);
			$m = explode('.', str_replace(',', '.', $money));
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
					. ' class="sc_price'
					. (!empty($class) ? ' '.esc_attr($class) : '')
					. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
					. '"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
					. '>'
				. '<span class="sc_price_currency">'.($currency).'</span>'
				. '<span class="sc_price_money">'.($m[0]).'</span>'
				. (!empty($m[1]) ? '<span class="sc_price_info">' : '')
				. (!empty($m[1]) ? '<span class="sc_price_penny">'.($m[1]).'</span>' : '')
				. (!empty($period) ? '<span class="sc_price_period">'.($period).'</span>' : (!empty($m[1]) ? '<span class="sc_price_period_empty"></span>' : ''))
				. (!empty($m[1]) ? '</span>' : '')
				. '</div>';
		}
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_price', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_price', 'organic_beauty_sc_price');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_price_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_price_reg_shortcodes');
	function organic_beauty_sc_price_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_price", array(
			"title" => esc_html__("Price", 'organic-beauty'),
			"desc" => wp_kses_data( __("Insert price with decoration", 'organic-beauty') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"money" => array(
					"title" => esc_html__("Money", 'organic-beauty'),
					"desc" => wp_kses_data( __("Money value (dot or comma separated)", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"currency" => array(
					"title" => esc_html__("Currency", 'organic-beauty'),
					"desc" => wp_kses_data( __("Currency character", 'organic-beauty') ),
					"value" => "$",
					"type" => "text"
				),
				"period" => array(
					"title" => esc_html__("Period", 'organic-beauty'),
					"desc" => wp_kses_data( __("Period text (if need). For example: monthly, daily, etc.", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"align" => array(
					"title" => esc_html__("Alignment", 'organic-beauty'),
					"desc" => wp_kses_data( __("Align price to left or right side", 'organic-beauty') ),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => organic_beauty_get_sc_param('float')
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
if ( !function_exists( 'organic_beauty_sc_price_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_price_reg_shortcodes_vc');
	function organic_beauty_sc_price_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_price",
			"name" => esc_html__("Price", 'organic-beauty'),
			"description" => wp_kses_data( __("Insert price with decoration", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_price',
			"class" => "trx_sc_single trx_sc_price",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "money",
					"heading" => esc_html__("Money", 'organic-beauty'),
					"description" => wp_kses_data( __("Money value (dot or comma separated)", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "currency",
					"heading" => esc_html__("Currency symbol", 'organic-beauty'),
					"description" => wp_kses_data( __("Currency character", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "$",
					"type" => "textfield"
				),
				array(
					"param_name" => "period",
					"heading" => esc_html__("Period", 'organic-beauty'),
					"description" => wp_kses_data( __("Period text (if need). For example: monthly, daily, etc.", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", 'organic-beauty'),
					"description" => wp_kses_data( __("Align price to left or right side", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(organic_beauty_get_sc_param('float')),
					"type" => "dropdown"
				),
				organic_beauty_get_vc_param('id'),
				organic_beauty_get_vc_param('class'),
				organic_beauty_get_vc_param('css'),
				organic_beauty_get_vc_param('margin_top'),
				organic_beauty_get_vc_param('margin_bottom'),
				organic_beauty_get_vc_param('margin_left'),
				organic_beauty_get_vc_param('margin_right')
			)
		) );
		
		class WPBakeryShortCode_Trx_Price extends ORGANIC_BEAUTY_VC_ShortCodeSingle {}
	}
}
?>