<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'organic_beauty_template_testimonials_4_theme_setup' ) ) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_template_testimonials_4_theme_setup', 1 );
	function organic_beauty_template_testimonials_4_theme_setup() {
		organic_beauty_add_template(array(
			'layout' => 'testimonials-4',
			'template' => 'testimonials-4',
			'mode'   => 'testimonials',
			'title'  => esc_html__('Testimonials /Style 4/', 'organic-beauty'),
			'thumb_title'  => esc_html__('Avatar (small)', 'organic-beauty'),
			'w'		 => 75,
			'h'		 => 75
		));
	}
}

// Template output
if ( !function_exists( 'organic_beauty_template_testimonials_4_output' ) ) {
	function organic_beauty_template_testimonials_4_output($post_options, $post_data) {
		$show_title = true;
		$parts = explode('_', $post_options['layout']);
		$style = $parts[0];
		$columns = max(1, min(12, empty($parts[1]) ? (!empty($post_options['columns_count']) ? $post_options['columns_count'] : 1) : (int) $parts[1]));
		if (organic_beauty_param_is_on($post_options['slider'])) {
			?><div class="swiper-slide" data-style="<?php echo esc_attr($post_options['tag_css_wh']); ?>" style="<?php echo esc_attr($post_options['tag_css_wh']); ?>"><?php
		} else if ($columns > 1) {
			?><div class="column-1_<?php echo esc_attr($columns); ?> column_padding_bottom"><?php
		}
		?>
			<div<?php echo !empty($post_options['tag_id']) ? ' id="'.esc_attr($post_options['tag_id']).'"' : ''; ?> class="sc_testimonial_item<?php echo (!empty($post_options['tag_class']) ? ' '.esc_attr($post_options['tag_class']) : ''); ?>"<?php echo !empty($post_options['tag_css']) ? ' style="'.esc_attr($post_options['tag_css']).'"' : '';?>>
				<div class="sc_testimonial_content"><?php organic_beauty_show_layout($post_data['post_content']); ?></div>
				<?php if ($post_options['photo']) { ?>
				<div class="sc_testimonial_avatar"><?php organic_beauty_show_layout($post_options['photo']); ?></div>
				<?php } ?>
				<?php if ($post_options['author']) { ?>
				<div class="sc_testimonial_author"><?php 
					echo (!empty($post_options['link'])
							? '<a href="'.esc_url($post_options['link']).'" class="sc_testimonial_author_name">'.($post_options['author']).'</a>' 
							: '<span class="sc_testimonial_author_name">'.($post_options['author']).'</span>')
						. (!empty($post_options['position'])
							? '<span class="sc_testimonial_author_position">'.($post_options['position']).'</span>'
							: ''); ?></div>
				<?php } ?>
			</div>
		<?php
		if (organic_beauty_param_is_on($post_options['slider']) || $columns > 1) {
			?></div><?php
		}
	}
}
?>