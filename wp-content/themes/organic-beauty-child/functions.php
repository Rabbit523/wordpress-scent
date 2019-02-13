<?php
/**
 * Child-Theme functions and definitions
 */

add_theme_support('woocommerce');

function gi_remove_image_zoom_support() {
    remove_theme_support( 'wc-product-gallery-zoom' );
}
add_action( 'after_setup_theme', 'gi_remove_image_zoom_support', 100 );


function henry_megamenu_javascript_localisation( $args ) {
    $args['timeout'] = 0;
    $args['interval'] = 0;
    return $args;
}
add_filter( 'megamenu_javascript_localisation', 'henry_megamenu_javascript_localisation', 10 );

add_shortcode( 'henry_product_categories', 'henry_product_categories' );
function henry_product_categories( $atts, $content="") {
	extract( shortcode_atts( array(
		'catid' => '',
	), $atts ) );
	$html = '';
	if( $catid ) {
		$category = get_term($catid, 'product_cat');
		if( $category ) {
			$html = '<h4 class="mega-block-title">'. $category->name .'</h4>';
			$child = get_terms( array( 
			    'taxonomy' => 'product_cat',
			    'parent'   => $catid
			) );
			if( $child ) { 
				$html .= '<ul class="product-categories">';
				foreach( $child as $c ) {
				$html .= '<li class="cat-item"><a href="'. get_term_link($c,'product_cat') .'">'. $c->name .'</a></li>';
				}
				$html .= '</ul>';
			}
		}
	}

	return $html;
}

add_filter( 'loop_shop_columns', 'giang_loop_shop_columns', 10);
function giang_loop_shop_columns() {
	return 3;
}

add_filter( 'woocommerce_product_tabs', 'giang_woo_remove_product_tabs', 100 );
function giang_woo_remove_product_tabs( $tabs ) {
	global $post;
	if( get_field('ingredients') ) {
		$tabs['ingredients']['title'] = __('Ingredients','giang');
		$tabs['ingredients']['callback'] = 'giang_woo_ingredients_product_tab_content';
	}
    return $tabs;
}

function giang_woo_ingredients_product_tab_content() {
	the_field('ingredients');
}

	
function giang_child_scripts() {
	wp_enqueue_script( 'custom', get_stylesheet_directory_uri() . '/js/custom.js', array('jquery'), '', true );
}
add_action( 'wp_enqueue_scripts', 'giang_child_scripts' );

function gi_cmp($a, $b) {
    return absint($a->count) < absint($b->count);
}

add_shortcode('product-cat-menu', 'giang_product_cat_menu_shortcode');
function giang_product_cat_menu_shortcode( $atts, $content="" ) {
	extract( shortcode_atts( array(
		'slug' => '',
	), $atts ) );
	ob_start(); ?>
	<?php if( $slug ): ?>
	<?php $term = get_term_by('slug', $slug, 'product_cat'); ?>
	<h4 class="mega-block-title"><a href="<?php echo get_term_link( $term ); ?>"><?php echo $term->name; ?></a></h4>
	<?php 
		$child = get_terms( 'product_cat', array(
		    'orderby'  => 'id',
		    'order '	=> 'ASC',
		    'hide_empty' => 0,
		    'parent'   => $term->term_id
		));
		usort($child, "gi_cmp");
		if( $child ) : $index = 0;
	?>
	<ul id="menu-<?php echo $term->slug; ?>" class="menu">
		<?php foreach( $child as $t ) : $index++; ?>
		
		<?php if( $index <= 5 ) : ?>
		<li><a href="<?php echo get_term_link( $t ); ?>"><?php echo $t->name; ?></a></li>
		<?php endif; ?>

		
		<?php if( sizeof($child) > 5 && $index == 6 ) : ?>
		<li><a href="<?php echo get_term_link( $term ); ?>"><?php _e('Vis alle','giang'); ?></a></li>
		<?php endif; ?>

		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
	<?php endif; ?>

	<?php
	$html = ob_get_contents();
	ob_end_clean();
	return $html;
}

add_shortcode('product-brand-menu', 'giang_product_brand_menu_shortcode');
function giang_product_brand_menu_shortcode( $atts, $content="" ) {
	ob_start(); ?>
	<div class="product-brand">
		<?php 
			$brands = get_terms( 'pwb-brand', array(
			    'orderby'  => 'name',
			    'order '	=> 'ASC',
			    'hide_empty' => 0
			));
			// usort($brands, "gi_cmp");
			if( $brands ) : $index = 0;
			echo '<ul class="brands-menu">';
				foreach( $brands as $t ) : $index++;?>
			<li class="brand-item"><a href="<?php echo get_term_link($t); ?>"><?php echo $t->name; ?></a></li>
			<?php
				endforeach;
			echo '</ul>';
			endif;
		?>
	</div>
	<?php
	$html = ob_get_contents();
	ob_end_clean();
	return $html;
}

add_shortcode('vegan-menu', 'giang_vegan_menu_shortcode');
function giang_vegan_menu_shortcode( $atts, $content="" ) {
	ob_start(); 
	// delete_transient('giang_vegan_menu_shortcode');
	if ( false === ( $terms = get_transient( 'giang_vegan_menu_shortcode' ) ) ) {
		$products = get_posts(array(
			'post_type' => 'product',
			'posts_per_page'	=> -1,
			'tax_query'	=> array(
				array(
					'taxonomy' => 'product_cat',
					'field' => 'slug',
					'terms' => 'vegan'
				)
			)
		));
		$terms = array();
		$check = array();
		foreach( $products as $p ) {
			$categories = wp_get_post_terms( $p->ID, 'product_cat', array("fields" => "all"));
			foreach( $categories as $cat ) {
				if( !in_array( $cat->term_id, $check ) && $cat->slug != 'vegan' ) {
					array_push( $terms, $cat );
					array_push( $check, $cat->term_id );
				}
			}
		}
		set_transient( 'giang_vegan_menu_shortcode', $terms, 24 * HOUR_IN_SECONDS );

	}
	if( is_array( $terms ) ) :
?>
	<div class="product-brand">
		<ul class="brands-menu">
		<?php foreach( $terms as $term ) : if( $term->parent == 0 ) : ?>
			<li class="brand-item"><a href="<?php echo get_term_link($term); ?>"><?php echo $term->name; ?></a></li>
		<?php endif; endforeach; ?>
		</ul>
	</div>
	<?php
	endif;
	$html = ob_get_contents();
	ob_end_clean();
	return $html;
}

add_filter( 'single_product_archive_thumbnail_size', 'bi_single_product_archive_thumbnail_size', 10, 1 );
function bi_single_product_archive_thumbnail_size( $size ) {
	return 'full';
}

add_action('init', 'henry_init_functions', 12);
function henry_init_functions() {
	if( isset($_GET['giang']) ) {
		$products = get_posts(array(
			'post_type' => 'product',
			'posts_per_page' => -1
		));
		foreach( $products as $product ) {
			$meta = '';
			$brand = wp_get_post_terms($product->ID, 'pwb-brand');
			foreach( $brand as $b ) {
				$meta .= $b->name .' ';
			}
			$tags = wp_get_post_terms($product->ID, 'product_tag');
			foreach( $tags as $b ) {
				$meta .= $b->name .' ';
			}
			$sku = get_post_meta($product->ID, '_sku', true);
			if( $sku ) {
				$meta .= ' '. $sku;
			}
			global $wpdb;
			$wpdb->query( $wpdb->prepare(
				"UPDATE $wpdb->posts SET `post_content_filtered` = '%s' WHERE `ID` = %d", trim($meta), $product->ID
			) );
		}
	}
}

function gi_product_save_meta(){
	global $post;
	if( $post && $post->post_type == 'product' ){
		$meta = '';
		$brand = wp_get_post_terms($post->ID, 'pwb-brand');
		foreach( $brand as $b ) {
			$meta .= $b->name .' ';
		}
		$tags = wp_get_post_terms($post->ID, 'product_tag');
		foreach( $tags as $b ) {
			$meta .= $b->name .' ';
		}
		$sku = get_post_meta($post->ID, '_sku', true);
		if( $sku ) {
			$meta .= ' '. $sku;
		}
		global $wpdb;
		$wpdb->query( $wpdb->prepare(
			"UPDATE $wpdb->posts SET `post_content_filtered` = '%s' WHERE `ID` = %d", trim($meta), $post->ID
		) );
	}
}

add_action('save_post', 'gi_product_save_meta');

add_filter('posts_search', 'bi_custom_posts_search', 10, 2);
function bi_custom_posts_search( $search, &$query ) {
	if( ( ( isset($query->query['s']) && !empty($query->query['s']) ) || ( isset($_GET['s']) && !empty($_GET['s']) ) ) && isset($query->query['post_type']) && $query->query['post_type'] == 'product' ) {
		global $wpdb;
		$like_op = 'LIKE';
		$searchand = ' AND ';
		$andor_op = 'OR';
		$key = $query->query['s'] ? $query->query['s'] : $_GET['s'];
		$like = '%' . $wpdb->esc_like( $key ) . '%';
		$search = $wpdb->prepare( "{$searchand}(({$wpdb->posts}.post_title $like_op %s) $andor_op ({$wpdb->posts}.post_excerpt $like_op %s) $andor_op ({$wpdb->posts}.post_content $like_op %s) $andor_op ({$wpdb->posts}.post_content_filtered $like_op %s))", $like, $like, $like, $like );
	}
	return $search;
}

add_filter('posts_where_request', 'bi_custom_posts_where_request', 100, 2);
function bi_custom_posts_where_request( $where, &$query ) {
	if( ( ( isset($query->query['s']) && !empty($query->query['s']) ) || ( isset($_GET['s']) && !empty($_GET['s']) ) ) && isset($query->query['post_type']) && $query->query['post_type'] == 'product' ) {
		$key = $query->query['s'] ? $query->query['s'] : $_GET['s'];
		$key = strtolower($key);
		// $index = strrpos($where, "AND (  ( LOWER(post_title) REGEXP '$key' AND LOWER(post_content) REGEXP '$key')");
		// $where = substr( $where, 0, $index);
		$where = str_replace( "AND (  ( LOWER(post_title) REGEXP '$key' AND LOWER(post_content) REGEXP '$key')  )", "", $where);
		$where = str_replace( "AND (  ( LOWER(post_title) REGEXP '$key' AND LOWER(post_content) REGEXP '$key') )", "", $where);
		$where = str_replace( "AND ( ( LOWER(post_title) REGEXP '$key' AND LOWER(post_content) REGEXP '$key') )", "", $where);
	}
	
	return $where;
}

add_filter( 'loop_shop_per_page', 'giang_loop_shop_per_page', 20 );
function giang_loop_shop_per_page( $cols ) {
	$cols = 12; return $cols;
}

add_action( 'woocommerce_product_query', 'woocommerce_product_query' );
function woocommerce_product_query( $q ) {
    if ( $q->is_main_query() && ( $q->get( 'wc_query' ) === 'product_query' ) ) {
        $q->set( 'posts_per_page', '12' );
    }
}

add_filter( 'woocommerce_product_tabs', 'giang_woo_rename_tabs', 98 );
function giang_woo_rename_tabs( $tabs ) {

	$tabs['description']['title'] = 'Description';

	return $tabs;

}


function themename_woocommerce_infinite_scroll_support( $supported ) {
	return ( is_shop() || is_product_taxonomy() ) ? false : $supported;
}
add_filter( 'infinite_scroll_archive_supported', 'themename_woocommerce_infinite_scroll_support' );

// if( isset($_GET['giang']) ) {
// 	$query_images_args = array(
// 	    'post_type'      => 'attachment',
// 	    'post_mime_type' => 'image',
// 	    'post_status'    => 'inherit',
// 	    'posts_per_page' => - 1,
// 	);

// 	$query_images = new WP_Query( $query_images_args );

// 	$images = array();
// 	foreach ( $query_images->posts as $image ) {
// 	    $sku = $image->post_name;
// 	    $product = wc_get_product_id_by_sku( $sku );var_dump($product);

// 	    if( $product && ! has_post_thumbnail($product) ) {
// 	    	set_post_thumbnail( $product, $image->ID );
// 	    }
// 	}
	
// 	exit;
// }