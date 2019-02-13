<?php
/*
Plugin Name: YITH WooCommerce Gift Cards
Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-gift-cards/
Description: Allow your users to purchase and give gift cards, an easy and direct way to encourage new sales.
Author: YITHEMES
Text Domain: yith-woocommerce-gift-cards
Version: 1.3.1
Author URI: http://yithemes.com/
*/

if ( ! defined ( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/** Define plugin constants */
$wp_upload_dir = wp_upload_dir ();

defined ( 'YITH_YWGC_FREE_INIT' ) || define ( 'YITH_YWGC_FREE_INIT', plugin_basename ( __FILE__ ) );
defined ( 'YITH_YWGC_PLUGIN_BASENAME' ) || define ( 'YITH_YWGC_PLUGIN_BASENAME', plugin_basename ( __FILE__ ) );
defined ( 'YITH_YWGC_VERSION' ) || define ( 'YITH_YWGC_VERSION', '1.3.1' );
defined ( 'YITH_YWGC_DB_CURRENT_VERSION' ) || define ( 'YITH_YWGC_DB_CURRENT_VERSION', '1.0.1' );
defined ( 'YITH_YWGC_FILE' ) || define ( 'YITH_YWGC_FILE', __FILE__ );
defined ( 'YITH_YWGC_DIR' ) || define ( 'YITH_YWGC_DIR', plugin_dir_path ( __FILE__ ) );
defined ( 'YITH_YWGC_URL' ) || define ( 'YITH_YWGC_URL', plugins_url ( '/', __FILE__ ) );
defined ( 'YITH_YWGC_ASSETS_URL' ) || define ( 'YITH_YWGC_ASSETS_URL', YITH_YWGC_URL . 'assets' );
defined ( 'YITH_YWGC_TEMPLATES_DIR' ) || define ( 'YITH_YWGC_TEMPLATES_DIR', YITH_YWGC_DIR . 'templates' );
defined ( 'YITH_YWGC_ASSETS_IMAGES_URL' ) || define ( 'YITH_YWGC_ASSETS_IMAGES_URL', YITH_YWGC_ASSETS_URL . '/images/' );
defined ( 'YITH_YWGC_SAVE_DIR' ) || define ( 'YITH_YWGC_SAVE_DIR', $wp_upload_dir[ 'basedir' ] . '/yith-gift-cards/' );
defined ( 'YITH_YWGC_SAVE_URL' ) || define ( 'YITH_YWGC_SAVE_URL', $wp_upload_dir[ 'baseurl' ] . '/yith-gift-cards/' );

require_once ( plugin_dir_path ( __FILE__ ) . 'functions.php' );
yith_initialize_plugin_fw ( plugin_dir_path ( __FILE__ ) );

/* register current plugin for YITH */
register_activation_hook ( __FILE__, 'yith_plugin_registration_hook' );
/* Plugin Framework Version Check */
yit_maybe_plugin_fw_loader ( plugin_dir_path ( __FILE__ ) );

add_action ( 'yith_ywgc_init', 'yith_ywgc_init' );
add_action ( 'plugins_loaded', 'yith_ywgc_install', 11 );

//  Init database vars
require_once ( YITH_YWGC_DIR . 'lib/class.yith-woocommerce-gift-cards.php' );
