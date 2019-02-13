<?php
/**
 * Organic Beauty Framework: Services support
 *
 * @package	organic_beauty
 * @since	organic_beauty 1.0
 */

// Theme init
if (!function_exists('organic_beauty_services_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_services_theme_setup',1 );
	function organic_beauty_services_theme_setup() {
		
		// Detect current page type, taxonomy and title (for custom post_types use priority < 10 to fire it handles early, than for standard post types)
		add_filter('organic_beauty_filter_get_blog_type',			'organic_beauty_services_get_blog_type', 9, 2);
		add_filter('organic_beauty_filter_get_blog_title',		'organic_beauty_services_get_blog_title', 9, 2);
		add_filter('organic_beauty_filter_get_current_taxonomy',	'organic_beauty_services_get_current_taxonomy', 9, 2);
		add_filter('organic_beauty_filter_is_taxonomy',			'organic_beauty_services_is_taxonomy', 9, 2);
		add_filter('organic_beauty_filter_get_stream_page_title',	'organic_beauty_services_get_stream_page_title', 9, 2);
		add_filter('organic_beauty_filter_get_stream_page_link',	'organic_beauty_services_get_stream_page_link', 9, 2);
		add_filter('organic_beauty_filter_get_stream_page_id',	'organic_beauty_services_get_stream_page_id', 9, 2);
		add_filter('organic_beauty_filter_query_add_filters',		'organic_beauty_services_query_add_filters', 9, 2);
		add_filter('organic_beauty_filter_detect_inheritance_key','organic_beauty_services_detect_inheritance_key', 9, 1);

		// Extra column for services lists
		if (organic_beauty_get_theme_option('show_overriden_posts')=='yes') {
			add_filter('manage_edit-services_columns',			'organic_beauty_post_add_options_column', 9);
			add_filter('manage_services_posts_custom_column',	'organic_beauty_post_fill_options_column', 9, 2);
		}

		// Register shortcodes [trx_services] and [trx_services_item]
		add_action('organic_beauty_action_shortcodes_list',		'organic_beauty_services_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_services_reg_shortcodes_vc');
		
		// Add supported data types
		organic_beauty_theme_support_pt('services');
		organic_beauty_theme_support_tx('services_group');
	}
}

if ( !function_exists( 'organic_beauty_services_settings_theme_setup2' ) ) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_services_settings_theme_setup2', 3 );
	function organic_beauty_services_settings_theme_setup2() {
		// Add post type 'services' and taxonomy 'services_group' into theme inheritance list
		organic_beauty_add_theme_inheritance( array('services' => array(
			'stream_template' => 'blog-services',
			'single_template' => 'single-service',
			'taxonomy' => array('services_group'),
			'taxonomy_tags' => array(),
			'post_type' => array('services'),
			'override' => 'custom'
			) )
		);
	}
}



// Return true, if current page is services page
if ( !function_exists( 'organic_beauty_is_services_page' ) ) {
	function organic_beauty_is_services_page() {
		$is = in_array(organic_beauty_storage_get('page_template'), array('blog-services', 'single-service'));
		if (!$is) {
			if (!organic_beauty_storage_empty('pre_query'))
				$is = organic_beauty_storage_call_obj_method('pre_query', 'get', 'post_type')=='services' 
						|| organic_beauty_storage_call_obj_method('pre_query', 'is_tax', 'services_group') 
						|| (organic_beauty_storage_call_obj_method('pre_query', 'is_page') 
								&& ($id=organic_beauty_get_template_page_id('blog-services')) > 0 
								&& $id==organic_beauty_storage_get_obj_property('pre_query', 'queried_object_id', 0) 
							);
			else
				$is = get_query_var('post_type')=='services' 
						|| is_tax('services_group') 
						|| (is_page() && ($id=organic_beauty_get_template_page_id('blog-services')) > 0 && $id==get_the_ID());
		}
		return $is;
	}
}

// Filter to detect current page inheritance key
if ( !function_exists( 'organic_beauty_services_detect_inheritance_key' ) ) {
	//add_filter('organic_beauty_filter_detect_inheritance_key',	'organic_beauty_services_detect_inheritance_key', 9, 1);
	function organic_beauty_services_detect_inheritance_key($key) {
		if (!empty($key)) return $key;
		return organic_beauty_is_services_page() ? 'services' : '';
	}
}

// Filter to detect current page slug
if ( !function_exists( 'organic_beauty_services_get_blog_type' ) ) {
	//add_filter('organic_beauty_filter_get_blog_type',	'organic_beauty_services_get_blog_type', 9, 2);
	function organic_beauty_services_get_blog_type($page, $query=null) {
		if (!empty($page)) return $page;
		if ($query && $query->is_tax('services_group') || is_tax('services_group'))
			$page = 'services_category';
		else if ($query && $query->get('post_type')=='services' || get_query_var('post_type')=='services')
			$page = $query && $query->is_single() || is_single() ? 'services_item' : 'services';
		return $page;
	}
}

// Filter to detect current page title
if ( !function_exists( 'organic_beauty_services_get_blog_title' ) ) {
	//add_filter('organic_beauty_filter_get_blog_title',	'organic_beauty_services_get_blog_title', 9, 2);
	function organic_beauty_services_get_blog_title($title, $page) {
		if (!empty($title)) return $title;
		if ( organic_beauty_strpos($page, 'services')!==false ) {
			if ( $page == 'services_category' ) {
				$term = get_term_by( 'slug', get_query_var( 'services_group' ), 'services_group', OBJECT);
				$title = $term->name;
			} else if ( $page == 'services_item' ) {
				$title = organic_beauty_get_post_title();
			} else {
				$title = esc_html__('All services', 'organic-beauty');
			}
		}
		return $title;
	}
}

// Filter to detect stream page title
if ( !function_exists( 'organic_beauty_services_get_stream_page_title' ) ) {
	//add_filter('organic_beauty_filter_get_stream_page_title',	'organic_beauty_services_get_stream_page_title', 9, 2);
	function organic_beauty_services_get_stream_page_title($title, $page) {
		if (!empty($title)) return $title;
		if (organic_beauty_strpos($page, 'services')!==false) {
			if (($page_id = organic_beauty_services_get_stream_page_id(0, $page=='services' ? 'blog-services' : $page)) > 0)
				$title = organic_beauty_get_post_title($page_id);
			else
				$title = esc_html__('All services', 'organic-beauty');				
		}
		return $title;
	}
}

// Filter to detect stream page ID
if ( !function_exists( 'organic_beauty_services_get_stream_page_id' ) ) {
	//add_filter('organic_beauty_filter_get_stream_page_id',	'organic_beauty_services_get_stream_page_id', 9, 2);
	function organic_beauty_services_get_stream_page_id($id, $page) {
		if (!empty($id)) return $id;
		if (organic_beauty_strpos($page, 'services')!==false) $id = organic_beauty_get_template_page_id('blog-services');
		return $id;
	}
}

// Filter to detect stream page URL
if ( !function_exists( 'organic_beauty_services_get_stream_page_link' ) ) {
	//add_filter('organic_beauty_filter_get_stream_page_link',	'organic_beauty_services_get_stream_page_link', 9, 2);
	function organic_beauty_services_get_stream_page_link($url, $page) {
		if (!empty($url)) return $url;
		if (organic_beauty_strpos($page, 'services')!==false) {
			$id = organic_beauty_get_template_page_id('blog-services');
			if ($id) $url = get_permalink($id);
		}
		return $url;
	}
}

// Filter to detect current taxonomy
if ( !function_exists( 'organic_beauty_services_get_current_taxonomy' ) ) {
	//add_filter('organic_beauty_filter_get_current_taxonomy',	'organic_beauty_services_get_current_taxonomy', 9, 2);
	function organic_beauty_services_get_current_taxonomy($tax, $page) {
		if (!empty($tax)) return $tax;
		if ( organic_beauty_strpos($page, 'services')!==false ) {
			$tax = 'services_group';
		}
		return $tax;
	}
}

// Return taxonomy name (slug) if current page is this taxonomy page
if ( !function_exists( 'organic_beauty_services_is_taxonomy' ) ) {
	//add_filter('organic_beauty_filter_is_taxonomy',	'organic_beauty_services_is_taxonomy', 9, 2);
	function organic_beauty_services_is_taxonomy($tax, $query=null) {
		if (!empty($tax))
			return $tax;
		else 
			return $query && $query->get('services_group')!='' || is_tax('services_group') ? 'services_group' : '';
	}
}

// Add custom post type and/or taxonomies arguments to the query
if ( !function_exists( 'organic_beauty_services_query_add_filters' ) ) {
	//add_filter('organic_beauty_filter_query_add_filters',	'organic_beauty_services_query_add_filters', 9, 2);
	function organic_beauty_services_query_add_filters($args, $filter) {
		if ($filter == 'services') {
			$args['post_type'] = 'services';
		}
		return $args;
	}
}





// ---------------------------------- [trx_services] ---------------------------------------

/*
[trx_services id="unique_id" columns="4" count="4" style="services-1|services-2|..." title="Block title" subtitle="xxx" description="xxxxxx"]
	[trx_services_item icon="url" title="Item title" description="Item description" link="url" link_caption="Link text"]
	[trx_services_item icon="url" title="Item title" description="Item description" link="url" link_caption="Link text"]
[/trx_services]
*/
if ( !function_exists( 'organic_beauty_sc_services' ) ) {
	function organic_beauty_sc_services($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "services-1",
			"columns" => 4,
			"slider" => "no",
			"slides_space" => 0,
			"controls" => "no",
			"interval" => "",
			"autoheight" => "no",
			"equalheight" => "no",
			"align" => "",
			"custom" => "no",
			"type" => "images",	// icons | images
			"ids" => "",
			"cat" => "",
			"count" => 4,
			"offset" => "",
			"orderby" => "date",
			"order" => "desc",
			"readmore" => esc_html__('Learn more', 'organic-beauty'),
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"link_caption" => esc_html__('Learn more', 'organic-beauty'),
			"link" => '',
			"scheme" => '',
			"image" => '',
			"image_align" => '',
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"width" => "",
			"height" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
	
		if (organic_beauty_param_is_off($slider) && $columns > 1 && $style == 'services-5' && !empty($image)) $columns = 2;
		if (!empty($image)) {
			if ($image > 0) {
				$attach = wp_get_attachment_image_src( $image, 'full' );
				if (isset($attach[0]) && $attach[0]!='')
					$image = $attach[0];
			}
		}

		if (empty($id)) $id = "sc_services_".str_replace('.', '', mt_rand());
		if (empty($width)) $width = "100%";
		if (!empty($height) && organic_beauty_param_is_on($autoheight)) $autoheight = "no";
		if (empty($interval)) $interval = mt_rand(5000, 10000);
		
		$class .= ($class ? ' ' : '') . organic_beauty_get_css_position_as_classes($top, $right, $bottom, $left);

		$ws = organic_beauty_get_css_dimensions_from_values($width);
		$hs = organic_beauty_get_css_dimensions_from_values('', $height);
		$css .= ($hs) . ($ws);

		$columns = max(1, min(12, (int) $columns));
		$count = max(1, (int) $count);
		if (organic_beauty_param_is_off($custom) && $count < $columns) $columns = $count;

		if (organic_beauty_param_is_on($slider)) organic_beauty_enqueue_slider('swiper');

		organic_beauty_storage_set('sc_services_data', array(
			'id' => $id,
            'style' => $style,
            'type' => $type,
            'columns' => $columns,
            'counter' => 0,
            'slider' => $slider,
            'css_wh' => $ws . $hs,
            'readmore' => $readmore
            )
        );
		
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'_wrap"' : '') 
						. ' class="sc_services_wrap'
						. ($scheme && !organic_beauty_param_is_off($scheme) && !organic_beauty_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
						.'">'
					. '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
						. ' class="sc_services'
							. ' sc_services_style_'.esc_attr($style)
							. ' sc_services_type_'.esc_attr($type)
							. ' ' . esc_attr(organic_beauty_get_template_property($style, 'container_classes'))
							. (!empty($class) ? ' '.esc_attr($class) : '')
							. ($align!='' && $align!='none' ? ' align'.esc_attr($align) : '')
							. '"'
						. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
						. (!organic_beauty_param_is_off($equalheight) ? ' data-equal-height=".sc_services_item"' : '')
						. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
					. '>'
					. (!empty($subtitle) ? '<h6 class="sc_services_subtitle sc_item_subtitle">' . trim(organic_beauty_strmacros($subtitle)) . '</h6>' : '')
					. (!empty($title) ? '<h2 class="sc_services_title sc_item_title' . (empty($description) ? ' sc_item_title_without_descr' : ' sc_item_title_without_descr') . '">' . trim(organic_beauty_strmacros($title)) . '</h2>' : '')
					. (!empty($description) ? '<div class="sc_services_descr sc_item_descr">' . trim(organic_beauty_strmacros($description)) . '</div>' : '')
					. (organic_beauty_param_is_on($slider) 
						? ('<div class="sc_slider_swiper swiper-slider-container'
										. ' ' . esc_attr(organic_beauty_get_slider_controls_classes($controls))
										. (organic_beauty_param_is_on($autoheight) ? ' sc_slider_height_auto' : '')
										. ($hs ? ' sc_slider_height_fixed' : '')
										. '"'
									. (!empty($width) && organic_beauty_strpos($width, '%')===false ? ' data-old-width="' . esc_attr($width) . '"' : '')
									. (!empty($height) && organic_beauty_strpos($height, '%')===false ? ' data-old-height="' . esc_attr($height) . '"' : '')
									. ((int) $interval > 0 ? ' data-interval="'.esc_attr($interval).'"' : '')
									. ($columns > 1 ? ' data-slides-per-view="' . esc_attr($columns) . '"' : '')
									. ($slides_space > 0 ? ' data-slides-space="' . esc_attr($slides_space) . '"' : '')
									. ' data-slides-min-width="250"'
								. '>'
							. '<div class="slides swiper-wrapper">')
						: ($columns > 1 
							? ($style == 'services-5' && !empty($image) 
								? '<div class="sc_service_container sc_align_'.esc_attr($image_align).'">'
									. '<div class="sc_services_image"><img src="'.esc_url($image).'" alt=""></div>' 
								: '')
								. '<div class="sc_columns columns_wrap">' 
							: '')
						);
	
		if (organic_beauty_param_is_on($custom) && $content) {
			$output .= do_shortcode($content);
		} else {
			global $post;
	
			if (!empty($ids)) {
				$posts = explode(',', $ids);
				$count = count($posts);
			}
			
			$args = array(
				'post_type' => 'services',
				'post_status' => 'publish',
				'posts_per_page' => $count,
				'ignore_sticky_posts' => true,
				'order' => $order=='asc' ? 'asc' : 'desc',
				'readmore' => $readmore
			);
		
			if ($offset > 0 && empty($ids)) {
				$args['offset'] = $offset;
			}
		
			$args = organic_beauty_query_add_sort_order($args, $orderby, $order);
			$args = organic_beauty_query_add_posts_and_cats($args, $ids, 'services', $cat, 'services_group');
			
			$query = new WP_Query( $args );
	
			$post_number = 0;
				
			while ( $query->have_posts() ) { 
				$query->the_post();
				$post_number++;
				$args = array(
					'layout' => $style,
					'show' => false,
					'number' => $post_number,
					'posts_on_page' => ($count > 0 ? $count : $query->found_posts),
					"descr" => organic_beauty_get_custom_option('post_excerpt_maxlength'.($columns > 1 ? '_masonry' : '')),
					"orderby" => $orderby,
					'content' => false,
					'terms_list' => false,
					'readmore' => $readmore,
					'tag_type' => $type,
					'columns_count' => $columns,
					'slider' => $slider,
					'tag_id' => $id ? $id . '_' . $post_number : '',
					'tag_class' => '',
					'tag_animation' => '',
					'tag_css' => '',
					'tag_css_wh' => $ws . $hs
				);
				$output .= organic_beauty_show_post_layout($args);
			}
			wp_reset_postdata();
		}
	
		if (organic_beauty_param_is_on($slider)) {
			$output .= '</div>'
				. '<div class="sc_slider_controls_wrap"><a class="sc_slider_prev" href="#"></a><a class="sc_slider_next" href="#"></a></div>'
				. '<div class="sc_slider_pagination_wrap"></div>'
				. '</div>';
		} else if ($columns > 1) {
			$output .= '</div>';
			if ($style == 'services-5' && !empty($image))
				$output .= '</div>';
		}

		$output .=  (!empty($link) ? '<div class="sc_services_button sc_item_button">'.organic_beauty_do_shortcode('[trx_button link="'.esc_url($link).'"]'.esc_html($link_caption).'[/trx_button]').'</div>' : '')
					. '</div><!-- /.sc_services -->'
				. '</div><!-- /.sc_services_wrap -->';
	
		// Add template specific scripts and styles
		do_action('organic_beauty_action_blog_scripts', $style);
	
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_services', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_services', 'organic_beauty_sc_services');
}


if ( !function_exists( 'organic_beauty_sc_services_item' ) ) {
	function organic_beauty_sc_services_item($atts, $content=null) {
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts( array(
			// Individual params
			"icon" => "",
			"image" => "",
			"title" => "",
			"title_top" => "",
			"link" => "",
			"readmore" => "(none)",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => ""
		), $atts)));
	
		organic_beauty_storage_inc_array('sc_services_data', 'counter');

		$id = $id ? $id : (organic_beauty_storage_get_array('sc_services_data', 'id') ? organic_beauty_storage_get_array('sc_services_data', 'id') . '_' . organic_beauty_storage_get_array('sc_services_data', 'counter') : '');

		$descr = trim(chop(do_shortcode($content)));
		$readmore = $readmore=='(none)' ? organic_beauty_storage_get_array('sc_services_data', 'readmore') : $readmore;

		$type = organic_beauty_storage_get_array('sc_services_data', 'type');
		if (!empty($icon)) {
			$type = 'icons';
		} else if (!empty($image)) {
			$type = 'images';
			if ($image > 0) {
				$attach = wp_get_attachment_image_src( $image, 'full' );
				if (isset($attach[0]) && $attach[0]!='')
					$image = $attach[0];
			}
			$thumb_sizes = organic_beauty_get_thumb_sizes(array('layout' => organic_beauty_storage_get_array('sc_services_data', 'style')));
			$image = organic_beauty_get_resized_image_tag($image, $thumb_sizes['w'], $thumb_sizes['h']);
		}
	
		$post_data = array(
			'post_title' => $title,
			'title_top' => $title_top,
			'post_excerpt' => $descr,
			'post_thumb' => $image,
			'post_icon' => $icon,
			'post_link' => $link,
			'post_protected' => false,
			'post_format' => 'standard'
		);
		$args = array(
			'layout' => organic_beauty_storage_get_array('sc_services_data', 'style'),
			'number' => organic_beauty_storage_get_array('sc_services_data', 'counter'),
			'columns_count' => organic_beauty_storage_get_array('sc_services_data', 'columns'),
			'slider' => organic_beauty_storage_get_array('sc_services_data', 'slider'),
			'show' => false,
			'descr'  => -1,		// -1 - don't strip tags, 0 - strip_tags, >0 - strip_tags and truncate string
			'readmore' => $readmore,
			'tag_type' => $type,
			'tag_id' => $id,
			'tag_class' => $class,
			'tag_animation' => $animation,
			'tag_css' => $css,
			'tag_css_wh' => organic_beauty_storage_get_array('sc_services_data', 'css_wh')
		);
		$output = organic_beauty_show_post_layout($args, $post_data);
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_services_item', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_services_item', 'organic_beauty_sc_services_item');
}
// ---------------------------------- [/trx_services] ---------------------------------------



// Add [trx_services] and [trx_services_item] in the shortcodes list
if (!function_exists('organic_beauty_services_reg_shortcodes')) {
	//add_filter('organic_beauty_action_shortcodes_list',	'organic_beauty_services_reg_shortcodes');
	function organic_beauty_services_reg_shortcodes() {
		if (organic_beauty_storage_isset('shortcodes')) {

			$services_groups = organic_beauty_get_list_terms(false, 'services_group');
			$services_styles = organic_beauty_get_list_templates('services');
			$controls 		 = organic_beauty_get_list_slider_controls();

			organic_beauty_sc_map_after('trx_section', array(

				// Services
				"trx_services" => array(
					"title" => esc_html__("Services", 'organic-beauty'),
					"desc" => wp_kses_data( __("Insert services list in your page (post)", 'organic-beauty') ),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"title" => array(
							"title" => esc_html__("Title", 'organic-beauty'),
							"desc" => wp_kses_data( __("Title for the block", 'organic-beauty') ),
							"value" => "",
							"type" => "text"
						),
						"subtitle" => array(
							"title" => esc_html__("Subtitle", 'organic-beauty'),
							"desc" => wp_kses_data( __("Subtitle for the block", 'organic-beauty') ),
							"value" => "",
							"type" => "text"
						),
						"description" => array(
							"title" => esc_html__("Description", 'organic-beauty'),
							"desc" => wp_kses_data( __("Short description for the block", 'organic-beauty') ),
							"value" => "",
							"type" => "textarea"
						),
						"style" => array(
							"title" => esc_html__("Services style", 'organic-beauty'),
							"desc" => wp_kses_data( __("Select style to display services list", 'organic-beauty') ),
							"value" => "services-1",
							"type" => "select",
							"options" => $services_styles
						),
						"columns" => array(
							"title" => esc_html__("Columns", 'organic-beauty'),
							"desc" => wp_kses_data( __("How many columns use to show services list", 'organic-beauty') ),
							"value" => 4,
							"min" => 2,
							"max" => 6,
							"step" => 1,
							"type" => "spinner"
						),
						"scheme" => array(
							"title" => esc_html__("Color scheme", 'organic-beauty'),
							"desc" => wp_kses_data( __("Select color scheme for this block", 'organic-beauty') ),
							"value" => "",
							"type" => "checklist",
							"options" => organic_beauty_get_sc_param('schemes')
						),
						"slider" => array(
							"title" => esc_html__("Slider", 'organic-beauty'),
							"desc" => wp_kses_data( __("Use slider to show services", 'organic-beauty') ),
							"value" => "no",
							"type" => "switch",
							"options" => organic_beauty_get_sc_param('yes_no')
						),
						"controls" => array(
							"title" => esc_html__("Controls", 'organic-beauty'),
							"desc" => wp_kses_data( __("Slider controls style and position", 'organic-beauty') ),
							"dependency" => array(
								'slider' => array('yes')
							),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $controls
						),
						"slides_space" => array(
							"title" => esc_html__("Space between slides", 'organic-beauty'),
							"desc" => wp_kses_data( __("Size of space (in px) between slides", 'organic-beauty') ),
							"dependency" => array(
								'slider' => array('yes')
							),
							"value" => 0,
							"min" => 0,
							"max" => 100,
							"step" => 10,
							"type" => "spinner"
						),
						"interval" => array(
							"title" => esc_html__("Slides change interval", 'organic-beauty'),
							"desc" => wp_kses_data( __("Slides change interval (in milliseconds: 1000ms = 1s)", 'organic-beauty') ),
							"dependency" => array(
								'slider' => array('yes')
							),
							"value" => 7000,
							"step" => 500,
							"min" => 0,
							"type" => "spinner"
						),
						"autoheight" => array(
							"title" => esc_html__("Autoheight", 'organic-beauty'),
							"desc" => wp_kses_data( __("Change whole slider's height (make it equal current slide's height)", 'organic-beauty') ),
							"dependency" => array(
								'slider' => array('yes')
							),
							"value" => "yes",
							"type" => "switch",
							"options" => organic_beauty_get_sc_param('yes_no')
						),
						"align" => array(
							"title" => esc_html__("Alignment", 'organic-beauty'),
							"desc" => wp_kses_data( __("Alignment of the services block", 'organic-beauty') ),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => organic_beauty_get_sc_param('align')
						),
						"custom" => array(
							"title" => esc_html__("Custom", 'organic-beauty'),
							"desc" => wp_kses_data( __("Allow get services items from inner shortcodes (custom) or get it from specified group (cat)", 'organic-beauty') ),
							"divider" => true,
							"value" => "no",
							"type" => "switch",
							"options" => organic_beauty_get_sc_param('yes_no')
						),
						"cat" => array(
							"title" => esc_html__("Categories", 'organic-beauty'),
							"desc" => wp_kses_data( __("Select categories (groups) to show services list. If empty - select services from any category (group) or from IDs list", 'organic-beauty') ),
							"dependency" => array(
								'custom' => array('no')
							),
							"divider" => true,
							"value" => "",
							"type" => "select",
							"style" => "list",
							"multiple" => true,
							"options" => organic_beauty_array_merge(array(0 => esc_html__('- Select category -', 'organic-beauty')), $services_groups)
						),
						"count" => array(
							"title" => esc_html__("Number of posts", 'organic-beauty'),
							"desc" => wp_kses_data( __("How many posts will be displayed? If used IDs - this parameter ignored.", 'organic-beauty') ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => 4,
							"min" => 1,
							"max" => 100,
							"type" => "spinner"
						),
						"offset" => array(
							"title" => esc_html__("Offset before select posts", 'organic-beauty'),
							"desc" => wp_kses_data( __("Skip posts before select next part.", 'organic-beauty') ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => 0,
							"min" => 0,
							"type" => "spinner"
						),
						"orderby" => array(
							"title" => esc_html__("Post order by", 'organic-beauty'),
							"desc" => wp_kses_data( __("Select desired posts sorting method", 'organic-beauty') ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "date",
							"type" => "select",
							"options" => organic_beauty_get_sc_param('sorting')
						),
						"order" => array(
							"title" => esc_html__("Post order", 'organic-beauty'),
							"desc" => wp_kses_data( __("Select desired posts order", 'organic-beauty') ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "desc",
							"type" => "switch",
							"size" => "big",
							"options" => organic_beauty_get_sc_param('ordering')
						),
						"ids" => array(
							"title" => esc_html__("Post IDs list", 'organic-beauty'),
							"desc" => wp_kses_data( __("Comma separated list of posts ID. If set - parameters above are ignored!", 'organic-beauty') ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "",
							"type" => "text"
						),
						"readmore" => array(
							"title" => esc_html__("Read more", 'organic-beauty'),
							"desc" => wp_kses_data( __("Caption for the Read more link (if empty - link not showed)", 'organic-beauty') ),
							"value" => "",
							"type" => "text"
						),
						"link" => array(
							"title" => esc_html__("Button URL", 'organic-beauty'),
							"desc" => wp_kses_data( __("Link URL for the button at the bottom of the block", 'organic-beauty') ),
							"value" => "",
							"type" => "text"
						),
						"link_caption" => array(
							"title" => esc_html__("Button caption", 'organic-beauty'),
							"desc" => wp_kses_data( __("Caption for the button at the bottom of the block", 'organic-beauty') ),
							"value" => "",
							"type" => "text"
						),
						"width" => organic_beauty_shortcodes_width(),
						"height" => organic_beauty_shortcodes_height(),
						"top" => organic_beauty_get_sc_param('top'),
						"bottom" => organic_beauty_get_sc_param('bottom'),
						"left" => organic_beauty_get_sc_param('left'),
						"right" => organic_beauty_get_sc_param('right'),
						"id" => organic_beauty_get_sc_param('id'),
						"class" => organic_beauty_get_sc_param('class'),
						"animation" => organic_beauty_get_sc_param('animation'),
						"css" => organic_beauty_get_sc_param('css')
					),
					"children" => array(
						"name" => "trx_services_item",
						"title" => esc_html__("Service item", 'organic-beauty'),
						"desc" => wp_kses_data( __("Service item", 'organic-beauty') ),
						"container" => true,
						"params" => array(
							"title" => array(
								"title" => esc_html__("Title", 'organic-beauty'),
								"desc" => wp_kses_data( __("Item's title", 'organic-beauty') ),
								"divider" => true,
								"value" => "",
								"type" => "text"
							),
							"title_top" => array(
								"title" => esc_html__("Top Title", 'organic-beauty'),
								"desc" => wp_kses_data( __("Item's top title", 'organic-beauty') ),
								"divider" => true,
								"value" => "",
								"type" => "text"
							),
							"image" => array(
								"title" => esc_html__("Item's image", 'organic-beauty'),
								"desc" => wp_kses_data( __("Item's image (if icon not selected)", 'organic-beauty') ),
								"value" => "",
								"readonly" => false,
								"type" => "media"
							),
							"link" => array(
								"title" => esc_html__("Link", 'organic-beauty'),
								"desc" => wp_kses_data( __("Link on service's item page", 'organic-beauty') ),
								"divider" => true,
								"value" => "",
								"type" => "text"
							),
							"readmore" => array(
								"title" => esc_html__("Read more", 'organic-beauty'),
								"desc" => wp_kses_data( __("Caption for the Read more link (if empty - link not showed)", 'organic-beauty') ),
								"value" => "",
								"type" => "text"
							),
							"_content_" => array(
								"title" => esc_html__("Description", 'organic-beauty'),
								"desc" => wp_kses_data( __("Item's short description", 'organic-beauty') ),
								"divider" => true,
								"rows" => 4,
								"value" => "",
								"type" => "textarea"
							),
							"id" => organic_beauty_get_sc_param('id'),
							"class" => organic_beauty_get_sc_param('class'),
							"animation" => organic_beauty_get_sc_param('animation'),
							"css" => organic_beauty_get_sc_param('css')
						)
					)
				)

			));
		}
	}
}


// Add [trx_services] and [trx_services_item] in the VC shortcodes list
if (!function_exists('organic_beauty_services_reg_shortcodes_vc')) {
	//add_filter('organic_beauty_action_shortcodes_list_vc',	'organic_beauty_services_reg_shortcodes_vc');
	function organic_beauty_services_reg_shortcodes_vc() {

		$services_groups = organic_beauty_get_list_terms(false, 'services_group');
		$services_styles = organic_beauty_get_list_templates('services');
		$controls		 = organic_beauty_get_list_slider_controls();

		// Services
		vc_map( array(
				"base" => "trx_services",
				"name" => esc_html__("Services", 'organic-beauty'),
				"description" => wp_kses_data( __("Insert services list", 'organic-beauty') ),
				"category" => esc_html__('Content', 'organic-beauty'),
				"icon" => 'icon_trx_services',
				"class" => "trx_sc_columns trx_sc_services",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => true,
				"as_parent" => array('only' => 'trx_services_item'),
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => esc_html__("Services style", 'organic-beauty'),
						"description" => wp_kses_data( __("Select style to display services list", 'organic-beauty') ),
						"class" => "",
						"admin_label" => true,
						"value" => array_flip($services_styles),
						"type" => "dropdown"
					),
					array(
						"param_name" => "equalheight",
						"heading" => esc_html__("Equal height", 'organic-beauty'),
						"description" => wp_kses_data( __("Make equal height for all items in the row", 'organic-beauty') ),
						"value" => array("Equal height" => "yes" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "scheme",
						"heading" => esc_html__("Color scheme", 'organic-beauty'),
						"description" => wp_kses_data( __("Select color scheme for this block", 'organic-beauty') ),
						"class" => "",
						"value" => array_flip((array)organic_beauty_get_sc_param('schemes')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "slider",
						"heading" => esc_html__("Slider", 'organic-beauty'),
						"description" => wp_kses_data( __("Use slider to show services", 'organic-beauty') ),
						"admin_label" => true,
						"group" => esc_html__('Slider', 'organic-beauty'),
						"class" => "",
						"std" => "no",
						"value" => array_flip((array)organic_beauty_get_sc_param('yes_no')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "controls",
						"heading" => esc_html__("Controls", 'organic-beauty'),
						"description" => wp_kses_data( __("Slider controls style and position", 'organic-beauty') ),
						"admin_label" => true,
						"group" => esc_html__('Slider', 'organic-beauty'),
						'dependency' => array(
							'element' => 'slider',
							'value' => 'yes'
						),
						"class" => "",
						"std" => "no",
						"value" => array_flip($controls),
						"type" => "dropdown"
					),
					array(
						"param_name" => "slides_space",
						"heading" => esc_html__("Space between slides", 'organic-beauty'),
						"description" => wp_kses_data( __("Size of space (in px) between slides", 'organic-beauty') ),
						"admin_label" => true,
						"group" => esc_html__('Slider', 'organic-beauty'),
						'dependency' => array(
							'element' => 'slider',
							'value' => 'yes'
						),
						"class" => "",
						"value" => "0",
						"type" => "textfield"
					),
					array(
						"param_name" => "interval",
						"heading" => esc_html__("Slides change interval", 'organic-beauty'),
						"description" => wp_kses_data( __("Slides change interval (in milliseconds: 1000ms = 1s)", 'organic-beauty') ),
						"group" => esc_html__('Slider', 'organic-beauty'),
						'dependency' => array(
							'element' => 'slider',
							'value' => 'yes'
						),
						"class" => "",
						"value" => "7000",
						"type" => "textfield"
					),
					array(
						"param_name" => "autoheight",
						"heading" => esc_html__("Autoheight", 'organic-beauty'),
						"description" => wp_kses_data( __("Change whole slider's height (make it equal current slide's height)", 'organic-beauty') ),
						"group" => esc_html__('Slider', 'organic-beauty'),
						'dependency' => array(
							'element' => 'slider',
							'value' => 'yes'
						),
						"class" => "",
						"value" => array("Autoheight" => "yes" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "align",
						"heading" => esc_html__("Alignment", 'organic-beauty'),
						"description" => wp_kses_data( __("Alignment of the services block", 'organic-beauty') ),
						"class" => "",
						"value" => array_flip((array)organic_beauty_get_sc_param('align')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "custom",
						"heading" => esc_html__("Custom", 'organic-beauty'),
						"description" => wp_kses_data( __("Allow get services from inner shortcodes (custom) or get it from specified group (cat)", 'organic-beauty') ),
						"class" => "",
						"value" => array("Custom services" => "yes" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "title",
						"heading" => esc_html__("Title", 'organic-beauty'),
						"description" => wp_kses_data( __("Title for the block", 'organic-beauty') ),
						"admin_label" => true,
						"group" => esc_html__('Captions', 'organic-beauty'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "subtitle",
						"heading" => esc_html__("Subtitle", 'organic-beauty'),
						"description" => wp_kses_data( __("Subtitle for the block", 'organic-beauty') ),
						"group" => esc_html__('Captions', 'organic-beauty'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "description",
						"heading" => esc_html__("Description", 'organic-beauty'),
						"description" => wp_kses_data( __("Description for the block", 'organic-beauty') ),
						"group" => esc_html__('Captions', 'organic-beauty'),
						"class" => "",
						"value" => "",
						"type" => "textarea"
					),
					array(
						"param_name" => "cat",
						"heading" => esc_html__("Categories", 'organic-beauty'),
						"description" => wp_kses_data( __("Select category to show services. If empty - select services from any category (group) or from IDs list", 'organic-beauty') ),
						"group" => esc_html__('Query', 'organic-beauty'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => array_flip((array)organic_beauty_array_merge(array(0 => esc_html__('- Select category -', 'organic-beauty')), $services_groups)),
						"type" => "dropdown"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", 'organic-beauty'),
						"description" => wp_kses_data( __("How many columns use to show services list", 'organic-beauty') ),
						"group" => esc_html__('Query', 'organic-beauty'),
						"admin_label" => true,
						"class" => "",
						"value" => "4",
						"type" => "textfield"
					),
					array(
						"param_name" => "count",
						"heading" => esc_html__("Number of posts", 'organic-beauty'),
						"description" => wp_kses_data( __("How many posts will be displayed? If used IDs - this parameter ignored.", 'organic-beauty') ),
						"admin_label" => true,
						"group" => esc_html__('Query', 'organic-beauty'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "4",
						"type" => "textfield"
					),
					array(
						"param_name" => "offset",
						"heading" => esc_html__("Offset before select posts", 'organic-beauty'),
						"description" => wp_kses_data( __("Skip posts before select next part.", 'organic-beauty') ),
						"group" => esc_html__('Query', 'organic-beauty'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "0",
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => esc_html__("Post sorting", 'organic-beauty'),
						"description" => wp_kses_data( __("Select desired posts sorting method", 'organic-beauty') ),
						"group" => esc_html__('Query', 'organic-beauty'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"std" => "date",
						"class" => "",
						"value" => array_flip((array)organic_beauty_get_sc_param('sorting')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => esc_html__("Post order", 'organic-beauty'),
						"description" => wp_kses_data( __("Select desired posts order", 'organic-beauty') ),
						"group" => esc_html__('Query', 'organic-beauty'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"std" => "desc",
						"class" => "",
						"value" => array_flip((array)organic_beauty_get_sc_param('ordering')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "ids",
						"heading" => esc_html__("Service's IDs list", 'organic-beauty'),
						"description" => wp_kses_data( __("Comma separated list of service's ID. If set - parameters above (category, count, order, etc.)  are ignored!", 'organic-beauty') ),
						"group" => esc_html__('Query', 'organic-beauty'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "readmore",
						"heading" => esc_html__("Read more", 'organic-beauty'),
						"description" => wp_kses_data( __("Caption for the Read more link (if empty - link not showed)", 'organic-beauty') ),
						"admin_label" => true,
						"group" => esc_html__('Captions', 'organic-beauty'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "link",
						"heading" => esc_html__("Button URL", 'organic-beauty'),
						"description" => wp_kses_data( __("Link URL for the button at the bottom of the block", 'organic-beauty') ),
						"group" => esc_html__('Captions', 'organic-beauty'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "link_caption",
						"heading" => esc_html__("Button caption", 'organic-beauty'),
						"description" => wp_kses_data( __("Caption for the button at the bottom of the block", 'organic-beauty') ),
						"group" => esc_html__('Captions', 'organic-beauty'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					organic_beauty_vc_width(),
					organic_beauty_vc_height(),
					organic_beauty_get_vc_param('margin_top'),
					organic_beauty_get_vc_param('margin_bottom'),
					organic_beauty_get_vc_param('margin_left'),
					organic_beauty_get_vc_param('margin_right'),
					organic_beauty_get_vc_param('id'),
					organic_beauty_get_vc_param('class'),
					organic_beauty_get_vc_param('animation'),
					organic_beauty_get_vc_param('css')
				),
				'default_content' => '
					[trx_services_item title="' . esc_html__( 'Service item 1', 'organic-beauty' ) . '"][/trx_services_item]
					[trx_services_item title="' . esc_html__( 'Service item 2', 'organic-beauty' ) . '"][/trx_services_item]
					[trx_services_item title="' . esc_html__( 'Service item 3', 'organic-beauty' ) . '"][/trx_services_item]
					[trx_services_item title="' . esc_html__( 'Service item 4', 'organic-beauty' ) . '"][/trx_services_item]
				',
				'js_view' => 'VcTrxColumnsView'
			) );
			
			
		vc_map( array(
				"base" => "trx_services_item",
				"name" => esc_html__("Services item", 'organic-beauty'),
				"description" => wp_kses_data( __("Custom services item - all data pull out from shortcode parameters", 'organic-beauty') ),
				"show_settings_on_create" => true,
				"class" => "trx_sc_collection trx_sc_column_item trx_sc_services_item",
				"content_element" => true,
				"is_container" => true,
				'icon' => 'icon_trx_services_item',
				"as_child" => array('only' => 'trx_services'),
				"as_parent" => array('except' => 'trx_services'),
				"params" => array(
					array(
						"param_name" => "title",
						"heading" => esc_html__("Title", 'organic-beauty'),
						"description" => wp_kses_data( __("Item's title", 'organic-beauty') ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "title_top",
						"heading" => esc_html__("Title Top", 'organic-beauty'),
						"description" => wp_kses_data( __("Item's top title", 'organic-beauty') ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "image",
						"heading" => esc_html__("Image", 'organic-beauty'),
						"description" => wp_kses_data( __("Item's image", 'organic-beauty') ),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "link",
						"heading" => esc_html__("Link", 'organic-beauty'),
						"description" => wp_kses_data( __("Link on item's page", 'organic-beauty') ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "readmore",
						"heading" => esc_html__("Read more", 'organic-beauty'),
						"description" => wp_kses_data( __("Caption for the Read more link (if empty - link not showed)", 'organic-beauty') ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					organic_beauty_get_vc_param('id'),
					organic_beauty_get_vc_param('class'),
					organic_beauty_get_vc_param('animation'),
					organic_beauty_get_vc_param('css')
				),
				'js_view' => 'VcTrxColumnItemView'
			) );
			
		class WPBakeryShortCode_Trx_Services extends ORGANIC_BEAUTY_VC_ShortCodeColumns {}
		class WPBakeryShortCode_Trx_Services_Item extends ORGANIC_BEAUTY_VC_ShortCodeCollection {}

	}
}
?>