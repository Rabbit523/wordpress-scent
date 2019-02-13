<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_title_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_title_theme_setup' );
	function organic_beauty_sc_title_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_title_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_title_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_title id="unique_id" style='regular|iconed' icon='' image='' background="on|off" type="1-6"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_title]
*/

if (!function_exists('organic_beauty_sc_title')) {	
	function organic_beauty_sc_title($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"type" => "1",
			"style" => "regular",
			"align" => "",
			"font_weight" => "",
			"font_size" => "",
			"color" => "",
			"icon" => "",
			"image" => "",
			"picture" => "",
			"image_size" => "small",
			"position" => "left",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"width" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . organic_beauty_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= organic_beauty_get_css_dimensions_from_values($width)
			.($align && $align!='none' && !organic_beauty_param_is_inherit($align) ? 'text-align:' . esc_attr($align) .';' : '')
			.($color ? 'color:' . esc_attr($color) .';' : '')
			.($font_weight && !organic_beauty_param_is_inherit($font_weight) ? 'font-weight:' . esc_attr($font_weight) .';' : '')
			.($font_size   ? 'font-size:' . esc_attr($font_size) .';' : '')
			;
		$type = min(6, max(1, $type));
		if ($picture > 0) {
			$attach = wp_get_attachment_image_src( $picture, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$picture = $attach[0];
		}
		$pic = $style!='iconed' 
			? '' 
			: '<span class="sc_title_icon sc_title_icon_'.esc_attr($position).'  sc_title_icon_'.esc_attr($image_size).($icon!='' && $icon!='none' ? ' '.esc_attr($icon) : '').'"'.'>'
				.($picture ? '<img src="'.esc_url($picture).'" alt="" />' : '')
				.(empty($picture) && $image && $image!='none' ? '<img src="'.esc_url(organic_beauty_strpos($image, 'http')===0 ? $image : organic_beauty_get_file_url('images/icons/'.($image).'.png')).'" alt="" />' : '')
				.'</span>';
		$output = '<h' . esc_attr($type) . ($id ? ' id="'.esc_attr($id).'"' : '')
				. ' class="sc_title sc_title_'.esc_attr($style)
					.($align && $align!='none' && !organic_beauty_param_is_inherit($align) ? ' sc_align_' . esc_attr($align) : '')
					.(!empty($class) ? ' '.esc_attr($class) : '')
					.'"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
				. '>'
					. ($pic)
					. ($style=='divider' ? '<span class="sc_title_divider_before"'.($color ? ' style="background-color: '.esc_attr($color).'"' : '').'></span>' : '')
					. do_shortcode($content) 
					. ($style=='divider' ? '<span class="sc_title_divider_after"'.($color ? ' style="background-color: '.esc_attr($color).'"' : '').'></span>' : '')
				. '</h' . esc_attr($type) . '>';
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_title', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_title', 'organic_beauty_sc_title');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_title_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_title_reg_shortcodes');
	function organic_beauty_sc_title_reg_shortcodes() {
	
		organic_beauty_sc_map("trx_title", array(
			"title" => esc_html__("Title", 'organic-beauty'),
			"desc" => wp_kses_data( __("Create header tag (1-6 level) with many styles", 'organic-beauty') ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"_content_" => array(
					"title" => esc_html__("Title content", 'organic-beauty'),
					"desc" => wp_kses_data( __("Title content", 'organic-beauty') ),
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				),
				"type" => array(
					"title" => esc_html__("Title type", 'organic-beauty'),
					"desc" => wp_kses_data( __("Title type (header level)", 'organic-beauty') ),
					"divider" => true,
					"value" => "1",
					"type" => "select",
					"options" => array(
						'1' => esc_html__('Header 1', 'organic-beauty'),
						'2' => esc_html__('Header 2', 'organic-beauty'),
						'3' => esc_html__('Header 3', 'organic-beauty'),
						'4' => esc_html__('Header 4', 'organic-beauty'),
						'5' => esc_html__('Header 5', 'organic-beauty'),
						'6' => esc_html__('Header 6', 'organic-beauty'),
					)
				),
				"style" => array(
					"title" => esc_html__("Title style", 'organic-beauty'),
					"desc" => wp_kses_data( __("Title style", 'organic-beauty') ),
					"value" => "regular",
					"type" => "select",
					"options" => array(
						'regular' => esc_html__('Regular', 'organic-beauty'),
						'underline' => esc_html__('Underline', 'organic-beauty'),
						'divider' => esc_html__('Divider', 'organic-beauty'),
						'iconed' => esc_html__('With icon (image)', 'organic-beauty')
					)
				),
				"align" => array(
					"title" => esc_html__("Alignment", 'organic-beauty'),
					"desc" => wp_kses_data( __("Title text alignment", 'organic-beauty') ),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => organic_beauty_get_sc_param('align')
				), 
				"font_size" => array(
					"title" => esc_html__("Font_size", 'organic-beauty'),
					"desc" => wp_kses_data( __("Custom font size. If empty - use theme default", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"font_weight" => array(
					"title" => esc_html__("Font weight", 'organic-beauty'),
					"desc" => wp_kses_data( __("Custom font weight. If empty or inherit - use theme default", 'organic-beauty') ),
					"value" => "",
					"type" => "select",
					"size" => "medium",
					"options" => array(
						'inherit' => esc_html__('Default', 'organic-beauty'),
						'100' => esc_html__('Thin (100)', 'organic-beauty'),
						'300' => esc_html__('Light (300)', 'organic-beauty'),
						'400' => esc_html__('Normal (400)', 'organic-beauty'),
						'600' => esc_html__('Semibold (600)', 'organic-beauty'),
						'700' => esc_html__('Bold (700)', 'organic-beauty'),
						'900' => esc_html__('Black (900)', 'organic-beauty')
					)
				),
				"color" => array(
					"title" => esc_html__("Title color", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select color for the title", 'organic-beauty') ),
					"value" => "",
					"type" => "color"
				),
				"icon" => array(
					"title" => esc_html__('Title font icon',  'organic-beauty'),
					"desc" => wp_kses_data( __("Select font icon for the title from Fontello icons set (if style=iconed)",  'organic-beauty') ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"value" => "",
					"type" => "icons",
					"options" => organic_beauty_get_sc_param('icons')
				),
				"image" => array(
					"title" => esc_html__('or image icon',  'organic-beauty'),
					"desc" => wp_kses_data( __("Select image icon for the title instead icon above (if style=iconed)",  'organic-beauty') ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"value" => "",
					"type" => "images",
					"size" => "small",
					"options" => organic_beauty_get_sc_param('images')
				),
				"picture" => array(
					"title" => esc_html__('or URL for image file', 'organic-beauty'),
					"desc" => wp_kses_data( __("Select or upload image or write URL from other site (if style=iconed)", 'organic-beauty') ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"image_size" => array(
					"title" => esc_html__('Image (picture) size', 'organic-beauty'),
					"desc" => wp_kses_data( __("Select image (picture) size (if style='iconed')", 'organic-beauty') ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"value" => "small",
					"type" => "checklist",
					"options" => array(
						'small' => esc_html__('Small', 'organic-beauty'),
						'medium' => esc_html__('Medium', 'organic-beauty'),
						'large' => esc_html__('Large', 'organic-beauty')
					)
				),
				"position" => array(
					"title" => esc_html__('Icon (image) position', 'organic-beauty'),
					"desc" => wp_kses_data( __("Select icon (image) position (if style=iconed)", 'organic-beauty') ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"value" => "left",
					"type" => "checklist",
					"options" => array(
						'top' => esc_html__('Top', 'organic-beauty'),
						'left' => esc_html__('Left', 'organic-beauty')
					)
				),
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
if ( !function_exists( 'organic_beauty_sc_title_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_title_reg_shortcodes_vc');
	function organic_beauty_sc_title_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_title",
			"name" => esc_html__("Title", 'organic-beauty'),
			"description" => wp_kses_data( __("Create header tag (1-6 level) with many styles", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_title',
			"class" => "trx_sc_single trx_sc_title",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "content",
					"heading" => esc_html__("Title content", 'organic-beauty'),
					"description" => wp_kses_data( __("Title content", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
				array(
					"param_name" => "type",
					"heading" => esc_html__("Title type", 'organic-beauty'),
					"description" => wp_kses_data( __("Title type (header level)", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('Header 1', 'organic-beauty') => '1',
						esc_html__('Header 2', 'organic-beauty') => '2',
						esc_html__('Header 3', 'organic-beauty') => '3',
						esc_html__('Header 4', 'organic-beauty') => '4',
						esc_html__('Header 5', 'organic-beauty') => '5',
						esc_html__('Header 6', 'organic-beauty') => '6'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "style",
					"heading" => esc_html__("Title style", 'organic-beauty'),
					"description" => wp_kses_data( __("Title style: only text (regular) or with icon/image (iconed)", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('Regular', 'organic-beauty') => 'regular',
						esc_html__('Underline', 'organic-beauty') => 'underline',
						esc_html__('Divider', 'organic-beauty') => 'divider',
						esc_html__('With icon (image)', 'organic-beauty') => 'iconed'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", 'organic-beauty'),
					"description" => wp_kses_data( __("Title text alignment", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(organic_beauty_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "font_size",
					"heading" => esc_html__("Font size", 'organic-beauty'),
					"description" => wp_kses_data( __("Custom font size. If empty - use theme default", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "font_weight",
					"heading" => esc_html__("Font weight", 'organic-beauty'),
					"description" => wp_kses_data( __("Custom font weight. If empty or inherit - use theme default", 'organic-beauty') ),
					"class" => "",
					"value" => array(
						esc_html__('Default', 'organic-beauty') => 'inherit',
						esc_html__('Thin (100)', 'organic-beauty') => '100',
						esc_html__('Light (300)', 'organic-beauty') => '300',
						esc_html__('Normal (400)', 'organic-beauty') => '400',
						esc_html__('Semibold (600)', 'organic-beauty') => '600',
						esc_html__('Bold (700)', 'organic-beauty') => '700',
						esc_html__('Black (900)', 'organic-beauty') => '900'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Title color", 'organic-beauty'),
					"description" => wp_kses_data( __("Select color for the title", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Title font icon", 'organic-beauty'),
					"description" => wp_kses_data( __("Select font icon for the title from Fontello icons set (if style=iconed)", 'organic-beauty') ),
					"class" => "",
					"group" => esc_html__('Icon &amp; Image', 'organic-beauty'),
					'dependency' => array(
						'element' => 'style',
						'value' => array('iconed')
					),
					"value" => organic_beauty_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "image",
					"heading" => esc_html__("or image icon", 'organic-beauty'),
					"description" => wp_kses_data( __("Select image icon for the title instead icon above (if style=iconed)", 'organic-beauty') ),
					"class" => "",
					"group" => esc_html__('Icon &amp; Image', 'organic-beauty'),
					'dependency' => array(
						'element' => 'style',
						'value' => array('iconed')
					),
					"value" => organic_beauty_get_sc_param('images'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "picture",
					"heading" => esc_html__("or select uploaded image", 'organic-beauty'),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site (if style=iconed)", 'organic-beauty') ),
					"group" => esc_html__('Icon &amp; Image', 'organic-beauty'),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "image_size",
					"heading" => esc_html__("Image (picture) size", 'organic-beauty'),
					"description" => wp_kses_data( __("Select image (picture) size (if style=iconed)", 'organic-beauty') ),
					"group" => esc_html__('Icon &amp; Image', 'organic-beauty'),
					"class" => "",
					"value" => array(
						esc_html__('Small', 'organic-beauty') => 'small',
						esc_html__('Medium', 'organic-beauty') => 'medium',
						esc_html__('Large', 'organic-beauty') => 'large'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "position",
					"heading" => esc_html__("Icon (image) position", 'organic-beauty'),
					"description" => wp_kses_data( __("Select icon (image) position (if style=iconed)", 'organic-beauty') ),
					"group" => esc_html__('Icon &amp; Image', 'organic-beauty'),
					"class" => "",
					"std" => "left",
					"value" => array(
						esc_html__('Top', 'organic-beauty') => 'top',
						esc_html__('Left', 'organic-beauty') => 'left'
					),
					"type" => "dropdown"
				),
				organic_beauty_get_vc_param('id'),
				organic_beauty_get_vc_param('class'),
				organic_beauty_get_vc_param('animation'),
				organic_beauty_get_vc_param('css'),
				organic_beauty_get_vc_param('margin_top'),
				organic_beauty_get_vc_param('margin_bottom'),
				organic_beauty_get_vc_param('margin_left'),
				organic_beauty_get_vc_param('margin_right')
			),
			'js_view' => 'VcTrxTextView'
		) );
		
		class WPBakeryShortCode_Trx_Title extends ORGANIC_BEAUTY_VC_ShortCodeSingle {}
	}
}
?>