<?php
/**
 * Plugin Name: WooCommerce Search by Product SKU
 * Plugin URI: https://wordpress.org/plugins/woocommerce-search-by-product-sku
 * Description: Makes it possible to search by SKU on both the front end and back end for WooCommerce.
 * Version: 1.0
 * Author: Clayton Kriesel [Three Remain Production]
 * Author URI: http://www.threeremainproductions.com
 * License: GPL2
 */

/**
 * Search by SKU or ID for products. Adapted from code by BenIrvin (Admin Search by ID)
 *
 * @access public
 * @param mixed $wp
 * @return void
 */
function tr_woocommerce_admin_product_search( $wp ) {
    global $pagenow, $wpdb;

    if( 'edit.php' != $pagenow ) return;
    if( !isset( $wp->query_vars['s'] ) ) return;
    if ($wp->query_vars['post_type']!='product') return;

    if( '#' == substr( $wp->query_vars['s'], 0, 1 ) ) :

        $id = absint( substr( $wp->query_vars['s'], 1 ) );

        if( !$id ) return;

        unset( $wp->query_vars['s'] );
        $wp->query_vars['p'] = $id;

    elseif( 'SKU:' == strtoupper( substr( $wp->query_vars['s'], 0, 4 ) ) ) :

        $sku = trim( substr( $wp->query_vars['s'], 4 ) );

        if( !$sku ) return;

        $ids = $wpdb->get_col( 'SELECT post_id FROM ' . $wpdb->postmeta . ' WHERE meta_key="_sku" AND meta_value LIKE "%' . $sku . '%";' );

        if ( ! $ids ) return;

        unset( $wp->query_vars['s'] );
        $wp->query_vars['post__in'] = $ids;
        $wp->query_vars['sku'] = $sku;

    endif;
}

/**
 * Label for the search by ID/SKU feature
 *
 * @access public
 * @param mixed $query
 * @return void
 */
function tr_woocommerce_admin_product_search_label($query) {
    global $pagenow, $typenow, $wp;

    if ( 'edit.php' != $pagenow ) return $query;
    if ( $typenow!='product' ) return $query;

    $s = get_query_var( 's' );
    if ($s) return $query;

    $sku = get_query_var( 'sku' );
    if($sku) {
        $post_type = get_post_type_object($wp->query_vars['post_type']);
        return sprintf(__( '[%s with SKU of %s]', 'woocommerce' ), $post_type->labels->singular_name, $sku);
    }

    $p = get_query_var( 'p' );
    if ($p) {
        $post_type = get_post_type_object($wp->query_vars['post_type']);
        return sprintf(__( '[%s with ID of %d]', 'woocommerce' ), $post_type->labels->singular_name, $p);
    }

    return $query;
}

if ( is_admin() ) {
    add_action( 'parse_request', 'tr_woocommerce_admin_product_search' );
    add_filter( 'get_search_query', 'tr_woocommerce_admin_product_search_label' );
}

//Helps search by SKU better in WooCommerce
function tr_sku_search_helper($wp){
    global $wpdb;

    //Check to see if query is requested
    if( !isset( $wp->query['s'] ) || !isset( $wp->query['post_type'] ) || $wp->query['post_type'] != 'product') return;
    $sku = $wp->query['s'];
    $ids = $wpdb->get_col( $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value = %s;", $sku) );
    if ( ! $ids ) return;
    unset( $wp->query['s'] );
    unset( $wp->query_vars['s'] );
    $wp->query['post__in'] = array();
    foreach($ids as $id){
        $post = get_post($id);
        if($post->post_type == 'product_variation'){
            $wp->query['post__in'][] = $post->post_parent;
            $wp->query_vars['post__in'][] = $post->post_parent;
        } else {
            $wp->query_vars['post__in'][] = $post->ID;
        }
    }
}
add_filter( 'pre_get_posts', 'tr_sku_search_helper', 15 );
