<?php
// Get template args
extract(organic_beauty_template_get_args('post-featured'));

if (!empty($post_data['post_video'])) {
	organic_beauty_show_layout(organic_beauty_get_video_frame($post_data['post_video'], $post_data['post_video_image'] ? $post_data['post_video_image'] : $post_data['post_thumb']));

} else if (!empty($post_data['post_audio'])) {
	if (organic_beauty_get_custom_option('substitute_audio')=='no' || !organic_beauty_in_shortcode_blogger(true))
		organic_beauty_show_layout(organic_beauty_get_audio_frame($post_data['post_audio'], $post_data['post_audio_image'] ? $post_data['post_audio_image'] : $post_data['post_thumb_url']));
	else
		organic_beauty_show_layout($post_data['post_audio']);

} else if (!empty($post_data['post_thumb']) && ($post_data['post_format']!='gallery' || empty($post_data['post_gallery']) || organic_beauty_get_custom_option('gallery_instead_image')=='no')) {
	?>
	<div class="post_thumb" data-image="<?php echo (isset($post_data['post_attachment']) ? esc_url($post_data['post_attachment']) : ''); ?>" data-title="<?php echo esc_attr($post_data['post_title']); ?>">
	<?php
	if ($post_data['post_format']=='link' && $post_data['post_url']!='')
		echo '<a class="hover_icon hover_icon_link" href="'.esc_url($post_data['post_url']).'"'.($post_data['post_url_target'] ? ' target="'.esc_attr($post_data['post_url_target']).'"' : '').'>'.($post_data['post_thumb']).'</a>';
	else if ($post_data['post_link']!='')
		echo '<a class="hover_icon hover_icon_link" href="'.esc_url($post_data['post_link']).'">'.($post_data['post_thumb']).'</a>';
	else
		organic_beauty_show_layout($post_data['post_thumb']); 
	?>
	</div>
	<?php

} else if (!empty($post_data['post_gallery'])) {
	organic_beauty_enqueue_slider();
	organic_beauty_show_layout($post_data['post_gallery']);
}
?>