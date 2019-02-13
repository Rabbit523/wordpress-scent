<?php
/* Instagram Widget support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('organic_beauty_instagram_widget_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_instagram_widget_theme_setup', 1 );
	function organic_beauty_instagram_widget_theme_setup() {
		if (organic_beauty_exists_instagram_widget()) {
			add_action( 'organic_beauty_action_add_styles', 						'organic_beauty_instagram_widget_frontend_scripts' );
		}
		if (is_admin()) {
			add_filter( 'organic_beauty_filter_importer_required_plugins',		'organic_beauty_instagram_widget_importer_required_plugins', 10, 2 );
			add_filter( 'organic_beauty_filter_required_plugins',					'organic_beauty_instagram_widget_required_plugins' );
		}
	}
}

// Check if Instagram Widget installed and activated
if ( !function_exists( 'organic_beauty_exists_instagram_widget' ) ) {
	function organic_beauty_exists_instagram_widget() {
		return function_exists('wpiw_init');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'organic_beauty_instagram_widget_required_plugins' ) ) {
	//add_filter('organic_beauty_filter_required_plugins',	'organic_beauty_instagram_widget_required_plugins');
	function organic_beauty_instagram_widget_required_plugins($list=array()) {
		if (in_array('instagram_widget', (array)organic_beauty_storage_get('required_plugins')))
			$list[] = array(
					'name' 		=> esc_html__('Instagram Widget', 'organic-beauty'),
					'slug' 		=> 'wp-instagram-widget',
					'required' 	=> false
				);
		return $list;
	}
}

// Enqueue custom styles
if ( !function_exists( 'organic_beauty_instagram_widget_frontend_scripts' ) ) {
	//add_action( 'organic_beauty_action_add_styles', 'organic_beauty_instagram_widget_frontend_scripts' );
	function organic_beauty_instagram_widget_frontend_scripts() {
		if (file_exists(organic_beauty_get_file_dir('css/plugin.instagram-widget.css')))
			organic_beauty_enqueue_style( 'organic_beauty-plugin.instagram-widget-style',  organic_beauty_get_file_url('css/plugin.instagram-widget.css'), array(), null );
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check Instagram Widget in the required plugins
if ( !function_exists( 'organic_beauty_instagram_widget_importer_required_plugins' ) ) {
	//add_filter( 'organic_beauty_filter_importer_required_plugins',	'organic_beauty_instagram_widget_importer_required_plugins', 10, 2 );
	function organic_beauty_instagram_widget_importer_required_plugins($not_installed='', $list='') {
		if (organic_beauty_strpos($list, 'instagram_widget')!==false && !organic_beauty_exists_instagram_widget() )
			$not_installed .= '<br>' . esc_html__('WP Instagram Widget', 'organic-beauty');
		return $not_installed;
	}
}
?>