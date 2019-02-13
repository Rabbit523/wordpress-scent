<?php

/**
* The plugin main class
**/


require_once __DIR__.'/Mobile_Detect.php';
require_once __DIR__.'/NetsAPIManager.php';
require_once __DIR__.'/NetsTerminal.php';
require_once __DIR__.'/WC_nets.php';


class WC_nets_nets extends WC_nets {
    public function __construct() {

        $this->id = "nets";
        $this->icon = NETS_PLUGIN_DIR_URL."images/logo.png";
        $this->has_fields = false;
        $this->log = new WC_Logger();

        $this->method_title = "Nets Netaxept";
        $this->method_description = "Accept payments through Nets Netaxept ";

        $this->possible_methods = [
            "visa",
            "mastercard",
            "vipps",
            "mobilepay",
            "klarna",
            "collector",

            "nets",
            "paypal",

            "maestro",
            "afterpay",
            "americanexpress",
            "applepay",
            "bankaxept",
            "bankacess",

            "dankort",
            "diners",
            "jcb",
            "samsungpay",
            "svea-webpay",
            "swish",
        ];


        parent::__construct();

    }

    // Show icons in checkout title
    public static function nets_gateway_icon( $icon, $id ) {
        if ($id == "nets") { // Filter Nets Gateway
            $nets = new WC_nets_nets();
            $icons = $nets->nets_icon_modify($icon);
            return $icons;
        }
        return $icon;
    }

    // Called from Woocommerce whe the status of an order is set as complete ------------------------------------------------------------------------
    public static function payment_complete_hook($order_id) {
        $nets 	= new WC_nets_nets();

        return $nets->complete_order_with_nets($order_id);
    }




}
