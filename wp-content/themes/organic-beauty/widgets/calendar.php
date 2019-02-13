<?php
/**
 * Theme Widget: Advanced Calendar
 */

// Theme init
if (!function_exists('organic_beauty_widget_calendar_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_widget_calendar_theme_setup', 1 );
	function organic_beauty_widget_calendar_theme_setup() {

		// Register shortcodes in the shortcodes list
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_widget_calendar_reg_shortcodes_vc');
	}
}

// Load widget
if (!function_exists('organic_beauty_widget_calendar_load')) {
	add_action( 'widgets_init', 'organic_beauty_widget_calendar_load' );
	function organic_beauty_widget_calendar_load() {
		register_widget('organic_beauty_widget_calendar');
	}
}

// Widget Class
class organic_beauty_widget_calendar extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_calendar', 'description' => esc_html__('Calendar for posts and/or Events', 'organic-beauty'));
		parent::__construct( 'organic_beauty_widget_calendar', esc_html__('Organic Beauty - Advanced Calendar', 'organic-beauty'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {
		extract($args);

		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '');
		$post_type = isset($instance['post_type']) ? $instance['post_type'] : 'post';
		
		$output = organic_beauty_get_calendar(false, 0, 0, array('post_type'=>$post_type));

		if (!empty($output)) {
	
			// Before widget (defined by themes)
			organic_beauty_show_layout($before_widget);
			
			// Display the widget title if one was input (before and after defined by themes)
			if ($title) organic_beauty_show_layout($title, $before_title, $after_title);
	
			organic_beauty_show_layout($output);
			
			// After widget (defined by themes)
			organic_beauty_show_layout($after_widget);
		}
	}

	// Update the widget settings.
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['post_type'] = isset($new_instance['post_type']) ? join(',', $new_instance['post_type']) : 'post';
		return $instance;
	}

	// Displays the widget settings controls on the widget panel.
	function form($instance) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'post_type'=>'post'
			)
		);
		$title = $instance['title'];
		$post_type = $instance['post_type'];
		$posts_types = organic_beauty_get_list_posts_types(false);
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Widget title:', 'organic-beauty'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($title); ?>"  class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('post_type')); ?>_1"><?php esc_html_e('Post type:', 'organic-beauty'); ?></label><br>
			<?php
				$i=0;
				if (is_array($posts_types) && count($posts_types) > 0) {
					foreach ($posts_types as $type=>$type_title) {
						$i++;
						echo '<span class="post_type widgets_param_post_type" ><input type="checkbox" id="'.esc_attr($this->get_field_id('post_type').'_'.intval($i)).'" name="'.esc_attr($this->get_field_name('post_type')).'[]" value="'.esc_attr($type).'"'.(organic_beauty_strpos($post_type, $type)!==false ? ' checked="checked"' : '').'><label for="'.esc_attr($this->get_field_id('post_type').'_'.intval($i)).'">'.($type_title).'</label></span>';
					}
				}
			?>
			</select>
			<br><span class="description"><?php esc_html_e('Attention! If you check custom post types, please check also settings in the correspond plugins and enable display this posts in the blog!', 'organic-beauty'); ?></span>
		</p>

	<?php
	}
}



// trx_widget_calendar
//-------------------------------------------------------------
/*
[trx_widget_calendar id="unique_id" title="Widget title" weekdays="short|initial"]
*/
if ( !function_exists( 'organic_beauty_sc_widget_calendar' ) ) {
	function organic_beauty_sc_widget_calendar($atts, $content=null){	
		$atts = organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"weekdays" => "short",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts));
		if ($atts['weekdays']=='') $atts['weekdays'] = 'short';
		extract($atts);
		$type = 'organic_beauty_widget_calendar';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_calendar' 
								. (organic_beauty_exists_visual_composer() ? ' vc_widget_calendar wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
						. '">';
			ob_start();
			the_widget( $type, $atts, organic_beauty_prepare_widgets_args(organic_beauty_storage_get('widgets_args'), $id ? $id.'_widget' : 'widget_calendar', 'widget_calendar') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_widget_calendar', $atts, $content);
	}
	organic_beauty_require_shortcode("trx_widget_calendar", "organic_beauty_sc_widget_calendar");
}


// Add [trx_widget_calendar] in the VC shortcodes list
if (!function_exists('organic_beauty_widget_calendar_reg_shortcodes_vc')) {
	//add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_widget_calendar_reg_shortcodes_vc');
	function organic_beauty_widget_calendar_reg_shortcodes_vc() {
		
		vc_map( array(
				"base" => "trx_widget_calendar",
				"name" => esc_html__("Widget Calendar", 'organic-beauty'),
				"description" => wp_kses_data( __("Insert standard WP Calendar, but allow user select week day's captions", 'organic-beauty') ),
				"category" => esc_html__('Content', 'organic-beauty'),
				"icon" => 'icon_trx_widget_calendar',
				"class" => "trx_widget_calendar",
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
						"param_name" => "weekdays",
						"heading" => esc_html__("Week days", 'organic-beauty'),
						"description" => wp_kses_data( __("Show captions for the week days as three letters (Sun, Mon, etc.) or as one initial letter (S, M, etc.)", 'organic-beauty') ),
						"class" => "",
						"value" => array(esc_html__("Initial letter", 'organic-beauty') => "initial" ),
						"type" => "checkbox"
					),
					organic_beauty_get_vc_param('id'),
					organic_beauty_get_vc_param('class'),
					organic_beauty_get_vc_param('css')
				)
			) );
			
		class WPBakeryShortCode_Trx_Widget_Calendar extends WPBakeryShortCode {}

	}
}
?>