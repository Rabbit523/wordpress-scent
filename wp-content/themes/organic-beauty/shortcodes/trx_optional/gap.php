<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_gap_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_gap_theme_setup' );
	function organic_beauty_sc_gap_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_gap_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_gap_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

//[trx_gap]Fullwidth content[/trx_gap]

if (!function_exists('organic_beauty_sc_gap')) {	
	function organic_beauty_sc_gap($atts, $content = null) {
		if (organic_beauty_in_shortcode_blogger()) return '';
		$output = organic_beauty_gap_start() . do_shortcode($content) . organic_beauty_gap_end();
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_gap', $atts, $content);
	}
	organic_beauty_require_shortcode("trx_gap", "organic_beauty_sc_gap");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_gap_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_gap_reg_shortcodes');
	function organic_beauty_sc_gap_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_gap", array(
			"title" => esc_html__("Gap", 'organic-beauty'),
			"desc" => wp_kses_data( __("Insert gap (fullwidth area) in the post content. Attention! Use the gap only in the posts (pages) without left or right sidebar", 'organic-beauty') ),
			"decorate" => true,
			"container" => true,
			"params" => array(
				"_content_" => array(
					"title" => esc_html__("Gap content", 'organic-beauty'),
					"desc" => wp_kses_data( __("Gap inner content", 'organic-beauty') ),
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_gap_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_gap_reg_shortcodes_vc');
	function organic_beauty_sc_gap_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_gap",
			"name" => esc_html__("Gap", 'organic-beauty'),
			"description" => wp_kses_data( __("Insert gap (fullwidth area) in the post content", 'organic-beauty') ),
			"category" => esc_html__('Structure', 'organic-beauty'),
			'icon' => 'icon_trx_gap',
			"class" => "trx_sc_collection trx_sc_gap",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => false,
			"params" => array(
			)
		) );
		
		class WPBakeryShortCode_Trx_Gap extends ORGANIC_BEAUTY_VC_ShortCodeCollection {}
	}
}
?>