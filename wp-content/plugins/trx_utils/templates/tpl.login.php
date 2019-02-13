<?php
/**
 * The template to display login link and popup
 *
 * @package WordPress
 * @subpackage ThemeREX Utilities
 * @since v3.0
 */

// Display link
$args = get_query_var('trx_utils_args_login');
?><a href="#popup_login" class="popup_link popup_login_link icon-user" title="<?php echo esc_attr($args['link_title']); ?>"><?php echo esc_html($args['link_text']); ?></a><?php

// Prepare popup
$social_login = do_shortcode(apply_filters('trx_utils_filter_social_login', ''));
?>
<div id="popup_login" class="popup_wrap popup_login bg_tint_light<?php if (empty($social_login)) echo ' popup_half'; ?>">
	<a href="#" class="popup_close"></a>
	<div class="form_wrap">
		<div<?php if (!empty($social_login)) echo ' class="form_left"'; ?>>
			<form action="<?php echo wp_login_url(); ?>" method="post" name="login_form" class="popup_form login_form">
				<input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url('/')); ?>">
				<div class="popup_form_field login_field iconed_field icon-user"><input type="text" id="log" name="log" value="" placeholder="<?php esc_attr_e('Login or Email', 'themerex'); ?>"></div>
				<div class="popup_form_field password_field iconed_field icon-lock"><input type="password" id="password" name="pwd" value="" placeholder="<?php esc_attr_e('Password', 'themerex'); ?>"></div>
				<div class="popup_form_field remember_field">
					<a href="<?php echo esc_url(wp_lostpassword_url(get_permalink())); ?>" class="forgot_password"><?php esc_html_e('Forgot password?', 'themerex'); ?></a>
					<input type="checkbox" value="forever" id="rememberme" name="rememberme">
					<label for="rememberme"><?php esc_html_e('Remember me', 'themerex'); ?></label>
				</div>
				<div class="popup_form_field submit_field"><input type="submit" class="submit_button" value="<?php esc_attr_e('Login', 'themerex'); ?>"></div>
			</form>
		</div>
		<?php if (!empty($social_login))  { ?>
			<div class="form_right">
				<div class="login_socials_title"><?php esc_html_e('You can login using your social profile', 'themerex'); ?></div>
				<div class="login_socials_list">
					<?php trx_utils_show_layout($social_login); ?>
				</div>
			</div>
		<?php } ?>
	</div>	<!-- /.login_wrap -->
</div>		<!-- /.popup_login -->
