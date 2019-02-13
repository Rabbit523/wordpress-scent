<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'organic_beauty_template_header_1_theme_setup' ) ) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_template_header_1_theme_setup', 1 );
	function organic_beauty_template_header_1_theme_setup() {
		organic_beauty_add_template(array(
			'layout' => 'header_1',
			'mode'   => 'header',
			'title'  => esc_html__('Header 1', 'organic-beauty'),
			'icon'   => organic_beauty_get_file_url('templates/headers/images/1.jpg')
			));
	}
}

// Template output
if ( !function_exists( 'organic_beauty_template_header_1_output' ) ) {
	function organic_beauty_template_header_1_output($post_options, $post_data) {

		// WP custom header
		$header_css = '';
		if ($post_options['position'] != 'over') {
			$header_image = get_header_image();
			$header_css = $header_image!='' 
				? ' style="background-image: url('.esc_url($header_image).')"' 
				: '';
		}
		?>
		
		<div class="top_panel_fixed_wrap"></div>

		<header class="top_panel_wrap top_panel_style_1 scheme_<?php echo esc_attr($post_options['scheme']); ?>">
			<div class="top_panel_wrap_inner top_panel_inner_style_1 top_panel_position_<?php echo esc_attr(organic_beauty_get_custom_option('top_panel_position')); ?>">
			
			<?php if (organic_beauty_get_custom_option('show_top_panel_top')=='yes') { ?>
				<div class="top_panel_top">
					<div class="content_wrap clearfix">
						<?php
						organic_beauty_template_set_args('top-panel-top', array(
							'top_panel_top_components' => array('login', 'socials', 'currency', 'bookmarks')
						));
						get_template_part(organic_beauty_get_file_slug('templates/headers/_parts/top-panel-top.php'));
						?>
					</div>
				</div>
			<?php } ?>

			<div class="top_panel_middle" <?php organic_beauty_show_layout($header_css); ?>>
				<div class="content_wrap">
					<div class="columns_wrap columns_fluid no_margins">
						<div class="contact_logo">
							<?php organic_beauty_show_logo(); ?>
						</div><div class="contact_in"><div class="columns_fluid">
							<?php if (organic_beauty_get_custom_option('show_search')=='yes') organic_beauty_show_layout(organic_beauty_sc_search(array("style"=>organic_beauty_get_theme_option('search_style')))); ?>
							<div class="header-buttons">
								<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>">
									<?php echo is_user_logged_in() ? 'DASHBOARD' : 'Bli medlem'; ?>
								</a>
								
								<?php if( ! is_user_logged_in() ) { ?>
									<a href="#popup_login" class="popup_link popup_login_link inited login-bt">Logg inn</a>
									<?php get_template_part(organic_beauty_get_file_slug('templates/_parts/popup-login.php')); ?>
								<?php } else { ?>
									<a href="<?php echo wp_logout_url( home_url() ); ?>" class="inited logout-bt">LOGG UT</a>
								<?php } ?>
							</div>
						</div>
				</div>
				</div>
			</div>

			<div class="top_panel_bottom">
				<div class="content_wrap clearfix">
					<nav class="menu_main_nav_area menu_hover_fade">
						<?php
						$menu_main = organic_beauty_get_nav_menu('menu_main');
						if (empty($menu_main)) $menu_main = organic_beauty_get_nav_menu();
						organic_beauty_show_layout($menu_main);
						?>
						<?php global $woocommerce; if( is_object($woocommerce) ) : ?>
						<div class="header_cart">
							<a href="<?php echo wc_get_cart_url(); ?>"><span class="cart_count"><?php echo $woocommerce->cart->cart_contents_count ?></span>
							Til kassen: <?php wc_cart_totals_order_total_html(); ?></a>
						</div>
						<?php endif; ?>
					</nav>
				</div>
			</div>

			</div>
			</div>
		</header>

		<?php
		organic_beauty_storage_set('header_mobile', array(
			 'login' => true,
			 'socials' => true,
			 'bookmarks' => false,
			 'contact_address' => false,
			 'contact_phone_email' => false,
			 'woo_cart' => true,
			 'search' => true
			)
		);
	}
}
?>