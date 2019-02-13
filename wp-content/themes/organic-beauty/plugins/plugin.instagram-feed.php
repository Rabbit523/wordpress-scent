<?php
/* Instagram Feed support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('organic_beauty_instagram_feed_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_instagram_feed_theme_setup', 1 );
	function organic_beauty_instagram_feed_theme_setup() {
		if (organic_beauty_exists_instagram_feed()) {
			if (is_admin()) {
				add_filter( 'organic_beauty_filter_importer_options',				'organic_beauty_instagram_feed_importer_set_options' );
			}
		}
		if (is_admin()) {
			add_filter( 'organic_beauty_filter_importer_required_plugins',		'organic_beauty_instagram_feed_importer_required_plugins', 10, 2 );
			add_filter( 'organic_beauty_filter_required_plugins',					'organic_beauty_instagram_feed_required_plugins' );
		}
	}
}

// Check if Instagram Feed installed and activated
if ( !function_exists( 'organic_beauty_exists_instagram_feed' ) ) {
	function organic_beauty_exists_instagram_feed() {
		return defined('SBIVER');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'organic_beauty_instagram_feed_required_plugins' ) ) {
	//add_filter('organic_beauty_filter_required_plugins',	'organic_beauty_instagram_feed_required_plugins');
	function organic_beauty_instagram_feed_required_plugins($list=array()) {
		if (in_array('instagram_feed', (array)organic_beauty_storage_get('required_plugins')))
			$list[] = array(
					'name' 		=> esc_html__('Instagram Feed', 'organic-beauty'),
					'slug' 		=> 'instagram-feed',
					'required' 	=> false
				);
		return $list;
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check Instagram Feed in the required plugins
if ( !function_exists( 'organic_beauty_instagram_feed_importer_required_plugins' ) ) {
	//add_filter( 'organic_beauty_filter_importer_required_plugins',	'organic_beauty_instagram_feed_importer_required_plugins', 10, 2 );
	function organic_beauty_instagram_feed_importer_required_plugins($not_installed='', $list='') {
		if (organic_beauty_strpos($list, 'instagram_feed')!==false && !organic_beauty_exists_instagram_feed() )
			$not_installed .= '<br>' . esc_html__('Instagram Feed', 'organic-beauty');
		return $not_installed;
	}
}

// Set options for one-click importer
if ( !function_exists( 'organic_beauty_instagram_feed_importer_set_options' ) ) {
	//add_filter( 'organic_beauty_filter_importer_options',	'organic_beauty_instagram_feed_importer_set_options' );
	function organic_beauty_instagram_feed_importer_set_options($options=array()) {
		if ( in_array('instagram_feed', (array)organic_beauty_storage_get('required_plugins')) && organic_beauty_exists_instagram_feed() ) {
			// Add slugs to export options for this plugin
			$options['additional_options'][] = 'sb_instagram_settings';
		}
		return $options;
	}
}
?>