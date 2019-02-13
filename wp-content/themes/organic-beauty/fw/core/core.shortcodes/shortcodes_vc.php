<?php
if (is_admin() 
		|| (isset($_GET['vc_editable']) && $_GET['vc_editable']=='true' )
		|| (isset($_GET['vc_action']) && $_GET['vc_action']=='vc_inline')
	) {
	require_once ORGANIC_BEAUTY_FW_PATH . 'core/core.shortcodes/shortcodes_vc_classes.php';
}

// Width and height params
if ( !function_exists( 'organic_beauty_vc_width' ) ) {
	function organic_beauty_vc_width($w='') {
		return array(
			"param_name" => "width",
			"heading" => esc_html__("Width", 'organic-beauty'),
			"description" => wp_kses_data( __("Width of the element", 'organic-beauty') ),
			"group" => esc_html__('Size &amp; Margins', 'organic-beauty'),
			"value" => $w,
			"type" => "textfield"
		);
	}
}
if ( !function_exists( 'organic_beauty_vc_height' ) ) {
	function organic_beauty_vc_height($h='') {
		return array(
			"param_name" => "height",
			"heading" => esc_html__("Height", 'organic-beauty'),
			"description" => wp_kses_data( __("Height of the element", 'organic-beauty') ),
			"group" => esc_html__('Size &amp; Margins', 'organic-beauty'),
			"value" => $h,
			"type" => "textfield"
		);
	}
}

// Load scripts and styles for VC support
if ( !function_exists( 'organic_beauty_shortcodes_vc_scripts_admin' ) ) {
	//add_action( 'admin_enqueue_scripts', 'organic_beauty_shortcodes_vc_scripts_admin' );
	function organic_beauty_shortcodes_vc_scripts_admin() {
		// Include CSS 
		organic_beauty_enqueue_style ( 'shortcodes_vc_admin-style', organic_beauty_get_file_url('shortcodes/theme.shortcodes_vc_admin.css'), array(), null );
		// Include JS
		organic_beauty_enqueue_script( 'shortcodes_vc_admin-script', organic_beauty_get_file_url('core/core.shortcodes/shortcodes_vc_admin.js'), array('jquery'), null, true );
	}
}

// Load scripts and styles for VC support
if ( !function_exists( 'organic_beauty_shortcodes_vc_scripts_front' ) ) {
	//add_action( 'wp_enqueue_scripts', 'organic_beauty_shortcodes_vc_scripts_front' );
	function organic_beauty_shortcodes_vc_scripts_front() {
		if (organic_beauty_vc_is_frontend()) {
			// Include CSS 
			organic_beauty_enqueue_style ( 'shortcodes_vc_front-style', organic_beauty_get_file_url('shortcodes/theme.shortcodes_vc_front.css'), array(), null );
			// Include JS
			organic_beauty_enqueue_script( 'shortcodes_vc_front-script', organic_beauty_get_file_url('core/core.shortcodes/shortcodes_vc_front.js'), array('jquery'), null, true );
			organic_beauty_enqueue_script( 'shortcodes_vc_theme-script', organic_beauty_get_file_url('shortcodes/theme.shortcodes_vc_front.js'), array('jquery'), null, true );
		}
	}
}

// Add init script into shortcodes output in VC frontend editor
if ( !function_exists( 'organic_beauty_shortcodes_vc_add_init_script' ) ) {
	//add_filter('organic_beauty_shortcode_output', 'organic_beauty_shortcodes_vc_add_init_script', 10, 4);
	function organic_beauty_shortcodes_vc_add_init_script($output, $tag='', $atts=array(), $content='') {
		if ( (isset($_GET['vc_editable']) && $_GET['vc_editable']=='true') && (isset($_POST['action']) && $_POST['action']=='vc_load_shortcode')
				&& ( isset($_POST['shortcodes'][0]['tag']) && $_POST['shortcodes'][0]['tag']==$tag )
		) {
			if (organic_beauty_strpos($output, 'organic_beauty_vc_init_shortcodes')===false) {
				$id = "organic_beauty_vc_init_shortcodes_".str_replace('.', '', mt_rand());
				// Attention! This code will be appended in the shortcode's output
				// to init shortcode after it inserted in the page in the VC Frontend editor
				$holder = 'script';
				$output .= '<'.trim($holder).' id="'.esc_attr($id).'">
						try {
							organic_beauty_init_post_formats();
							organic_beauty_init_shortcodes(jQuery("body").eq(0));
							organic_beauty_scroll_actions();
						} catch (e) { };
					</'.trim($holder).'>';
			}
		}
		return $output;
	}
}

// Return vc_param value
if ( !function_exists( 'organic_beauty_get_vc_param' ) ) {
	function organic_beauty_get_vc_param($prm) {
		return organic_beauty_storage_get_array('vc_params', $prm);
	}
}

// Set vc_param value
if ( !function_exists( 'organic_beauty_set_vc_param' ) ) {
	function organic_beauty_set_vc_param($prm, $val) {
		organic_beauty_storage_set_array('vc_params', $prm, $val);
	}
}


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'organic_beauty_shortcodes_vc_theme_setup' ) ) {
	//if ( organic_beauty_vc_is_frontend() )
	if ( (isset($_GET['vc_editable']) && $_GET['vc_editable']=='true') || (isset($_GET['vc_action']) && $_GET['vc_action']=='vc_inline') )
		add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_shortcodes_vc_theme_setup', 20 );
	else
		add_action( 'organic_beauty_action_after_init_theme', 'organic_beauty_shortcodes_vc_theme_setup' );
	function organic_beauty_shortcodes_vc_theme_setup() {


		// Set dir with theme specific VC shortcodes
		if ( function_exists( 'vc_set_shortcodes_templates_dir' ) ) {
			vc_set_shortcodes_templates_dir( organic_beauty_get_folder_dir('shortcodes/vc' ) );
		}
		
		// Add/Remove params in the standard VC shortcodes
		vc_add_param("vc_row", array(
					"param_name" => "scheme",
					"heading" => esc_html__("Color scheme", 'organic-beauty'),
					"description" => wp_kses_data( __("Select color scheme for this block", 'organic-beauty') ),
					"group" => esc_html__('Color scheme', 'organic-beauty'),
					"class" => "",
					"value" => array_flip(organic_beauty_get_list_color_schemes(true)),
					"type" => "dropdown"
		));
		vc_add_param("vc_row", array(
					"param_name" => "inverse",
					"heading" => esc_html__("Inverse colors", 'organic-beauty'),
					"description" => wp_kses_data( __("Inverse all colors of this block", 'organic-beauty') ),
					"group" => esc_html__('Color scheme', 'organic-beauty'),
					"class" => "",
					"std" => "no",
					"value" => array(esc_html__('Inverse colors', 'organic-beauty') => 'yes'),
					"type" => "checkbox"
		));

		if (organic_beauty_shortcodes_is_used() && class_exists('ORGANIC_BEAUTY_VC_ShortCodeSingle')) {

			// Set VC as main editor for the theme
			vc_set_as_theme( true );
			
			// Enable VC on follow post types
			vc_set_default_editor_post_types( array('page', 'team') );
			
			// Load scripts and styles for VC support
			add_action( 'wp_enqueue_scripts',		'organic_beauty_shortcodes_vc_scripts_front');
			add_action( 'admin_enqueue_scripts',	'organic_beauty_shortcodes_vc_scripts_admin' );

			// Add init script into shortcodes output in VC frontend editor
			add_filter('organic_beauty_shortcode_output', 'organic_beauty_shortcodes_vc_add_init_script', 10, 4);

			organic_beauty_storage_set('vc_params', array(
				
				// Common arrays and strings
				'category' => esc_html__("Organic Beauty shortcodes", 'organic-beauty'),
			
				// Current element id
				'id' => array(
					"param_name" => "id",
					"heading" => esc_html__("Element ID", 'organic-beauty'),
					"description" => wp_kses_data( __("ID for the element", 'organic-beauty') ),
					"group" => esc_html__('ID &amp; Class', 'organic-beauty'),
					"value" => "",
					"type" => "textfield"
				),
			
				// Current element class
				'class' => array(
					"param_name" => "class",
					"heading" => esc_html__("Element CSS class", 'organic-beauty'),
					"description" => wp_kses_data( __("CSS class for the element", 'organic-beauty') ),
					"group" => esc_html__('ID &amp; Class', 'organic-beauty'),
					"value" => "",
					"type" => "textfield"
				),

				// Current element animation
				'animation' => array(
					"param_name" => "animation",
					"heading" => esc_html__("Animation", 'organic-beauty'),
					"description" => wp_kses_data( __("Select animation while object enter in the visible area of page", 'organic-beauty') ),
					"group" => esc_html__('ID &amp; Class', 'organic-beauty'),
					"class" => "",
					"value" => array_flip(organic_beauty_get_sc_param('animations')),
					"type" => "dropdown"
				),
			
				// Current element style
				'css' => array(
					"param_name" => "css",
					"heading" => esc_html__("CSS styles", 'organic-beauty'),
					"description" => wp_kses_data( __("Any additional CSS rules (if need)", 'organic-beauty') ),
					"group" => esc_html__('ID &amp; Class', 'organic-beauty'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
			
				// Margins params
				'margin_top' => array(
					"param_name" => "top",
					"heading" => esc_html__("Top margin", 'organic-beauty'),
					"description" => wp_kses_data( __("Margin above this shortcode", 'organic-beauty') ),
					"group" => esc_html__('Size &amp; Margins', 'organic-beauty'),
					"std" => "inherit",
					"value" => array_flip(organic_beauty_get_sc_param('margins')),
					"type" => "dropdown"
				),
			
				'margin_bottom' => array(
					"param_name" => "bottom",
					"heading" => esc_html__("Bottom margin", 'organic-beauty'),
					"description" => wp_kses_data( __("Margin below this shortcode", 'organic-beauty') ),
					"group" => esc_html__('Size &amp; Margins', 'organic-beauty'),
					"std" => "inherit",
					"value" => array_flip(organic_beauty_get_sc_param('margins')),
					"type" => "dropdown"
				),
			
				'margin_left' => array(
					"param_name" => "left",
					"heading" => esc_html__("Left margin", 'organic-beauty'),
					"description" => wp_kses_data( __("Margin on the left side of this shortcode", 'organic-beauty') ),
					"group" => esc_html__('Size &amp; Margins', 'organic-beauty'),
					"std" => "inherit",
					"value" => array_flip(organic_beauty_get_sc_param('margins')),
					"type" => "dropdown"
				),
				
				'margin_right' => array(
					"param_name" => "right",
					"heading" => esc_html__("Right margin", 'organic-beauty'),
					"description" => wp_kses_data( __("Margin on the right side of this shortcode", 'organic-beauty') ),
					"group" => esc_html__('Size &amp; Margins', 'organic-beauty'),
					"std" => "inherit",
					"value" => array_flip(organic_beauty_get_sc_param('margins')),
					"type" => "dropdown"
				)
			) );
			
			// Add theme-specific shortcodes
			do_action('organic_beauty_action_shortcodes_list_vc');

		}
	}
}
?>