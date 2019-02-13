<?php
/* 
Class Name: Widget Brands List

Author: MagniumThemes
Author URI: http://magniumthemes.com/
Copyright MagniumThemes.com. All rights reserved
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class mgwoocommercebrands_list_widget extends WP_Widget {

	function mgwoocommercebrands_list_widget() {
		$widget_ops = array( 'classname'   => 'mgwoocommercebrands_class',
							 'description' => 'Display a list of your brands on your site. ' );
		parent::__construct( 'mgwoocommercebrands_list_widget', 'WooCommerce Brands list', $widget_ops );
	}

	function form( $instance ) {
		if(isset($instance['title'])) {
			$title  = $instance['title'];
		}
		else {
			$title  = 'Shop by brand';
		}

		if(!isset($instance['hide_empty'])) {
			$instance['hide_empty'] = 0;
		}

		if(!isset($instance['show_count'])) {
			$instance['show_count'] = 1;
		}
		
		?>
	
		<p><label><?php echo __( 'Title:', 'mgwoocommercebrands' ); ?></label>
			<input class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p><label><?php echo __( 'Hide empty brands:', 'mgwoocommercebrands' ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>">
				<option value='0' <?php if ( $instance['hide_empty'] == 0 ) {	echo "selected='selected'"; } ?>><?php echo __( 'No', 'mgwoocommercebrands' ); ?></option>
				<option value='1' <?php if ( $instance['hide_empty'] == 1 ) {	echo "selected='selected'"; } ?>><?php echo __( 'Yes', 'mgwoocommercebrands' ); ?></option>
			</select>
		</p>
		<p><label><?php echo __( 'Show product count (for Text brands display):', 'mgwoocommercebrands' ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'show_count' ); ?>">
				<option value='0' <?php if ( $instance['show_count'] == 0 ) {	echo "selected='selected'"; } ?>><?php echo __( 'No', 'mgwoocommercebrands' ); ?></option>
				<option value='1' <?php if ( $instance['show_count'] == 1 ) {	echo "selected='selected'"; } ?>><?php echo __( 'Yes', 'mgwoocommercebrands' ); ?></option>
			</select>
		</p>
	<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance               = $old_instance;
		$instance['hide_empty'] = esc_sql( $new_instance['hide_empty'] );
		$instance['show_count'] = esc_sql( $new_instance['show_count'] );
		$instance['title']      = sanitize_text_field( $new_instance['title'] );

		return $instance;
	}

	function widget( $args, $instance ) {
		extract( $args );
		
		$hide_empty = $instance['hide_empty'];
		$show_count = $instance['show_count'];

		if ( $instance['title'] ) {
			echo "<h3>{$instance['title']}</h3>";
		}?>
		<?php 
		
			echo '<div class="widget woocommerce widget_mgwoocommercebrands">';
	
			$brands_list = get_terms( 'product_brand', array(
				'orderby'    => 'name',
				'order'             => 'ASC',
				'hide_empty'	=> $hide_empty
			));

			if ( !empty( $brands_list ) && !is_wp_error( $brands_list ) ){
				
				echo "<ul>";

				foreach ( $brands_list as $brand_item ) {
					echo '<li>';	
					
					if($show_count == 1) {
						echo '<a href="'.get_term_link( $brand_item->slug, 'product_brand' ).'">'.$brand_item->name.'</a> <span class="count">('.$brand_item->count.')</span>';
					} else {
						echo '<a href="'.get_term_link( $brand_item->slug, 'product_brand' ).'">'.$brand_item->name.'</a>';
					}
					
					echo '</li>';
				}

				echo '</ul>';
			} 
			?>
		</div>
	<?php
	}
}

?>