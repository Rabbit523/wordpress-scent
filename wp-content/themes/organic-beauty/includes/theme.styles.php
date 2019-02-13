<?php
/**
 * Theme custom styles
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if (!function_exists('organic_beauty_action_theme_styles_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_action_theme_styles_theme_setup', 1 );
	function organic_beauty_action_theme_styles_theme_setup() {
	
		// Add theme fonts in the used fonts list
		add_filter('organic_beauty_filter_used_fonts',			'organic_beauty_filter_theme_styles_used_fonts');
		// Add theme fonts (from Google fonts) in the main fonts list (if not present).
		add_filter('organic_beauty_filter_list_fonts',			'organic_beauty_filter_theme_styles_list_fonts');

		// Add theme stylesheets
		add_action('organic_beauty_action_add_styles',			'organic_beauty_action_theme_styles_add_styles');
		// Add theme inline styles
		add_filter('organic_beauty_filter_add_styles_inline',		'organic_beauty_filter_theme_styles_add_styles_inline');

		// Add theme scripts
		add_action('organic_beauty_action_add_scripts',			'organic_beauty_action_theme_styles_add_scripts');
		// Add theme scripts inline
		add_filter('organic_beauty_filter_localize_script',		'organic_beauty_filter_theme_styles_localize_script');

		// Add theme less files into list for compilation
		add_filter('organic_beauty_filter_compile_less',			'organic_beauty_filter_theme_styles_compile_less');


		/* Color schemes
		
		// Block's border and background
		bd_color		- border for the entire block
		bg_color		- background color for the entire block
		// Next settings are deprecated
		//bg_image, bg_image_position, bg_image_repeat, bg_image_attachment  - first background image for the entire block
		//bg_image2,bg_image2_position,bg_image2_repeat,bg_image2_attachment - second background image for the entire block
		
		// Additional accented colors (if need)
		accent2			- theme accented color 2
		accent2_hover	- theme accented color 2 (hover state)		
		accent3			- theme accented color 3
		accent3_hover	- theme accented color 3 (hover state)		
		
		// Headers, text and links
		text			- main content
		text_light		- post info
		text_dark		- headers
		text_link		- links
		text_hover		- hover links
		
		// Inverse blocks
		inverse_text	- text on accented background
		inverse_light	- post info on accented background
		inverse_dark	- headers on accented background
		inverse_link	- links on accented background
		inverse_hover	- hovered links on accented background
		
		// Input colors - form fields
		input_text		- inactive text
		input_light		- placeholder text
		input_dark		- focused text
		input_bd_color	- inactive border
		input_bd_hover	- focused borde
		input_bg_color	- inactive background
		input_bg_hover	- focused background
		
		// Alternative colors - highlight blocks, form fields, etc.
		alter_text		- text on alternative background
		alter_light		- post info on alternative background
		alter_dark		- headers on alternative background
		alter_link		- links on alternative background
		alter_hover		- hovered links on alternative background
		alter_bd_color	- alternative border
		alter_bd_hover	- alternative border for hovered state or active field
		alter_bg_color	- alternative background
		alter_bg_hover	- alternative background for hovered state or active field 
		// Next settings are deprecated
		//alter_bg_image, alter_bg_image_position, alter_bg_image_repeat, alter_bg_image_attachment - background image for the alternative block
		
		*/

		// Add color schemes
		organic_beauty_add_color_scheme('original', array(

			'title'					=> esc_html__('Original', 'organic-beauty'),
			
			// Whole block border and background
			'bd_color'				=> '#ebedf0',
			'bg_color'				=> '#ffffff',
			
			// Headers, text and links colors
			'text'					=> '#777777',
			'text_light'			=> '#adadad',
			'text_dark'				=> '#3f2803',
			'text_link'				=> '#3f2803',
			'text_hover'			=> '#81be17',

			// Inverse colors
			'inverse_text'			=> '#ffffff',
			'inverse_light'			=> '#ffffff',
			'inverse_dark'			=> '#ffffff',
			'inverse_link'			=> '#ffffff',
			'inverse_hover'			=> '#ffffff',
		
			// Input fields
			'input_text'			=> '#777777',
			'input_light'			=> '#acb4b6',
			'input_dark'			=> '#232a34',
			'input_bd_color'		=> '#3f2803',
			'input_bd_hover'		=> '#a0d445',
			'input_bg_color'		=> '#ffffff',
			'input_bg_hover'		=> '#ffffff',
		
			// Alternative blocks (submenu items, etc.)
			'alter_text'			=> '#8a8a8a',
			'alter_light'			=> '#c8c9cc',
			'alter_dark'			=> '#232a34',
			'alter_link'			=> '#a0d445',
			'alter_hover'			=> '#b8e866',
			'alter_bd_color'		=> '#dddddd',
			'alter_bd_hover'		=> '#bbbbbb',
			'alter_bg_color'		=> '#f2f5f8',
			'alter_bg_hover'		=> '#f0f0f0',
			)
		);

		// Add color schemes
		organic_beauty_add_color_scheme('light', array(

			'title'					=> esc_html__('Light', 'organic-beauty'),

			// Whole block border and background
			'bd_color'				=> '#dddddd',
			'bg_color'				=> '#f7f7f7',
		
			// Headers, text and links colors
			'text'					=> '#8a8a8a',
			'text_light'			=> '#acb4b6',
			'text_dark'				=> '#232a34',
			'text_link'				=> '#3f2803',
			'text_hover'			=> '#81be17',

			// Inverse colors
			'inverse_text'			=> '#ffffff',
			'inverse_light'			=> '#ffffff',
			'inverse_dark'			=> '#ffffff',
			'inverse_link'			=> '#ffffff',
			'inverse_hover'			=> '#ffffff',
		
			// Input fields
			'input_text'			=> '#8a8a8a',
			'input_light'			=> '#acb4b6',
			'input_dark'			=> '#232a34',
			'input_bd_color'		=> '#e7e7e7',
			'input_bd_hover'		=> '#dddddd',
			'input_bg_color'		=> '#ffffff',
			'input_bg_hover'		=> '#f0f0f0',
		
			// Alternative blocks (submenu items, etc.)
			'alter_text'			=> '#8a8a8a',
			'alter_light'			=> '#acb4b6',
			'alter_dark'			=> '#232a34',
			'alter_link'			=> '#20c7ca',
			'alter_hover'			=> '#b8e866',
			'alter_bd_color'		=> '#e7e7e7',
			'alter_bd_hover'		=> '#dddddd',
			'alter_bg_color'		=> '#ffffff',
			'alter_bg_hover'		=> '#f0f0f0',
			)
		);

		// Add color schemes
		organic_beauty_add_color_scheme('dark', array(

			'title'					=> esc_html__('Dark', 'organic-beauty'),
			
			// Whole block border and background
			'bd_color'				=> '#7d7d7d',
			'bg_color'				=> '#333333',

			// Headers, text and links colors
			'text'					=> '#909090',
			'text_light'			=> '#a0a0a0',
			'text_dark'				=> '#e0e0e0',
			'text_link'				=> '#20c7ca',
			'text_hover'			=> '#189799',

			// Inverse colors
			'inverse_text'			=> '#f0f0f0',
			'inverse_light'			=> '#e0e0e0',
			'inverse_dark'			=> '#ffffff',
			'inverse_link'			=> '#ffffff',
			'inverse_hover'			=> '#e5e5e5',
		
			// Input fields
			'input_text'			=> '#999999',
			'input_light'			=> '#aaaaaa',
			'input_dark'			=> '#d0d0d0',
			'input_bd_color'		=> '#909090',
			'input_bd_hover'		=> '#888888',
			'input_bg_color'		=> '#666666',
			'input_bg_hover'		=> '#505050',
		
			// Alternative blocks (submenu items, etc.)
			'alter_text'			=> '#999999',
			'alter_light'			=> '#aaaaaa',
			'alter_dark'			=> '#d0d0d0',
			'alter_link'			=> '#20c7ca',
			'alter_hover'			=> '#29fbff',
			'alter_bd_color'		=> '#909090',
			'alter_bd_hover'		=> '#888888',
			'alter_bg_color'		=> '#666666',
			'alter_bg_hover'		=> '#505050',
			)
		);


		/* Font slugs:
		h1 ... h6	- headers
		p			- plain text
		link		- links
		info		- info blocks (Posted 15 May, 2015 by John Doe)
		menu		- main menu
		submenu		- dropdown menus
		logo		- logo text
		button		- button's caption
		input		- input fields
		*/

		// Add Custom fonts
		organic_beauty_add_custom_font('h1', array(
			'title'			=> esc_html__('Heading 1', 'organic-beauty'),
			'description'	=> '',
			'font-family'	=> 'Open Sans Condensed',
			'font-size' 	=> '2.143em',
			'font-weight'	=> '700',
			'font-style'	=> '',
			'line-height'	=> '1.32em',
			'margin-top'	=> '2em',
			'margin-bottom'	=> '1.1em'
			)
		);
		organic_beauty_add_custom_font('h2', array(
			'title'			=> esc_html__('Heading 2', 'organic-beauty'),
			'description'	=> '',
			'font-family'	=> 'Open Sans Condensed',
			'font-size' 	=> '1.714em',
			'font-weight'	=> '700',
			'font-style'	=> '',
			'line-height'	=> '1.5em',
			'margin-top'	=> '2.1em',
			'margin-bottom'	=> '1.35em'
			)
		);
		organic_beauty_add_custom_font('h3', array(
			'title'			=> esc_html__('Heading 3', 'organic-beauty'),
			'description'	=> '',
			'font-family'	=> 'Open Sans',
			'font-size' 	=> '1.571em',
			'font-weight'	=> '600',
			'font-style'	=> '',
			'line-height'	=> '1.45em',
			'margin-top'	=> '2.25em',
			'margin-bottom'	=> '0.85em'
			)
		);
		organic_beauty_add_custom_font('h4', array(
			'title'			=> esc_html__('Heading 4', 'organic-beauty'),
			'description'	=> '',
			'font-family'	=> 'Open Sans',
			'font-size' 	=> '1.286em',
			'font-weight'	=> '600',
			'font-style'	=> '',
			'line-height'	=> '1.35em',
			'margin-top'	=> '2em',
			'margin-bottom'	=> '0.9em'
			)
		);
		organic_beauty_add_custom_font('h5', array(
			'title'			=> esc_html__('Heading 5', 'organic-beauty'),
			'description'	=> '',
			'font-family'	=> 'Open Sans Condensed',
			'font-size' 	=> '1.143em',
			'font-weight'	=> '700',
			'font-style'	=> '',
			'line-height'	=> '1.5em',
			'margin-top'	=> '2.3em',
			'margin-bottom'	=> '1em'
			)
		);
		organic_beauty_add_custom_font('h6', array(
			'title'			=> esc_html__('Heading 6', 'organic-beauty'),
			'description'	=> '',
			'font-family'	=> 'Open Sans',
			'font-size' 	=> '1em',
			'font-weight'	=> '400',
			'font-style'	=> '',
			'line-height'	=> '1.4em',
			'margin-top'	=> '2.8em',
			'margin-bottom'	=> '0.75em'
			)
		);
		organic_beauty_add_custom_font('p', array(
			'title'			=> esc_html__('Text', 'organic-beauty'),
			'description'	=> '',
			'font-family'	=> 'Open Sans',
			'font-size' 	=> '14px',
			'font-weight'	=> '400',
			'font-style'	=> '',
			'line-height'	=> '1.85em'
			)
		);
		organic_beauty_add_custom_font('link', array(
			'title'			=> esc_html__('Links', 'organic-beauty'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '',
			'font-weight'	=> '',
			'font-style'	=> ''
			)
		);
		organic_beauty_add_custom_font('info', array(
			'title'			=> esc_html__('Post info', 'organic-beauty'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '0.857em',
			'font-weight'	=> '600',
			'font-style'	=> '',
			'line-height'	=> '1.2857em',
			'margin-top'	=> '',
			'margin-bottom'	=> '2.8em'
			)
		);
		organic_beauty_add_custom_font('menu', array(
			'title'			=> esc_html__('Main menu items', 'organic-beauty'),
			'description'	=> '',
			'font-family'	=> 'Open Sans Condensed',
			'font-size' 	=> '0.929em',
			'font-weight'	=> '700',
			'font-style'	=> '',
			'line-height'	=> '1.2857em'
			)
		);
		organic_beauty_add_custom_font('submenu', array(
			'title'			=> esc_html__('Dropdown menu items', 'organic-beauty'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '0.929em',
			'font-weight'	=> '',
			'font-style'	=> '',
			'line-height'	=> '1.2857em'
			)
		);
		organic_beauty_add_custom_font('logo', array(
			'title'			=> esc_html__('Logo', 'organic-beauty'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '2em',
			'font-weight'	=> '700',
			'font-style'	=> '',
			'line-height'	=> '1.15em',
			)
		);
		organic_beauty_add_custom_font('button', array(
			'title'			=> esc_html__('Buttons', 'organic-beauty'),
			'description'	=> '',
			'font-family'	=> 'Open Sans Condensed',
			'font-size' 	=> '1.143em',
			'font-weight'	=> '700',
			'font-style'	=> '',
			'line-height'	=> '1.2857em'
			)
		);
		organic_beauty_add_custom_font('input', array(
			'title'			=> esc_html__('Input fields', 'organic-beauty'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '',
			'font-weight'	=> '',
			'font-style'	=> '',
			'line-height'	=> '1.2857em'
			)
		);
		organic_beauty_add_custom_font('other', array(
				'title'			=> esc_html__('Other', 'organic-beauty'),
				'description'	=> '',
				'font-family'	=> 'Old Standard TT'
			)
		);

	}
}





//------------------------------------------------------------------------------
// Theme fonts
//------------------------------------------------------------------------------

// Add theme fonts in the used fonts list
if (!function_exists('organic_beauty_filter_theme_styles_used_fonts')) {
	//add_filter('organic_beauty_filter_used_fonts', 'organic_beauty_filter_theme_styles_used_fonts');
	function organic_beauty_filter_theme_styles_used_fonts($theme_fonts) {
		$theme_fonts['Open Sans Condensed'] = 1;
		return $theme_fonts;
	}
}

// Add theme fonts (from Google fonts) in the main fonts list (if not present).
// To use custom font-face you not need add it into list in this function
// How to install custom @font-face fonts into the theme?
// All @font-face fonts are located in "theme_name/css/font-face/" folder in the separate subfolders for the each font. Subfolder name is a font-family name!
// Place full set of the font files (for each font style and weight) and css-file named stylesheet.css in the each subfolder.
// Create your @font-face kit by using Fontsquirrel @font-face Generator (http://www.fontsquirrel.com/fontface/generator)
// and then extract the font kit (with folder in the kit) into the "theme_name/css/font-face" folder to install
if (!function_exists('organic_beauty_filter_theme_styles_list_fonts')) {
	//add_filter('organic_beauty_filter_list_fonts', 'organic_beauty_filter_theme_styles_list_fonts');
	function organic_beauty_filter_theme_styles_list_fonts($list) {
		// Example:
		// if (!isset($list['Advent Pro'])) {
		//		$list['Advent Pro'] = array(
		//			'family' => 'sans-serif',																						// (required) font family
		//			'link'   => 'Advent+Pro:100,100italic,300,300italic,400,400italic,500,500italic,700,700italic,900,900italic',	// (optional) if you use Google font repository
		//			'css'    => organic_beauty_get_file_url('/css/font-face/Advent-Pro/stylesheet.css')									// (optional) if you use custom font-face
		//			);
		// }
		if (!isset($list['Open Sans Condensed']))	$list['Open Sans Condensed'] = array('family'=>'sans-serif');
		return $list;
	}
}



//------------------------------------------------------------------------------
// Theme stylesheets
//------------------------------------------------------------------------------

// Add theme.less into list files for compilation
if (!function_exists('organic_beauty_filter_theme_styles_compile_less')) {
	//add_filter('organic_beauty_filter_compile_less', 'organic_beauty_filter_theme_styles_compile_less');
	function organic_beauty_filter_theme_styles_compile_less($files) {
		if (file_exists(organic_beauty_get_file_dir('css/theme.less'))) {
		 	$files[] = organic_beauty_get_file_dir('css/theme.less');
		}
		return $files;	
	}
}

// Add theme stylesheets
if (!function_exists('organic_beauty_action_theme_styles_add_styles')) {
	//add_action('organic_beauty_action_add_styles', 'organic_beauty_action_theme_styles_add_styles');
	function organic_beauty_action_theme_styles_add_styles() {
		// Add stylesheet files only if LESS supported
		if ( organic_beauty_get_theme_setting('less_compiler') != 'no' ) {
			organic_beauty_enqueue_style( 'organic_beauty-theme-style', organic_beauty_get_file_url('css/theme.css'), array(), null );
			wp_add_inline_style( 'organic_beauty-theme-style', organic_beauty_get_inline_css() );
		}
	}
}

// Add theme inline styles
if (!function_exists('organic_beauty_filter_theme_styles_add_styles_inline')) {
	//add_filter('organic_beauty_filter_add_styles_inline', 'organic_beauty_filter_theme_styles_add_styles_inline');
	function organic_beauty_filter_theme_styles_add_styles_inline($custom_style) {
		// Todo: add theme specific styles in the $custom_style to override
		//       rules from style.css and shortcodes.css
		// Example:
		//		$scheme = organic_beauty_get_custom_option('body_scheme');
		//		if (empty($scheme)) $scheme = 'original';
		//		$clr = organic_beauty_get_scheme_color('text_link');
		//		if (!empty($clr)) {
		// 			$custom_style .= '
		//				a,
		//				.bg_tint_light a,
		//				.top_panel .content .search_wrap.search_style_default .search_form_wrap .search_submit,
		//				.top_panel .content .search_wrap.search_style_default .search_icon,
		//				.search_results .post_more,
		//				.search_results .search_results_close {
		//					color:'.esc_attr($clr).';
		//				}
		//			';
		//		}

		// Submenu width
		$menu_width = organic_beauty_get_theme_option('menu_width');
		if (!empty($menu_width)) {
			$custom_style .= "
				/* Submenu width */
				.menu_side_nav > li ul,
				.menu_main_nav > li ul {
					width: ".intval($menu_width)."px;
				}
				.menu_side_nav > li > ul ul,
				.menu_main_nav > li > ul ul {
					left:".intval($menu_width+4)."px;
				}
				.menu_side_nav > li > ul ul.submenu_left,
				.menu_main_nav > li > ul ul.submenu_left {
					left:-".intval($menu_width+1)."px;
				}
			";
		}
	
		// Logo height
		$logo_height = organic_beauty_get_custom_option('logo_height');
		if (!empty($logo_height)) {
			$custom_style .= "
				/* Logo header height */
				.top_panel_wrap .logo_main,
				.top_panel_wrap .logo_fixed {
					height:".intval($logo_height)."px;
				}
			";
		}
	
		// Logo top offset
		$logo_offset = organic_beauty_get_custom_option('logo_offset');
		if (!empty($logo_offset)) {
			$custom_style .= "
				/* Logo header top offset */
				.top_panel_wrap .logo {
					margin-top:".intval($logo_offset)."px;
				}
			";
		}


		// Custom css from theme options
		$custom_style .= organic_beauty_get_custom_option('custom_css');

		return $custom_style;	
	}
}


//------------------------------------------------------------------------------
// Theme scripts
//------------------------------------------------------------------------------

// Add theme scripts
if (!function_exists('organic_beauty_action_theme_styles_add_scripts')) {
	//add_action('organic_beauty_action_add_scripts', 'organic_beauty_action_theme_styles_add_scripts');
	function organic_beauty_action_theme_styles_add_scripts() {}
}

// Add theme scripts inline
if (!function_exists('organic_beauty_filter_theme_styles_localize_script')) {
	//add_filter('organic_beauty_filter_localize_script',		'organic_beauty_filter_theme_styles_localize_script');
	function organic_beauty_filter_theme_styles_localize_script($vars) {
		if (empty($vars['theme_font']))
			$vars['theme_font'] = organic_beauty_get_custom_font_settings('p', 'font-family');
		$vars['theme_color'] = organic_beauty_get_scheme_color('text_dark');
		$vars['theme_bg_color'] = organic_beauty_get_scheme_color('bg_color');
		return $vars;
	}
}
?>