<?php
/**
 * Attachment page
 */
get_header(); 

while ( have_posts() ) { the_post();

	// Move organic_beauty_set_post_views to the javascript - counter will work under cache system
	if (organic_beauty_get_custom_option('use_ajax_views_counter')=='no') {
		organic_beauty_set_post_views(get_the_ID());
	}

	organic_beauty_show_post_layout(
		array(
			'layout' => 'attachment',
			'sidebar' => !organic_beauty_param_is_off(organic_beauty_get_custom_option('show_sidebar_main'))
		)
	);

}

get_footer();
?>