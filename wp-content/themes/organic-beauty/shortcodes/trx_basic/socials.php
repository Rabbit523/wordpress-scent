<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_socials_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_socials_theme_setup' );
	function organic_beauty_sc_socials_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_socials_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_socials_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_socials id="unique_id" size="small"]
	[trx_social_item name="facebook" url="profile url" icon="path for the icon"]
	[trx_social_item name="twitter" url="profile url"]
[/trx_socials]
*/

if (!function_exists('organic_beauty_sc_socials')) {	
	function organic_beauty_sc_socials($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"size" => "small",		// tiny | small | medium | large
			"shape" => "square",	// round | square
			"type" => organic_beauty_get_theme_setting('socials_type'),	// icons | images
			"socials" => "",
			"custom" => "no",
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
		organic_beauty_storage_set('sc_social_data', array(
			'icons' => false,
            'type' => $type
            )
        );
		if (!empty($socials)) {
			$allowed = explode('|', $socials);
			$list = array();
			for ($i=0; $i<count($allowed); $i++) {
				$s = explode('=', $allowed[$i]);
				if (!empty($s[1])) {
					$list[] = array(
						'icon'	=> $type=='images' ? organic_beauty_get_socials_url($s[0]) : 'icon-'.trim($s[0]),
						'url'	=> $s[1]
						);
				}
			}
			if (count($list) > 0) organic_beauty_storage_set_array('sc_social_data', 'icons', $list);
		} else if (organic_beauty_param_is_on($custom))
			$content = do_shortcode($content);
		if (organic_beauty_storage_get_array('sc_social_data', 'icons')===false) organic_beauty_storage_set_array('sc_social_data', 'icons', organic_beauty_get_custom_option('social_icons'));
		$output = organic_beauty_prepare_socials(organic_beauty_storage_get_array('sc_social_data', 'icons'));
		$output = $output
			? '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_socials sc_socials_type_' . esc_attr($type) . ' sc_socials_shape_' . esc_attr($shape) . ' sc_socials_size_' . esc_attr($size) . (!empty($class) ? ' '.esc_attr($class) : '') . '"' 
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
				. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
				. '>' 
				. ($output)
				. '</div>'
			: '';
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_socials', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_socials', 'organic_beauty_sc_socials');
}


if (!function_exists('organic_beauty_sc_social_item')) {	
	function organic_beauty_sc_social_item($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"name" => "",
			"url" => "",
			"icon" => ""
		), $atts)));
		if (empty($icon)) {
			if (!empty($name)) {
				$type = organic_beauty_storage_get_array('sc_social_data', 'type');
				if ($type=='images') {
					if (file_exists(organic_beauty_get_socials_dir($name.'.png')))
						$icon = organic_beauty_get_socials_url($name.'.png');
				} else
					$icon = 'icon-'.esc_attr($name);
			}
		} else if ((int) $icon > 0) {
			$attach = wp_get_attachment_image_src( $icon, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$icon = $attach[0];
		}
		if (!empty($icon) && !empty($url)) {
			if (organic_beauty_storage_get_array('sc_social_data', 'icons')===false) organic_beauty_storage_set_array('sc_social_data', 'icons', array());
			organic_beauty_storage_set_array2('sc_social_data', 'icons', '', array(
				'icon' => $icon,
				'url' => $url
				)
			);
		}
		return '';
	}
	organic_beauty_require_shortcode('trx_social_item', 'organic_beauty_sc_social_item');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_socials_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_socials_reg_shortcodes');
	function organic_beauty_sc_socials_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_socials", array(
			"title" => esc_html__("Social icons", 'organic-beauty'),
			"desc" => wp_kses_data( __("List of social icons (with hovers)", 'organic-beauty') ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"type" => array(
					"title" => esc_html__("Icon's type", 'organic-beauty'),
					"desc" => wp_kses_data( __("Type of the icons - images or font icons", 'organic-beauty') ),
					"value" => organic_beauty_get_theme_setting('socials_type'),
					"options" => array(
						'icons' => esc_html__('Icons', 'organic-beauty'),
						'images' => esc_html__('Images', 'organic-beauty')
					),
					"type" => "checklist"
				), 
				"size" => array(
					"title" => esc_html__("Icon's size", 'organic-beauty'),
					"desc" => wp_kses_data( __("Size of the icons", 'organic-beauty') ),
					"value" => "small",
					"options" => organic_beauty_get_sc_param('sizes'),
					"type" => "checklist"
				), 
				"shape" => array(
					"title" => esc_html__("Icon's shape", 'organic-beauty'),
					"desc" => wp_kses_data( __("Shape of the icons", 'organic-beauty') ),
					"value" => "square",
					"options" => organic_beauty_get_sc_param('shapes'),
					"type" => "checklist"
				), 
				"socials" => array(
					"title" => esc_html__("Manual socials list", 'organic-beauty'),
					"desc" => wp_kses_data( __("Custom list of social networks. For example: twitter=http://twitter.com/my_profile|facebook=http://facebook.com/my_profile. If empty - use socials from Theme options.", 'organic-beauty') ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"custom" => array(
					"title" => esc_html__("Custom socials", 'organic-beauty'),
					"desc" => wp_kses_data( __("Make custom icons from inner shortcodes (prepare it on tabs)", 'organic-beauty') ),
					"divider" => true,
					"value" => "no",
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
			),
			"children" => array(
				"name" => "trx_social_item",
				"title" => esc_html__("Custom social item", 'organic-beauty'),
				"desc" => wp_kses_data( __("Custom social item: name, profile url and icon url", 'organic-beauty') ),
				"decorate" => false,
				"container" => false,
				"params" => array(
					"name" => array(
						"title" => esc_html__("Social name", 'organic-beauty'),
						"desc" => wp_kses_data( __("Name (slug) of the social network (twitter, facebook, linkedin, etc.)", 'organic-beauty') ),
						"value" => "",
						"type" => "text"
					),
					"url" => array(
						"title" => esc_html__("Your profile URL", 'organic-beauty'),
						"desc" => wp_kses_data( __("URL of your profile in specified social network", 'organic-beauty') ),
						"value" => "",
						"type" => "text"
					),
					"icon" => array(
						"title" => esc_html__("URL (source) for icon file", 'organic-beauty'),
						"desc" => wp_kses_data( __("Select or upload image or write URL from other site for the current social icon", 'organic-beauty') ),
						"readonly" => false,
						"value" => "",
						"type" => "media"
					)
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_socials_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_socials_reg_shortcodes_vc');
	function organic_beauty_sc_socials_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_socials",
			"name" => esc_html__("Social icons", 'organic-beauty'),
			"description" => wp_kses_data( __("Custom social icons", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_socials',
			"class" => "trx_sc_collection trx_sc_socials",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"as_parent" => array('only' => 'trx_social_item'),
			"params" => array_merge(array(
				array(
					"param_name" => "type",
					"heading" => esc_html__("Icon's type", 'organic-beauty'),
					"description" => wp_kses_data( __("Type of the icons - images or font icons", 'organic-beauty') ),
					"class" => "",
					"std" => organic_beauty_get_theme_setting('socials_type'),
					"value" => array(
						esc_html__('Icons', 'organic-beauty') => 'icons',
						esc_html__('Images', 'organic-beauty') => 'images'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "size",
					"heading" => esc_html__("Icon's size", 'organic-beauty'),
					"description" => wp_kses_data( __("Size of the icons", 'organic-beauty') ),
					"class" => "",
					"std" => "small",
					"value" => array_flip(organic_beauty_get_sc_param('sizes')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "shape",
					"heading" => esc_html__("Icon's shape", 'organic-beauty'),
					"description" => wp_kses_data( __("Shape of the icons", 'organic-beauty') ),
					"class" => "",
					"std" => "square",
					"value" => array_flip(organic_beauty_get_sc_param('shapes')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "socials",
					"heading" => esc_html__("Manual socials list", 'organic-beauty'),
					"description" => wp_kses_data( __("Custom list of social networks. For example: twitter=http://twitter.com/my_profile|facebook=http://facebook.com/my_profile. If empty - use socials from Theme options.", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "custom",
					"heading" => esc_html__("Custom socials", 'organic-beauty'),
					"description" => wp_kses_data( __("Make custom icons from inner shortcodes (prepare it on tabs)", 'organic-beauty') ),
					"class" => "",
					"value" => array(esc_html__('Custom socials', 'organic-beauty') => 'yes'),
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
			))
		) );
		
		
		vc_map( array(
			"base" => "trx_social_item",
			"name" => esc_html__("Custom social item", 'organic-beauty'),
			"description" => wp_kses_data( __("Custom social item: name, profile url and icon url", 'organic-beauty') ),
			"show_settings_on_create" => true,
			"content_element" => true,
			"is_container" => false,
			'icon' => 'icon_trx_social_item',
			"class" => "trx_sc_single trx_sc_social_item",
			"as_child" => array('only' => 'trx_socials'),
			"as_parent" => array('except' => 'trx_socials'),
			"params" => array(
				array(
					"param_name" => "name",
					"heading" => esc_html__("Social name", 'organic-beauty'),
					"description" => wp_kses_data( __("Name (slug) of the social network (twitter, facebook, linkedin, etc.)", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "url",
					"heading" => esc_html__("Your profile URL", 'organic-beauty'),
					"description" => wp_kses_data( __("URL of your profile in specified social network", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("URL (source) for icon file", 'organic-beauty'),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site for the current social icon", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				)
			)
		) );
		
		class WPBakeryShortCode_Trx_Socials extends ORGANIC_BEAUTY_VC_ShortCodeCollection {}
		class WPBakeryShortCode_Trx_Social_Item extends ORGANIC_BEAUTY_VC_ShortCodeSingle {}
	}
}
?>