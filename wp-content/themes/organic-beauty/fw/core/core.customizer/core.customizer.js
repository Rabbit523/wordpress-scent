// Organic Beauty Customizer script

jQuery(document).ready(function(){
	"use strict";

	// Add scheme name to each demo block
	jQuery('.to_demo_wrap').each(function(idx) {
		"use strict";
		var tab = jQuery(this).parents('.organic_beauty_options_tab_content');
		var tab_id = tab.attr('id');
		var scheme = tab_id.substring(4, tab_id.length-8);
		var info = jQuery(this).parents('.organic_beauty_options_field_info').attr('id', 'to_demo_scheme_'+scheme).attr('data-scheme', scheme).data('scheme', scheme);
		if (idx==0 && jQuery(this).find('.to_demo_pin').length==1 && organic_beauty_get_cookie('to_demo_fixed')==1) organic_beauty_options_fix_demo(info);
	});

	// Stick demo panel
	jQuery(document).on('click', '.organic_beauty_options_field_info .to_demo_pin', function(e) {
		organic_beauty_options_fix_demo(jQuery(this).parents('.organic_beauty_options_field_info'));
		e.preventDefault();
		return false;
	});	

	// Update demo on page load
	organic_beauty_options_decorate_demo();

	// Update demo on change fields
	jQuery('#partition_schemes_content').on('change', '.organic_beauty_options_input,.organic_beauty_options_field input[type="hidden"]', function(e) {
		"use strict";
		var tab = jQuery(this).parents('.organic_beauty_options_tab_content');
		var tab_id = tab.attr('id');
		var scheme = tab_id.substring(4, tab_id.length-8);
		organic_beauty_options_decorate_demo(scheme);
	});

	// Update demo on blur color fields
	jQuery('#partition_schemes_content').on('blur', '.organic_beauty_options_input_color', function(e) {
		"use strict";
		var tab = jQuery(this).parents('.organic_beauty_options_tab_content');
		var tab_id = tab.attr('id');
		var scheme = tab_id.substring(4, tab_id.length-8);
		organic_beauty_options_decorate_demo(scheme);
	});
	
	// Sort tabs with color schemes
	jQuery('#partition_schemes_content .organic_beauty_options_tab > ul').sortable({
		update: function(event, ui) {
			var tabs = "";
			ui.item.parents('.organic_beauty_options_tab').find('> ul > li').each(function() {
				tabs += (tabs ? ORGANIC_BEAUTY_STORAGE['to_delimiter'] : '') + jQuery(this).attr('id').substr(4);
			});
			ui.item.parents('.organic_beauty_options_tab').find('> .organic_beauty_options_schemes_order').val(tabs);
		}
	}).disableSelection().after('<input type="hidden" name="organic_beauty_options_schemes_order" class="organic_beauty_options_schemes_order" value="">');
});


// When switch tabs - update fixed demo block
function organic_beauty_init_hidden_elements(panel) {
	"use strict";
	var demo = panel.find('.organic_beauty_options_field_info[data-scheme]');
	if (demo.length == 0) return;
	var info2 = jQuery('.organic_beauty_options').find('>.organic_beauty_options_field_info');
	if (info2.length == 0) return;
	var scheme = demo.data('scheme');
	info2.attr('data-scheme', scheme).data('scheme', scheme);
	organic_beauty_options_decorate_demo(scheme);
}



// Delete current color scheme
function organic_beauty_options_action_scheme_delete(obj) {
	"use strict";
	var button = jQuery(obj);
	var tab_id = button.parents('.organic_beauty_options_tab_content').attr('id');
	var scheme = tab_id.substring(4, tab_id.length-8);
	organic_beauty_message_confirm(ORGANIC_BEAUTY_STORAGE['to_strings']['scheme_delete_confirm'], ORGANIC_BEAUTY_STORAGE['to_strings']['scheme_delete']+': "'+scheme+'"', function(btn) {
		"use strict";
		if (btn != 1) return;
		var data = {
			action: 'organic_beauty_options_scheme_delete',
			nonce: ORGANIC_BEAUTY_STORAGE['ajax_nonce'],
			scheme: scheme,
			order: button.parents('.organic_beauty_options_tab').find('> .organic_beauty_options_schemes_order').val()
		};
		setTimeout(function(){
			organic_beauty_message_info(ORGANIC_BEAUTY_STORAGE['to_strings']['recompile_styles'], ORGANIC_BEAUTY_STORAGE['to_strings']['wait'], 'spin3 animate-spin', 60000);
		}, 600);
		jQuery.post(ORGANIC_BEAUTY_STORAGE['ajax_url'], data, function(response) {
			"use strict";
			var rez = {};
			try {
				rez = JSON.parse(response);
			} catch (e) {
				rez = { error: ORGANIC_BEAUTY_STORAGE['ajax_error'] };
				console.log(response);
			}
			if (rez.error === '') {
				organic_beauty_message_success(ORGANIC_BEAUTY_STORAGE['to_strings']['scheme_delete_complete']+'<br>'+ORGANIC_BEAUTY_STORAGE['to_strings']['reload_page'], ORGANIC_BEAUTY_STORAGE['to_strings']['scheme_delete']);
				setTimeout(function() { location.reload(); }, 3000);
			} else {
				organic_beauty_message_warning(ORGANIC_BEAUTY_STORAGE['to_strings']['scheme_delete_failed']+':<br>'+rez.error, ORGANIC_BEAUTY_STORAGE['to_strings']['scheme_delete']);
			}
		});
		
	});
}


// Copy current color scheme
function organic_beauty_options_action_scheme_copy(obj) {
	"use strict";
	var button = jQuery(obj);
	var tab_id = button.parents('.organic_beauty_options_tab_content').attr('id');
	var scheme = tab_id.substring(4, tab_id.length-8);
	organic_beauty_message_confirm(ORGANIC_BEAUTY_STORAGE['to_strings']['scheme_copy_confirm'], ORGANIC_BEAUTY_STORAGE['to_strings']['scheme_copy']+': "'+scheme+'"', function(btn) {
		"use strict";
		if (btn != 1) return;
		var data = {
			action: 'organic_beauty_options_scheme_copy',
			nonce: ORGANIC_BEAUTY_STORAGE['ajax_nonce'],
			scheme: scheme,
			order: button.parents('.organic_beauty_options_tab').find('> .organic_beauty_options_schemes_order').val()
		};
		setTimeout(function(){
			organic_beauty_message_info(ORGANIC_BEAUTY_STORAGE['to_strings']['recompile_styles'], ORGANIC_BEAUTY_STORAGE['to_strings']['wait'], 'spin3 animate-spin', 60000);
		}, 600);
		jQuery.post(ORGANIC_BEAUTY_STORAGE['ajax_url'], data, function(response) {
			"use strict";
			var rez = {};
			try {
				rez = JSON.parse(response);
			} catch (e) {
				rez = { error: ORGANIC_BEAUTY_STORAGE['ajax_error'] };
				console.log(response);
			}
			if (rez.error === '') {
				organic_beauty_message_success(ORGANIC_BEAUTY_STORAGE['to_strings']['scheme_copy_complete']+'<br>'+ORGANIC_BEAUTY_STORAGE['to_strings']['reload_page'], ORGANIC_BEAUTY_STORAGE['to_strings']['scheme_copy']);
				setTimeout(function() { location.reload(); }, 3000);
			} else {
				organic_beauty_message_warning(ORGANIC_BEAUTY_STORAGE['to_strings']['scheme_copy_failed']+':<br>'+rez.error, ORGANIC_BEAUTY_STORAGE['to_strings']['scheme_copy']);
			}
		});
		
	});
}


// Fix/Unfix demo panel
function organic_beauty_options_fix_demo(info) {
	var info2 = info.parents('.organic_beauty_options').toggleClass('to_demo_fixed').find('>.organic_beauty_options_field_info');
	if (info2.length == 0) {
		var clonedObj = info.clone();
		clonedObj.removeAttr('id');
		info.parents('.organic_beauty_options').append(clonedObj);
		info2 = info.parents('.organic_beauty_options').find('>.organic_beauty_options_field_info');
	}
	organic_beauty_set_cookie('to_demo_fixed', jQuery('.organic_beauty_options').hasClass('to_demo_fixed') ? 1 : 0, 365);
}


// Decorate demo block
function organic_beauty_options_decorate_demo() {
	"use strict";
	var refresh_scheme = arguments[0] ? arguments[0] : '';

	jQuery('.to_demo_body').each(function() {
		"use strict";
		
		var scheme = jQuery(this).parents('.organic_beauty_options_field_info').data('scheme');
		if (refresh_scheme!='' && scheme!=refresh_scheme) return;
		
		var tab = jQuery('#tab_'+scheme+'_content');

		var clr = '';

		// Body
		jQuery(this).css({
			'border': '1px solid '+((clr=tab.find('[name="'+scheme+'-bd_color"]').val()) ? clr : 'transparent'),
			'backgroundColor': (clr=tab.find('[name="'+scheme+'-bg_color"]').val()) ? clr : 'transparent'
		});
		var img  = tab.find('[name="'+scheme+'-bg_image"]').val();
		var img2 = tab.find('[name="'+scheme+'-bg_image2"]').val();
		if (img || img2) {
			jQuery(this).css({
				'backgroundImage': (img2 ? 'url('+img2+')' : '') + (img ? (img2 ? ',' : '') + 'url('+img+')' : ''),
				'backgroundRepeat': (img2 ? tab.find('[name="'+scheme+'-bg_image2_repeat"]').val() : '') + (img ? (img2 ? ',' : '') + tab.find('[name="'+scheme+'-bg_image_repeat"]').val() : ''),
				'backgroundPosition': (img2 ? tab.find('[name="'+scheme+'-bg_image2_position"]').val() : '') + (img ? (img2 ? ',' : '') + tab.find('[name="'+scheme+'-bg_image_position"]').val() : '')
			});
		} else
			jQuery(this).css({'backgroundImage': 'none'});
		
		
		
		// Accent 2
		if (jQuery(this).find('.to_demo_accent2').length > 0) {
			jQuery(this).find('.to_demo_accent2').css({'color': (clr=tab.find('[name="'+scheme+'-accent2"]').val()) ? clr : 'inherit'});
			jQuery(this).find('.to_demo_accent2_hover').css({'color': (clr=tab.find('[name="'+scheme+'-accent2_hover"]').val()) ? clr : 'inherit'});
			jQuery(this).find('.to_demo_accent2_bg').css({'backgroundColor': (clr=tab.find('[name="'+scheme+'-accent2"]').val()) ? clr : 'transparent'});
			jQuery(this).find('.to_demo_accent2_hover_bg').css({'backgroundColor': (clr=tab.find('[name="'+scheme+'-accent2_hover"]').val()) ? clr : 'transparent'});
		}

		// Accent 3
		if (jQuery(this).find('.to_demo_accent3').length > 0) {
			jQuery(this).find('.to_demo_accent3').css({'color': (clr=tab.find('[name="'+scheme+'-accent3"]').val()) ? clr : 'inherit'});
			jQuery(this).find('.to_demo_accent3_hover').css({'color': (clr=tab.find('[name="'+scheme+'-accent3_hover"]').val()) ? clr : 'inherit'});
			jQuery(this).find('.to_demo_accent3_bg').css({'backgroundColor': (clr=tab.find('[name="'+scheme+'-accent3"]').val()) ? clr : 'transparent'});
			jQuery(this).find('.to_demo_accent3_hover_bg').css({'backgroundColor': (clr=tab.find('[name="'+scheme+'-accent3_hover"]').val()) ? clr : 'transparent'});
		}
		
		
		
		// Headers
		jQuery(this).find('.to_demo_header').css({'color': (clr=tab.find('[name="'+scheme+'-text_dark"]').val()) ? clr : 'inherit'});
		jQuery(this).find('.to_demo_header_link').css({'color': (clr=tab.find('[name="'+scheme+'-text_dark"]').val()) ? clr : 'inherit'});
		jQuery(this).find('.to_demo_header_hover').css({'color': (clr=tab.find('[name="'+scheme+'-text_hover"]').val()) ? clr : 'inherit'});
		
		// Text and links
		jQuery(this).find('.to_demo_text').css({'color': (clr=tab.find('[name="'+scheme+'-text"]').val()) ? clr : 'inherit'});
		jQuery(this).find('.to_demo_info').css({'color': (clr=tab.find('[name="'+scheme+'-text_light"]').val()) ? clr : 'inherit'});
		jQuery(this).find('.to_demo_text_link,.to_demo_info_link').css({'color': (clr=tab.find('[name="'+scheme+'-text_link"]').val()) ? clr : 'inherit'});
		jQuery(this).find('.to_demo_text_hover,.to_demo_info_hover').css({'color': (clr=tab.find('[name="'+scheme+'-text_hover"]').val()) ? clr : 'inherit'});
		jQuery(this).find('.to_demo_text_link_bg').css({'backgroundColor': (clr=tab.find('[name="'+scheme+'-text_link"]').val()) ? clr : 'transparent'});
		jQuery(this).find('.to_demo_text_hover_bg').css({'backgroundColor': (clr=tab.find('[name="'+scheme+'-text_hover"]').val()) ? clr : 'transparent'});
		
		// Inverse
		jQuery(this).find('.to_demo_inverse_text').css({'color': (clr=tab.find('[name="'+scheme+'-inverse_text"]').val()) ? clr : 'inherit'});
		jQuery(this).find('.to_demo_inverse_light').css({'color': (clr=tab.find('[name="'+scheme+'-inverse_light"]').val()) ? clr : 'inherit'});
		jQuery(this).find('.to_demo_inverse_dark').css({'color': (clr=tab.find('[name="'+scheme+'-inverse_dark"]').val()) ? clr : 'inherit'});
		jQuery(this).find('.to_demo_inverse_link').css({'color': (clr=tab.find('[name="'+scheme+'-inverse_link"]').val()) ? clr : 'inherit'});
		jQuery(this).find('.to_demo_inverse_hover').css({'color': (clr=tab.find('[name="'+scheme+'-inverse_hover"]').val()) ? clr : 'inherit'});
		
		// Alternative blocks: highlight blocks and form fields
		jQuery(this).find('.to_demo_alter_text').css({'color': (clr=tab.find('[name="'+scheme+'-alter_text"]').val()) ? clr : 'inherit'});
		jQuery(this).find('.to_demo_alter_info').css({'color': (clr=tab.find('[name="'+scheme+'-alter_light"]').val()) ? clr : 'inherit'});
		jQuery(this).find('.to_demo_alter_header').css({'color': (clr=tab.find('[name="'+scheme+'-alter_dark"]').val()) ? clr : 'inherit'});
		jQuery(this).find('.to_demo_alter_link').css({'color': (clr=tab.find('[name="'+scheme+'-alter_link"]').val()) ? clr : 'inherit'});
		jQuery(this).find('.to_demo_alter_hover').css({'color': (clr=tab.find('[name="'+scheme+'-alter_hover"]').val()) ? clr : 'inherit'});

		// Highlight block and submenu
		jQuery(this).find('.to_demo_alter_block').css({
			'border': '1px solid '+((clr=tab.find('[name="'+scheme+'-alter_bd_color"]').val()) ? clr : 'transparent'),
			'backgroundColor': (clr=tab.find('[name="'+scheme+'-alter_bg_color"]').val()) ? clr : 'transparent'
		});

		// Field
		jQuery(this).find('.to_demo_field').css({
			'border': '1px solid '+((clr=tab.find('[name="'+scheme+'-input_bd_color"]').val()) ? clr : 'transparent'),
			'backgroundColor': ((clr=tab.find('[name="'+scheme+'-input_bg_color"]').val()) ? clr : 'transparent'),
			'color': (clr=tab.find('[name="'+scheme+'-input_text"]').val()) ? clr : 'inherit'
		});

		// Focused field
		jQuery(this).find('.to_demo_field_focused').css({
			'border': '1px solid '+((clr=tab.find('[name="'+scheme+'-input_bd_hover"]').val()) ? clr : 'transparent'),
			'backgroundColor': (clr=tab.find('[name="'+scheme+'-input_bg_hover"]').val()) ? clr : 'transparent',
			'color': (clr=tab.find('[name="'+scheme+'-input_dark"]').val()) ? clr : 'inherit'
		});

	});
}
