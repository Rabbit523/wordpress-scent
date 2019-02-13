<?php
/**
 * Single post
 */
get_header(); 

$single_style = organic_beauty_storage_get('single_style');
if (empty($single_style)) $single_style = organic_beauty_get_custom_option('single_style');

while ( have_posts() ) { the_post();
	organic_beauty_show_post_layout(
		array(
			'layout' => $single_style,
			'sidebar' => !organic_beauty_param_is_off(organic_beauty_get_custom_option('show_sidebar_main')),
			'content' => organic_beauty_get_template_property($single_style, 'need_content'),
			'terms_list' => organic_beauty_get_template_property($single_style, 'need_terms')
		)
	);
}

get_footer();
?>