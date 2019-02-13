<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'organic_beauty_template_gallery_theme_setup' ) ) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_template_gallery_theme_setup', 1 );
	function organic_beauty_template_gallery_theme_setup() {
		organic_beauty_add_template(array(
			'layout' => 'gallery_2',
			'template' => 'gallery',
			'mode'   => 'blog',
			'need_isotope' => true,
			'title'  => esc_html__('Gallery tile with preview mode /2 columns/', 'organic-beauty'),
			'thumb_title'  => esc_html__('Medium image', 'organic-beauty'),
			'w'		 => 370,
			'h_crop' => 209,
			'h'		 => null
		));
		organic_beauty_add_template(array(
			'layout' => 'gallery_3',
			'template' => 'gallery',
			'mode'   => 'blog',
			'need_isotope' => true,
			'title'  => esc_html__('Gallery tile /3 columns/', 'organic-beauty'),
			'thumb_title'  => esc_html__('Medium image', 'organic-beauty'),
			'w'		 => 370,
			'h_crop' => 209,
			'h'		 => null
		));
		organic_beauty_add_template(array(
			'layout' => 'gallery_4',
			'template' => 'gallery',
			'mode'   => 'blog',
			'need_isotope' => true,
			'title'  => esc_html__('Gallery tile /4 columns/', 'organic-beauty'),
			'thumb_title'  => esc_html__('Medium image', 'organic-beauty'),
			'w'		 => 370,
			'h_crop' => 209,
			'h'		 => null
		));
		// Add template specific scripts
		add_action('organic_beauty_action_blog_scripts', 'organic_beauty_template_gallery_add_scripts');
	}
}

// Add template specific scripts
if (!function_exists('organic_beauty_template_gallery_add_scripts')) {
	//add_action('organic_beauty_action_blog_scripts', 'organic_beauty_template_gallery_add_scripts');
	function organic_beauty_template_gallery_add_scripts($style) {
		if (organic_beauty_substr($style, 0, 8) == 'gallery_') {
			organic_beauty_enqueue_script( 'isotope', organic_beauty_get_file_url('js/jquery.isotope.min.js'), array(), null, true );
			organic_beauty_enqueue_script( 'classie', organic_beauty_get_file_url('js/core.gallery/classie.js'), array(), null, true );
			organic_beauty_enqueue_script( 'imagesloaded', organic_beauty_get_file_url('js/core.gallery/imagesloaded.js'), array(), null, true );
			organic_beauty_enqueue_script( 'organic_beauty-gallery-script', organic_beauty_get_file_url('js/core.gallery/core.gallery.js'), array(), null, true );
			organic_beauty_enqueue_style(  'organic_beauty-gallery-style', organic_beauty_get_file_url('css/core.gallery.css'), array(), null );
		}
	}
}

// Template output
if ( !function_exists( 'organic_beauty_template_gallery_output' ) ) {
	function organic_beauty_template_gallery_output($post_options, $post_data) {
		$show_title = !in_array($post_data['post_format'], array('aside', 'chat', 'status', 'link', 'quote'));
		$parts = explode('_', $post_options['layout']);
		$style = $parts[0];
		$columns = max(1, min(12, empty($post_options['columns_count']) 
									? (empty($parts[1]) ? 1 : (int) $parts[1])
									: $post_options['columns_count']
									));
		$tag = organic_beauty_in_shortcode_blogger(true) ? 'div' : 'article';
		$link_start = !isset($post_options['links']) || $post_options['links'] ? '<a href="'.esc_url($post_data['post_link']).'">' : '';
		$link_end = !isset($post_options['links']) || $post_options['links'] ? '</a>' : '';
		$original_size = !empty($post_data['post_attachment']) ? organic_beauty_getimagesize($post_data['post_attachment']) : array(0,0);
		?>
		<div class="isotope_item isotope_item_<?php echo esc_attr($style); ?> isotope_item_<?php echo esc_attr($post_options['layout']); ?> isotope_column_<?php echo esc_attr($columns); ?>
			<?php
			if ($post_options['filters'] != '') {
				if ($post_options['filters']=='categories' && !empty($post_data['post_terms'][$post_data['post_taxonomy']]->terms_ids))
					echo ' flt_' . esc_attr(join(' flt_', $post_data['post_terms'][$post_data['post_taxonomy']]->terms_ids));
				else if ($post_options['filters']=='tags' && !empty($post_data['post_terms'][$post_data['post_taxonomy_tags']]->terms_ids))
					echo ' flt_' . esc_attr(join(' flt_', $post_data['post_terms'][$post_data['post_taxonomy_tags']]->terms_ids));
			}
			?>"
			data-size="<?php echo intval($original_size[0]) .'x' . intval($original_size[1]); ?>"
			data-src="<?php echo esc_url($post_data['post_attachment']); ?>"
			>
			<<?php organic_beauty_show_layout($tag); ?> class="post_item post_item_<?php echo esc_attr($style); ?> post_item_<?php echo esc_attr($post_options['layout']); ?>
				<?php echo 'post_format_'.esc_attr($post_data['post_format']) 
					. ($post_options['number']%2==0 ? ' even' : ' odd') 
					. ($post_options['number']==0 ? ' first' : '') 
					. ($post_options['number']==$post_options['posts_on_page'] ? ' last' : '');
				?>">
				<div class="post_content isotope_item_content">
					<div class="post_featured <?php echo ($post_data['post_type'] == 'matches' ? 'matches_hover' : '');?>"><?php
						organic_beauty_show_layout($post_data['post_thumb']);
						$new = organic_beauty_get_custom_option('mark_as_new', '', $post_data['post_id'], $post_data['post_type']);			// !!!!!! Get option from specified post
						if ($new && $new > date('Y-m-d')) {
							?><div class="post_mark_new"><?php esc_html_e('NEW', 'organic-beauty'); ?></div><?php
						}
						?>
						<div class="post_details">
							<?php
							$category = !empty($post_data['post_terms'][$post_data['post_taxonomy']]->terms) 
										? ($post_data['post_terms'][$post_data['post_taxonomy']]->terms[0]->link ? '<a href="'.esc_url($post_data['post_terms'][$post_data['post_taxonomy']]->terms[0]->link).'">' : '')
											. ($post_data['post_terms'][$post_data['post_taxonomy']]->terms[0]->name)
											. ($post_data['post_terms'][$post_data['post_taxonomy']]->terms[0]->link ? '</a>' : '')
										: '';
							if (!empty($category)) {			
								?><div class="post_category"><?php organic_beauty_show_layout($category); ?></div><?php
							}
							?>
							<h2 class="post_title"><?php organic_beauty_show_layout($post_data['post_title'], $link_start, $link_end); ?></h2>
							<div class="post_description">
								<?php
								if (!$post_data['post_protected'] && $post_options['info']) {
									$post_options['info_parts'] = array('counters'=>true, 'terms'=>false, 'author' => false, 'tag' => 'p');
									organic_beauty_template_set_args('post-info', array(
										'post_options' => $post_options,
										'post_data' => $post_data
									));
									get_template_part(organic_beauty_get_file_slug('templates/_parts/post-info.php')); 
								}

								organic_beauty_show_layout($post_data['post_excerpt']);

								if ($post_data['post_link'] != '') {
									?><p class="post_buttons"><?php
									if ((!isset($post_options['readmore']) || !organic_beauty_param_is_off($post_options['readmore'])) && !in_array($post_data['post_format'], array('quote', 'link', 'chat', 'aside', 'status'))) {
										?><a href="<?php echo esc_url($post_data['post_link']); ?>" class="sc_button sc_button_style_filled post_readmore"><span class="post_readmore_label"><?php echo !empty($post_options['readmore']) ? trim($post_options['readmore']) : esc_html__('Learn more', 'organic-beauty'); ?></span></a><?php
									}
									?></p><?php
								}
								?>
							</div>
						</div>
						<a href="#" class="hover_icon hover_icon_zoom"></a>
					</div>
				</div>
			</<?php organic_beauty_show_layout($tag); ?>>
		</div>
		<?php
	}
}
?>