<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_price_block_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_price_block_theme_setup' );
	function organic_beauty_sc_price_block_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_price_block_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_price_block_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

if (!function_exists('organic_beauty_sc_price_block')) {	
	function organic_beauty_sc_price_block($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"style" => 1,
			"title" => "",
			"link" => "",
			"link_text" => "",
			"icon" => "",
			"money" => "",
			"currency" => "$",
			"period" => "",
			"align" => "",
			"scheme" => "",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"width" => "",
			"height" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$output = '';
		$class .= ($class ? ' ' : '') . organic_beauty_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= organic_beauty_get_css_dimensions_from_values($width, $height);
		if ($money) $money = do_shortcode('[trx_price money="'.esc_attr($money).'" period="'.esc_attr($period).'"'.($currency ? ' currency="'.esc_attr($currency).'"' : '').']');
		$content = do_shortcode(organic_beauty_sc_clear_around($content));
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
					. ' class="sc_price_block sc_price_block_style_'.max(1, min(3, $style))
						. (!empty($class) ? ' '.esc_attr($class) : '')
						. ($scheme && !organic_beauty_param_is_off($scheme) && !organic_beauty_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
						. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
						. '"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
					. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
					. '>'
				. (!empty($title) ? '<div class="sc_price_block_title"><span>'.($title).'</span></div>' : '')
				. '<div class="sc_price_block_money">'
					. (!empty($icon) ? '<div class="sc_price_block_icon '.esc_attr($icon).'"></div>' : '')
					. ($money)
				. '</div>'
				. (!empty($content) ? '<div class="sc_price_block_description">'.($content).'</div>' : '')
				. (!empty($link_text) ? '<div class="sc_price_block_link">'.do_shortcode('[trx_button size="large" link="'.($link ? esc_url($link) : '#').'"]'.($link_text).'[/trx_button]').'</div>' : '')
			. '</div>';
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_price_block', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_price_block', 'organic_beauty_sc_price_block');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_price_block_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_price_block_reg_shortcodes');
	function organic_beauty_sc_price_block_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_price_block", array(
			"title" => esc_html__("Price block", 'organic-beauty'),
			"desc" => wp_kses_data( __("Insert price block with title, price and description", 'organic-beauty') ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"title" => array(
					"title" => esc_html__("Title", 'organic-beauty'),
					"desc" => wp_kses_data( __("Block title", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"link" => array(
					"title" => esc_html__("Link URL", 'organic-beauty'),
					"desc" => wp_kses_data( __("URL for link from button (at bottom of the block)", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"link_text" => array(
					"title" => esc_html__("Link text", 'organic-beauty'),
					"desc" => wp_kses_data( __("Text (caption) for the link button (at bottom of the block). If empty - button not showed", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"money" => array(
					"title" => esc_html__("Money", 'organic-beauty'),
					"desc" => wp_kses_data( __("Money value (dot or comma separated)", 'organic-beauty') ),
					"divider" => true,
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
				"scheme" => array(
					"title" => esc_html__("Color scheme", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select color scheme for this block", 'organic-beauty') ),
					"value" => "",
					"type" => "checklist",
					"options" => organic_beauty_get_sc_param('schemes')
				),
				"align" => array(
					"title" => esc_html__("Alignment", 'organic-beauty'),
					"desc" => wp_kses_data( __("Align price to left or right side", 'organic-beauty') ),
					"divider" => true,
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => organic_beauty_get_sc_param('float')
				), 
				"_content_" => array(
					"title" => esc_html__("Description", 'organic-beauty'),
					"desc" => wp_kses_data( __("Description for this price block", 'organic-beauty') ),
					"divider" => true,
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				),
				"width" => organic_beauty_shortcodes_width(),
				"height" => organic_beauty_shortcodes_height(),
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
if ( !function_exists( 'organic_beauty_sc_price_block_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_price_block_reg_shortcodes_vc');
	function organic_beauty_sc_price_block_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_price_block",
			"name" => esc_html__("Price block", 'organic-beauty'),
			"description" => wp_kses_data( __("Insert price block with title, price and description", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_price_block',
			"class" => "trx_sc_single trx_sc_price_block",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", 'organic-beauty'),
					"description" => wp_kses_data( __("Block title", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link URL", 'organic-beauty'),
					"description" => wp_kses_data( __("URL for link from button (at bottom of the block)", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link_text",
					"heading" => esc_html__("Link text", 'organic-beauty'),
					"description" => wp_kses_data( __("Text (caption) for the link button (at bottom of the block). If empty - button not showed", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "money",
					"heading" => esc_html__("Money", 'organic-beauty'),
					"description" => wp_kses_data( __("Money value (dot or comma separated)", 'organic-beauty') ),
					"admin_label" => true,
					"group" => esc_html__('Money', 'organic-beauty'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "currency",
					"heading" => esc_html__("Currency symbol", 'organic-beauty'),
					"description" => wp_kses_data( __("Currency character", 'organic-beauty') ),
					"admin_label" => true,
					"group" => esc_html__('Money', 'organic-beauty'),
					"class" => "",
					"value" => "$",
					"type" => "textfield"
				),
				array(
					"param_name" => "period",
					"heading" => esc_html__("Period", 'organic-beauty'),
					"description" => wp_kses_data( __("Period text (if need). For example: monthly, daily, etc.", 'organic-beauty') ),
					"admin_label" => true,
					"group" => esc_html__('Money', 'organic-beauty'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "scheme",
					"heading" => esc_html__("Color scheme", 'organic-beauty'),
					"description" => wp_kses_data( __("Select color scheme for this block", 'organic-beauty') ),
					"group" => esc_html__('Colors and Images', 'organic-beauty'),
					"class" => "",
					"value" => array_flip(organic_beauty_get_sc_param('schemes')),
					"type" => "dropdown"
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
				array(
					"param_name" => "content",
					"heading" => esc_html__("Description", 'organic-beauty'),
					"description" => wp_kses_data( __("Description for this price block", 'organic-beauty') ),
					"class" => "",
					"value" => "",
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
			'js_view' => 'VcTrxTextView'
		) );
		
		class WPBakeryShortCode_Trx_PriceBlock extends ORGANIC_BEAUTY_VC_ShortCodeSingle {}
	}
}
?>