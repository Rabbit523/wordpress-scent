<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_googlemap_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_googlemap_theme_setup' );
	function organic_beauty_sc_googlemap_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_googlemap_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_googlemap_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

//[trx_googlemap id="unique_id" width="width_in_pixels_or_percent" height="height_in_pixels"]
//	[trx_googlemap_marker address="your_address"]
//[/trx_googlemap]

if (!function_exists('organic_beauty_sc_googlemap')) {
	function organic_beauty_sc_googlemap($atts, $content = null) {
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"zoom" => 16,
			"style" => 'default',
			"scheme" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"width" => "100%",
			"height" => "400",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . organic_beauty_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= organic_beauty_get_css_dimensions_from_values($width, $height);
		if (empty($id)) $id = 'sc_googlemap_'.str_replace('.', '', mt_rand());
		if (empty($style)) $style = organic_beauty_get_custom_option('googlemap_style');
		$api_key = organic_beauty_get_theme_option('api_google');
		wp_enqueue_script( 'googlemap', organic_beauty_get_protocol().'://maps.google.com/maps/api/js'.($api_key ? '?key='.$api_key : ''), array(), null, true );
		wp_enqueue_script( 'organic_beauty-googlemap-script', organic_beauty_get_file_url('js/core.googlemap.js'), array(), null, true );
		organic_beauty_storage_set('sc_googlemap_markers', array());
		$content = do_shortcode($content);
		$output = '';
		$markers = organic_beauty_storage_get('sc_googlemap_markers');
		if (count($markers) == 0) {
			$markers[] = array(
				'title' => organic_beauty_get_custom_option('googlemap_title'),
				'description' => organic_beauty_strmacros(organic_beauty_get_custom_option('googlemap_description')),
				'latlng' => organic_beauty_get_custom_option('googlemap_latlng'),
				'address' => organic_beauty_get_custom_option('googlemap_address'),
				'point' => organic_beauty_get_custom_option('googlemap_marker')
			);
		}
		$output .= 
			($content ? '<div id="'.esc_attr($id).'_wrap" class="sc_googlemap_wrap'
					. ($scheme && !organic_beauty_param_is_off($scheme) && !organic_beauty_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '')
					. '">' : '')
			. '<div id="'.esc_attr($id).'"'
				. ' class="sc_googlemap'. (!empty($class) ? ' '.esc_attr($class) : '').'"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
				. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
				. ' data-zoom="'.esc_attr($zoom).'"'
				. ' data-style="'.esc_attr($style).'"'
				. '>';
		$cnt = 0;
		foreach ($markers as $marker) {
			$cnt++;
			if (empty($marker['id'])) $marker['id'] = $id.'_'.intval($cnt);
			$output .= '<div id="'.esc_attr($marker['id']).'" class="sc_googlemap_marker"'
				. ' data-title="'.esc_attr($marker['title']).'"'
				. ' data-description="'.esc_attr(organic_beauty_strmacros($marker['description'])).'"'
				. ' data-address="'.esc_attr($marker['address']).'"'
				. ' data-latlng="'.esc_attr($marker['latlng']).'"'
				. ' data-point="'.esc_attr($marker['point']).'"'
				. '></div>';
		}
		$output .= '</div>'
			. ($content ? '<div class="sc_googlemap_content">' . trim($content) . '</div></div>' : '');
			
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_googlemap', $atts, $content);
	}
	organic_beauty_require_shortcode("trx_googlemap", "organic_beauty_sc_googlemap");
}


if (!function_exists('organic_beauty_sc_googlemap_marker')) {
	function organic_beauty_sc_googlemap_marker($atts, $content = null) {
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"address" => "",
			"latlng" => "",
			"point" => "",
			// Common params
			"id" => ""
		), $atts)));
		if (!empty($point)) {
			if ($point > 0) {
				$attach = wp_get_attachment_image_src( $point, 'full' );
				if (isset($attach[0]) && $attach[0]!='')
					$point = $attach[0];
			}
		}
		$content = do_shortcode($content);
		organic_beauty_storage_set_array('sc_googlemap_markers', '', array(
			'id' => $id,
			'title' => $title,
			'description' => !empty($content) ? $content : $address,
			'latlng' => $latlng,
			'address' => $address,
			'point' => $point ? $point : organic_beauty_get_custom_option('googlemap_marker')
			)
		);
		return '';
	}
	organic_beauty_require_shortcode("trx_googlemap_marker", "organic_beauty_sc_googlemap_marker");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_googlemap_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_googlemap_reg_shortcodes');
	function organic_beauty_sc_googlemap_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_googlemap", array(
			"title" => esc_html__("Google map", 'organic-beauty'),
			"desc" => wp_kses_data( __("Insert Google map with specified markers", 'organic-beauty') ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"zoom" => array(
					"title" => esc_html__("Zoom", 'organic-beauty'),
					"desc" => wp_kses_data( __("Map zoom factor", 'organic-beauty') ),
					"divider" => true,
					"value" => 16,
					"min" => 1,
					"max" => 20,
					"type" => "spinner"
				),
				"style" => array(
					"title" => esc_html__("Map style", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select map style", 'organic-beauty') ),
					"value" => "default",
					"type" => "checklist",
					"options" => organic_beauty_get_sc_param('googlemap_styles')
				),
				"scheme" => array(
					"title" => esc_html__("Color scheme", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select color scheme for this block", 'organic-beauty') ),
					"value" => "",
					"type" => "checklist",
					"options" => organic_beauty_get_sc_param('schemes')
				),
				"width" => organic_beauty_shortcodes_width('100%'),
				"height" => organic_beauty_shortcodes_height(240),
				"top" => organic_beauty_get_sc_param('top'),
				"bottom" => organic_beauty_get_sc_param('bottom'),
				"left" => organic_beauty_get_sc_param('left'),
				"right" => organic_beauty_get_sc_param('right'),
				"id" => organic_beauty_get_sc_param('id'),
				"class" => organic_beauty_get_sc_param('class'),
				"animation" => organic_beauty_get_sc_param('animation'),
				"css" => organic_beauty_get_sc_param('css')
			),
			"children" => array(
				"name" => "trx_googlemap_marker",
				"title" => esc_html__("Google map marker", 'organic-beauty'),
				"desc" => wp_kses_data( __("Google map marker", 'organic-beauty') ),
				"decorate" => false,
				"container" => true,
				"params" => array(
					"address" => array(
						"title" => esc_html__("Address", 'organic-beauty'),
						"desc" => wp_kses_data( __("Address of this marker", 'organic-beauty') ),
						"value" => "",
						"type" => "text"
					),
					"latlng" => array(
						"title" => esc_html__("Latitude and Longitude", 'organic-beauty'),
						"desc" => wp_kses_data( __("Comma separated marker's coorditanes (instead Address)", 'organic-beauty') ),
						"value" => "",
						"type" => "text"
					),
					"point" => array(
						"title" => esc_html__("URL for marker image file", 'organic-beauty'),
						"desc" => wp_kses_data( __("Select or upload image or write URL from other site for this marker. If empty - use default marker", 'organic-beauty') ),
						"readonly" => false,
						"value" => "",
						"type" => "media"
					),
					"title" => array(
						"title" => esc_html__("Title", 'organic-beauty'),
						"desc" => wp_kses_data( __("Title for this marker", 'organic-beauty') ),
						"value" => "",
						"type" => "text"
					),
					"_content_" => array(
						"title" => esc_html__("Description", 'organic-beauty'),
						"desc" => wp_kses_data( __("Description for this marker", 'organic-beauty') ),
						"rows" => 4,
						"value" => "",
						"type" => "textarea"
					),
					"id" => organic_beauty_get_sc_param('id')
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_googlemap_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_googlemap_reg_shortcodes_vc');
	function organic_beauty_sc_googlemap_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_googlemap",
			"name" => esc_html__("Google map", 'organic-beauty'),
			"description" => wp_kses_data( __("Insert Google map with desired address or coordinates", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_googlemap',
			"class" => "trx_sc_collection trx_sc_googlemap",
			"content_element" => true,
			"is_container" => true,
			"as_parent" => array('only' => 'trx_googlemap_marker,trx_form,trx_section,trx_block,trx_promo'),
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "zoom",
					"heading" => esc_html__("Zoom", 'organic-beauty'),
					"description" => wp_kses_data( __("Map zoom factor", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "16",
					"type" => "textfield"
				),
				array(
					"param_name" => "style",
					"heading" => esc_html__("Style", 'organic-beauty'),
					"description" => wp_kses_data( __("Map custom style", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(organic_beauty_get_sc_param('googlemap_styles')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "scheme",
					"heading" => esc_html__("Color scheme", 'organic-beauty'),
					"description" => wp_kses_data( __("Select color scheme for this block", 'organic-beauty') ),
					"class" => "",
					"value" => array_flip(organic_beauty_get_sc_param('schemes')),
					"type" => "dropdown"
				),
				organic_beauty_get_vc_param('id'),
				organic_beauty_get_vc_param('class'),
				organic_beauty_get_vc_param('animation'),
				organic_beauty_get_vc_param('css'),
				organic_beauty_vc_width('100%'),
				organic_beauty_vc_height(240),
				organic_beauty_get_vc_param('margin_top'),
				organic_beauty_get_vc_param('margin_bottom'),
				organic_beauty_get_vc_param('margin_left'),
				organic_beauty_get_vc_param('margin_right')
			)
		) );
		
		vc_map( array(
			"base" => "trx_googlemap_marker",
			"name" => esc_html__("Googlemap marker", 'organic-beauty'),
			"description" => wp_kses_data( __("Insert new marker into Google map", 'organic-beauty') ),
			"class" => "trx_sc_collection trx_sc_googlemap_marker",
			'icon' => 'icon_trx_googlemap_marker',
			"show_settings_on_create" => true,
			"content_element" => true,
			"is_container" => true,
			"as_child" => array('only' => 'trx_googlemap'), // Use only|except attributes to limit parent (separate multiple values with comma)
			"params" => array(
				array(
					"param_name" => "address",
					"heading" => esc_html__("Address", 'organic-beauty'),
					"description" => wp_kses_data( __("Address of this marker", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "latlng",
					"heading" => esc_html__("Latitude and Longitude", 'organic-beauty'),
					"description" => wp_kses_data( __("Comma separated marker's coorditanes (instead Address)", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", 'organic-beauty'),
					"description" => wp_kses_data( __("Title for this marker", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "point",
					"heading" => esc_html__("URL for marker image file", 'organic-beauty'),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site for this marker. If empty - use default marker", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				organic_beauty_get_vc_param('id')
			)
		) );
		
		class WPBakeryShortCode_Trx_Googlemap extends ORGANIC_BEAUTY_VC_ShortCodeCollection {}
		class WPBakeryShortCode_Trx_Googlemap_Marker extends ORGANIC_BEAUTY_VC_ShortCodeCollection {}
	}
}
?>