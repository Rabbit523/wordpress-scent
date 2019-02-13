<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_table_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_table_theme_setup' );
	function organic_beauty_sc_table_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_table_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_table_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_table id="unique_id" style="1"]
Table content, generated on one of many public internet resources, for example: http://www.impressivewebs.com/html-table-code-generator/
[/trx_table]
*/

if (!function_exists('organic_beauty_sc_table')) {	
	function organic_beauty_sc_table($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"align" => "",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => "",
			"width" => "100%"
		), $atts)));
		$class .= ($class ? ' ' : '') . organic_beauty_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= organic_beauty_get_css_dimensions_from_values($width);
		$content = str_replace(
					array('<p><table', 'table></p>', '><br />'),
					array('<table', 'table>', '>'),
					html_entity_decode($content, ENT_COMPAT, 'UTF-8'));
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_table' 
					. (!empty($align) && $align!='none' ? ' align'.esc_attr($align) : '') 
					. (!empty($class) ? ' '.esc_attr($class) : '') 
					. '"'
				. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
				.'>' 
				. do_shortcode($content) 
				. '</div>';
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_table', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_table', 'organic_beauty_sc_table');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_table_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_table_reg_shortcodes');
	function organic_beauty_sc_table_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_table", array(
			"title" => esc_html__("Table", 'organic-beauty'),
			"desc" => wp_kses_data( __("Insert a table into post (page). ", 'organic-beauty') ),
			"decorate" => true,
			"container" => true,
			"params" => array(
				"align" => array(
					"title" => esc_html__("Content alignment", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select alignment for each table cell", 'organic-beauty') ),
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => organic_beauty_get_sc_param('align')
				),
				"_content_" => array(
					"title" => esc_html__("Table content", 'organic-beauty'),
					"desc" => wp_kses_data( __("Content, created with any table-generator", 'organic-beauty') ),
					"divider" => true,
					"rows" => 8,
					"value" => "Paste here table content, generated on one of many public internet resources, for example: http://www.impressivewebs.com/html-table-code-generator/ or http://html-tables.com/",
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
if ( !function_exists( 'organic_beauty_sc_table_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_table_reg_shortcodes_vc');
	function organic_beauty_sc_table_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_table",
			"name" => esc_html__("Table", 'organic-beauty'),
			"description" => wp_kses_data( __("Insert a table", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_table',
			"class" => "trx_sc_container trx_sc_table",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "align",
					"heading" => esc_html__("Cells content alignment", 'organic-beauty'),
					"description" => wp_kses_data( __("Select alignment for each table cell", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(organic_beauty_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "content",
					"heading" => esc_html__("Table content", 'organic-beauty'),
					"description" => wp_kses_data( __("Content, created with any table-generator", 'organic-beauty') ),
					"class" => "",
					"value" => esc_html__("Paste here table content, generated on one of many public internet resources, for example: http://www.impressivewebs.com/html-table-code-generator/ or http://html-tables.com/", 'organic-beauty'),
					"type" => "textarea_html"
				),
				organic_beauty_get_vc_param('id'),
				organic_beauty_get_vc_param('class'),
				organic_beauty_get_vc_param('animation'),
				organic_beauty_get_vc_param('css'),
				organic_beauty_vc_width(),
				organic_beauty_vc_height(),
				organic_beauty_get_vc_param('margin_top'),
				organic_beauty_get_vc_param('margin_bottom'),
				organic_beauty_get_vc_param('margin_left'),
				organic_beauty_get_vc_param('margin_right')
			),
			'js_view' => 'VcTrxTextContainerView'
		) );
		
		class WPBakeryShortCode_Trx_Table extends ORGANIC_BEAUTY_VC_ShortCodeContainer {}
	}
}
?>