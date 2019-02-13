<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_image_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_image_theme_setup' );
	function organic_beauty_sc_image_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_image_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_image_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_image id="unique_id" src="image_url" width="width_in_pixels" height="height_in_pixels" title="image's_title" align="left|right"]
*/

if (!function_exists('organic_beauty_sc_image')) {	
	function organic_beauty_sc_image($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"align" => "",
			"shape" => "square",
			"src" => "",
			"url" => "",
			"icon" => "",
			"link" => "",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => "",
			"width" => "",
			"height" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . organic_beauty_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= organic_beauty_get_css_dimensions_from_values($width, $height);
		$src = $src!='' ? $src : $url;
		if ($src > 0) {
			$attach = wp_get_attachment_image_src( $src, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$src = $attach[0];
		}
		if (!empty($width) || !empty($height)) {
			$w = !empty($width) && strlen(intval($width)) == strlen($width) ? $width : null;
			$h = !empty($height) && strlen(intval($height)) == strlen($height) ? $height : null;
			if ($w || $h) $src = organic_beauty_get_resized_image_url($src, $w, $h);
		}
		if (trim($link)) organic_beauty_enqueue_popup();
		$output = empty($src) ? '' : ('<figure' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_image ' . ($align && $align!='none' ? ' align' . esc_attr($align) : '') . (!empty($shape) ? ' sc_image_shape_'.esc_attr($shape) : '') . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
			. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. '>'
				. (trim($link) ? '<a href="'.esc_url($link).'">' : '')
				. '<img src="'.esc_url($src).'" alt="" />'
				. (trim($link) ? '</a>' : '')
				. (trim($title) || trim($icon) ? '<figcaption><span'.($icon ? ' class="'.esc_attr($icon).'"' : '').'></span> ' . ($title) . '</figcaption>' : '')
			. '</figure>');
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_image', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_image', 'organic_beauty_sc_image');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_image_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_image_reg_shortcodes');
	function organic_beauty_sc_image_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_image", array(
			"title" => esc_html__("Image", 'organic-beauty'),
			"desc" => wp_kses_data( __("Insert image into your post (page)", 'organic-beauty') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"url" => array(
					"title" => esc_html__("URL for image file", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select or upload image or write URL from other site", 'organic-beauty') ),
					"readonly" => false,
					"value" => "",
					"type" => "media",
					"before" => array(
						'sizes' => true		// If you want allow user select thumb size for image. Otherwise, thumb size is ignored - image fullsize used
					)
				),
				"title" => array(
					"title" => esc_html__("Title", 'organic-beauty'),
					"desc" => wp_kses_data( __("Image title (if need)", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"icon" => array(
					"title" => esc_html__("Icon before title",  'organic-beauty'),
					"desc" => wp_kses_data( __('Select icon for the title from Fontello icons set',  'organic-beauty') ),
					"value" => "",
					"type" => "icons",
					"options" => organic_beauty_get_sc_param('icons')
				),
				"align" => array(
					"title" => esc_html__("Float image", 'organic-beauty'),
					"desc" => wp_kses_data( __("Float image to left or right side", 'organic-beauty') ),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => organic_beauty_get_sc_param('float')
				), 
				"shape" => array(
					"title" => esc_html__("Image Shape", 'organic-beauty'),
					"desc" => wp_kses_data( __("Shape of the image: square (rectangle) or round", 'organic-beauty') ),
					"value" => "square",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => array(
						"square" => esc_html__('Square', 'organic-beauty'),
						"round" => esc_html__('Round', 'organic-beauty')
					)
				), 
				"link" => array(
					"title" => esc_html__("Link", 'organic-beauty'),
					"desc" => wp_kses_data( __("The link URL from the image", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
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
if ( !function_exists( 'organic_beauty_sc_image_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_image_reg_shortcodes_vc');
	function organic_beauty_sc_image_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_image",
			"name" => esc_html__("Image", 'organic-beauty'),
			"description" => wp_kses_data( __("Insert image", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_image',
			"class" => "trx_sc_single trx_sc_image",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "url",
					"heading" => esc_html__("Select image", 'organic-beauty'),
					"description" => wp_kses_data( __("Select image from library", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Image alignment", 'organic-beauty'),
					"description" => wp_kses_data( __("Align image to left or right side", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(organic_beauty_get_sc_param('float')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "shape",
					"heading" => esc_html__("Image shape", 'organic-beauty'),
					"description" => wp_kses_data( __("Shape of the image: square or round", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('Square', 'organic-beauty') => 'square',
						esc_html__('Round', 'organic-beauty') => 'round'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", 'organic-beauty'),
					"description" => wp_kses_data( __("Image's title", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Title's icon", 'organic-beauty'),
					"description" => wp_kses_data( __("Select icon for the title from Fontello icons set", 'organic-beauty') ),
					"class" => "",
					"value" => organic_beauty_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link", 'organic-beauty'),
					"description" => wp_kses_data( __("The link URL from the image", 'organic-beauty') ),
					"admin_label" => true,
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
			)
		) );
		
		class WPBakeryShortCode_Trx_Image extends ORGANIC_BEAUTY_VC_ShortCodeSingle {}
	}
}
?>