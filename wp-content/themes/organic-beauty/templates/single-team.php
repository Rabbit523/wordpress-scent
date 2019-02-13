<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'organic_beauty_template_single_team_theme_setup' ) ) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_template_single_team_theme_setup', 1 );
	function organic_beauty_template_single_team_theme_setup() {
		organic_beauty_add_template(array(
			'layout' => 'single-team',
			'mode'   => 'single_team',
			'need_content' => true,
			'need_terms' => true,
			'title'  => esc_html__('Single Team member', 'organic-beauty'),
			'thumb_title'  => esc_html__('Large image (crop)', 'organic-beauty'),
			'w'		 => 770,
			'h'		 => 474
		));
	}
}

// Template output
if ( !function_exists( 'organic_beauty_template_single_team_output' ) ) {
	function organic_beauty_template_single_team_output($post_options, $post_data) {
		$post_data['post_views']++;
		$show_title = organic_beauty_get_custom_option('show_post_title')=='yes';
		$title_tag = organic_beauty_get_custom_option('show_page_title')=='yes' ? 'h3' : 'h1';
        $post_meta = get_post_meta($post_data['post_id'], 'organic_beauty_team_data', true);

		organic_beauty_open_wrapper('<article class="' 
				. join(' ', get_post_class('itemscope'
					. ' post_item post_item_single_team'
					. ' post_featured_' . esc_attr($post_options['post_class'])
					. ' post_format_' . esc_attr($post_data['post_format'])))
				. '"'
				. ' itemscope itemtype="http://schema.org/Article'
				. '">');

		if ($show_title && $post_options['location'] == 'center' && organic_beauty_get_custom_option('show_page_title')=='no') {
			?>
			<<?php echo esc_html($title_tag); ?> itemprop="headline" class="post_title entry-title"><?php organic_beauty_show_layout($post_data['post_title']); ?></<?php echo esc_html($title_tag); ?>>
			<?php 
		}

		if (!$post_data['post_protected'] && (
			!empty($post_options['dedicated']) ||
			(organic_beauty_get_custom_option('show_featured_image')=='yes' && $post_data['post_thumb'])	// && $post_data['post_format']!='gallery' && $post_data['post_format']!='image')
		)) {
			?>
			<section class="post_featured single_team_post_featured">
				<?php
				if (!empty($post_options['dedicated'])) {
					organic_beauty_show_layout($post_options['dedicated']);
				} else {
					organic_beauty_enqueue_popup();
					?>
					<div class="post_thumb" data-image="<?php echo esc_url($post_data['post_attachment']); ?>" data-title="<?php echo esc_attr($post_data['post_title']); ?>">
						<a class="hover_icon hover_icon_view" href="<?php echo esc_url($post_data['post_attachment']); ?>" title="<?php echo esc_attr($post_data['post_title']); ?>"><?php organic_beauty_show_layout($post_data['post_thumb']); ?></a>
					</div>
					<?php 
				}
	
				$soc_list = $post_meta['team_member_socials'];
				if (is_array($soc_list) && count($soc_list)>0) {
					$soc_str = '';
					foreach ($soc_list as $sn=>$sl) {
						if (!empty($sl))
							$soc_str .= (!empty($soc_str) ? '|' : '') . ($sn) . '=' . ($sl);
					}
					if (!empty($soc_str)) {
						?><div class="socials_single_team"><?php organic_beauty_show_layout(organic_beauty_sc_socials(array('size'=>"tiny", 'shape'=>'round', 'socials'=>$soc_str))); ?></div><?php
					}
				}
				?>
			</section>

			<section class="single_team_post_description">
				<h2 class="team_title"><?php organic_beauty_show_layout($post_data['post_title']); ?></h2>
				<h6 class="team_position"><?php organic_beauty_show_layout($post_meta['team_member_position']); ?></h6>
				<div class="team_meta"><?php
					if (!empty($post_meta['team_member_email'])) {
						?><p><?php esc_attr_e('E-mail: ', 'organic-beauty'); ?><?php organic_beauty_show_layout($post_meta['team_member_email']); ?></p><?php
					}
					if (!empty($post_meta['team_member_bday'])) {
						?><p><?php esc_attr_e('Date Of Birth: ', 'organic-beauty'); ?><?php organic_beauty_show_layout($post_meta['team_member_bday']); ?></p><?php
					}
				?></div><?php

				if (!empty($post_meta['team_member_brief_info'])) {
					?>
					<div class="team_brief_info">
						<h5 class="team_brief_info_title"><?php esc_attr_e('Brief info', 'organic-beauty'); ?></h5>
						<div class="team_brief_info_text"><?php echo wpautop($post_meta['team_member_brief_info']); ?></div>
					</div>
					<?php
				}
				?>
			</section>
			<?php
		}
		

		organic_beauty_open_wrapper('<section class="post_content'.(!$post_data['post_protected'] && $post_data['post_edit_enable'] ? ' '.esc_attr('post_content_editor_present') : '').'" itemprop="articleBody">');
		
		if ($show_title && $post_options['location'] != 'center' && organic_beauty_get_custom_option('show_page_title')=='no') {
			?>
			<<?php echo esc_html($title_tag); ?> itemprop="name" class="post_title entry-title"><?php organic_beauty_show_layout($post_data['post_title']); ?></<?php echo esc_html($title_tag); ?>>
			<?php 
		}
			
		// Post content
		if ($post_data['post_protected']) { 
			organic_beauty_show_layout($post_data['post_excerpt']);
			echo get_the_password_form(); 
		} else {
			organic_beauty_show_layout(organic_beauty_gap_wrapper(organic_beauty_reviews_wrapper($post_data['post_content'])));
			wp_link_pages( array( 
				'before' => '<nav class="pagination_single"><span class="pager_pages">' . esc_html__( 'Pages:', 'organic-beauty' ) . '</span>', 
				'after' => '</nav>',
				'link_before' => '<span class="pager_numbers">',
				'link_after' => '</span>'
				)
			); 
			if ( organic_beauty_get_custom_option('show_post_tags') == 'yes' && !empty($post_data['post_terms'][$post_data['post_taxonomy_tags']]->terms_links)) {
				?>
				<div class="post_info post_info_bottom">
					<span class="post_info_item post_info_tags"><?php esc_html_e('Tags:', 'organic-beauty'); ?> <?php echo join(', ', $post_data['post_terms'][$post_data['post_taxonomy_tags']]->terms_links); ?></span>
				</div>
				<?php 
			}
		} 

		// Prepare args for all rest template parts
		// This parts not pop args from storage!
		organic_beauty_template_set_args('single-footer', array(
			'post_options' => $post_options,
			'post_data' => $post_data
		));
			
		if (!$post_data['post_protected'] && $post_data['post_edit_enable']) {
			get_template_part(organic_beauty_get_file_slug('templates/_parts/editor-area.php'));
		}

		organic_beauty_close_wrapper();	// .post_content
			
		if (!$post_data['post_protected']) {
			get_template_part(organic_beauty_get_file_slug('templates/_parts/share.php'));
		}

		organic_beauty_close_wrapper();	// .post_item

		if (!$post_data['post_protected']) {
			// Show replated posts
			get_template_part(organic_beauty_get_file_slug('templates/_parts/related-posts.php'));
			// Show comments
			if ( comments_open() || get_comments_number() != 0 ) {
				comments_template();
			}
		}

		// Manually pop args from storage
		// after all single footer templates
		organic_beauty_template_get_args('single-footer');
	}
}
?>