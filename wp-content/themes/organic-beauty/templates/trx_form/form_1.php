<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'organic_beauty_template_form_1_theme_setup' ) ) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_template_form_1_theme_setup', 1 );
	function organic_beauty_template_form_1_theme_setup() {
		organic_beauty_add_template(array(
			'layout' => 'form_1',
			'mode'   => 'forms',
			'title'  => esc_html__('Contact Form 1', 'organic-beauty')
			));
	}
}

// Template output
if ( !function_exists( 'organic_beauty_template_form_1_output' ) ) {
	function organic_beauty_template_form_1_output($post_options, $post_data) {
		$form_style = 'default';
		?>
		<form <?php echo !empty($post_options['id']) ? ' id="'.esc_attr($post_options['id']).'_form"' : ''; ?>
			class="sc_input_hover_<?php echo esc_attr($form_style); ?>"
			data-formtype="<?php echo esc_attr($post_options['layout']); ?>"
			method="post"
			action="<?php echo esc_url($post_options['action'] ? $post_options['action'] : admin_url('admin-ajax.php')); ?>">
			<?php organic_beauty_sc_form_show_fields($post_options['fields']); ?>
			<div class="sc_form_info">
				<div class="sc_columns columns_wrap"><div class="column-1_2"><div class="sc_form_item sc_form_field label_over"><input id="sc_form_username" type="text" name="username"<?php if ($form_style=='default') echo ' placeholder="'.esc_attr__('Name *', 'organic-beauty').'"'; ?> aria-required="true"><?php
						if ($form_style!='default') {
							?><label class="required" for="sc_form_username"><?php
								if ($form_style == 'path') {
									?><svg class="sc_form_graphic" preserveAspectRatio="none" viewBox="0 0 404 77" height="100%" width="100%"><path d="m0,0l404,0l0,77l-404,0l0,-77z"></svg><?php
								} else if ($form_style == 'iconed') {
									?><i class="sc_form_label_icon icon-user"></i><?php
								}
								?><span class="sc_form_label_content" data-content="<?php esc_html_e('Name', 'organic-beauty'); ?>"><?php esc_html_e('Name', 'organic-beauty'); ?></span><?php
							?></label><?php
						}
					?></div></div><div class="column-1_2"><div class="sc_form_item sc_form_field label_over"><input id="sc_form_email" type="text" name="email"<?php if ($form_style=='default') echo ' placeholder="'.esc_attr__('E-mail *', 'organic-beauty').'"'; ?> aria-required="true"><?php
						if ($form_style!='default') {
							?><label class="required" for="sc_form_email"><?php
								if ($form_style == 'path') {
									?><svg class="sc_form_graphic" preserveAspectRatio="none" viewBox="0 0 404 77" height="100%" width="100%"><path d="m0,0l404,0l0,77l-404,0l0,-77z"></svg><?php
								} else if ($form_style == 'iconed') {
									?><i class="sc_form_label_icon icon-mail-empty"></i><?php
								}
								?><span class="sc_form_label_content" data-content="<?php esc_html_e('E-mail', 'organic-beauty'); ?>"><?php esc_html_e('E-mail', 'organic-beauty'); ?></span><?php
							?></label><?php
						}
					?></div></div></div>
				<div class="sc_form_item sc_form_field label_over"><input id="sc_form_subj" type="text" name="subject"<?php if ($form_style=='default') echo ' placeholder="'.esc_attr__('Subject', 'organic-beauty').'"'; ?> aria-required="true"><?php
					if ($form_style!='default') { 
						?><label class="required" for="sc_form_subj"><?php
							if ($form_style == 'path') {
								?><svg class="sc_form_graphic" preserveAspectRatio="none" viewBox="0 0 404 77" height="100%" width="100%"><path d="m0,0l404,0l0,77l-404,0l0,-77z"></svg><?php
							} else if ($form_style == 'iconed') {
								?><i class="sc_form_label_icon icon-menu"></i><?php
							}
							?><span class="sc_form_label_content" data-content="<?php esc_html_e('Subject', 'organic-beauty'); ?>"><?php esc_html_e('Subject', 'organic-beauty'); ?></span><?php
						?></label><?php
					}
				?></div>
			</div>
			<div class="sc_form_item sc_form_message"><textarea id="sc_form_message" name="message"<?php if ($form_style=='default') echo ' placeholder="'.esc_attr__('Message', 'organic-beauty').'"'; ?> aria-required="true"></textarea><?php
				if ($form_style!='default') { 
					?><label class="required" for="sc_form_message"><?php 
						if ($form_style == 'path') {
							?><svg class="sc_form_graphic" preserveAspectRatio="none" viewBox="0 0 404 77" height="100%" width="100%"><path d="m0,0l404,0l0,77l-404,0l0,-77z"></svg><?php
						} else if ($form_style == 'iconed') {
							?><i class="sc_form_label_icon icon-feather"></i><?php
						}
						?><span class="sc_form_label_content" data-content="<?php esc_html_e('Message', 'organic-beauty'); ?>"><?php esc_html_e('Message', 'organic-beauty'); ?></span><?php
					?></label><?php
				}
			?></div>
			<div class="sc_form_item sc_form_button"><button><?php esc_html_e('Send Message', 'organic-beauty'); ?></button></div>
			<div class="result sc_infobox"></div>
		</form>
		<?php
	}
}
?>