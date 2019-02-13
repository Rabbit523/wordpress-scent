<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'organic_beauty_template_services_1_theme_setup' ) ) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_template_services_1_theme_setup', 1 );
	function organic_beauty_template_services_1_theme_setup() {
		organic_beauty_add_template(array(
			'layout' => 'services-1',
			'template' => 'services-1',
			'mode'   => 'services',
			'title'  => esc_html__('Services /Style 1/', 'organic-beauty'),
			'thumb_title'  => esc_html__('Large image extra (crop)', 'organic-beauty'),
			'w'		 => 770,
			'h'		 => 550
		));
	}
}

// Template output
if ( !function_exists( 'organic_beauty_template_services_1_output' ) ) {
	function organic_beauty_template_services_1_output($post_options, $post_data) {
		$show_title = !empty($post_data['post_title']);
		$parts = explode('_', $post_options['layout']);
		$style = $parts[0];
		$columns = max(1, min(12, empty($parts[1]) ? (!empty($post_options['columns_count']) ? $post_options['columns_count'] : 1) : (int) $parts[1]));
		if (organic_beauty_param_is_on($post_options['slider'])) {
			?><div class="swiper-slide" data-style="<?php echo esc_attr($post_options['tag_css_wh']); ?>" style="<?php echo esc_attr($post_options['tag_css_wh']); ?>"><div class="sc_services_item_wrap"><?php
		} else if ($columns > 1) {
			?><div class="column-1_<?php echo esc_attr($columns); ?> column_padding_bottom"><?php
		}
		?>
			<div<?php echo !empty($post_options['tag_id']) ? ' id="'.esc_attr($post_options['tag_id']).'"' : ''; ?>
				class="sc_services_item sc_services_item_<?php echo esc_attr($post_options['number']) . ($post_options['number'] % 2 == 1 ? ' odd' : ' even') . ($post_options['number'] == 1 ? ' first' : '') . (!empty($post_options['tag_class']) ? ' '.esc_attr($post_options['tag_class']) : ''); ?>"
				<?php echo (!empty($post_options['tag_css']) ? ' style="'.esc_attr($post_options['tag_css']).'"' : '') 
					. (!organic_beauty_param_is_off($post_options['tag_animation']) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($post_options['tag_animation'])).'"' : ''); ?>>
				<?php

				if(isset($post_data['title_top']) && !empty($post_data['title_top'])) { ?>
					<h5 class="sc_services_item_title_top"><?php organic_beauty_show_layout($post_data['title_top']); ?></h5><?php
				}

				if ($show_title) {
					if ((!isset($post_options['links']) || $post_options['links']) && !empty($post_data['post_link'])) {
						?><h4 class="sc_services_item_title"><a href="<?php echo esc_url($post_data['post_link']); ?>"><?php organic_beauty_show_layout($post_data['post_title']); ?></a></h4><?php
					} else {
						?><h4 class="sc_services_item_title"><?php organic_beauty_show_layout($post_data['post_title']); ?></h4><?php
					}
				}
				?>
				<div class="sc_services_item_featured post_featured<?php echo (!empty($post_data['post_link']) && !organic_beauty_param_is_off($post_options['readmore']) ? ' with_link' : ''); ?>">
						<?php
						organic_beauty_show_layout($post_data['post_thumb']);
						if (!empty($post_data['post_link']) && !organic_beauty_param_is_off($post_options['readmore'])) {
							?><a href="<?php echo esc_url($post_data['post_link']); ?>" class="sc_services_item_readmore sc_button sc_button_style_filled"><?php organic_beauty_show_layout($post_options['readmore']); ?></a><?php
						}
						?>
					</div>
			</div>
		<?php
		if (organic_beauty_param_is_on($post_options['slider'])) {
			?></div></div><?php
		} else if ($columns > 1) {
			?></div><?php
		}
	}
}
?>