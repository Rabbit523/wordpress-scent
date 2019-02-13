<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_audio_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_audio_theme_setup' );
	function organic_beauty_sc_audio_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_audio_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_audio_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_audio url="http://trex2.themerex.dnw/wp-content/uploads/2014/12/Dream-Music-Relax.mp3" image="http://trex2.themerex.dnw/wp-content/uploads/2014/10/post_audio.jpg" title="Insert Audio Title Here" author="Lily Hunter" controls="show" autoplay="off"]
*/

if (!function_exists('organic_beauty_sc_audio')) {	
	function organic_beauty_sc_audio($atts, $content = null) {
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"author" => "",
			"image" => "",
			"mp3" => '',
			"wav" => '',
			"src" => '',
			"url" => '',
			"align" => '',
			"controls" => "",
			"autoplay" => "",
			"frame" => "on",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"width" => '',
			"height" => '',
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		if ($src=='' && $url=='' && isset($atts[0])) {
			$src = $atts[0];
		}
		if ($src=='') {
			if ($url) $src = $url;
			else if ($mp3) $src = $mp3;
			else if ($wav) $src = $wav;
		}
		if ($image > 0) {
			$attach = wp_get_attachment_image_src( $image, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$image = $attach[0];
		}
		$class .= ($class ? ' ' : '') . organic_beauty_get_css_position_as_classes($top, $right, $bottom, $left);
		$data = ($title != ''  ? ' data-title="'.esc_attr($title).'"'   : '')
				. ($author != '' ? ' data-author="'.esc_attr($author).'"' : '')
				. ($image != ''  ? ' data-image="'.esc_url($image).'"'   : '')
				. ($align && $align!='none' ? ' data-align="'.esc_attr($align).'"' : '')
				. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '');
		$audio = '<audio'
			. ($id ? ' id="'.esc_attr($id).'"' : '')
			. ' class="sc_audio' . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
			. ' src="'.esc_url($src).'"'
			. (organic_beauty_param_is_on($controls) ? ' controls="controls"' : '')
			. (organic_beauty_param_is_on($autoplay) && is_single() ? ' autoplay="autoplay"' : '')
			. ' width="'.esc_attr($width).'" height="'.esc_attr($height).'"'
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. ($data)
			. '></audio>';
		if ( organic_beauty_get_custom_option('substitute_audio')=='no') {
			if (organic_beauty_param_is_on($frame)) {
				$audio = organic_beauty_get_audio_frame($audio, $image, $s);
			}
		} else {
			if ((isset($_GET['vc_editable']) && $_GET['vc_editable']=='true') && (isset($_POST['action']) && $_POST['action']=='vc_load_shortcode')) {
				$audio = organic_beauty_substitute_audio($audio, false);
			}
		}
		if (organic_beauty_get_theme_option('use_mediaelement')=='yes')
			organic_beauty_enqueue_script('wp-mediaelement');
		return apply_filters('organic_beauty_shortcode_output', $audio, 'trx_audio', $atts, $content);
	}
	organic_beauty_require_shortcode("trx_audio", "organic_beauty_sc_audio");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_audio_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_audio_reg_shortcodes');
	function organic_beauty_sc_audio_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_audio", array(
			"title" => esc_html__("Audio", 'organic-beauty'),
			"desc" => wp_kses_data( __("Insert audio player", 'organic-beauty') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"url" => array(
					"title" => esc_html__("URL for audio file", 'organic-beauty'),
					"desc" => wp_kses_data( __("URL for audio file", 'organic-beauty') ),
					"readonly" => false,
					"value" => "",
					"type" => "media",
					"before" => array(
						'title' => esc_html__('Choose audio', 'organic-beauty'),
						'action' => 'media_upload',
						'type' => 'audio',
						'multiple' => false,
						'linked_field' => '',
						'captions' => array( 	
							'choose' => esc_html__('Choose audio file', 'organic-beauty'),
							'update' => esc_html__('Select audio file', 'organic-beauty')
						)
					),
					"after" => array(
						'icon' => 'icon-cancel',
						'action' => 'media_reset'
					)
				),
				"image" => array(
					"title" => esc_html__("Cover image", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select or upload image or write URL from other site for audio cover", 'organic-beauty') ),
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"title" => array(
					"title" => esc_html__("Title", 'organic-beauty'),
					"desc" => wp_kses_data( __("Title of the audio file", 'organic-beauty') ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"author" => array(
					"title" => esc_html__("Author", 'organic-beauty'),
					"desc" => wp_kses_data( __("Author of the audio file", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"controls" => array(
					"title" => esc_html__("Show controls", 'organic-beauty'),
					"desc" => wp_kses_data( __("Show controls in audio player", 'organic-beauty') ),
					"divider" => true,
					"size" => "medium",
					"value" => "show",
					"type" => "switch",
					"options" => organic_beauty_get_sc_param('show_hide')
				),
				"autoplay" => array(
					"title" => esc_html__("Autoplay audio", 'organic-beauty'),
					"desc" => wp_kses_data( __("Autoplay audio on page load", 'organic-beauty') ),
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
if ( !function_exists( 'organic_beauty_sc_audio_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_audio_reg_shortcodes_vc');
	function organic_beauty_sc_audio_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_audio",
			"name" => esc_html__("Audio", 'organic-beauty'),
			"description" => wp_kses_data( __("Insert audio player", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_audio',
			"class" => "trx_sc_single trx_sc_audio",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "url",
					"heading" => esc_html__("URL for audio file", 'organic-beauty'),
					"description" => wp_kses_data( __("Put here URL for audio file", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "image",
					"heading" => esc_html__("Cover image", 'organic-beauty'),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site for audio cover", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", 'organic-beauty'),
					"description" => wp_kses_data( __("Title of the audio file", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "author",
					"heading" => esc_html__("Author", 'organic-beauty'),
					"description" => wp_kses_data( __("Author of the audio file", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "controls",
					"heading" => esc_html__("Controls", 'organic-beauty'),
					"description" => wp_kses_data( __("Show/hide controls", 'organic-beauty') ),
					"class" => "",
					"value" => array("Hide controls" => "hide" ),
					"type" => "checkbox"
				),
				array(
					"param_name" => "autoplay",
					"heading" => esc_html__("Autoplay", 'organic-beauty'),
					"description" => wp_kses_data( __("Autoplay audio on page load", 'organic-beauty') ),
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
		) );
		
		class WPBakeryShortCode_Trx_Audio extends ORGANIC_BEAUTY_VC_ShortCodeSingle {}
	}
}
?>