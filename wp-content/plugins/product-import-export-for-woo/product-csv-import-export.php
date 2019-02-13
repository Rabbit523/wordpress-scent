<?php
/*
Plugin Name: Product CSV Import Export (BASIC)
Plugin URI: https://www.xadapter.com/product/product-import-export-plugin-for-woocommerce/
Description: Import and Export Products From and To your WooCommerce Store.
Author: XAdapter
Author URI: https://www.xadapter.com/
Version: 1.4.1
WC tested up to: 3.2.5
Text Domain: wf_csv_import_export
*/

if ( ! defined( 'ABSPATH' ) || ! is_admin() ) {
	return;
}

if( !defined('WF_PROD_IMP_EXP_ID') ){
	define( "WF_PROD_IMP_EXP_ID", "wf_prod_imp_exp" );
}
if( !defined('WF_WOOCOMMERCE_CSV_IM_EX') ){
	define( "WF_WOOCOMMERCE_CSV_IM_EX", "wf_woocommerce_csv_im_ex" );
}
/**
 * Check if WooCommerce is active
 */
if (in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {	

	if ( ! class_exists( 'WF_Product_Import_Export_CSV' ) ) :

	/**
	 * Main CSV Import class
	 */
	class WF_Product_Import_Export_CSV {

		/**
		 * Constructor
		 */
		public function __construct() {
                        if( !defined('WF_ProdImpExpCsv_FILE') ){
                                     define( 'WF_ProdImpExpCsv_FILE', __FILE__ );
                        }

			add_filter( 'woocommerce_screen_ids', array( $this, 'woocommerce_screen_ids' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'wf_plugin_action_links' ) );
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'init', array( $this, 'catch_export_request' ), 20 );
			add_action( 'admin_init', array( $this, 'register_importers' ) );

			include_once( 'includes/class-wf-prodimpexpcsv-system-status-tools.php' );
			include_once( 'includes/class-wf-prodimpexpcsv-admin-screen.php' );
			include_once( 'includes/importer/class-wf-prodimpexpcsv-importer.php' );

			if ( defined('DOING_AJAX') ) {
				include_once( 'includes/class-wf-prodimpexpcsv-ajax-handler.php' );
			}
		}
		
		public function wf_plugin_action_links( $links ) {
			$plugin_links = array(
				'<a href="' . admin_url( 'admin.php?page=wf_woocommerce_csv_im_ex' ) . '">' . __( 'Import Export', 'wf_csv_import_export' ) . '</a>',
				'<a href="https://www.xadapter.com/product/product-import-export-plugin-for-woocommerce/" target="_blank">' . __( 'Premium Upgrade', 'wf_csv_import_export' ) . '</a>',
                                '<a href="https://www.xadapter.com/support/forum/product-import-export-plugin-for-woocommerce/">' . __( 'Support', 'wf_csv_import_export' ) . '</a>',
                                '<a href="https://wordpress.org/support/plugin/product-import-export-for-woo/reviews/">' . __( 'Review', 'wf_csv_import_export' ) . '</a>',
			);
			return array_merge( $plugin_links, $links );
		}

		/**
		 * Add screen ID
		 */
		public function woocommerce_screen_ids( $ids ) {
			$ids[] = 'admin'; // For import screen
			return $ids;
		}

		/**
		 * Handle localisation
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'wf_csv_import_export', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		}

		/**
		 * Catches an export request and exports the data. This class is only loaded in admin.
		 */
		public function catch_export_request() {
			if ( ! empty( $_GET['action'] ) && ! empty( $_GET['page'] ) && $_GET['page'] == 'wf_woocommerce_csv_im_ex' ) {
				switch ( $_GET['action'] ) {
					case "export" :
                                            $user_ok = $this->hf_user_permission();
                                            if ($user_ok) {
						include_once( 'includes/exporter/class-wf-prodimpexpcsv-exporter.php' );
						WF_ProdImpExpCsv_Exporter::do_export( 'product' );
                                            } else {
                                                wp_redirect(wp_login_url());
                                            }    
					break;
				}
			}
		}
		
		public function catch_save_settings() {
			if ( ! empty( $_GET['action'] ) && ! empty( $_GET['page'] ) && $_GET['page'] == 'wf_woocommerce_csv_im_ex' ) {
				switch ( $_GET['action'] ) {
					case "settings" :
						include_once( 'includes/settings/class-wf-prodimpexpcsv-settings.php' );
						WF_ProdImpExpCsv_Settings::save_settings( );
					break;
				}
			}
		}

		/**
		 * Register importers for use
		 */
		public function register_importers() {
			register_importer( 'woocommerce_csv', 'WooCommerce Products (CSV)', __('Import <strong>products</strong> to your store via a csv file.', 'wf_csv_import_export'), 'WF_ProdImpExpCsv_Importer::product_importer' );
		}
                private function hf_user_permission() {
                // Check if user has rights to export
                $current_user = wp_get_current_user();
                $user_ok = false;
                $wf_roles = apply_filters('hf_user_permission_roles', array('administrator', 'shop_manager'));
                if ($current_user instanceof WP_User) {
                    $can_users =  array_intersect($wf_roles, $current_user->roles);
                    if (!empty($can_users)) {
                        $user_ok = true;
                    }
                }
                return $user_ok;
            }
	}
	endif;

	new WF_Product_Import_Export_CSV();

}

        // Welcome screen tutorial video
        add_action('admin_init', 'impexp_welcome');
        add_action('admin_menu', 'impexp_welcome_screen');
        add_action('admin_head', 'impexp_welcome_screen_remove_menus');
        register_activation_hook(__FILE__, 'hf_welcome_screen_activate_basic');

        function hf_welcome_screen_activate_basic() {
            if ( is_plugin_active('product-csv-import-export-for-woocommerce/product-csv-import-export.php') ){    
                deactivate_plugins( basename( __FILE__ ) );
                wp_die(__("Is everything fine? You already have the Premium version installed in your website. For any issues, kindly raise a ticket via <a target='_blank' href='//support.xadapter.com/'>support.xadapter.com</a>","wf-shipping-fedex"), "", array('back_link' => 1 ));
            }
            set_transient('_welcome_screen_activation_redirect', true, 30);
        }

        if( !function_exists('impexp_welcome') ){
            function impexp_welcome() {
                if (!get_transient('_welcome_screen_activation_redirect')) {
                    return;
                }
                delete_transient('_welcome_screen_activation_redirect');
                wp_safe_redirect(add_query_arg(array('page' => 'impexp-welcome'), admin_url('index.php')));
            }
        }

        if( !function_exists('impexp_welcome_screen') ){
            function impexp_welcome_screen() {
                add_dashboard_page('Welcome To Import Export', 'Welcome To Import Export', 'read', 'impexp-welcome', 'impexp_screen_content');
            }
        }

        if( !function_exists('impexp_screen_content') ){
            function impexp_screen_content() {
                include 'welcome/welcome.php';
            }
        }

        if( !function_exists('impexp_welcome_screen_remove_menus') ){
            function impexp_welcome_screen_remove_menus() {
                remove_submenu_page('index.php', 'impexp-welcome');
            }
        }
