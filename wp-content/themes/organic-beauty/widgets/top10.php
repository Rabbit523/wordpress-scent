<?php
/**
 * Theme Widget: Top10 reviews
 */

// Theme init
if (!function_exists('organic_beauty_widget_top10_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_widget_top10_theme_setup', 1 );
	function organic_beauty_widget_top10_theme_setup() {

		// Register shortcodes in the shortcodes list
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_widget_top10_reg_shortcodes_vc');
	}
}

// Load widget
if (!function_exists('organic_beauty_widget_top10_load')) {
	add_action( 'widgets_init', 'organic_beauty_widget_top10_load' );
	function organic_beauty_widget_top10_load() {
		register_widget('organic_beauty_widget_top10');
	}
}

// Widget Class
class organic_beauty_widget_top10 extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_top10', 'description' => esc_html__('Top 10 posts by average reviews marks (by author and users)', 'organic-beauty'));
		parent::__construct( 'organic_beauty_widget_top10', esc_html__('Organic Beauty - Top 10 Posts', 'organic-beauty'), $widget_ops );

		// Add thumb sizes into list
		organic_beauty_add_thumb_sizes( array( 'layout' => 'widgets', 'w' => 75, 'h' => 75, 'title'=>esc_html__('Widgets', 'organic-beauty') ) );
	}

	// Show widget
	function widget($args, $instance) {
		extract($args);

		global $post;

		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '');
		$title_tabs = array(
			isset($instance['title_author']) ? $instance['title_author'] : '',
			isset($instance['title_users']) ? $instance['title_users'] : ''
		);
		
		$post_type = isset($instance['post_type']) ? $instance['post_type'] : 'post';
		$category = isset($instance['category']) ? (int) $instance['category'] : 0;
		$taxonomy = organic_beauty_get_taxonomy_categories_by_post_type($post_type);

		$number = isset($instance['number']) ? (int) $instance['number'] : '';

		$show_date = isset($instance['show_date']) ? (int) $instance['show_date'] : 0;
		$show_image = isset($instance['show_image']) ? (int) $instance['show_image'] : 0;
		$show_author = isset($instance['show_author']) ? (int) $instance['show_author'] : 0;
		$show_counters = isset($instance['show_counters']) ? (int) $instance['show_counters'] : 0;
		$show_counters = $show_counters==2 ? 'stars' : ($show_counters==1 ? 'rating' : '');

		$titles = '';
		$content = '';
		$id = 'widget_top10_'.str_replace('.', '', mt_rand());
		
		$reviews_first_author = organic_beauty_get_theme_option('reviews_first')=='author';
		$reviews_second_hide = organic_beauty_get_theme_option('reviews_second')=='hide';

		for ($i=0; $i<2; $i++) {
			
			if ($i==0 && !$reviews_first_author && $reviews_second_hide) continue;
			if ($i==1 && $reviews_first_author && $reviews_second_hide) continue;
			
			$post_rating = organic_beauty_storage_get('options_prefix').'_reviews_avg'.($i==0 ? '' : '2');
			
			$args = array(
				'post_type' => $post_type,
				'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish',
				'post_password' => '',
				'posts_per_page' => $number,
				'ignore_sticky_posts' => true,
				'order' => 'DESC',
				'orderby' => 'meta_value_num',
				'meta_key' => $post_rating
			);
			if ($category > 0) {
				if ($taxonomy=='category')
					$args['cat'] = $category;
				else {
					$args['tax_query'] = array(
						array(
							'taxonomy' => $taxonomy,
							'field' => 'id',
							'terms' => $category
						)
					);
				}
			}
			$ex = organic_beauty_get_theme_option('exclude_cats');
			if (!empty($ex)) {
				$args['category__not_in'] = explode(',', $ex);
			}
			
			$q = new WP_Query($args); 
			
			if ($q->have_posts()) {
				$post_number = 0;
				$output = '';
				while ($q->have_posts()) { $q->the_post();
					$post_number++;
					organic_beauty_template_set_args('widgets-posts', array(
						'post_number' => $post_number,
						'post_rating' => $post_rating,
						'show_date' => $show_date,
						'show_image' => $show_image,
						'show_author' => $show_author,
						'show_counters' => $show_counters
					));
					get_template_part(organic_beauty_get_file_slug('templates/_parts/widgets-posts.php'));
					$output .= organic_beauty_storage_get('widgets_posts_output');
					if ($post_number >= $number) break;
				}
				if ( !empty($output) ) {
					$titles .= '<li class="sc_tabs_title"><a href="#'.$id.'_'.esc_attr($i).'">'.esc_html($title_tabs[$i]).'</a></li>';
					$content .= '<div id="'.$id.'_'.esc_attr($i).'" class="widget_top10_tab_content sc_tabs_content">' . $output . '</div>';
				}
			}
		}

		wp_reset_postdata();

		
		if ( !empty($titles) ) {
			
			// Before widget (defined by themes)
			organic_beauty_show_layout($before_widget);
			
			// Display the widget title if one was input (before and after defined by themes)
			if ($title) organic_beauty_show_layout($title, $before_title, $after_title);
	
			echo '<div id="'.$id.'" class="widget_top10_content sc_tabs sc_tabs_style_2 no_jquery_ui">'
					. '<ul class="widget_top10_tab_titles sc_tabs_titles">' . trim($titles) . '</ul>'
					. $content
				. '</div>';

			// After widget (defined by themes)
			organic_beauty_show_layout($after_widget);
		}
	}

	// Update the widget settings.
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['title_author'] = strip_tags($new_instance['title_author']);
		$instance['title_users'] = strip_tags($new_instance['title_users']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = (int) $new_instance['show_date'];
		$instance['show_image'] = (int) $new_instance['show_image'];
		$instance['show_author'] = (int) $new_instance['show_author'];
		$instance['show_counters'] = (int) $new_instance['show_counters'];
		$instance['category'] = (int) $new_instance['category'];
		$instance['post_type'] = strip_tags( $new_instance['post_type'] );
		return $instance;
	}

	// Displays the widget settings controls on the widget panel.
	function form($instance) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'title_author' => '',
			'title_users' => '',
			'number' => '4',
			'show_date' => '1',
			'show_image' => '1',
			'show_author' => '1',
			'show_counters' => '1',
			'category'=>'0',
			'post_type' => 'post'
			)
		);
		$title = $instance['title'];
		$title_author = $instance['title_author'];
		$title_users = $instance['title_users'];
		$number = (int) $instance['number'];
		$show_date = (int) $instance['show_date'];
		$show_image = (int) $instance['show_image'];
		$show_author = (int) $instance['show_author'];
		$show_counters = (int) $instance['show_counters'];
		$post_type = $instance['post_type'];
		$category = (int) $instance['category'];

		$posts_types = organic_beauty_get_list_posts_types(false);
		$categories = organic_beauty_get_list_terms(false, organic_beauty_get_taxonomy_categories_by_post_type($post_type));
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Widget title:', 'organic-beauty'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title_author')); ?>"><?php esc_html_e('Author rating tab title:', 'organic-beauty'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('title_author')); ?>" name="<?php echo esc_attr($this->get_field_name('title_author')); ?>" value="<?php echo esc_attr($title_author); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title_users')); ?>"><?php esc_html_e('Users rating tab title:', 'organic-beauty'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('title_users')); ?>" name="<?php echo esc_attr($this->get_field_name('title_users')); ?>" value="<?php echo esc_attr($title_users); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('post_type')); ?>"><?php esc_html_e('Post type:', 'organic-beauty'); ?></label>
			<select id="<?php echo esc_attr($this->get_field_id('post_type')); ?>" name="<?php echo esc_attr($this->get_field_name('post_type')); ?>" class="widgets_param_fullwidth widgets_param_post_type_selector">
			<?php
				if (is_array($posts_types) && count($posts_types) > 0) {
					foreach ($posts_types as $type => $type_name) {
						echo '<option value="'.esc_attr($type).'"'.($post_type==$type ? ' selected="selected"' : '').'>'.esc_html($type_name).'</option>';
					}
				}
			?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php esc_html_e('Category:', 'organic-beauty'); ?></label>
			<select id="<?php echo esc_attr($this->get_field_id('category')); ?>" name="<?php echo esc_attr($this->get_field_name('category')); ?>" class="widgets_param_fullwidth">
				<option value="0"><?php esc_html_e('-- Any category --', 'organic-beauty'); ?></option> 
			<?php
				if (is_array($categories) && count($categories) > 0) {
					foreach ($categories as $cat_id => $cat_name) {
						echo '<option value="'.esc_attr($cat_id).'"'.($category==$cat_id ? ' selected="selected"' : '').'>'.esc_html($cat_name).'</option>';
					}
				}
			?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_html_e('Number posts to show:', 'organic-beauty'); ?></label>
			<input type="text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" value="<?php echo esc_attr($number); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_image')); ?>_1"><?php esc_html_e('Show post image:', 'organic-beauty'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_image')); ?>_1" name="<?php echo esc_attr($this->get_field_name('show_image')); ?>" value="1" <?php echo (1==$show_image ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_image')); ?>_1"><?php esc_html_e('Show', 'organic-beauty'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_image')); ?>_0" name="<?php echo esc_attr($this->get_field_name('show_image')); ?>" value="0" <?php echo (0==$show_image ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_image')); ?>_0"><?php esc_html_e('Hide', 'organic-beauty'); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_author')); ?>_1"><?php esc_html_e('Show post author:', 'organic-beauty'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_author')); ?>_1" name="<?php echo esc_attr($this->get_field_name('show_author')); ?>" value="1" <?php echo (1==$show_author ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_author')); ?>_1"><?php esc_html_e('Show', 'organic-beauty'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_author')); ?>_0" name="<?php echo esc_attr($this->get_field_name('show_author')); ?>" value="0" <?php echo (0==$show_author ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_author')); ?>_0"><?php esc_html_e('Hide', 'organic-beauty'); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>_1"><?php esc_html_e('Show post date:', 'organic-beauty'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_date')); ?>_1" name="<?php echo esc_attr($this->get_field_name('show_date')); ?>" value="1" <?php echo (1==$show_date ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>_1"><?php esc_html_e('Show', 'organic-beauty'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_date')); ?>_0" name="<?php echo esc_attr($this->get_field_name('show_date')); ?>" value="0" <?php echo (0==$show_date ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>_0"><?php esc_html_e('Hide', 'organic-beauty'); ?></label>
		</p>


		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_counters')); ?>_1"><?php esc_html_e('Show post counters:', 'organic-beauty'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_counters')); ?>_2" name="<?php echo esc_attr($this->get_field_name('show_counters')); ?>" value="2" <?php echo (2==$show_counters ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_counters')); ?>_2"><?php esc_html_e('As stars', 'organic-beauty'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_counters')); ?>_1" name="<?php echo esc_attr($this->get_field_name('show_counters')); ?>" value="1" <?php echo (1==$show_counters ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_counters')); ?>_1"><?php esc_html_e('As icon', 'organic-beauty'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_counters')); ?>_0" name="<?php echo esc_attr($this->get_field_name('show_counters')); ?>" value="0" <?php echo (0==$show_counters ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_counters')); ?>_0"><?php esc_html_e('Hide', 'organic-beauty'); ?></label>
		</p>

	<?php
	}
}



// trx_widget_top10
//-------------------------------------------------------------
/*
[trx_widget_top10 id="unique_id" title="Widget title" title_author="title for the tab 'By author'" title_users="title for the tab 'By Visitors'" number="4"]
*/
if ( !function_exists( 'organic_beauty_sc_widget_top10' ) ) {
	function organic_beauty_sc_widget_top10($atts, $content=null){	
		$atts = organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"title_author" => "",
			"title_users" => "",
			"number" => 4,
			"show_date" => 1,
			"show_image" => 1,
			"show_author" => 1,
			"show_counters" => 1,
			'category' 		=> '',
			'cat' 			=> 0,
			'post_type'		=> 'post',
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts));
		if ($atts['post_type']=='') $atts['post_type'] = 'post';
		if ($atts['cat']!='' && $atts['category']=='') $atts['category'] = $atts['cat'];
		if ($atts['show_date']=='') $atts['show_date'] = 0;
		if ($atts['show_image']=='') $atts['show_image'] = 0;
		if ($atts['show_author']=='') $atts['show_author'] = 0;
		if ($atts['show_counters']=='') $atts['show_counters'] = 0;
		extract($atts);
		$type = 'organic_beauty_widget_top10';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_top10' 
								. (organic_beauty_exists_visual_composer() ? ' vc_widget_top10 wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
						. '">';
			ob_start();
			the_widget( $type, $atts, organic_beauty_prepare_widgets_args(organic_beauty_storage_get('widgets_args'), $id ? $id.'_widget' : 'widget_top10', 'widget_top10') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_widget_top10', $atts, $content);
	}
	organic_beauty_require_shortcode("trx_widget_top10", "organic_beauty_sc_widget_top10");
}


// Add [trx_widget_top10] in the VC shortcodes list
if (!function_exists('organic_beauty_widget_top10_reg_shortcodes_vc')) {
	//add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_widget_top10_reg_shortcodes_vc');
	function organic_beauty_widget_top10_reg_shortcodes_vc() {
		
		$posts_types = organic_beauty_get_list_posts_types(false);
		$categories = organic_beauty_get_list_terms(false, organic_beauty_get_taxonomy_categories_by_post_type('post'));

		vc_map( array(
				"base" => "trx_widget_top10",
				"name" => esc_html__("Widget Top10 Reviews", 'organic-beauty'),
				"description" => wp_kses_data( __("Insert Top10 reviews list with thumbs and post's meta", 'organic-beauty') ),
				"category" => esc_html__('Content', 'organic-beauty'),
				"icon" => 'icon_trx_widget_top10',
				"class" => "trx_widget_top10",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "title",
						"heading" => esc_html__("Widget title", 'organic-beauty'),
						"description" => wp_kses_data( __("Title of the widget", 'organic-beauty') ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "title_author",
						"heading" => esc_html__("Author's tab title", 'organic-beauty'),
						"description" => wp_kses_data( __("Title for the tab with authors's reviews", 'organic-beauty') ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "title_users",
						"heading" => esc_html__("User's tab title", 'organic-beauty'),
						"description" => wp_kses_data( __("Title for the tab with authors's reviews", 'organic-beauty') ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "number",
						"heading" => esc_html__("Number posts to show", 'organic-beauty'),
						"description" => wp_kses_data( __("How many posts display in widget?", 'organic-beauty') ),
						"admin_label" => true,
						"class" => "",
						"value" => "4",
						"type" => "textfield"
					),
					array(
						"param_name" => "post_type",
						"heading" => esc_html__("Post type", 'organic-beauty'),
						"description" => wp_kses_data( __("Select post type to show", 'organic-beauty') ),
						"class" => "",
						"std" => "post",
						"value" => array_flip($posts_types),
						"type" => "dropdown"
					),
					array(
						"param_name" => "cat",
						"heading" => esc_html__("Parent category", 'organic-beauty'),
						"description" => wp_kses_data( __("Select parent category. If empty - show posts from any category", 'organic-beauty') ),
						"class" => "",
						"value" => array_flip(organic_beauty_array_merge(array(0 => esc_html__('- Select category -', 'organic-beauty')), $categories)),
						"type" => "dropdown"
					),
					array(
						"param_name" => "show_image",
						"heading" => esc_html__("Show post's image", 'organic-beauty'),
						"description" => wp_kses_data( __("Do you want display post's featured image?", 'organic-beauty') ),
						"group" => esc_html__('Details', 'organic-beauty'),
						"class" => "",
						"std" => 1,
						"value" => array("Show image" => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "show_author",
						"heading" => esc_html__("Show post's author", 'organic-beauty'),
						"description" => wp_kses_data( __("Do you want display post's author?", 'organic-beauty') ),
						"group" => esc_html__('Details', 'organic-beauty'),
						"class" => "",
						"std" => 1,
						"value" => array("Show author" => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "show_date",
						"heading" => esc_html__("Show post's date", 'organic-beauty'),
						"description" => wp_kses_data( __("Do you want display post's publish date?", 'organic-beauty') ),
						"group" => esc_html__('Details', 'organic-beauty'),
						"class" => "",
						"std" => 1,
						"value" => array("Show date" => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "show_counters",
						"heading" => esc_html__("Show post's counters", 'organic-beauty'),
						"description" => wp_kses_data( __("Do you want display post's counters?", 'organic-beauty') ),
						"admin_label" => true,
						"group" => esc_html__('Details', 'organic-beauty'),
						"class" => "",
						"value" => array_flip(array(
							'2' => esc_html__('As stars', 'organic-beauty'),
							'1' => esc_html__('As text', 'organic-beauty'),
							'0' => esc_html__('Hide', 'organic-beauty')
						)),
						"type" => "dropdown"
					),
					organic_beauty_get_vc_param('id'),
					organic_beauty_get_vc_param('class'),
					organic_beauty_get_vc_param('css')
				)
			) );
			
		class WPBakeryShortCode_Trx_Widget_Top10 extends WPBakeryShortCode {}

	}
}
?>