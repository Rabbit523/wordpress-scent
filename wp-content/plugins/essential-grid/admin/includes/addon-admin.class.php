<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       h
 * @since      1.0.0
 *
 * @package    ess_grid_addon
 * @subpackage ess_grid_addon/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    ess_grid_addon
 * @subpackage ess_grid_addon/admin
 * @author     ThemePunch <info@themepunch.com>
 */
class Ess_Grid_Addon_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$plugin = Essential_Grid::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in ess_grid_addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ess_grid_addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if(isset($_GET["page"]) && $_GET["page"]=="essgrid_addon"){
			wp_enqueue_style($this->plugin_slug .'-admin-styles', EG_PLUGIN_URL . 'admin/assets/css/admin.css', array(), Essential_Grid::VERSION );
			wp_enqueue_style( $this->plugin_name.'-addon-admin', EG_PLUGIN_URL . 'admin/assets/css/addon-admin.css', array( ), $this->version);
			wp_enqueue_style('rs-open-sans', '//fonts.googleapis.com/css?family=Open+Sans:400,300,700,600,800');
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in ess_grid_addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ess_grid_addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if(isset($_GET["page"]) && $_GET["page"]=="essgrid_addon"){
			wp_enqueue_script('tp-tools', EG_PLUGIN_URL .'public/assets/js/jquery.themepunch.tools.min.js', array(), false );
			wp_enqueue_script('unite_admin', RS_PLUGIN_URL .'admin/assets/js/admin.js', array(), RevSliderGlobals::SLIDER_REVISION );
			wp_enqueue_script( $this->plugin_name, EG_PLUGIN_URL .'admin/assets/js/essgrid_addon-admin.js', array( 'jquery' ), $this->version, false );
			wp_localize_script( $this->plugin_name, 'essgrid_addon', array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'please_wait_a_moment' => __("Please Wait a Moment",EG_TEXTDOMAIN),
					'settings_saved' => __("Settings saved",EG_TEXTDOMAIN)
				));
				
		}
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		$this->plugin_screen_hook_suffix = add_submenu_page(
			'essential-grid',
			__( 'Add-Ons', EG_TEXTDOMAIN ),
			__( 'Add-Ons', EG_TEXTDOMAIN ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( EG_PLUGIN_PATH.'admin/views/grid-addons-admin-display.php' );
	}

	/**
	 * Activates Installed Add-On/Plugin
	 *
	 * @since    1.0.0
	 */
	public function activate_plugin() {
		// Verify that the incoming request is coming with the security nonce
		if( wp_verify_nonce( $_REQUEST['nonce'], 'ajax_essgrid_addon_nonce' ) ) {
			if(isset($_REQUEST['plugin'])){
				$result = activate_plugin( $_REQUEST['plugin'] );
				if ( is_wp_error( $result ) ) {
					// Process Error
					die('0');
				}
				die( '1' );
			}
			else{
				die( '0' );
			}
		} 
		else {
			die( '-1' );
		}
	}

	/**
	 * Deactivates Installed Add-On/Plugin
	 *
	 * @since    1.0.0
	 */
	public function deactivate_plugin() {
		// Verify that the incoming request is coming with the security nonce
		if( wp_verify_nonce( $_REQUEST['nonce'], 'ajax_essgrid_addon_nonce' ) ) {
			if(isset($_REQUEST['plugin'])){
				$result = deactivate_plugins( $_REQUEST['plugin'] );
				if ( is_wp_error( $result ) ) {
					// Process Error
					die('0');
				}
				die( '1' );
			}
			else{
				die( '0' );
			}
		} 
		else {
			die( '-1' );
		}
	}

	/**
	 * Install Add-On/Plugin
	 *
	 * @since    1.0.0
	 */
	public function install_plugin() {
		if( wp_verify_nonce( $_REQUEST['nonce'], 'ajax_essgrid_addon_nonce' ) ) {
			if(isset($_REQUEST['plugin'])){
				global $wp_version;
				$plugin_slug = basename($_REQUEST['plugin']);
				$plugin_result = false;
				$plugin_message = 'UNKNOWN';

				if(0 !== strpos($plugin_slug, 'essential-grid-')) die( '-1' );

				$url = 'http://updates.themepunch.tools/addons/'.$plugin_slug.'/'.$plugin_slug.'.zip';

				//	echo $url;

				$get = wp_remote_post($url, array(
					'user-agent' => 'WordPress/'.$wp_version.'; '.get_bloginfo('url'),
					'body' => '',
					'timeout' => 45
				));

				if( !$get || $get["response"]["code"] != "200" ){
				  $plugin_message = 'FAILED TO DOWNLOAD';
				}else{
					$plugin_message = 'ZIP is there';
					$upload_dir = wp_upload_dir();
					$file = $upload_dir['basedir']. /*'/essgrid/templates/' .*/ $plugin_slug . '.zip';
					@mkdir(dirname($file));
					$ret = @file_put_contents( $file, $get['body'] );

					WP_Filesystem();

					global $wp_filesystem;

					$upload_dir = wp_upload_dir();
					$d_path = WP_PLUGIN_DIR;
					$unzipfile = unzip_file( $file, $d_path);

					if( is_wp_error($unzipfile) ){
						define('FS_METHOD', 'direct'); //lets try direct. 

						WP_Filesystem();  //WP_Filesystem() needs to be called again since now we use direct !

						//@chmod($file, 0775);
						$unzipfile = unzip_file( $file, $d_path);
						if( is_wp_error($unzipfile) ){
							$d_path = WP_PLUGIN_DIR;
							$unzipfile = unzip_file( $file, $d_path);

							if( is_wp_error($unzipfile) ){
								$f = basename($file);
								$d_path = str_replace($f, '', $file);

								$unzipfile = unzip_file( $file, $d_path);
							}
						}
					}
					@unlink($file);
					die('1');
				}
				//$result = activate_plugin( $plugin_slug.'/'.$plugin_slug.'.php' );
			}
			else{
				die( '0' );
			}
		} 
		else {
			die( '-1' );
		}
	}

} // END of class