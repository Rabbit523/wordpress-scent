<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_intro_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_intro_theme_setup' );
	function organic_beauty_sc_intro_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_intro_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_intro_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

if (!function_exists('organic_beauty_sc_intro')) {	
	function organic_beauty_sc_intro($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"style" => 1,
			"align" => "none",
			"image" => "",
			"bg_color" => "",
			"icon" => "",
			"scheme" => "",
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"link" => '',
			"link_caption" => esc_html__('Read more', 'organic-beauty'),
			"link2" => '',
			"link2_caption" => '',
			"url" => "",
			"content_position" => "",
			"content_width" => "",
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
	
		if ($image > 0) {
			$attach = wp_get_attachment_image_src($image, 'full');
			if (isset($attach[0]) && $attach[0]!='')
				$image = $attach[0];
		}
		
		$width  = organic_beauty_prepare_css_value($width);
		$height = organic_beauty_prepare_css_value($height);
		
		$class .= ($class ? ' ' : '') . organic_beauty_get_css_position_as_classes($top, $right, $bottom, $left);

		$css .= organic_beauty_get_css_dimensions_from_values($width,$height);
		$css .= ($image ? 'background: url('.$image.');' : '');
		$css .= ($bg_color ? 'background-color: '.$bg_color.';' : '');
		
		$buttons = (!empty($link) || !empty($link2) 
						? '<div class="sc_intro_buttons sc_item_buttons">'
							. (!empty($link) 
								? '<div class="sc_intro_button sc_item_button">'.do_shortcode('[trx_button link="'.esc_url($link).'" size="medium"'.($style==4 ? ' icon="icon-right"' : '').']'.esc_html($link_caption).'[/trx_button]').'</div>'
								: '')
							. (!empty($link2) && $style==2 
								? '<div class="sc_intro_button sc_item_button">'.do_shortcode('[trx_button link="'.esc_url($link2).'" size="medium"]'.esc_html($link2_caption).'[/trx_button]').'</div>' 
								: '')
							. '</div>'
						: '');



		$output = '<div '.(!empty($url) ? 'data-href="'.esc_url($url).'"' : '') 
					. ($id ? ' id="'.esc_attr($id).'"' : '') 
					. ' class="sc_intro' 
						. ($class ? ' ' . esc_attr($class) : '') 
						. ($content_position && $style==1 ? ' sc_intro_position_' . esc_attr($content_position) : '') 
						. ($style==5 ? ' small_padding' : '') 
						. ($style==2 ? ' extra_padding' : '')
						. ($style==4 ? ' extra_border' : '')
						. ($scheme && !organic_beauty_param_is_off($scheme) && !organic_beauty_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '')
						. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
						. '"'
					. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
					. ($css ? ' style="'.esc_attr($css).'"' : '')
					.'>' 
					. '<div class="sc_intro_inner '.($style ? ' sc_intro_style_' . esc_attr($style) : '').'"'.(!empty($content_width) ? ' style="width:'.esc_attr($content_width).';"' : '').'>'
						. (!empty($icon) && $style==5 ? '<div class="sc_intro_icon '.esc_attr($icon).'"></div>' : '')
						. '<div class="sc_intro_content">'
							. (!empty($subtitle) && $style!=4 && $style!=5 ? '<h6 class="sc_intro_subtitle">' . trim(organic_beauty_strmacros($subtitle)) . '</h6>' : '')
							. (!empty($title) ? '<h2 class="sc_intro_title">' . trim(organic_beauty_strmacros($title)) . '</h2>' : '')
							. (!empty($description) && $style!=1 ? '<div class="sc_intro_descr">' . trim(organic_beauty_strmacros($description)) . '</div>' : '')
							. ($style==2 || $style==3 || $style==4 ? $buttons : '')
						. '</div>'
					. '</div>'
				.'</div>';
	
	
	
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_intro', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_intro', 'organic_beauty_sc_intro');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_intro_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_intro_reg_shortcodes');
	function organic_beauty_sc_intro_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_intro", array(
			"title" => esc_html__("Intro", 'organic-beauty'),
			"desc" => wp_kses_data( __("Insert Intro block in your page (post)", 'organic-beauty') ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Style", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select style to display block", 'organic-beauty') ),
					"value" => "1",
					"type" => "checklist",
					"options" => organic_beauty_get_list_styles(1, 5)
				),
				"align" => array(
					"title" => esc_html__("Alignment of the intro block", 'organic-beauty'),
					"desc" => wp_kses_data( __("Align whole intro block to left or right side of the page or parent container", 'organic-beauty') ),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => organic_beauty_get_sc_param('float')
				), 
				"image" => array(
					"title" => esc_html__("Image URL", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select the intro image from the library for this section", 'organic-beauty') ),
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"bg_color" => array(
					"title" => esc_html__("Background color", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select background color for the intro", 'organic-beauty') ),
					"value" => "",
					"type" => "color"
				),
				"icon" => array(
					"title" => esc_html__('Icon',  'organic-beauty'),
					"desc" => wp_kses_data( __("Select icon from Fontello icons set",  'organic-beauty') ),
					"dependency" => array(
						'style' => array(5)
					),
					"value" => "",
					"type" => "icons",
					"options" => organic_beauty_get_sc_param('icons')
				),
				"content_position" => array(
					"title" => esc_html__('Content position', 'organic-beauty'),
					"desc" => wp_kses_data( __("Select content position", 'organic-beauty') ),
					"dependency" => array(
						'style' => array(1)
					),
					"value" => "top_left",
					"type" => "checklist",
					"options" => array(
						'top_left' => esc_html__('Top Left', 'organic-beauty'),
						'top_right' => esc_html__('Top Right', 'organic-beauty'),
						'bottom_right' => esc_html__('Bottom Right', 'organic-beauty'),
						'bottom_left' => esc_html__('Bottom Left', 'organic-beauty')
					)
				),
				"content_width" => array(
					"title" => esc_html__('Content width', 'organic-beauty'),
					"desc" => wp_kses_data( __("Select content width", 'organic-beauty') ),
					"dependency" => array(
						'style' => array(1)
					),
					"value" => "100%",
					"type" => "checklist",
					"options" => array(
						'100%' => esc_html__('100%', 'organic-beauty'),
						'90%' => esc_html__('90%', 'organic-beauty'),
						'80%' => esc_html__('80%', 'organic-beauty'),
						'70%' => esc_html__('70%', 'organic-beauty'),
						'60%' => esc_html__('60%', 'organic-beauty'),
						'50%' => esc_html__('50%', 'organic-beauty'),
						'40%' => esc_html__('40%', 'organic-beauty'),
						'30%' => esc_html__('30%', 'organic-beauty')
					)
				),
				"subtitle" => array(
					"title" => esc_html__("Subtitle", 'organic-beauty'),
					"desc" => wp_kses_data( __("Subtitle for the block", 'organic-beauty') ),
					"divider" => true,
					"dependency" => array(
						'style' => array(1,2,3)
					),
					"value" => "",
					"type" => "text"
				),
				"title" => array(
					"title" => esc_html__("Title", 'organic-beauty'),
					"desc" => wp_kses_data( __("Title for the block", 'organic-beauty') ),
					"value" => "",
					"type" => "textarea"
				),
				"description" => array(
					"title" => esc_html__("Description", 'organic-beauty'),
					"desc" => wp_kses_data( __("Short description for the block", 'organic-beauty') ),
					"dependency" => array(
						'style' => array(2,3,4,5),
					),
					"value" => "",
					"type" => "textarea"
				),
				"link" => array(
					"title" => esc_html__("Button URL", 'organic-beauty'),
					"desc" => wp_kses_data( __("Link URL for the button at the bottom of the block", 'organic-beauty') ),
					"dependency" => array(
						'style' => array(2,3,4),
					),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"link_caption" => array(
					"title" => esc_html__("Button caption", 'organic-beauty'),
					"desc" => wp_kses_data( __("Caption for the button at the bottom of the block", 'organic-beauty') ),
					"dependency" => array(
						'style' => array(2,3,4),
					),
					"value" => "",
					"type" => "text"
				),
				"link2" => array(
					"title" => esc_html__("Button 2 URL", 'organic-beauty'),
					"desc" => wp_kses_data( __("Link URL for the second button at the bottom of the block", 'organic-beauty') ),
					"dependency" => array(
						'style' => array(2)
					),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"link2_caption" => array(
					"title" => esc_html__("Button 2 caption", 'organic-beauty'),
					"desc" => wp_kses_data( __("Caption for the second button at the bottom of the block", 'organic-beauty') ),
					"dependency" => array(
						'style' => array(2)
					),
					"value" => "",
					"type" => "text"
				),
				"url" => array(
					"title" => esc_html__("Link", 'organic-beauty'),
					"desc" => wp_kses_data( __("Link of the intro block", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"scheme" => array(
					"title" => esc_html__("Color scheme", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select color scheme for the section with text", 'organic-beauty') ),
					"value" => "",
					"type" => "checklist",
					"options" => organic_beauty_get_sc_param('schemes')
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
if ( !function_exists( 'organic_beauty_sc_intro_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_intro_reg_shortcodes_vc');
	function organic_beauty_sc_intro_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_intro",
			"name" => esc_html__("Intro", 'organic-beauty'),
			"description" => wp_kses_data( __("Insert Intro block", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_intro',
			"class" => "trx_sc_single trx_sc_intro",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Style of the block", 'organic-beauty'),
					"description" => wp_kses_data( __("Select style to display this block", 'organic-beauty') ),
					"class" => "",
					"admin_label" => true,
					"value" => array_flip(organic_beauty_get_list_styles(1, 5)),
					"type" => "dropdown"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment of the block", 'organic-beauty'),
					"description" => wp_kses_data( __("Align whole intro block to left or right side of the page or parent container", 'organic-beauty') ),
					"class" => "",
					"std" => 'none',
					"value" => array_flip(organic_beauty_get_sc_param('float')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "image",
					"heading" => esc_html__("Image URL", 'organic-beauty'),
					"description" => wp_kses_data( __("Select the intro image from the library for this section", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", 'organic-beauty'),
					"description" => wp_kses_data( __("Select background color for the intro", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Icon", 'organic-beauty'),
					"description" => wp_kses_data( __("Select icon from Fontello icons set", 'organic-beauty') ),
					"class" => "",
					'dependency' => array(
						'element' => 'style',
						'value' => array('5')
					),
					"value" => organic_beauty_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "content_position",
					"heading" => esc_html__("Content position", 'organic-beauty'),
					"description" => wp_kses_data( __("Select content position", 'organic-beauty') ),
					"class" => "",
					"admin_label" => true,
					"value" => array(
						esc_html__('Top Left', 'organic-beauty') => 'top_left',
						esc_html__('Top Right', 'organic-beauty') => 'top_right',
						esc_html__('Bottom Right', 'organic-beauty') => 'bottom_right',
						esc_html__('Bottom Left', 'organic-beauty') => 'bottom_left'
					),
					'dependency' => array(
						'element' => 'style',
						'value' => array('1')
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "content_width",
					"heading" => esc_html__("Content width", 'organic-beauty'),
					"description" => wp_kses_data( __("Select content width", 'organic-beauty') ),
					"class" => "",
					"admin_label" => true,
					"value" => array(
						esc_html__('100%', 'organic-beauty') => '100%',
						esc_html__('90%', 'organic-beauty') => '90%',
						esc_html__('80%', 'organic-beauty') => '80%',
						esc_html__('70%', 'organic-beauty') => '70%',
						esc_html__('60%', 'organic-beauty') => '60%',
						esc_html__('50%', 'organic-beauty') => '50%',
						esc_html__('40%', 'organic-beauty') => '40%',
						esc_html__('30%', 'organic-beauty') => '30%'
					),
					'dependency' => array(
						'element' => 'style',
						'value' => array('1')
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "subtitle",
					"heading" => esc_html__("Subtitle", 'organic-beauty'),
					"description" => wp_kses_data( __("Subtitle for the block", 'organic-beauty') ),
					'dependency' => array(
						'element' => 'style',
						'value' => array('1','2','3')
					),
					"group" => esc_html__('Captions', 'organic-beauty'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", 'organic-beauty'),
					"description" => wp_kses_data( __("Title for the block", 'organic-beauty') ),
					"group" => esc_html__('Captions', 'organic-beauty'),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textarea"
				),
				array(
					"param_name" => "description",
					"heading" => esc_html__("Description", 'organic-beauty'),
					"description" => wp_kses_data( __("Description for the block", 'organic-beauty') ),
					"group" => esc_html__('Captions', 'organic-beauty'),
					'dependency' => array(
						'element' => 'style',
						'value' => array('2','3','4','5')
					),
					"class" => "",
					"value" => "",
					"type" => "textarea"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Button URL", 'organic-beauty'),
					"description" => wp_kses_data( __("Link URL for the button at the bottom of the block", 'organic-beauty') ),
					"group" => esc_html__('Captions', 'organic-beauty'),
					'dependency' => array(
						'element' => 'style',
						'value' => array('2','3','4')
					),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link_caption",
					"heading" => esc_html__("Button caption", 'organic-beauty'),
					"description" => wp_kses_data( __("Caption for the button at the bottom of the block", 'organic-beauty') ),
					"group" => esc_html__('Captions', 'organic-beauty'),
					'dependency' => array(
						'element' => 'style',
						'value' => array('2','3','4')
					),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link2",
					"heading" => esc_html__("Button 2 URL", 'organic-beauty'),
					"description" => wp_kses_data( __("Link URL for the second button at the bottom of the block", 'organic-beauty') ),
					"group" => esc_html__('Captions', 'organic-beauty'),
					'dependency' => array(
						'element' => 'style',
						'value' => array('2')
					),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link2_caption",
					"heading" => esc_html__("Button 2 caption", 'organic-beauty'),
					"description" => wp_kses_data( __("Caption for the second button at the bottom of the block", 'organic-beauty') ),
					"group" => esc_html__('Captions', 'organic-beauty'),
					'dependency' => array(
						'element' => 'style',
						'value' => array('2')
					),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "url",
					"heading" => esc_html__("Link", 'organic-beauty'),
					"description" => wp_kses_data( __("Link of the intro block", 'organic-beauty') ),
					"value" => '',
					"type" => "textfield"
				),
				array(
					"param_name" => "scheme",
					"heading" => esc_html__("Color scheme", 'organic-beauty'),
					"description" => wp_kses_data( __("Select color scheme for the section with text", 'organic-beauty') ),
					"class" => "",
					"value" => array_flip(organic_beauty_get_sc_param('schemes')),
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
			)
		) );
		
		class WPBakeryShortCode_Trx_Intro extends ORGANIC_BEAUTY_VC_ShortCodeSingle {}
	}
}
?>