jQuery(document).ready(function($){
	$('.woof_sid_widget .woof_container_product_cat .woof_list_checkbox li input[disabled]').each(function(){
		$(this).closest('li').remove();
	});
	$('.woof_sid_widget .woof_container_pwb-brand .woof_list_checkbox li input[disabled]').each(function(){
		$(this).closest('li').remove();
	});
	$('.woocommerce-product-gallery__wrapper [rel="magnific"]').removeAttr('rel');
	$('header .search_form').append('<input type="hidden" name="post_type" value="product" />');
	
	$(document).on('click', '.mega-toggle-blocks-right', function(e){
		if( $(window).width() <= 600 ) {
			$('.header_mobile .mask').removeClass('show');
			$('.header_mobile .side_wrap').removeClass('open');
		}
	});
	
	// $('#menu_mobile ul ul').parents('li').addClass('mega-menu-item-has-children');
	// $(document).on('click', '#menu_mobile .mega-menu-item-has-children > a', function(e){
		// if( $(window).width() <= 600 ) {
			// e.preventDefault();
			// $(this).parent().find(">ul").slideToggle();
		// }
	// });
});
jQuery(window).load(function(){
	$ = jQuery;
	$('.woocommerce-product-gallery__wrapper [rel="magnific"]').removeAttr('rel').removeClass('inited');
});