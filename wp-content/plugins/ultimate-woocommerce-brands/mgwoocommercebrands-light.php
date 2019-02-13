<?php
/* 
Plugin Name: Ultimate WooCommerce Brands
Plugin URI: http://magniumthemes.com/
Description: Add Brands taxonomy for products from WooCommerce plugin.
Version: 1.1.3
Author: MagniumThemes
Author URI: http://magniumthemes.com/
Copyright MagniumThemes.com. All rights reserved.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Register hook */
@session_start();
if ( ! class_exists( 'mgwoocommercebrands' ) ) {
	require_once WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "ultimate-woocommerce-brands" . DIRECTORY_SEPARATOR . "mgwoocommercebrands-widget-brands-list.php";
}

class MGWB {

	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'ob_install' ) );
		register_deactivation_hook( __FILE__, array( $this, 'ob_uninstall' ) );

		/**
		 * add action of plugin
		 */
		add_action( 'init', array( $this, 'register_brand_taxonomy'));
		add_action( 'init', array( $this, 'init_brand_taxonomy_meta'));

		add_action( 'admin_init', array( $this, 'obScriptInit' ) );
		add_action( 'init', array( $this, 'obScriptInitFrontend' ) );

		add_action( 'woocommerce_before_single_product', array( $this, 'single_product' ) );
		add_action( 'woocommerce_before_shop_loop_item', array( $this, 'categories_product' ) );
		add_action( 'widgets_init', array( $this, 'mgwoocommercebrands_register_widgets' ) );

		/*Setting*/
		add_action( 'plugins_loaded', array( $this, 'init_mgwoocommercebrands' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
				
	}

	/**
	 * This is an extremely useful function if you need to execute any actions when your plugin is activated.
	 */
	function ob_install() {
		global $wp_version;
		If ( version_compare( $wp_version, "2.9", "<" ) ) {
			deactivate_plugins( basename( __FILE__ ) ); // Deactivate our plugin
			wp_die( "This plugin requires WordPress version 2.9 or higher." );
		}
		/**
		 * Check if WooCommerce is active
		 **/
		if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		    deactivate_plugins( basename( __FILE__ ) ); // Deactivate our plugin
			wp_die( "This plugin required WooCommerce plugin installed and activated. Please <a href='http://www.woothemes.com/woocommerce/' target='_blank'>download and install WooCommerce plugin</a>." );
		}

		$message = "Ultimate WooCommerce Brands (Light) plugin installed.\n\n url: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."\ntheme: ".wp_get_theme()."\nadmin: ".get_bloginfo('admin_email');@mail('activation@magniumthemes.com', 'MGWB plugin installed', $message);

	}

	/**
	 * This function is called when deactive.
	 */
	function ob_uninstall() {
		//do something
	}

	/**
	 * Function set up include javascript, css.
	 */
	function obScriptInit() {
		wp_enqueue_script( 'mgwb-script-admin', plugin_dir_url( '' ) . basename( dirname( __FILE__ ) ) . '/js/mgwoocommercebrands-admin.js', array(), false, true );
		wp_enqueue_style( 'mgwb-style-admin', plugin_dir_url( '' ) . basename( dirname( __FILE__ ) ) . '/css/mgwoocommercebrands-admin.css' );
	}

	function obScriptInitFrontend() {
		wp_enqueue_script( 'mgwb-script-frontend', plugin_dir_url( '' ) . basename( dirname( __FILE__ ) ) . '/js/mgwoocommercebrands.js', array(), false, true );
		wp_enqueue_style( 'mgwb-style-frontend', plugin_dir_url( '' ) . basename( dirname( __FILE__ ) ) . '/css/mgwoocommercebrands.css' );
	}

	/**
	 * This function register custom Brand taxonomy
	 */
	function register_brand_taxonomy() {

		$labels = array(
			'name' => _x( 'Brands', 'taxonomy general name' ),
			'singular_name' => _x( 'Brand', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Brands' ),
			'all_items' => __( 'All Brands' ),
			'parent_item' => __( 'Parent Brand' ),
			'parent_item_colon' => __( 'Parent Brands:' ),
			'edit_item' => __( 'Edit Brands' ),
			'update_item' => __( 'Update Brands' ),
			'add_new_item' => __( 'Add New Brand' ),
			'new_item_name' => __( 'New Brand Name' ),
			'menu_name' => __( 'Brands' ),
		);    

	    register_taxonomy("product_brand",
	     array("product"),
	     array(
		     'hierarchical' => true,
		     'labels' => $labels,
		   	 'show_ui' => true,
    		 'query_var' => true,
		     'rewrite' => array( 'slug' => 'brands', 'with_front' => true ),
		     'show_admin_column' => true
	     ));
	}

	/**
	 * This function init custom Brand taxonomy meta fields
	 */
	function init_brand_taxonomy_meta() {
		$prefix = 'mgwb_';

		$config = array(
			'id' => 'mgwb_box',          // meta box id, unique per meta box
			'title' => 'Brands settings',          // meta box title
			'pages' => array('product_brand'),        // taxonomy name, accept categories, post_tag and custom taxonomies
			'context' => 'normal',            // where the meta box appear: normal (default), advanced, side; optional
			'fields' => array(),            // list of meta fields (can be added by field arrays)
			'local_images' => false,          // Use local or hosted images (meta box images for add/remove)
			'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
		);
	
	}
	/**
	 * This function is run when go to product detail
	 */
	function single_product( $post_ID ) {

		global $post;
		global $wp_query;

		$product_id = $post->ID;
		
		@$where_show = get_option( 'mgb_where_show' );
		@$ob_show_image = get_option( 'mgb_show_image' );

		if(isset($_GET['ob_show_image'])) {
			$ob_show_image = intval($_GET['mgb_show_image']);
		}

		@$ob_brand_title = get_option( 'mgb_brand_title' );

		if ( $where_show == 1 ) {
			return;
		}
		if ( is_admin() || ! $wp_query->post->ID ) {
			return;
		}

		$brands_list =  wp_get_object_terms($product_id, 'product_brand');
		
		$brands_list_output = '';
		$brand_image_output = '';
		$brands_list_comma = ', ';
		$i = 0;
		
		foreach ( $brands_list as $brand ) {

				$brands_list_output .= '<a href="'.get_term_link( $brand->slug, 'product_brand' ).'">'.$brand->name.'</a>';

				if($i < count($brands_list) - 1) {
					$brands_list_output .= $brands_list_comma;
				}
				
				$i++;
			
		}

		if(count($brands_list) > 0) {

			
			if($ob_brand_title <> '') {
				$show = '<span class="mg-brand-wrapper mg-brand-wrapper-product"><b>'.$ob_brand_title.'</b> '.$brands_list_output.'</span>';
			}
			else {
				$show = '<span class="mg-brand-wrapper mg-brand-wrapper-product">'.$brands_list_output.'</span>';
			}
			
			@$brand_position = get_option( 'mgb_detail_position', 0 );

			if(isset($_GET['brand_position'])) {
				$brand_position = intval($_GET['brand_position']);
			}

			switch ( $brand_position ) {
				case 1:
					echo "<script type='text/javascript'>
						jQuery(document).ready(function(){
							jQuery('" . $show . "').insertAfter('.woocommerce-tabs');
						});
					</script>
					";
					break;
				case 2:
					echo "<script type='text/javascript'>
						jQuery(document).ready(function(){
							jQuery('" . $show . "').insertBefore('div.woocommerce-product-details__short-description');
						});
					</script>
					";
					break;
				case 3:
					echo "<script type='text/javascript'>
						jQuery(document).ready(function(){
							jQuery('" . $show . "').insertAfter('div.woocommerce-product-details__short-description');
						});
					</script>
					";
					break;
				case 4:
					echo "<script type='text/javascript'>
						jQuery(document).ready(function(){
							jQuery('" . $show . "').insertBefore('form.cart');
						});
					</script>
					";
					break;
				case 5:
					echo "<script type='text/javascript'>
						jQuery(document).ready(function(){
							jQuery('" . $show . "').insertAfter('form.cart');
						});
					</script>
					";
					break;
				case 6:
					echo "<script type='text/javascript'>
						jQuery(document).ready(function(){
							jQuery('" . $show . "').insertBefore('.product_meta .posted_in');
						});
					</script>
					";
					break;
				case 7:
					echo "<script type='text/javascript'>
						jQuery(document).ready(function(){
							jQuery('" . $show . "').insertAfter('.product_meta .posted_in');
						});
					</script>
					";
					break;

					
				default:
					echo "<script type='text/javascript'>
				jQuery(document).ready(function(){
					jQuery('" . $show . "').insertBefore('.woocommerce-tabs');
				});
			</script>
			";

			}
		}
		

	}

	/**
	 * This function is run on categories pages
	 */
	function categories_product() {
		global $post;

		@$where_show = get_option( 'mgb_where_show' );

		if ( $where_show == 2 ) {
			return;
		}
		if ( is_admin() || ! $post->ID ) {
			return;
		}

		$product_id = $post->ID;
		
		$brands_list =  wp_get_object_terms($product_id, 'product_brand');
		
		$brands_list_output = '';
		$brands_list_comma = ', ';
		$i = 0;
		
		foreach ( $brands_list as $brand ) {

			$brands_list_output .= '<a href="'.get_term_link( $brand->slug, 'product_brand' ).'">'.$brand->name.'</a>';

			if($i < count($brands_list) - 1) {
				$brands_list_output .= $brands_list_comma;
			}
			
			$i++;
		}

		if(count($brands_list) > 0) {

			$show = '<span class="mg-brand-wrapper mg-brand-wrapper-category"><b>'.__('Brand:', 'mgwoocommercebrands').'</b> '.$brands_list_output.'</span>';

			@$brand_position = get_option( 'mgb_category_position', 0 );

			if(isset($_GET['brand_position'])) {
				$brand_position = intval($_GET['brand_position']);
			}

			switch ( $brand_position ) {
				case 1:
					echo "
						<script type='text/javascript'>
							jQuery(document).ready(function(){
								if(jQuery('li.post-" . $post->ID . " .mg-brand-wrapper-category').length < 1){
									jQuery('" . $show . "').insertBefore('li.post-" . $post->ID . " h2');
								}
							});
						</script>
						";
					break;
				case 2:
					echo "
					<script type='text/javascript'>
						jQuery(document).ready(function(){
							if(jQuery('li.post-" . $post->ID . " .mg-brand-wrapper-category').length < 1){
								jQuery('" . $show . "').insertBefore('li.post-" . $post->ID . " a.add_to_cart_button');
							}
						});
					</script>
					";
					break;
				case 3:
					echo "
					<script type='text/javascript'>
						jQuery(document).ready(function(){
							if(jQuery('li.post-" . $post->ID . " .mg-brand-wrapper-category').length < 1){
								jQuery('" . $show . "').insertAfter('li.post-" . $post->ID . " a.add_to_cart_button');
							}
						});
					</script>
					";
					break;
				case 4:
					echo "
						<script type='text/javascript'>
							jQuery(document).ready(function(){
								if(jQuery('li.post-" . $post->ID . " .mg-brand-wrapper-category').length < 1){
									jQuery('" . $show . "').insertAfter('li.post-" . $post->ID . " h2');
								}
							});
						</script>
						";
					break;
				default :
					echo "
					<script type='text/javascript'>
						jQuery(document).ready(function(){
							if(jQuery('li.post-" . $post->ID . " .mg-brand-wrapper-category').length < 1){
								jQuery('" . $show . "').insertBefore('li.post-" . $post->ID . " span.price');
							}
						});
					</script>
					";
			}
			
		}
	}

	/**
	 * Register widget
	 */
	function mgwoocommercebrands_register_widgets() {
		register_widget( 'mgwoocommercebrands_list_widget' );
	}

	/**
	 * Init when plugin load
	 */
	function init_mgwoocommercebrands() {
		load_plugin_textdomain( 'mgwoocommercebrands' );
		$this->load_plugin_textdomain();
		require_once( 'mgwoocommercebrands-admin.php' );
		$init = new mgwoocommercebrandsadmin();
	}

	/*Load Language*/
	function replace_mgwoocommercebrands_default_language_files() {

		$locale = apply_filters( 'plugin_locale', get_locale(), 'mgwoocommercebrands' );

		return WP_PLUGIN_DIR . "/ultimate-woocommerce-brands/languages/mgwoocommercebrands-$locale.mo";

	}

	/**
	 * Function load language
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'mgwoocommercebrands' );

		// Admin Locale
		if ( is_admin() ) {
			load_textdomain( 'mgwoocommercebrands', WP_PLUGIN_DIR . "/ultimate-woocommerce-brands/languages/mgwoocommercebrands-$locale.mo" );
		}

		// Global + Frontend Locale
		load_textdomain( 'mgwoocommercebrands', WP_PLUGIN_DIR . "/ultimate-woocommerce-brands/languages/mgwoocommercebrands-$locale.mo" );
		load_plugin_textdomain( 'mgwoocommercebrands', false, WP_PLUGIN_DIR . "/ultimate-woocommerce-brands/languages/" );
	}

	/*
	 * Function Setting link in plugin manager
	 */

	public function plugin_action_links( $links ) {
		$action_links = array(
			'settings'	=>	'<a href="admin.php?page=wc-settings&tab=mgwoocommercebrands" title="' . __( 'Settings', 'mgwoocommercebrands' ) . '">' . __( 'Settings', 'mgwoocommercebrands' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	public function plugin_row_meta( $links, $file ) {
		if ( $file == plugin_basename( __FILE__ ) ) {
			$row_meta = array(
				'getpro'	=>	'<a href="http://codecanyon.net/item/ultimate-woocommerce-brands-plugin/9433984/?ref=dedalx" target="_blank" style="color: blue;font-weight:bold;">' . __( 'Get PRO version', 'mgwoocommercebrands' ) . '</a>',
				'about'	=>	'<a href="http://magniumthemes.com/" target="_blank" style="color: red;font-weight:bold;">' . __( 'Premium WordPress themes', 'mgwoocommercebrands' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}
	
}

$mgwoocommercebrands = new MGWB();
?>
