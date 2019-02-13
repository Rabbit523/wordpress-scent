<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_chat_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_chat_theme_setup' );
	function organic_beauty_sc_chat_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_chat_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_chat_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_chat id="unique_id" link="url" title=""]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_chat]
[trx_chat id="unique_id" link="url" title=""]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_chat]
...
*/

if (!function_exists('organic_beauty_sc_chat')) {	
	function organic_beauty_sc_chat($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"photo" => "",
			"title" => "",
			"link" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"width" => "",
			"height" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . organic_beauty_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= organic_beauty_get_css_dimensions_from_values($width, $height);
		$title = $title=='' ? $link : $title;
		if (!empty($photo)) {
			if ($photo > 0) {
				$attach = wp_get_attachment_image_src( $photo, 'full' );
				if (isset($attach[0]) && $attach[0]!='')
					$photo = $attach[0];
			}
			$photo = organic_beauty_get_resized_image_tag($photo, 75, 75);
		}
		$content = do_shortcode($content);
		if (organic_beauty_substr($content, 0, 2)!='<p') $content = '<p>' . ($content) . '</p>';
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_chat' . (!empty($class) ? ' '.esc_attr($class) : '') . '"' 
				. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
				. ($css ? ' style="'.esc_attr($css).'"' : '') 
				. '>'
					. '<div class="sc_chat_inner">'
						. ($photo ? '<div class="sc_chat_avatar">'.($photo).'</div>' : '')
						. ($title == '' ? '' : ('<div class="sc_chat_title">' . ($link!='' ? '<a href="'.esc_url($link).'">' : '') . ($title) . ($link!='' ? '</a>' : '') . '</div>'))
						. '<div class="sc_chat_content">'.($content).'</div>'
					. '</div>'
				. '</div>';
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_chat', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_chat', 'organic_beauty_sc_chat');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_chat_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_chat_reg_shortcodes');
	function organic_beauty_sc_chat_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_chat", array(
			"title" => esc_html__("Chat", 'organic-beauty'),
			"desc" => wp_kses_data( __("Chat message", 'organic-beauty') ),
			"decorate" => true,
			"container" => true,
			"params" => array(
				"title" => array(
					"title" => esc_html__("Item title", 'organic-beauty'),
					"desc" => wp_kses_data( __("Chat item title", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"photo" => array(
					"title" => esc_html__("Item photo", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select or upload image or write URL from other site for the item photo (avatar)", 'organic-beauty') ),
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"link" => array(
					"title" => esc_html__("Item link", 'organic-beauty'),
					"desc" => wp_kses_data( __("Chat item link", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"_content_" => array(
					"title" => esc_html__("Chat item content", 'organic-beauty'),
					"desc" => wp_kses_data( __("Current chat item content", 'organic-beauty') ),
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
if ( !function_exists( 'organic_beauty_sc_chat_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_chat_reg_shortcodes_vc');
	function organic_beauty_sc_chat_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_chat",
			"name" => esc_html__("Chat", 'organic-beauty'),
			"description" => wp_kses_data( __("Chat message", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_chat',
			"class" => "trx_sc_container trx_sc_chat",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "title",
					"heading" => esc_html__("Item title", 'organic-beauty'),
					"description" => wp_kses_data( __("Title for current chat item", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "photo",
					"heading" => esc_html__("Item photo", 'organic-beauty'),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site for the item photo (avatar)", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link URL", 'organic-beauty'),
					"description" => wp_kses_data( __("URL for the link on chat title click", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
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
		
		class WPBakeryShortCode_Trx_Chat extends ORGANIC_BEAUTY_VC_ShortCodeContainer {}
	}
}
?>