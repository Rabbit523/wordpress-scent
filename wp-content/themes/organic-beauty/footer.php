<?php
/**
 * The template for displaying the footer.
 */

				organic_beauty_close_wrapper();	// <!-- </.content> -->

				// Show main sidebar
				get_sidebar();

				if (organic_beauty_get_custom_option('body_style')!='fullscreen') organic_beauty_close_wrapper();	// <!-- </.content_wrap> -->
				?>
			
			</div>		<!-- </.page_content_wrap> -->
			
			<?php
			// Footer Testimonials stream
			if (organic_beauty_get_custom_option('show_testimonials_in_footer')=='yes') { 
				$count = max(1, organic_beauty_get_custom_option('testimonials_count'));
				$data = organic_beauty_sc_testimonials(array('count'=>$count));
				if ($data) {
					?>
					<footer class="testimonials_wrap sc_section scheme_<?php echo esc_attr(organic_beauty_get_custom_option('testimonials_scheme')); ?>">
						<div class="testimonials_wrap_inner sc_section_inner sc_section_overlay">
							<div class="content_wrap"><?php organic_beauty_show_layout($data); ?></div>
						</div>
					</footer>
					<?php
				}
			}
			
			// Footer sidebar
			$footer_show  = organic_beauty_get_custom_option('show_sidebar_footer');
			$sidebar_name = organic_beauty_get_custom_option('sidebar_footer');
			if (!organic_beauty_param_is_off($footer_show) && is_active_sidebar($sidebar_name)) { 
				organic_beauty_storage_set('current_sidebar', 'footer');
				?>
				<footer class="footer_wrap widget_area scheme_<?php echo esc_attr(organic_beauty_get_custom_option('sidebar_footer_scheme')); ?>">
					<div class="footer_wrap_inner widget_area_inner">
						<div class="content_wrap">
							<div class="columns_wrap"><?php
							ob_start();
							do_action( 'before_sidebar' );
							if ( !dynamic_sidebar($sidebar_name) ) {
								// Put here html if user no set widgets in sidebar
							}
							do_action( 'after_sidebar' );
							$out = ob_get_contents();
							ob_end_clean();
							organic_beauty_show_layout(trim(preg_replace("/<\/aside>[\r\n\s]*<aside/", "</aside><aside", $out)));
							?></div>	<!-- /.columns_wrap -->
						</div>	<!-- /.content_wrap -->
					</div>	<!-- /.footer_wrap_inner -->
				</footer>	<!-- /.footer_wrap -->
				<?php
			}


			// Footer Twitter stream
			if (organic_beauty_get_custom_option('show_twitter_in_footer')=='yes') { 
				$count = max(1, organic_beauty_get_custom_option('twitter_count'));
				$data = organic_beauty_sc_twitter(array('count'=>$count));
				if ($data) {
					?>
					<footer class="twitter_wrap sc_section scheme_<?php echo esc_attr(organic_beauty_get_custom_option('twitter_scheme')); ?>">
						<div class="twitter_wrap_inner sc_section_inner sc_section_overlay">
							<div class="content_wrap"><?php organic_beauty_show_layout($data); ?></div>
						</div>
					</footer>
					<?php
				}
			}


			// Google map
			if ( organic_beauty_get_custom_option('show_googlemap')=='yes' ) { 
				$map_address = organic_beauty_get_custom_option('googlemap_address');
				$map_latlng  = organic_beauty_get_custom_option('googlemap_latlng');
				$map_zoom    = organic_beauty_get_custom_option('googlemap_zoom');
				$map_style   = organic_beauty_get_custom_option('googlemap_style');
				$map_height  = organic_beauty_get_custom_option('googlemap_height');
				if (!empty($map_address) || !empty($map_latlng)) {
					$args = array();
					if (!empty($map_style))		$args['style'] = esc_attr($map_style);
					if (!empty($map_zoom))		$args['zoom'] = esc_attr($map_zoom);
					if (!empty($map_height))	$args['height'] = esc_attr($map_height);
					organic_beauty_show_layout(organic_beauty_sc_googlemap($args));
				}
			}

			// Footer contacts
			$footer_show_contacts_area = 'hide_con_area';
			if (organic_beauty_get_custom_option('show_contacts_in_footer')=='yes') {
				$contacts_text = organic_beauty_get_custom_option('contacts_in_footer');
				if (!empty($contacts_text)) {
					$footer_show_contacts_area = ' show_con_area ';
					?>
					<footer class="contacts_wrap scheme_<?php echo esc_attr(organic_beauty_get_custom_option('contacts_scheme')); ?>">
						<div class="contacts_wrap_inner">
							<div class="content_wrap">
								<div class="contacts_text">
									<?php echo organic_beauty_do_shortcode(apply_filters('organic_beauty_filter_sc_clear_around', $contacts_text)); ?>
								</div>
							</div>	<!-- /.content_wrap -->
						</div>	<!-- /.contacts_wrap_inner -->
					</footer>	<!-- /.contacts_wrap -->
					<?php
				}
			}

			// Copyright area
			$copyright_style = organic_beauty_get_custom_option('show_copyright_in_footer');
			if (!organic_beauty_param_is_off($copyright_style)) {
				?> 
				<div class="copyright_wrap copyright_style_<?php echo esc_attr($copyright_style); ?> <?php echo esc_attr($footer_show_contacts_area); ?> scheme_<?php echo esc_attr(organic_beauty_get_custom_option('copyright_scheme')); ?>">
					<div class="copyright_wrap_inner">
						<div class="content_wrap">
							<?php
							if ($copyright_style == 'menu' || $copyright_style == 'socials') {
								if (($menu = organic_beauty_get_nav_menu('menu_footer'))!='') {
									organic_beauty_show_layout($menu);
								}
							}
							?>
							<div class="copyright_text"><?php echo force_balance_tags(organic_beauty_get_custom_option('footer_copyright')); ?></div>
							<?php
							if ($copyright_style == 'socials') {
								organic_beauty_show_layout(organic_beauty_sc_socials(array('size'=>"small")));
							}
							?>
						</div>
					</div>
				</div>
				<?php
			}
			?>
			
		</div>	<!-- /.page_wrap -->

	</div>		<!-- /.body_wrap -->

	<?php wp_footer(); ?>

</body>
</html>