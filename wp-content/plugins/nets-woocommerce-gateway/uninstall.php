<?php
/** * Uninstall - removes all nets options from DB when user deletes the plugin via WordPress backend. * @since 1 **/
if ( !defined('WP_UNINSTALL_PLUGIN') ) {    exit();}delete_option( 'woocommerce_nets_settings' );