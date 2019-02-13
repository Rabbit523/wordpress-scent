<?php
/*
 * Plugin Name: WC Netaxept Payment Gateway + Vipps Instant Checkout
 * Plugin URI: https://www.nettpilot.no/produkt/nets-betalingsmodul-for-wordpress-woocommerce/
 * Description: Extends WooCommerce. Provides a <a href="https://shop.nets.eu" target="_blank"> NETS Netaxept</a> payment gateway for WooCommerce.
 * Version: 0.6.1
 * Author: Nettpilot
 * Author URI: http://nettpilot.no/
 * WC requires at least: 3.0.0
 * WC tested up to: 3.2.0
 */
/* Copyright 2017 Nettpilot */



define('NETS_PLUGIN_DIR_URL',plugin_dir_url(__FILE__));

$gateways = [
    "WC_nets_nets",
    "WC_nets_vipps"
];

function add_nets_gateways($methods) {
    global $gateways;
    return array_merge($methods, $gateways);
}

// INIT GATEWAY -------------------------------------------------------------------------------
add_action('plugins_loaded', 'init_nets_gateway', 0);

function init_nets_gateway() {
    global $gateways;

    if (!class_exists('WC_Payment_Gateway')) return;


    load_plugin_textdomain('woocommerce-gateway-nets', false, dirname( plugin_basename( __FILE__ ) ).'/languages/');


    // ADD ALL GATEWAYS -------------------------------------------------------------------------------
    foreach ($gateways as $gateway) {

        require_once('includes/'.$gateway.'.php');

        // ORDER COMPLETE HOOK
        add_action ('woocommerce_order_status_completed', $gateway.'::payment_complete_hook');

        // GATEWAY CHECKOUT ICONS
        add_filter( 'woocommerce_gateway_icon', $gateway.'::nets_gateway_icon', 10, 2 );

    }

    add_filter('woocommerce_payment_gateways', 'add_nets_gateways');


    /*
    # MAIN NETS -------------------------------------------------------------------------------
    require_once('includes/WC_nets.php');

    function add_nets_gateway($methods) {
        $methods[] = 'WC_nets';
        return $methods;
    }
    add_filter('woocommerce_payment_gateways', 'add_nets_gateway');


    // ORDER COMPLETE HOOK
    add_action ('woocommerce_order_status_completed', 'WC_nets::payment_complete_hook');


    // GATEWAY CHECKOUT ICONS
    add_filter( 'woocommerce_gateway_icon', 'WC_nets::nets_gateway_icon', 10, 2 );
    */
}


// PLUGIN OPTIONS -------------------------------------------------------------------------------

add_action('admin_menu', function() {
    add_menu_page(  __('Nets Netaxept Settings','woocommerce-gateway-nets'),
                    __('Nets Netaxept','woocommerce-gateway-nets'),
                    'manage_options',
                    'woocommerce-gateway-nets',
                    'plugin_options',
                    NETS_PLUGIN_DIR_URL.'/images/mini-admin.png');
});

function plugin_options() {
    if (!current_user_can('manage_options')) {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    ?>
    <form method="POST" action="options.php">
        <?php
        echo "<h1>" . __( 'Nets Netaxept settings', 'woocommerce-gateway-nets' ) . "</h1>";
        echo "<p>" . __( 'Settings for all the Nets Netaxept payment gateways.', 'woocommerce-gateway-nets' ) . "</p>";
        settings_fields( 'woocommerce-gateway-nets' );	//pass slug name of page, also referred
        //to in Settings API as option group name
        do_settings_sections( 'woocommerce-gateway-nets' ); 	//pass slug name of page
        submit_button();
        ?>
    </form>
    <?php
}



add_action( 'admin_init', function() {
    add_settings_section(   'nets_general_settings',
                            __('General Settings','woocommerce-gateway-nets'),
                            'nets_general_settings',
                            'woocommerce-gateway-nets'
    );
    add_settings_section(   'nets_live_settings',
                            __('Live settings','woocommerce-gateway-nets'),
                            'nets_live_settings',
                            'woocommerce-gateway-nets'
    );
    add_settings_section(   'nets_test_settings',
                            __('Test settings','woocommerce-gateway-nets'),
                            'nets_test_settings',
                            'woocommerce-gateway-nets'
    );


    // General settings
    add_settings_field( 'nets_language',
                        __('Language','woocommerce-gateway-nets'),
                        'nets_language',
                        'woocommerce-gateway-nets',
                        'nets_general_settings'
    );
    add_settings_field( 'nets_easy',
                        __('Easy payment','woocommerce-gateway-nets'),
                        'nets_easy',
                        'woocommerce-gateway-nets',
                        'nets_general_settings'
    );
    add_settings_field( 'nets_recurring',
                        __('Recurring payment','woocommerce-gateway-nets'),
                        'nets_recurring',
                        'woocommerce-gateway-nets',
                        'nets_general_settings'
    );
    add_settings_field( 'nets_debug',
                        __('Debug mode','woocommerce-gateway-nets'),
                        'nets_debug',
                        'woocommerce-gateway-nets',
                        'nets_general_settings'
    );

    // Live settings
    add_settings_field( 'nets_live_id',
                        __('Merchant ID','woocommerce-gateway-nets'),
                        'nets_live_id',
                        'woocommerce-gateway-nets',
                        'nets_live_settings'
    );
    add_settings_field( 'nets_live_token',
                        __('Token','woocommerce-gateway-nets'),
                        'nets_live_token',
                        'woocommerce-gateway-nets',
                        'nets_live_settings'
    );

    // Test settings
    add_settings_field( 'nets_test_mode',
                        __('Test mode','woocommerce-gateway-nets'),
                        'nets_test_mode',
                        'woocommerce-gateway-nets',
                        'nets_test_settings'
    );
    add_settings_field( 'nets_test_id',
                        __('Merchant ID','woocommerce-gateway-nets'),
                        'nets_test_id',
                        'woocommerce-gateway-nets',
                        'nets_test_settings'
    );
    add_settings_field( 'nets_test_token',
                        __('Token','woocommerce-gateway-nets'),
                        'nets_test_token',
                        'woocommerce-gateway-nets',
                        'nets_test_settings'
    );


    register_setting( 'woocommerce-gateway-nets', 'nets_language' );
    register_setting( 'woocommerce-gateway-nets', 'nets_easy' );
    register_setting( 'woocommerce-gateway-nets', 'nets_recurring' );
    register_setting( 'woocommerce-gateway-nets', 'nets_debug' );

    register_setting( 'woocommerce-gateway-nets', 'nets_live_id' );
    register_setting( 'woocommerce-gateway-nets', 'nets_live_token' );
    register_setting( 'woocommerce-gateway-nets', 'nets_test_mode' );
    register_setting( 'woocommerce-gateway-nets', 'nets_test_id' );
    register_setting( 'woocommerce-gateway-nets', 'nets_test_token' );
});


function nets_live_settings() {
    echo '<p>'. __('Settings for a live webshop','woocommerce-gateway-nets') .'</p>';
}
function nets_test_settings(){
    echo '<p>'. __('Settings for a shop in test/being built','woocommerce-gateway-nets') .'</p>';
}
function nets_general_settings(){
    echo '<p>'. __('General settings','woocommerce-gateway-nets') .'</p>';
}

function nets_language() {
    echo '<select name="nets_language" id="nets_language">
            <option value="en_GB" '.selected(get_option('nets_language'), "en_GB",false).'>English</option>
            <option value="da_DK" '.selected(get_option('nets_language'), "da_DK",false).'>Danish</option>
            <option value="de_DE" '.selected(get_option('nets_language'), "de_DE",false).'>German</option>
            <option value="fi_FI" '.selected(get_option('nets_language'), "fi_FI",false).'>Finnish</option>
            <option value="ru_RU" '.selected(get_option('nets_language'), "ru_RU",false).'>Russian</option>
            <option value="no_NO" '.selected(get_option('nets_language'), "no_NO",false).'>Norwegian</option>
            <option value="sv_SE" '.selected(get_option('nets_language'), "sv_SE",false).'>Swedish</option>
        </select> <p>Set the language in which the page will be opened when the customer is redirected to nets.</p>';
}
function nets_easy() {
    echo '<input name="nets_easy" id="nets_easy" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'nets_easy' ), false ) . ' /> Do you have an easy payment agreement?';
}
function nets_recurring() {
    echo '<input name="nets_recurring" id="nets_recurring" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'nets_recurring' ), false ) . ' /> Do you have an recurring payment agreement?';
}
function nets_debug() {
    echo '<input name="nets_debug" id="nets_debug" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'nets_debug' ), false ) . ' />  Enable debug mode logging (<a href="/wp-admin/admin.php?page=wc-status&tab=logs">Show logs</a>)';
}


function nets_live_id() {
    echo '<input name="nets_live_id" id="nets_live_id" type="text" value="'.get_option( 'nets_live_id' ).'" class="code" /> <p>Please enter your nets Merchant ID; this is needed in order to take payment.<p/>';
}
function nets_live_token() {
    echo '<input name="nets_live_token" id="nets_live_token" type="text" value="'.get_option( 'nets_live_token' ).'" class="code" /> <p>Please enter your Nets Token.<p/>';
}

function nets_test_mode() {
    echo '<input name="nets_test_mode" id="nets_test_mode" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'nets_test_mode' ), false ) . ' /> Enable nets Test Mode.';
}
function nets_test_id() {
    echo '<input name="nets_test_id" id="nets_test_id" type="text" value="'.get_option( 'nets_test_id' ).'" class="code" /> <p>Please enter your nets Test Merchant ID; this is needed in order to use nets\' test mode.<p/>';
}
function nets_test_token() {
    echo '<input name="nets_test_token" id="nets_test_token" type="text" value="'.get_option( 'nets_test_token' ).'" class="code" /> <p>Please enter your Nets Test Token.<p/>';
}
















// NETS CALLBACK -------------------------------------------------------------------------------

/*
//Add the callback url for nets API
function nets_callback_url_init() {
    add_feed('nets-callback-url', 'nets_callback');
}
add_action('init', 'nets_callback_url_init');

//Filter the type, this hook wil set the correct HTTP header for Content-type.
function nets_callback_url_content_type( $content_type, $type ) {

    if ( 'nets-callback-url' === $type ) return feed_content_type( 'rss2' );
    return $content_type;
}
add_filter( 'feed_content_type', 'nets_callback_url_content_type', 10, 2 );


//When payment is completed execute this function through the nets callback url. This function will get the necessary info needed to possibly complete an order
function nets_callback() {
    $input = file_get_contents('php://input'); // input from nets
    $data = json_decode( $input, true ); // turn into json

    require_once('includes/WC_nets.php');

    $nets = new WC_nets();
    $nets->log->add('nets', 'callback: '.var_export($_GET, true)); // Add to the nets log
    $nets->nets_callback($_GET);
}
*/
