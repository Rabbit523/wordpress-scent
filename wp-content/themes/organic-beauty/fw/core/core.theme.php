<?php
/**
 * Organic Beauty Framework: Theme specific actions
 *
 * @package	organic_beauty
 * @since	organic_beauty 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

// Default Theme Options
if ( !function_exists( 'organic_beauty_core_theme_setup1' ) ) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_core_theme_setup1', 1 );	// Priority 1 for add themerex_filter handlers
	function organic_beauty_core_theme_setup1() {
		// Make theme available for translation
		// Translations can be filed in the /languages directory
		load_theme_textdomain( 'organic-beauty', organic_beauty_get_folder_dir('languages') );
	}
}

if ( !function_exists( 'organic_beauty_core_theme_setup' ) ) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_core_theme_setup', 11 );
	function organic_beauty_core_theme_setup() {

		// Add default posts and comments RSS feed links to head 
		add_theme_support( 'automatic-feed-links' );
		
		// Enable support for Post Thumbnails
		add_theme_support( 'post-thumbnails' );
		
		// Custom header setup
		add_theme_support( 'custom-header', array('header-text'=>false));
		
		// Custom backgrounds setup
		add_theme_support( 'custom-background');
		
		// Supported posts formats
		add_theme_support( 'post-formats', array('gallery', 'video', 'audio', 'link', 'quote', 'image', 'status', 'aside', 'chat') ); 
 
 		// Autogenerate title tag
		add_theme_support('title-tag');
 		
		// Add user menu
		add_theme_support('nav-menus');
		
		// WooCommerce Support
		add_theme_support( 'woocommerce' );
		
		// Editor custom stylesheet - for user
		add_editor_style(organic_beauty_get_file_url('css/editor-style.css'));	

		/* Front and Admin actions and filters:
		------------------------------------------------------------------------ */

		if ( !is_admin() ) {
			
			/* Front actions and filters:
			------------------------------------------------------------------------ */
	
			// Filters wp_title to print a neat <title> tag based on what is being viewed
			if (floatval(get_bloginfo('version')) < "4.1") {
				add_action('wp_head',						'organic_beauty_wp_title_show');
				add_filter('wp_title',						'organic_beauty_wp_title_modify', 10, 2);
			}

			// Prepare logo text
			add_filter('organic_beauty_filter_prepare_logo_text',	'organic_beauty_prepare_logo_text', 10, 1);
	
			// Add class "widget_number_#' for each widget
			add_filter('dynamic_sidebar_params', 			'organic_beauty_add_widget_number', 10, 1);
	
			// Enqueue scripts and styles
			add_action('wp_enqueue_scripts', 				'organic_beauty_core_frontend_scripts');
			add_action('wp_footer',		 					'organic_beauty_core_frontend_scripts_inline', 1);
			add_action('wp_footer',		 					'organic_beauty_core_frontend_add_js_vars', 2);
			add_action('organic_beauty_action_add_scripts_inline','organic_beauty_core_add_scripts_inline');
			add_filter('organic_beauty_filter_localize_script',	'organic_beauty_core_localize_script');
	
			add_filter('style_loader_tag', 					'organic_beauty_core_add_property_to_link', 10, 3);

			// Prepare theme core global variables
			add_action('organic_beauty_action_prepare_globals',	'organic_beauty_core_prepare_globals');
		}

		// Frontend editor: Save post data
		add_action('wp_ajax_frontend_editor_save',		'organic_beauty_callback_frontend_editor_save');
		add_action('wp_ajax_nopriv_frontend_editor_save', 'organic_beauty_callback_frontend_editor_save');

		// Frontend editor: Delete post
		add_action('wp_ajax_frontend_editor_delete', 	'organic_beauty_callback_frontend_editor_delete');
		add_action('wp_ajax_nopriv_frontend_editor_delete', 'organic_beauty_callback_frontend_editor_delete');

		// Register theme specific nav menus
		organic_beauty_register_theme_menus();

		// Register theme specific sidebars
		organic_beauty_register_theme_sidebars();
	}
}




/* Theme init
------------------------------------------------------------------------ */

// Init theme template
function organic_beauty_core_init_theme() {
	if (organic_beauty_storage_get('theme_inited')===true) return;
	organic_beauty_storage_set('theme_inited', true);

	// Load custom options from GET and post/page/cat options
	if (isset($_GET['set']) && $_GET['set']==1) {
		foreach ($_GET as $k=>$v) {
			if (organic_beauty_get_theme_option($k, null) !== null) {
				setcookie($k, $v, 0, '/');
				$_COOKIE[$k] = $v;
			}
		}
	}

	// Get custom options from current category / page / post / shop / event
	organic_beauty_load_custom_options();

	// Fire init theme actions (after custom options are loaded)
	do_action('organic_beauty_action_init_theme');

	// Prepare theme core global variables
	do_action('organic_beauty_action_prepare_globals');

	// Fire after init theme actions
	do_action('organic_beauty_action_after_init_theme');
}


// Prepare theme global variables
if ( !function_exists( 'organic_beauty_core_prepare_globals' ) ) {
	function organic_beauty_core_prepare_globals() {
		if (!is_admin()) {
			// Logo text and slogan
			organic_beauty_storage_set('logo_text', apply_filters('organic_beauty_filter_prepare_logo_text', organic_beauty_get_custom_option('logo_text')));
			organic_beauty_storage_set('logo_slogan', get_bloginfo('description'));
			
			// Logo image and icons
			$logo        = organic_beauty_get_logo_icon('logo');
			$logo_side   = organic_beauty_get_logo_icon('logo_side');
			$logo_fixed  = organic_beauty_get_logo_icon('logo_fixed');
			$logo_footer = '';
			organic_beauty_storage_set('logo', $logo);
			organic_beauty_storage_set('logo_icon',   organic_beauty_get_logo_icon('logo_icon'));
			organic_beauty_storage_set('logo_side',   $logo_side   ? $logo_side   : $logo);
			organic_beauty_storage_set('logo_fixed',  $logo_fixed  ? $logo_fixed  : $logo);
			organic_beauty_storage_set('logo_footer', $logo_footer ? $logo_footer : $logo);
	
			$shop_mode = '';
			if (organic_beauty_get_custom_option('show_mode_buttons')=='yes')
				$shop_mode = organic_beauty_get_value_gpc('organic_beauty_shop_mode');
			if (empty($shop_mode))
				$shop_mode = organic_beauty_get_custom_option('shop_mode', '');
			if (empty($shop_mode) || !is_archive())
				$shop_mode = 'thumbs';
			organic_beauty_storage_set('shop_mode', $shop_mode);
		}
	}
}


// Return url for the uploaded logo image
if ( !function_exists( 'organic_beauty_get_logo_icon' ) ) {
	function organic_beauty_get_logo_icon($slug) {
		// This way to load retina logo only if 'Retina' enabled in the Theme Options
		//$mult = organic_beauty_get_retina_multiplier();
		// This way ignore the 'Retina' setting and load retina logo on any display with retina support
		$mult = (int) organic_beauty_get_value_gpc('organic_beauty_retina', 0) > 0 ? 2 : 1;
		$logo_icon = '';
		if ($mult > 1) 			$logo_icon = organic_beauty_get_custom_option($slug.'_retina');
		if (empty($logo_icon))	$logo_icon = organic_beauty_get_custom_option($slug);
		return $logo_icon;
	}
}


// Display logo image with text and slogan (if specified)
if ( !function_exists( 'organic_beauty_show_logo' ) ) {
	function organic_beauty_show_logo($logo_main=true, $logo_fixed=false, $logo_footer=false, $logo_side=false, $logo_text=true, $logo_slogan=true) {
		if ($logo_main===true) 		$logo_main   = organic_beauty_storage_get('logo');
		if ($logo_fixed===true)		$logo_fixed  = organic_beauty_storage_get('logo_fixed');
		if ($logo_footer===true)	$logo_footer = organic_beauty_storage_get('logo_footer');
		if ($logo_side===true)		$logo_side   = organic_beauty_storage_get('logo_side');
		if ($logo_text===true)		$logo_text   = organic_beauty_storage_get('logo_text');
		if ($logo_slogan===true)	$logo_slogan = organic_beauty_storage_get('logo_slogan');
		if (empty($logo_main) && empty($logo_fixed) && empty($logo_footer) && empty($logo_side) && empty($logo_text))
			 $logo_text = get_bloginfo('name');
		if ($logo_main || $logo_fixed || $logo_footer || $logo_side || $logo_text) {
		?>
		<div class="logo">
			<a href="<?php echo esc_url(home_url('/')); ?>"><?php
				if (!empty($logo_main)) {
					$attr = organic_beauty_getimagesize($logo_main);
					echo '<img src="'.esc_url($logo_main).'" class="logo_main" alt=""'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
				}
				if (!empty($logo_fixed)) {
					$attr = organic_beauty_getimagesize($logo_fixed);
					echo '<img src="'.esc_url($logo_fixed).'" class="logo_fixed" alt=""'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
				}
				if (!empty($logo_footer)) {
					$attr = organic_beauty_getimagesize($logo_footer);
					echo '<img src="'.esc_url($logo_footer).'" class="logo_footer" alt=""'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
				}
				if (!empty($logo_side)) {
					$attr = organic_beauty_getimagesize($logo_side);
					echo '<img src="'.esc_url($logo_side).'" class="logo_side" alt=""'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
				}
				echo !empty($logo_text) ? '<div class="logo_text">'.trim($logo_text).'</div>' : '';
				echo !empty($logo_slogan) ? '<br><div class="logo_slogan">' . esc_html($logo_slogan) . '</div>' : '';
			?></a>
		</div>
		<?php 
		}
	} 
}


// Add menu locations
if ( !function_exists( 'organic_beauty_register_theme_menus' ) ) {
	function organic_beauty_register_theme_menus() {
		register_nav_menus(apply_filters('organic_beauty_filter_add_theme_menus', array(
			'menu_main'		=> esc_html__('Main Menu', 'organic-beauty'),
			'menu_user'		=> esc_html__('User Menu', 'organic-beauty'),
			'menu_footer'	=> esc_html__('Footer Menu', 'organic-beauty')
		)));
	}
}


// Register widgetized area
if ( !function_exists( 'organic_beauty_register_theme_sidebars' ) ) {
	function organic_beauty_register_theme_sidebars($sidebars=array()) {
		if (!is_array($sidebars)) $sidebars = array();
		// Custom sidebars
		$custom = organic_beauty_get_theme_option('custom_sidebars');
		if (is_array($custom) && count($custom) > 0) {
			foreach ($custom as $i => $sb) {
				if (trim(chop($sb))=='') continue;
				$sidebars['sidebar_custom_'.($i)]  = $sb;
			}
		}
		$sidebars = apply_filters( 'organic_beauty_filter_add_theme_sidebars', $sidebars );
		organic_beauty_storage_set('registered_sidebars', $sidebars);
		if (is_array($sidebars) && count($sidebars) > 0) {
			foreach ($sidebars as $id=>$name) {
				register_sidebar( array_merge( array(
													'name'          => $name,
													'id'            => $id
												),
												organic_beauty_storage_get('widgets_args')
									)
				);
			}
		}
	}
}





/* Front actions and filters:
------------------------------------------------------------------------ */

//  Enqueue scripts and styles
if ( !function_exists( 'organic_beauty_core_frontend_scripts' ) ) {
	function organic_beauty_core_frontend_scripts() {
		
		// Modernizr will load in head before other scripts and styles
		// Use older version (from photostack)
		organic_beauty_enqueue_script( 'organic_beauty-core-modernizr-script', organic_beauty_get_file_url('js/photostack/modernizr.min.js'), array(), null, false );
		
		// Enqueue styles
		//-----------------------------------------------------------------------------------------------------
		
		// Prepare custom fonts
	    if ( 'off' !== _x( 'on', 'Google fonts: on or off', 'organic-beauty' ) ) {
			$fonts = organic_beauty_get_list_fonts(false);
			$theme_fonts = array();
			$custom_fonts = organic_beauty_get_custom_fonts();
			if (is_array($custom_fonts) && count($custom_fonts) > 0) {
				foreach ($custom_fonts as $s=>$f) {
					if (!empty($f['font-family']) && !organic_beauty_is_inherit_option($f['font-family'])) $theme_fonts[$f['font-family']] = 1;
				}
			}
			// Prepare current theme fonts
			$theme_fonts = apply_filters('organic_beauty_filter_used_fonts', $theme_fonts);
			// Link to selected fonts
			if (is_array($theme_fonts) && count($theme_fonts) > 0) {
				$google_fonts = '';
				foreach ($theme_fonts as $font=>$v) {
					if (isset($fonts[$font])) {
						$font_name = ($pos=organic_beauty_strpos($font,' ('))!==false ? organic_beauty_substr($font, 0, $pos) : $font;
						if (!empty($fonts[$font]['css'])) {
							$css = $fonts[$font]['css'];
							organic_beauty_enqueue_style( 'organic_beauty-font-'.str_replace(' ', '-', $font_name).'-style', $css, array(), null );
						} else {
							$google_fonts .= ($google_fonts ? '%7C' : '') // %7C = |
								. (!empty($fonts[$font]['link']) ? $fonts[$font]['link'] : str_replace(' ', '+', $font_name).':300,300italic,400,400italic,500,500italic,600,600italic,700,700italic');
						}
					}
				}
				if ($google_fonts) {
					organic_beauty_enqueue_style( 'organic_beauty-font-google_fonts-style', add_query_arg( 'family', $google_fonts.'&subset='.organic_beauty_get_theme_option('fonts_subset'), "//fonts.googleapis.com/css" ), array(), null );
				}
			}
		}
		
		// Fontello styles must be loaded before main stylesheet
		organic_beauty_enqueue_style( 'organic_beauty-fontello-style',  organic_beauty_get_file_url('css/fontello/css/fontello.css'),  array(), null);

		// Main stylesheet
		organic_beauty_enqueue_style( 'organic_beauty-main-style', get_stylesheet_uri(), array(), null );
		
		// Animations
		if (organic_beauty_get_theme_option('css_animation')=='yes' && (organic_beauty_get_theme_option('animation_on_mobile')=='yes' || !wp_is_mobile()) && !organic_beauty_vc_is_frontend())
			organic_beauty_enqueue_style( 'organic_beauty-animation-style',	organic_beauty_get_file_url('css/core.animation.css'), array(), null );

		// Theme stylesheets
		do_action('organic_beauty_action_add_styles');

		// Responsive
		if (organic_beauty_get_theme_option('responsive_layouts') == 'yes') {
			organic_beauty_enqueue_style( 'organic_beauty-responsive-style', organic_beauty_get_file_url('css/responsive.css'), array(), null );
			do_action('organic_beauty_action_add_responsive');
			$css = apply_filters('organic_beauty_filter_add_responsive_inline', '');
			if (!empty($css)) wp_add_inline_style( 'organic_beauty-responsive-style', $css );
		}

		// Disable loading JQuery UI CSS
		wp_deregister_style('jquery_ui');
		wp_deregister_style('date-picker-css');


		// Enqueue scripts	
		//----------------------------------------------------------------------------------------------------------------------------
		
		// Load separate theme scripts
		organic_beauty_enqueue_script( 'superfish', organic_beauty_get_file_url('js/superfish.js'), array('jquery'), null, true );

		if ( is_single() && organic_beauty_get_custom_option('show_reviews')=='yes' ) {
			organic_beauty_enqueue_script( 'organic_beauty-core-reviews-script', organic_beauty_get_file_url('js/core.reviews.js'), array('jquery'), null, true );
		}

		organic_beauty_enqueue_script( 'organic_beauty-core-utils-script',	organic_beauty_get_file_url('js/core.utils.js'), array('jquery'), null, true );
		organic_beauty_enqueue_script( 'organic_beauty-core-init-script',	organic_beauty_get_file_url('js/core.init.js'), array('jquery'), null, true );	
		organic_beauty_enqueue_script( 'organic_beauty-theme-init-script',	organic_beauty_get_file_url('js/theme.init.js'), array('jquery'), null, true );	

		// Media elements library	
		if (organic_beauty_get_theme_option('use_mediaelement')=='yes') {
			wp_enqueue_style ( 'mediaelement' );
			wp_enqueue_style ( 'wp-mediaelement' );
			wp_enqueue_script( 'mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}
		
		// Video background
		if (organic_beauty_get_custom_option('show_video_bg') == 'yes' && organic_beauty_get_custom_option('video_bg_youtube_code') != '') {
			organic_beauty_enqueue_script( 'organic_beauty-video-bg-script', organic_beauty_get_file_url('js/jquery.tubular.1.0.js'), array('jquery'), null, true );
		}

		// Google map
		if ( organic_beauty_get_custom_option('show_googlemap')=='yes' ) { 
			$api_key = organic_beauty_get_theme_option('api_google');
			wp_enqueue_script( 'googlemap', organic_beauty_get_protocol().'://maps.google.com/maps/api/js'.($api_key ? '?key='.$api_key : ''), array(), null, false );
			wp_enqueue_script( 'organic_beauty-googlemap-script', organic_beauty_get_file_url('js/core.googlemap.js'), array(), null, true );
		}

			
		// Social share buttons
		if (is_singular() && !organic_beauty_storage_get('blog_streampage') && organic_beauty_get_custom_option('show_share')!='hide') {
			organic_beauty_enqueue_script( 'organic_beauty-social-share-script', organic_beauty_get_file_url('js/social/social-share.js'), array('jquery'), null, true );
		}

		// Comments
		if ( is_singular() && !organic_beauty_storage_get('blog_streampage') && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply', false, array(), null, true );
		}

		//Debug utils
		if (organic_beauty_get_theme_option('debug_mode')=='yes') {
			organic_beauty_enqueue_script( 'organic_beauty-core-debug-script', organic_beauty_get_file_url('js/core.debug.js'), array(), null, true );
		}

		// Theme scripts
		do_action('organic_beauty_action_add_scripts');
	}
}

//  Enqueue Swiper Slider scripts and styles
if ( !function_exists( 'organic_beauty_enqueue_slider' ) ) {
	function organic_beauty_enqueue_slider($engine='all') {
		if ($engine=='all' || $engine=='swiper') {
			organic_beauty_enqueue_style(  'organic_beauty-swiperslider-style', 			organic_beauty_get_file_url('js/swiper/swiper.css'), array(), null );
			// jQuery version of Swiper conflict with Revolution Slider!!! Use DOM version
			organic_beauty_enqueue_script( 'organic_beauty-swiperslider-script', 			organic_beauty_get_file_url('js/swiper/swiper.js'), array(), null, true );
		}
	}
}

//  Enqueue Photostack gallery
if ( !function_exists( 'organic_beauty_enqueue_polaroid' ) ) {
	function organic_beauty_enqueue_polaroid() {
		organic_beauty_enqueue_style(  'organic_beauty-polaroid-style', 	organic_beauty_get_file_url('js/photostack/component.css'), array(), null );
		organic_beauty_enqueue_script( 'organic_beauty-classie-script',		organic_beauty_get_file_url('js/photostack/classie.js'), array(), null, true );
		organic_beauty_enqueue_script( 'organic_beauty-polaroid-script',	organic_beauty_get_file_url('js/photostack/photostack.js'), array(), null, true );
	}
}

//  Enqueue Messages scripts and styles
if ( !function_exists( 'organic_beauty_enqueue_messages' ) ) {
	function organic_beauty_enqueue_messages() {
		organic_beauty_enqueue_style(  'organic_beauty-messages-style',		organic_beauty_get_file_url('js/core.messages/core.messages.css'), array(), null );
		organic_beauty_enqueue_script( 'organic_beauty-messages-script',	organic_beauty_get_file_url('js/core.messages/core.messages.js'),  array('jquery'), null, true );
	}
}

//  Enqueue Portfolio hover scripts and styles
if ( !function_exists( 'organic_beauty_enqueue_portfolio' ) ) {
	function organic_beauty_enqueue_portfolio($hover='') {
		organic_beauty_enqueue_style( 'organic_beauty-portfolio-style',  organic_beauty_get_file_url('css/core.portfolio.css'), array(), null );
		if (organic_beauty_strpos($hover, 'effect_dir')!==false)
			organic_beauty_enqueue_script( 'hoverdir', organic_beauty_get_file_url('js/hover/jquery.hoverdir.js'), array(), null, true );
	}
}

//  Enqueue Charts and Diagrams scripts and styles
if ( !function_exists( 'organic_beauty_enqueue_diagram' ) ) {
	function organic_beauty_enqueue_diagram($type='all') {
		if ($type=='all' || $type=='pie') organic_beauty_enqueue_script( 'organic_beauty-diagram-chart-script',	organic_beauty_get_file_url('js/diagram/chart.min.js'), array(), null, true );
		if ($type=='all' || $type=='arc') organic_beauty_enqueue_script( 'organic_beauty-diagram-raphael-script',	organic_beauty_get_file_url('js/diagram/diagram.raphael.min.js'), array(), 'no-compose', true );
	}
}

// Enqueue Theme Popup scripts and styles
// Link must have attribute: data-rel="popup" or data-rel="popup[gallery]"
if ( !function_exists( 'organic_beauty_enqueue_popup' ) ) {
	function organic_beauty_enqueue_popup($engine='') {
		if ($engine=='pretty' || (empty($engine) && organic_beauty_get_theme_option('popup_engine')=='pretty')) {
			organic_beauty_enqueue_style(  'organic_beauty-prettyphoto-style',	organic_beauty_get_file_url('js/prettyphoto/css/prettyPhoto.css'), array(), null );
			organic_beauty_enqueue_script( 'organic_beauty-prettyphoto-script',	organic_beauty_get_file_url('js/prettyphoto/jquery.prettyPhoto.min.js'), array('jquery'), 'no-compose', true );
		} else if ($engine=='magnific' || (empty($engine) && organic_beauty_get_theme_option('popup_engine')=='magnific')) {
			organic_beauty_enqueue_style(  'organic_beauty-magnific-style',	organic_beauty_get_file_url('js/magnific/magnific-popup.css'), array(), null );
			organic_beauty_enqueue_script( 'organic_beauty-magnific-script',organic_beauty_get_file_url('js/magnific/jquery.magnific-popup.min.js'), array('jquery'), '', true );
		} else if ($engine=='internal' || (empty($engine) && organic_beauty_get_theme_option('popup_engine')=='internal')) {
			organic_beauty_enqueue_messages();
		}
	}
}

//  Add inline scripts in the footer hook
if ( !function_exists( 'organic_beauty_core_frontend_scripts_inline' ) ) {
	//add_action('wp_footer', 'organic_beauty_core_frontend_scripts_inline', 1);
	function organic_beauty_core_frontend_scripts_inline() {
		do_action('organic_beauty_action_add_scripts_inline');
	}
}

//  Localize scripts in the footer hook
if ( !function_exists( 'organic_beauty_core_frontend_add_js_vars' ) ) {
	//add_action('wp_footer', 'organic_beauty_core_frontend_add_js_vars', 2);
	function organic_beauty_core_frontend_add_js_vars() {
		$vars = apply_filters( 'organic_beauty_filter_localize_script', organic_beauty_storage_empty('js_vars') ? array() : organic_beauty_storage_get('js_vars'));
		if (!empty($vars)) wp_localize_script( 'organic_beauty-core-init-script', 'ORGANIC_BEAUTY_STORAGE', $vars);
		if (!organic_beauty_storage_empty('js_code')) {
			$holder = 'script';
			?><<?php organic_beauty_show_layout($holder); ?>>
				jQuery(document).ready(function() {
					<?php organic_beauty_show_layout(organic_beauty_minify_js(organic_beauty_storage_get('js_code'))); ?>
				});
			</<?php organic_beauty_show_layout($holder); ?>><?php
		}
	}
}


//  Add property="stylesheet" into all tags <link> in the footer
if (!function_exists('organic_beauty_core_add_property_to_link')) {
	//add_filter('style_loader_tag', 'organic_beauty_core_add_property_to_link', 10, 3);
	function organic_beauty_core_add_property_to_link($link, $handle='', $href='') {
		return str_replace('<link ', '<link property="stylesheet" ', $link);
	}
}

//  Add inline scripts in the footer
if (!function_exists('organic_beauty_core_add_scripts_inline')) {
	//add_action('organic_beauty_action_add_scripts_inline','organic_beauty_core_add_scripts_inline');
	function organic_beauty_core_add_scripts_inline() {
		// System message
		$msg = organic_beauty_get_system_message(true); 
		if (!empty($msg['message'])) organic_beauty_enqueue_messages();
		organic_beauty_storage_set_array('js_vars', 'system_message',	$msg);
	}
}

//  Localize script
if (!function_exists('organic_beauty_core_localize_script')) {
	//add_filter('organic_beauty_filter_localize_script',	'organic_beauty_core_localize_script');
	function organic_beauty_core_localize_script($vars) {

		// AJAX parameters
		$vars['ajax_url'] = esc_url(admin_url('admin-ajax.php'));
		$vars['ajax_nonce'] = wp_create_nonce(admin_url('admin-ajax.php'));

		// Site base url
		$vars['site_url'] = esc_url(get_site_url());

		// Site protocol
		$vars['site_protocol'] = organic_beauty_get_protocol();
			
		// VC frontend edit mode
		$vars['vc_edit_mode'] = function_exists('organic_beauty_vc_is_frontend') && organic_beauty_vc_is_frontend();
			
		// Theme base font
		$vars['theme_font'] = organic_beauty_get_custom_font_settings('p', 'font-family');
			
		// Theme colors
		$vars['theme_color'] = organic_beauty_get_scheme_color('text_dark');
		$vars['theme_bg_color'] = organic_beauty_get_scheme_color('bg_color');
		$vars['accent1_color'] = organic_beauty_get_scheme_color('text_link');
		$vars['accent1_hover'] = organic_beauty_get_scheme_color('text_hover');
			
		// Slider height
		$vars['slider_height'] = max(100, organic_beauty_get_custom_option('slider_height'));
			
		// User logged in
		$vars['user_logged_in'] = is_user_logged_in();
			
		// Show table of content for the current page
		$vars['toc_menu'] = organic_beauty_get_custom_option('menu_toc');
		$vars['toc_menu_home'] = organic_beauty_get_custom_option('menu_toc')!='hide' && organic_beauty_get_custom_option('menu_toc_home')=='yes';
		$vars['toc_menu_top'] = organic_beauty_get_custom_option('menu_toc')!='hide' && organic_beauty_get_custom_option('menu_toc_top')=='yes';

		// Fix main menu
		$vars['menu_fixed'] = organic_beauty_get_theme_option('menu_attachment')=='fixed';
			
		// Use responsive version for main menu
		$vars['menu_mobile'] = organic_beauty_get_theme_option('responsive_layouts') == 'yes' ? max(0, (int) organic_beauty_get_theme_option('menu_mobile')) : 0;
		$vars['menu_hover'] = 'fade';
			
		// Theme's buttons hover
		$vars['button_hover'] = organic_beauty_get_theme_option('button_hover');

		// Theme's form fields style
		$vars['input_hover'] = 'default';

		// Right panel demo timer
		$vars['demo_time'] = 0;

		// Video and Audio tag wrapper
		$vars['media_elements_enabled'] = organic_beauty_get_theme_option('use_mediaelement')=='yes';
			
		// Use AJAX search
		$vars['ajax_search_enabled'] = organic_beauty_get_theme_option('use_ajax_search')=='yes';
		$vars['ajax_search_min_length'] = min(3, organic_beauty_get_theme_option('ajax_search_min_length'));
		$vars['ajax_search_delay'] = min(200, max(1000, organic_beauty_get_theme_option('ajax_search_delay')));

		// Use CSS animation
		$vars['css_animation'] = organic_beauty_get_theme_option('css_animation')=='yes';
		$vars['menu_animation_in'] = '';
		$vars['menu_animation_out'] = '';

		// Popup windows engine
		$vars['popup_engine'] = organic_beauty_get_theme_option('popup_engine');

		// E-mail mask
		$vars['email_mask'] = '^([a-zA-Z0-9_\\-]+\\.)*[a-zA-Z0-9_\\-]+@[a-z0-9_\\-]+(\\.[a-z0-9_\\-]+)*\\.[a-z]{2,6}$';
			
		// Messages max length
		$vars['contacts_maxlength'] = organic_beauty_get_theme_option('message_maxlength_contacts');
		$vars['comments_maxlength'] = organic_beauty_get_theme_option('message_maxlength_comments');

		// Remember visitors settings
		$vars['remember_visitors_settings'] = organic_beauty_get_theme_option('remember_visitors_settings')=='yes';

		// Internal vars - do not change it!
		// Flag for review mechanism
		$vars['admin_mode'] = false;
		// Max scale factor for the portfolio and other isotope elements before relayout
		$vars['isotope_resize_delta'] = 0.3;
		// jQuery object for the message box in the form
		$vars['error_message_box'] = null;
		// Waiting for the viewmore results
		$vars['viewmore_busy'] = false;
		$vars['video_resize_inited'] = false;
		$vars['top_panel_height'] = 0;

		return $vars;
	}
}

// Show content with the html layout (if not empty)
if ( !function_exists('organic_beauty_show_layout') ) {
	function organic_beauty_show_layout($str, $before='', $after='') {
		if (!empty($str)) {
			printf("%s%s%s", $before, $str, $after);
		}
	}
}

// Add class "widget_number_#' for each widget
if ( !function_exists( 'organic_beauty_add_widget_number' ) ) {
	//add_filter('dynamic_sidebar_params', 'organic_beauty_add_widget_number', 10, 1);
	function organic_beauty_add_widget_number($prm) {
		if (is_admin()) return $prm;
		static $num=0, $last_sidebar='', $last_sidebar_id='', $last_sidebar_columns=0, $last_sidebar_count=0, $sidebars_widgets=array();
		$cur_sidebar = organic_beauty_storage_get('current_sidebar');
		if (empty($cur_sidebar)) $cur_sidebar = 'undefined';
		if (count($sidebars_widgets) == 0)
			$sidebars_widgets = wp_get_sidebars_widgets();
		if ($last_sidebar != $cur_sidebar) {
			$num = 0;
			$last_sidebar = $cur_sidebar;
			$last_sidebar_id = $prm[0]['id'];
			$last_sidebar_columns = max(1, (int) organic_beauty_get_custom_option('sidebar_'.($cur_sidebar).'_columns'));
			$last_sidebar_count = count($sidebars_widgets[$last_sidebar_id]);
		}
		$num++;
		$prm[0]['before_widget'] = str_replace(' class="', ' class="widget_number_'.esc_attr($num).($last_sidebar_columns > 1 ? ' column-1_'.esc_attr($last_sidebar_columns) : '').' ', $prm[0]['before_widget']);
		return $prm;
	}
}


// Show <title> tag under old WP (version < 4.1)
if ( !function_exists( 'organic_beauty_wp_title_show' ) ) {
	// add_action('wp_head', 'organic_beauty_wp_title_show');
	function organic_beauty_wp_title_show() {
		?><title><?php wp_title( '|', true, 'right' ); ?></title><?php
	}
}

// Filters wp_title to print a neat <title> tag based on what is being viewed.
if ( !function_exists( 'organic_beauty_wp_title_modify' ) ) {
	// add_filter( 'wp_title', 'organic_beauty_wp_title_modify', 10, 2 );
	function organic_beauty_wp_title_modify( $title, $sep ) {
		global $page, $paged;
		if ( is_feed() ) return $title;
		// Add the blog name
		$title .= get_bloginfo( 'name' );
		// Add the blog description for the home/front page.
		if ( is_home() || is_front_page() ) {
			$site_description = get_bloginfo( 'description', 'display' );
			if ( $site_description )
				$title .= " $sep $site_description";
		}
		// Add a page number if necessary:
		if ( $paged >= 2 || $page >= 2 )
			$title .= " $sep " . sprintf( esc_html__( 'Page %s', 'organic-beauty' ), max( $paged, $page ) );
		return $title;
	}
}

// Add main menu classes
if ( !function_exists( 'organic_beauty_add_mainmenu_classes' ) ) {
	// add_filter('wp_nav_menu_objects', 'organic_beauty_add_mainmenu_classes', 10, 2);
	function organic_beauty_add_mainmenu_classes($items, $args) {
		if (is_admin()) return $items;
		if ($args->menu_id == 'mainmenu' && organic_beauty_get_theme_option('menu_colored')=='yes' && is_array($items) && count($items) > 0) {
			foreach($items as $k=>$item) {
				if ($item->menu_item_parent==0) {
					if ($item->type=='taxonomy' && $item->object=='category') {
						$cur_tint = organic_beauty_taxonomy_get_inherited_property('category', $item->object_id, 'bg_tint');
						if (!empty($cur_tint) && !organic_beauty_is_inherit_option($cur_tint))
							$items[$k]->classes[] = 'bg_tint_'.esc_attr($cur_tint);
					}
				}
			}
		}
		return $items;
	}
}


// Save post data from frontend editor
if ( !function_exists( 'organic_beauty_callback_frontend_editor_save' ) ) {
	function organic_beauty_callback_frontend_editor_save() {

		if ( !wp_verify_nonce( organic_beauty_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			die();
		$response = array('error'=>'');

		parse_str($_REQUEST['data'], $output);
		$post_id = $output['frontend_editor_post_id'];

		if ( organic_beauty_get_theme_option("allow_editor")=='yes' && (current_user_can('edit_posts', $post_id) || current_user_can('edit_pages', $post_id)) ) {
			if ($post_id > 0) {
				$title   = stripslashes($output['frontend_editor_post_title']);
				$content = stripslashes($output['frontend_editor_post_content']);
				$excerpt = stripslashes($output['frontend_editor_post_excerpt']);
				$rez = wp_update_post(array(
					'ID'           => $post_id,
					'post_content' => $content,
					'post_excerpt' => $excerpt,
					'post_title'   => $title
				));
				if ($rez == 0) 
					$response['error'] = esc_html__('Post update error!', 'organic-beauty');
			} else {
				$response['error'] = esc_html__('Post update error!', 'organic-beauty');
			}
		} else
			$response['error'] = esc_html__('Post update denied!', 'organic-beauty');
		
		echo json_encode($response);
		die();
	}
}

// Delete post from frontend editor
if ( !function_exists( 'organic_beauty_callback_frontend_editor_delete' ) ) {
	function organic_beauty_callback_frontend_editor_delete() {

		if ( !wp_verify_nonce( organic_beauty_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			die();

		$response = array('error'=>'');
		
		$post_id = $_REQUEST['post_id'];

		if ( organic_beauty_get_theme_option("allow_editor")=='yes' && (current_user_can('delete_posts', $post_id) || current_user_can('delete_pages', $post_id)) ) {
			if ($post_id > 0) {
				$rez = wp_delete_post($post_id);
				if ($rez === false) 
					$response['error'] = esc_html__('Post delete error!', 'organic-beauty');
			} else {
				$response['error'] = esc_html__('Post delete error!', 'organic-beauty');
			}
		} else
			$response['error'] = esc_html__('Post delete denied!', 'organic-beauty');

		echo json_encode($response);
		die();
	}
}


// Prepare logo text
if ( !function_exists( 'organic_beauty_prepare_logo_text' ) ) {
	function organic_beauty_prepare_logo_text($text) {
		$text = str_replace(array('[', ']'), array('<span class="theme_accent">', '</span>'), $text);
		$text = str_replace(array('{', '}'), array('<strong>', '</strong>'), $text);
		return $text;
	}
}
?>