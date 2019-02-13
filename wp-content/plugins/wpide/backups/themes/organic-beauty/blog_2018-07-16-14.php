<?php /* start WPide restore code */
                                    if ($_POST["restorewpnonce"] === "882b870f837533b2c0464b557f175b5bcb0592b27e"){
                                        if ( file_put_contents ( "/var/www/html/scent.fantasylab.no/wp-content/themes/organic-beauty/blog.php" ,  preg_replace("#<\?php /\* start WPide(.*)end WPide restore code \*/ \?>#s", "", file_get_contents("/var/www/html/scent.fantasylab.no/wp-content/plugins/wpide/backups/themes/organic-beauty/blog_2018-07-16-14.php") )  ) ){
                                            echo "Your file has been restored, overwritting the recently edited file! \n\n The active editor still contains the broken or unwanted code. If you no longer need that content then close the tab and start fresh with the restored file.";
                                        }
                                    }else{
                                        echo "-1";
                                    }
                                    die();
                            /* end WPide restore code */ ?><?php
/*
Template Name: Blog streampage
*/

/**
 * Make empty page with this template 
 * and put it into menu
 * to display all posts as streampage
 */

organic_beauty_storage_set('blog_streampage', true);

get_header(); 
echo 'aaaaaaaaaaaaaaaaa';
if (empty($blog_style))	$blog_style = organic_beauty_get_custom_option('blog_style');
$blog_columns	= max(1, (int) organic_beauty_substr($blog_style, -1));
$show_sidebar 	= organic_beauty_get_custom_option('show_sidebar_main');
$show_filters 	= organic_beauty_get_custom_option('show_filters');
$ppp			= (int) organic_beauty_get_custom_option('posts_per_page');
$hover			= organic_beauty_get_custom_option('hover_style');
$hover_dir		= organic_beauty_get_custom_option('hover_dir');
if (organic_beauty_strpos($blog_style, 'colored')===false) {
	if (empty($hover)) $hover = 'square effect_shift';
	if (empty($hover_dir)) $hover_dir = 'left_to_right';
}

$page_number = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);

$args = organic_beauty_get_query_vars();
$args['post_status'] = current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish';

if ( is_page() || organic_beauty_storage_get('blog_filters')!='' ) {
	unset($args['p']);
	unset($args['page_id']);
	unset($args['pagename']);
	unset($args['name']);
	$args['posts_per_page'] = $ppp;
	if ($page_number > 1) {
		$args['paged'] = $page_number;
		$args['ignore_sticky_posts'] = true;
	}
	$args = organic_beauty_query_add_sort_order($args);
	$args = organic_beauty_query_add_filters($args, organic_beauty_storage_get('blog_filters'));
	query_posts( $args );
}

$per_page = organic_beauty_get_query_vars('posts_count');
$post_number = 0;
$parent_tax_id = (int) organic_beauty_get_custom_option('taxonomy_id');
$flt_ids = array();

$container = apply_filters('organic_beauty_filter_blog_container', organic_beauty_get_template_property($blog_style, 'container'), array('style'=>$blog_style, 'dir'=>'horizontal'));
$container_start = $container_end = '';
if (!empty($container)) {
	$container = explode('%s', $container);
	$container_start = !empty($container[0]) ? $container[0] : '';
	$container_end = !empty($container[1]) ? $container[1] : '';
}

$container2 = apply_filters('organic_beauty_filter_blog_container2', organic_beauty_get_template_property($blog_style, 'container2'), array('style'=>$blog_style, 'dir'=>'horizontal'));
$container2_start = $container2_end = '';
if (!empty($container2)) {
	$container2 = explode('%s', $container2);
	$container2_start = !empty($container2[0]) ? $container2[0] : '';
	$container2_end = !empty($container2[1]) ? $container2[1] : '';
}

organic_beauty_show_layout('', $container_start, $container2_start);

if (organic_beauty_get_template_property($blog_style, 'need_columns') && $blog_columns > 1) {
	?>
	<div class="columns_wrap <?php echo esc_attr(organic_beauty_get_template_property($blog_style, 'container_classes')); ?>">
	<?php
}

if (organic_beauty_get_template_property($blog_style, 'need_isotope')) {
	if (!organic_beauty_param_is_off($show_filters)) {
		?>
		<div class="isotope_filters"></div>
		<?php
	}
	?>
	<div class="isotope_wrap <?php echo esc_attr(organic_beauty_get_template_property($blog_style, 'container_classes')); ?>" data-columns="<?php echo esc_attr($blog_columns); ?>">
	<?php
}

while ( have_posts() ) { the_post(); 
	$post_number++;
	$post_args = array(
		'layout' => $blog_style,
		'number' => $post_number,
		'add_view_more' => false,
		'posts_on_page' => $per_page,
		'columns_count' => $blog_columns,
		// Get post data
		'strip_teaser' => false,
		'content' => organic_beauty_get_template_property($blog_style, 'need_content'),
		'terms_list' => !organic_beauty_param_is_off($show_filters) || organic_beauty_get_template_property($blog_style, 'need_terms'),
		'parent_tax_id' => $parent_tax_id,
		'descr' => organic_beauty_get_custom_option('post_excerpt_maxlength'.($blog_columns > 1 ? '_masonry' : '')),
		'sidebar' => !organic_beauty_param_is_off($show_sidebar),
		'filters' => $show_filters != 'hide' ? $show_filters : '',
		'hover' => $hover,
		'hover_dir' => $hover_dir
	);

	$post_data = organic_beauty_get_post_data($post_args);

	organic_beauty_show_post_layout($post_args, $post_data);

	if ($show_filters=='tags') {					// Use tags as filter items
		if (!empty($post_data['post_terms'][$post_data['post_taxonomy_tags']]->terms)) {
			foreach ($post_data['post_terms'][$post_data['post_taxonomy_tags']]->terms as $tag) {
				$flt_ids[$tag->term_id] = $tag->name;
			}
		}
	}
}

if (organic_beauty_get_template_property($blog_style, 'need_isotope')) {
	?>
	</div> <!-- /.isotope_wrap -->
	<?php 
}

if (organic_beauty_get_template_property($blog_style, 'need_columns') && $blog_columns > 1) {
	?>
	</div> <!-- /.columns_wrap -->
	<?php
}

organic_beauty_show_layout($container2_end . trim($container_end));

if (!$post_number) { 
	if ( is_search() ) {
		organic_beauty_show_post_layout( array('layout' => 'no-search'), false );
	} else {
		organic_beauty_show_post_layout( array('layout' => 'no-articles'), false );
	}
} else {
	// Isotope filters list
	$filters = '';
	$filter_button_classes = 'isotope_filters_button';
	if ($show_filters == 'categories') {			// Use categories as filter items
		$taxonomy = organic_beauty_is_taxonomy();
		$cur_term = $taxonomy ? organic_beauty_get_current_term($taxonomy) : 0;
		$cur_term_id = $cur_term ? $cur_term->term_id : 0;
		$portfolio_parent = $cur_term_id ? max(0, organic_beauty_get_parent_taxonomy_by_property($cur_term_id, 'show_filters', 'yes', true, $taxonomy)) : 0;
		$args2 = array(
			'type'			=> !empty($args['post_type']) ? $args['post_type'] : 'post',
			'child_of'		=> $portfolio_parent,
			'orderby'		=> 'name',
			'order'			=> 'ASC',
			'hide_empty'	=> 1,
			'hierarchical'	=> 0,
			'exclude'		=> '',
			'include'		=> '',
			'number'		=> '',
			'taxonomy'		=> $taxonomy,
			'pad_counts'	=> false
		);
		$portfolio_list = get_categories($args2);
		if (is_array($portfolio_list) && count($portfolio_list) > 0) {
			$filters .= '<a href="#" data-filter="*" class="'.esc_attr($filter_button_classes . ($portfolio_parent==$cur_term_id ? ' active' : '')) . '">' . esc_html__('All', 'organic-beauty').'</a>';
			foreach ($portfolio_list as $cat) {
				if (isset($cat->term_id)) $filters .= '<a href="#" data-filter=".flt_'.esc_attr($cat->term_id).'" class="'.esc_attr($filter_button_classes . ($cat->term_id==$cur_term_id ? ' active' : '')).'">'.($cat->name).'</a>';
			}
		}
	} else if ($show_filters == 'tags') {																	// Use tags as filter items
		if (is_array($flt_ids) && count($flt_ids) > 0) {
			$filters .= '<a href="#" data-filter="*" class="'.esc_attr($filter_button_classes).' active">'.esc_html__('All', 'organic-beauty').'</a>';
			foreach ($flt_ids as $flt_id=>$flt_name) {
				$filters .= '<a href="#" data-filter=".flt_'.esc_attr($flt_id).'" class="'.esc_attr($filter_button_classes).'">'.($flt_name).'</a>';
			}
		}
	}
	if ($filters) {
		organic_beauty_storage_set_array('js_vars', 'ppp', $ppp);
		organic_beauty_storage_concat('js_code', '
			jQuery(".isotope_filters").append("'.addslashes($filters).'");
		');
	}
}

if ($post_number > 0) {
	// Pagination
	$pagination = organic_beauty_get_custom_option('blog_pagination');
	if (in_array($pagination, array('viewmore', 'infinite'))) {
		if ($page_number < organic_beauty_get_query_vars('max_num_pages')) {
			organic_beauty_storage_set_array('js_vars', 'viewmore_page', $page_number);
			organic_beauty_storage_set_array('js_vars', 'viewmore_data', $args);
			organic_beauty_storage_set_array('js_vars', 'viewmore_vars', array(
							'blog_style' => $blog_style,
							'columns_count' => $blog_columns,
							'parent_tax_id' => $parent_tax_id,
							'show_sidebar' => $show_sidebar,
							'filters' => $show_filters!='hide' ? $show_filters : '',
							'hover' => $hover,
							'hover_dir' => $hover_dir,
							'ppp' => $ppp
						));
			?>
			<div id="viewmore" class="pagination_wrap pagination_<?php echo esc_attr($pagination); ?>">
				<a href="#" id="viewmore_link" class="theme_button viewmore_button"><span class="icon-spin3 animate-spin viewmore_loading"></span><span class="viewmore_text_1"><?php esc_html_e('LOAD MORE', 'organic-beauty'); ?></span><span class="viewmore_text_2"><?php esc_html_e('Loading ...', 'organic-beauty'); ?></span></a>
				<span class="viewmore_loader"></span>
			</div>
			<?php
		}
	} else {
		organic_beauty_show_pagination(array(
			'class' => 'pagination_wrap pagination_'.esc_attr($pagination),
			'style' => $pagination,
			'button_class' => '',
			'first_text'=> '',
			'last_text' => '',
			'prev_text' => '',
			'next_text' => '',
			'pages_in_group' => $pagination=='pages' ? 10 : 20
			)
		);
	}
}

// Add template specific scripts and styles
do_action('organic_beauty_action_blog_scripts', $blog_style);

get_footer();
?>