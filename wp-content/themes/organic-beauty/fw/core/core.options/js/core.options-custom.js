/* global jQuery:false */

jQuery(document).ready(function() {
    if (typeof ORGANIC_BEAUTY_STORAGE == 'undefined') ORGANIC_BEAUTY_STORAGE = {};
	ORGANIC_BEAUTY_STORAGE['media_frame'] = null;
	ORGANIC_BEAUTY_STORAGE['media_link'] = '';
	jQuery('.organic_beauty_media_selector').on('click', function(e) {
		organic_beauty_show_media_manager(this);
		e.preventDefault();
		return false;
	});
});

function organic_beauty_show_media_manager(el) {
	"use strict";

	ORGANIC_BEAUTY_STORAGE['media_link'] = jQuery(el);
	// If the media frame already exists, reopen it.
	if ( ORGANIC_BEAUTY_STORAGE['media_frame'] ) {
		ORGANIC_BEAUTY_STORAGE['media_frame'].open();
		return false;
	}

	// Create the media frame.
	ORGANIC_BEAUTY_STORAGE['media_frame'] = wp.media({
		// Set the title of the modal.
		title: ORGANIC_BEAUTY_STORAGE['media_link'].data('choose'),
		// Tell the modal to show only images.
		library: {
			type: 'image'
		},
		// Multiple choise
		multiple: ORGANIC_BEAUTY_STORAGE['media_link'].data('multiple')===true ? 'add' : false,
		// Customize the submit button.
		button: {
			// Set the text of the button.
			text: ORGANIC_BEAUTY_STORAGE['media_link'].data('update'),
			// Tell the button not to close the modal, since we're
			// going to refresh the page when the image is selected.
			close: true
		}
	});

	// When an image is selected, run a callback.
	ORGANIC_BEAUTY_STORAGE['media_frame'].on( 'select', function(selection) {
		"use strict";
		// Grab the selected attachment.
		var field = jQuery("#"+ORGANIC_BEAUTY_STORAGE['media_link'].data('linked-field')).eq(0);
		var attachment = '';
		if (ORGANIC_BEAUTY_STORAGE['media_link'].data('multiple')===true) {
			ORGANIC_BEAUTY_STORAGE['media_frame'].state().get('selection').map( function( att ) {
				attachment += (attachment ? "\n" : "") + att.toJSON().url;
			});
			var val = field.val();
			attachment = val + (val ? "\n" : '') + attachment;
		} else {
			attachment = ORGANIC_BEAUTY_STORAGE['media_frame'].state().get('selection').first().toJSON().url;
		}
		field.val(attachment);
		if (field.siblings('img').length > 0) field.siblings('img').attr('src', attachment);
		field.trigger('change');
	});

	// Finally, open the modal.
	ORGANIC_BEAUTY_STORAGE['media_frame'].open();
	return false;
}
