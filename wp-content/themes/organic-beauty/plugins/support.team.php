<?php
/**
 * Organic Beauty Framework: Team support
 *
 * @package	organic_beauty
 * @since	organic_beauty 1.0
 */

// Theme init
if (!function_exists('organic_beauty_team_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_team_theme_setup', 1 );
	function organic_beauty_team_theme_setup() {

		// Add item in the admin menu
		add_action('add_meta_boxes',						'organic_beauty_team_add_meta_box');

		// Save data from meta box
		add_action('save_post',								'organic_beauty_team_save_data');
		
		// Detect current page type, taxonomy and title (for custom post_types use priority < 10 to fire it handles early, than for standard post types)
		add_filter('organic_beauty_filter_get_blog_type',			'organic_beauty_team_get_blog_type', 9, 2);
		add_filter('organic_beauty_filter_get_blog_title',		'organic_beauty_team_get_blog_title', 9, 2);
		add_filter('organic_beauty_filter_get_current_taxonomy',	'organic_beauty_team_get_current_taxonomy', 9, 2);
		add_filter('organic_beauty_filter_is_taxonomy',			'organic_beauty_team_is_taxonomy', 9, 2);
		add_filter('organic_beauty_filter_get_stream_page_title',	'organic_beauty_team_get_stream_page_title', 9, 2);
		add_filter('organic_beauty_filter_get_stream_page_link',	'organic_beauty_team_get_stream_page_link', 9, 2);
		add_filter('organic_beauty_filter_get_stream_page_id',	'organic_beauty_team_get_stream_page_id', 9, 2);
		add_filter('organic_beauty_filter_query_add_filters',		'organic_beauty_team_query_add_filters', 9, 2);
		add_filter('organic_beauty_filter_detect_inheritance_key','organic_beauty_team_detect_inheritance_key', 9, 1);

		// Extra column for team members lists
		if (organic_beauty_get_theme_option('show_overriden_posts')=='yes') {
			add_filter('manage_edit-team_columns',			'organic_beauty_post_add_options_column', 9);
			add_filter('manage_team_posts_custom_column',	'organic_beauty_post_fill_options_column', 9, 2);
		}

		// Register shortcodes [trx_team] and [trx_team_item]
		add_action('organic_beauty_action_shortcodes_list',		'organic_beauty_team_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_team_reg_shortcodes_vc');

		// Meta box fields
		organic_beauty_storage_set('team_meta_box', array(
			'id' => 'team-meta-box',
			'title' => esc_html__('Team Member Details', 'organic-beauty'),
			'page' => 'team',
			'context' => 'normal',
			'priority' => 'high',
			'fields' => array(
				"team_member_position" => array(
					"title" => esc_html__('Position',  'organic-beauty'),
					"desc" => wp_kses_data( __("Position of the team member", 'organic-beauty') ),
					"class" => "team_member_position",
					"std" => "",
					"type" => "text"),
				"team_member_bday" => array(
					"title" => esc_html__("Birthday",  'organic-beauty'),
					"desc" => wp_kses_data( __("Birthday of the team member", 'organic-beauty') ),
					"class" => "team_member_email",
					"std" => "",
					"type" => "text"),
				"team_member_email" => array(
					"title" => esc_html__("E-mail",  'organic-beauty'),
					"desc" => wp_kses_data( __("E-mail of the team member - need to take Gravatar (if registered)", 'organic-beauty') ),
					"class" => "team_member_email",
					"std" => "",
					"type" => "text"),
				"team_member_link" => array(
					"title" => esc_html__('Link to profile',  'organic-beauty'),
					"desc" => wp_kses_data( __("URL of the team member profile page (if not this page)", 'organic-beauty') ),
					"class" => "team_member_link",
					"std" => "",
					"type" => "text"),
				"team_member_socials" => array(
					"title" => esc_html__("Social links",  'organic-beauty'),
					"desc" => wp_kses_data( __("Links to the social profiles of the team member", 'organic-beauty') ),
					"class" => "team_member_email",
					"std" => "",
					"type" => "social"),
				"team_member_brief_info" => array(
					"title" => esc_html__("Brief info",  'organic-beauty'),
					"desc" => wp_kses_data( __("Brief info about the team member", 'organic-beauty') ),
					"class" => "team_member_brief_info",
					"std" => "",
					"type" => "textarea"),
				)
			)
		);
		
		// Add supported data types
		organic_beauty_theme_support_pt('team');
		organic_beauty_theme_support_tx('team_group');
	}
}

if ( !function_exists( 'organic_beauty_team_settings_theme_setup2' ) ) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_team_settings_theme_setup2', 3 );
	function organic_beauty_team_settings_theme_setup2() {
		// Add post type 'team' and taxonomy 'team_group' into theme inheritance list
		organic_beauty_add_theme_inheritance( array('team' => array(
			'stream_template' => 'blog-team',
			'single_template' => 'single-team',
			'taxonomy' => array('team_group'),
			'taxonomy_tags' => array(),
			'post_type' => array('team'),
			'override' => 'custom'
			) )
		);
	}
}


// Add meta box
if (!function_exists('organic_beauty_team_add_meta_box')) {
	//add_action('add_meta_boxes', 'organic_beauty_team_add_meta_box');
	function organic_beauty_team_add_meta_box() {
		$mb = organic_beauty_storage_get('team_meta_box');
		add_meta_box($mb['id'], $mb['title'], 'organic_beauty_team_show_meta_box', $mb['page'], $mb['context'], $mb['priority']);
	}
}

// Callback function to show fields in meta box
if (!function_exists('organic_beauty_team_show_meta_box')) {
	function organic_beauty_team_show_meta_box() {
		global $post;

		$data = get_post_meta($post->ID, organic_beauty_storage_get('options_prefix').'_team_data', true);
		$fields = organic_beauty_storage_get_array('team_meta_box', 'fields');
		?>
		<input type="hidden" name="meta_box_team_nonce" value="<?php echo esc_attr(wp_create_nonce(admin_url())); ?>" />
		<table class="team_area">
		<?php
		if (is_array($fields) && count($fields) > 0) {
			foreach ($fields as $id=>$field) { 
				$meta = isset($data[$id]) ? $data[$id] : '';
				?>
				<tr class="team_field <?php echo esc_attr($field['class']); ?>" valign="top">
					<td><label for="<?php echo esc_attr($id); ?>"><?php echo esc_attr($field['title']); ?></label></td>
					<td>
						<?php
						if ($id == 'team_member_socials') {
							$socials_type = organic_beauty_get_theme_setting('socials_type');
							$social_list = organic_beauty_get_theme_option('social_icons');
							if (is_array($social_list) && count($social_list) > 0) {
								foreach ($social_list as $soc) {
									if ($socials_type == 'icons') {
										$parts = explode('-', $soc['icon'], 2);
										$sn = isset($parts[1]) ? $parts[1] : $soc['icon'];
									} else {
										$sn = basename($soc['icon']);
										$sn = organic_beauty_substr($sn, 0, organic_beauty_strrpos($sn, '.'));
										if (($pos=organic_beauty_strrpos($sn, '_'))!==false)
											$sn = organic_beauty_substr($sn, 0, $pos);
									}   
									$link = isset($meta[$sn]) ? $meta[$sn] : '';
									?>
									<label for="<?php echo esc_attr(($id).'_'.($sn)); ?>"><?php echo esc_attr(organic_beauty_strtoproper($sn)); ?></label><br>
									<input type="text" name="<?php echo esc_attr($id); ?>[<?php echo esc_attr($sn); ?>]" id="<?php echo esc_attr(($id).'_'.($sn)); ?>" value="<?php echo esc_attr($link); ?>" size="30" /><br>
									<?php
								}
							}
						} else if (!empty($field['type']) && $field['type']=='textarea') {
							?>
							<textarea name="<?php echo esc_attr($id); ?>" id="<?php echo esc_attr($id); ?>" rows="8" cols="100"><?php echo esc_html($meta); ?></textarea>
							<?php
						} else {
							?>
							<input type="text" name="<?php echo esc_attr($id); ?>" id="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($meta); ?>" size="30" />
							<?php
						}
						?>
						<br><small><?php echo esc_attr($field['desc']); ?></small>
					</td>
				</tr>
				<?php
			}
		}
		?>
		</table>
		<?php
	}
}


// Save data from meta box
if (!function_exists('organic_beauty_team_save_data')) {
	//add_action('save_post', 'organic_beauty_team_save_data');
	function organic_beauty_team_save_data($post_id) {
		// verify nonce
		if ( !wp_verify_nonce( organic_beauty_get_value_gp('meta_box_team_nonce'), admin_url() ) )
			return $post_id;

		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}

		// check permissions
		if ($_POST['post_type']!='team' || !current_user_can('edit_post', $post_id)) {
			return $post_id;
		}

		$data = array();

		$fields = organic_beauty_storage_get_array('team_meta_box', 'fields');

		// Post type specific data handling
		if (is_array($fields) && count($fields) > 0) {
			foreach ($fields as $id=>$field) {
				if (isset($_POST[$id])) {
					if (is_array($_POST[$id]) && count($_POST[$id]) > 0) {
						foreach ($_POST[$id] as $sn=>$link) {
							$_POST[$id][$sn] = stripslashes($link);
						}
						$data[$id] = $_POST[$id];
					} else {
						$data[$id] = stripslashes($_POST[$id]);
					}
				}
			}
		}

		update_post_meta($post_id, organic_beauty_storage_get('options_prefix').'_team_data', $data);
	}
}



// Return true, if current page is team member page
if ( !function_exists( 'organic_beauty_is_team_page' ) ) {
	function organic_beauty_is_team_page() {
		$is = in_array(organic_beauty_storage_get('page_template'), array('blog-team', 'single-team'));
		if (!$is) {
			if (!organic_beauty_storage_empty('pre_query'))
				$is = organic_beauty_storage_call_obj_method('pre_query', 'get', 'post_type')=='team' 
						|| organic_beauty_storage_call_obj_method('pre_query', 'is_tax', 'team_group') 
						|| (organic_beauty_storage_call_obj_method('pre_query', 'is_page') 
								&& ($id=organic_beauty_get_template_page_id('blog-team')) > 0 
								&& $id==organic_beauty_storage_get_obj_property('pre_query', 'queried_object_id', 0)
							);
			else
				$is = get_query_var('post_type')=='team' || is_tax('team_group') || (is_page() && ($id=organic_beauty_get_template_page_id('blog-team')) > 0 && $id==get_the_ID());
		}
		return $is;
	}
}

// Filter to detect current page inheritance key
if ( !function_exists( 'organic_beauty_team_detect_inheritance_key' ) ) {
	//add_filter('organic_beauty_filter_detect_inheritance_key',	'organic_beauty_team_detect_inheritance_key', 9, 1);
	function organic_beauty_team_detect_inheritance_key($key) {
		if (!empty($key)) return $key;
		return organic_beauty_is_team_page() ? 'team' : '';
	}
}

// Filter to detect current page slug
if ( !function_exists( 'organic_beauty_team_get_blog_type' ) ) {
	//add_filter('organic_beauty_filter_get_blog_type',	'organic_beauty_team_get_blog_type', 9, 2);
	function organic_beauty_team_get_blog_type($page, $query=null) {
		if (!empty($page)) return $page;
		if ($query && $query->is_tax('team_group') || is_tax('team_group'))
			$page = 'team_category';
		else if ($query && $query->get('post_type')=='team' || get_query_var('post_type')=='team')
			$page = $query && $query->is_single() || is_single() ? 'team_item' : 'team';
		return $page;
	}
}

// Filter to detect current page title
if ( !function_exists( 'organic_beauty_team_get_blog_title' ) ) {
	//add_filter('organic_beauty_filter_get_blog_title',	'organic_beauty_team_get_blog_title', 9, 2);
	function organic_beauty_team_get_blog_title($title, $page) {
		if (!empty($title)) return $title;
		if ( organic_beauty_strpos($page, 'team')!==false ) {
			if ( $page == 'team_category' ) {
				$term = get_term_by( 'slug', get_query_var( 'team_group' ), 'team_group', OBJECT);
				$title = $term->name;
			} else if ( $page == 'team_item' ) {
				$title = organic_beauty_get_post_title();
			} else {
				$title = esc_html__('All team', 'organic-beauty');
			}
		}

		return $title;
	}
}

// Filter to detect stream page title
if ( !function_exists( 'organic_beauty_team_get_stream_page_title' ) ) {
	//add_filter('organic_beauty_filter_get_stream_page_title',	'organic_beauty_team_get_stream_page_title', 9, 2);
	function organic_beauty_team_get_stream_page_title($title, $page) {
		if (!empty($title)) return $title;
		if (organic_beauty_strpos($page, 'team')!==false) {
			if (($page_id = organic_beauty_team_get_stream_page_id(0, $page=='team' ? 'blog-team' : $page)) > 0)
				$title = organic_beauty_get_post_title($page_id);
			else
				$title = esc_html__('All team', 'organic-beauty');				
		}
		return $title;
	}
}

// Filter to detect stream page ID
if ( !function_exists( 'organic_beauty_team_get_stream_page_id' ) ) {
	//add_filter('organic_beauty_filter_get_stream_page_id',	'organic_beauty_team_get_stream_page_id', 9, 2);
	function organic_beauty_team_get_stream_page_id($id, $page) {
		if (!empty($id)) return $id;
		if (organic_beauty_strpos($page, 'team')!==false) $id = organic_beauty_get_template_page_id('blog-team');
		return $id;
	}
}

// Filter to detect stream page URL
if ( !function_exists( 'organic_beauty_team_get_stream_page_link' ) ) {
	//add_filter('organic_beauty_filter_get_stream_page_link',	'organic_beauty_team_get_stream_page_link', 9, 2);
	function organic_beauty_team_get_stream_page_link($url, $page) {
		if (!empty($url)) return $url;
		if (organic_beauty_strpos($page, 'team')!==false) {
			$id = organic_beauty_get_template_page_id('blog-team');
			if ($id) $url = get_permalink($id);
		}
		return $url;
	}
}

// Filter to detect current taxonomy
if ( !function_exists( 'organic_beauty_team_get_current_taxonomy' ) ) {
	//add_filter('organic_beauty_filter_get_current_taxonomy',	'organic_beauty_team_get_current_taxonomy', 9, 2);
	function organic_beauty_team_get_current_taxonomy($tax, $page) {
		if (!empty($tax)) return $tax;
		if ( organic_beauty_strpos($page, 'team')!==false ) {
			$tax = 'team_group';
		}
		return $tax;
	}
}

// Return taxonomy name (slug) if current page is this taxonomy page
if ( !function_exists( 'organic_beauty_team_is_taxonomy' ) ) {
	//add_filter('organic_beauty_filter_is_taxonomy',	'organic_beauty_team_is_taxonomy', 9, 2);
	function organic_beauty_team_is_taxonomy($tax, $query=null) {
		if (!empty($tax))
			return $tax;
		else 
			return $query && $query->get('team_group')!='' || is_tax('team_group') ? 'team_group' : '';
	}
}

// Add custom post type and/or taxonomies arguments to the query
if ( !function_exists( 'organic_beauty_team_query_add_filters' ) ) {
	//add_filter('organic_beauty_filter_query_add_filters',	'organic_beauty_team_query_add_filters', 9, 2);
	function organic_beauty_team_query_add_filters($args, $filter) {
		if ($filter == 'team') {
			$args['post_type'] = 'team';
		}
		return $args;
	}
}





// ---------------------------------- [trx_team] ---------------------------------------

/*
[trx_team id="unique_id" columns="3" style="team-1|team-2|..."]
	[trx_team_item user="user_login"]
	[trx_team_item member="member_id"]
	[trx_team_item name="team member name" photo="url" email="address" position="director"]
[/trx_team]
*/
if ( !function_exists( 'organic_beauty_sc_team' ) ) {
	function organic_beauty_sc_team($atts, $content=null){	
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "team-1",
			"slider" => "no",
			"controls" => "no",
			"slides_space" => 0,
			"interval" => "",
			"autoheight" => "no",
			"align" => "",
			"custom" => "no",
			"ids" => "",
			"cat" => "",
			"count" => 3,
			"columns" => 3,
			"offset" => "",
			"orderby" => "title",
			"order" => "asc",
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"link_caption" => esc_html__('Learn more', 'organic-beauty'),
			"link" => '',
			"scheme" => '',
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

		if (empty($id)) $id = "sc_team_".str_replace('.', '', mt_rand());
		if (empty($width)) $width = "100%";
		if (!empty($height) && organic_beauty_param_is_on($autoheight)) $autoheight = "no";
		if (empty($interval)) $interval = mt_rand(5000, 10000);

		$class .= ($class ? ' ' : '') . organic_beauty_get_css_position_as_classes($top, $right, $bottom, $left);

		$ws = organic_beauty_get_css_dimensions_from_values($width);
		$hs = organic_beauty_get_css_dimensions_from_values('', $height);
		$css .= ($hs) . ($ws);

		$count = max(1, (int) $count);
		$columns = max(1, min(12, (int) $columns));
		if (organic_beauty_param_is_off($custom) && $count < $columns) $columns = $count;

		organic_beauty_storage_set('sc_team_data', array(
			'id' => $id,
            'style' => $style,
            'columns' => $columns,
            'counter' => 0,
            'slider' => $slider,
            'css_wh' => $ws . $hs
            )
        );

		if (organic_beauty_param_is_on($slider)) organic_beauty_enqueue_slider('swiper');
	
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'_wrap"' : '') 
						. ' class="sc_team_wrap'
						. ($scheme && !organic_beauty_param_is_off($scheme) && !organic_beauty_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
						.'">'
					. '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
						. ' class="sc_team sc_team_style_'.esc_attr($style)
							. ' ' . esc_attr(organic_beauty_get_template_property($style, 'container_classes'))
							. (!empty($class) ? ' '.esc_attr($class) : '')
							. ($align!='' && $align!='none' ? ' align'.esc_attr($align) : '')
						.'"'
						. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
						. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
					. '>'
					. (!empty($subtitle) ? '<h6 class="sc_team_subtitle sc_item_subtitle">' . trim(organic_beauty_strmacros($subtitle)) . '</h6>' : '')
					. (!empty($title) ? '<h2 class="sc_team_title sc_item_title' . (empty($description) ? ' sc_item_title_without_descr' : ' sc_item_title_without_descr') . '">' . trim(organic_beauty_strmacros($title)) . '</h2>' : '')
					. (!empty($description) ? '<div class="sc_team_descr sc_item_descr">' . trim(organic_beauty_strmacros($description)) . '</div>' : '')
					. (organic_beauty_param_is_on($slider) 
						? ('<div class="sc_slider_swiper swiper-slider-container'
										. ' ' . esc_attr(organic_beauty_get_slider_controls_classes($controls))
										. (organic_beauty_param_is_on($autoheight) ? ' sc_slider_height_auto' : '')
										. ($hs ? ' sc_slider_height_fixed' : '')
										. '"'
									. (!empty($width) && organic_beauty_strpos($width, '%')===false ? ' data-old-width="' . esc_attr($width) . '"' : '')
									. (!empty($height) && organic_beauty_strpos($height, '%')===false ? ' data-old-height="' . esc_attr($height) . '"' : '')
									. ((int) $interval > 0 ? ' data-interval="'.esc_attr($interval).'"' : '')
									. ($slides_space > 0 ? ' data-slides-space="' . esc_attr($slides_space) . '"' : '')
									. ($columns > 1 ? ' data-slides-per-view="' . esc_attr($columns) . '"' : '')
									. ' data-slides-min-width="250"'
								. '>'
							. '<div class="slides swiper-wrapper">')
						: ($columns > 1 // && organic_beauty_get_template_property($style, 'need_columns')
							? '<div class="sc_columns columns_wrap">' 
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
				'post_type' => 'team',
				'post_status' => 'publish',
				'posts_per_page' => $count,
				'ignore_sticky_posts' => true,
				'order' => $order=='asc' ? 'asc' : 'desc',
			);
		
			if ($offset > 0 && empty($ids)) {
				$args['offset'] = $offset;
			}
		
			$args = organic_beauty_query_add_sort_order($args, $orderby, $order);
			$args = organic_beauty_query_add_posts_and_cats($args, $ids, 'team', $cat, 'team_group');
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
					"columns_count" => $columns,
					'slider' => $slider,
					'tag_id' => $id ? $id . '_' . $post_number : '',
					'tag_class' => '',
					'tag_animation' => '',
					'tag_css' => '',
					'tag_css_wh' => $ws . $hs
				);
				$post_data = organic_beauty_get_post_data($args);
				$post_meta = get_post_meta($post_data['post_id'], organic_beauty_storage_get('options_prefix').'_team_data', true);
				$thumb_sizes = organic_beauty_get_thumb_sizes(array('layout' => $style));
				$args['position'] = $post_meta['team_member_position'];
				$args['link'] = !empty($post_meta['team_member_link']) ? $post_meta['team_member_link'] : $post_data['post_link'];
				$args['email'] = $post_meta['team_member_email'];
				$args['photo'] = $post_data['post_thumb'];
				$mult = organic_beauty_get_retina_multiplier();
				if (empty($args['photo']) && !empty($args['email'])) $args['photo'] = get_avatar($args['email'], $thumb_sizes['w']*$mult);
				$args['socials'] = '';
				$soc_list = $post_meta['team_member_socials'];
				if (is_array($soc_list) && count($soc_list)>0) {
					$soc_str = '';
					foreach ($soc_list as $sn=>$sl) {
						if (!empty($sl))
							$soc_str .= (!empty($soc_str) ? '|' : '') . ($sn) . '=' . ($sl);
					}
					if (!empty($soc_str))
						$args['socials'] = organic_beauty_do_shortcode('[trx_socials size="tiny" shape="round" socials="'.esc_attr($soc_str).'"][/trx_socials]');
				}
				$output .= organic_beauty_show_post_layout($args, $post_data);
			}
			wp_reset_postdata();
		}

		if (organic_beauty_param_is_on($slider)) {
			$output .= '</div>'
				. '<div class="sc_slider_controls_wrap"><a class="sc_slider_prev" href="#"></a><a class="sc_slider_next" href="#"></a></div>'
				. '<div class="sc_slider_pagination_wrap"></div>'
				. '</div>';
		} else if ($columns > 1) {// && organic_beauty_get_template_property($style, 'need_columns')) {
			$output .= '</div>';
		}

		$output .= (!empty($link) ? '<div class="sc_team_button sc_item_button">'.organic_beauty_do_shortcode('[trx_button link="'.esc_url($link).'" icon="icon-right"]'.esc_html($link_caption).'[/trx_button]').'</div>' : '')
					. '</div><!-- /.sc_team -->'
				. '</div><!-- /.sc_team_wrap -->';
	
		// Add template specific scripts and styles
		do_action('organic_beauty_action_blog_scripts', $style);
	
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_team', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_team', 'organic_beauty_sc_team');
}


if ( !function_exists( 'organic_beauty_sc_team_item' ) ) {
	function organic_beauty_sc_team_item($atts, $content=null) {
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts( array(
			// Individual params
			"user" => "",
			"member" => "",
			"name" => "",
			"position" => "",
			"photo" => "",
			"email" => "",
			"link" => "",
			"socials" => "",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => ""
		), $atts)));
	
		organic_beauty_storage_inc_array('sc_team_data', 'counter');
	
		$id = $id ? $id : (organic_beauty_storage_get_array('sc_team_data', 'id') ? organic_beauty_storage_get_array('sc_team_data', 'id') . '_' . organic_beauty_storage_get_array('sc_team_data', 'counter') : '');
	
		$descr = trim(chop(do_shortcode($content)));
	
		$thumb_sizes = organic_beauty_get_thumb_sizes(array('layout' => organic_beauty_storage_get_array('sc_team_data', 'style')));
	
		if (!empty($socials)) $socials = organic_beauty_do_shortcode('[trx_socials size="tiny" shape="round" socials="'.esc_attr($socials).'"][/trx_socials]');
	
		if (!empty($user) && $user!='none' && ($user_obj = get_user_by('login', $user)) != false) {
			$meta = get_user_meta($user_obj->ID);
			if (empty($email))		$email = $user_obj->data->user_email;
			if (empty($name))		$name = $user_obj->data->display_name;
			if (empty($position))	$position = isset($meta['user_position'][0]) ? $meta['user_position'][0] : '';
			if (empty($descr))		$descr = isset($meta['description'][0]) ? $meta['description'][0] : '';
			if (empty($socials))	$socials = organic_beauty_show_user_socials(array('author_id'=>$user_obj->ID, 'echo'=>false));
		}
	
		if (!empty($member) && $member!='none' && ($member_obj = (intval($member) > 0 ? get_post($member, OBJECT) : get_page_by_title($member, OBJECT, 'team'))) != null) {
			if (empty($name))		$name = $member_obj->post_title;
			if (empty($descr))		$descr = $member_obj->post_excerpt;
			$post_meta = get_post_meta($member_obj->ID, organic_beauty_storage_get('options_prefix').'_team_data', true);
			if (empty($position))	$position = $post_meta['team_member_position'];
			if (empty($link))		$link = !empty($post_meta['team_member_link']) ? $post_meta['team_member_link'] : get_permalink($member_obj->ID);
			if (empty($email))		$email = $post_meta['team_member_email'];
			if (empty($photo)) 		$photo = wp_get_attachment_url(get_post_thumbnail_id($member_obj->ID));
			if (empty($socials)) {
				$socials = '';
				$soc_list = $post_meta['team_member_socials'];
				if (is_array($soc_list) && count($soc_list)>0) {
					$soc_str = '';
					foreach ($soc_list as $sn=>$sl) {
						if (!empty($sl))
							$soc_str .= (!empty($soc_str) ? '|' : '') . ($sn) . '=' . ($sl);
					}
					if (!empty($soc_str))
						$socials = organic_beauty_do_shortcode('[trx_socials size="tiny" shape="round" socials="'.esc_attr($soc_str).'"][/trx_socials]');
				}
			}
		}
		if (empty($photo)) {
			$mult = organic_beauty_get_retina_multiplier();
			if (!empty($email)) $photo = get_avatar($email, $thumb_sizes['w']*$mult);
		} else {
			if ($photo > 0) {
				$attach = wp_get_attachment_image_src( $photo, 'full' );
				if (isset($attach[0]) && $attach[0]!='')
					$photo = $attach[0];
			}
			$photo = organic_beauty_get_resized_image_tag($photo, $thumb_sizes['w'], $thumb_sizes['h']);
		}
		$post_data = array(
			'post_title' => $name,
			'post_excerpt' => $descr
		);
		$args = array(
			'layout' => organic_beauty_storage_get_array('sc_team_data', 'style'),
			'number' => organic_beauty_storage_get_array('sc_team_data', 'counter'),
			'columns_count' => organic_beauty_storage_get_array('sc_team_data', 'columns'),
			'slider' => organic_beauty_storage_get_array('sc_team_data', 'slider'),
			'show' => false,
			'descr'  => 0,
			'tag_id' => $id,
			'tag_class' => $class,
			'tag_animation' => $animation,
			'tag_css' => $css,
			'tag_css_wh' => organic_beauty_storage_get_array('sc_team_data', 'css_wh'),
			'position' => $position,
			'link' => $link,
			'email' => $email,
			'photo' => $photo,
			'socials' => $socials
		);
		$output = organic_beauty_show_post_layout($args, $post_data);

		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_team_item', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_team_item', 'organic_beauty_sc_team_item');
}
// ---------------------------------- [/trx_team] ---------------------------------------



// Add [trx_team] and [trx_team_item] in the shortcodes list
if (!function_exists('organic_beauty_team_reg_shortcodes')) {
	//add_filter('organic_beauty_action_shortcodes_list',	'organic_beauty_team_reg_shortcodes');
	function organic_beauty_team_reg_shortcodes() {
		if (organic_beauty_storage_isset('shortcodes')) {

			$users = organic_beauty_get_list_users();
			$members = organic_beauty_get_list_posts(false, array(
				'post_type'=>'team',
				'orderby'=>'title',
				'order'=>'asc',
				'return'=>'title'
				)
			);
			$team_groups = organic_beauty_get_list_terms(false, 'team_group');
			$team_styles = organic_beauty_get_list_templates('team');
			$controls	 = organic_beauty_get_list_slider_controls();

			organic_beauty_sc_map_after('trx_tabs', array(

				// Team
				"trx_team" => array(
					"title" => esc_html__("Team", 'organic-beauty'),
					"desc" => wp_kses_data( __("Insert team in your page (post)", 'organic-beauty') ),
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
							"title" => esc_html__("Team style", 'organic-beauty'),
							"desc" => wp_kses_data( __("Select style to display team members", 'organic-beauty') ),
							"value" => "team-1",
							"type" => "select",
							"options" => $team_styles
						),
						"columns" => array(
							"title" => esc_html__("Columns", 'organic-beauty'),
							"desc" => wp_kses_data( __("How many columns use to show team members", 'organic-beauty') ),
							"value" => 3,
							"min" => 2,
							"max" => 5,
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
							"desc" => wp_kses_data( __("Use slider to show team members", 'organic-beauty') ),
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
							"desc" => wp_kses_data( __("Alignment of the team block", 'organic-beauty') ),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => organic_beauty_get_sc_param('align')
						),
						"custom" => array(
							"title" => esc_html__("Custom", 'organic-beauty'),
							"desc" => wp_kses_data( __("Allow get team members from inner shortcodes (custom) or get it from specified group (cat)", 'organic-beauty') ),
							"divider" => true,
							"value" => "no",
							"type" => "switch",
							"options" => organic_beauty_get_sc_param('yes_no')
						),
						"cat" => array(
							"title" => esc_html__("Categories", 'organic-beauty'),
							"desc" => wp_kses_data( __("Select categories (groups) to show team members. If empty - select team members from any category (group) or from IDs list", 'organic-beauty') ),
							"dependency" => array(
								'custom' => array('no')
							),
							"divider" => true,
							"value" => "",
							"type" => "select",
							"style" => "list",
							"multiple" => true,
							"options" => organic_beauty_array_merge(array(0 => esc_html__('- Select category -', 'organic-beauty')), $team_groups)
						),
						"count" => array(
							"title" => esc_html__("Number of posts", 'organic-beauty'),
							"desc" => wp_kses_data( __("How many posts will be displayed? If used IDs - this parameter ignored.", 'organic-beauty') ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => 3,
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
							"value" => "title",
							"type" => "select",
							"options" => organic_beauty_get_sc_param('sorting')
						),
						"order" => array(
							"title" => esc_html__("Post order", 'organic-beauty'),
							"desc" => wp_kses_data( __("Select desired posts order", 'organic-beauty') ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "asc",
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
						"name" => "trx_team_item",
						"title" => esc_html__("Member", 'organic-beauty'),
						"desc" => wp_kses_data( __("Team member", 'organic-beauty') ),
						"container" => true,
						"params" => array(
							"user" => array(
								"title" => esc_html__("Registerd user", 'organic-beauty'),
								"desc" => wp_kses_data( __("Select one of registered users (if present) or put name, position, etc. in fields below", 'organic-beauty') ),
								"value" => "",
								"type" => "select",
								"options" => $users
							),
							"member" => array(
								"title" => esc_html__("Team member", 'organic-beauty'),
								"desc" => wp_kses_data( __("Select one of team members (if present) or put name, position, etc. in fields below", 'organic-beauty') ),
								"value" => "",
								"type" => "select",
								"options" => $members
							),
							"link" => array(
								"title" => esc_html__("Link", 'organic-beauty'),
								"desc" => wp_kses_data( __("Link on team member's personal page", 'organic-beauty') ),
								"divider" => true,
								"value" => "",
								"type" => "text"
							),
							"name" => array(
								"title" => esc_html__("Name", 'organic-beauty'),
								"desc" => wp_kses_data( __("Team member's name", 'organic-beauty') ),
								"divider" => true,
								"dependency" => array(
									'user' => array('is_empty', 'none'),
									'member' => array('is_empty', 'none')
								),
								"value" => "",
								"type" => "text"
							),
							"position" => array(
								"title" => esc_html__("Position", 'organic-beauty'),
								"desc" => wp_kses_data( __("Team member's position", 'organic-beauty') ),
								"dependency" => array(
									'user' => array('is_empty', 'none'),
									'member' => array('is_empty', 'none')
								),
								"value" => "",
								"type" => "text"
							),
							"email" => array(
								"title" => esc_html__("E-mail", 'organic-beauty'),
								"desc" => wp_kses_data( __("Team member's e-mail", 'organic-beauty') ),
								"dependency" => array(
									'user' => array('is_empty', 'none'),
									'member' => array('is_empty', 'none')
								),
								"value" => "",
								"type" => "text"
							),
							"photo" => array(
								"title" => esc_html__("Photo", 'organic-beauty'),
								"desc" => wp_kses_data( __("Team member's photo (avatar)", 'organic-beauty') ),
								"dependency" => array(
									'user' => array('is_empty', 'none'),
									'member' => array('is_empty', 'none')
								),
								"value" => "",
								"readonly" => false,
								"type" => "media"
							),
							"socials" => array(
								"title" => esc_html__("Socials", 'organic-beauty'),
								"desc" => wp_kses_data( __("Team member's socials icons: name=url|name=url... For example: facebook=http://facebook.com/myaccount|twitter=http://twitter.com/myaccount", 'organic-beauty') ),
								"dependency" => array(
									'user' => array('is_empty', 'none'),
									'member' => array('is_empty', 'none')
								),
								"value" => "",
								"type" => "text"
							),
							"_content_" => array(
								"title" => esc_html__("Description", 'organic-beauty'),
								"desc" => wp_kses_data( __("Team member's short description", 'organic-beauty') ),
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


// Add [trx_team] and [trx_team_item] in the VC shortcodes list
if (!function_exists('organic_beauty_team_reg_shortcodes_vc')) {
	//add_filter('organic_beauty_action_shortcodes_list_vc',	'organic_beauty_team_reg_shortcodes_vc');
	function organic_beauty_team_reg_shortcodes_vc() {

		$users = organic_beauty_get_list_users();
		$members = organic_beauty_get_list_posts(false, array(
			'post_type'=>'team',
			'orderby'=>'title',
			'order'=>'asc',
			'return'=>'title'
			)
		);
		$team_groups = organic_beauty_get_list_terms(false, 'team_group');
		$team_styles = organic_beauty_get_list_templates('team');
		$controls	 = organic_beauty_get_list_slider_controls();

		// Team
		vc_map( array(
				"base" => "trx_team",
				"name" => esc_html__("Team", 'organic-beauty'),
				"description" => wp_kses_data( __("Insert team members", 'organic-beauty') ),
				"category" => esc_html__('Content', 'organic-beauty'),
				'icon' => 'icon_trx_team',
				"class" => "trx_sc_columns trx_sc_team",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => true,
				"as_parent" => array('only' => 'trx_team_item'),
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => esc_html__("Team style", 'organic-beauty'),
						"description" => wp_kses_data( __("Select style to display team members", 'organic-beauty') ),
						"class" => "",
						"admin_label" => true,
						"value" => array_flip($team_styles),
						"type" => "dropdown"
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
						"description" => wp_kses_data( __("Use slider to show team members", 'organic-beauty') ),
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
						"description" => wp_kses_data( __("Alignment of the team block", 'organic-beauty') ),
						"class" => "",
						"value" => array_flip((array)organic_beauty_get_sc_param('align')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "custom",
						"heading" => esc_html__("Custom", 'organic-beauty'),
						"description" => wp_kses_data( __("Allow get team members from inner shortcodes (custom) or get it from specified group (cat)", 'organic-beauty') ),
						"class" => "",
						"value" => array("Custom members" => "yes" ),
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
						"description" => wp_kses_data( __("Select category to show team members. If empty - select team members from any category (group) or from IDs list", 'organic-beauty') ),
						"group" => esc_html__('Query', 'organic-beauty'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => array_flip((array)organic_beauty_array_merge(array(0 => esc_html__('- Select category -', 'organic-beauty')), $team_groups)),
						"type" => "dropdown"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", 'organic-beauty'),
						"description" => wp_kses_data( __("How many columns use to show team members", 'organic-beauty') ),
						"group" => esc_html__('Query', 'organic-beauty'),
						"admin_label" => true,
						"class" => "",
						"value" => "3",
						"type" => "textfield"
					),
					array(
						"param_name" => "count",
						"heading" => esc_html__("Number of posts", 'organic-beauty'),
						"description" => wp_kses_data( __("How many posts will be displayed? If used IDs - this parameter ignored.", 'organic-beauty') ),
						"group" => esc_html__('Query', 'organic-beauty'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "3",
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
						"std" => "title",
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
						"std" => "asc",
						"class" => "",
						"value" => array_flip((array)organic_beauty_get_sc_param('ordering')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "ids",
						"heading" => esc_html__("Team member's IDs list", 'organic-beauty'),
						"description" => wp_kses_data( __("Comma separated list of team members's ID. If set - parameters above (category, count, order, etc.)  are ignored!", 'organic-beauty') ),
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
					[trx_team_item user="' . esc_html__( 'Member 1', 'organic-beauty' ) . '"][/trx_team_item]
					[trx_team_item user="' . esc_html__( 'Member 2', 'organic-beauty' ) . '"][/trx_team_item]
					[trx_team_item user="' . esc_html__( 'Member 4', 'organic-beauty' ) . '"][/trx_team_item]
				',
				'js_view' => 'VcTrxColumnsView'
			) );
			
			
		vc_map( array(
				"base" => "trx_team_item",
				"name" => esc_html__("Team member", 'organic-beauty'),
				"description" => wp_kses_data( __("Team member - all data pull out from it account on your site", 'organic-beauty') ),
				"show_settings_on_create" => true,
				"class" => "trx_sc_collection trx_sc_column_item trx_sc_team_item",
				"content_element" => true,
				"is_container" => true,
				'icon' => 'icon_trx_team_item',
				"as_child" => array('only' => 'trx_team'),
				"as_parent" => array('except' => 'trx_team'),
				"params" => array(
					array(
						"param_name" => "user",
						"heading" => esc_html__("Registered user", 'organic-beauty'),
						"description" => wp_kses_data( __("Select one of registered users (if present) or put name, position, etc. in fields below", 'organic-beauty') ),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($users),
						"type" => "dropdown"
					),
					array(
						"param_name" => "member",
						"heading" => esc_html__("Team member", 'organic-beauty'),
						"description" => wp_kses_data( __("Select one of team members (if present) or put name, position, etc. in fields below", 'organic-beauty') ),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($members),
						"type" => "dropdown"
					),
					array(
						"param_name" => "link",
						"heading" => esc_html__("Link", 'organic-beauty'),
						"description" => wp_kses_data( __("Link on team member's personal page", 'organic-beauty') ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "name",
						"heading" => esc_html__("Name", 'organic-beauty'),
						"description" => wp_kses_data( __("Team member's name", 'organic-beauty') ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "position",
						"heading" => esc_html__("Position", 'organic-beauty'),
						"description" => wp_kses_data( __("Team member's position", 'organic-beauty') ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "email",
						"heading" => esc_html__("E-mail", 'organic-beauty'),
						"description" => wp_kses_data( __("Team member's e-mail", 'organic-beauty') ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "photo",
						"heading" => esc_html__("Member's Photo", 'organic-beauty'),
						"description" => wp_kses_data( __("Team member's photo (avatar)", 'organic-beauty') ),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "socials",
						"heading" => esc_html__("Socials", 'organic-beauty'),
						"description" => wp_kses_data( __("Team member's socials icons: name=url|name=url... For example: facebook=http://facebook.com/myaccount|twitter=http://twitter.com/myaccount", 'organic-beauty') ),
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
			
		class WPBakeryShortCode_Trx_Team extends ORGANIC_BEAUTY_VC_ShortCodeColumns {}
		class WPBakeryShortCode_Trx_Team_Item extends ORGANIC_BEAUTY_VC_ShortCodeCollection {}

	}
}
?>