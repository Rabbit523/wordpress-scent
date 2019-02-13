<?php
/* Mail Chimp support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('organic_beauty_mailchimp_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_mailchimp_theme_setup', 1 );
	function organic_beauty_mailchimp_theme_setup() {
		if (organic_beauty_exists_mailchimp()) {
			if (is_admin()) {
				add_filter( 'organic_beauty_filter_importer_options',				'organic_beauty_mailchimp_importer_set_options' );
				add_action( 'organic_beauty_action_importer_params',				'organic_beauty_mailchimp_importer_show_params', 10, 1 );
				add_filter( 'organic_beauty_filter_importer_import_row',			'organic_beauty_mailchimp_importer_check_row', 9, 4);
			}
		}
		if (is_admin()) {
			add_filter( 'organic_beauty_filter_importer_required_plugins',		'organic_beauty_mailchimp_importer_required_plugins', 10, 2 );
			add_filter( 'organic_beauty_filter_required_plugins',					'organic_beauty_mailchimp_required_plugins' );
		}
	}
}

// Check if Instagram Feed installed and activated
if ( !function_exists( 'organic_beauty_exists_mailchimp' ) ) {
	function organic_beauty_exists_mailchimp() {
		return function_exists('mc4wp_load_plugin');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'organic_beauty_mailchimp_required_plugins' ) ) {
	//add_filter('organic_beauty_filter_required_plugins',	'organic_beauty_mailchimp_required_plugins');
	function organic_beauty_mailchimp_required_plugins($list=array()) {
		if (in_array('mailchimp', (array)organic_beauty_storage_get('required_plugins')))
			$list[] = array(
				'name' 		=> esc_html__('MailChimp for WP', 'organic-beauty'),
				'slug' 		=> 'mailchimp-for-wp',
				'required' 	=> false
			);
		return $list;
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check Mail Chimp in the required plugins
if ( !function_exists( 'organic_beauty_mailchimp_importer_required_plugins' ) ) {
	//add_filter( 'organic_beauty_filter_importer_required_plugins',	'organic_beauty_mailchimp_importer_required_plugins', 10, 2 );
	function organic_beauty_mailchimp_importer_required_plugins($not_installed='', $list='') {
		if (organic_beauty_strpos($list, 'mailchimp')!==false && !organic_beauty_exists_mailchimp() )
			$not_installed .= '<br>' . esc_html__('Mail Chimp', 'organic-beauty');
		return $not_installed;
	}
}

// Set options for one-click importer
if ( !function_exists( 'organic_beauty_mailchimp_importer_set_options' ) ) {
	//add_filter( 'organic_beauty_filter_importer_options',	'organic_beauty_mailchimp_importer_set_options' );
	function organic_beauty_mailchimp_importer_set_options($options=array()) {
		if ( in_array('mailchimp', (array)organic_beauty_storage_get('required_plugins')) && organic_beauty_exists_mailchimp() ) {
			// Add slugs to export options for this plugin
			$options['additional_options'][] = 'mc4wp_lite_checkbox';
			$options['additional_options'][] = 'mc4wp_lite_form';
		}
		return $options;
	}
}

// Add checkbox to the one-click importer
if ( !function_exists( 'organic_beauty_mailchimp_importer_show_params' ) ) {
	//add_action( 'organic_beauty_action_importer_params',	'organic_beauty_mailchimp_importer_show_params', 10, 1 );
	function organic_beauty_mailchimp_importer_show_params($importer) {
		if ( organic_beauty_exists_mailchimp() && in_array('mailchimp', (array)organic_beauty_storage_get('required_plugins')) ) {
			$importer->show_importer_params(array(
				'slug' => 'mailchimp',
				'title' => esc_html__('Import MailChimp for WP', 'organic-beauty'),
				'part' => 1
			));
		}
	}
}

// Check if the row will be imported
if ( !function_exists( 'organic_beauty_mailchimp_importer_check_row' ) ) {
	//add_filter('organic_beauty_filter_importer_import_row', 'organic_beauty_mailchimp_importer_check_row', 9, 4);
	function organic_beauty_mailchimp_importer_check_row($flag, $table, $row, $list) {
		if ($flag || strpos($list, 'mailchimp')===false) return $flag;
		if ( organic_beauty_exists_mailchimp() ) {
			if ($table == 'posts')
				$flag = $row['post_type']=='mc4wp-form';
		}
		return $flag;
	}
}
?>