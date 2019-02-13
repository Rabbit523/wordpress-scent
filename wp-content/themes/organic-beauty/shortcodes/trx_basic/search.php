<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_search_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_search_theme_setup' );
	function organic_beauty_sc_search_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_search_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_search_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_search id="unique_id" open="yes|no"]
*/

if (!function_exists('organic_beauty_sc_search')) {	
	function organic_beauty_sc_search($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "",
			"state" => "",
			"ajax" => "",
			"title" => esc_html__('Søk produkt, navn, merke m.m.', 'organic-beauty'),
			"scheme" => "original",
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
		if ($style == 'fullscreen') {
			if (empty($ajax)) $ajax = "no";
			if (empty($state)) $state = "closed";
		} else if ($style == 'expand') {
			if (empty($ajax)) $ajax = organic_beauty_get_theme_option('use_ajax_search');
			if (empty($state)) $state = "closed";
		} else if ($style == 'slide') {
			if (empty($ajax)) $ajax = organic_beauty_get_theme_option('use_ajax_search');
			if (empty($state)) $state = "closed";
		} else {
			if (empty($ajax)) $ajax = organic_beauty_get_theme_option('use_ajax_search');
			if (empty($state)) $state = "fixed";
		}
		// Load core messages
		organic_beauty_enqueue_messages();
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') . ' class="search_wrap search_style_'.esc_attr($style).' search_state_'.esc_attr($state)
						. (organic_beauty_param_is_on($ajax) ? ' search_ajax' : '')
						. ($class ? ' '.esc_attr($class) : '')
						. '"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
					. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
					. '>
						<div class="search_form_wrap">
							<form role="search" method="get" class="search_form" action="' . esc_url(home_url('/')) . '">
								<button type="submit" class="search_submit icon-search" title="' . ($state=='closed' ? esc_attr__('Open search', 'organic-beauty') : esc_attr__('Start search', 'organic-beauty')) . '"></button>
								<input type="text" class="search_field" placeholder="' . esc_attr($title) . '" value="' . esc_attr(get_search_query()) . '" name="s" />'
								. ($style == 'fullscreen' ? '<a class="search_close icon-cancel"></a>' : '')
                                . (defined('ICL_LANGUAGE_CODE') ? '<input type="hidden" name="lang" value="' . ICL_LANGUAGE_CODE . '"/>' : '')
                            . '</form>
						</div>'
						. (organic_beauty_param_is_on($ajax) ? '<div class="search_results widget_area' . ($scheme && !organic_beauty_param_is_off($scheme) && !organic_beauty_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') . '"><a class="search_results_close icon-cancel"></a><div class="search_results_content"></div></div>' : '')
					. '</div>';
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_search', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_search', 'organic_beauty_sc_search');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_search_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_search_reg_shortcodes');
	function organic_beauty_sc_search_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_search", array(
			"title" => esc_html__("Search", 'organic-beauty'),
			"desc" => wp_kses_data( __("Show search form", 'organic-beauty') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"state" => array(
					"title" => esc_html__("State", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select search field initial state", 'organic-beauty') ),
					"value" => "fixed",
					"options" => array(
						"fixed"  => esc_html__('Fixed',  'organic-beauty'),
						"opened" => esc_html__('Opened', 'organic-beauty'),
						"closed" => esc_html__('Closed', 'organic-beauty')
					),
					"type" => "checklist"
				),
				"title" => array(
					"title" => esc_html__("Title", 'organic-beauty'),
					"desc" => wp_kses_data( __("Title (placeholder) for the search field", 'organic-beauty') ),
					"value" => esc_html__("Search &hellip;", 'organic-beauty'),
					"type" => "text"
				),
				"ajax" => array(
					"title" => esc_html__("AJAX", 'organic-beauty'),
					"desc" => wp_kses_data( __("Search via AJAX or reload page", 'organic-beauty') ),
					"value" => "yes",
					"options" => organic_beauty_get_sc_param('yes_no'),
					"type" => "switch"
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
if ( !function_exists( 'organic_beauty_sc_search_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_search_reg_shortcodes_vc');
	function organic_beauty_sc_search_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_search",
			"name" => esc_html__("Search form", 'organic-beauty'),
			"description" => wp_kses_data( __("Insert search form", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_search',
			"class" => "trx_sc_single trx_sc_search",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "state",
					"heading" => esc_html__("State", 'organic-beauty'),
					"description" => wp_kses_data( __("Select search field initial state", 'organic-beauty') ),
					"class" => "",
					"value" => array(
						esc_html__('Fixed', 'organic-beauty')  => "fixed",
						esc_html__('Opened', 'organic-beauty') => "opened",
						esc_html__('Closed', 'organic-beauty') => "closed"
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", 'organic-beauty'),
					"description" => wp_kses_data( __("Title (placeholder) for the search field", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => esc_html__("Search &hellip;", 'organic-beauty'),
					"type" => "textfield"
				),
				array(
					"param_name" => "ajax",
					"heading" => esc_html__("AJAX", 'organic-beauty'),
					"description" => wp_kses_data( __("Search via AJAX or reload page", 'organic-beauty') ),
					"class" => "",
					"value" => array(esc_html__('Use AJAX search', 'organic-beauty') => 'yes'),
					"type" => "checkbox"
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
		
		class WPBakeryShortCode_Trx_Search extends ORGANIC_BEAUTY_VC_ShortCodeSingle {}
	}
}
?>