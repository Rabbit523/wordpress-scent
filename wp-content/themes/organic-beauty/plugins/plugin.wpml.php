<?php
/* WPML support functions
------------------------------------------------------------------------------- */

// Check if WPML installed and activated
if ( !function_exists( 'organic_beauty_exists_wpml' ) ) {
	function organic_beauty_exists_wpml() {
		return defined('ICL_SITEPRESS_VERSION') && class_exists('sitepress');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'organic_beauty_wpml_required_plugins' ) ) {
	//add_filter('organic_beauty_filter_required_plugins',	'organic_beauty_wpml_required_plugins');
	function organic_beauty_wpml_required_plugins($list=array()) {
		if (in_array('wpml', (array)organic_beauty_storage_get('required_plugins'))) {
			$path = organic_beauty_get_file_dir('plugins/install/wpml.zip');
			if (file_exists($path)) {
				$list[] = array(
					'name' 		=> esc_html__('WPML', 'organic-beauty'),
					'slug' 		=> 'wpml',
					'source'	=> $path,
					'required' 	=> false
					);
			}
		}
		return $list;
	}
}
?>