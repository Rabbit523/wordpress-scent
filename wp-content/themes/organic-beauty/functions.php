<?php
/**
 * Theme sprecific functions and definitions
 */

/* Theme setup section
------------------------------------------------------------------- */


// Set the content width based on the theme's design and stylesheet.
if ( ! isset( $content_width ) ) $content_width = 1170; /* pixels */

// Add theme specific actions and filters
// Attention! Function were add theme specific actions and filters handlers must have priority 1
if ( !function_exists( 'organic_beauty_theme_setup' ) ) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_theme_setup', 1 );
	function organic_beauty_theme_setup() {

		// Register theme menus
		add_filter( 'organic_beauty_filter_add_theme_menus',		'organic_beauty_add_theme_menus' );

		// Register theme sidebars
		add_filter( 'organic_beauty_filter_add_theme_sidebars',	'organic_beauty_add_theme_sidebars' );

		// Set options for importer
		add_filter( 'organic_beauty_filter_importer_options',		'organic_beauty_set_importer_options' );

		// Add theme required plugins
		add_filter( 'organic_beauty_filter_required_plugins',		'organic_beauty_add_required_plugins' );
		
		// Add preloader styles
		add_filter('organic_beauty_filter_add_styles_inline',		'organic_beauty_head_add_page_preloader_styles');

		// Init theme after WP is created
		add_action( 'wp',									'organic_beauty_core_init_theme' );

		// Add theme specified classes into the body
		add_filter( 'body_class', 							'organic_beauty_body_classes' );

		// Add data to the head and to the beginning of the body
		add_action('wp_head',								'organic_beauty_head_add_page_meta', 1);
		add_action('before',								'organic_beauty_body_add_gtm');
		add_action('before',								'organic_beauty_body_add_toc');
		add_action('before',								'organic_beauty_body_add_page_preloader');

		// Add data to the footer (priority 1, because priority 2 used for localize scripts)
		add_action('wp_footer',								'organic_beauty_footer_add_views_counter', 1);
		add_action('wp_footer',								'organic_beauty_footer_add_theme_customizer', 1);
		add_action('wp_footer',								'organic_beauty_footer_add_scroll_to_top', 1);
		add_action('wp_footer',								'organic_beauty_footer_add_custom_html', 1);
		add_action('wp_footer',								'organic_beauty_footer_add_gtm2', 1);

		// Set list of the theme required plugins
		organic_beauty_storage_set('required_plugins', array(
			'essgrids',
			'revslider',
			'trx_utils',
			'visual_composer',
			'woocommerce',
			'mailchimp',
			'instagram_feed'
			)
		);

		// Set list of the theme required custom fonts from folder /css/font-faces
		// Attention! Font's folder must have name equal to the font's name
		organic_beauty_storage_set('required_custom_fonts', array(
			'Amadeus'
			)
		);
		
		organic_beauty_storage_set('demo_data_url',  esc_url(organic_beauty_get_protocol() . '://organic-beauty.themerex.net/demo/'));
		
	}
}


// Add/Remove theme nav menus
if ( !function_exists( 'organic_beauty_add_theme_menus' ) ) {
	//add_filter( 'organic_beauty_filter_add_theme_menus', 'organic_beauty_add_theme_menus' );
	function organic_beauty_add_theme_menus($menus) {
		//For example:
		//$menus['menu_footer'] = esc_html__('Footer Menu', 'organic-beauty');
		//if (isset($menus['menu_panel'])) unset($menus['menu_panel']);
		return $menus;
	}
}


// Add theme specific widgetized areas
if ( !function_exists( 'organic_beauty_add_theme_sidebars' ) ) {
	//add_filter( 'organic_beauty_filter_add_theme_sidebars',	'organic_beauty_add_theme_sidebars' );
	function organic_beauty_add_theme_sidebars($sidebars=array()) {
		if (is_array($sidebars)) {
			$theme_sidebars = array(
				'sidebar_main'		=> esc_html__( 'Main Sidebar', 'organic-beauty' ),
				'sidebar_footer'	=> esc_html__( 'Footer Sidebar', 'organic-beauty' )
			);
			if (function_exists('organic_beauty_exists_woocommerce') && organic_beauty_exists_woocommerce()) {
				$theme_sidebars['sidebar_cart']  = esc_html__( 'WooCommerce Cart Sidebar', 'organic-beauty' );
			}
			$sidebars = array_merge($theme_sidebars, $sidebars);
		}
		return $sidebars;
	}
}


// Add theme required plugins
if ( !function_exists( 'organic_beauty_add_required_plugins' ) ) {
	//add_filter( 'organic_beauty_filter_required_plugins',		'organic_beauty_add_required_plugins' );
	function organic_beauty_add_required_plugins($plugins) {
		$plugins[] = array(
			'name' 		=> esc_html__('ThemeREX Utilities', 'organic-beauty'),
			'version'	=> '3.0',					// Minimal required version
			'slug' 		=> 'trx_utils',
			'source'	=> organic_beauty_get_file_dir('plugins/install/trx_utils.zip'),
			'required' 	=> true
		);
		return $plugins;
	}
}


// One-click import support
//------------------------------------------------------------------------

// Set theme specific importer options
if ( !function_exists( 'organic_beauty_set_importer_options' ) ) {
	//add_filter( 'organic_beauty_filter_importer_options',	'organic_beauty_set_importer_options' );
	function organic_beauty_set_importer_options($options=array()) {
		if (is_array($options)) {
			// Default demo
			$options['demo_url'] = organic_beauty_storage_get('demo_data_url');
			$options['files']['default']['title'] = esc_html__('Organic Beauty Demo', 'organic-beauty');
			$options['files']['default']['domain_dev'] = '';																	// Developers domain
			$options['files']['default']['domain_demo']= esc_url(organic_beauty_get_protocol().'://organic-beauty.themerex.net');		// Demo-site domain
		}
		return $options;
	}
}


// Add data to the head and to the beginning of the body
//------------------------------------------------------------------------

// Add theme specified classes to the body tag
if ( !function_exists('organic_beauty_body_classes') ) {
	//add_filter( 'body_class', 'organic_beauty_body_classes' );
	function organic_beauty_body_classes( $classes ) {

		$classes[] = 'organic_beauty_body';
		$classes[] = 'body_style_' . trim(organic_beauty_get_custom_option('body_style'));
		$classes[] = 'body_' . (organic_beauty_get_custom_option('body_filled')=='yes' ? 'filled' : 'transparent');
		$classes[] = 'article_style_' . trim(organic_beauty_get_custom_option('article_style'));
		
		$blog_style = organic_beauty_get_custom_option(is_singular() && !organic_beauty_storage_get('blog_streampage') ? 'single_style' : 'blog_style');
		$classes[] = 'layout_' . trim($blog_style);
		$classes[] = 'template_' . trim(organic_beauty_get_template_name($blog_style));
		
		$body_scheme = organic_beauty_get_custom_option('body_scheme');
		if (empty($body_scheme)  || organic_beauty_is_inherit_option($body_scheme)) $body_scheme = 'original';
		$classes[] = 'scheme_' . $body_scheme;

		$top_panel_position = organic_beauty_get_custom_option('top_panel_position');
		if (!organic_beauty_param_is_off($top_panel_position)) {
			$classes[] = 'top_panel_show';
			$classes[] = 'top_panel_' . trim($top_panel_position);
		} else 
			$classes[] = 'top_panel_hide';
		$classes[] = organic_beauty_get_sidebar_class();

		if (organic_beauty_get_custom_option('show_video_bg')=='yes' && (organic_beauty_get_custom_option('video_bg_youtube_code')!='' || organic_beauty_get_custom_option('video_bg_url')!=''))
			$classes[] = 'video_bg_show';

		if (!organic_beauty_param_is_off(organic_beauty_get_theme_option('page_preloader')))
			$classes[] = 'preloader';

		return $classes;
	}
}


// Add page meta to the head
if (!function_exists('organic_beauty_head_add_page_meta')) {
	//add_action('wp_head', 'organic_beauty_head_add_page_meta', 1);
	function organic_beauty_head_add_page_meta() {
		?>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1<?php if (organic_beauty_get_theme_option('responsive_layouts')=='yes') echo ', maximum-scale=1'; ?>">
		<meta name="format-detection" content="telephone=no">
	
		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<?php
	}
}

// Add page preloader styles to the head
if (!function_exists('organic_beauty_head_add_page_preloader_styles')) {
	//add_filter('organic_beauty_filter_add_styles_inline', 'organic_beauty_head_add_page_preloader_styles');
	function organic_beauty_head_add_page_preloader_styles($css) {
		if (($preloader=organic_beauty_get_theme_option('page_preloader'))!='none') {
			$image = organic_beauty_get_theme_option('page_preloader_image');
			$bg_clr = organic_beauty_get_scheme_color('bg_color');
			$link_clr = organic_beauty_get_scheme_color('text_link');
			$css .= '
				#page_preloader {
					background-color: '. esc_attr($bg_clr) . ';'
					. ($preloader=='custom' && $image
						? 'background-image:url('.esc_url($image).');'
						: ''
						)
				    . '
				}
				.preloader_wrap > div {
					background-color: '.esc_attr($link_clr).';
				}';
		}
		return $css;
	}
}

// Add gtm code to the beginning of the body 
if (!function_exists('organic_beauty_body_add_gtm')) {
	//add_action('before', 'organic_beauty_body_add_gtm');
	function organic_beauty_body_add_gtm() {
		echo force_balance_tags(organic_beauty_get_custom_option('gtm_code'));
	}
}

// Add TOC anchors to the beginning of the body 
if (!function_exists('organic_beauty_body_add_toc')) {
	//add_action('before', 'organic_beauty_body_add_toc');
	function organic_beauty_body_add_toc() {
		// Add TOC items 'Home' and "To top"
		if (organic_beauty_get_custom_option('menu_toc_home')=='yes')
			organic_beauty_show_layout(organic_beauty_sc_anchor(array(
				'id' => "toc_home",
				'title' => esc_html__('Home', 'organic-beauty'),
				'description' => esc_html__('{{Return to Home}} - ||navigate to home page of the site', 'organic-beauty'),
				'icon' => "icon-home",
				'separator' => "yes",
				'url' => esc_url(home_url('/'))
				)
			)); 
		if (organic_beauty_get_custom_option('menu_toc_top')=='yes')
			organic_beauty_show_layout(organic_beauty_sc_anchor(array(
				'id' => "toc_top",
				'title' => esc_html__('To Top', 'organic-beauty'),
				'description' => esc_html__('{{Back to top}} - ||scroll to top of the page', 'organic-beauty'),
				'icon' => "icon-double-up",
				'separator' => "yes")
				)); 
	}
}

// Add page preloader to the beginning of the body
if (!function_exists('organic_beauty_body_add_page_preloader')) {
	//add_action('before', 'organic_beauty_body_add_page_preloader');
	function organic_beauty_body_add_page_preloader() {
		if ( ($preloader=organic_beauty_get_theme_option('page_preloader')) != 'none' && ( $preloader != 'custom' || ($image=organic_beauty_get_theme_option('page_preloader_image')) != '')) {
			?><div id="page_preloader"><?php
				if ($preloader == 'circle') {
					?><div class="preloader_wrap preloader_<?php echo esc_attr($preloader); ?>"><div class="preloader_circ1"></div><div class="preloader_circ2"></div><div class="preloader_circ3"></div><div class="preloader_circ4"></div></div><?php
				} else if ($preloader == 'square') {
					?><div class="preloader_wrap preloader_<?php echo esc_attr($preloader); ?>"><div class="preloader_square1"></div><div class="preloader_square2"></div></div><?php
				}
			?></div><?php
		}
	}
}


// Add data to the footer
//------------------------------------------------------------------------

// Add post/page views counter
if (!function_exists('organic_beauty_footer_add_views_counter')) {
	//add_action('wp_footer', 'organic_beauty_footer_add_views_counter');
	function organic_beauty_footer_add_views_counter() {
		// Post/Page views counter
		get_template_part(organic_beauty_get_file_slug('templates/_parts/views-counter.php'));
	}
}

// Add theme customizer
if (!function_exists('organic_beauty_footer_add_theme_customizer')) {
	//add_action('wp_footer', 'organic_beauty_footer_add_theme_customizer');
	function organic_beauty_footer_add_theme_customizer() {
		// Front customizer
		if (organic_beauty_get_custom_option('show_theme_customizer')=='yes') {
			require_once ORGANIC_BEAUTY_FW_PATH . 'core/core.customizer/front.customizer.php';
		}
	}
}

// Add scroll to top button
if (!function_exists('organic_beauty_footer_add_scroll_to_top')) {
	//add_action('wp_footer', 'organic_beauty_footer_add_scroll_to_top');
	function organic_beauty_footer_add_scroll_to_top() {
		?><a href="#" class="scroll_to_top icon-up" title="<?php esc_attr_e('Scroll to top', 'organic-beauty'); ?>"></a><?php
	}
}

// Add custom html
if (!function_exists('organic_beauty_footer_add_custom_html')) {
	//add_action('wp_footer', 'organic_beauty_footer_add_custom_html');
	function organic_beauty_footer_add_custom_html() {
		?><div class="custom_html_section"><?php
			echo force_balance_tags(organic_beauty_get_custom_option('custom_code'));
		?></div><?php
	}
}

// Add gtm code
if (!function_exists('organic_beauty_footer_add_gtm2')) {
	//add_action('wp_footer', 'organic_beauty_footer_add_gtm2');
	function organic_beauty_footer_add_gtm2() {
		echo force_balance_tags(organic_beauty_get_custom_option('gtm_code2'));
	}
}



// Include framework core files
//-------------------------------------------------------------------
require_once trailingslashit( get_template_directory() ) . 'fw/loader.php';
?>