<?php
$social_login = do_shortcode(apply_filters('organic_beauty_filter_social_login', organic_beauty_get_theme_option('social_login')));
?>
<div id="popup_login" class="popup_wrap popup_login bg_tint_light<?php if (empty($social_login)) echo ' popup_half'; ?>">
	<a href="#" class="popup_close"></a>
	<div class="form_wrap">
		<div<?php if (!empty($social_login)) echo ' class="form_left"'; ?>>
			<form action="<?php echo wp_login_url(); ?>" method="post" name="login_form" class="popup_form login_form">
				<input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url('/')); ?>">
				<div class="popup_form_field login_field iconed_field icon-user"><input type="text" id="log" name="log" value="" placeholder="<?php esc_attr_e('Login or Email', 'organic-beauty'); ?>"></div>
				<div class="popup_form_field password_field iconed_field icon-lock"><input type="password" id="password" name="pwd" value="" placeholder="<?php esc_attr_e('Password', 'organic-beauty'); ?>"></div>
				<div class="popup_form_field remember_field">
					<a href="<?php echo esc_url(wp_lostpassword_url(get_permalink())); ?>" class="forgot_password"><?php esc_html_e('Forgot password?', 'organic-beauty'); ?></a>
					<input type="checkbox" value="forever" id="rememberme" name="rememberme">
					<label for="rememberme"><?php esc_html_e('Remember me', 'organic-beauty'); ?></label>
				</div>
				<div class="popup_form_field submit_field"><input type="submit" class="submit_button" value="<?php esc_attr_e('Login', 'organic-beauty'); ?>"></div>
			</form>
		</div>
		<?php if (!empty($social_login))  { ?>
			<div class="form_right">
				<div class="login_socials_title"><?php esc_html_e('You can login using your social profile', 'organic-beauty'); ?></div>
				<div class="login_socials_list">
					<?php organic_beauty_show_layout($social_login); ?>
				</div>
			</div>
		<?php } ?>
	</div>	<!-- /.login_wrap -->
</div>		<!-- /.popup_login -->
