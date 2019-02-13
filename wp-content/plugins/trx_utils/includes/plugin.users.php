<?php
/**
 * Users utilities
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v3.0
 */

// Don't load directly
if ( ! defined( 'TRX_UTILS_VERSION' ) ) {
	die( '-1' );
}


// Check current user (or user with specified ID) role
// For example: if (trx_utils_users_check_role('author')) { ... }
if (!function_exists('trx_utils_users_check_role')) {
	function trx_utils_users_check_role( $role, $user_id = null ) {
		if ( is_numeric( $user_id ) )
			$user = get_userdata( $user_id );
		else
			$user = wp_get_current_user();
		if ( empty( $user ) )
			return false;
		return in_array( $role, (array) $user->roles );
	}
}



/* Login and Registration
-------------------------------------------------------------------------------- */

// Add 'Login' link to the body
if (!function_exists('trx_utils_add_login_link')) {
	add_action('trx_utils_action_login', 'trx_utils_add_login_link', 10, 2);
	function trx_utils_add_login_link($link_text='', $link_title='') {
		if (($fdir = trx_utils_get_file_dir('templates/tpl.login.php')) != '') {
			set_query_var('trx_utils_args_login', array(
				'link_text' => empty($link_text) ? __('Login', 'trx_utils') : $link_text,
				'link_title' => $link_title
			));
			include $fdir;
		}
	}
}

// Add 'Register' link to the body
if (!function_exists('trx_utils_add_register_link')) {
	add_action('trx_utils_action_register', 'trx_utils_add_register_link', 10, 2);
	function trx_utils_add_register_link($link_text='', $link_title='') {
		if (($fdir = trx_utils_get_file_dir('templates/tpl.register.php')) != '') {
			set_query_var('trx_utils_args_register', array(
				'link_text' => empty($link_text) ? __('Register', 'trx_utils') : $link_text,
				'link_title' => $link_title
			));
			include_once $fdir;
		}
	}
}
	
// Add vars in the localization array
if ( !function_exists( 'trx_utils_users_localize_script' ) ) {
	add_action("trx_utils_localize_script", 'trx_utils_users_localize_script');
	function trx_utils_users_localize_script($vars) {
		$vars['login_via_ajax'] 			= apply_filters('trx_utils_filter_login_via_ajax', true);
		$vars['msg_login_empty'] 			= addslashes(esc_html__("The Login field can't be empty", 'trx_utils'));
		$vars['msg_login_long']				= addslashes(esc_html__('The Login field is too long', 'trx_utils'));
		$vars['msg_password_empty']			= addslashes(esc_html__("The password can't be empty and shorter then 4 characters", 'trx_utils'));
		$vars['msg_password_long']			= addslashes(esc_html__('The password is too long', 'trx_utils'));
		$vars['msg_login_success']			= addslashes(esc_html__('Login success! The page will be reloaded in 3 sec.', 'trx_utils'));
		$vars['msg_login_error']			= addslashes(esc_html__('Login failed!', 'trx_utils'));
		$vars['msg_not_agree']				= addslashes(esc_html__("Please, read and check 'Terms and Conditions'", 'trx_utils'));
		$vars['msg_email_empty']			= addslashes(esc_html__('Too short (or empty) email address', 'trx_utils'));
		$vars['msg_email_long']				= addslashes(esc_html__('E-mail address is too long', 'trx_utils'));
		$vars['msg_email_not_valid']		= addslashes(esc_html__('E-mail address is invalid', 'trx_utils'));
		$vars['msg_password_not_equal']		= addslashes(esc_html__('The passwords in both fields are not equal', 'trx_utils'));
		$vars['msg_registration_success']	= addslashes(esc_html__('Registration success! Please log in!', 'trx_utils'));
		$vars['msg_registration_error']		= addslashes(esc_html__('Registration failed!', 'trx_utils'));
		return $vars;
	}
}


// AJAX: New user registration
if ( !function_exists( 'trx_utils_users_registration_user' ) ) {
	add_action('wp_ajax_trx_utils_registration_user',			'trx_utils_users_registration_user');
	add_action('wp_ajax_nopriv_trx_utils_registration_user',	'trx_utils_users_registration_user');
	function trx_utils_users_registration_user() {
	
		if ( !wp_verify_nonce( trx_utils_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			die();
	
		$user_name  = substr($_REQUEST['user_name'], 0, 60);
		$user_email = substr($_REQUEST['user_email'], 0, 60);
		$user_pwd   = substr($_REQUEST['user_pwd'], 0, 60);
	
		$response = array(
			'error' => '',
			'redirect_to' => substr($_REQUEST['redirect_to'], 0, 200)
		);
	
		$id = wp_insert_user( array ('user_login' => $user_name, 'user_pass' => $user_pwd, 'user_email' => $user_email) );
		if ( is_wp_error($id) ) {
			$response['error'] = $id->get_error_message();
		} else {
			if (($notify = apply_filters('trx_utils_filter_notify_about_new_registration', 'no')) != 'no' && ($admin_email = get_option('admin_email'))) {
				// Send notify to the site admin
				if (in_array($notify, array('both', 'admin'))) {
					$subj = sprintf(esc_html__('Site %s - New user registration: %s', 'trx_utils'), esc_html(get_bloginfo('site_name')), esc_html($user_name));
					$msg = "\n".esc_html__('New registration:', 'trx_utils')
						."\n".esc_html__('Name:', 'trx_utils').' '.esc_html($user_name)
						."\n".esc_html__('E-mail:', 'trx_utils').' '.esc_html($user_email)
						."\n\n............ " . esc_html(get_bloginfo('site_name')) . " (" . esc_html(esc_url(home_url('/'))) . ") ............";
					$head = "From: " . sanitize_text_field($user_email) . "\n"
						. "Reply-To: " . sanitize_text_field($user_email) . "\n";
					$rez = wp_mail($admin_email, $subj, $msg, $head);
				}
				// Send notify to the new user
				if (in_array($notify, array('both', 'user'))) {
					$subj = sprintf(esc_html__('Welcome to the "%s"', 'trx_utils'), get_bloginfo('site_name'));
					$msg = "\n".esc_html__('Your registration data:', 'trx_utils')
						."\n".esc_html__('Name:', 'trx_utils').' '.esc_html($user_name)
						."\n".esc_html__('E-mail:', 'trx_utils').' '.esc_html($user_email)
						."\n".esc_html__('Password:', 'trx_utils').' '.esc_html($user_pwd)
						."\n\n............ " . esc_html(get_bloginfo('site_name')) . " (<a href=\"" . esc_url(home_url('/')) . "\">" . esc_html(esc_url(home_url('/'))) . "</a>) ............";
					$head = "From: " . sanitize_text_field($admin_email) . "\n"
						. "Reply-To: " . sanitize_text_field($admin_email) . "\n";
					wp_mail($user_email, $subj, $msg, $head);
				}
			}
		}
		echo json_encode($response);
		die();
	}
}



// AJAX: Login user
if ( !function_exists( 'trx_utils_users_login_user' ) ) {
	add_action('wp_ajax_trx_utils_login_user',			'trx_utils_users_login_user');
	add_action('wp_ajax_nopriv_trx_utils_login_user',	'trx_utils_users_login_user');
	function trx_utils_users_login_user() {

		if (!apply_filters('trx_utils_filter_login_via_ajax', true)) return;
	
		if ( !wp_verify_nonce( trx_utils_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			die();
	
		$user_log = substr($_REQUEST['user_log'], 0, 60);
		$user_pwd = substr($_REQUEST['user_pwd'], 0, 60);
		$remember = substr($_REQUEST['remember'], 0, 7)=='forever';

		$response = array(
			'error' => '',
			'redirect_to' => substr($_REQUEST['redirect_to'], 0, 200)
		);

		if ( is_email( $user_log ) ) {
			$user = get_user_by('email', $user_log );
			if ( $user ) $user_log = $user->user_login;
		}

		$rez = wp_signon( array(
			'user_login' => $user_log,
			'user_password' => $user_pwd,
			'remember' => $remember
			), false );

		if ( is_wp_error($rez) ) {
			$response['error'] = $rez->get_error_message();
		}

		echo json_encode($response);
		die();
	}
}
?>