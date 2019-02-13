<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_video_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_video_theme_setup' );
	function organic_beauty_sc_video_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_video_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_video_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

//[trx_video id="unique_id" url="http://player.vimeo.com/video/20245032?title=0&amp;byline=0&amp;portrait=0" width="" height=""]

if (!function_exists('organic_beauty_sc_video')) {	
	function organic_beauty_sc_video($atts, $content = null) {
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"url" => '',
			"src" => '',
			"image" => '',
			"ratio" => '16:9',
			"autoplay" => 'off',
			"align" => '',
			"bg_image" => '',
			"bg_top" => '',
			"bg_bottom" => '',
			"bg_left" => '',
			"bg_right" => '',
			"frame" => "on",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"width" => '',
			"height" => '',
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
	
		if (empty($autoplay)) $autoplay = 'off';
		
		$ratio = empty($ratio) ? "16:9" : str_replace(array('/','\\','-'), ':', $ratio);
		$ratio_parts = explode(':', $ratio);
		if (empty($height) && empty($width)) {
			$width='100%';
			if (organic_beauty_param_is_off(organic_beauty_get_custom_option('substitute_video'))) $height="400";
		}
		$ed = organic_beauty_substr($width, -1);
		if (empty($height) && !empty($width) && $ed!='%') {
			$height = round($width / $ratio_parts[0] * $ratio_parts[1]);
		}
		if (!empty($height) && empty($width)) {
			$width = round($height * $ratio_parts[0] / $ratio_parts[1]);
		}
		$class .= ($class ? ' ' : '') . organic_beauty_get_css_position_as_classes($top, $right, $bottom, $left);
		$css_dim = organic_beauty_get_css_dimensions_from_values($width, $height);
		$css_bg = organic_beauty_get_css_paddings_from_values($bg_top, $bg_right, $bg_bottom, $bg_left);
	
		if ($src=='' && $url=='' && isset($atts[0])) {
			$src = $atts[0];
		}
		$url = $src!='' ? $src : $url;
		if ($image!='' && organic_beauty_param_is_off($image))
			$image = '';
		else {
			if (organic_beauty_param_is_on($autoplay) && is_singular() && !organic_beauty_storage_get('blog_streampage'))
				$image = '';
			else {
				if ($image > 0) {
					$attach = wp_get_attachment_image_src( $image, 'full' );
					if (isset($attach[0]) && $attach[0]!='')
						$image = $attach[0];
				}
				if ($bg_image) {
					$thumb_sizes = organic_beauty_get_thumb_sizes(array(
						'layout' => 'grid_3'
					));
					if (!is_single() || !empty($image)) $image = organic_beauty_get_resized_image_url(empty($image) ? get_the_ID() : $image, $thumb_sizes['w'], $thumb_sizes['h'], null, false, false, false);
				} else
					if (!is_single() || !empty($image)) $image = organic_beauty_get_resized_image_url(empty($image) ? get_the_ID() : $image, $ed!='%' ? $width : null, $height);
				if (empty($image) && (!is_singular() || organic_beauty_storage_get('blog_streampage')))	// || organic_beauty_param_is_off($autoplay)))
					$image = organic_beauty_get_video_cover_image($url);
			}
		}
		if ($bg_image > 0) {
			$attach = wp_get_attachment_image_src( $bg_image, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$bg_image = $attach[0];
		}
		if ($bg_image) {
			$css_bg .= $css . 'background-image: url('.esc_url($bg_image).');';
			$css = $css_dim;
		} else {
			$css .= $css_dim;
		}
	
		$url = organic_beauty_get_video_player_url($src!='' ? $src : $url);
		
		$video = '<video' . ($id ? ' id="' . esc_attr($id) . '"' : '') 
			. ' class="sc_video"'
			. ' src="' . esc_url($url) . '"'
			. ' width="' . esc_attr($width) . '" height="' . esc_attr($height) . '"' 
			. ' data-width="' . esc_attr($width) . '" data-height="' . esc_attr($height) . '"' 
			. ' data-ratio="'.esc_attr($ratio).'"'
			. ($image ? ' poster="'.esc_attr($image).'" data-image="'.esc_attr($image).'"' : '') 
			. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
			. ($align && $align!='none' ? ' data-align="'.esc_attr($align).'"' : '')
			. ($class ? ' data-class="'.esc_attr($class).'"' : '')
			. ($bg_image ? ' data-bg-image="'.esc_attr($bg_image).'"' : '') 
			. ($css_bg!='' ? ' data-style="'.esc_attr($css_bg).'"' : '') 
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
			. (($image && organic_beauty_param_is_on(organic_beauty_get_custom_option('substitute_video'))) || (organic_beauty_param_is_on($autoplay) && is_singular() && !organic_beauty_storage_get('blog_streampage')) ? ' autoplay="autoplay"' : '') 
			. ' controls="controls" loop="loop"'
			. '>'
			. '</video>';
		if (organic_beauty_param_is_off(organic_beauty_get_custom_option('substitute_video'))) {
			if (organic_beauty_param_is_on($frame)) $video = organic_beauty_get_video_frame($video, $image, $css, $css_bg);
		} else {
			if ((isset($_GET['vc_editable']) && $_GET['vc_editable']=='true') && (isset($_POST['action']) && $_POST['action']=='vc_load_shortcode')) {
				$video = organic_beauty_substitute_video($video, $width, $height, false);
			}
		}
		if (organic_beauty_get_theme_option('use_mediaelement')=='yes')
			organic_beauty_enqueue_script('wp-mediaelement');
		return apply_filters('organic_beauty_shortcode_output', $video, 'trx_video', $atts, $content);
	}
	organic_beauty_require_shortcode("trx_video", "organic_beauty_sc_video");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_video_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_video_reg_shortcodes');
	function organic_beauty_sc_video_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_video", array(
			"title" => esc_html__("Video", 'organic-beauty'),
			"desc" => wp_kses_data( __("Insert video player", 'organic-beauty') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"url" => array(
					"title" => esc_html__("URL for video file", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select video from media library or paste URL for video file from other site", 'organic-beauty') ),
					"readonly" => false,
					"value" => "",
					"type" => "media",
					"before" => array(
						'title' => esc_html__('Choose video', 'organic-beauty'),
						'action' => 'media_upload',
						'type' => 'video',
						'multiple' => false,
						'linked_field' => '',
						'captions' => array( 	
							'choose' => esc_html__('Choose video file', 'organic-beauty'),
							'update' => esc_html__('Select video file', 'organic-beauty')
						)
					),
					"after" => array(
						'icon' => 'icon-cancel',
						'action' => 'media_reset'
					)
				),
				"ratio" => array(
					"title" => esc_html__("Ratio", 'organic-beauty'),
					"desc" => wp_kses_data( __("Ratio of the video", 'organic-beauty') ),
					"value" => "16:9",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => array(
						"16:9" => esc_html__("16:9", 'organic-beauty'),
						"4:3" => esc_html__("4:3", 'organic-beauty')
					)
				),
				"autoplay" => array(
					"title" => esc_html__("Autoplay video", 'organic-beauty'),
					"desc" => wp_kses_data( __("Autoplay video on page load", 'organic-beauty') ),
					"value" => "off",
					"type" => "switch",
					"options" => organic_beauty_get_sc_param('on_off')
				),
				"align" => array(
					"title" => esc_html__("Align", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select block alignment", 'organic-beauty') ),
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => organic_beauty_get_sc_param('align')
				),
				"image" => array(
					"title" => esc_html__("Cover image", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select or upload image or write URL from other site for video preview", 'organic-beauty') ),
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"bg_image" => array(
					"title" => esc_html__("Background image", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select or upload image or write URL from other site for video background. Attention! If you use background image - specify paddings below from background margins to video block in percents!", 'organic-beauty') ),
					"divider" => true,
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"bg_top" => array(
					"title" => esc_html__("Top offset", 'organic-beauty'),
					"desc" => wp_kses_data( __("Top offset (padding) inside background image to video block (in percent). For example: 3%", 'organic-beauty') ),
					"dependency" => array(
						'bg_image' => array('not_empty')
					),
					"value" => "",
					"type" => "text"
				),
				"bg_bottom" => array(
					"title" => esc_html__("Bottom offset", 'organic-beauty'),
					"desc" => wp_kses_data( __("Bottom offset (padding) inside background image to video block (in percent). For example: 3%", 'organic-beauty') ),
					"dependency" => array(
						'bg_image' => array('not_empty')
					),
					"value" => "",
					"type" => "text"
				),
				"bg_left" => array(
					"title" => esc_html__("Left offset", 'organic-beauty'),
					"desc" => wp_kses_data( __("Left offset (padding) inside background image to video block (in percent). For example: 20%", 'organic-beauty') ),
					"dependency" => array(
						'bg_image' => array('not_empty')
					),
					"value" => "",
					"type" => "text"
				),
				"bg_right" => array(
					"title" => esc_html__("Right offset", 'organic-beauty'),
					"desc" => wp_kses_data( __("Right offset (padding) inside background image to video block (in percent). For example: 12%", 'organic-beauty') ),
					"dependency" => array(
						'bg_image' => array('not_empty')
					),
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
if ( !function_exists( 'organic_beauty_sc_video_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_video_reg_shortcodes_vc');
	function organic_beauty_sc_video_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_video",
			"name" => esc_html__("Video", 'organic-beauty'),
			"description" => wp_kses_data( __("Insert video player", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_video',
			"class" => "trx_sc_single trx_sc_video",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "url",
					"heading" => esc_html__("URL for video file", 'organic-beauty'),
					"description" => wp_kses_data( __("Paste URL for video file", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "ratio",
					"heading" => esc_html__("Ratio", 'organic-beauty'),
					"description" => wp_kses_data( __("Select ratio for display video", 'organic-beauty') ),
					"class" => "",
					"value" => array(
						esc_html__('16:9', 'organic-beauty') => "16:9",
						esc_html__('4:3', 'organic-beauty') => "4:3"
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "autoplay",
					"heading" => esc_html__("Autoplay video", 'organic-beauty'),
					"description" => wp_kses_data( __("Autoplay video on page load", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => array("Autoplay" => "on" ),
					"type" => "checkbox"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", 'organic-beauty'),
					"description" => wp_kses_data( __("Select block alignment", 'organic-beauty') ),
					"class" => "",
					"value" => array_flip(organic_beauty_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "image",
					"heading" => esc_html__("Cover image", 'organic-beauty'),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site for video preview", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "bg_image",
					"heading" => esc_html__("Background image", 'organic-beauty'),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site for video background. Attention! If you use background image - specify paddings below from background margins to video block in percents!", 'organic-beauty') ),
					"group" => esc_html__('Background', 'organic-beauty'),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "bg_top",
					"heading" => esc_html__("Top offset", 'organic-beauty'),
					"description" => wp_kses_data( __("Top offset (padding) from background image to video block (in percent). For example: 3%", 'organic-beauty') ),
					"group" => esc_html__('Background', 'organic-beauty'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "bg_bottom",
					"heading" => esc_html__("Bottom offset", 'organic-beauty'),
					"description" => wp_kses_data( __("Bottom offset (padding) from background image to video block (in percent). For example: 3%", 'organic-beauty') ),
					"group" => esc_html__('Background', 'organic-beauty'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "bg_left",
					"heading" => esc_html__("Left offset", 'organic-beauty'),
					"description" => wp_kses_data( __("Left offset (padding) from background image to video block (in percent). For example: 20%", 'organic-beauty') ),
					"group" => esc_html__('Background', 'organic-beauty'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "bg_right",
					"heading" => esc_html__("Right offset", 'organic-beauty'),
					"description" => wp_kses_data( __("Right offset (padding) from background image to video block (in percent). For example: 12%", 'organic-beauty') ),
					"group" => esc_html__('Background', 'organic-beauty'),
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
		
		class WPBakeryShortCode_Trx_Video extends ORGANIC_BEAUTY_VC_ShortCodeSingle {}
	}
}
?>