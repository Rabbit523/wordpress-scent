<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_tooltip_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_tooltip_theme_setup' );
	function organic_beauty_sc_tooltip_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_tooltip_reg_shortcodes');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_tooltip id="unique_id" title="Tooltip text here"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/tooltip]
*/

if (!function_exists('organic_beauty_sc_tooltip')) {	
	function organic_beauty_sc_tooltip($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts)));
		$output = '<span' . ($id ? ' id="'.esc_attr($id).'"' : '') 
					. ' class="sc_tooltip_parent'. (!empty($class) ? ' '.esc_attr($class) : '').'"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
					. '>'
						. do_shortcode($content)
						. '<span class="sc_tooltip">' . ($title) . '</span>'
					. '</span>';
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_tooltip', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_tooltip', 'organic_beauty_sc_tooltip');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_tooltip_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_tooltip_reg_shortcodes');
	function organic_beauty_sc_tooltip_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_tooltip", array(
			"title" => esc_html__("Tooltip", 'organic-beauty'),
			"desc" => wp_kses_data( __("Create tooltip for selected text", 'organic-beauty') ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"title" => array(
					"title" => esc_html__("Title", 'organic-beauty'),
					"desc" => wp_kses_data( __("Tooltip title (required)", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"_content_" => array(
					"title" => esc_html__("Tipped content", 'organic-beauty'),
					"desc" => wp_kses_data( __("Highlighted content with tooltip", 'organic-beauty') ),
					"divider" => true,
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				),
				"id" => organic_beauty_get_sc_param('id'),
				"class" => organic_beauty_get_sc_param('class'),
				"css" => organic_beauty_get_sc_param('css')
			)
		));
	}
}
?>