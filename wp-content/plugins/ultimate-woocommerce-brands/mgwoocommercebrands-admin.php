<?php
/**
 * Admin setting class
 *
 * @author  MagniumThemes
 * @package magniumthemes.com
 */


if ( ! class_exists( 'mgwoocommercebrandsadmin' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class mgwoocommercebrandsadmin {

		public function __construct() {

			//Actions
			add_action( 'init', array( $this, 'init' ) );

			add_action( 'woocommerce_settings_tabs_mgwoocommercebrands', array( $this, 'print_plugin_options' ) );
			add_action( 'woocommerce_update_options_mgwoocommercebrands', array( $this, 'update_options' ) );

			//Filters
			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_tab_woocommerce' ), 30 );

			add_action( 'admin_notices', array( $this, 'show_admin_notice') );

		}

		public function show_admin_notice() {
    ?>
    <div class="uwb-message error notice is-dismissible" style="display:none;">
        <p><?php _e( 'You are using FREE Version of Ultimate WooCommerce Brands plugin without additional features.', 'mgwoocommercebrands' ); ?></p>
    	<a style="margin:10px 0; display:block;" href="//www.bluehost.com/track/magniumthemes/uwb" target="_blank">
        <img border="0" src="<?php echo WP_PLUGIN_URL . DIRECTORY_SEPARATOR . "ultimate-woocommerce-brands" . DIRECTORY_SEPARATOR; ?>img/hosting-wp-button.png">
        </a>
        <a class="button-primary" style="margin-bottom: 10px;" href="http://codecanyon.net/item/ultimate-woocommerce-brands-plugin/9433984/?ref=dedalx" target="_blank">Update to PRO version to get premium features and disable ads</a>
    </div>

                    
	    <?php
	}
		/**
		 * Init method:
		 *  - default options
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function init() {
			$this->options = $this->_initOptions();
			//$this->_default_options();
		}


		/**
		 * Update plugin options.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function update_options() {
			foreach ( $this->options as $option ) {
				woocommerce_update_options( $option );
			}
		}


		/**
		 * Add Magnifier's tab to Woocommerce -> Settings page
		 *
		 * @access public
		 *
		 * @param array $tabs
		 *
		 * @return array
		 */
		public function add_tab_woocommerce( $tabs ) {
			$tabs['mgwoocommercebrands'] = __( 'Brands settings', 'mgwoocommercebrands' );

			return $tabs;
		}


		/**
		 * Print all plugin options.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_plugin_options() {
			?>
			<div class="subsubsub_section">
				<map name="Map" id="Map">
				  <area shape="rect" coords="2,0,794,130" href="http://magniumthemes.com/" target="_blank" alt="MagniumThemes" />
				  <area shape="rect" coords="10,139,198,178" href="http://magniumthemes.com/themes/" target="_blank" alt="Check all our themes" />
				  <area shape="rect" coords="202,140,393,178" href="http://facebook.com/magniumthemes" target="_blank" alt="Facebook" />
				  <area shape="rect" coords="398,142,583,178" href="http://twitter.com/magniumthemes/" target="_blank" alt="Twitter" />
				  <area shape="rect" coords="589,140,778,181" href="http://google.com/+magniumthemescompany" target="_blank" alt="Google+" />
				</map>
				<div class="mg-plugin-wrapper"><img src="<?php echo WP_PLUGIN_URL . DIRECTORY_SEPARATOR . "ultimate-woocommerce-brands" . DIRECTORY_SEPARATOR; ?>img/plugin-wrapper.png" usemap="#Map" alt="MagniumThemes - Premium Themes and Plugins"/></div>
				<div class="mgwoocommercebrands-pro-version updated woocommerce-message">
					<p><strong>You are using free light version of Ultimate WooCommerce Brands Plugin. <a target="_blank" href="http://codecanyon.net/item/ultimate-woocommerce-brands-plugin/9433984/?ref=dedalx">Purchase PRO version</a> to get this amazing features:</strong><p>
					<ul style="list-style: inherit; margin-left: 20px;">
			          <li><strong>Add logos to your brands</strong></li>
			          <li><strong>Display brand name and/or logo on Product page</strong></li>
			          <li>Special <strong>Brands page</strong> with all brands listing <strong>with ABC filtering</strong> (with logos, titles, items counts). Page display style can be changed in various ways in shortcode/VC item settings</li>
			          <li><strong>Special Responsive Brands slider</strong> with touch support (you can add it with Shortcode/VC item to any page on your site)</li>
			          <li><strong>Shortcodes</strong> for easy usage (Brands listing page, Brands Slider, Products by Brand)</li>
			          <li><strong>WordPress Widget to show your brands list</strong> in Sidebars in different ways (logos, titles, counts)</li>
			          <li><strong>WPBakery Visual Composer elements</strong> to use instead of shortcodes</li>
			          <li><strong>100% Responsive </strong>all plugin elements and pages</li>
			          <li><strong>Free Plugin updates and support</strong></li>
			        </ul>
			        <p class="submit">
			        <a class="button-primary" target="_blank" href="http://codecanyon.net/item/ultimate-woocommerce-brands-plugin/9433984/?ref=dedalx">Buy PRO version for $19</a> &nbsp;<a class="button-secondary" target="_blank" href="http://wp-plugins.dedalx.com/woocommerce-brands/">Check PRO version Demo</a>
			        </p>
				</div>
				<?php foreach ( $this->options as $id => $tab ) : ?>
					<!-- tab #<?php echo $id ?> -->
					<div class="section" id="mgwoocommercebrands_<?php echo $id ?>">
						<?php woocommerce_admin_fields( $this->options[$id] ) ?>
					</div>
				<?php endforeach ?>
			</div>
		<?php
		}


		/**
		 * Initialize the options
		 *
		 * @access protected
		 * @return array
		 * @since  1.0.0
		 */
		protected function _initOptions() {
			$options = array(
				'general' => array(
					array( 'title' => __( 'General Options', 'mgwoocommercebrands' ),
						   'type'  => 'title',
						   'desc'  => '',
						   'id'    => 'mgwoocommercebrands_options' ),
					array(
						'title'    => __( 'Brand name show on', 'mgwoocommercebrands' ),
						'id'       => 'mgb_where_show',
						'default'  => '0',
						'type'     => 'radio',
						'desc_tip' => __( 'Please select where you want show Brand name.', 'mgwoocommercebrands' ),
						'options'  => array(
							'0' => __( 'Both categories and product detail page', 'mgwoocommercebrands' ),
							'1' => __( 'Only categories ', 'mgwoocommercebrands' ),
							'2' => __( 'Only product detail', 'mgwoocommercebrands' )
						),
					),
					array(
						'title'    => __( 'Brand title', 'mgwoocommercebrands' ),
						'id'       => 'mgb_brand_title',
						'default'  => 'Brand:',
						'type'     => 'text',
						'desc_tip' => __( 'Leave empty if you dont want to show brand title before brand name(s)', 'mgwoocommercebrands' ),
						'options'  => array(
							'0' => __( 'Show as brand(s) title', 'mgwoocommercebrands' ),
							'1' => __( 'Show as brand(s) image', 'mgwoocommercebrands' )
						),
					),
					
					array(
						'title'    => __( 'Brand display type on product detail page', 'mgwoocommercebrands' ),
						'id'       => 'mgb_show_image',
						'default'  => '0',
						'type'     => 'radio',
						'desc_tip' => __( 'Please check if you want to see brand image instead of title', 'mgwoocommercebrands' ),
						'options'  => array(
							'0' => __( 'Show as brand(s) title', 'mgwoocommercebrands' ),
							'1' => __( 'Show as brand(s) image', 'mgwoocommercebrands' )
						),
					),
					
					array( 'type' => 'sectionend', 'id' => 'mgwoocommercebrands_options' ),

					array( 'title' => __( 'Product Details Page', 'mgwoocommercebrands' ),
						   'type'  => 'title',
						   'desc'  => __( 'Predefined brand display positions will work only if your theme does not changed default WooCommerce templates structure. If position does not work for you - you need to use Custom CSS selector (available in PRO version).', 'mgwoocommercebrands' ),
						   'id'    => 'mgwoocommercebrands_detail_product' ),
					array(
						'title'    => __( 'Brand display position', 'mgwoocommercebrands' ),
						'id'       => 'mgb_detail_position',
						'default'  => '0',
						'type'     => 'radio',
						'desc_tip' => __( 'Please choose postion where brand show on product details page.', 'mgwoocommercebrands' ),
						'options'  => array(
							'0' => __( 'Above tabs area', 'mgwoocommercebrands' ),
							'1' => __( 'Below tabs area', 'mgwoocommercebrands' ),
							'2' => __( 'Above short description', 'mgwoocommercebrands' ),
							'3' => __( 'Below short description', 'mgwoocommercebrands' ),
							'4' => __( 'Above Add to cart', 'mgwoocommercebrands' ),
							'5' => __( 'Below Add to cart', 'mgwoocommercebrands' ),
							'6' => __( 'Above Categories list', 'mgwoocommercebrands' ),
							'7' => __( 'Below Categories list', 'mgwoocommercebrands' )
						),
					),

					array( 'type' => 'sectionend', 'id' => 'mgwoocommercebrands_detail_product' ),

					array( 'title' => __( 'Product Category', 'mgwoocommercebrands' ),
						   'type'  => 'title',
						   'desc'  => __( 'Predefined brand display positions will work only if your theme does not changed default WooCommerce templates structure. If position does not work for you - you need to use Custom CSS selector (available in PRO version).', 'mgwoocommercebrands' ),
						   'id'    => 'mgwoocommercebrands_product_category' ),
					array(
						'title'    => __( 'Brand display position on category', 'mgwoocommercebrands' ),
						'id'       => 'mgb_category_position',
						'default'  => '0',
						'type'     => 'radio',
						'desc_tip' => __( 'Please choose postion where brand show on category products.', 'mgwoocommercebrands' ),
						'options'  => array(
							'0' => __( 'Above price', 'mgwoocommercebrands' ),
							'1' => __( 'Above title', 'mgwoocommercebrands' ),
							'4' => __( 'Below title', 'mgwoocommercebrands' ),
							'2' => __( 'Above Add to Cart', 'mgwoocommercebrands' ),
							'3' => __( 'Below Add to Cart', 'mgwoocommercebrands' )
							
						),
					),
					array( 'type' => 'sectionend', 'id' => 'mgwoocommercebrands_product_category' )

				)
			);

			return apply_filters( 'mgwoocommercebrands_tab_options', $options );
		}
	}
}
