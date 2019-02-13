<?php
if (!function_exists('organic_beauty_theme_shortcodes_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_theme_shortcodes_setup', 1 );
	function organic_beauty_theme_shortcodes_setup() {
		add_filter('organic_beauty_filter_googlemap_styles', 'organic_beauty_theme_shortcodes_googlemap_styles');
	}
}


// Add theme-specific Google map styles
if ( !function_exists( 'organic_beauty_theme_shortcodes_googlemap_styles' ) ) {
	function organic_beauty_theme_shortcodes_googlemap_styles($list) {
		$list['simple']		= esc_html__('Simple', 'organic-beauty');
		$list['greyscale']	= esc_html__('Greyscale', 'organic-beauty');
		$list['inverse']	= esc_html__('Inverse', 'organic-beauty');
		$list['apple']		= esc_html__('Apple', 'organic-beauty');
		return $list;
	}
}
?>