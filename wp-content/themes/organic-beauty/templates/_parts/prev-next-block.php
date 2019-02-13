<?php
// Get template args
extract(organic_beauty_template_get_args('prev-next-block'));

if (!$post_data['post_protected']) {
	?>
	<section class="post_featured">
	<?php
	if (!empty($post_options['dedicated'])) {
		organic_beauty_show_layout($post_options['dedicated']);
	} else if ($post_data['post_thumb']) {
		$page_style = organic_beauty_get_custom_option('single_style');
		?>
		<div class="post_thumb post_nav"<?php echo 'single-portfolio-fullscreen'==$page_style ? ' style="background-image:url('.esc_url($post_data['post_attachment']).');"' : ''; ?>>
			<?php 
			if ($page_style!='single-portfolio-fullscreen') organic_beauty_show_layout($post_data['post_thumb']);
			$cur = get_post();
			$args = array(
				'post_type' => $post_data['post_type'],
				'posts_per_page' => -1,
				'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish',
				'ignore_sticky_posts' => true
			);
			$args = organic_beauty_query_add_posts_and_cats($args, '', $post_data['post_type'], 
				!empty($post_data['post_terms'][$post_data['post_taxonomy']]->terms_ids) 
					? join(',', $post_data['post_terms'][$post_data['post_taxonomy']]->terms_ids) 
					: '', $post_data['post_taxonomy']);
			$args = organic_beauty_query_add_sort_order($args);
			
			$q = new WP_Query( $args );

			$prev = $next = null;
			$found = false;
			while ( $q->have_posts() ) { $q->the_post();
				if (!$found) {
					if ($cur->ID == get_the_ID())
						$found = true;
					else
						$prev = get_post(get_the_ID());
				} else {
					$next = get_post(get_the_ID());
					break;
				}
			}
			wp_reset_postdata();

			if ( $prev ) {
				$link = get_permalink($prev->ID).'#top_of_page';
				$desc = organic_beauty_strshort($prev->post_title, 30);
				?>
				<a class="post_nav_item post_nav_prev" href="<?php echo esc_url($link); ?>">
					<span class="post_nav_info">
						<span class="post_nav_info_title"><?php esc_html_e('Previous item', 'organic-beauty'); ?></span>
						<span class="post_nav_info_description"><?php organic_beauty_show_layout($desc); ?></span>
					</span>
				</a>
				<?php
			}
			if ( $next ) {
				$link = get_permalink( $next->ID ).'#top_of_page';
				$desc = organic_beauty_strshort($next->post_title, 30);
				?>
				<a class="post_nav_item post_nav_next" href="<?php echo esc_url($link); ?>">
					<span class="post_nav_info">
						<span class="post_nav_info_title"><?php esc_html_e('Next item', 'organic-beauty'); ?></span>
						<span class="post_nav_info_description"><?php organic_beauty_show_layout($desc); ?></span>
					</span>
				</a>
				<?php
			}
			?>
		</div>
		<?php
	}
	?>
	</section>
	<?php
}
?>