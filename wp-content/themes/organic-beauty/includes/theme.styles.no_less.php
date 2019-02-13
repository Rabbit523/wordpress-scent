<?php
/**
 * Theme custom styles without LESS
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if (!function_exists('organic_beauty_action_theme_styles_no_less_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_action_theme_styles_no_less_theme_setup', 1 );
	function organic_beauty_action_theme_styles_no_less_theme_setup() {

		// If no LESS support
		if (organic_beauty_get_theme_setting('less_compiler') == 'no') {
			// Add theme styles
			add_action('organic_beauty_action_add_styles', 				'organic_beauty_action_theme_styles_no_less_add_styles');

			// Add colors and fonts for the WP Customizer
			add_action( 'customize_controls_print_footer_scripts',	'organic_beauty_customizer_wp_css_template' );
			add_action( 'organic_beauty_filter_get_css',					'organic_beauty_customizer_wp_add_scheme_in_css', 100, 4 );
		}
	}
}


// Add theme stylesheets
if (!function_exists('organic_beauty_action_theme_styles_no_less_add_styles')) {
	//add_action('organic_beauty_action_add_styles', 'organic_beauty_action_theme_styles_no_less_add_styles');
	function organic_beauty_action_theme_styles_no_less_add_styles() {
		// Add stylesheet files
		if ( !is_customize_preview() && !isset($_GET['color_scheme']) && organic_beauty_param_is_off(organic_beauty_get_theme_option('debug_mode')) ) {
			organic_beauty_enqueue_style( 'organic_beauty-theme-style', organic_beauty_get_file_url('css/theme.css'), array(), null );
			wp_add_inline_style( 'organic_beauty-theme-style', organic_beauty_get_inline_css() );
		} else {
			wp_add_inline_style( 'organic_beauty-main-style', organic_beauty_get_custom_css() . organic_beauty_get_inline_css() );
		}
	}
}


// Add scheme name in each selector in the CSS (priority 100 - after complete css)
if (!function_exists('organic_beauty_customizer_wp_add_scheme_in_css')) {
	//add_action( 'organic_beauty_filter_get_css', 'organic_beauty_customizer_wp_add_scheme_in_css', 100, 4 );
	function organic_beauty_customizer_wp_add_scheme_in_css($css, $colors, $fonts, $scheme) {
		$rez = '';
		$in_comment = $in_rule = false;
		$allow = true;
		$scheme_class = '.scheme_' . trim($scheme) . ' ';
		$self_class = '.scheme_self';
		$self_class_len = organic_beauty_strlen($self_class);
		$css_str = str_replace(array('{{', '}}'), array('[[',']]'), $css['colors']);
		for ($i=0; $i<strlen($css_str); $i++) {
			$ch = $css_str[$i];
			if ($in_comment) {
				$rez .= $ch;
				if ($ch=='/' && $css_str[$i-1]=='*') {
					$in_comment = false;
					$allow = !$in_rule;
				}
			} else if ($in_rule) {
				$rez .= $ch;
				if ($ch=='}') {
					$in_rule = false;
					$allow = !$in_comment;
				}
			} else {
				if ($ch=='/' && $css_str[$i+1]=='*') {
					$rez .= $ch;
					$in_comment = true;
				} else if ($ch=='{') {
					$rez .= $ch;
					$in_rule = true;
				} else if ($ch==',') {
					$rez .= $ch;
					$allow = true;
				} else if (organic_beauty_strpos(" \t\r\n", $ch)===false) {
					if ($allow && organic_beauty_substr($css_str, $i, $self_class_len) == $self_class) {
						$rez .= trim($scheme_class);
						$i += $self_class_len - 1;
					} else
						$rez .= ($allow ? $scheme_class : '') . $ch;
					$allow = false;
				} else {
					$rez .= $ch;
				}
			}
		}
		$rez = str_replace(array('[[',']]'), array('{{', '}}'), $rez);
		$css['colors'] = $rez;
		return $css;
	}
}



// Output an Underscore template for generating CSS for the color scheme.
// The template generates the css dynamically for instant display in the Customizer preview.
if ( !function_exists( 'organic_beauty_customizer_wp_css_template' ) ) {
	//add_action( 'customize_controls_print_footer_scripts', 'organic_beauty_customizer_wp_css_template' );
	function organic_beauty_customizer_wp_css_template() {

		// Colors
		$colors = array(
			
			// Whole block border and background
			'bg_color'				=> '{{ data.bg_color }}',
			'bd_color'				=> '{{ data.bd_color }}',
			
			// Text and links colors
			'text'					=> '{{ data.text }}',
			'text_light'			=> '{{ data.text_light }}',
			'text_dark'				=> '{{ data.text_dark }}',
			'text_link'				=> '{{ data.text_link }}',
			'text_hover'			=> '{{ data.text_hover }}',
		
			// Alternative blocks (submenu, buttons, tabs, etc.)
			'alter_bg_color'		=> '{{ data.alter_bg_color }}',
			'alter_bg_hover'		=> '{{ data.alter_bg_hover }}',
			'alter_bd_color'		=> '{{ data.alter_bd_color }}',
			'alter_bd_hover'		=> '{{ data.alter_bd_hover }}',
			'alter_text'			=> '{{ data.alter_text }}',
			'alter_light'			=> '{{ data.alter_light }}',
			'alter_dark'			=> '{{ data.alter_dark }}',
			'alter_link'			=> '{{ data.alter_link }}',
			'alter_hover'			=> '{{ data.alter_hover }}',
		
			// Input fields (form's fields and textarea)
			'input_bg_color'		=> '{{ data.input_bg_color }}',
			'input_bg_hover'		=> '{{ data.input_bg_hover }}',
			'input_bd_color'		=> '{{ data.input_bd_color }}',
			'input_bd_hover'		=> '{{ data.input_bd_hover }}',
			'input_text'			=> '{{ data.input_text }}',
			'input_light'			=> '{{ data.input_light }}',
			'input_dark'			=> '{{ data.input_dark }}',

			// Inverse blocks (with background equal to the links color or one of accented colors)
			'inverse_text'			=> '{{ data.inverse_text }}',
			'inverse_light'			=> '{{ data.inverse_light }}',
			'inverse_dark'			=> '{{ data.inverse_dark }}',
			'inverse_link'			=> '{{ data.inverse_link }}',
			'inverse_hover'			=> '{{ data.inverse_hover }}',

			// Additional accented colors (if used in the current theme)
			// For example:
			// 'accent2'			=> '{{ data.accent2 }}',
			// 'accent2_hover'		=> '{{ data.accent2_hover }}',

		);

		$tmpl_holder = 'script';

		$schemes = array_keys(organic_beauty_get_list_color_schemes());
		if (count($schemes) > 0) {
			foreach ($schemes as $scheme) {
				echo '<'.trim($tmpl_holder).' type="text/html" id="tmpl-organic_beauty-color-scheme-'.esc_attr($scheme).'">'
						. trim(organic_beauty_get_custom_css( $colors, false, false, $scheme ))
					. '</'.trim($tmpl_holder).'>';
			}
		}

		// Fonts
		$custom_fonts = organic_beauty_get_custom_fonts();
		if (is_array($custom_fonts) && count($custom_fonts) > 0) {
			$fonts = array();
			foreach ($custom_fonts as $tag => $font) {
				$fonts[$tag.'_font-family']			= '{{ data["'.$tag.'_font-family"] }}';
				$fonts[$tag.'_font-size']			= '{{ data["'.$tag.'_font-size"] }}';
				$fonts[$tag.'_line-height']			= '{{ data["'.$tag.'_line-height"] }}';
				$fonts[$tag.'_line-height_value']	= '{{ data["'.$tag.'_line-height_value"] }}';
				$fonts[$tag.'_font-weight']			= '{{ data["'.$tag.'_font-weight"] }}';
				$fonts[$tag.'_font-style']			= '{{ data["'.$tag.'_font-style"] }}';
				$fonts[$tag.'_text-decoration']		= '{{ data["'.$tag.'_text-decoration"] }}';
				$fonts[$tag.'_margin-top']			= '{{ data["'.$tag.'_margin-top"] }}';
				$fonts[$tag.'_margin-top_value']	= '{{ data["'.$tag.'_margin-top_value"] }}';
				$fonts[$tag.'_margin-bottom']		= '{{ data["'.$tag.'_margin-bottom"] }}';
				$fonts[$tag.'_margin-bottom_value']	= '{{ data["'.$tag.'_margin-bottom_value"] }}';
			}
			echo '<'.trim($tmpl_holder).' type="text/html" id="tmpl-organic_beauty-fonts">'
					. trim(organic_beauty_get_custom_css( false, $fonts, false, false ))
				. '</'.trim($tmpl_holder).'>';
		}

	}
}

// Additional (calculated) theme-specific colors
// Attention! Don't forget setup custom colors also in the core.customizer.wp.color-scheme.js
if (!function_exists('organic_beauty_customizer_wp_add_theme_colors')) {
	function organic_beauty_customizer_wp_add_theme_colors($colors) {
		if (organic_beauty_substr($colors['text'], 0, 2) == '{{') {
			$colors['text_dark_0_05']	= '{{ data.text_dark_0_05 }}';
			$colors['text_dark_0_1']	= '{{ data.text_dark_0_1 }}';
			$colors['text_dark_0_8']	= '{{ data.text_dark_0_8 }}';
			$colors['text_link_0_3']	= '{{ data.text_link_0_3 }}';
			$colors['text_link_0_6']	= '{{ data.text_link_0_6 }}';
			$colors['text_link_0_8']	= '{{ data.text_link_0_8 }}';
			$colors['text_hover_0_2']	= '{{ data.text_hover_0_2 }}';
			$colors['text_hover_0_3']	= '{{ data.text_hover_0_3 }}';
			$colors['text_hover_0_8']	= '{{ data.text_hover_0_8 }}';
			$colors['inverse_text_0_1']	= '{{ data.inverse_text_0_1 }}';
			$colors['bg_color_0_8']		= '{{ data.bg_color_0_8 }}';
			$colors['alter_text_0_1']	= '{{ data.alter_text_0_1 }}';
			$colors['alter_bg_color_0_8'] = '{{ data.alter_bg_color_0_8 }}';
			$colors['alter_bg_hover_0_5'] = '{{ data.alter_bg_hover_0_5 }}';
			$colors['alter_bd_color_0_1'] = '{{ data.alter_bd_color_0_1 }}';
		} else {
			$colors['text_dark_0_05']	= organic_beauty_hex2rgba( $colors['text_dark'], 0.05 );
			$colors['text_dark_0_1']	= organic_beauty_hex2rgba( $colors['text_dark'], 0.1 );
			$colors['text_dark_0_8']	= organic_beauty_hex2rgba( $colors['text_dark'], 0.8 );
			$colors['text_link_0_3']	= organic_beauty_hex2rgba( $colors['text_link'], 0.3 );
			$colors['text_link_0_6']	= organic_beauty_hex2rgba( $colors['text_link'], 0.6 );
			$colors['text_link_0_8']	= organic_beauty_hex2rgba( $colors['text_link'], 0.8 );
			$colors['text_hover_0_2']	= organic_beauty_hex2rgba( $colors['text_hover'], 0.2 );
			$colors['text_hover_0_3']	= organic_beauty_hex2rgba( $colors['text_hover'], 0.3 );
			$colors['text_hover_0_8']	= organic_beauty_hex2rgba( $colors['text_hover'], 0.8 );
			$colors['inverse_text_0_1']	= organic_beauty_hex2rgba( $colors['inverse_text'], 0.1 );
			$colors['bg_color_0_8']		= organic_beauty_hex2rgba( $colors['bg_color'], 0.8 );
			$colors['alter_text_0_1']	= organic_beauty_hex2rgba( $colors['alter_text'], 0.1 );
			$colors['alter_bg_color_0_8'] = organic_beauty_hex2rgba( $colors['alter_bg_color'], 0.8 );
			$colors['alter_bg_hover_0_5'] = organic_beauty_hex2rgba( $colors['alter_bg_hover'], 0.5 );
			$colors['alter_bd_color_0_1'] = organic_beauty_hex2rgba( $colors['alter_bd_color'], 0.1 );
		}
		return $colors;
	}
}
			
// Additional (calculated) theme-specific font parameters
// Attention! Don't forget setup custom fonts also in the core.customizer.wp.color-scheme.js
if (!function_exists('organic_beauty_customizer_wp_add_theme_fonts')) {
	function organic_beauty_customizer_wp_add_theme_fonts($fonts) {
		if (organic_beauty_substr($fonts['h1_font-family'], 0, 2) == '{{') {
			$fonts['logo_margin-top_0_75']	 = '{{ data["logo_margin-top_0_75"] }}';
			$fonts['logo_margin-bottom_0_5'] = '{{ data["logo_margin-bottom_0_5"] }}';
			$fonts['menu_margin-top_1']		 = '{{ data["menu_margin-top_1"] }}';
			$fonts['menu_margin-top_0_3']	 = '{{ data["menu_margin-top_0_3"] }}';
			$fonts['menu_margin-top_0_6']	 = '{{ data["menu_margin-top_0_6"] }}';
			$fonts['menu_margin-top_0_65']	 = '{{ data["menu_margin-top_0_65"] }}';
			$fonts['menu_margin-top_0_7']	 = '{{ data["menu_margin-top_0_7"] }}';
			$fonts['menu_margin-top_0_8']	 = '{{ data["menu_margin-top_0_8"] }}';
			$fonts['menu_margin-bottom_1']	 = '{{ data["menu_margin-bottom_1"] }}';
			$fonts['menu_margin-bottom_0_5'] = '{{ data["menu_margin-bottom_0_5"] }}';
			$fonts['menu_margin-bottom_0_6'] = '{{ data["menu_margin-bottom_0_6"] }}';
			$fonts['menu_height_1']			 = '{{ data["menu_height_1"] }}';
			$fonts['submenu_margin-top_1']	 = '{{ data["submenu_margin-top_1"] }}';
			$fonts['submenu_margin-bottom_1']= '{{ data["submenu_margin-bottom_1"] }}';
		} else {
			$fonts['logo_margin-top_0_75']	 = organic_beauty_multiply_css_value( $fonts['logo_margin-top_value'], 0.75 );
			$fonts['logo_margin-bottom_0_5'] = organic_beauty_multiply_css_value( $fonts['logo_margin-bottom_value'], 0.5 );
			$fonts['menu_margin-top_1']		 = $fonts['menu_margin-top_value'];
			$fonts['menu_margin-top_0_3']	 = organic_beauty_multiply_css_value( $fonts['menu_margin-top_value'], 0.3 );
			$fonts['menu_margin-top_0_6']	 = organic_beauty_multiply_css_value( $fonts['menu_margin-top_value'], 0.6 );
			$fonts['menu_margin-top_0_65']	 = organic_beauty_multiply_css_value( $fonts['menu_margin-top_value'], 0.65 );
			$fonts['menu_margin-top_0_7']	 = organic_beauty_multiply_css_value( $fonts['menu_margin-top_value'], 0.7 );
			$fonts['menu_margin-top_0_8']	 = organic_beauty_multiply_css_value( $fonts['menu_margin-top_value'], 0.8 );
			$fonts['menu_margin-bottom_1']	 = $fonts['menu_margin-bottom_value'];
			$fonts['menu_margin-bottom_0_5'] = organic_beauty_multiply_css_value( $fonts['menu_margin-bottom_value'], 0.5 );
			$fonts['menu_margin-bottom_0_6'] = organic_beauty_multiply_css_value( $fonts['menu_margin-bottom_value'], 0.6 );
			$fonts['menu_height_1']			 = organic_beauty_summ_css_value( organic_beauty_summ_css_value( $fonts['menu_margin-top_value'],  $fonts['menu_margin-bottom_value'] ), $fonts['menu_line-height_value'] );
			$fonts['submenu_margin-top_1']	 = $fonts['submenu_margin-top_value'];
			$fonts['submenu_margin-bottom_1']= $fonts['submenu_margin-bottom_value'];
		}
		return $fonts;
	}
}

// Return CSS with custom colors and fonts
if (!function_exists('organic_beauty_get_custom_css')) {

	function organic_beauty_get_custom_css($colors=null, $fonts=null, $minify=true, $only_scheme='') {

		$add_comment = $colors===null && $fonts===null && empty($only_scheme)
						? '/* ' . strip_tags( __("ATTENTION! This file was generated automatically! Don't change it!!!", 'organic-beauty') ) . "*/\n"
						: '';

		$css = $rez = array(
			'fonts' => '',
			'colors' => ''
		);
		
		// Prepare fonts
		if ($fonts===null) {
			$fonts_list = organic_beauty_get_list_fonts(false);
			$custom_fonts = organic_beauty_get_custom_fonts();
			$fonts = array();
			foreach ($custom_fonts as $tag => $font) {
			
				$fonts[$tag.'_font-family-style'] = !empty($font['font-family']) && !organic_beauty_is_inherit_option($font['font-family'])
												? "font-family:\"" . str_replace(' ('.esc_html__('uploaded font', 'organic-beauty').')', '', $font['font-family']) . '"' 
													. (isset($fonts_list[$font['font-family']]['family']) ? ',' . $fonts_list[$font['font-family']]['family'] : '' ) 
													. " !important;"
												: '';
			
				$fonts[$tag.'_font-family'] = !empty($font['font-family']) && !organic_beauty_is_inherit_option($font['font-family'])
												? "font-family:\"" . str_replace(' ('.esc_html__('uploaded font', 'organic-beauty').')', '', $font['font-family']) . '"' 
													. (isset($fonts_list[$font['font-family']]['family']) ? ',' . $fonts_list[$font['font-family']]['family'] : '' ) 
													. ";"
												: '';
				$fonts[$tag.'_font-size'] = !empty($font['font-size']) && !organic_beauty_is_inherit_option($font['font-size'])
												? "font-size:" . organic_beauty_prepare_css_value($font['font-size']) . ";"
												: '';
				$fonts[$tag.'_line-height'] = !empty($font['line-height']) && !organic_beauty_is_inherit_option($font['line-height'])
												? "line-height: " . organic_beauty_prepare_css_value($font['line-height']) . ";"
												: '';
				$fonts[$tag.'_line-height_value'] = !empty($font['line-height'])	// && !organic_beauty_is_inherit_option($font['line-height'])
												? organic_beauty_prepare_css_value($font['line-height'])
												: 'inherit';
				$fonts[$tag.'_font-weight'] = !empty($font['font-weight']) && !organic_beauty_is_inherit_option($font['font-weight'])
												? "font-weight: " . trim($font['font-weight']) . ";"
												: '';
				$fonts[$tag.'_font-style'] = !empty($font['font-style']) && !organic_beauty_is_inherit_option($font['font-style']) && organic_beauty_strpos($font['font-style'], 'i')!==false
												? "font-style: italic;"
												: '';
				$fonts[$tag.'_text-decoration'] = !empty($font['font-style']) && !organic_beauty_is_inherit_option($font['font-style']) && organic_beauty_strpos($font['font-style'], 'u')!==false
												? "text-decoration: underline;"
												: '';
				$fonts[$tag.'_margin-top'] = !empty($font['margin-top']) && !organic_beauty_is_inherit_option($font['margin-top'])
												? "margin-top: " . organic_beauty_prepare_css_value($font['margin-top']) . ";"
												: '';
				$fonts[$tag.'_margin-top_value'] = !empty($font['margin-top'])	// && !organic_beauty_is_inherit_option($font['margin-top'])
												? organic_beauty_prepare_css_value($font['margin-top'])
												: 'inherit';
				$fonts[$tag.'_margin-bottom'] = !empty($font['margin-bottom']) && !organic_beauty_is_inherit_option($font['margin-bottom'])
												? "margin-bottom: " . organic_beauty_prepare_css_value($font['margin-bottom']) . ";"
												: '';
				$fonts[$tag.'_margin-bottom_value'] = !empty($font['margin-bottom'])	// && !organic_beauty_is_inherit_option($font['margin-bottom'])
												? organic_beauty_prepare_css_value($font['margin-bottom'])
												: 'inherit';
			}
		}
		if ($fonts) {
			$fonts = organic_beauty_customizer_wp_add_theme_fonts($fonts);
			// Attention! All font's rules mustn't have ';' in the end of the row
			$rez['fonts'] = <<<FONTS

/* Theme typography */
body {
	{$fonts['p_font-family']}
	{$fonts['p_font-size']}
	{$fonts['p_font-weight']}
	{$fonts['p_font-style']}
	{$fonts['p_line-height']}
	{$fonts['p_text-decoration']}
}

h1 {
	{$fonts['h1_font-family']}
	{$fonts['h1_font-size']}
	{$fonts['h1_font-weight']}
	{$fonts['h1_font-style']}
	{$fonts['h1_line-height']}
	{$fonts['h1_text-decoration']}
	{$fonts['h1_margin-top']}
	{$fonts['h1_margin-bottom']}
}
h2 {
	{$fonts['h2_font-family']}
	{$fonts['h2_font-size']}
	{$fonts['h2_font-weight']}
	{$fonts['h2_font-style']}
	{$fonts['h2_line-height']}
	{$fonts['h2_text-decoration']}
	{$fonts['h2_margin-top']}
	{$fonts['h2_margin-bottom']}
}
h3 {
	{$fonts['h3_font-family']}
	{$fonts['h3_font-size']}
	{$fonts['h3_font-weight']}
	{$fonts['h3_font-style']}
	{$fonts['h3_line-height']}
	{$fonts['h3_text-decoration']}
	{$fonts['h3_margin-top']}
	{$fonts['h3_margin-bottom']}
}
h4 {
	{$fonts['h4_font-family']}
	{$fonts['h4_font-size']}
	{$fonts['h4_font-weight']}
	{$fonts['h4_font-style']}
	{$fonts['h4_line-height']}
	{$fonts['h4_text-decoration']}
	{$fonts['h4_margin-top']}
	{$fonts['h4_margin-bottom']}
}
h5 {
	{$fonts['h5_font-family']}
	{$fonts['h5_font-size']}
	{$fonts['h5_font-weight']}
	{$fonts['h5_font-style']}
	{$fonts['h5_line-height']}
	{$fonts['h5_text-decoration']}
	{$fonts['h5_margin-top']}
	{$fonts['h5_margin-bottom']}
}
h6 {
	{$fonts['h6_font-family']}
	{$fonts['h6_font-size']}
	{$fonts['h6_font-weight']}
	{$fonts['h6_font-style']}
	{$fonts['h6_line-height']}
	{$fonts['h6_text-decoration']}
	{$fonts['h6_margin-top']}
	{$fonts['h6_margin-bottom']}
}

a {
	{$fonts['link_font-family']}
	{$fonts['link_font-size']}
	{$fonts['link_font-weight']}
	{$fonts['link_font-style']}
	{$fonts['link_line-height']}
	{$fonts['link_text-decoration']}
}

/* Form fields settings */
input[type="text"],
input[type="number"],
input[type="email"],
input[type="search"],
input[type="password"],
select,
textarea {
	{$fonts['input_font-family']}
	{$fonts['input_font-size']}
	{$fonts['input_font-weight']}
	{$fonts['input_font-style']}
	{$fonts['input_line-height']}
	{$fonts['input_text-decoration']}
}

input[type="submit"],
input[type="reset"],
input[type="button"],
button,
.sc_button,
.post_readmore {
	{$fonts['button_font-family']}
	{$fonts['button_font-size']}
	{$fonts['button_font-weight']}
	{$fonts['button_font-style']}
	{$fonts['button_line-height']}
	{$fonts['button_text-decoration']}
}

.logo .logo_text {
	{$fonts['logo_font-family']}
	{$fonts['logo_font-size']}
	{$fonts['logo_font-weight']}
	{$fonts['logo_font-style']}
	{$fonts['logo_line-height']}
	{$fonts['logo_text-decoration']}
}





/* Main menu */
.menu_main_nav > li > a,
.header_mobile .login .popup_login_link,
.header_mobile .login .popup_register_link {
	{$fonts['menu_font-family']}
	{$fonts['menu_font-size']}
	{$fonts['menu_font-weight']}
	{$fonts['menu_font-style']}
	{$fonts['menu_line-height']}
	{$fonts['menu_text-decoration']}
}
.menu_main_nav > li ul {
	{$fonts['submenu_font-family']}
	{$fonts['submenu_font-weight']}
	{$fonts['submenu_font-style']}
	{$fonts['submenu_line-height']}
	{$fonts['submenu_text-decoration']}
}
.menu_main_nav > li > ul {
	{$fonts['submenu_font-size']}
}


.top_panel_top ul > li > a {
	{$fonts['menu_font-family']}
}
.top_panel_top ul > li ul {
	{$fonts['submenu_font-family']}
}



/* Post info */
.post_info {
	{$fonts['info_font-family']}
	{$fonts['info_font-size']}
	{$fonts['info_font-weight']}
	{$fonts['info_font-style']}
	{$fonts['info_line-height']}
	{$fonts['info_text-decoration']}
	{$fonts['info_margin-top']}
	{$fonts['info_margin-bottom']}
}

/* Page 404 */
.post_item_404 .page_title,
.post_item_404 .page_subtitle {
	{$fonts['logo_font-family']}
}


/* Booking Calendar */
.booking_font_custom,
.booking_day_container,
.booking_calendar_container_all {
	{$fonts['p_font-family']}
}
.booking_weekdays_custom {
	{$fonts['h1_font-family']}
}


/* Shortcodes */
.sc_recent_news .post_item .post_title {
	{$fonts['h5_font-family']}
	{$fonts['h5_font-size']}
	{$fonts['h5_font-weight']}
	{$fonts['h5_font-style']}
	{$fonts['h5_line-height']}
	{$fonts['h5_text-decoration']}
	{$fonts['h5_margin-top']}
	{$fonts['h5_margin-bottom']}
}
.sc_recent_news .post_item h6.post_title {
	{$fonts['h6_font-family']}
	{$fonts['h6_font-size']}
	{$fonts['h6_font-weight']}
	{$fonts['h6_font-style']}
	{$fonts['h6_line-height']}
	{$fonts['h6_text-decoration']}
	{$fonts['h6_margin-top']}
	{$fonts['h6_margin-bottom']}
}


.sc_call_to_action_style_2.sc_call_to_action_align_left .sc_call_to_action_title {
	{$fonts['p_font-family']}
}

.sc_services_item .sc_services_item_title_top,
.comments_list_wrap .comment_info .comment_author,
.sc_audio .sc_audio_author_name,
.cq-testimoniallist.beauty .cq-testimoniallist-name,
.sc_skills_counter .sc_skills_item.sc_skills_style_2 .sc_skills_info,
.sc_skills_pie.sc_skills_compact_off .sc_skills_total {
	{$fonts['h1_font-family']}
}


.eg-store-grid-fixed-wrapper .esg-cc > div a,
.eg-store-grid-wrapper .esg-cc > div a {
	{$fonts['h1_font-family-style']}
}


.copyright_wrap .menu_footer_nav {
	{$fonts['menu_font-family']}
}

.post_item_related .post_title,
.isotope_item_masonry .post_title,
.post_item_classic .post_title {
	{$fonts['h6_font-family']}
}





/* Other */
.other_font,
.sc_intro_style_4 .sc_intro_title,
.sc_intro_style_2 .sc_intro_title,
.sc_intro_style_3 .sc_intro_title,
.sc_price_block .sc_price_block_money,
.sc_price_block .sc_price_block_title,
.sc_skills_counter .sc_skills_item .sc_skills_count .sc_skills_total,
figure figcaption,
.sc_image figcaption,
blockquote,
.sc_dropcaps .sc_dropcaps_item {
	{$fonts['other_font-family']}
}









FONTS;
		}

		if ($colors!==false) {
			$schemes = empty($only_scheme) ? array_keys(organic_beauty_get_list_color_schemes()) : array($only_scheme);
			if (count($schemes) > 0) {
				$step = 1;
				foreach ($schemes as $scheme) {
					// Prepare colors
					if (empty($only_scheme)) $colors = organic_beauty_get_scheme_colors($scheme);

					// Make theme-specific colors and tints
					$colors = organic_beauty_customizer_wp_add_theme_colors($colors);

					// Make styles
					$rez['colors'] = <<<CSS

/* 2. Theme Colors
------------------------------------------------------------------------- */

h1, h2, h3, h4, h5, h6,
h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {
	color: {$colors['text_dark']};
}
a {
	color: {$colors['text_link']};
}
a:hover {
	color: {$colors['text_hover']};
}
.header_mobile .popup_login .forgot_password:hover,
.header_mobile .popup_registration .form_left a:hover {
	color: {$colors['text_hover']} !important;
}

blockquote:before {
	color: {$colors['text_dark']};
}
blockquote, blockquote p {
	color: {$colors['text_dark']};
}
blockquote > a,
blockquote > p > a,
blockquote cite {
	color: {$colors['text_hover']};
}
blockquote > ahover,
blockquote > p > a:hover {
	color: {$colors['text_link']};
}

.accent1 {			color: {$colors['text_link']}; }
.accent1_bgc {		background-color: {$colors['text_link']}; }
.accent1_bg {		background: {$colors['text_link']}; }
.accent1_border {	border-color: {$colors['text_link']}; }
a.accent1:hover {	color: {$colors['text_hover']}; }

/* Portfolio hovers */
.post_content.ih-item.circle.effect1.colored .info,
.post_content.ih-item.circle.effect2.colored .info,
.post_content.ih-item.circle.effect3.colored .info,
.post_content.ih-item.circle.effect4.colored .info,
.post_content.ih-item.circle.effect5.colored .info .info-back,
.post_content.ih-item.circle.effect6.colored .info,
.post_content.ih-item.circle.effect7.colored .info,
.post_content.ih-item.circle.effect8.colored .info,
.post_content.ih-item.circle.effect9.colored .info,
.post_content.ih-item.circle.effect10.colored .info,
.post_content.ih-item.circle.effect11.colored .info,
.post_content.ih-item.circle.effect12.colored .info,
.post_content.ih-item.circle.effect13.colored .info,
.post_content.ih-item.circle.effect14.colored .info,
.post_content.ih-item.circle.effect15.colored .info,
.post_content.ih-item.circle.effect16.colored .info,
.post_content.ih-item.circle.effect18.colored .info .info-back,
.post_content.ih-item.circle.effect19.colored .info,
.post_content.ih-item.circle.effect20.colored .info .info-back,
.post_content.ih-item.square.effect1.colored .info,
.post_content.ih-item.square.effect2.colored .info,
.post_content.ih-item.square.effect3.colored .info,
.post_content.ih-item.square.effect4.colored .mask1,
.post_content.ih-item.square.effect4.colored .mask2,
.post_content.ih-item.square.effect5.colored .info,
.post_content.ih-item.square.effect6.colored .info,
.post_content.ih-item.square.effect7.colored .info,
.post_content.ih-item.square.effect8.colored .info,
.post_content.ih-item.square.effect9.colored .info .info-back,
.post_content.ih-item.square.effect10.colored .info,
.post_content.ih-item.square.effect11.colored .info,
.post_content.ih-item.square.effect12.colored .info,
.post_content.ih-item.square.effect13.colored .info,
.post_content.ih-item.square.effect14.colored .info,
.post_content.ih-item.square.effect15.colored .info,
.post_content.ih-item.circle.effect20.colored .info .info-back,
.post_content.ih-item.square.effect_book.colored .info,
.post_content.ih-item.square.effect_pull.colored .post_descr {
	background: {$colors['text_link']};
	color: {$colors['inverse_text']};
}

.post_content.ih-item.circle.effect1.colored .info,
.post_content.ih-item.circle.effect2.colored .info,
.post_content.ih-item.circle.effect5.colored .info .info-back,
.post_content.ih-item.circle.effect19.colored .info,
.post_content.ih-item.square.effect4.colored .mask1,
.post_content.ih-item.square.effect4.colored .mask2,
.post_content.ih-item.square.effect6.colored .info,
.post_content.ih-item.square.effect7.colored .info,
.post_content.ih-item.square.effect12.colored .info,
.post_content.ih-item.square.effect13.colored .info,
.post_content.ih-item.square.effect_more.colored .info,
.post_content.ih-item.square.effect_dir.colored .info,
.post_content.ih-item.square.effect_shift.colored .info {
	background: {$colors['text_link_0_6']};
	color: {$colors['inverse_text']};
}
.post_content.ih-item.square.effect_border.colored .img,
.post_content.ih-item.square.effect_fade.colored .img,
.post_content.ih-item.square.effect_slide.colored .img {
	background: {$colors['text_link']};
}
.post_content.ih-item.square.effect_border.colored .info,
.post_content.ih-item.square.effect_fade.colored .info,
.post_content.ih-item.square.effect_slide.colored .info {
	color: {$colors['inverse_text']};
}
.post_content.ih-item.square.effect_border.colored .info:before,
.post_content.ih-item.square.effect_border.colored .info:after {
	border-color: {$colors['inverse_text']};
}

.post_content.ih-item.circle.effect1 .spinner {
	border-right-color: {$colors['text_link']};
	border-bottom-color: {$colors['text_link']};
}
.post_content.ih-item .post_readmore .post_readmore_label,
.post_content.ih-item .info a,
.post_content.ih-item .info a > span {
	color: {$colors['inverse_link']};
}
.post_content.ih-item .post_readmore:hover .post_readmore_label,
.post_content.ih-item .info a:hover,
.post_content.ih-item .info a:hover > span {
	color: {$colors['inverse_hover']};
}

/* Tables */
td, th {
	border-color: {$colors['text_hover']};
}
.sc_table table {
	color: {$colors['text_dark']};
}
.sc_table table tr:first-child {
	color: {$colors['text_hover']};
}

/* Table of contents */
pre.code,
#toc .toc_item.current,
#toc .toc_item:hover {
	border-color: {$colors['text_link']};
}


::selection,
::-moz-selection { 
	background-color: {$colors['text_link']};
	color: {$colors['inverse_text']};
}

/* 3. Form fields settings
-------------------------------------------------------------- */

input[type="tel"],
input[type="text"],
input[type="number"],
input[type="email"],
input[type="search"],
input[type="password"],
select,
textarea {
	color: {$colors['input_text']};
	border-color: {$colors['input_bd_color']};
	background-color: {$colors['input_bg_color']};
}
input[type="tel"]:focus,
input[type="text"]:focus,
input[type="number"]:focus,
input[type="email"]:focus,
input[type="search"]:focus,
input[type="password"]:focus,
select:focus,
textarea:focus {
	color: {$colors['text_hover']};
	border-color: {$colors['input_bd_hover']};
	background-color: {$colors['input_bg_hover']};
}
input::-webkit-input-placeholder,
textarea::-webkit-input-placeholder {
	color: {$colors['input_light']};
}
fieldset {
	border-color: {$colors['bd_color']};
}
fieldset legend {
	background-color: {$colors['bg_color']};
	color: {$colors['text']};
}

.sc_form input::-webkit-input-placeholder {color: {$colors['input_text']}; opacity: 1;}
.sc_form input::-moz-placeholder          {color: {$colors['input_text']};opacity: 1;}/* Firefox 19+ */
.sc_form input:-moz-placeholder           {color: {$colors['input_text']};; opacity: 1;}/* Firefox 18- */
.sc_form input:-ms-input-placeholder      {color: {$colors['input_text']}; opacity: 1;}

.sc_form textarea::-webkit-input-placeholder {color: {$colors['input_text']}; opacity: 1;}
.sc_form textarea::-moz-placeholder          {color: {$colors['input_text']};opacity: 1;}/* Firefox 19+ */
.sc_form textarea:-moz-placeholder           {color: {$colors['input_text']};; opacity: 1;}/* Firefox 18- */
.sc_form textarea:-ms-input-placeholder      {color: {$colors['input_text']}; opacity: 1;}



/* ======================== END INPUT'S STYLES ================== */



/* 6. Page layouts
-------------------------------------------------------------- */
.body_wrap {
	color: {$colors['text']};
}
.body_style_boxed .body_wrap {
	background-color: {$colors['bg_color']};
}


/* 7. Section's decorations
-------------------------------------------------------------- */

/* If in the Theme options set "Body filled", else - leave this sections transparent */
body:not(.video_bg_show),
body:not(.video_bg_show) .page_wrap,
.copy_wrap,
.sidebar_cart,
.widget_area_inner,
#page_preloader {
	background-color: {$colors['bg_color']};
}


.article_style_boxed .content > article > .post_content,
.article_style_boxed[class*="single-"] .content > .comments_wrap,
.article_style_boxed[class*="single-"] .content > article > .post_info_share,
.article_style_boxed:not(.layout_excerpt):not(.single) .content .post_item {
	background-color: {$colors['alter_bg_color']};
}


/* 7.1 Top panel
-------------------------------------------------------------- */

.top_panel_top {
	background-color: {$colors['alter_hover']};
}


.top_panel_wrap .contact_field > a > span,
.top_panel_wrap .contact_field > span {
	color: {$colors['text_light']};
}


.top_panel_wrap_inner {
	background-color: {$colors['bg_color']};
}
.top_panel_fixed .top_panel_position_over.top_panel_wrap_inner {
	background-color: {$colors['alter_bg_color']} !important;
}
.top_panel_middle .sidebar_cart:after,
.top_panel_middle .sidebar_cart {
	border-color: {$colors['bd_color']};
	background-color: {$colors['bg_color']};
}

.top_panel_top a {
	color: {$colors['text']};
}
.top_panel_top a:hover {
	color: {$colors['text_hover']};
}


.top_panel_top .sc_socials.sc_socials_type_icons a {
	color: {$colors['text_dark']};
}
.top_panel_top .sc_socials.sc_socials_type_icons a:hover {
	color: {$colors['text_hover']};
}



/* User menu */
.menu_user_nav > li > a {
	color: {$colors['text_dark']};
}
.menu_user_nav > li > a:hover {
	color: {$colors['text_hover']};
}
.menu_user_nav > li ul:not(.cart_list) {
	border-color: {$colors['bd_color']};
	background-color: {$colors['bg_color']};
}
.menu_user_nav > li > ul:after{
	border-color: {$colors['bd_color']};
	background-color: {$colors['bg_color']};
}

.menu_user_nav > li ul {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_link']};
	border-color: {$colors['text_hover']};
}
.menu_user_nav > li ul li a {
	color: {$colors['alter_text']};
}
.menu_user_nav > li ul li a:hover,
.menu_user_nav > li ul li.current-menu-item > a,
.menu_user_nav > li ul li.current-menu-ancestor > a {
	color: {$colors['alter_link']};
}

.menu_user_nav > li.menu_user_controls .user_avatar {
	border-color: {$colors['bd_color']};
}

/* Bookmarks */
.menu_user_nav > li.menu_user_bookmarks .bookmarks_add {
	border-bottom-color: {$colors['alter_bd_color']};
}


/* Top panel - middle area */
.top_panel_middle {}
.top_panel_position_over.top_panel_middle {}
.logo .logo_text {
	color: {$colors['text_dark']};
}
.logo .logo_slogan {
	color: {$colors['text']};
}


/* Top panel (bottom area) */
.top_panel_bottom {
	border-color: {$colors['bd_color']};
}


.top_panel_style_3 .top_panel_middle .search_wrap:before,
.top_panel_style_5 .top_panel_middle .search_wrap:before {
	border-color: {$colors['alter_light']};
}

/* Main menu */
.menu_main_nav > li > a {
	color: {$colors['text_dark']};
}
.menu_main_nav > li + li:before {
	border-color: {$colors['alter_light']};
}
.top_panel_middle .sidebar_cart:after,
.menu_main_nav > li > ul:after {
	border-color: {$colors['alter_link']};
}
.top_panel_middle .sidebar_cart,
.menu_main_nav > li ul {
	color: {$colors['text']};
	background-color: {$colors['bg_color']};
	border-color: {$colors['alter_link']};
}
.menu_main_nav > a:hover,
.menu_main_nav > li > a:hover,
.menu_main_nav > li.sfHover > a,
.menu_main_nav > li.current-menu-item > a,
.menu_main_nav > li.current-menu-parent > a,
.menu_main_nav > li.current-menu-ancestor > a {
	color: {$colors['alter_link']};
}
.menu_main_nav > li ul li a {
	color: {$colors['alter_dark']};
}
.menu_main_nav > li ul li a:hover,
.menu_main_nav > li ul li.current-menu-item > a,
.menu_main_nav > li ul li.current-menu-ancestor > a {
	color: {$colors['alter_link']};
}


/* ---------------------- MENU HOVERS ----------------------- */


/* slide_box */
.menu_hover_slide_box .menu_main_nav > li#blob {
	background-color: {$colors['alter_bg_hover']};
}
.top_panel_inner_style_1 .menu_hover_slide_box .menu_main_nav > li#blob,
.top_panel_inner_style_2 .menu_hover_slide_box .menu_main_nav > li#blob {
	background-color: {$colors['text_hover']};
}

/* slide_line */
.menu_hover_slide_line .menu_main_nav > li#blob {
	background-color: {$colors['alter_hover']};
}
.top_panel_inner_style_1 .menu_hover_slide_line .menu_main_nav > li#blob {
	background-color: {$colors['text_hover']};
}

/* zoom_line */
.menu_hover_zoom_line .menu_main_nav > li > a:before {
	background-color: {$colors['alter_hover']};
}
.top_panel_inner_style_1 .menu_hover_zoom_line .menu_main_nav > li > a:before,
.top_panel_inner_style_2 .menu_hover_zoom_line .menu_main_nav > li > a:before {
	background-color: {$colors['inverse_text']};
}

/* path_line */
.menu_hover_path_line .menu_main_nav > li:before,
.menu_hover_path_line .menu_main_nav > li:after,
.menu_hover_path_line .menu_main_nav > li > a:before,
.menu_hover_path_line .menu_main_nav > li > a:after {
	background-color: {$colors['alter_hover']};
}
.top_panel_inner_style_1 .menu_hover_path_line .menu_main_nav > li:before,
.top_panel_inner_style_1 .menu_hover_path_line .menu_main_nav > li:after,
.top_panel_inner_style_1 .menu_hover_path_line .menu_main_nav > li > a:before,
.top_panel_inner_style_1 .menu_hover_path_line .menu_main_nav > li > a:after,
.top_panel_inner_style_2 .menu_hover_path_line .menu_main_nav > li:before,
.top_panel_inner_style_2 .menu_hover_path_line .menu_main_nav > li:after,
.top_panel_inner_style_2 .menu_hover_path_line .menu_main_nav > li > a:before,
.top_panel_inner_style_2 .menu_hover_path_line .menu_main_nav > li > a:after {
	background-color: {$colors['inverse_text']};
}

/* roll_down */
.menu_hover_roll_down .menu_main_nav > li > a:before {
	background-color: {$colors['alter_hover']};
}
.top_panel_inner_style_1 .menu_hover_roll_down .menu_main_nav > li > a:before,
.top_panel_inner_style_2 .menu_hover_roll_down .menu_main_nav > li > a:before {
	background-color: {$colors['inverse_text']};
}

/* color_line */
.menu_hover_color_line .menu_main_nav > li > a:hover,
.menu_hover_color_line .menu_main_nav > li > a:focus {
	color: {$colors['alter_dark']};
}
.top_panel_inner_style_1 .menu_hover_color_line .menu_main_nav > li > a:hover,
.top_panel_inner_style_1 .menu_hover_color_line .menu_main_nav > li > a:focus,
.top_panel_inner_style_2 .menu_hover_color_line .menu_main_nav > li > a:hover,
.top_panel_inner_style_2 .menu_hover_color_line .menu_main_nav > li > a:focus {
	color: {$colors['inverse_text']};
}
.menu_hover_color_line .menu_main_nav > li > a:before {
	background-color: {$colors['alter_dark']};
}
.top_panel_inner_style_1 .menu_hover_color_line .menu_main_nav > li > a:before,
.top_panel_inner_style_2 .menu_hover_color_line .menu_main_nav > li > a:before {
	background-color: {$colors['inverse_text']};
}
.menu_hover_color_line .menu_main_nav > li > a:after {
	background-color: {$colors['alter_hover']};
}
.top_panel_inner_style_1 .menu_hover_color_line .menu_main_nav > li > a:after {
	background-color: {$colors['inverse_hover']};
}
.menu_hover_color_line .menu_main_nav > li.sfHover > a,
.menu_hover_color_line .menu_main_nav > li > a:hover,
.menu_hover_color_line .menu_main_nav > li > a:focus {
	color: {$colors['alter_hover']};
}
.top_panel_inner_style_1 .menu_hover_color_line .menu_main_nav > li.sfHover > a,
.top_panel_inner_style_1 .menu_hover_color_line .menu_main_nav > li > a:hover,
.top_panel_inner_style_1 .menu_hover_color_line .menu_main_nav > li > a:focus {
	color: {$colors['inverse_hover']};
}

/* ---------------------- END MENU HOVERS ----------------------- */

/* Contact fields */
.top_panel_middle .contact_field,
.top_panel_middle .contact_field > a {
	color: {$colors['text_dark']}; 
}


/* Search field */
.content .search_field {}
.content .search_field {}
.content .search_submit {
	color: {$colors['text_dark']};
}
.content .search_submit:hover {
	color: {$colors['text_hover']};
}
.content .search_submit {}
.content .search_field:focus {  }
.top_panel_bottom .search_wrap .search_field,
.content .search_submit:hover {
	color: {$colors['text_hover']};
}
.top_panel_bottom .search_wrap,
.top_panel_bottom .search_wrap .search_submit,
.top_panel_bottom .search_wrap .search_field {
	color: {$colors['text_dark']};
}

.top_panel_wrap .search_style_fullscreen.search_state_opened .search_close:hover,
.top_panel_wrap .search_style_fullscreen.search_state_opened .search_submit:hover {
    color: {$colors['text_hover']} !important;
}


.top_panel_icon.search_wrap {
	background-color: {$colors['bg_color']};
	color: {$colors['text_link']};
}
.top_panel_icon .contact_icon,
.top_panel_icon .search_submit {
	color: {$colors['text_link']};
}
.top_panel_icon.menu_main_cart .contact_icon {
	background-color: {$colors['bg_color']};
}
.top_panel_middle .contact_cart a:hover > span {
	color: {$colors['text_dark']};
}

.search_style_fullscreen.search_state_closed:not(.top_panel_icon) .search_submit,
.search_style_slide.search_state_closed:not(.top_panel_icon) .search_submit {
	color: {$colors['text_dark']};
}
.search_style_expand.search_state_opened:not(.top_panel_icon) .search_submit:hover,
.search_style_slide.search_state_opened:not(.top_panel_icon) .search_submit:hover {
	color: {$colors['alter_link']};
}
.top_panel_wrap .search_wrap .search_submit:hover {
	color: {$colors['alter_link']};
}


/* Search results */
.search_results .post_more,
.search_results .search_results_close {
	color: {$colors['text_link']};
}
.search_results .post_more:hover,
.search_results .search_results_close:hover {
	color: {$colors['text_hover']};
}
.top_panel_inner_style_1 .search_results,
.top_panel_inner_style_1 .search_results:after,
.top_panel_inner_style_2 .search_results,
.top_panel_inner_style_2 .search_results:after,
.top_panel_inner_style_3 .search_results,
.top_panel_inner_style_3 .search_results:after {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_link']}; 
	border-color: {$colors['text_hover']}; 
}
.top_panel_inner_style_1 .search_results a,
.top_panel_inner_style_1 .search_results .post_info a,
.top_panel_inner_style_1 .search_results .post_info a > span,
.top_panel_inner_style_1 .search_results .post_more,
.top_panel_inner_style_1 .search_results .search_results_close,
.top_panel_inner_style_2 .search_results a,
.top_panel_inner_style_2 .search_results .post_info a,
.top_panel_inner_style_2 .search_results .post_info a > span,
.top_panel_inner_style_2 .search_results .post_more,
.top_panel_inner_style_2 .search_results .search_results_close,
.top_panel_inner_style_3 .search_results a,
.top_panel_inner_style_3 .search_results .post_info a,
.top_panel_inner_style_3 .search_results .post_info a > span,
.top_panel_inner_style_3 .search_results .post_more,
.top_panel_inner_style_3 .search_results .search_results_close {
	color: {$colors['inverse_link']};
}
.top_panel_inner_style_1 .search_results a:hover,
.top_panel_inner_style_1 .search_results .post_info a:hover,
.top_panel_inner_style_1 .search_results .post_info a:hover > span,
.top_panel_inner_style_1 .search_results .post_more:hover,
.top_panel_inner_style_1 .search_results .search_results_close:hover,
.top_panel_inner_style_2 .search_results a:hover,
.top_panel_inner_style_2 .search_results .post_info a:hover,
.top_panel_inner_style_2 .search_results .post_info a:hover > span,
.top_panel_inner_style_2 .search_results .post_more:hover,
.top_panel_inner_style_2 .search_results .search_results_close:hover,
.top_panel_inner_style_3 .search_results a:hover,
.top_panel_inner_style_3 .search_results .post_info a:hover,
.top_panel_inner_style_3 .search_results .post_info a:hover > span,
.top_panel_inner_style_3 .search_results .post_more:hover,
.top_panel_inner_style_3 .search_results .search_results_close:hover {
	color: {$colors['inverse_hover']};
}

/* Header style 8 */
.top_panel_inner_style_8 .menu_pushy_wrap .menu_pushy_button {
	color: {$colors['alter_text']}; 
}
.top_panel_inner_style_8 .menu_pushy_wrap .menu_pushy_button:hover {
	color: {$colors['alter_dark']}; 
}
.top_panel_inner_style_8 .top_panel_buttons .contact_icon,
.top_panel_inner_style_8 .top_panel_buttons .top_panel_icon .search_submit {
	color: {$colors['alter_text']}; 
}
.top_panel_inner_style_8 .top_panel_buttons a:hover .contact_icon,
.top_panel_inner_style_8 .top_panel_buttons .top_panel_icon:hover .search_submit {
	color: {$colors['alter_dark']}; 
}
.pushy_inner {
	color: {$colors['text']}; 
	background-color: {$colors['bg_color']}; 
}
.pushy_inner a {
	color: {$colors['text_link']}; 
}
.pushy_inner a:hover {
	color: {$colors['text_hover']}; 
}
.pushy_inner ul ul {
	background-color: {$colors['alter_bg_color_0_8']}; 
}

/* Register and login popups */
.top_panel_inner_style_3 .popup_wrap a,
.top_panel_inner_style_3 .popup_wrap .sc_socials.sc_socials_type_icons a:hover,
.top_panel_inner_style_4 .popup_wrap a,
.top_panel_inner_style_4 .popup_wrap .sc_socials.sc_socials_type_icons a:hover,
.top_panel_inner_style_5 .popup_wrap a,
.top_panel_inner_style_5 .popup_wrap .sc_socials.sc_socials_type_icons a:hover {
	color: {$colors['text_link']};
}
.top_panel_inner_style_3 .popup_wrap a:hover,
.top_panel_inner_style_4 .popup_wrap a:hover,
.top_panel_inner_style_5 .popup_wrap a:hover {
	color: {$colors['text_hover']};
}
.top_panel_inner_style_3 .popup_wrap,
.top_panel_inner_style_4 .popup_wrap,
.top_panel_inner_style_5 .popup_wrap,
.top_panel_inner_style_3 .popup_wrap .popup_close,
.top_panel_inner_style_3 .popup_wrap .sc_socials.sc_socials_type_icons a,
.top_panel_inner_style_4 .popup_wrap .popup_close,
.top_panel_inner_style_4 .popup_wrap .sc_socials.sc_socials_type_icons a,
.top_panel_inner_style_5 .popup_wrap .popup_close,
.top_panel_inner_style_5 .popup_wrap .sc_socials.sc_socials_type_icons a {
	color: {$colors['text']};
}
.top_panel_inner_style_3 .popup_wrap .popup_close:hover,
.top_panel_inner_style_4 .popup_wrap .popup_close:hover,
.top_panel_inner_style_5 .popup_wrap .popup_close:hover {
	color: {$colors['text_dark']};
}


/* Header mobile */
.header_mobile .menu_button,
.header_mobile .menu_main_cart .top_panel_cart_button .contact_icon {
	color: {$colors['text_dark']};
}
.header_mobile .side_wrap {
	color: {$colors['inverse_text']};
}
.header_mobile .panel_top,
.header_mobile .side_wrap {
	background-color: {$colors['text_link']};
}
.header_mobile .panel_middle {
	background-color: {$colors['text_link']};
}

.header_mobile .menu_button:hover,
.header_mobile .menu_main_cart .top_panel_cart_button .contact_icon:hover,
.header_mobile .menu_main_cart.top_panel_icon:hover .top_panel_cart_button .contact_icon,
.header_mobile .side_wrap .close:hover{
	color: {$colors['text_hover']};
}
.header_mobile .sidebar_cart {
	border-color: {$colors['text_hover']};
}

.header_mobile .menu_main_nav > li a,
.header_mobile .menu_main_nav > li > a:hover {
	color: {$colors['inverse_link']};
}
.header_mobile .menu_main_nav > a:hover, 
.header_mobile .menu_main_nav > li.sfHover > a, 
.header_mobile .menu_main_nav > li.current-menu-item > a, 
.header_mobile .menu_main_nav > li.current-menu-parent > a, 
.header_mobile .menu_main_nav > li.current-menu-ancestor > a,
.header_mobile .menu_main_nav > li > a:hover,
.header_mobile .menu_main_nav > li ul li a:hover, 
.header_mobile .menu_main_nav > li ul li.current-menu-item > a, 
.header_mobile .menu_main_nav > li ul li.current-menu-ancestor > a,
.header_mobile .login a:hover {
    color: {$colors['inverse_hover']};
}
.header_mobile .popup_wrap .popup_close:hover {
    color: {$colors['text_dark']};
}

.header_mobile .search_wrap,
.header_mobile .login {
	border-color: {$colors['text_link']};
}
.header_mobile .login .popup_link,
.header_mobile .sc_socials.sc_socials_type_icons a {
	color: {$colors['inverse_link']};
}

.header_mobile .search_wrap .search_field,
.header_mobile .search_wrap .search_field:focus {  
	color: {$colors['inverse_text']};
}

.header_mobile .widget_shopping_cart ul.cart_list > li > a:hover {
	color: {$colors['inverse_text']};
}

.header_mobile .popup_wrap .sc_socials.sc_socials_type_icons a {
	color: {$colors['text_light']};
}




/* 7.2 Main Slider
-------------------------------------------------------------- */
.tparrows.default {
	color: {$colors['bg_color']};
}
.tp-bullets.simplebullets.round .bullet {
	background-color: {$colors['bg_color']};
}
.tp-bullets.simplebullets.round .bullet.selected {
	border-color: {$colors['bg_color']};
}
.slider_over_content_inner {
	background-color: {$colors['bg_color_0_8']};
}
.slider_over_button {
	color: {$colors['text_dark']};
	background-color: {$colors['bg_color_0_8']};
}
.slider_over_close {
	color: {$colors['text_dark']};
}



/* 7.3 Top panel: Page title and breadcrumbs
-------------------------------------------------------------- */

.top_panel_title_inner .page_title {
	color: {$colors['text_dark']};
}

.top_panel_title_inner .post_navi .post_navi_item a,
.top_panel_title_inner .breadcrumbs a.breadcrumbs_item {
	color: {$colors['text_dark']};
}
.top_panel_title_inner .post_navi .post_navi_item a:hover,
.top_panel_title_inner .breadcrumbs a.breadcrumbs_item:hover {
	color: {$colors['alter_light']};
}
.top_panel_title_inner .post_navi span,
.top_panel_title_inner .breadcrumbs span {
	color: {$colors['alter_light']};
}
.post_navi .post_navi_item + .post_navi_item:before,
.top_panel_title_inner .breadcrumbs .breadcrumbs_delimiter {
	color: {$colors['alter_light']};
}





/* 7.4 Main content wrapper
-------------------------------------------------------------- */

/* Layout Excerpt */
.post_title .post_icon {
	color: {$colors['text_link']};
}

/* Blog pagination */
.pagination > a {
	border-color: {$colors['text_link']};
}



/* 7.5 Post formats
-------------------------------------------------------------- */

/* Aside */
.post_format_aside.post_item_single .post_content p,
.post_format_aside .post_descr {
	border-color: {$colors['text_link']};
	background-color: {$colors['bg_color']};
}




/* 7.6 Posts layouts
-------------------------------------------------------------- */

.post_item_excerpt:not([class*="column"]) {
	border-color: {$colors['bd_color']};
}

/* Hover icon */
.hover_icon:before {
	border-color: {$colors['inverse_text']};
	background-color: {$colors['text_link']};
	color: {$colors['inverse_text']};
}
.hover_icon:after {}

/* Post info */
.post_info a,
.post_info a > span {
	color: {$colors['text_light']};
}
.post_info a[class*="icon-"] {
	color: {$colors['text_hover']};
}
.post_info a:hover,
.post_info a:hover > span {
	color: {$colors['text_hover']};
}


.post_item .post_readmore {
	color: {$colors['text_dark']};
	background-color: {$colors['text_hover']};
	color: {$colors['inverse_text']};
}
.post_item .post_readmore:after {
	border-color: {$colors['text_link']};
}
.post_item .post_readmore:hover {
	background-color: {$colors['text_link']};
	color: {$colors['inverse_text']};
}


/* Related posts */
.post_item_related .post_info a {
	color: {$colors['text']};
}
.post_item_related .post_info a:hover,
.post_item_related .post_title a:hover {
	color: {$colors['text_hover']};
}
.article_style_boxed.sidebar_show[class*="single-"] .related_wrap .post_item_related {
	background-color: {$colors['alter_bg_color']};
}
.post_item_related .post_info_date {
	color: {$colors['text_light']};
}
.post_item_related .post_info_date:hover {
	color: {$colors['text_hover']};
}



/* Masonry and Portfolio */
.isotope_wrap .isotope_item_colored_1 .post_featured {
	border-color: {$colors['text_link']};
}

/* Isotope filters */
.isotope_filters a {
	border-color: {$colors['text_link']};
	background-color: {$colors['text_link']};
	color: {$colors['inverse_text']};
}
.isotope_filters a.active,
.isotope_filters a:hover {
	border-color: {$colors['text_hover']};
	background-color: {$colors['text_hover']};
}




/* 7.7 Paginations
-------------------------------------------------------------- */

/* Style 'Pages' and 'Slider' */
.pagination_single > .pager_numbers,
.pagination_single a,
.pagination_slider .pager_cur,
.pagination_pages > a,
.pagination_pages > span {
	background-color: {$colors['alter_bg_color']};
	color: {$colors['text_light']};
}
.pagination_single > .pager_numbers,
.pagination_single a:hover,
.pagination_slider .pager_cur:hover,
.pagination_slider .pager_cur:focus,
.pagination_pages > .active,
.pagination_pages > a:hover {
	background-color: {$colors['alter_link']};
	color: {$colors['inverse_text']};
}

.pagination_slider .pager_slider {
	border-color: {$colors['bd_color']};
	background-color: {$colors['bg_color']};
}

.pagination_wrap .pager_next,
.pagination_wrap .pager_prev,
.pagination_wrap .pager_last,
.pagination_wrap .pager_first {
	color: {$colors['text_light']};
}
.pagination_wrap .pager_next:hover,
.pagination_wrap .pager_prev:hover,
.pagination_wrap .pager_last:hover,
.pagination_wrap .pager_first:hover {
	color: {$colors['inverse_text']};
}



/* Style 'Load more' */
.pagination_viewmore > a {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_link']};
}
.pagination_viewmore > a:hover {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_hover']};
}

/* Loader picture */
.viewmore_loader,
.mfp-preloader span,
.sc_video_frame.sc_video_active:before {
	background-color: {$colors['text_hover']};
}



/* 8 Single page parts
-------------------------------------------------------------- */


/* 8.1 Attachment and Portfolio post navigation
------------------------------------------------------------- */
.post_featured .post_nav_item {
	color: {$colors['inverse_text']};
}
.post_featured .post_nav_item:before {
	background-color: {$colors['text_link']};
	color: {$colors['inverse_text']};
}
.post_featured .post_nav_item .post_nav_info {
	background-color: {$colors['text_link']};
}


/* 8.2 Reviews block
-------------------------------------------------------------- */
.reviews_block .reviews_summary .reviews_item {
	background-color: {$colors['text_link']};
}
.reviews_block .reviews_summary,
.reviews_block .reviews_max_level_100 .reviews_stars_bg {
	background-color: {$colors['alter_bg_hover']};
}
.reviews_block .reviews_max_level_100 .reviews_stars_hover,
.reviews_block .reviews_item .reviews_slider {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_link']};
}
.reviews_block .reviews_item .reviews_stars_hover {
	color: {$colors['text_link']};
}
.reviews_block .reviews_value {
	color: {$colors['text_dark']};
}
.reviews_block .reviews_summary .reviews_criteria {
	color: {$colors['text']};
}
.reviews_block .reviews_summary .reviews_value {
	color: {$colors['inverse_text']};
}

/* Summary stars in the post item (under the title) */
.post_item .post_rating .reviews_stars_bg,
.post_item .post_rating .reviews_stars_hover,
.post_item .post_rating .reviews_value {
	color: {$colors['text_link']};
}


/* 8.3 Post author
-------------------------------------------------------------- */
.post_author {
	background-color: {$colors['alter_bg_color']};
	color: {$colors['alter_dark']};
}
.post_author .post_author_title {
	color: {$colors['alter_dark']};
}
.post_author .post_author_title a {
	color: {$colors['text_link']};
}
.post_author .post_author_title a:hover {
	color: {$colors['text_hover']};
}
.post_author .post_author_info .sc_socials_shape_square a {
	color: {$colors['alter_dark']};
}
.post_author .post_author_info .sc_socials_shape_square a:hover {
	color: {$colors['text_hover']};
}



/* 8.4 Comments
-------------------------------------------------------- */
.comments_list_wrap ul.children,
.comments_list_wrap ul > li + li {
	border-top-color: {$colors['bd_color']};
}
.comments_list_wrap .comment-respond {
	border-bottom-color: {$colors['bd_color']};
}
.comments_list_wrap > ul {
	border-bottom-color: {$colors['bd_color']};
}
.comments_list_wrap .comment_info > span.comment_author {
	color: {$colors['text_link']};
}
.comments_list_wrap .comment_info .comment_date_label,
.comments_list_wrap .comment_info .comment_date_value,
.comments_list_wrap .comment_info .comment_time {
	color: {$colors['text_light']};
}
.comments_list_wrap .comment_reply a {
	color: {$colors['text_hover']};
}
.comments_list_wrap .comment_reply a:hover {
	color: {$colors['text_link']};
}


/* 8.5 Page 404
-------------------------------------------------------------- */
.post_item_404 .page_title,
.post_item_404 .page_subtitle {
	color: {$colors['text_link']};
}
.post_item_404 .page_description a {
	color: {$colors['text_hover']};
}
.post_item_404 .page_description a:hover {
	color: {$colors['alter_hover']};
}


/* 9. Sidebars
-------------------------------------------------------------- */

/* Side menu */
.sidebar_outer_menu .menu_side_nav li > a,
.sidebar_outer_menu .menu_side_responsive li > a {
	color: {$colors['text_dark']};
}
.sidebar_outer_menu .menu_side_nav li > a:hover,
.sidebar_outer_menu .menu_side_nav li.sfHover > a,
.sidebar_outer_menu .menu_side_responsive li > a:hover,
.sidebar_outer_menu .menu_side_responsive li.sfHover > a {
	color: {$colors['alter_dark']};
	background-color: {$colors['alter_bg_hover']};
}
.sidebar_outer_menu .menu_side_nav > li ul,
.sidebar_outer_menu .menu_side_responsive > li ul {
	color: {$colors['text_dark']};
	background-color: {$colors['bg_color']};
	border-color: {$colors['bd_color']};
}
.sidebar_outer_menu .menu_side_nav li.current-menu-item > a,
.sidebar_outer_menu .menu_side_nav li.current-menu-parent > a,
.sidebar_outer_menu .menu_side_nav li.current-menu-ancestor > a,
.sidebar_outer_menu .menu_side_responsive li.current-menu-item > a,
.sidebar_outer_menu .menu_side_responsive li.current-menu-parent > a,
.sidebar_outer_menu .menu_side_responsive li.current-menu-ancestor > a {
	color: {$colors['text_light']};
}
.sidebar_outer_menu .sidebar_outer_menu_buttons > a {
	color: {$colors['text_dark']};
}
.sidebar_outer_menu .sidebar_outer_menu_buttons > a:hover {
	color: {$colors['text_link']};
}

/* Common rules */
.sidebar_inner aside:nth-child(3n+4),
.sidebar_inner aside:nth-child(3n+5),
.sidebar_inner aside:nth-child(3n+6),
.sidebar_outer_inner aside:nth-child(3n+4),
.sidebar_outer_inner aside:nth-child(3n+5),
.sidebar_outer_inner aside:nth-child(3n+6),
.widget_area_inner aside:nth-child(2n+3),
.widget_area_inner aside:nth-child(2n+4),
.widget_area_inner aside+aside {
	border-color: {$colors['bd_color']};
}
.widget_area_inner {
	color: {$colors['text']};
}
.widget_area_inner a,
.widget_area_inner ul li a:hover {
	color: {$colors['text_link']};
}
.widget_area_inner ul li:before,
.widget_area_inner button:before {
	color: {$colors['text_hover']};
}
.widget_area_inner a:hover,
.widget_area_inner ul li a,
.widget_area_inner button:hover:before {
	color: {$colors['text_hover']};
}




.widget_area_inner .post_title a {
	color: {$colors['text_dark']};
}
.widget_area_inner .post_title a:hover {
	color: {$colors['text_hover']};
}
.widget_area_inner .widget_text a:not(.sc_button),
.widget_area_inner .post_info a {
	color: {$colors['text_link']};
}
.widget_area_inner .widget_text a:not(.sc_button):hover,
.widget_area_inner .post_info a:hover {
	color: {$colors['text_hover']};
}


/* Widget: Search */
.widget_area_inner .widget_product_search .search_field,
.widget_area_inner .widget_search .search_field { color: {$colors['text_dark']}; }

.widget_area_inner .widget_product_search .search_button:before,
.widget_area_inner .widget_search .search_button:before { color: {$colors['text_dark']}; }
.widget_area_inner .widget_product_search .search_button:hover:before,
.widget_area_inner .widget_search .search_button:hover:before {
	color: {$colors['text_hover']};
}

/* Widget: Calendar */
.widget_area_inner .widget_calendar .weekday {
	color: {$colors['text_dark']};
}
.widget_area_inner .widget_calendar td a:hover {
	background-color: {$colors['text_link']};
	color: {$colors['inverse_text']};
}
.widget_area_inner .widget_calendar .today .day_wrap {
	border-color: {$colors['text_hover']};
}



/* Widget: Tag Cloud */
.widget_area_inner .widget_product_tag_cloud a,
.widget_area_inner .widget_tag_cloud a {
	background-color: {$colors['alter_bg_color']};
	color: {$colors['text']};
}
.widget_area_inner .widget_product_tag_cloud a:hover,
.widget_area_inner .widget_tag_cloud a:hover {
	background-color: {$colors['text_hover']};
	color: {$colors['inverse_text']};
}
.widget_area_inner .widget_product_tag_cloud a:after,
.widget_area_inner .widget_tag_cloud a:after {
	border-color: {$colors['text_hover']};
}


/* Left or Right sidebar */
.sidebar_outer_inner aside,
.sidebar_inner aside {
	border-top-color: {$colors['bd_color']};
}


/* 10. Footer areas
-------------------------------------------------------------- */

/* Contacts */
.contacts_wrap_inner {
	color: {$colors['text']};
	background-color: {$colors['bg_color']};
}

/* Testimonials and Twitter */
.testimonials_wrap_inner,
.twitter_wrap_inner {
	color: {$colors['text']};
	background-color: {$colors['bg_color']};
}

/* Copyright */
.copyright_wrap_inner {
    background-color: {$colors['bg_color']};
	border-color: {$colors['bd_color']};
}
.copyright_style_socials .menu_footer_nav li + li:before,
.copyright_style_menu .menu_footer_nav li + li:before {
	 border-color: {$colors['alter_light']};
}
.copyright_wrap_inner .menu_footer_nav li a {
    color: {$colors['text_link']};
}
.copyright_wrap_inner .menu_footer_nav li a:hover {
    color: {$colors['text_hover']};
}
.copyright_wrap_inner .copyright_text {
    color: {$colors['text_light']};
}
.copyright_wrap_inner .copyright_text a {
    color: {$colors['text_hover']};
}
.copyright_wrap_inner .copyright_text a:hover {
    color: {$colors['text_link']};
}



/* 11. Utils
-------------------------------------------------------------- */

/* Scroll to top */
.scroll_to_top {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_link']};
}
.scroll_to_top:hover {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_hover']};
}

/* Preloader */
#page_preloader {
	background-color: {$colors['bg_color']};
}
.preloader_wrap > div {
	background-color: {$colors['text_link']};
}

/* Gallery preview */
.scheme_self.gallery_preview:before {
	background-color: {$colors['bg_color']};
}

/* 12. Registration and Login popups
-------------------------------------------------------------- */
.popup_wrap {
	background-color: {$colors['bg_color']};
}




/* 13. Third party plugins
------------------------------------------------------- */

/* 13.2 WooCommerce
------------------------------------------------------ */

/* Theme colors */
/*
.woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce #content input.button.alt:hover, .woocommerce-page a.button.alt:hover, .woocommerce-page button.button.alt:hover, .woocommerce-page input.button.alt:hover, .woocommerce-page #respond input#submit.alt:hover, .woocommerce-page #content input.button.alt:hover,
.woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover, .woocommerce #content input.button:hover, .woocommerce #respond input#submit:hover, .woocommerce-page a.button:hover, .woocommerce-page button.button:hover, .woocommerce-page input.button:hover, .woocommerce-page #respond input#submit:hover, .woocommerce-page #content input.button:hover,
.woocommerce .quantity input[type="button"]:hover, .woocommerce #content input[type="button"]:hover, .woocommerce-page .quantity input[type="button"]:hover, .woocommerce-page #content .quantity input[type="button"]:hover,
*/
.woocommerce .woocommerce-message:before, .woocommerce-page .woocommerce-message:before,
.woocommerce div.product span.price, .woocommerce div.product p.price, .woocommerce #content div.product span.price, .woocommerce #content div.product p.price, .woocommerce-page div.product span.price, .woocommerce-page div.product p.price, .woocommerce-page #content div.product span.price, .woocommerce-page #content div.product p.price,.woocommerce ul.products li.product .price,.woocommerce-page ul.products li.product .price,
.woocommerce a:hover h3, .woocommerce-page a:hover h3,
.woocommerce .cart-collaterals .order-total strong, .woocommerce-page .cart-collaterals .order-total strong
{
	color: {$colors['text_link']};
}

.woocommerce .star-rating, .woocommerce-page .star-rating, .woocommerce .star-rating:before, .woocommerce-page .star-rating:before,
.widget_area_inner .widgetWrap ul > li .star-rating span, .woocommerce #review_form #respond .stars a, .woocommerce-page #review_form #respond .stars a
{
	color: {$colors['alter_link']};
}

.woocommerce .checkout #order_review .order-total .amount, .woocommerce-page .checkout #order_review .order-total .amount,
.woocommerce ul.cart_list li > .amount, .woocommerce ul.product_list_widget li > .amount, .woocommerce-page ul.cart_list li > .amount, .woocommerce-page ul.product_list_widget li > .amount,
.woocommerce ul.cart_list li span .amount, .woocommerce ul.product_list_widget li span .amount, .woocommerce-page ul.cart_list li span .amount, .woocommerce-page ul.product_list_widget li span .amount,
.woocommerce ul.cart_list li ins .amount, .woocommerce ul.product_list_widget li ins .amount, .woocommerce-page ul.cart_list li ins .amount, .woocommerce-page ul.product_list_widget li ins .amount,
.woocommerce.widget_shopping_cart .total .amount, .woocommerce .widget_shopping_cart .total .amount, .woocommerce-page.widget_shopping_cart .total .amount, .woocommerce-page .widget_shopping_cart .total .amount {
	color: #f94c00;
}

.woocommerce div.quantity span, .woocommerce-page div.quantity span {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_link']};
}
.woocommerce div.quantity span:hover, .woocommerce-page div.quantity span:hover {
	background-color: {$colors['text_hover']};
}

.woocommerce .widget_price_filter .price_slider_wrapper .ui-widget-content {
	background-color: {$colors['alter_light']};
}
.woocommerce .widget_price_filter .ui-slider .ui-slider-range,.woocommerce-page .widget_price_filter .ui-slider .ui-slider-range
{ 
	background-color: {$colors['text_hover']};
}
.woocommerce .widget_price_filter .price_label span {
	color: {$colors['text_hover']};
}

.woocommerce .widget_price_filter .ui-slider .ui-slider-handle, .woocommerce-page .widget_price_filter .ui-slider .ui-slider-handle
{
	background: {$colors['text_link']};
}

.woocommerce .woocommerce-message, .woocommerce-page .woocommerce-message,
.woocommerce a.button.alt:active, .woocommerce button.button.alt:active, .woocommerce input.button.alt:active, .woocommerce #respond input#submit.alt:active, .woocommerce #content input.button.alt:active, .woocommerce-page a.button.alt:active, .woocommerce-page button.button.alt:active, .woocommerce-page input.button.alt:active, .woocommerce-page #respond input#submit.alt:active, .woocommerce-page #content input.button.alt:active,
.woocommerce a.button:active, .woocommerce button.button:active, .woocommerce input.button:active, .woocommerce #respond input#submit:active, .woocommerce #content input.button:active, .woocommerce-page a.button:active, .woocommerce-page button.button:active, .woocommerce-page input.button:active, .woocommerce-page #respond input#submit:active, .woocommerce-page #content input.button:active
{ 
	border-top-color: {$colors['text_link']};
}

/* Buttons */
.woocommerce a.button, .woocommerce button.button, .woocommerce input.button,
.woocommerce #respond input#submit, .woocommerce #content input.button, .woocommerce-page a.button, 
.woocommerce-page button.button, .woocommerce-page input.button, .woocommerce-page #respond input#submit, 
.woocommerce-page #content input.button, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, 
.woocommerce #respond input#submit.alt, .woocommerce #content input.button.alt, .woocommerce-page a.button.alt, .woocommerce-page button.button.alt, 
.woocommerce-page input.button.alt, .woocommerce-page #respond input#submit.alt, .woocommerce-page #content input.button.alt, .woocommerce-account .addresses .title .edit,
.woocommerce ul.products li.product .add_to_cart_button, .woocommerce-page ul.products li.product .add_to_cart_button {
	background-color: {$colors['text_link']};
	color: {$colors['inverse_text']};
	{$fonts['button_font-family']}
}


.woocommerce [class*="sc_button_hover_fade"]:after {
    border-color: {$colors['text_hover']};
}


.woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover,
.woocommerce #respond input#submit:hover, .woocommerce #content input.button:hover, .woocommerce-page a.button:hover, 
.woocommerce-page button.button:hover, .woocommerce-page input.button:hover, .woocommerce-page #respond input#submit:hover,
.woocommerce-page #content input.button:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover,
.woocommerce #respond input#submit.alt:hover, .woocommerce #content input.button.alt:hover, .woocommerce-page a.button.alt:hover, .woocommerce-page button.button.alt:hover,
.woocommerce-page input.button.alt:hover, .woocommerce-page #respond input#submit.alt:hover, .woocommerce-page #content input.button.alt:hover, .woocommerce-account .addresses .title .edit:hover,
.woocommerce ul.products li.product .add_to_cart_button:hover, .woocommerce-page ul.products li.product .add_to_cart_button:hover {
	background-color: {$colors['text_hover']};
	color: {$colors['inverse_text']};
}


/* Messages */
.article_style_boxed.woocommerce .woocommerce-error, .article_style_boxed.woocommerce .woocommerce-info, .article_style_boxed.woocommerce .woocommerce-message,
.article_style_boxed.woocommerce-page .woocommerce-error, .article_style_boxed.woocommerce-page .woocommerce-info, .article_style_boxed.woocommerce-page .woocommerce-message {
	background-color: {$colors['alter_bg_color']};
}
.article_style_boxed.woocommerce.archive .woocommerce-error, .article_style_boxed.woocommerce.archive .woocommerce-info, .article_style_boxed.woocommerce.archive .woocommerce-message,
.article_style_boxed.woocommerce-page.archive .woocommerce-error, .article_style_boxed.woocommerce-page.archive .woocommerce-info, .article_style_boxed.woocommerce-page.archive .woocommerce-message {
	background-color: {$colors['alter_bg_color']};
}

/* Products stream */
.woocommerce span.new, .woocommerce-page span.new,
.woocommerce span.onsale, .woocommerce-page span.onsale {
	background-color: {$colors['text_hover']};
	color: {$colors['inverse_text']};
}
.article_style_boxed.woocommerce ul.products li.product .post_item_wrap, .article_style_boxed.woocommerce-page ul.products li.product .post_item_wrap {
	background-color: {$colors['alter_bg_color']};
}

.woocommerce ul.products li.product .price, .woocommerce-page ul.products li.product .price {
	color: {$colors['text_link']};
}
.woocommerce ul.products li.product .star-rating:before, .woocommerce ul.products li.product .star-rating span {
	color: {$colors['text_hover']};
}
.woocommerce ul.products li.product .price del, .woocommerce-page ul.products li.product .price del {
	color: {$colors['text_light']};
}

.scheme_self.article_style_boxed.woocommerce ul.products li.product .post_item_wrap {
	background-color: {$colors['alter_bg_hover']};
}
.scheme_self.article_style_boxed.woocommerce-page ul.products li.product .post_item_wrap {
	background-color: {$colors['alter_bg_hover']};
}
.scheme_self.article_style_boxed.woocommerce ul.products li.product .post_content {
	background-color: {$colors['alter_bg_color']};
}
.scheme_self.article_style_boxed.woocommerce-page ul.products li.product .post_content {
	background-color: {$colors['alter_bg_color']};
}

/* Pagination */
.woocommerce nav.woocommerce-pagination ul li a, .woocommerce nav.woocommerce-pagination ul li span.current {
	border-color: {$colors['text_link']};
	background-color: {$colors['text_link']};
	color: {$colors['inverse_text']};
}
.woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li span.current {
	color: {$colors['text_link']};
	background-color: {$colors['bg_color']};
}

/* Tabs */
.woocommerce div.product .woocommerce-tabs .panel, .woocommerce #content div.product .woocommerce-tabs .panel, .woocommerce-page div.product .woocommerce-tabs .panel, .woocommerce-page #content div.product .woocommerce-tabs .panel {
	border-color: {$colors['bd_color']};
}

/* Tabs on single product page */
.scheme_self.woocommerce-tabs.trx-stretch-width {
	background-color: {$colors['bg_color']};
}
.single-product div.product .woocommerce-tabs.trx-stretch-width .wc-tabs li a {
	color: {$colors['text']};
}
.single-product div.product .woocommerce-tabs.trx-stretch-width .wc-tabs li a:hover,
.single-product div.product .woocommerce-tabs.trx-stretch-width .wc-tabs li.active a {
	color: {$colors['text_dark']};
}
.single-product div.product .woocommerce-tabs.trx-stretch-width .wc-tabs li.active a:after {
	background-color: {$colors['text_link']};
}

/* Cart */
.woocommerce table.cart thead th, .woocommerce #content table.cart thead th, .woocommerce-page table.cart thead th, .woocommerce-page #content table.cart thead th {
	background-color: {$colors['text_link']};
	color: {$colors['inverse_text']};
}

/* My Account */
.woocommerce-account .woocommerce-MyAccount-navigation,
.woocommerce-MyAccount-navigation li+li {
	border-color: {$colors['bd_color']};
}
.woocommerce-MyAccount-navigation li.is-active a {
	color: {$colors['text_dark']};
}

/* Widgets */
.top_panel_wrap .widget_shopping_cart ul.cart_list > li > a:hover {
	color: {$colors['text_hover']}; 
}

/* Widget Product Categories */
body:not(.woocommerce) .widget_area:not(.footer_wrap) .widget_product_categories ul.product-categories li+li {
	border-color: {$colors['alter_bd_color']};
}
body:not(.woocommerce) .widget_area:not(.footer_wrap) .widget_product_categories ul.product-categories li,
body:not(.woocommerce) .widget_area:not(.footer_wrap) .widget_product_categories ul.product-categories li > a {
	color: {$colors['text']};
}
body:not(.woocommerce) .widget_area:not(.footer_wrap) .widget_product_categories ul.product-categories li:hover,
body:not(.woocommerce) .widget_area:not(.footer_wrap) .widget_product_categories ul.product-categories li:hover > a,
body:not(.woocommerce) .widget_area:not(.footer_wrap) .widget_product_categories ul.product-categories li > a:hover {
	color: {$colors['text_dark']};
}
body:not(.woocommerce) .widget_area:not(.footer_wrap) .widget_product_categories ul.product-categories ul {
	background-color: {$colors['alter_bg_color']};
}

.woocommerce ul.cart_list li a, .woocommerce ul.product_list_widget li a, .woocommerce-page ul.cart_list li a, .woocommerce-page ul.product_list_widget li a {
	color: {$colors['text_link']};
}
.woocommerce ul.cart_list li a:hover, .woocommerce ul.product_list_widget li a:hover, .woocommerce-page ul.cart_list li a:hover, .woocommerce-page ul.product_list_widget li a:hover {
	color: {$colors['text_hover']};
}
.woocommerce.widget_shopping_cart .total .amount, .woocommerce .widget_shopping_cart .total .amount, .woocommerce-page.widget_shopping_cart .total .amount, .woocommerce-page .widget_shopping_cart .total .amount {
	color: {$colors['text_hover']};
}
li.product .product_cats,
li.product .product_cats a {
	color: {$colors['text_hover']};
}
li.product .product_cats a:hover {
	color: {$colors['text_link']};
}


/* 13.3 Tribe Events
------------------------------------------------------- */
.tribe-events-calendar thead th {
	background-color: {$colors['text_link']};
}

/* Buttons */
a.tribe-events-read-more,
.tribe-events-button,
.tribe-events-nav-previous a,
.tribe-events-nav-next a,
.tribe-events-widget-link a,
.tribe-events-viewmore a {
	background-color: {$colors['text_link']};
	color: {$colors['inverse_text']};
}
a.tribe-events-read-more:hover,
.tribe-events-button:hover,
.tribe-events-nav-previous a:hover,
.tribe-events-nav-next a:hover,
.tribe-events-widget-link a:hover,
.tribe-events-viewmore a:hover {
	background-color: {$colors['text_hover']};
	color: {$colors['inverse_text']};
}





/* 13.5 Visual Composer
------------------------------------------------------ */
.scheme_self.vc_row {
	background-color: {$colors['bg_color']};
}


/* 13.6 Booking Calendar
------------------------------------------------------ */
.booking_month_container_custom,
.booking_month_navigation_button_custom {
	background-color: {$colors['alter_bg_color']} !important;
}
.booking_month_name_custom,
.booking_month_navigation_button_custom {
	color: {$colors['alter_dark']} !important;
}
.booking_month_navigation_button_custom:hover {
	color: {$colors['inverse_text']} !important;
	background-color: {$colors['text_hover']} !important;
}


/* 13.6 LearnDash LMS
------------------------------------------------------ */
#learndash_next_prev_link > a {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_link']};
}
#learndash_next_prev_link > a:hover {
	background-color: {$colors['text_hover']};
}
.widget_area dd.course_progress div.course_progress_blue {
	background-color: {$colors['text_hover']};
}


/* 13.7 HTML5 Audio Player
------------------------------------------------------- */
#myplayer .ttw-music-player .progress-wrapper {
	background-color: {$colors['alter_bg_hover']};
}
#myplayer .ttw-music-player .tracklist li.track {
	border-color: {$colors['bd_color']};
}
#myplayer .ttw-music-player .tracklist,
#myplayer .ttw-music-player .buy,
#myplayer .ttw-music-player .description,
#myplayer .ttw-music-player .artist,
#myplayer .ttw-music-player .artist-outer {
	color: {$colors['text']};
}
#myplayer .ttw-music-player .player .title,
#myplayer .ttw-music-player .tracklist li:hover {
	color: {$colors['text_dark']};
}




/* 15. Shortcodes
-------------------------------------------------------------- */

/* Accordion */
.sc_accordion .sc_accordion_item .sc_accordion_title {
	border-color: {$colors['bd_color']};
}
.sc_accordion .sc_accordion_item .sc_accordion_title .sc_accordion_icon {
	color: {$colors['alter_light']};
	background-color: {$colors['alter_bg_color']};
}
.sc_accordion .sc_accordion_item .sc_accordion_title.ui-state-active {
	color: {$colors['text_link']};
	border-color: {$colors['text_link']};
}
.sc_accordion .sc_accordion_item .sc_accordion_title.ui-state-active .sc_accordion_icon_opened {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_link']};
}
.sc_accordion .sc_accordion_item .sc_accordion_title:hover {
	color: {$colors['text_hover']};
	border-color: {$colors['text_hover']};
}
.sc_accordion .sc_accordion_item .sc_accordion_title:hover .sc_accordion_icon_opened {
	background-color: {$colors['text_hover']};
}
.sc_accordion .sc_accordion_item .sc_accordion_content {
	border-color: {$colors['bd_color']};
}


/* Audio */

/* Standard style */
/*
.sc_audio .sc_audio_author_name,
.sc_audio .sc_audio_title {
	color: {$colors['text_link']};
}
.mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-current, .mejs-controls .mejs-time-rail .mejs-time-current {
	background: {$colors['text_link']} !important;
}
*/

.sc_audio {
	border-color: {$colors['text_hover']};
}

/* Modern style */
.sc_audio.sc_audio_info {
	border-color: {$colors['alter_bg_color']};
	background-color: {$colors['alter_bg_color']};
}
.sc_audio .sc_audio_author_name {
	color: {$colors['text_hover']};
}
.mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-current,
.mejs-controls .mejs-volume-button .mejs-volume-slider,
.mejs-controls .mejs-time-rail .mejs-time-current {
	background: {$colors['text_hover']} !important;
}
.mejs-container, .mejs-embed, .mejs-embed body, .mejs-container .mejs-controls {
	background-color: {$colors['bg_color']};
	border-color: {$colors['bd_color']};
}
.mejs-container .mejs-controls .mejs-time {
	color: {$colors['text_light']};
}
.mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-total:before,
.mejs-controls .mejs-time-rail .mejs-time-total:before {
	background-color: {$colors['bg_color']};
}
.mejs-controls .mejs-time-rail .mejs-time-loaded {}
.mejs-container .mejs-controls .mejs-fullscreen-button,
.mejs-container .mejs-controls .mejs-volume-button,
.mejs-container .mejs-controls .mejs-playpause-button {
	background: {$colors['text_link']} !important;
}



/* Button */
input[type="submit"],
input[type="reset"],
input[type="button"],
button,
.sc_button.sc_button_style_filled {
	background-color: {$colors['text_hover']};
	color: {$colors['inverse_text']};
}

input[type="submit"]:hover,
input[type="reset"]:hover,
input[type="button"]:hover,
button:hover,
.sc_button.sc_button_style_filled:hover {
	background-color: {$colors['text_link']};
	color: {$colors['inverse_text']};
}
.sc_button.sc_button_style_filled2 {
	background-color: {$colors['text_link']};
	color: {$colors['inverse_text']};
}
.sc_button.sc_button_style_filled2:hover {
	background-color: {$colors['text_hover']};
	color: {$colors['inverse_text']};
}

/* ================= BUTTON'S HOVERS ==================== */

/* Fade */
[class*="sc_button_hover_fade"]:after {
	border-color: {$colors['text_link']};
}
.sc_button_style_filled2[class*="sc_button_hover_fade"]:after {
	border-color: {$colors['text_hover']};
}

/* Slide */
/* This way via :after element
[class*="sc_button_hover_slide"]:after {
	background-color: {$colors['text_hover']};
	color: {$colors['inverse_text']};
}
*/
/* This way via gradient */
[class*="sc_button_hover_slide"] {
	color: {$colors['inverse_text']} !important;
	background-color: {$colors['text_link']};
}
[class*="sc_button_hover_slide"]:hover {
	background-color: {$colors['text_hover']} !important;
}
.sc_button_hover_slide_left {
    background: linear-gradient(to right, {$colors['text_hover']} 50%, {$colors['text_link']} 50%) repeat scroll right bottom / 210% 100% rgba(0, 0, 0, 0) !important;
}
.sc_button_hover_slide_top {
    background: linear-gradient(to bottom, {$colors['text_hover']} 50%, {$colors['text_link']} 50%) repeat scroll right bottom / 100% 210% rgba(0, 0, 0, 0) !important;
}

/* ================= END BUTTON'S HOVERS ==================== */



/* Blogger */
.sc_blogger.layout_date .sc_blogger_item .sc_blogger_date { 
	background-color: {$colors['text_link']};
	border-color: {$colors['text_link']};
	color: {$colors['inverse_text']};
}
.sc_blogger.layout_date .sc_blogger_item .sc_blogger_date .year:before {
	border-color: {$colors['inverse_text']};
}
.sc_blogger.layout_date .sc_blogger_item::before {
	background-color: {$colors['alter_bg_color']};
}
.sc_blogger_item.sc_plain_item {
	background-color: {$colors['alter_bg_color']};
}
.sc_blogger.layout_polaroid .photostack nav span.current {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_link']};
}
.sc_blogger.layout_polaroid .photostack nav span.current.flip {
	background-color: {$colors['text_hover']};
}

/* Call to Action */
.sc_call_to_action_accented {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_link']};
}
.sc_call_to_action_accented .sc_item_title,
.sc_call_to_action_accented .sc_item_subtitle,
.sc_call_to_action_accented .sc_item_descr {
	color: {$colors['inverse_text']};
}
.sc_call_to_action_accented .sc_item_button > a {
	color: {$colors['text_link']};
	background-color: {$colors['inverse_text']};
}
.sc_call_to_action_accented .sc_item_button > a:before {
	background-color: {$colors['text_link']};
	color: {$colors['inverse_text']};
}
.sc_call_to_action b {	
	color: {$colors['alter_link']};
}

/* Chat */
.sc_chat:after {
	background-color: {$colors['alter_bg_color']};
	border-color: {$colors['alter_bd_color']};
}
.sc_chat_inner {
	color: {$colors['alter_text']};
	background-color: {$colors['alter_bg_color']};
	border-color: {$colors['alter_bd_color']};
}
.sc_chat_inner a {
	color: {$colors['alter_link']};
}
.sc_chat_inner a:hover {
	color: {$colors['alter_hover']};
}

/* Clients */
.sc_clients_style_clients-2 .sc_client_image .sc_client_hover {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_dark_0_8']};
}
.sc_clients_style_clients-2 .sc_client_title,
.sc_clients_style_clients-2 .sc_client_title a {
	color: {$colors['inverse_text']};
}
.sc_clients_style_clients-2 .sc_client_title a:hover {
	color: {$colors['text_link']};
}
.sc_clients_style_clients-2 .sc_client_description:before,
.sc_clients_style_clients-2 .sc_client_position {
	color: {$colors['text_link']};
}

/* Contact form */
.sc_form .sc_form_item.sc_form_button button {}
.sc_form .sc_form_button button:hover {}
.sc_form .sc_form_address_label,
.sc_form .sc_form_item > label {
	color: {$colors['text_dark']};
}
.sc_form .sc_form_item .sc_form_element input[type="radio"] + label:before,
.sc_form .sc_form_item .sc_form_element input[type="checkbox"] + label:before {
	border-color: {$colors['input_bd_color']};
	background-color: {$colors['input_bg_color']};
}
.sc_form_select_container {
	background-color: {$colors['input_bg_color']};
}

/* picker */
.sc_form .picker {
	color: {$colors['input_text']};
	border-color: {$colors['input_bd_color']};
	background-color: {$colors['input_bg_color']};
}
.picker__month,
.picker__year {
	color: {$colors['input_dark']};
}
.sc_form .picker__nav--prev:before,
.sc_form .picker__nav--next:before {
	color: {$colors['input_text']};
}
.sc_form .picker__nav--prev:hover:before,
.sc_form .picker__nav--next:hover:before {
	color: {$colors['input_dark']};
}
.sc_form .picker__nav--disabled,
.sc_form .picker__nav--disabled:hover,
.sc_form .picker__nav--disabled:before,
.sc_form .picker__nav--disabled:before:hover {
	color: {$colors['input_light']};
}
.sc_form table.picker__table th {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_link']};
}
.sc_form .picker__day--infocus {
	color: {$colors['input_dark']};
}
.sc_form .picker__day--today,
.sc_form .picker__day--infocus:hover,
.sc_form .picker__day--outfocus:hover,
.sc_form .picker__day--highlighted:hover,
.sc_form .picker--focused .picker__day--highlighted {
	color: {$colors['input_dark']};
	background-color: {$colors['input_bg_hover']};
}
.sc_form .picker__day--disabled,
.sc_form .picker__day--disabled:hover {
	color: {$colors['input_light']};
}
.sc_form .picker__day--highlighted.picker__day--disabled,
.sc_form .picker__day--highlighted.picker__day--disabled:hover {
	color: {$colors['input_light']};
	background-color: {$colors['input_bg_hover']} !important;
}
.sc_form .picker__day--today:before,
.sc_form .picker__button--today:before,
.sc_form .picker__button--clear:before,
.sc_form button:focus {
	border-color: {$colors['text_link']};
}
.sc_form .picker__button--close:before {
	color: {$colors['text_link']};
}
.sc_form .picker--time .picker__button--clear:hover,
.sc_form .picker--time .picker__button--clear:focus {
	background-color: {$colors['text_hover']};
}
.sc_form .picker__footer {
	border-color: {$colors['input_bd_color']};
}
.sc_form .picker__button--today,
.sc_form .picker__button--clear,
.sc_form .picker__button--close {
	color: {$colors['input_text']};
}
.sc_form .picker__button--today:hover,
.sc_form .picker__button--clear:hover,
.sc_form .picker__button--close:hover {
	color: {$colors['input_dark']};
	background-color: {$colors['input_bg_hover']} !important;
}
.sc_form .picker__button--today[disabled],
.sc_form .picker__button--today[disabled]:hover {
	color: {$colors['input_light']};
	background-color: {$colors['input_bg_hover']};
	border-color: {$colors['input_bg_hover']};
}
.sc_form .picker__button--today[disabled]:before {
	border-top-color: {$colors['input_light']};
}

/* Time picker */
.sc_form .picker__list-item {
	color: {$colors['input_text']};
	border-color: {$colors['input_bd_color']};
}
.sc_form .picker__list-item:hover,
.sc_form .picker__list-item--highlighted,
.sc_form .picker__list-item--highlighted:hover,
.sc_form .picker--focused .picker__list-item--highlighted,
.sc_form .picker__list-item--selected,
.sc_form .picker__list-item--selected:hover,
.sc_form .picker--focused .picker__list-item--selected {
	color: {$colors['input_dark']};
	background-color: {$colors['input_bg_hover']};
	border-color: {$colors['input_bd_hover']};
}
.sc_form .picker__list-item--disabled,
.sc_form .picker__list-item--disabled:hover,
.sc_form .picker--focused .picker__list-item--disabled {
	color: {$colors['input_light']};
	background-color: {$colors['input_bg_color']};
	border-color: {$colors['input_bd_color']};
}


/* Countdown Style 1 */
.sc_countdown.sc_countdown_style_1 .sc_countdown_digits,
.sc_countdown.sc_countdown_style_1 .sc_countdown_separator {
	color: {$colors['text_link']};
}
.sc_countdown.sc_countdown_style_1 .sc_countdown_digits {
	border-color: {$colors['alter_bd_color']};
	background-color: {$colors['alter_bg_color']};
}
.sc_countdown.sc_countdown_style_1 .sc_countdown_label {
	color: {$colors['text_link']};
}

/* Countdown Style 2 */
.sc_countdown.sc_countdown_style_2 .sc_countdown_separator {
	color: {$colors['text_link']};
}
.sc_countdown.sc_countdown_style_2 .sc_countdown_digits span {
	background-color: {$colors['text_link']};
}
.sc_countdown.sc_countdown_style_2 .sc_countdown_label {
	color: {$colors['text_link']};
}

/* Dropcaps */
.sc_dropcaps .sc_dropcaps_item {
	color: {$colors['text_hover']};
}
.sc_dropcaps.sc_dropcaps_style_2 .sc_dropcaps_item {
	color: {$colors['text_link']};
}


/* Events */
.sc_events_item .sc_events_item_readmore {
	color: {$colors['text_dark']};
}
.sc_events_item .sc_events_item_readmore span {
	color: {$colors['text_link']};
}
.sc_events_item .sc_events_item_readmore:hover,
.sc_events_item .sc_events_item_readmore:hover span {
	color: {$colors['text_hover']};
}
.sc_events_style_events-1 .sc_events_item {
	background-color: {$colors['bg_color']};
	color: {$colors['text']};
}
.sc_events_style_events-2 .sc_events_item {
	border-color: {$colors['bd_color']};
}
.sc_events_style_events-2 .sc_events_item_date {
	background-color: {$colors['text_link']};
	color: {$colors['inverse_text']};
}
.sc_events_style_events-2 .sc_events_item_time:before,
.sc_events_style_events-2 .sc_events_item_details:before {
	background-color: {$colors['bd_color']};
}

/* Google map */
.sc_googlemap_content {
	background-color: {$colors['bg_color']};
}

/* Highlight */
.sc_highlight_style_1 {
	background-color: {$colors['text_link']};
	color: {$colors['inverse_text']};
}
.sc_highlight_style_2 {
	background-color: {$colors['text_hover']};
	color: {$colors['inverse_text']};
}
.sc_highlight_style_3 {
	background-color: {$colors['alter_bg_color']};
	color: {$colors['alter_text']};
}

/* Icon */
.sc_icon_hover:hover,
a:hover .sc_icon_hover {
	color: {$colors['inverse_text']} !important;
	background-color: {$colors['text_link']} !important; 
}

.sc_icon_shape_round.sc_icon,
.sc_icon_shape_square.sc_icon {	
	background-color: {$colors['text_link']};
	border-color: {$colors['text_link']};
	color: {$colors['inverse_text']};
}

.sc_icon_shape_round.sc_icon:hover,
.sc_icon_shape_square.sc_icon:hover,
a:hover .sc_icon_shape_round.sc_icon,
a:hover .sc_icon_shape_square.sc_icon {	
	color: {$colors['text_link']};
	background-color: {$colors['bg_color']};
}


/* Image */
figure figcaption,
.sc_image figcaption {
	background-color: {$colors['text_link_0_8']};
}


/* Infobox */
.sc_infobox.sc_infobox_style_regular {
	background-color: {$colors['text_link']};
}

/* Intro */
.sc_intro_inner .sc_intro_subtitle {
	color: {$colors['text_dark']};
} 
.sc_intro_inner .sc_intro_title {
	color: {$colors['text_dark']};
}
.sc_intro_inner .sc_intro_descr,
.sc_intro_inner .sc_intro_icon {
	color: {$colors['text_dark']};
}
.sc_intro_inner.sc_intro_style_2 .sc_intro_subtitle {
	color: {$colors['inverse_text']};
} 
.sc_intro_inner.sc_intro_style_2 .sc_intro_title {
	color: {$colors['inverse_text']};
}
.sc_intro_inner.sc_intro_style_2 .sc_intro_descr,
.sc_intro_inner.sc_intro_style_2 .sc_intro_icon {
	color: {$colors['inverse_text']};
}
.sc_intro.extra_border {
	border-color: {$colors['text_hover']};
}
.sc_intro_style_4 .sc_intro_descr {
	color: {$colors['text_light']};
}



/* List */
.sc_list_style_iconed li:before,
.sc_list_style_iconed .sc_list_icon {
	color: {$colors['text_link']};
}
.sc_list_style_iconed li .sc_list_title {
	color: {$colors['text_dark']};
}
.sc_list_style_iconed li a:hover .sc_list_title {
	color: {$colors['text_hover']};
}


/* Line */
.sc_line {
	border-color: {$colors['bd_color']};
}
.sc_line .sc_line_title {
	color: {$colors['text_dark']};
	background-color: {$colors['bg_color']};
}




/* Popup */
.sc_popup:before {
	background-color: {$colors['text_link']};
}


/* Price */
.sc_price .sc_price_currency, .sc_price .sc_price_money, .sc_price .sc_price_penny {
	color: {$colors['inverse_text']};
}
.sc_price .sc_price_info {
	color: {$colors['text']};
}

/* Price block */
.sc_price_block.sc_price_block_style_1 {
	border-color: {$colors['text_link']};
}
.sc_price_block .sc_price_block_title {
	color: {$colors['text_dark']};
}



/* Promo */
.sc_promo_image,
.sc_promo_block {
	background-color: {$colors['alter_bg_hover']};
}
.sc_promo_title {
	color: {$colors['alter_dark']};
}
.sc_promo_descr {
	color: {$colors['alter_text']};
}

/* Organic Beauty - Recent News */
.sc_recent_news_header {
	border-color: {$colors['text_dark']};
}
.sc_recent_news_header_category_item_more {
	color: {$colors['text_link']};
}
.sc_recent_news_header_more_categories {
	border-color: {$colors['alter_bd_color']};
	background-color: {$colors['alter_bg_color']};
}
.sc_recent_news_header_more_categories > a {
	color: {$colors['alter_link']};
}
.sc_recent_news_header_more_categories > a:hover {
	color: {$colors['alter_hover']};
	background-color: {$colors['alter_bg_hover']};
}
.sc_recent_news .post_counters_item,
.sc_recent_news .post_counters .post_edit a {
	background-color: {$colors['alter_bg_color']};
}
.sidebar .sc_recent_news .post_counters_item,
.sidebar .sc_recent_news .post_counters .post_edit a {
	background-color: {$colors['bg_color']};
}
.sc_recent_news .post_counters .post_edit a {
	color: {$colors['alter_dark']};
}
.sc_recent_news_style_news-magazine .post_accented_border {
	border-color: {$colors['bd_color']};
}
.sc_recent_news_style_news-excerpt .post_item {
	border-color: {$colors['bd_color']};
}


/* Section */
.sc_section_inner {
	color: {$colors['text']};
}


/* Services */
.sc_services_item .sc_services_item_title a,
.sc_services_item .sc_services_item_title {	
	color: {$colors['text_light']};
}
.sc_services_item .sc_services_item_title a:hover {
	color: {$colors['text_link']};
}
.sc_services_item .sc_services_item_readmore {
	background-color: {$colors['alter_link']};
}


/* Scroll controls */
.sc_scroll_controls_wrap a {
	color: {$colors['alter_link']};
}
.sc_scroll_controls_type_side .sc_scroll_controls_wrap a {
	background-color: {$colors['text_link_0_8']};
}
.sc_scroll_controls_wrap a:hover {
	color: {$colors['text_link']};
}
.sc_scroll_bar .swiper-scrollbar-drag:before {
	background-color: {$colors['text_link']};
}
.sc_scroll .sc_scroll_bar {
	border-color: {$colors['alter_bg_color']};
}

/* Skills */
.sc_skills_bar.sc_skills_horizontal .sc_skills_total {
	color: {$colors['text_light']};
}
.sc_skills_bar .sc_skills_info .sc_skills_label {
	color: {$colors['text_link']};
}
.sc_skills_bar .sc_skills_item {
	background-color: {$colors['alter_bg_color']};
}
.sc_skills_counter .sc_skills_item .sc_skills_icon {
	color: {$colors['text_link']};
}
.sc_skills_counter .sc_skills_item:hover .sc_skills_icon {
	color: {$colors['text_hover']};
}
.sc_skills_counter .sc_skills_item .sc_skills_info {
	color: {$colors['text_dark']};
}
.sc_skills_bar .sc_skills_item .sc_skills_count {
	border-color: {$colors['text_link']};
}


.sc_skills_pie.sc_skills_compact_off .sc_skills_info,
.sc_skills_pie.sc_skills_compact_off .sc_skills_total {
	color: {$colors['text_dark']};
}


.sc_skills_legend_title, .sc_skills_legend_value {
	color: {$colors['text_dark']};
}

.sc_skills_counter .sc_skills_item.sc_skills_style_2 .sc_skills_count .sc_skills_total {
	color: {$colors['text_hover']};
}
.sc_skills_counter .sc_skills_item.sc_skills_style_2 .sc_skills_info {
	color: {$colors['text_dark']};
}

.sc_skills_counter .sc_skills_item.sc_skills_style_1 {
	background-color: {$colors['alter_bg_color']};
}
.sc_skills_counter .sc_skills_item.sc_skills_style_1:hover {
	background-color: {$colors['alter_bg_hover']};
}
.sc_skills_counter .sc_skills_item.sc_skills_style_1 .sc_skills_count,
.sc_skills_counter .sc_skills_item.sc_skills_style_1 .sc_skills_info {
	color: {$colors['alter_dark']};
}
.sc_skills_counter .sc_skills_item.sc_skills_style_1 .sc_skills_info:before {
	background-color: {$colors['alter_bd_color']};
}
.sc_skills_bar .sc_skills_item .sc_skills_count,
.sc_skills_counter .sc_skills_item.sc_skills_style_3 .sc_skills_count,
.sc_skills_counter .sc_skills_item.sc_skills_style_4 .sc_skills_count,
.sc_skills_counter .sc_skills_item.sc_skills_style_4 .sc_skills_info {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_link']};
}

/* Slider */
.sc_slider_controls_wrap a {
	color: {$colors['text_hover']};
	border-color: {$colors['bd_color']};
	background-color: {$colors['bg_color']};
}
.sc_slider_controls_wrap a:hover {
	color: {$colors['inverse_text']};
	border-color: {$colors['text_link']};
	background-color: {$colors['text_link']};
}
.sc_slider_swiper .sc_slider_pagination_wrap .swiper-pagination-bullet-active,
.sc_slider_swiper .sc_slider_pagination_wrap span:hover {
	background-color: {$colors['text_link']};
}
.sc_slider_swiper .sc_slider_info {
	background-color: {$colors['text_link_0_8']} !important;
}
.sc_slider_pagination.widget_area .post_item + .post_item {
	border-color: {$colors['bd_color']};
}
.sc_slider_pagination_over .sc_slider_pagination {
	background-color: {$colors['alter_bg_color_0_8']};
}
.sc_slider_pagination_over .sc_slider_pagination_wrap span {
	border-color: {$colors['bd_color']};
}
.sc_slider_pagination_over .sc_slider_pagination_wrap span:hover,
.sc_slider_pagination_over .sc_slider_pagination_wrap .swiper-pagination-bullet-active {
	border-color: {$colors['text_link']};
	background-color: {$colors['text_link']};
}
.sc_slider_pagination_over .sc_slider_pagination .post_title {
	color: {$colors['alter_dark']};
}
.sc_slider_pagination_over .sc_slider_pagination .post_info {
	color: {$colors['alter_text']};
}
.sc_slider_pagination_area .sc_slider_pagination .post_item.active {
	background-color: {$colors['alter_bg_color']} !important;
}

/* Socials */
.sc_socials.sc_socials_type_icons a {
	color: {$colors['alter_light']};
	border-color: {$colors['alter_light']};
}
.sc_socials.sc_socials_type_icons a:hover {
	color: {$colors['text_hover']};
	border-color: {$colors['text_hover']};
}
.sc_socials.sc_socials_share.sc_socials_dir_vertical .sc_socials_item a {
	background-color: {$colors['alter_bg_color']};
}

/* Tabs */
.sc_tabs.sc_tabs_style_1 .sc_tabs_titles li a {
	color: {$colors['text_dark']};
	border-color: {$colors['bd_color']};
}
.sc_tabs.sc_tabs_style_1 .sc_tabs_titles li.ui-state-active a,
.sc_tabs.sc_tabs_style_1 .sc_tabs_titles li.sc_tabs_active a,
.sc_tabs.sc_tabs_style_1 .sc_tabs_titles li a:hover {
	color: {$colors['text_link']};
}
.sc_tabs.sc_tabs_style_1 .sc_tabs_titles li.ui-state-active a:after,
.sc_tabs.sc_tabs_style_1 .sc_tabs_titles li.sc_tabs_active a:after {
	background-color: {$colors['text_link']};
}
.sc_tabs.sc_tabs_style_1 .sc_tabs_content,
.sc_tabs.sc_tabs_style_2 .sc_tabs_content {
	border-color: {$colors['bd_color']};
}
.sc_tabs.sc_tabs_style_2 .sc_tabs_titles li a {
	border-color: {$colors['text_link']};
	background-color: {$colors['text_link']};
	color: {$colors['inverse_text']};
}
.sc_tabs.sc_tabs_style_2 .sc_tabs_titles li a:hover,
.sc_tabs.sc_tabs_style_2 .sc_tabs_titles li.ui-state-active a,
.sc_tabs.sc_tabs_style_2 .sc_tabs_titles li.sc_tabs_active a {
	color: {$colors['text_link']};
}


/* Team */
.sc_team_item .sc_team_item_info .sc_team_item_title a {
	color: {$colors['text_dark']};
}
.sc_team_item .sc_team_item_info .sc_team_item_title a:hover {
	color: {$colors['text_hover']};
}
.sc_team_item .sc_team_item_info .sc_team_item_position {
	color: {$colors['text_link']};
}
.sc_team_style_team-1 .sc_team_item_info,
.sc_team_style_team-3 .sc_team_item_info {
	border-color: {$colors['text_link']};
	color: {$colors['text']};
}
.sc_team.sc_team_style_team-3 .sc_socials_item a {
	color: {$colors['inverse_link']};
	border-color: {$colors['inverse_link']};
}
.sc_team.sc_team_style_team-3 .sc_socials_item a:hover {
	color: {$colors['inverse_hover']};
	border-color: {$colors['inverse_hover']};
}
.sc_team.sc_team_style_team-3 .sc_team_item_avatar .sc_team_item_hover {
	background-color: {$colors['text_link_0_8']};
}
.sc_team.sc_team_style_team-4 .sc_socials_item a {
	color: {$colors['inverse_link']};
	border-color: {$colors['inverse_link']};
}
.sc_team.sc_team_style_team-4 .sc_socials_item a:hover {
	color: {$colors['text_link']};
	border-color: {$colors['text_link']};
}
.sc_team.sc_team_style_team-4 .sc_team_item_avatar .sc_team_item_hover {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_dark_0_8']};
}
.sc_team_style_team-4 .sc_team_item_info .sc_team_item_title a {
	color: {$colors['inverse_text']};
}
.sc_team_style_team-4 .sc_team_item_info .sc_team_item_title a:hover {
	color: {$colors['text_link']};
}
.sc_team_style_team-4 .sc_team_item_info .sc_team_item_position {
	color: {$colors['inverse_text']};
}


/* Testimonials */
.sc_testimonials {
	color: {$colors['text']};
}
.sc_testimonial_author_name {
	color: {$colors['text_dark']};
}
.sc_testimonial_position_position {
	color: {$colors['text_light']};
}
.sc_testimonials_style_testimonials-2 .sc_testimonial_author_name:before {
	color: {$colors['text_light']};
}
.sc_testimonials_style_testimonials-3 .sc_testimonial_content,
.sc_testimonials_style_testimonials-3 .sc_testimonial_content:after {
	background-color: {$colors['bg_color']};
}
.sc_testimonials_style_testimonials-3 .sc_testimonial_content p:first-child:before,
.sc_testimonials_style_testimonials-3 .sc_testimonial_author_position {
	color: {$colors['text_link']};
}
.sc_testimonials_style_testimonials-4 .sc_testimonial_item {
	background-color: {$colors['bg_color']};
}
.sc_testimonials_style_testimonials-4 .sc_testimonial_content p:first-child:before,
.sc_testimonials_style_testimonials-4 .sc_testimonial_author_position {
	color: {$colors['text_link']};
}

/* Title */
.sc_title_icon {
	color: {$colors['text_link']};
}
.sc_title_underline::after {
	border-color: {$colors['text_link']};
}
.sc_title_divider .sc_title_divider_before,
.sc_title_divider .sc_title_divider_after {
	background-color: {$colors['text_dark']};
}

/* Toggles */
.sc_toggles .sc_toggles_item .sc_toggles_title {
	border-color: {$colors['bd_color']};
}
.sc_toggles .sc_toggles_item .sc_toggles_title .sc_toggles_icon {
	color: {$colors['alter_light']};
	background-color: {$colors['alter_bg_color']};
}
.sc_toggles .sc_toggles_item .sc_toggles_title.ui-state-active {
	color: {$colors['text_link']};
	border-color: {$colors['text_link']};
}
.sc_toggles .sc_toggles_item .sc_toggles_title.ui-state-active .sc_toggles_icon_opened {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_link']};
}
.sc_toggles .sc_toggles_item .sc_toggles_title:hover {
	color: {$colors['text_hover']};
	border-color: {$colors['text_hover']};
}
.sc_toggles .sc_toggles_item .sc_toggles_title:hover .sc_toggles_icon_opened {
	background-color: {$colors['text_hover']};
}
.sc_toggles .sc_toggles_item .sc_toggles_content {
	border-color: {$colors['bd_color']};
}


/* Tooltip */
.sc_tooltip_parent .sc_tooltip,
.sc_tooltip_parent .sc_tooltip:before {
	background-color: {$colors['text_link']};
}


/* Twitter */
.sc_twitter {
	color: {$colors['text']};
}
.sc_twitter .sc_slider_controls_wrap a {
	color: {$colors['inverse_text']};
}

/* Common styles (title, subtitle and description for some shortcodes) */
.sc_item_subtitle {
	color: {$colors['text_link']};
}
.sc_item_title:after {
	background-color: {$colors['text_link']};
}


.cq-testimoniallist.beauty .cq-testimoniallist-name {
	color: {$colors['text_link']};
}




.minimal-light .esg-filterbutton,
.minimal-light .esg-sortbutton, 
.minimal-light .esg-cartbutton a {
	{$fonts['p_font-family']}	
	color: {$colors['text_light']};
	background-color: {$colors['alter_bg_color']};
}
.minimal-light .esg-navigationbutton {
	color: {$colors['alter_link']};
}
.minimal-light .esg-navigationbutton:hover {
	color: {$colors['text_link']};
}
.minimal-light .esg-filterbutton:hover,
.minimal-light .esg-sortbutton:hover,
.minimal-light .esg-sortbutton-order:hover,
.minimal-light .esg-cartbutton a:hover,
.minimal-light .esg-filterbutton.selected {
	color: {$colors['text_link']};
	background-color: {$colors['alter_bg_color']};
}

.minimal-light .esg-cartbutton a {
	color: {$colors['text_link']};
}
.minimal-light .esg-cartbutton a:hover {
	color: {$colors['text_hover']};
}


CSS;
					
					$rez = apply_filters('organic_beauty_filter_get_css', $rez, $colors, $fonts, $scheme);
					
					$css['colors'] .= $rez['colors'];
					if ($step == 1) $css['fonts'] = $rez['fonts'];
					$step++;
				}
			}
		} else
			$css['fonts'] = $rez['fonts'];
		
		$css_str = (!empty($css['fonts']) ? $css['fonts'] : '')
					. (!empty($css['colors']) ? $css['colors'] : '');
		
		if (!empty($css_str))
			$css_str = $add_comment . ($minify ? organic_beauty_minify_css($css_str) : $css_str);

		return $css_str;
	}
}
?>