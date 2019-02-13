<?php
/**
 * Organic Beauty Framework: return lists
 *
 * @package organic_beauty
 * @since organic_beauty 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }



// Return styles list
if ( !function_exists( 'organic_beauty_get_list_styles' ) ) {
	function organic_beauty_get_list_styles($from=1, $to=2, $prepend_inherit=false) {
		$list = array();
		for ($i=$from; $i<=$to; $i++)
			$list[$i] = sprintf(esc_html__('Style %d', 'organic-beauty'), $i);
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return list of the shortcodes margins
if ( !function_exists( 'organic_beauty_get_list_margins' ) ) {
	function organic_beauty_get_list_margins($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_margins'))=='') {
			$list = array(
				'null'		=> esc_html__('0 (No margin)',	'organic-beauty'),
				'tiny'		=> esc_html__('Tiny',		'organic-beauty'),
				'small'		=> esc_html__('Small',		'organic-beauty'),
				'medium'	=> esc_html__('Medium',		'organic-beauty'),
				'large'		=> esc_html__('Large',		'organic-beauty'),
				'huge'		=> esc_html__('Huge',		'organic-beauty'),
				'tiny-'		=> esc_html__('Tiny (negative)',	'organic-beauty'),
				'small-'	=> esc_html__('Small (negative)',	'organic-beauty'),
				'medium-'	=> esc_html__('Medium (negative)',	'organic-beauty'),
				'large-'	=> esc_html__('Large (negative)',	'organic-beauty'),
				'huge-'		=> esc_html__('Huge (negative)',	'organic-beauty')
				);
			$list = apply_filters('organic_beauty_filter_list_margins', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_margins', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return list of the line styles
if ( !function_exists( 'organic_beauty_get_list_line_styles' ) ) {
	function organic_beauty_get_list_line_styles($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_line_styles'))=='') {
			$list = array(
				'solid'	=> esc_html__('Solid', 'organic-beauty'),
				'dashed'=> esc_html__('Dashed', 'organic-beauty'),
				'dotted'=> esc_html__('Dotted', 'organic-beauty'),
				'double'=> esc_html__('Double', 'organic-beauty'),
				'image'	=> esc_html__('Image', 'organic-beauty')
				);
			$list = apply_filters('organic_beauty_filter_list_line_styles', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_line_styles', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return list of the animations
if ( !function_exists( 'organic_beauty_get_list_animations' ) ) {
	function organic_beauty_get_list_animations($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_animations'))=='') {
			$list = array(
				'none'			=> esc_html__('- None -',	'organic-beauty'),
				'bounce'		=> esc_html__('Bounce',		'organic-beauty'),
				'elastic'		=> esc_html__('Elastic',	'organic-beauty'),
				'flash'			=> esc_html__('Flash',		'organic-beauty'),
				'flip'			=> esc_html__('Flip',		'organic-beauty'),
				'pulse'			=> esc_html__('Pulse',		'organic-beauty'),
				'rubberBand'	=> esc_html__('Rubber Band','organic-beauty'),
				'shake'			=> esc_html__('Shake',		'organic-beauty'),
				'swing'			=> esc_html__('Swing',		'organic-beauty'),
				'tada'			=> esc_html__('Tada',		'organic-beauty'),
				'wobble'		=> esc_html__('Wobble',		'organic-beauty')
				);
			$list = apply_filters('organic_beauty_filter_list_animations', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_animations', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return list of the enter animations
if ( !function_exists( 'organic_beauty_get_list_animations_in' ) ) {
	function organic_beauty_get_list_animations_in($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_animations_in'))=='') {
			$list = array(
				'none'				=> esc_html__('- None -',			'organic-beauty'),
				'bounceIn'			=> esc_html__('Bounce In',			'organic-beauty'),
				'bounceInUp'		=> esc_html__('Bounce In Up',		'organic-beauty'),
				'bounceInDown'		=> esc_html__('Bounce In Down',		'organic-beauty'),
				'bounceInLeft'		=> esc_html__('Bounce In Left',		'organic-beauty'),
				'bounceInRight'		=> esc_html__('Bounce In Right',	'organic-beauty'),
				'elastic'			=> esc_html__('Elastic In',			'organic-beauty'),
				'fadeIn'			=> esc_html__('Fade In',			'organic-beauty'),
				'fadeInUp'			=> esc_html__('Fade In Up',			'organic-beauty'),
				'fadeInUpSmall'		=> esc_html__('Fade In Up Small',	'organic-beauty'),
				'fadeInUpBig'		=> esc_html__('Fade In Up Big',		'organic-beauty'),
				'fadeInDown'		=> esc_html__('Fade In Down',		'organic-beauty'),
				'fadeInDownBig'		=> esc_html__('Fade In Down Big',	'organic-beauty'),
				'fadeInLeft'		=> esc_html__('Fade In Left',		'organic-beauty'),
				'fadeInLeftBig'		=> esc_html__('Fade In Left Big',	'organic-beauty'),
				'fadeInRight'		=> esc_html__('Fade In Right',		'organic-beauty'),
				'fadeInRightBig'	=> esc_html__('Fade In Right Big',	'organic-beauty'),
				'flipInX'			=> esc_html__('Flip In X',			'organic-beauty'),
				'flipInY'			=> esc_html__('Flip In Y',			'organic-beauty'),
				'lightSpeedIn'		=> esc_html__('Light Speed In',		'organic-beauty'),
				'rotateIn'			=> esc_html__('Rotate In',			'organic-beauty'),
				'rotateInUpLeft'	=> esc_html__('Rotate In Down Left','organic-beauty'),
				'rotateInUpRight'	=> esc_html__('Rotate In Up Right',	'organic-beauty'),
				'rotateInDownLeft'	=> esc_html__('Rotate In Up Left',	'organic-beauty'),
				'rotateInDownRight'	=> esc_html__('Rotate In Down Right','organic-beauty'),
				'rollIn'			=> esc_html__('Roll In',			'organic-beauty'),
				'slideInUp'			=> esc_html__('Slide In Up',		'organic-beauty'),
				'slideInDown'		=> esc_html__('Slide In Down',		'organic-beauty'),
				'slideInLeft'		=> esc_html__('Slide In Left',		'organic-beauty'),
				'slideInRight'		=> esc_html__('Slide In Right',		'organic-beauty'),
				'wipeInLeftTop'		=> esc_html__('Wipe In Left Top',	'organic-beauty'),
				'zoomIn'			=> esc_html__('Zoom In',			'organic-beauty'),
				'zoomInUp'			=> esc_html__('Zoom In Up',			'organic-beauty'),
				'zoomInDown'		=> esc_html__('Zoom In Down',		'organic-beauty'),
				'zoomInLeft'		=> esc_html__('Zoom In Left',		'organic-beauty'),
				'zoomInRight'		=> esc_html__('Zoom In Right',		'organic-beauty')
				);
			$list = apply_filters('organic_beauty_filter_list_animations_in', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_animations_in', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return list of the out animations
if ( !function_exists( 'organic_beauty_get_list_animations_out' ) ) {
	function organic_beauty_get_list_animations_out($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_animations_out'))=='') {
			$list = array(
				'none'				=> esc_html__('- None -',			'organic-beauty'),
				'bounceOut'			=> esc_html__('Bounce Out',			'organic-beauty'),
				'bounceOutUp'		=> esc_html__('Bounce Out Up',		'organic-beauty'),
				'bounceOutDown'		=> esc_html__('Bounce Out Down',	'organic-beauty'),
				'bounceOutLeft'		=> esc_html__('Bounce Out Left',	'organic-beauty'),
				'bounceOutRight'	=> esc_html__('Bounce Out Right',	'organic-beauty'),
				'fadeOut'			=> esc_html__('Fade Out',			'organic-beauty'),
				'fadeOutUp'			=> esc_html__('Fade Out Up',		'organic-beauty'),
				'fadeOutUpBig'		=> esc_html__('Fade Out Up Big',	'organic-beauty'),
				'fadeOutDown'		=> esc_html__('Fade Out Down',		'organic-beauty'),
				'fadeOutDownSmall'	=> esc_html__('Fade Out Down Small','organic-beauty'),
				'fadeOutDownBig'	=> esc_html__('Fade Out Down Big',	'organic-beauty'),
				'fadeOutLeft'		=> esc_html__('Fade Out Left',		'organic-beauty'),
				'fadeOutLeftBig'	=> esc_html__('Fade Out Left Big',	'organic-beauty'),
				'fadeOutRight'		=> esc_html__('Fade Out Right',		'organic-beauty'),
				'fadeOutRightBig'	=> esc_html__('Fade Out Right Big',	'organic-beauty'),
				'flipOutX'			=> esc_html__('Flip Out X',			'organic-beauty'),
				'flipOutY'			=> esc_html__('Flip Out Y',			'organic-beauty'),
				'hinge'				=> esc_html__('Hinge Out',			'organic-beauty'),
				'lightSpeedOut'		=> esc_html__('Light Speed Out',	'organic-beauty'),
				'rotateOut'			=> esc_html__('Rotate Out',			'organic-beauty'),
				'rotateOutUpLeft'	=> esc_html__('Rotate Out Down Left','organic-beauty'),
				'rotateOutUpRight'	=> esc_html__('Rotate Out Up Right','organic-beauty'),
				'rotateOutDownLeft'	=> esc_html__('Rotate Out Up Left',	'organic-beauty'),
				'rotateOutDownRight'=> esc_html__('Rotate Out Down Right','organic-beauty'),
				'rollOut'			=> esc_html__('Roll Out',			'organic-beauty'),
				'slideOutUp'		=> esc_html__('Slide Out Up',		'organic-beauty'),
				'slideOutDown'		=> esc_html__('Slide Out Down',		'organic-beauty'),
				'slideOutLeft'		=> esc_html__('Slide Out Left',		'organic-beauty'),
				'slideOutRight'		=> esc_html__('Slide Out Right',	'organic-beauty'),
				'zoomOut'			=> esc_html__('Zoom Out',			'organic-beauty'),
				'zoomOutUp'			=> esc_html__('Zoom Out Up',		'organic-beauty'),
				'zoomOutDown'		=> esc_html__('Zoom Out Down',		'organic-beauty'),
				'zoomOutLeft'		=> esc_html__('Zoom Out Left',		'organic-beauty'),
				'zoomOutRight'		=> esc_html__('Zoom Out Right',		'organic-beauty')
				);
			$list = apply_filters('organic_beauty_filter_list_animations_out', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_animations_out', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return classes list for the specified animation
if (!function_exists('organic_beauty_get_animation_classes')) {
	function organic_beauty_get_animation_classes($animation, $speed='normal', $loop='none') {
		// speed:	fast=0.5s | normal=1s | slow=2s
		// loop:	none | infinite
		return organic_beauty_param_is_off($animation) ? '' : 'animated '.esc_attr($animation).' '.esc_attr($speed).(!organic_beauty_param_is_off($loop) ? ' '.esc_attr($loop) : '');
	}
}


// Return list of the main menu hover effects
if ( !function_exists( 'organic_beauty_get_list_menu_hovers' ) ) {
	function organic_beauty_get_list_menu_hovers($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_menu_hovers'))=='') {
			$list = array(
				'fade'			=> esc_html__('Fade',		'organic-beauty'),
				'slide_line'	=> esc_html__('Slide Line',	'organic-beauty'),
				'slide_box'		=> esc_html__('Slide Box',	'organic-beauty'),
				'zoom_line'		=> esc_html__('Zoom Line',	'organic-beauty'),
				'path_line'		=> esc_html__('Path Line',	'organic-beauty'),
				'roll_down'		=> esc_html__('Roll Down',	'organic-beauty'),
				'color_line'	=> esc_html__('Color Line',	'organic-beauty'),
				);
			$list = apply_filters('organic_beauty_filter_list_menu_hovers', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_menu_hovers', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return list of the button's hover effects
if ( !function_exists( 'organic_beauty_get_list_button_hovers' ) ) {
	function organic_beauty_get_list_button_hovers($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_button_hovers'))=='') {
			$list = array(
				'default'		=> esc_html__('Default',			'organic-beauty'),
				'fade'			=> esc_html__('Fade',				'organic-beauty'),
				//'slide_left'	=> esc_html__('Slide from Left',	'organic-beauty'),
				//'slide_top'	=> esc_html__('Slide from Top',		'organic-beauty'),
				'arrow'			=> esc_html__('Arrow',				'organic-beauty'),
				);
			$list = apply_filters('organic_beauty_filter_list_button_hovers', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_button_hovers', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return list of the input field's hover effects
if ( !function_exists( 'organic_beauty_get_list_input_hovers' ) ) {
	function organic_beauty_get_list_input_hovers($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_input_hovers'))=='') {
			$list = array(
				'default'	=> esc_html__('Default',	'organic-beauty'),
				'accent'	=> esc_html__('Accented',	'organic-beauty'),
				'path'		=> esc_html__('Path',		'organic-beauty'),
				'jump'		=> esc_html__('Jump',		'organic-beauty'),
				'underline'	=> esc_html__('Underline',	'organic-beauty'),
				'iconed'	=> esc_html__('Iconed',		'organic-beauty'),
				);
			$list = apply_filters('organic_beauty_filter_list_input_hovers', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_input_hovers', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return list of the search field's styles
if ( !function_exists( 'organic_beauty_get_list_search_styles' ) ) {
	function organic_beauty_get_list_search_styles($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_search_styles'))=='') {
			$list = array(
				'default'	=> esc_html__('Default',	'organic-beauty'),
				'fullscreen'=> esc_html__('Fullscreen',	'organic-beauty'),
				//'slide'		=> esc_html__('Slide',		'organic-beauty'),
				'expand'	=> esc_html__('Expand',		'organic-beauty'),
				);
			$list = apply_filters('organic_beauty_filter_list_search_styles', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_search_styles', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return list of categories
if ( !function_exists( 'organic_beauty_get_list_categories' ) ) {
	function organic_beauty_get_list_categories($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_categories'))=='') {
			$list = array();
			$args = array(
				'type'                     => 'post',
				'child_of'                 => 0,
				'parent'                   => '',
				'orderby'                  => 'name',
				'order'                    => 'ASC',
				'hide_empty'               => 0,
				'hierarchical'             => 1,
				'exclude'                  => '',
				'include'                  => '',
				'number'                   => '',
				'taxonomy'                 => 'category',
				'pad_counts'               => false );
			$taxonomies = get_categories( $args );
			if (is_array($taxonomies) && count($taxonomies) > 0) {
				foreach ($taxonomies as $cat) {
					$list[$cat->term_id] = $cat->name;
				}
			}
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_categories', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return list of taxonomies
if ( !function_exists( 'organic_beauty_get_list_terms' ) ) {
	function organic_beauty_get_list_terms($prepend_inherit=false, $taxonomy='category') {
		if (($list = organic_beauty_storage_get('list_taxonomies_'.($taxonomy)))=='') {
			$list = array();
			if ( is_array($taxonomy) || taxonomy_exists($taxonomy) ) {
				$terms = get_terms( $taxonomy, array(
					'child_of'                 => 0,
					'parent'                   => '',
					'orderby'                  => 'name',
					'order'                    => 'ASC',
					'hide_empty'               => 0,
					'hierarchical'             => 1,
					'exclude'                  => '',
					'include'                  => '',
					'number'                   => '',
					'taxonomy'                 => $taxonomy,
					'pad_counts'               => false
					)
				);
			} else {
				$terms = organic_beauty_get_terms_by_taxonomy_from_db($taxonomy);
			}
			if (!is_wp_error( $terms ) && is_array($terms) && count($terms) > 0) {
				foreach ($terms as $cat) {
					$list[$cat->term_id] = $cat->name;	// . ($taxonomy!='category' ? ' /'.($cat->taxonomy).'/' : '');
				}
			}
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_taxonomies_'.($taxonomy), $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return list of post's types
if ( !function_exists( 'organic_beauty_get_list_posts_types' ) ) {
	function organic_beauty_get_list_posts_types($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_posts_types'))=='') {
			// Return only theme inheritance supported post types
			$list = apply_filters('organic_beauty_filter_list_post_types', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_posts_types', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return list post items from any post type and taxonomy
if ( !function_exists( 'organic_beauty_get_list_posts' ) ) {
	function organic_beauty_get_list_posts($prepend_inherit=false, $opt=array()) {
		$opt = array_merge(array(
			'post_type'			=> 'post',
			'post_status'		=> 'publish',
			'taxonomy'			=> 'category',
			'taxonomy_value'	=> '',
			'posts_per_page'	=> -1,
			'orderby'			=> 'post_date',
			'order'				=> 'desc',
			'return'			=> 'id'
			), is_array($opt) ? $opt : array('post_type'=>$opt));

		$hash = 'list_posts_'.($opt['post_type']).'_'.($opt['taxonomy']).'_'.($opt['taxonomy_value']).'_'.($opt['orderby']).'_'.($opt['order']).'_'.($opt['return']).'_'.($opt['posts_per_page']);
		if (($list = organic_beauty_storage_get($hash))=='') {
			$list = array();
			$list['none'] = esc_html__("- Not selected -", 'organic-beauty');
			$args = array(
				'post_type' => $opt['post_type'],
				'post_status' => $opt['post_status'],
				'posts_per_page' => $opt['posts_per_page'],
				'ignore_sticky_posts' => true,
				'orderby'	=> $opt['orderby'],
				'order'		=> $opt['order']
			);
			if (!empty($opt['taxonomy_value'])) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => $opt['taxonomy'],
						'field' => (int) $opt['taxonomy_value'] > 0 ? 'id' : 'slug',
						'terms' => $opt['taxonomy_value']
					)
				);
			}
			$posts = get_posts( $args );
			if (is_array($posts) && count($posts) > 0) {
				foreach ($posts as $post) {
					$list[$opt['return']=='id' ? $post->ID : $post->post_title] = $post->post_title;
				}
			}
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set($hash, $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return list pages
if ( !function_exists( 'organic_beauty_get_list_pages' ) ) {
	function organic_beauty_get_list_pages($prepend_inherit=false, $opt=array()) {
		$opt = array_merge(array(
			'post_type'			=> 'page',
			'post_status'		=> 'publish',
			'posts_per_page'	=> -1,
			'orderby'			=> 'title',
			'order'				=> 'asc',
			'return'			=> 'id'
			), is_array($opt) ? $opt : array('post_type'=>$opt));
		return organic_beauty_get_list_posts($prepend_inherit, $opt);
	}
}


// Return list of registered users
if ( !function_exists( 'organic_beauty_get_list_users' ) ) {
	function organic_beauty_get_list_users($prepend_inherit=false, $roles=array('administrator', 'editor', 'author', 'contributor', 'shop_manager')) {
		if (($list = organic_beauty_storage_get('list_users'))=='') {
			$list = array();
			$list['none'] = esc_html__("- Not selected -", 'organic-beauty');
			$args = array(
				'orderby'	=> 'display_name',
				'order'		=> 'ASC' );
			$users = get_users( $args );
			if (is_array($users) && count($users) > 0) {
				foreach ($users as $user) {
					$accept = true;
					if (is_array($user->roles)) {
						if (is_array($user->roles) && count($user->roles) > 0) {
							$accept = false;
							foreach ($user->roles as $role) {
								if (in_array($role, $roles)) {
									$accept = true;
									break;
								}
							}
						}
					}
					if ($accept) $list[$user->user_login] = $user->display_name;
				}
			}
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_users', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return slider engines list, prepended inherit (if need)
if ( !function_exists( 'organic_beauty_get_list_sliders' ) ) {
	function organic_beauty_get_list_sliders($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_sliders'))=='') {
			$list = array(
				'swiper' => esc_html__("Posts slider (Swiper)", 'organic-beauty')
			);
			$list = apply_filters('organic_beauty_filter_list_sliders', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_sliders', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return slider controls list, prepended inherit (if need)
if ( !function_exists( 'organic_beauty_get_list_slider_controls' ) ) {
	function organic_beauty_get_list_slider_controls($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_slider_controls'))=='') {
			$list = array(
				'no'		=> esc_html__('None', 'organic-beauty'),
				'side'		=> esc_html__('Side', 'organic-beauty'),
				'bottom'	=> esc_html__('Bottom', 'organic-beauty'),
				'pagination'=> esc_html__('Pagination', 'organic-beauty')
				);
			$list = apply_filters('organic_beauty_filter_list_slider_controls', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_slider_controls', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return slider controls classes
if ( !function_exists( 'organic_beauty_get_slider_controls_classes' ) ) {
	function organic_beauty_get_slider_controls_classes($controls) {
		if (organic_beauty_param_is_off($controls))	$classes = 'sc_slider_nopagination sc_slider_nocontrols';
		else if ($controls=='bottom')			$classes = 'sc_slider_nopagination sc_slider_controls sc_slider_controls_bottom';
		else if ($controls=='pagination')		$classes = 'sc_slider_pagination sc_slider_pagination_bottom sc_slider_nocontrols';
		else									$classes = 'sc_slider_nopagination sc_slider_controls sc_slider_controls_side';
		return $classes;
	}
}

// Return list with popup engines
if ( !function_exists( 'organic_beauty_get_list_popup_engines' ) ) {
	function organic_beauty_get_list_popup_engines($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_popup_engines'))=='') {
			$list = array(
				"pretty"	=> esc_html__("Pretty photo", 'organic-beauty'),
				"magnific"	=> esc_html__("Magnific popup", 'organic-beauty')
				);
			$list = apply_filters('organic_beauty_filter_list_popup_engines', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_popup_engines', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return menus list, prepended inherit
if ( !function_exists( 'organic_beauty_get_list_menus' ) ) {
	function organic_beauty_get_list_menus($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_menus'))=='') {
			$list = array();
			$list['default'] = esc_html__("Default", 'organic-beauty');
			$menus = wp_get_nav_menus();
			if (is_array($menus) && count($menus) > 0) {
				foreach ($menus as $menu) {
					$list[$menu->slug] = $menu->name;
				}
			}
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_menus', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return custom sidebars list, prepended inherit and main sidebars item (if need)
if ( !function_exists( 'organic_beauty_get_list_sidebars' ) ) {
	function organic_beauty_get_list_sidebars($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_sidebars'))=='') {
			if (($list = organic_beauty_storage_get('registered_sidebars'))=='') $list = array();
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_sidebars', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return sidebars positions
if ( !function_exists( 'organic_beauty_get_list_sidebars_positions' ) ) {
	function organic_beauty_get_list_sidebars_positions($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_sidebars_positions'))=='') {
			$list = array(
				'none'  => esc_html__('Hide',  'organic-beauty'),
				'left'  => esc_html__('Left',  'organic-beauty'),
				'right' => esc_html__('Right', 'organic-beauty')
				);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_sidebars_positions', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return sidebars class
if ( !function_exists( 'organic_beauty_get_sidebar_class' ) ) {
	function organic_beauty_get_sidebar_class() {
		$sb_main = organic_beauty_get_custom_option('show_sidebar_main');
		return (organic_beauty_param_is_off($sb_main) ? 'sidebar_hide' : 'sidebar_show sidebar_'.($sb_main));
	}
}

// Return body styles list, prepended inherit
if ( !function_exists( 'organic_beauty_get_list_body_styles' ) ) {
	function organic_beauty_get_list_body_styles($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_body_styles'))=='') {
			$list = array(
				'boxed'	=> esc_html__('Boxed',		'organic-beauty'),
				'wide'	=> esc_html__('Wide',		'organic-beauty')
				);
			if (organic_beauty_get_theme_setting('allow_fullscreen')) {
				$list['fullwide']	= esc_html__('Fullwide',	'organic-beauty');
				$list['fullscreen']	= esc_html__('Fullscreen',	'organic-beauty');
			}
			$list = apply_filters('organic_beauty_filter_list_body_styles', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_body_styles', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return templates list, prepended inherit
if ( !function_exists( 'organic_beauty_get_list_templates' ) ) {
	function organic_beauty_get_list_templates($mode='') {
		if (($list = organic_beauty_storage_get('list_templates_'.($mode)))=='') {
			$list = array();
			$tpl = organic_beauty_storage_get('registered_templates');
			if (is_array($tpl) && count($tpl) > 0) {
				foreach ($tpl as $k=>$v) {
					if ($mode=='' || in_array($mode, explode(',', $v['mode'])))
						$list[$k] = !empty($v['icon']) 
									? $v['icon'] 
									: (!empty($v['title']) 
										? $v['title'] 
										: organic_beauty_strtoproper($v['layout'])
										);
				}
			}
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_templates_'.($mode), $list);
		}
		return $list;
	}
}

// Return blog styles list, prepended inherit
if ( !function_exists( 'organic_beauty_get_list_templates_blog' ) ) {
	function organic_beauty_get_list_templates_blog($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_templates_blog'))=='') {
			$list = organic_beauty_get_list_templates('blog');
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_templates_blog', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return blogger styles list, prepended inherit
if ( !function_exists( 'organic_beauty_get_list_templates_blogger' ) ) {
	function organic_beauty_get_list_templates_blogger($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_templates_blogger'))=='') {
			$list = organic_beauty_array_merge(organic_beauty_get_list_templates('blogger'), organic_beauty_get_list_templates('blog'));
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_templates_blogger', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return single page styles list, prepended inherit
if ( !function_exists( 'organic_beauty_get_list_templates_single' ) ) {
	function organic_beauty_get_list_templates_single($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_templates_single'))=='') {
			$list = organic_beauty_get_list_templates('single');
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_templates_single', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return header styles list, prepended inherit
if ( !function_exists( 'organic_beauty_get_list_templates_header' ) ) {
	function organic_beauty_get_list_templates_header($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_templates_header'))=='') {
			$list = organic_beauty_get_list_templates('header');
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_templates_header', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return form styles list, prepended inherit
if ( !function_exists( 'organic_beauty_get_list_templates_forms' ) ) {
	function organic_beauty_get_list_templates_forms($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_templates_forms'))=='') {
			$list = organic_beauty_get_list_templates('forms');
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_templates_forms', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return article styles list, prepended inherit
if ( !function_exists( 'organic_beauty_get_list_article_styles' ) ) {
	function organic_beauty_get_list_article_styles($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_article_styles'))=='') {
			$list = array(
				"boxed"   => esc_html__('Boxed', 'organic-beauty'),
				"stretch" => esc_html__('Stretch', 'organic-beauty')
				);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_article_styles', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return post-formats filters list, prepended inherit
if ( !function_exists( 'organic_beauty_get_list_post_formats_filters' ) ) {
	function organic_beauty_get_list_post_formats_filters($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_post_formats_filters'))=='') {
			$list = array(
				"no"      => esc_html__('All posts', 'organic-beauty'),
				"thumbs"  => esc_html__('With thumbs', 'organic-beauty'),
				"reviews" => esc_html__('With reviews', 'organic-beauty'),
				"video"   => esc_html__('With videos', 'organic-beauty'),
				"audio"   => esc_html__('With audios', 'organic-beauty'),
				"gallery" => esc_html__('With galleries', 'organic-beauty')
				);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_post_formats_filters', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return portfolio filters list, prepended inherit
if ( !function_exists( 'organic_beauty_get_list_portfolio_filters' ) ) {
	function organic_beauty_get_list_portfolio_filters($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_portfolio_filters'))=='') {
			$list = array(
				"hide"		=> esc_html__('Hide', 'organic-beauty'),
				"tags"		=> esc_html__('Tags', 'organic-beauty'),
				"categories"=> esc_html__('Categories', 'organic-beauty')
				);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_portfolio_filters', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return hover styles list, prepended inherit
if ( !function_exists( 'organic_beauty_get_list_hovers' ) ) {
	function organic_beauty_get_list_hovers($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_hovers'))=='') {
			$list = array();
			$list['circle effect1']  = esc_html__('Circle Effect 1',  'organic-beauty');
			$list['circle effect2']  = esc_html__('Circle Effect 2',  'organic-beauty');
			$list['circle effect3']  = esc_html__('Circle Effect 3',  'organic-beauty');
			$list['circle effect4']  = esc_html__('Circle Effect 4',  'organic-beauty');
			$list['circle effect5']  = esc_html__('Circle Effect 5',  'organic-beauty');
			$list['circle effect6']  = esc_html__('Circle Effect 6',  'organic-beauty');
			$list['circle effect7']  = esc_html__('Circle Effect 7',  'organic-beauty');
			$list['circle effect8']  = esc_html__('Circle Effect 8',  'organic-beauty');
			$list['circle effect9']  = esc_html__('Circle Effect 9',  'organic-beauty');
			$list['circle effect10'] = esc_html__('Circle Effect 10',  'organic-beauty');
			$list['circle effect11'] = esc_html__('Circle Effect 11',  'organic-beauty');
			$list['circle effect12'] = esc_html__('Circle Effect 12',  'organic-beauty');
			$list['circle effect13'] = esc_html__('Circle Effect 13',  'organic-beauty');
			$list['circle effect14'] = esc_html__('Circle Effect 14',  'organic-beauty');
			$list['circle effect15'] = esc_html__('Circle Effect 15',  'organic-beauty');
			$list['circle effect16'] = esc_html__('Circle Effect 16',  'organic-beauty');
			$list['circle effect17'] = esc_html__('Circle Effect 17',  'organic-beauty');
			$list['circle effect18'] = esc_html__('Circle Effect 18',  'organic-beauty');
			$list['circle effect19'] = esc_html__('Circle Effect 19',  'organic-beauty');
			$list['circle effect20'] = esc_html__('Circle Effect 20',  'organic-beauty');
			$list['square effect1']  = esc_html__('Square Effect 1',  'organic-beauty');
			$list['square effect2']  = esc_html__('Square Effect 2',  'organic-beauty');
			$list['square effect3']  = esc_html__('Square Effect 3',  'organic-beauty');
			$list['square effect5']  = esc_html__('Square Effect 5',  'organic-beauty');
			$list['square effect6']  = esc_html__('Square Effect 6',  'organic-beauty');
			$list['square effect7']  = esc_html__('Square Effect 7',  'organic-beauty');
			$list['square effect8']  = esc_html__('Square Effect 8',  'organic-beauty');
			$list['square effect9']  = esc_html__('Square Effect 9',  'organic-beauty');
			$list['square effect10'] = esc_html__('Square Effect 10',  'organic-beauty');
			$list['square effect11'] = esc_html__('Square Effect 11',  'organic-beauty');
			$list['square effect12'] = esc_html__('Square Effect 12',  'organic-beauty');
			$list['square effect13'] = esc_html__('Square Effect 13',  'organic-beauty');
			$list['square effect14'] = esc_html__('Square Effect 14',  'organic-beauty');
			$list['square effect15'] = esc_html__('Square Effect 15',  'organic-beauty');
			$list['square effect_dir']   = esc_html__('Square Effect Dir',   'organic-beauty');
			$list['square effect_shift'] = esc_html__('Square Effect Shift', 'organic-beauty');
			$list['square effect_book']  = esc_html__('Square Effect Book',  'organic-beauty');
			$list['square effect_more']  = esc_html__('Square Effect More',  'organic-beauty');
			$list['square effect_fade']  = esc_html__('Square Effect Fade',  'organic-beauty');
			$list['square effect_pull']  = esc_html__('Square Effect Pull',  'organic-beauty');
			$list['square effect_slide'] = esc_html__('Square Effect Slide', 'organic-beauty');
			$list['square effect_border'] = esc_html__('Square Effect Border', 'organic-beauty');
			$list = apply_filters('organic_beauty_filter_portfolio_hovers', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_hovers', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return list of the blog counters
if ( !function_exists( 'organic_beauty_get_list_blog_counters' ) ) {
	function organic_beauty_get_list_blog_counters($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_blog_counters'))=='') {
			$list = array(
				'views'		=> esc_html__('Views', 'organic-beauty'),
				'likes'		=> esc_html__('Likes', 'organic-beauty'),
				'rating'	=> esc_html__('Rating', 'organic-beauty'),
				'comments'	=> esc_html__('Comments', 'organic-beauty')
				);
			$list = apply_filters('organic_beauty_filter_list_blog_counters', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_blog_counters', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return list of the item sizes for the portfolio alter style, prepended inherit
if ( !function_exists( 'organic_beauty_get_list_alter_sizes' ) ) {
	function organic_beauty_get_list_alter_sizes($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_alter_sizes'))=='') {
			$list = array(
					'1_1' => esc_html__('1x1', 'organic-beauty'),
					'1_2' => esc_html__('1x2', 'organic-beauty'),
					'2_1' => esc_html__('2x1', 'organic-beauty'),
					'2_2' => esc_html__('2x2', 'organic-beauty'),
					'1_3' => esc_html__('1x3', 'organic-beauty'),
					'2_3' => esc_html__('2x3', 'organic-beauty'),
					'3_1' => esc_html__('3x1', 'organic-beauty'),
					'3_2' => esc_html__('3x2', 'organic-beauty'),
					'3_3' => esc_html__('3x3', 'organic-beauty')
					);
			$list = apply_filters('organic_beauty_filter_portfolio_alter_sizes', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_alter_sizes', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return extended hover directions list, prepended inherit
if ( !function_exists( 'organic_beauty_get_list_hovers_directions' ) ) {
	function organic_beauty_get_list_hovers_directions($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_hovers_directions'))=='') {
			$list = array(
				'left_to_right' => esc_html__('Left to Right',  'organic-beauty'),
				'right_to_left' => esc_html__('Right to Left',  'organic-beauty'),
				'top_to_bottom' => esc_html__('Top to Bottom',  'organic-beauty'),
				'bottom_to_top' => esc_html__('Bottom to Top',  'organic-beauty'),
				'scale_up'      => esc_html__('Scale Up',  'organic-beauty'),
				'scale_down'    => esc_html__('Scale Down',  'organic-beauty'),
				'scale_down_up' => esc_html__('Scale Down-Up',  'organic-beauty'),
				'from_left_and_right' => esc_html__('From Left and Right',  'organic-beauty'),
				'from_top_and_bottom' => esc_html__('From Top and Bottom',  'organic-beauty')
			);
			$list = apply_filters('organic_beauty_filter_portfolio_hovers_directions', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_hovers_directions', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return list of the label positions in the custom forms
if ( !function_exists( 'organic_beauty_get_list_label_positions' ) ) {
	function organic_beauty_get_list_label_positions($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_label_positions'))=='') {
			$list = array(
				'top'		=> esc_html__('Top',		'organic-beauty'),
				'bottom'	=> esc_html__('Bottom',		'organic-beauty'),
				'left'		=> esc_html__('Left',		'organic-beauty'),
				'over'		=> esc_html__('Over',		'organic-beauty')
			);
			$list = apply_filters('organic_beauty_filter_label_positions', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_label_positions', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return list of the bg image positions
if ( !function_exists( 'organic_beauty_get_list_bg_image_positions' ) ) {
	function organic_beauty_get_list_bg_image_positions($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_bg_image_positions'))=='') {
			$list = array(
				'left top'	   => esc_html__('Left Top', 'organic-beauty'),
				'center top'   => esc_html__("Center Top", 'organic-beauty'),
				'right top'    => esc_html__("Right Top", 'organic-beauty'),
				'left center'  => esc_html__("Left Center", 'organic-beauty'),
				'center center'=> esc_html__("Center Center", 'organic-beauty'),
				'right center' => esc_html__("Right Center", 'organic-beauty'),
				'left bottom'  => esc_html__("Left Bottom", 'organic-beauty'),
				'center bottom'=> esc_html__("Center Bottom", 'organic-beauty'),
				'right bottom' => esc_html__("Right Bottom", 'organic-beauty')
			);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_bg_image_positions', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return list of the bg image repeat
if ( !function_exists( 'organic_beauty_get_list_bg_image_repeats' ) ) {
	function organic_beauty_get_list_bg_image_repeats($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_bg_image_repeats'))=='') {
			$list = array(
				'repeat'	=> esc_html__('Repeat', 'organic-beauty'),
				'repeat-x'	=> esc_html__('Repeat X', 'organic-beauty'),
				'repeat-y'	=> esc_html__('Repeat Y', 'organic-beauty'),
				'no-repeat'	=> esc_html__('No Repeat', 'organic-beauty')
			);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_bg_image_repeats', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return list of the bg image attachment
if ( !function_exists( 'organic_beauty_get_list_bg_image_attachments' ) ) {
	function organic_beauty_get_list_bg_image_attachments($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_bg_image_attachments'))=='') {
			$list = array(
				'scroll'	=> esc_html__('Scroll', 'organic-beauty'),
				'fixed'		=> esc_html__('Fixed', 'organic-beauty'),
				'local'		=> esc_html__('Local', 'organic-beauty')
			);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_bg_image_attachments', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}


// Return list of the bg tints
if ( !function_exists( 'organic_beauty_get_list_bg_tints' ) ) {
	function organic_beauty_get_list_bg_tints($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_bg_tints'))=='') {
			$list = array(
				'white'	=> esc_html__('White', 'organic-beauty'),
				'light'	=> esc_html__('Light', 'organic-beauty'),
				'dark'	=> esc_html__('Dark', 'organic-beauty')
			);
			$list = apply_filters('organic_beauty_filter_bg_tints', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_bg_tints', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return custom fields types list, prepended inherit
if ( !function_exists( 'organic_beauty_get_list_field_types' ) ) {
	function organic_beauty_get_list_field_types($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_field_types'))=='') {
			$list = array(
				'text'     => esc_html__('Text',  'organic-beauty'),
				'textarea' => esc_html__('Text Area','organic-beauty'),
				'password' => esc_html__('Password',  'organic-beauty'),
				'radio'    => esc_html__('Radio',  'organic-beauty'),
				'checkbox' => esc_html__('Checkbox',  'organic-beauty'),
				'select'   => esc_html__('Select',  'organic-beauty'),
				'date'     => esc_html__('Date','organic-beauty'),
				'time'     => esc_html__('Time','organic-beauty'),
				'button'   => esc_html__('Button','organic-beauty')
			);
			$list = apply_filters('organic_beauty_filter_field_types', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_field_types', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return Google map styles
if ( !function_exists( 'organic_beauty_get_list_googlemap_styles' ) ) {
	function organic_beauty_get_list_googlemap_styles($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_googlemap_styles'))=='') {
			$list = array(
				'default' => esc_html__('Default', 'organic-beauty')
			);
			$list = apply_filters('organic_beauty_filter_googlemap_styles', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_googlemap_styles', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return iconed classes list
if ( !function_exists( 'organic_beauty_get_list_icons' ) ) {
	function organic_beauty_get_list_icons($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_icons'))=='') {
			$list = organic_beauty_parse_icons_classes(organic_beauty_get_file_dir("css/fontello/css/fontello-codes.css"));
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_icons', $list);
		}
		return $prepend_inherit ? array_merge(array('inherit'), $list) : $list;
	}
}

// Return socials list
if ( !function_exists( 'organic_beauty_get_list_socials' ) ) {
	function organic_beauty_get_list_socials($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_socials'))=='') {
			$list = organic_beauty_get_list_images("images/socials", "png");
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_socials', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return list with 'Yes' and 'No' items
if ( !function_exists( 'organic_beauty_get_list_yesno' ) ) {
	function organic_beauty_get_list_yesno($prepend_inherit=false) {
		$list = array(
			'yes' => esc_html__("Yes", 'organic-beauty'),
			'no'  => esc_html__("No", 'organic-beauty')
		);
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return list with 'On' and 'Of' items
if ( !function_exists( 'organic_beauty_get_list_onoff' ) ) {
	function organic_beauty_get_list_onoff($prepend_inherit=false) {
		$list = array(
			"on" => esc_html__("On", 'organic-beauty'),
			"off" => esc_html__("Off", 'organic-beauty')
		);
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return list with 'Show' and 'Hide' items
if ( !function_exists( 'organic_beauty_get_list_showhide' ) ) {
	function organic_beauty_get_list_showhide($prepend_inherit=false) {
		$list = array(
			"show" => esc_html__("Show", 'organic-beauty'),
			"hide" => esc_html__("Hide", 'organic-beauty')
		);
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return list with 'Ascending' and 'Descending' items
if ( !function_exists( 'organic_beauty_get_list_orderings' ) ) {
	function organic_beauty_get_list_orderings($prepend_inherit=false) {
		$list = array(
			"asc" => esc_html__("Ascending", 'organic-beauty'),
			"desc" => esc_html__("Descending", 'organic-beauty')
		);
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return list with 'Horizontal' and 'Vertical' items
if ( !function_exists( 'organic_beauty_get_list_directions' ) ) {
	function organic_beauty_get_list_directions($prepend_inherit=false) {
		$list = array(
			"horizontal" => esc_html__("Horizontal", 'organic-beauty'),
			"vertical" => esc_html__("Vertical", 'organic-beauty')
		);
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return list with item's shapes
if ( !function_exists( 'organic_beauty_get_list_shapes' ) ) {
	function organic_beauty_get_list_shapes($prepend_inherit=false) {
		$list = array(
			"round"  => esc_html__("Round", 'organic-beauty'),
			"square" => esc_html__("Square", 'organic-beauty')
		);
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return list with item's sizes
if ( !function_exists( 'organic_beauty_get_list_sizes' ) ) {
	function organic_beauty_get_list_sizes($prepend_inherit=false) {
		$list = array(
			"tiny"   => esc_html__("Tiny", 'organic-beauty'),
			"small"  => esc_html__("Small", 'organic-beauty'),
			"medium" => esc_html__("Medium", 'organic-beauty'),
			"large"  => esc_html__("Large", 'organic-beauty')
		);
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return list with slider (scroll) controls positions
if ( !function_exists( 'organic_beauty_get_list_controls' ) ) {
	function organic_beauty_get_list_controls($prepend_inherit=false) {
		$list = array(
			"hide" => esc_html__("Hide", 'organic-beauty'),
			"side" => esc_html__("Side", 'organic-beauty'),
			"bottom" => esc_html__("Bottom", 'organic-beauty')
		);
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return list with float items
if ( !function_exists( 'organic_beauty_get_list_floats' ) ) {
	function organic_beauty_get_list_floats($prepend_inherit=false) {
		$list = array(
			"none" => esc_html__("None", 'organic-beauty'),
			"left" => esc_html__("Float Left", 'organic-beauty'),
			"right" => esc_html__("Float Right", 'organic-beauty')
		);
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return list with alignment items
if ( !function_exists( 'organic_beauty_get_list_alignments' ) ) {
	function organic_beauty_get_list_alignments($justify=false, $prepend_inherit=false) {
		$list = array(
			"none" => esc_html__("None", 'organic-beauty'),
			"left" => esc_html__("Left", 'organic-beauty'),
			"center" => esc_html__("Center", 'organic-beauty'),
			"right" => esc_html__("Right", 'organic-beauty')
		);
		if ($justify) $list["justify"] = esc_html__("Justify", 'organic-beauty');
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return list with horizontal positions
if ( !function_exists( 'organic_beauty_get_list_hpos' ) ) {
	function organic_beauty_get_list_hpos($prepend_inherit=false, $center=false) {
		$list = array();
		$list['left'] = esc_html__("Left", 'organic-beauty');
		if ($center) $list['center'] = esc_html__("Center", 'organic-beauty');
		$list['right'] = esc_html__("Right", 'organic-beauty');
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return list with vertical positions
if ( !function_exists( 'organic_beauty_get_list_vpos' ) ) {
	function organic_beauty_get_list_vpos($prepend_inherit=false, $center=false) {
		$list = array();
		$list['top'] = esc_html__("Top", 'organic-beauty');
		if ($center) $list['center'] = esc_html__("Center", 'organic-beauty');
		$list['bottom'] = esc_html__("Bottom", 'organic-beauty');
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return sorting list items
if ( !function_exists( 'organic_beauty_get_list_sortings' ) ) {
	function organic_beauty_get_list_sortings($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_sortings'))=='') {
			$list = array(
				"date" => esc_html__("Date", 'organic-beauty'),
				"title" => esc_html__("Alphabetically", 'organic-beauty'),
				"views" => esc_html__("Popular (views count)", 'organic-beauty'),
				"comments" => esc_html__("Most commented (comments count)", 'organic-beauty'),
				"author_rating" => esc_html__("Author rating", 'organic-beauty'),
				"users_rating" => esc_html__("Visitors (users) rating", 'organic-beauty'),
				"random" => esc_html__("Random", 'organic-beauty')
			);
			$list = apply_filters('organic_beauty_filter_list_sortings', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_sortings', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return list with columns widths
if ( !function_exists( 'organic_beauty_get_list_columns' ) ) {
	function organic_beauty_get_list_columns($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_columns'))=='') {
			$list = array(
				"none" => esc_html__("None", 'organic-beauty'),
				"1_1" => esc_html__("100%", 'organic-beauty'),
				"1_2" => esc_html__("1/2", 'organic-beauty'),
				"1_3" => esc_html__("1/3", 'organic-beauty'),
				"2_3" => esc_html__("2/3", 'organic-beauty'),
				"1_4" => esc_html__("1/4", 'organic-beauty'),
				"3_4" => esc_html__("3/4", 'organic-beauty'),
				"1_5" => esc_html__("1/5", 'organic-beauty'),
				"2_5" => esc_html__("2/5", 'organic-beauty'),
				"3_5" => esc_html__("3/5", 'organic-beauty'),
				"4_5" => esc_html__("4/5", 'organic-beauty'),
				"1_6" => esc_html__("1/6", 'organic-beauty'),
				"5_6" => esc_html__("5/6", 'organic-beauty'),
				"1_7" => esc_html__("1/7", 'organic-beauty'),
				"2_7" => esc_html__("2/7", 'organic-beauty'),
				"3_7" => esc_html__("3/7", 'organic-beauty'),
				"4_7" => esc_html__("4/7", 'organic-beauty'),
				"5_7" => esc_html__("5/7", 'organic-beauty'),
				"6_7" => esc_html__("6/7", 'organic-beauty'),
				"1_8" => esc_html__("1/8", 'organic-beauty'),
				"3_8" => esc_html__("3/8", 'organic-beauty'),
				"5_8" => esc_html__("5/8", 'organic-beauty'),
				"7_8" => esc_html__("7/8", 'organic-beauty'),
				"1_9" => esc_html__("1/9", 'organic-beauty'),
				"2_9" => esc_html__("2/9", 'organic-beauty'),
				"4_9" => esc_html__("4/9", 'organic-beauty'),
				"5_9" => esc_html__("5/9", 'organic-beauty'),
				"7_9" => esc_html__("7/9", 'organic-beauty'),
				"8_9" => esc_html__("8/9", 'organic-beauty'),
				"1_10"=> esc_html__("1/10", 'organic-beauty'),
				"3_10"=> esc_html__("3/10", 'organic-beauty'),
				"7_10"=> esc_html__("7/10", 'organic-beauty'),
				"9_10"=> esc_html__("9/10", 'organic-beauty'),
				"1_11"=> esc_html__("1/11", 'organic-beauty'),
				"2_11"=> esc_html__("2/11", 'organic-beauty'),
				"3_11"=> esc_html__("3/11", 'organic-beauty'),
				"4_11"=> esc_html__("4/11", 'organic-beauty'),
				"5_11"=> esc_html__("5/11", 'organic-beauty'),
				"6_11"=> esc_html__("6/11", 'organic-beauty'),
				"7_11"=> esc_html__("7/11", 'organic-beauty'),
				"8_11"=> esc_html__("8/11", 'organic-beauty'),
				"9_11"=> esc_html__("9/11", 'organic-beauty'),
				"10_11"=> esc_html__("10/11", 'organic-beauty'),
				"1_12"=> esc_html__("1/12", 'organic-beauty'),
				"5_12"=> esc_html__("5/12", 'organic-beauty'),
				"7_12"=> esc_html__("7/12", 'organic-beauty'),
				"10_12"=> esc_html__("10/12", 'organic-beauty'),
				"11_12"=> esc_html__("11/12", 'organic-beauty')
			);
			$list = apply_filters('organic_beauty_filter_list_columns', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_columns', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return list of locations for the dedicated content
if ( !function_exists( 'organic_beauty_get_list_dedicated_locations' ) ) {
	function organic_beauty_get_list_dedicated_locations($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_dedicated_locations'))=='') {
			$list = array(
				"default" => esc_html__('As in the post defined', 'organic-beauty'),
				"center"  => esc_html__('Above the text of the post', 'organic-beauty'),
				"left"    => esc_html__('To the left the text of the post', 'organic-beauty'),
				"right"   => esc_html__('To the right the text of the post', 'organic-beauty'),
				"alter"   => esc_html__('Alternates for each post', 'organic-beauty')
			);
			$list = apply_filters('organic_beauty_filter_list_dedicated_locations', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_dedicated_locations', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return post-format name
if ( !function_exists( 'organic_beauty_get_post_format_name' ) ) {
	function organic_beauty_get_post_format_name($format, $single=true) {
		$name = '';
		if ($format=='gallery')		$name = $single ? esc_html__('gallery', 'organic-beauty') : esc_html__('galleries', 'organic-beauty');
		else if ($format=='video')	$name = $single ? esc_html__('video', 'organic-beauty') : esc_html__('videos', 'organic-beauty');
		else if ($format=='audio')	$name = $single ? esc_html__('audio', 'organic-beauty') : esc_html__('audios', 'organic-beauty');
		else if ($format=='image')	$name = $single ? esc_html__('image', 'organic-beauty') : esc_html__('images', 'organic-beauty');
		else if ($format=='quote')	$name = $single ? esc_html__('quote', 'organic-beauty') : esc_html__('quotes', 'organic-beauty');
		else if ($format=='link')	$name = $single ? esc_html__('link', 'organic-beauty') : esc_html__('links', 'organic-beauty');
		else if ($format=='status')	$name = $single ? esc_html__('status', 'organic-beauty') : esc_html__('statuses', 'organic-beauty');
		else if ($format=='aside')	$name = $single ? esc_html__('aside', 'organic-beauty') : esc_html__('asides', 'organic-beauty');
		else if ($format=='chat')	$name = $single ? esc_html__('chat', 'organic-beauty') : esc_html__('chats', 'organic-beauty');
		else						$name = $single ? esc_html__('standard', 'organic-beauty') : esc_html__('standards', 'organic-beauty');
		return apply_filters('organic_beauty_filter_list_post_format_name', $name, $format);
	}
}

// Return post-format icon name (from Fontello library)
if ( !function_exists( 'organic_beauty_get_post_format_icon' ) ) {
	function organic_beauty_get_post_format_icon($format) {
		$icon = 'icon-';
		if ($format=='gallery')		$icon .= 'pictures';
		else if ($format=='video')	$icon .= 'video';
		else if ($format=='audio')	$icon .= 'note';
		else if ($format=='image')	$icon .= 'picture';
		else if ($format=='quote')	$icon .= 'quote';
		else if ($format=='link')	$icon .= 'link';
		else if ($format=='status')	$icon .= 'comment';
		else if ($format=='aside')	$icon .= 'doc-text';
		else if ($format=='chat')	$icon .= 'chat';
		else						$icon .= 'book-open';
		return apply_filters('organic_beauty_filter_list_post_format_icon', $icon, $format);
	}
}

// Return fonts styles list, prepended inherit
if ( !function_exists( 'organic_beauty_get_list_fonts_styles' ) ) {
	function organic_beauty_get_list_fonts_styles($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_fonts_styles'))=='') {
			$list = array(
				'i' => esc_html__('I','organic-beauty'),
				'u' => esc_html__('U', 'organic-beauty')
			);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_fonts_styles', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return Google fonts list
if ( !function_exists( 'organic_beauty_get_list_fonts' ) ) {
	function organic_beauty_get_list_fonts($prepend_inherit=false) {
		if (($list = organic_beauty_storage_get('list_fonts'))=='') {
			$list = array();
			$list = organic_beauty_array_merge($list, organic_beauty_get_list_font_faces());
			// Google and custom fonts list:
			//$list['Advent Pro'] = array(
			//		'family'=>'sans-serif',																						// (required) font family
			//		'link'=>'Advent+Pro:100,100italic,300,300italic,400,400italic,500,500italic,700,700italic,900,900italic',	// (optional) if you use Google font repository
			//		'css'=>organic_beauty_get_file_url('/css/font-face/Advent-Pro/stylesheet.css')									// (optional) if you use custom font-face
			//		);
			$list = organic_beauty_array_merge($list, array(
				'Advent Pro' => array('family'=>'sans-serif'),
				'Alegreya Sans' => array('family'=>'sans-serif'),
				'Arimo' => array('family'=>'sans-serif'),
				'Asap' => array('family'=>'sans-serif'),
				'Averia Sans Libre' => array('family'=>'cursive'),
				'Averia Serif Libre' => array('family'=>'cursive'),
				'Bree Serif' => array('family'=>'serif',),
				'Cabin' => array('family'=>'sans-serif'),
				'Cabin Condensed' => array('family'=>'sans-serif'),
				'Caudex' => array('family'=>'serif'),
				'Comfortaa' => array('family'=>'cursive'),
				'Cousine' => array('family'=>'sans-serif'),
				'Crimson Text' => array('family'=>'serif'),
				'Cuprum' => array('family'=>'sans-serif'),
				'Dosis' => array('family'=>'sans-serif'),
				'Economica' => array('family'=>'sans-serif'),
				'Exo' => array('family'=>'sans-serif'),
				'Expletus Sans' => array('family'=>'cursive'),
				'Karla' => array('family'=>'sans-serif'),
				'Lato' => array('family'=>'sans-serif'),
				'Lekton' => array('family'=>'sans-serif'),
				'Lobster Two' => array('family'=>'cursive'),
				'Maven Pro' => array('family'=>'sans-serif'),
				'Merriweather' => array('family'=>'serif'),
				'Montserrat' => array('family'=>'sans-serif'),
				'Neuton' => array('family'=>'serif'),
				'Noticia Text' => array('family'=>'serif'),
				'Old Standard TT' => array('family'=>'serif'),
				'Open Sans' => array('family'=>'sans-serif'),
				'Orbitron' => array('family'=>'sans-serif'),
				'Oswald' => array('family'=>'sans-serif'),
				'Overlock' => array('family'=>'cursive'),
				'Oxygen' => array('family'=>'sans-serif'),
				'Philosopher' => array('family'=>'serif'),
				'PT Serif' => array('family'=>'serif'),
				'Puritan' => array('family'=>'sans-serif'),
				'Raleway' => array('family'=>'sans-serif'),
				'Roboto' => array('family'=>'sans-serif'),
				'Roboto Slab' => array('family'=>'sans-serif'),
				'Roboto Condensed' => array('family'=>'sans-serif'),
				'Rosario' => array('family'=>'sans-serif'),
				'Share' => array('family'=>'cursive'),
				'Signika' => array('family'=>'sans-serif'),
				'Signika Negative' => array('family'=>'sans-serif'),
				'Source Sans Pro' => array('family'=>'sans-serif'),
				'Tinos' => array('family'=>'serif'),
				'Ubuntu' => array('family'=>'sans-serif'),
				'Vollkorn' => array('family'=>'serif')
				)
			);
			$list = apply_filters('organic_beauty_filter_list_fonts', $list);
			if (organic_beauty_get_theme_setting('use_list_cache')) organic_beauty_storage_set('list_fonts', $list);
		}
		return $prepend_inherit ? organic_beauty_array_merge(array('inherit' => esc_html__("Inherit", 'organic-beauty')), $list) : $list;
	}
}

// Return Custom font-face list
if ( !function_exists( 'organic_beauty_get_list_font_faces' ) ) {
	function organic_beauty_get_list_font_faces($prepend_inherit=false) {
		static $list = false;
		if (is_array($list)) return $list;
		$fonts = organic_beauty_storage_get('required_custom_fonts');
		$list = array();
		if (is_array($fonts)) {
			foreach ($fonts as $font) {
				if (($url = organic_beauty_get_file_url('css/font-face/'.trim($font).'/stylesheet.css'))!='') {
					$list[sprintf(esc_html__('%s (uploaded font)', 'organic-beauty'), $font)] = array('css' => $url);
				}
			}
		}
		return $list;
	}
}
?>