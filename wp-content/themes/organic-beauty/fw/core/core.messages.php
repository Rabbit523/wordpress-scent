<?php
/**
 * Organic Beauty Framework: messages subsystem
 *
 * @package	organic_beauty
 * @since	organic_beauty 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Theme init
if (!function_exists('organic_beauty_messages_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_messages_theme_setup' );
	function organic_beauty_messages_theme_setup() {
		// Core messages strings
		add_filter('organic_beauty_filter_localize_script', 'organic_beauty_messages_localize_script');
	}
}


/* Session messages
------------------------------------------------------------------------------------- */

if (!function_exists('organic_beauty_get_error_msg')) {
	function organic_beauty_get_error_msg() {
		return organic_beauty_storage_get('error_msg');
	}
}

if (!function_exists('organic_beauty_set_error_msg')) {
	function organic_beauty_set_error_msg($msg) {
		$msg2 = organic_beauty_get_error_msg();
		organic_beauty_storage_set('error_msg', trim($msg2) . ($msg2=='' ? '' : '<br />') . trim($msg));
	}
}

if (!function_exists('organic_beauty_get_success_msg')) {
	function organic_beauty_get_success_msg() {
		return organic_beauty_storage_get('success_msg');
	}
}

if (!function_exists('organic_beauty_set_success_msg')) {
	function organic_beauty_set_success_msg($msg) {
		$msg2 = organic_beauty_get_success_msg();
		organic_beauty_storage_set('success_msg', trim($msg2) . ($msg2=='' ? '' : '<br />') . trim($msg));
	}
}

if (!function_exists('organic_beauty_get_notice_msg')) {
	function organic_beauty_get_notice_msg() {
		return organic_beauty_storage_get('notice_msg');
	}
}

if (!function_exists('organic_beauty_set_notice_msg')) {
	function organic_beauty_set_notice_msg($msg) {
		$msg2 = organic_beauty_get_notice_msg();
		organic_beauty_storage_set('notice_msg', trim($msg2) . ($msg2=='' ? '' : '<br />') . trim($msg));
	}
}


/* System messages (save when page reload)
------------------------------------------------------------------------------------- */
if (!function_exists('organic_beauty_set_system_message')) {
	function organic_beauty_set_system_message($msg, $status='info', $hdr='') {
		update_option(organic_beauty_storage_get('options_prefix') . '_message', array('message' => $msg, 'status' => $status, 'header' => $hdr));
	}
}

if (!function_exists('organic_beauty_get_system_message')) {
	function organic_beauty_get_system_message($del=false) {
		$msg = get_option(organic_beauty_storage_get('options_prefix') . '_message', false);
		if (!$msg)
			$msg = array('message' => '', 'status' => '', 'header' => '');
		else if ($del)
			organic_beauty_del_system_message();
		return $msg;
	}
}

if (!function_exists('organic_beauty_del_system_message')) {
	function organic_beauty_del_system_message() {
		delete_option(organic_beauty_storage_get('options_prefix') . '_message');
	}
}


/* Messages strings
------------------------------------------------------------------------------------- */

if (!function_exists('organic_beauty_messages_localize_script')) {
	//add_filter('organic_beauty_filter_localize_script', 'organic_beauty_messages_localize_script');
	function organic_beauty_messages_localize_script($vars) {
		$vars['strings'] = array(
			'ajax_error'		=> esc_html__('Invalid server answer', 'organic-beauty'),
			'bookmark_add'		=> esc_html__('Add the bookmark', 'organic-beauty'),
            'bookmark_added'	=> esc_html__('Current page has been successfully added to the bookmarks. You can see it in the right panel on the tab \'Bookmarks\'', 'organic-beauty'),
            'bookmark_del'		=> esc_html__('Delete this bookmark', 'organic-beauty'),
            'bookmark_title'	=> esc_html__('Enter bookmark title', 'organic-beauty'),
            'bookmark_exists'	=> esc_html__('Current page already exists in the bookmarks list', 'organic-beauty'),
			'search_error'		=> esc_html__('Error occurs in AJAX search! Please, type your query and press search icon for the traditional search way.', 'organic-beauty'),
			'email_confirm'		=> esc_html__('On the e-mail address "%s" we sent a confirmation email. Please, open it and click on the link.', 'organic-beauty'),
			'reviews_vote'		=> esc_html__('Thanks for your vote! New average rating is:', 'organic-beauty'),
			'reviews_error'		=> esc_html__('Error saving your vote! Please, try again later.', 'organic-beauty'),
			'error_like'		=> esc_html__('Error saving your like! Please, try again later.', 'organic-beauty'),
			'error_global'		=> esc_html__('Global error text', 'organic-beauty'),
			'name_empty'		=> esc_html__('The name can\'t be empty', 'organic-beauty'),
			'name_long'			=> esc_html__('Too long name', 'organic-beauty'),
			'email_empty'		=> esc_html__('Too short (or empty) email address', 'organic-beauty'),
			'email_long'		=> esc_html__('Too long email address', 'organic-beauty'),
			'email_not_valid'	=> esc_html__('Invalid email address', 'organic-beauty'),
			'subject_empty'		=> esc_html__('The subject can\'t be empty', 'organic-beauty'),
			'subject_long'		=> esc_html__('Too long subject', 'organic-beauty'),
			'text_empty'		=> esc_html__('The message text can\'t be empty', 'organic-beauty'),
			'text_long'			=> esc_html__('Too long message text', 'organic-beauty'),
			'send_complete'		=> esc_html__("Send message complete!", 'organic-beauty'),
			'send_error'		=> esc_html__('Transmit failed!', 'organic-beauty'),
			'geocode_error'			=> esc_html__('Geocode was not successful for the following reason:', 'organic-beauty'),
			'googlemap_not_avail'	=> esc_html__('Google map API not available!', 'organic-beauty'),
			'editor_save_success'	=> esc_html__("Post content saved!", 'organic-beauty'),
			'editor_save_error'		=> esc_html__("Error saving post data!", 'organic-beauty'),
			'editor_delete_post'	=> esc_html__("You really want to delete the current post?", 'organic-beauty'),
			'editor_delete_post_header'	=> esc_html__("Delete post", 'organic-beauty'),
			'editor_delete_success'	=> esc_html__("Post deleted!", 'organic-beauty'),
			'editor_delete_error'	=> esc_html__("Error deleting post!", 'organic-beauty'),
			'editor_caption_cancel'	=> esc_html__('Cancel', 'organic-beauty'),
			'editor_caption_close'	=> esc_html__('Close', 'organic-beauty')
			);
		return $vars;
	}
}
?>