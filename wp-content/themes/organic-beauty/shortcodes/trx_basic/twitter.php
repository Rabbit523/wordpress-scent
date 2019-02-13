<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_twitter_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_twitter_theme_setup' );
	function organic_beauty_sc_twitter_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_twitter_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_twitter_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_twitter id="unique_id" user="username" consumer_key="" consumer_secret="" token_key="" token_secret=""]
*/

if (!function_exists('organic_beauty_sc_twitter')) {	
	function organic_beauty_sc_twitter($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"user" => "",
			"consumer_key" => "",
			"consumer_secret" => "",
			"token_key" => "",
			"token_secret" => "",
			"count" => "3",
			"controls" => "yes",
			"interval" => "",
			"autoheight" => "no",
			"align" => "",
			"scheme" => "",
			"bg_color" => "",
			"bg_image" => "",
			"bg_overlay" => "",
			"bg_texture" => "",
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
	
		$twitter_username = $user ? $user : organic_beauty_get_theme_option('twitter_username');
		$twitter_consumer_key = $consumer_key ? $consumer_key : organic_beauty_get_theme_option('twitter_consumer_key');
		$twitter_consumer_secret = $consumer_secret ? $consumer_secret : organic_beauty_get_theme_option('twitter_consumer_secret');
		$twitter_token_key = $token_key ? $token_key : organic_beauty_get_theme_option('twitter_token_key');
		$twitter_token_secret = $token_secret ? $token_secret : organic_beauty_get_theme_option('twitter_token_secret');
		$twitter_count = max(1, $count ? $count : intval(organic_beauty_get_theme_option('twitter_count')));
	
		if (empty($id)) $id = "sc_testimonials_".str_replace('.', '', mt_rand());
		if (empty($width)) $width = "100%";
		if (!empty($height) && organic_beauty_param_is_on($autoheight)) $autoheight = "no";
		if (empty($interval)) $interval = mt_rand(5000, 10000);
	
		if ($bg_image > 0) {
			$attach = wp_get_attachment_image_src( $bg_image, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$bg_image = $attach[0];
		}
	
		if ($bg_overlay > 0) {
			if ($bg_color=='') $bg_color = organic_beauty_get_scheme_color('bg');
			$rgb = organic_beauty_hex2rgb($bg_color);
		}
		
		$class .= ($class ? ' ' : '') . organic_beauty_get_css_position_as_classes($top, $right, $bottom, $left);
		$ws = organic_beauty_get_css_dimensions_from_values($width);
		$hs = organic_beauty_get_css_dimensions_from_values('', $height);
	
		$css .= ($hs) . ($ws);
	
		$output = '';
	
		if (!empty($twitter_consumer_key) && !empty($twitter_consumer_secret) && !empty($twitter_token_key) && !empty($twitter_token_secret)) {
			$data = organic_beauty_get_twitter_data(array(
				'mode'            => 'user_timeline',
				'consumer_key'    => $twitter_consumer_key,
				'consumer_secret' => $twitter_consumer_secret,
				'token'           => $twitter_token_key,
				'secret'          => $twitter_token_secret
				)
			);
			if ($data && isset($data[0]['text'])) {
				organic_beauty_enqueue_slider('swiper');
				$output = ($bg_color!='' || $bg_image!='' || $bg_overlay>0 || $bg_texture>0 || organic_beauty_strlen($bg_texture)>2 || ($scheme && !organic_beauty_param_is_off($scheme) && !organic_beauty_param_is_inherit($scheme))
						? '<div class="sc_twitter_wrap sc_section'
								. ($scheme && !organic_beauty_param_is_off($scheme) && !organic_beauty_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
								. ($align && $align!='none' && !organic_beauty_param_is_inherit($align) ? ' align' . esc_attr($align) : '')
								. '"'
							.' style="'
								. ($bg_color !== '' && $bg_overlay==0 ? 'background-color:' . esc_attr($bg_color) . ';' : '')
								. ($bg_image !== '' ? 'background-image:url('.esc_url($bg_image).');' : '')
								. '"'
							. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
							. '>'
							. '<div class="sc_section_overlay'.($bg_texture>0 ? ' texture_bg_'.esc_attr($bg_texture) : '') . '"'
									. ' style="' 
										. ($bg_overlay>0 ? 'background-color:rgba('.(int)$rgb['r'].','.(int)$rgb['g'].','.(int)$rgb['b'].','.min(1, max(0, $bg_overlay)).');' : '')
										. (organic_beauty_strlen($bg_texture)>2 ? 'background-image:url('.esc_url($bg_texture).');' : '')
										. '"'
										. ($bg_overlay > 0 ? ' data-overlay="'.esc_attr($bg_overlay).'" data-bg_color="'.esc_attr($bg_color).'"' : '')
										. '>' 
						: '')
						. '<div class="sc_twitter'
								. (!empty($class) ? ' '.esc_attr($class) : '')
								. ($bg_color=='' && $bg_image=='' && $bg_overlay==0 && ($bg_texture=='' || $bg_texture=='0') && $align && $align!='none' && !organic_beauty_param_is_inherit($align) ? ' align' . esc_attr($align) : '')
								. '"'
							. ($bg_color=='' && $bg_image=='' && $bg_overlay==0 && ($bg_texture=='' || $bg_texture=='0') && !organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
							. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
							. '>'
								. '<div class="sc_slider_swiper sc_slider_nopagination swiper-slider-container'
										. (organic_beauty_param_is_on($controls) ? ' sc_slider_controls' : ' sc_slider_nocontrols')
										. (organic_beauty_param_is_on($autoheight) ? ' sc_slider_height_auto' : '')
										. ($hs ? ' sc_slider_height_fixed' : '')
										. '"'
									. (!empty($width) && organic_beauty_strpos($width, '%')===false ? ' data-old-width="' . esc_attr($width) . '"' : '')
									. (!empty($height) && organic_beauty_strpos($height, '%')===false ? ' data-old-height="' . esc_attr($height) . '"' : '')
									. ((int) $interval > 0 ? ' data-interval="'.esc_attr($interval).'"' : '')
									. '>'
									. '<div class="slides swiper-wrapper">';
				$cnt = 0;
				if (is_array($data) && count($data) > 0) {
					foreach ($data as $tweet) {
						if (organic_beauty_substr($tweet['text'], 0, 1)=='@') continue;
							$output .= '<div class="swiper-slide" data-style="'.esc_attr(($ws).($hs)).'" style="'.esc_attr(($ws).($hs)).'">'
										. '<div class="sc_twitter_item">'
											. '<span class="sc_twitter_icon icon-twitter"></span>'
											. '<div class="sc_twitter_content">'
												. '<a href="' . esc_url('https://twitter.com/'.($twitter_username)).'" class="sc_twitter_author" target="_blank">@' . esc_html($tweet['user']['screen_name']) . '</a> '
												. force_balance_tags(organic_beauty_prepare_twitter_text($tweet))
											. '</div>'
										. '</div>'
									. '</div>';
						if (++$cnt >= $twitter_count) break;
					}
				}
				$output .= '</div>'
						. '<div class="sc_slider_controls_wrap"><a class="sc_slider_prev" href="#"></a><a class="sc_slider_next" href="#"></a></div>'
						. '</div>'
					. '</div>'
					. ($bg_color!='' || $bg_image!='' || $bg_overlay>0 || $bg_texture>0 || organic_beauty_strlen($bg_texture)>2
						?  '</div></div>'
						: '');
			}
		}
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_twitter', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_twitter', 'organic_beauty_sc_twitter');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_twitter_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_twitter_reg_shortcodes');
	function organic_beauty_sc_twitter_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_twitter", array(
			"title" => esc_html__("Twitter", 'organic-beauty'),
			"desc" => wp_kses_data( __("Insert twitter feed into post (page)", 'organic-beauty') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"user" => array(
					"title" => esc_html__("Twitter Username", 'organic-beauty'),
					"desc" => wp_kses_data( __("Your username in the twitter account. If empty - get it from Theme Options.", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"consumer_key" => array(
					"title" => esc_html__("Consumer Key", 'organic-beauty'),
					"desc" => wp_kses_data( __("Consumer Key from the twitter account", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"consumer_secret" => array(
					"title" => esc_html__("Consumer Secret", 'organic-beauty'),
					"desc" => wp_kses_data( __("Consumer Secret from the twitter account", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"token_key" => array(
					"title" => esc_html__("Token Key", 'organic-beauty'),
					"desc" => wp_kses_data( __("Token Key from the twitter account", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"token_secret" => array(
					"title" => esc_html__("Token Secret", 'organic-beauty'),
					"desc" => wp_kses_data( __("Token Secret from the twitter account", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"count" => array(
					"title" => esc_html__("Tweets number", 'organic-beauty'),
					"desc" => wp_kses_data( __("Tweets number to show", 'organic-beauty') ),
					"divider" => true,
					"value" => 3,
					"max" => 20,
					"min" => 1,
					"type" => "spinner"
				),
				"controls" => array(
					"title" => esc_html__("Show arrows", 'organic-beauty'),
					"desc" => wp_kses_data( __("Show control buttons", 'organic-beauty') ),
					"value" => "yes",
					"type" => "switch",
					"options" => organic_beauty_get_sc_param('yes_no')
				),
				"interval" => array(
					"title" => esc_html__("Tweets change interval", 'organic-beauty'),
					"desc" => wp_kses_data( __("Tweets change interval (in milliseconds: 1000ms = 1s)", 'organic-beauty') ),
					"value" => 7000,
					"step" => 500,
					"min" => 0,
					"type" => "spinner"
				),
				"align" => array(
					"title" => esc_html__("Alignment", 'organic-beauty'),
					"desc" => wp_kses_data( __("Alignment of the tweets block", 'organic-beauty') ),
					"divider" => true,
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => organic_beauty_get_sc_param('align')
				),
				"autoheight" => array(
					"title" => esc_html__("Autoheight", 'organic-beauty'),
					"desc" => wp_kses_data( __("Change whole slider's height (make it equal current slide's height)", 'organic-beauty') ),
					"value" => "yes",
					"type" => "switch",
					"options" => organic_beauty_get_sc_param('yes_no')
				),
				"scheme" => array(
					"title" => esc_html__("Color scheme", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select color scheme for this block", 'organic-beauty') ),
					"value" => "",
					"type" => "checklist",
					"options" => organic_beauty_get_sc_param('schemes')
				),
				"bg_color" => array(
					"title" => esc_html__("Background color", 'organic-beauty'),
					"desc" => wp_kses_data( __("Any background color for this section", 'organic-beauty') ),
					"value" => "",
					"type" => "color"
				),
				"bg_image" => array(
					"title" => esc_html__("Background image URL", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select or upload image or write URL from other site for the background", 'organic-beauty') ),
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"bg_overlay" => array(
					"title" => esc_html__("Overlay", 'organic-beauty'),
					"desc" => wp_kses_data( __("Overlay color opacity (from 0.0 to 1.0)", 'organic-beauty') ),
					"min" => "0",
					"max" => "1",
					"step" => "0.1",
					"value" => "0",
					"type" => "spinner"
				),
				"bg_texture" => array(
					"title" => esc_html__("Texture", 'organic-beauty'),
					"desc" => wp_kses_data( __("Predefined texture style from 1 to 11. 0 - without texture.", 'organic-beauty') ),
					"min" => "0",
					"max" => "11",
					"step" => "1",
					"value" => "0",
					"type" => "spinner"
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
if ( !function_exists( 'organic_beauty_sc_twitter_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_twitter_reg_shortcodes_vc');
	function organic_beauty_sc_twitter_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_twitter",
			"name" => esc_html__("Twitter", 'organic-beauty'),
			"description" => wp_kses_data( __("Insert twitter feed into post (page)", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_twitter',
			"class" => "trx_sc_single trx_sc_twitter",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "user",
					"heading" => esc_html__("Twitter Username", 'organic-beauty'),
					"description" => wp_kses_data( __("Your username in the twitter account. If empty - get it from Theme Options.", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "consumer_key",
					"heading" => esc_html__("Consumer Key", 'organic-beauty'),
					"description" => wp_kses_data( __("Consumer Key from the twitter account", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "consumer_secret",
					"heading" => esc_html__("Consumer Secret", 'organic-beauty'),
					"description" => wp_kses_data( __("Consumer Secret from the twitter account", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "token_key",
					"heading" => esc_html__("Token Key", 'organic-beauty'),
					"description" => wp_kses_data( __("Token Key from the twitter account", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "token_secret",
					"heading" => esc_html__("Token Secret", 'organic-beauty'),
					"description" => wp_kses_data( __("Token Secret from the twitter account", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "count",
					"heading" => esc_html__("Tweets number", 'organic-beauty'),
					"description" => wp_kses_data( __("Number tweets to show", 'organic-beauty') ),
					"class" => "",
					"divider" => true,
					"value" => 3,
					"type" => "textfield"
				),
				array(
					"param_name" => "controls",
					"heading" => esc_html__("Show arrows", 'organic-beauty'),
					"description" => wp_kses_data( __("Show control buttons", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(organic_beauty_get_sc_param('yes_no')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "interval",
					"heading" => esc_html__("Tweets change interval", 'organic-beauty'),
					"description" => wp_kses_data( __("Tweets change interval (in milliseconds: 1000ms = 1s)", 'organic-beauty') ),
					"class" => "",
					"value" => "7000",
					"type" => "textfield"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", 'organic-beauty'),
					"description" => wp_kses_data( __("Alignment of the tweets block", 'organic-beauty') ),
					"class" => "",
					"value" => array_flip(organic_beauty_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "autoheight",
					"heading" => esc_html__("Autoheight", 'organic-beauty'),
					"description" => wp_kses_data( __("Change whole slider's height (make it equal current slide's height)", 'organic-beauty') ),
					"class" => "",
					"value" => array("Autoheight" => "yes" ),
					"type" => "checkbox"
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
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", 'organic-beauty'),
					"description" => wp_kses_data( __("Any background color for this section", 'organic-beauty') ),
					"group" => esc_html__('Colors and Images', 'organic-beauty'),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_image",
					"heading" => esc_html__("Background image URL", 'organic-beauty'),
					"description" => wp_kses_data( __("Select background image from library for this section", 'organic-beauty') ),
					"group" => esc_html__('Colors and Images', 'organic-beauty'),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "bg_overlay",
					"heading" => esc_html__("Overlay", 'organic-beauty'),
					"description" => wp_kses_data( __("Overlay color opacity (from 0.0 to 1.0)", 'organic-beauty') ),
					"group" => esc_html__('Colors and Images', 'organic-beauty'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "bg_texture",
					"heading" => esc_html__("Texture", 'organic-beauty'),
					"description" => wp_kses_data( __("Texture style from 1 to 11. Empty or 0 - without texture.", 'organic-beauty') ),
					"group" => esc_html__('Colors and Images', 'organic-beauty'),
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
		) );
		
		class WPBakeryShortCode_Trx_Twitter extends ORGANIC_BEAUTY_VC_ShortCodeSingle {}
	}
}
?>