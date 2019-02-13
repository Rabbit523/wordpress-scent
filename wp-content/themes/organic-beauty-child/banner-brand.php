<?php if( is_tax( 'pwb-brand' ) ) : ?>
<header class="woocommerce-products-header banner-brand">
	<div class="content_wrap">
		<div class="inner-banner">
			<?php
				global $wp_query;
				$tag = $wp_query->get_queried_object();
			?>
			<h1 class="woocommerce-products-header__title page-title"><?php echo $tag->name; ?></h1>
			
			<div class="brand-description">
				<?php if( get_term_meta($tag->term_id, 'pwb_brand_image', true) ) {
					echo '<div class="brand-logo"><img alt="" src="'. wp_get_attachment_url(get_term_meta($tag->term_id, 'pwb_brand_image', true)) .'" /></div>';
				} ?>
				<?php echo wpautop( $tag->description ); ?>
			</div>
		</div>
	</div>
</header>
<?php endif; ?>