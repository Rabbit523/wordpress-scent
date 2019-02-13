<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_hide_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_hide_theme_setup' );
	function organic_beauty_sc_hide_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_hide_reg_shortcodes');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_hide selector="unique_id"]
*/

if (!function_exists('organic_beauty_sc_hide')) {	
	function organic_beauty_sc_hide($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"selector" => "",
			"hide" => "on",
			"delay" => 0
		), $atts)));
		$selector = trim(chop($selector));
		if (!empty($selector)) {
			organic_beauty_storage_concat('js_code', '
				'.($delay>0 ? 'setTimeout(function() {' : '').'
					jQuery("'.esc_attr($selector).'").' . ($hide=='on' ? 'hide' : 'show') . '();
				'.($delay>0 ? '},'.($delay).');' : '').'
			');
		}
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_hide', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_hide', 'organic_beauty_sc_hide');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_hide_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_hide_reg_shortcodes');
	function organic_beauty_sc_hide_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_hide", array(
			"title" => esc_html__("Hide/Show any block", 'organic-beauty'),
			"desc" => wp_kses_data( __("Hide or Show any block with desired CSS-selector", 'organic-beauty') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"selector" => array(
					"title" => esc_html__("Selector", 'organic-beauty'),
					"desc" => wp_kses_data( __("Any block's CSS-selector", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"hide" => array(
					"title" => esc_html__("Hide or Show", 'organic-beauty'),
					"desc" => wp_kses_data( __("New state for the block: hide or show", 'organic-beauty') ),
					"value" => "yes",
					"size" => "small",
					"options" => organic_beauty_get_sc_param('yes_no'),
					"type" => "switch"
				)
			)
		));
	}
}
?>