<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('organic_beauty_sc_form_theme_setup')) {
	add_action( 'organic_beauty_action_before_init_theme', 'organic_beauty_sc_form_theme_setup' );
	function organic_beauty_sc_form_theme_setup() {
		add_action('organic_beauty_action_shortcodes_list', 		'organic_beauty_sc_form_reg_shortcodes');
		if (function_exists('organic_beauty_exists_visual_composer') && organic_beauty_exists_visual_composer())
			add_action('organic_beauty_action_shortcodes_list_vc','organic_beauty_sc_form_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_form id="unique_id" title="Contact Form" description="Mauris aliquam habitasse magna."]
*/

if (!function_exists('organic_beauty_sc_form')) {	
	function organic_beauty_sc_form($atts, $content = null) {
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "form_custom",
			"action" => "",
			"return_url" => "",
			"return_page" => "",
			"align" => "",
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"scheme" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"width" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
	
		if (empty($id)) $id = "sc_form_".str_replace('.', '', mt_rand());
		$class .= ($class ? ' ' : '') . organic_beauty_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= organic_beauty_get_css_dimensions_from_values($width);
	
		organic_beauty_enqueue_messages();	// Load core messages
	
		organic_beauty_storage_set('sc_form_data', array(
			'id' => $id,
            'counter' => 0
            )
        );
	
		if ($style == 'form_custom')
			$content = do_shortcode($content);
		
		$fields = array();
		if (!empty($return_page)) 
			$return_url = get_permalink($return_page);
		if (!empty($return_url))
			$fields[] = array(
				'name' => 'return_url',
				'type' => 'hidden',
				'value' => $return_url
			);

		$output = '<div ' . ($id ? ' id="'.esc_attr($id).'_wrap"' : '')
					. ' class="sc_form_wrap'
					. ($scheme && !organic_beauty_param_is_off($scheme) && !organic_beauty_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
					. '">'
			.'<div ' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_form'
					. ' sc_form_style_'.($style) 
					. (!empty($align) && !organic_beauty_param_is_off($align) ? ' align'.esc_attr($align) : '') 
					. (!empty($class) ? ' '.esc_attr($class) : '') 
					. '"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
				. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
				. '>'
					. (!empty($subtitle) 
						? '<h6 class="sc_form_subtitle sc_item_subtitle">' . trim(organic_beauty_strmacros($subtitle)) . '</h6>' 
						: '')
					. (!empty($title) 
						? '<h2 class="sc_form_title sc_item_title' . (empty($description) ? ' sc_item_title_without_descr' : ' sc_item_title_without_descr') . '">' . trim(organic_beauty_strmacros($title)) . '</h2>' 
						: '')
					. (!empty($description) 
						? '<div class="sc_form_descr sc_item_descr">' . trim(organic_beauty_strmacros($description)) . ($style == 1 ? do_shortcode('[trx_socials size="tiny" shape="round"][/trx_socials]') : '') . '</div>' 
						: '');
		
		$output .= organic_beauty_show_post_layout(array(
												'layout' => $style,
												'id' => $id,
												'action' => $action,
												'content' => $content,
												'fields' => $fields,
												'show' => false
												), false);

		$output .= '</div>'
				. '</div>';
	
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_form', $atts, $content);
	}
	organic_beauty_require_shortcode("trx_form", "organic_beauty_sc_form");
}

if (!function_exists('organic_beauty_sc_form_item')) {	
	function organic_beauty_sc_form_item($atts, $content=null) {
		if (organic_beauty_in_shortcode_blogger()) return '';
		extract(organic_beauty_html_decode(shortcode_atts( array(
			// Individual params
			"type" => "text",
			"name" => "",
			"value" => "",
			"options" => "",
			"align" => "",
			"label" => "",
			"label_position" => "top",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
	
		organic_beauty_storage_inc_array('sc_form_data', 'counter');
	
		$class .= ($class ? ' ' : '') . organic_beauty_get_css_position_as_classes($top, $right, $bottom, $left);
		if (empty($id)) $id = organic_beauty_storage_get_array('sc_form_data', 'id').'_'.organic_beauty_storage_get_array('sc_form_data', 'counter');
	
		$label = $type!='button' && $type!='submit' && $label ? '<label for="' . esc_attr($id) . '">' . esc_attr($label) . '</label>' : $label;
	
		// Open field container
		$output = '<div class="sc_form_item sc_form_item_'.esc_attr($type)
						.' sc_form_'.($type == 'textarea' ? 'message' : ($type == 'button' || $type == 'submit' ? 'button' : 'field'))
						.' label_'.esc_attr($label_position)
						.($class ? ' '.esc_attr($class) : '')
						.($align && $align!='none' ? ' align'.esc_attr($align) : '')
					.'"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
					. (!organic_beauty_param_is_off($animation) ? ' data-animation="'.esc_attr(organic_beauty_get_animation_classes($animation)).'"' : '')
					. '>';
		
		// Label top or left
		if ($type!='button' && $type!='submit' && ($label_position=='top' || $label_position=='left'))
			$output .= $label;

		// Field output
		if ($type == 'textarea')

			$output .= '<textarea id="' . esc_attr($id) . '" name="' . esc_attr($name ? $name : $id) . '">' . esc_attr($value) . '</textarea>';

		else if ($type=='button' || $type=='submit')

			$output .= '<button id="' . esc_attr($id) . '">'.($label ? $label : $value).'</button>';

		else if ($type=='radio' || $type=='checkbox') {

			if (!empty($options)) {
				$options = explode('|', $options);
				if (!empty($options)) {
					$i = 0;
					foreach ($options as $v) {
						$i++;
						$parts = explode('=', $v);
						if (count($parts)==1) $parts[1] = $parts[0];
						$output .= '<div class="sc_form_element">'
										. '<input type="'.esc_attr($type) . '"'
											. ' id="' . esc_attr($id.($i>1 ? '_'.intval($i) : '')) . '"'
											. ' name="' . esc_attr($name ? $name : $id) . (count($options) > 1 && $type=='checkbox' ? '[]' : '') . '"'
											. ' value="' . esc_attr(trim(chop($parts[0]))) . '"' 
											. (in_array($parts[0], explode(',', $value)) ? ' checked="checked"' : '') 
										. '>'
										. '<label for="' . esc_attr($id.($i>1 ? '_'.intval($i) : '')) . '">' . trim(chop($parts[1])) . '</label>'
									. '</div>';
					}
				}
			}

		} else if ($type=='select') {

			if (!empty($options)) {
				$options = explode('|', $options);
				if (!empty($options)) {
					$output .= '<div class="sc_form_select_container">'
						. '<select id="' . esc_attr($id) . '" name="' . esc_attr($name ? $name : $id) . '">';
					foreach ($options as $v) {
						$parts = explode('=', $v);
						if (count($parts)==1) $parts[1] = $parts[0];
						$output .= '<option'
										. ' value="' . esc_attr(trim(chop($parts[0]))) . '"' 
										. (in_array($parts[0], explode(',', $value)) ? ' selected="selected"' : '') 
									. '>'
									. trim(chop($parts[1]))
									. '</option>';
					}
					$output .= '</select>'
							. '</div>';
				}
			}

		} else if ($type=='date') {
			organic_beauty_enqueue_script( 'jquery-picker', organic_beauty_get_file_url('/js/picker/picker.js'), array('jquery'), null, true );
			organic_beauty_enqueue_script( 'jquery-picker-date', organic_beauty_get_file_url('/js/picker/picker.date.js'), array('jquery'), null, true );
			$output .= '<div class="sc_form_date_wrap icon-calendar-light">'
						. '<input placeholder="' . esc_attr__('Date', 'organic-beauty') . '" id="' . esc_attr($id) . '" class="js__datepicker" type="text" name="' . esc_attr($name ? $name : $id) . '">'
					. '</div>';

		} else if ($type=='time') {
			organic_beauty_enqueue_script( 'jquery-picker', organic_beauty_get_file_url('/js/picker/picker.js'), array('jquery'), null, true );
			organic_beauty_enqueue_script( 'jquery-picker-time', organic_beauty_get_file_url('/js/picker/picker.time.js'), array('jquery'), null, true );
			$output .= '<div class="sc_form_time_wrap icon-clock-empty">'
						. '<input placeholder="' . esc_attr__('Time', 'organic-beauty') . '" id="' . esc_attr($id) . '" class="js__timepicker" type="text" name="' . esc_attr($name ? $name : $id) . '">'
					. '</div>';
	
		} else

			$output .= '<input type="'.esc_attr($type ? $type : 'text').'" id="' . esc_attr($id) . '" name="' . esc_attr($name ? $name : $id) . '" value="' . esc_attr($value) . '">';

		// Label bottom
		if ($type!='button' && $type!='submit' && $label_position=='bottom')
			$output .= $label;
		
		// Close field container
		$output .= '</div>';
	
		return apply_filters('organic_beauty_shortcode_output', $output, 'trx_form_item', $atts, $content);
	}
	organic_beauty_require_shortcode('trx_form_item', 'organic_beauty_sc_form_item');
}

// AJAX Callback: Send contact form data
if ( !function_exists( 'organic_beauty_sc_form_send' ) ) {
	function organic_beauty_sc_form_send() {
	
		if ( !wp_verify_nonce( organic_beauty_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			die();
	
		$response = array('error'=>'');
		if (!($contact_email = organic_beauty_get_theme_option('contact_email')) && !($contact_email = organic_beauty_get_theme_option('admin_email'))) 
			$response['error'] = esc_html__('Unknown admin email!', 'organic-beauty');
		else {
			$type = organic_beauty_substr($_REQUEST['type'], 0, 7);
			parse_str($_POST['data'], $post_data);

			if (in_array($type, array('form_1', 'form_2'))) {
				$user_name	= organic_beauty_strshort($post_data['username'],	100);
				$user_email	= organic_beauty_strshort($post_data['email'],	100);
				$user_subj	= organic_beauty_strshort($post_data['subject'],	100);
				$user_msg	= organic_beauty_strshort($post_data['message'],	organic_beauty_get_theme_option('message_maxlength_contacts'));
		
				$subj = sprintf(esc_html__('Site %s - Contact form message from %s', 'organic-beauty'), get_bloginfo('site_name'), $user_name);
				$msg = "\n".esc_html__('Name:', 'organic-beauty')   .' '.esc_html($user_name)
					.  "\n".esc_html__('E-mail:', 'organic-beauty') .' '.esc_html($user_email)
					.  "\n".esc_html__('Subject:', 'organic-beauty').' '.esc_html($user_subj)
					.  "\n".esc_html__('Message:', 'organic-beauty').' '.esc_html($user_msg);

			} else {

				$subj = sprintf(esc_html__('Site %s - Custom form data', 'organic-beauty'), get_bloginfo('site_name'));
				$msg = '';
				if (is_array($post_data) && count($post_data) > 0) {
					foreach ($post_data as $k=>$v) {
						if (is_array($v) || is_object($v)) {
							$msg .= "\n{$k}: [";
							foreach ($v as $k1=>$v1)
								$msg .= ($k1 > 0 ? ' ,' : '') . $v1;
							$msg .= ']';
						} else
							$msg .= "\n{$k}: $v";
					}
				}
			}

			$msg .= "\n\n............. " . get_bloginfo('site_name') . " (" . esc_url(home_url('/')) . ") ............";

			$mail = organic_beauty_get_theme_option('mail_function');
			if (!@$mail($contact_email, $subj, apply_filters('organic_beauty_filter_form_send_message', $msg))) {
				$response['error'] = esc_html__('Error send message!', 'organic-beauty');
			}
		
			echo json_encode($response);
			die();
		}
	}
}

// Show additional fields in the form
if ( !function_exists( 'organic_beauty_sc_form_show_fields' ) ) {
	function organic_beauty_sc_form_show_fields($fields) {
		if (is_array($fields) && count($fields)>0) {
			foreach ($fields as $f) {
				if (in_array($f['type'], array('hidden', 'text'))) {
					echo '<input type="'.esc_attr($f['type']).'" name="'.esc_attr($f['name']).'" value="'.esc_attr($f['value']).'">';
				}
			}
		}
	}
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_form_reg_shortcodes' ) ) {
	//add_action('organic_beauty_action_shortcodes_list', 'organic_beauty_sc_form_reg_shortcodes');
	function organic_beauty_sc_form_reg_shortcodes() {
	
		$pages = organic_beauty_get_list_pages(false);

		organic_beauty_sc_map("trx_form", array(
			"title" => esc_html__("Form", 'organic-beauty'),
			"desc" => wp_kses_data( __("Insert form with specified style or with set of custom fields", 'organic-beauty') ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"title" => array(
					"title" => esc_html__("Title", 'organic-beauty'),
					"desc" => wp_kses_data( __("Title for the block", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"subtitle" => array(
					"title" => esc_html__("Subtitle", 'organic-beauty'),
					"desc" => wp_kses_data( __("Subtitle for the block", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"description" => array(
					"title" => esc_html__("Description", 'organic-beauty'),
					"desc" => wp_kses_data( __("Short description for the block", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"style" => array(
					"title" => esc_html__("Style", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select style of the form (if 'style' is not equal 'Custom Form' - all tabs 'Field #' are ignored!)", 'organic-beauty') ),
					"divider" => true,
					"value" => 'form_custom',
					"options" => organic_beauty_get_sc_param('forms'),
					"type" => "checklist"
				), 
				"scheme" => array(
					"title" => esc_html__("Color scheme", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select color scheme for this block", 'organic-beauty') ),
					"divider" => true,
					"value" => "",
					"type" => "checklist",
					"options" => organic_beauty_get_sc_param('schemes')
				),
				"action" => array(
					"title" => esc_html__("Action", 'organic-beauty'),
					"desc" => wp_kses_data( __("Contact form action (URL to handle form data). If empty - use internal action", 'organic-beauty') ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"return_page" => array(
					"title" => esc_html__("Page after submit", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select page to redirect after form submit", 'organic-beauty') ),
					"value" => "0",
					"type" => "select",
					"options" => $pages
				),
				"return_url" => array(
					"title" => esc_html__("URL to redirect", 'organic-beauty'),
					"desc" => wp_kses_data( __("or specify any URL to redirect after form submit. If both fields are empty - no navigate from current page after submission", 'organic-beauty') ),
					"value" => "",
					"type" => "text"
				),
				"align" => array(
					"title" => esc_html__("Align", 'organic-beauty'),
					"desc" => wp_kses_data( __("Select form alignment", 'organic-beauty') ),
					"divider" => true,
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => organic_beauty_get_sc_param('align')
				),
				"width" => organic_beauty_shortcodes_width(),
				"top" => organic_beauty_get_sc_param('top'),
				"bottom" => organic_beauty_get_sc_param('bottom'),
				"left" => organic_beauty_get_sc_param('left'),
				"right" => organic_beauty_get_sc_param('right'),
				"id" => organic_beauty_get_sc_param('id'),
				"class" => organic_beauty_get_sc_param('class'),
				"animation" => organic_beauty_get_sc_param('animation'),
				"css" => organic_beauty_get_sc_param('css')
			),
			"children" => array(
				"name" => "trx_form_item",
				"title" => esc_html__("Field", 'organic-beauty'),
				"desc" => wp_kses_data( __("Custom field", 'organic-beauty') ),
				"container" => false,
				"params" => array(
					"type" => array(
						"title" => esc_html__("Type", 'organic-beauty'),
						"desc" => wp_kses_data( __("Type of the custom field", 'organic-beauty') ),
						"value" => "text",
						"type" => "checklist",
						"dir" => "horizontal",
						"options" => organic_beauty_get_sc_param('field_types')
					), 
					"name" => array(
						"title" => esc_html__("Name", 'organic-beauty'),
						"desc" => wp_kses_data( __("Name of the custom field", 'organic-beauty') ),
						"value" => "",
						"type" => "text"
					),
					"value" => array(
						"title" => esc_html__("Default value", 'organic-beauty'),
						"desc" => wp_kses_data( __("Default value of the custom field", 'organic-beauty') ),
						"value" => "",
						"type" => "text"
					),
					"options" => array(
						"title" => esc_html__("Options", 'organic-beauty'),
						"desc" => wp_kses_data( __("Field options. For example: big=My daddy|middle=My brother|small=My little sister", 'organic-beauty') ),
						"dependency" => array(
							'type' => array('radio', 'checkbox', 'select')
						),
						"value" => "",
						"type" => "text"
					),
					"label" => array(
						"title" => esc_html__("Label", 'organic-beauty'),
						"desc" => wp_kses_data( __("Label for the custom field", 'organic-beauty') ),
						"value" => "",
						"type" => "text"
					),
					"label_position" => array(
						"title" => esc_html__("Label position", 'organic-beauty'),
						"desc" => wp_kses_data( __("Label position relative to the field", 'organic-beauty') ),
						"value" => "top",
						"type" => "checklist",
						"dir" => "horizontal",
						"options" => organic_beauty_get_sc_param('label_positions')
					), 
					"top" => organic_beauty_get_sc_param('top'),
					"bottom" => organic_beauty_get_sc_param('bottom'),
					"left" => organic_beauty_get_sc_param('left'),
					"right" => organic_beauty_get_sc_param('right'),
					"id" => organic_beauty_get_sc_param('id'),
					"class" => organic_beauty_get_sc_param('class'),
					"animation" => organic_beauty_get_sc_param('animation'),
					"css" => organic_beauty_get_sc_param('css')
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'organic_beauty_sc_form_reg_shortcodes_vc' ) ) {
	//add_action('organic_beauty_action_shortcodes_list_vc', 'organic_beauty_sc_form_reg_shortcodes_vc');
	function organic_beauty_sc_form_reg_shortcodes_vc() {

		$pages = organic_beauty_get_list_pages(false);
	
		vc_map( array(
			"base" => "trx_form",
			"name" => esc_html__("Form", 'organic-beauty'),
			"description" => wp_kses_data( __("Insert form with specefied style of with set of custom fields", 'organic-beauty') ),
			"category" => esc_html__('Content', 'organic-beauty'),
			'icon' => 'icon_trx_form',
			"class" => "trx_sc_collection trx_sc_form",
			"content_element" => true,
			"is_container" => true,
			"as_parent" => array('except' => 'trx_form'),
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Style", 'organic-beauty'),
					"description" => wp_kses_data( __("Select style of the form (if 'style' is not equal 'custom' - all tabs 'Field NN' are ignored!", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"std" => "form_custom",
					"value" => array_flip(organic_beauty_get_sc_param('forms')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "scheme",
					"heading" => esc_html__("Color scheme", 'organic-beauty'),
					"description" => wp_kses_data( __("Select color scheme for this block", 'organic-beauty') ),
					"class" => "",
					"value" => array_flip(organic_beauty_get_sc_param('schemes')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "action",
					"heading" => esc_html__("Action", 'organic-beauty'),
					"description" => wp_kses_data( __("Contact form action (URL to handle form data). If empty - use internal action", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "return_page",
					"heading" => esc_html__("Page after submit", 'organic-beauty'),
					"description" => wp_kses_data( __("Select page to redirect after form submit", 'organic-beauty') ),
					"class" => "",
					"std" => 0,
					"value" => array_flip($pages),
					"type" => "dropdown"
				),
				array(
					"param_name" => "return_url",
					"heading" => esc_html__("URL to redirect", 'organic-beauty'),
					"description" => wp_kses_data( __("or specify any URL to redirect after form submit. If both fields are empty - no navigate from current page after submission", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", 'organic-beauty'),
					"description" => wp_kses_data( __("Select form alignment", 'organic-beauty') ),
					"class" => "",
					"value" => array_flip(organic_beauty_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", 'organic-beauty'),
					"description" => wp_kses_data( __("Title for the block", 'organic-beauty') ),
					"admin_label" => true,
					"group" => esc_html__('Captions', 'organic-beauty'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "subtitle",
					"heading" => esc_html__("Subtitle", 'organic-beauty'),
					"description" => wp_kses_data( __("Subtitle for the block", 'organic-beauty') ),
					"group" => esc_html__('Captions', 'organic-beauty'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "description",
					"heading" => esc_html__("Description", 'organic-beauty'),
					"description" => wp_kses_data( __("Description for the block", 'organic-beauty') ),
					"group" => esc_html__('Captions', 'organic-beauty'),
					"class" => "",
					"value" => "",
					"type" => "textarea"
				),
				organic_beauty_get_vc_param('id'),
				organic_beauty_get_vc_param('class'),
				organic_beauty_get_vc_param('animation'),
				organic_beauty_get_vc_param('css'),
				organic_beauty_vc_width(),
				organic_beauty_get_vc_param('margin_top'),
				organic_beauty_get_vc_param('margin_bottom'),
				organic_beauty_get_vc_param('margin_left'),
				organic_beauty_get_vc_param('margin_right')
			)
		) );
		
		
		vc_map( array(
			"base" => "trx_form_item",
			"name" => esc_html__("Form item (custom field)", 'organic-beauty'),
			"description" => wp_kses_data( __("Custom field for the contact form", 'organic-beauty') ),
			"class" => "trx_sc_item trx_sc_form_item",
			'icon' => 'icon_trx_form_item',
			"show_settings_on_create" => true,
			"content_element" => true,
			"is_container" => false,
			"as_child" => array('only' => 'trx_form,trx_column_item'), // Use only|except attributes to limit parent (separate multiple values with comma)
			"params" => array(
				array(
					"param_name" => "type",
					"heading" => esc_html__("Type", 'organic-beauty'),
					"description" => wp_kses_data( __("Select type of the custom field", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(organic_beauty_get_sc_param('field_types')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "name",
					"heading" => esc_html__("Name", 'organic-beauty'),
					"description" => wp_kses_data( __("Name of the custom field", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "value",
					"heading" => esc_html__("Default value", 'organic-beauty'),
					"description" => wp_kses_data( __("Default value of the custom field", 'organic-beauty') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "options",
					"heading" => esc_html__("Options", 'organic-beauty'),
					"description" => wp_kses_data( __("Field options. For example: big=My daddy|middle=My brother|small=My little sister", 'organic-beauty') ),
					'dependency' => array(
						'element' => 'type',
						'value' => array('radio','checkbox','select')
					),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "label",
					"heading" => esc_html__("Label", 'organic-beauty'),
					"description" => wp_kses_data( __("Label for the custom field", 'organic-beauty') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "label_position",
					"heading" => esc_html__("Label position", 'organic-beauty'),
					"description" => wp_kses_data( __("Label position relative to the field", 'organic-beauty') ),
					"class" => "",
					"value" => array_flip(organic_beauty_get_sc_param('label_positions')),
					"type" => "dropdown"
				),
				organic_beauty_get_vc_param('id'),
				organic_beauty_get_vc_param('class'),
				organic_beauty_get_vc_param('animation'),
				organic_beauty_get_vc_param('css'),
				organic_beauty_get_vc_param('margin_top'),
				organic_beauty_get_vc_param('margin_bottom'),
				organic_beauty_get_vc_param('margin_left'),
				organic_beauty_get_vc_param('margin_right')
			)
		) );
		
		class WPBakeryShortCode_Trx_Form extends ORGANIC_BEAUTY_VC_ShortCodeCollection {}
		class WPBakeryShortCode_Trx_Form_Item extends ORGANIC_BEAUTY_VC_ShortCodeItem {}
	}
}
?>