<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_br_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_br_theme_setup' );
	function organic_beauty_sc_br_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_br_reg_shortcodes');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_br clear="left|right|both"]
*/

if (!function_exists('organic_beauty_sc_br')) {	
	function organic_beauty_sc_br($atts, $content = null) {
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			"clear" => ""
		), $atts)));
		$output = in_array($clear, array('left', 'right', 'both', 'all')) 
			? '<div class="clearfix" style="clear:' . str_replace('all', 'both', $clear) . '"></div>'
			: '<br />';
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_br', $atts, $content);
	}
	organic_beauty_require_shortcode("trx_br", "organic_beauty_sc_br");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_br_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_br_reg_shortcodes');
	function organic_beauty_sc_br_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_br", array(
			"title" => esc_html__("Break", 'organic-beauty'),
			"desc" => wp_kses_data( __("Line break with clear floating (if need)", 'organic-beauty') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"clear" => 	array(
					"title" => esc_html__("Clear floating", 'organic-beauty'),
					"desc" => wp_kses_data( __("Clear floating (if need)", 'organic-beauty') ),
					"value" => "",
					"type" => "checklist",
					"options" => array(
						'none' => esc_html__('None', 'organic-beauty'),
						'left' => esc_html__('Left', 'organic-beauty'),
						'right' => esc_html__('Right', 'organic-beauty'),
						'both' => esc_html__('Both', 'organic-beauty')
					)
				)
			)
		));
	}
}
?>