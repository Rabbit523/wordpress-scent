<?php
// Get template args
extract(organic_beauty_template_get_args('counters'));

$show_all_counters = !isset($post_options['counters']);
$counters_tag = is_single() ? 'span' : 'a';

// Views
if ($show_all_counters || organic_beauty_strpos($post_options['counters'], 'views')!==false) {
	?>
	<<?php organic_beauty_show_layout($counters_tag); ?> class="post_counters_item post_counters_views icon-eye" title="<?php echo esc_attr( sprintf(__('Views - %s', 'organic-beauty'), $post_data['post_views']) ); ?>" href="<?php echo esc_url($post_data['post_link']); ?>"><span class="post_counters_number"><?php organic_beauty_show_layout($post_data['post_views']); ?></span><?php if (organic_beauty_strpos($post_options['counters'], 'captions')!==false) echo ' '.esc_html__('Views', 'organic-beauty'); ?></<?php organic_beauty_show_layout($counters_tag); ?>>
	<?php
}

// Comments
if ($show_all_counters || organic_beauty_strpos($post_options['counters'], 'comments')!==false) {
	?>
	<a class="post_counters_item post_counters_comments icon-comment-in" title="<?php echo esc_attr( sprintf(__('Comments - %s', 'organic-beauty'), $post_data['post_comments']) ); ?>" href="<?php echo esc_url($post_data['post_comments_link']); ?>"><span class="post_counters_number"><?php organic_beauty_show_layout($post_data['post_comments']); ?></span><?php if (organic_beauty_strpos($post_options['counters'], 'captions')!==false) echo ' '.esc_html__('Comments', 'organic-beauty'); ?></a>
	<?php 
}
 
// Rating
$rating = $post_data['post_reviews_'.(organic_beauty_get_theme_option('reviews_first')=='author' ? 'author' : 'users')];
if ($rating > 0 && ($show_all_counters || organic_beauty_strpos($post_options['counters'], 'rating')!==false)) { 
	?>
	<<?php organic_beauty_show_layout($counters_tag); ?> class="post_counters_item post_counters_rating icon-star" title="<?php echo esc_attr( sprintf(__('Rating - %s', 'organic-beauty'), $rating) ); ?>" href="<?php echo esc_url($post_data['post_link']); ?>"><span class="post_counters_number"><?php organic_beauty_show_layout($rating); ?></span></<?php organic_beauty_show_layout($counters_tag); ?>>
	<?php
}

// Likes
if ($show_all_counters || organic_beauty_strpos($post_options['counters'], 'likes')!==false) {
	// Load core messages
	organic_beauty_enqueue_messages();
	$likes = isset($_COOKIE['organic_beauty_likes']) ? $_COOKIE['organic_beauty_likes'] : '';
	$allow = organic_beauty_strpos($likes, ','.($post_data['post_id']).',')===false;
	?>
	<a class="post_counters_item post_counters_likes icon-heart <?php echo !empty($allow) ? 'enabled' : 'disabled'; ?>" title="<?php echo !empty($allow) ? esc_attr__('Like', 'organic-beauty') : esc_attr__('Dislike', 'organic-beauty'); ?>" href="#"
		data-postid="<?php echo esc_attr($post_data['post_id']); ?>"
		data-likes="<?php echo esc_attr($post_data['post_likes']); ?>"
		data-title-like="<?php esc_attr_e('Like', 'organic-beauty'); ?>"
		data-title-dislike="<?php esc_attr_e('Dislike', 'organic-beauty'); ?>"><span class="post_counters_number"><?php organic_beauty_show_layout($post_data['post_likes']); ?></span><?php if (organic_beauty_strpos($post_options['counters'], 'captions')!==false) echo ' '.esc_html__('Likes', 'organic-beauty'); ?></a>
	<?php
}

// Edit page link
if (organic_beauty_strpos($post_options['counters'], 'edit')!==false) {
	edit_post_link( esc_html__( 'Edit', 'organic-beauty' ), '<span class="post_edit edit-link">', '</span>' );
}

// Markup for search engines
if (is_single() && organic_beauty_strpos($post_options['counters'], 'markup')!==false) {
	?>
	<meta itemprop="interactionCount" content="User<?php echo esc_attr(organic_beauty_strpos($post_options['counters'],'comments')!==false ? 'Comments' : 'PageVisits'); ?>:<?php echo esc_attr(organic_beauty_strpos($post_options['counters'], 'comments')!==false ? $post_data['post_comments'] : $post_data['post_views']); ?>" />
	<?php
}
?>