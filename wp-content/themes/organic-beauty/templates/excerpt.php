<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'organic_beauty_template_excerpt_theme_setup' ) ) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_template_excerpt_theme_setup', 1 );
	function organic_beauty_template_excerpt_theme_setup() {
		organic_beauty_add_template(array(
			'layout' => 'excerpt',
			'mode'   => 'blog',
			'title'  => esc_html__('Excerpt', 'organic-beauty'),
			'thumb_title'  => esc_html__('Large image (crop)', 'organic-beauty'),
			'w'		 => 770,
			'h'		 => 474
		));
	}
}

// Template output
if ( !function_exists( 'organic_beauty_template_excerpt_output' ) ) {
	function organic_beauty_template_excerpt_output($post_options, $post_data) {
		$show_title = true;
		$tag = organic_beauty_in_shortcode_blogger(true) ? 'div' : 'article';
		?>
		<<?php organic_beauty_show_layout($tag); ?> <?php post_class('post_item post_item_excerpt post_featured_' . esc_attr($post_options['post_class']) . ' post_format_'.esc_attr($post_data['post_format']) . ($post_options['number']%2==0 ? ' even' : ' odd') . ($post_options['number']==0 ? ' first' : '') . ($post_options['number']==$post_options['posts_on_page']? ' last' : '') . ($post_options['add_view_more'] ? ' viewmore' : '')); ?>>
			<?php
			if ($post_data['post_flags']['sticky']) {
				?><span class="sticky_label"></span><?php
			}

			if ($show_title && $post_options['location'] == 'center' && !empty($post_data['post_title'])) {
				?><h3 class="post_title"><a href="<?php echo esc_url($post_data['post_link']); ?>"><?php organic_beauty_show_layout($post_data['post_title']); ?></a></h3><?php
			}
			
			if (!$post_data['post_protected'] && (!empty($post_options['dedicated']) || $post_data['post_thumb'] || $post_data['post_gallery'] || $post_data['post_video'] || $post_data['post_audio'])) {
				?>
				<div class="post_featured">
				<?php
				if (!empty($post_options['dedicated'])) {
					organic_beauty_show_layout($post_options['dedicated']);
				} else if ($post_data['post_thumb'] || $post_data['post_gallery'] || $post_data['post_video'] || $post_data['post_audio']) {
					organic_beauty_template_set_args('post-featured', array(
						'post_options' => $post_options,
						'post_data' => $post_data
					));
					get_template_part(organic_beauty_get_file_slug('templates/_parts/post-featured.php'));
				}
				?>
				</div>
			<?php
			}
			?>
	
			<div class="post_content clearfix">
				<?php
				if ($show_title && $post_options['location'] != 'center' && !empty($post_data['post_title'])) {
					?><h3 class="post_title"><a href="<?php echo esc_url($post_data['post_link']); ?>"><?php organic_beauty_show_layout($post_data['post_title']); ?></a></h3><?php
				}
				
				if (!$post_data['post_protected'] && $post_options['info']) {
					organic_beauty_template_set_args('post-info', array(
						'post_options' => $post_options,
						'post_data' => $post_data
					));
					get_template_part(organic_beauty_get_file_slug('templates/_parts/post-info.php')); 
				}
				?>
		
				<div class="post_descr">
				<?php
					if ($post_data['post_protected']) {
						organic_beauty_show_layout($post_data['post_excerpt']); 
					} else {
						// Uncomment next rows to show full content in the blogger if descr==0
						//if ($post_data['post_content'] && isset($post_options['descr']) && $post_options['descr']==0 ) {
						//	organic_beauty_show_layout($post_data['post_content']);
						//} else 
						if ($post_data['post_excerpt']) {
							echo in_array($post_data['post_format'], array('quote', 'link', 'chat', 'aside', 'status')) ? $post_data['post_excerpt'] : '<p>'.trim(organic_beauty_strshort($post_data['post_excerpt'], isset($post_options['descr']) ? $post_options['descr'] : organic_beauty_get_custom_option('post_excerpt_maxlength'))).'</p>';
						}
					}
					if (empty($post_options['readmore'])) $post_options['readmore'] = esc_html__('Read more', 'organic-beauty');
					if (!organic_beauty_param_is_off($post_options['readmore']) && !in_array($post_data['post_format'], array('quote', 'link', 'chat', 'aside', 'status'))) {
						organic_beauty_show_layout(organic_beauty_sc_button(array('link'=>$post_data['post_link']), $post_options['readmore']));
					}
				?>
				</div>

			</div>	<!-- /.post_content -->

		</<?php organic_beauty_show_layout($tag); ?>>	<!-- /.post_item -->

	<?php
	}
}
?>