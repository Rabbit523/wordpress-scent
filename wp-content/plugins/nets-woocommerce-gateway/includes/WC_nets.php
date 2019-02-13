<?php

/**
* The plugin main class
**/


require_once __DIR__.'/Mobile_Detect.php';
require_once __DIR__.'/NetsAPIManager.php';
require_once __DIR__.'/NetsTerminal.php';


class WC_nets extends WC_Payment_Gateway {


    public function __construct() {


        $this->special_invoice_methods = [
            "GothiaInvoice", // AfterPay
            "GothiaInstallment", // AfterPay
            "Collector",
            "CollectorInstallment",
            "CollectorAccount",
            "HandelsbankenInvoice",
            "HandelsbankenInstallment",
            #"Klarna", // Klarna needs AUTH/CAPTURE or VERIFY
            #"KlarnaInstallment", // Klarna needs AUTH/CAPTURE or VERIFY
            "Lindorff"
        ];

        // Load form fields for admin panel
        $this->init_form_fields();


        // Load gateway settings
        $this->init_settings();

        $this->title 			        = (isset($this->settings['title'])) ? $this->settings['title'] : '';
        $this->description 			    = (isset($this->settings['description'])) ? $this->settings['description'] : '';
        $this->description_logo 		= (isset($this->settings['description_logo']) && $this->settings['description_logo'] == "yes") ? true : false;
        $this->title_logo               = (isset($this->settings['title_logo']) && $this->settings['title_logo'] == "yes") ? true : false;

        $this->capture_type         = (isset($this->settings['capture_type'])) ? $this->settings['capture_type'] : '';

        $this->methods = [];
        foreach ($this->possible_methods as $method) {
            $setting_string = "method_".$method;
            $this->methods[$method]  = (isset($this->settings[$setting_string]) && $this->settings[$setting_string] == "yes") ? true : false;
        }



        $this->language 		    = get_option( 'nets_language' );
        $this->easy_payment         = get_option( 'nets_easy' )  ? true : false;
        $this->recurring_payment    = get_option( 'nets_recurring' ) ? true : false;
        $this->debug_mode 			= get_option( 'nets_debug' ) ? true : false;

        $this->test_mode 			= get_option( 'nets_test_mode' ) ? true : false;
        // Set token and merchant_id depending on if test_mode is active:
        if (!$this->test_mode) { // Use live fields
            $this->merchant_id 		= get_option( 'nets_live_id' );
            $this->token            = get_option( 'nets_live_token' );
        }
        else { //Use test fields
            $this->merchant_id 		= get_option( 'nets_test_id' );
            $this->token 			= get_option( 'nets_test_token' );
        }
        $this->token = html_entity_decode($this->token);



        //TODO: Error message for missing plugin set






        //API Manager
        $this->api = new NetsAPIManager($this->test_mode);



        // Mobile detect
        $detect = new Mobile_Detect;
        $this->is_mobile =  ( $detect->isMobile() || $detect->isTablet() ) ? true : false;


        // Multiple currency support
        $this->selected_currency = get_woocommerce_currency();

        if ( !$this->is_valid_for_use() ) $this->enabled = false;
        else $this->enabled = (isset($this->settings['enabled']) && $this->settings['enabled'] == 'yes' ) ? 'yes' : 'no';


        // Subscription support
        $this->supports = array(
            'products',
            'subscriptions',
            'subscription_cancellation',
            'subscription_suspension',
            'subscription_reactivation',
            'subscription_amount_changes',
            'subscription_date_changes',
            'subscription_payment_method_change',
            'refunds'
        );


        // Callback
        $this->check_callback();

        // HOOKS

        // Settings hooks
        $version_comp = version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' );
        if ( $version_comp ) add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options' ] );
        else                 add_action( 'woocommerce_update_options_payment_gateways', [$this, 'process_admin_options' ] );


        // Subscription hooks
        add_action('scheduled_subscription_payment_'.$this->id, [$this, 'scheduled_subscription_payment_hook'], 10, 3);
        add_action('cancelled_subscription_'.$this->id, [$this, 'cancelled_subscription_hook'], 10, 2);
        add_action('woocommerce_subscriptions_changed_failing_payment_method_'.$this->id, [$this, 'update_failing_payment_method_hook'], 10, 2);


        // Flush rewrite rules on activation plugin
        register_activation_hook( __FILE__, 'nets_flush_rewrites' );
    }


    public function is_valid_for_use() {
        return in_array(get_woocommerce_currency() ,[ 'NOK', 'SEK', 'DKK', 'EUR', 'USD', 'GBP', 'AUD', 'THB']);
    }

    public function nets_flush_rewrites() {
        flush_rewrite_rules();
        $this->info('Nets flushed the rewrite rules');
    }


    public function init_form_fields() {
        $this->form_fields = [
            'enabled' => array(
                'title' => __('Enable/Disable', 'woocommerce-gateway-nets'),
                'type' => 'checkbox',
                'label' => __('Enable Nets Payment Gateway', 'woocommerce-gateway-nets'),
                'default' => 'yes'
            ),
            'title' => array(
                'title' => __('Title', 'woocommerce-gateway-nets'),
                'type' => 'text',
                'description' => __('This controls the title which the user sees during checkout.', 'woocommerce-gateway-nets'),
                'default' => $this->method_title
            ),
            'description' => array(
                'title' => __('Description', 'woocommerce-gateway-nets'),
                'type' => 'textarea',
                'description' => __('This controls the description which the user sees during checkout.', 'woocommerce-gateway-nets'),
                'default' => __("Pay via nets using credit card or bank transfer.", 'woocommerce-gateway-nets')
            ),

            'capture_type' => array(
                'title' => __('Capture', 'woocommerce-gateway-nets'),
                'type' => 'select',
                'options' => array(
                    'autocapture' => 'Nets autocapture',
                    'internalcapture' => 'Internal capture'
                ),
                'description' => __('<b>Nets autocapture</b>: Nets will charge the customer immediately. <br/><b>Internal capture</b>: The order is manually captured from WooCommerce admin ', 'woocommerce-gateway-nets'),
                'default' => 'internalcapture'
            ),


            'description_logo' => array(
                'title' => __('Display Logo With Description', 'woocommerce-gateway-nets'),
                'type' => 'checkbox',
                'label' => __('Display Gateway Logos on checkout Description', 'woocommerce-gateway-nets'),
                'default' => 'no'
            ),
            'title_logo' => array(
                'title' => __('Display Logo With Title', 'woocommerce-gateway-nets'),
                'type' => 'checkbox',
                'label' => __('Display Gateway Logos With Checkout Title', 'woocommerce-gateway-nets'),
                'default' => 'no'
            )

        ];
        foreach ($this->possible_methods as $method) {
            $setting_string = "method_".$method;
            $title = ucfirst($method);
            $this->form_fields[$setting_string] = [
                'title' => $title,
                'type' => 'checkbox',
                'label' => __('Show checkout logo for ', 'woocommerce-gateway-nets')."<b>".$title."</b>",
                'default' => 'no'
            ];
        }
    }







    // PROCESS PAYMENTS ====================================================================================================================================

    public function process_payment($order_id) {
        $this->debug("process_payment() - called for order ".$order_id);

        global $woocommerce;

        $order = wc_get_order( $order_id );

        if ( $this->merchant_id == "" || $this->token == "" ){
            $woocommerce->add_error(__('Nets is not configured properly, Please choose another payment method.','woocommerce-gateway-nets'));
            return false;
        }

        if ($register = $this->register_order_with_nets($order_id) ) {
            // Send user to terminal
            $terminal = new NetsTerminal($this->test_mode, $this->is_mobile, $this->merchant_id, $register->TransactionId);

            $terminal_url = $terminal->present(); // present terminal to user
            $this->debug('Sending user to terminal: ' . var_export($terminal_url, true));

            return array(
                'result' => 'success',
                'redirect' 	=> $terminal_url
            );
        }

    }


    function process_subscription_payment($order, $amount) {
        $this->debug("process_subscription_payment() - called for order ".$order->get_id());

        $register_data = $this->register_subscription_order_with_nets($order,$amount);

        $process = false;
        if ($register_data) {
            $transactionId = $register_data['TransactionId'];
            $process = $this->process_subscription_order_with_nets($order, $transactionId);
        }

        return $reg && $process;
    }

    public function process_refund( $order_id,  $amount = null,  $reason = '') {
        $this->debug("process_refund() - called for order ".$order_id);

        $refund = $this->refund_order_with_nets($order_id,$amount,$reason);

        return $refund;
    }






    // REGISTER PAYMENTS (Nets REGISTER) ====================================================================================================================================

    public function register_order_with_nets($order_id) {
        $this->debug("register_order_with_nets() - called for order ".$order_id);

        global $woocommerce;

        $order = wc_get_order($order_id);

        // Get goods list
        $goods = $this->get_goods_from_order($order);

        $data = [
            # AUTH
            "merchantId" => $this->merchant_id,
            "token" => $this->token,

            # REQUEST
            "serviceType" => "B",
            "transactionId" => null,
            "description" => null,

            # ORDER, R
            "orderNumber" => ltrim( $order->get_order_number(), '#'),
            "currencyCode" => $this->selected_currency,
            "amount" => $order->get_total() * 100,
            "goods" => $goods,

            # TERMINAL, O
            "orderDescription" => null,
            "language" => $this->language,
            "redirectUrl" => $order->get_checkout_payment_url() . "&" . http_build_query(["method" => $this->id]),
            "terminalSinglePage" => false,

            # CUSTOMER, O, only in nets admin
            "customerFirstName" => $order->get_billing_first_name(),
            "customerLastName" => $order->get_billing_last_name(),
            "customerAddress1" => $order->get_billing_address_1(),
            "customerPostcode" => $order->get_billing_postcode(),
            "customerTown" => $order->get_billing_city(),
            "customerCountry" =>ucfirst($order->get_billing_country()),
            "customerEmail" => $order->get_billing_email(),
            "customerPhoneNumber" => $order->get_billing_phone()
        ];

        if ( $this->method_list ) $data["paymentMethodActionList"] = json_encode($this->method_list);


        // Easy Payment
        if( $this->is_mobile && $this->easy_payment ) {

            $stored_pan_hash = get_post_meta((int)$order->user_id, 'nets_panhash', true);

            $data['recurringType'] = "S";

            if ($stored_pan_hash) { // No need to update
                $data['panHash'] = $stored_pan_hash;
                $data['updateStoredPaymentInfo'] = false;
            }
            else { // We need to register the customer pan hash
                $data['updateStoredPaymentInfo'] = true;
            }

            $this->debug("Register using easypayment");
        }

        // Recurring payment
        if(class_exists('WC_Subscriptions_Order') && WC_Subscriptions_Order::order_contains_subscription($order_id) && $this->recurring_payment){

            // Check if initial subscription payment is 0, change this to 1
            if( WC_Subscriptions_Order::get_total_initial_payment( $order ) == 0 ) $amount = 100;
            else $amount = WC_Subscriptions_Order::get_total_initial_payment( $order )*100;

            $product_id = null;						// Required
            $order_items = $order->get_items();		// Required

            // Loop through order items to find the subscription
            if(sizeof($order_items) > 0) {
                // Get the Subscription Product ID;
                foreach($order_items as $index => $item){
                    $s_period = $order->get_product_from_item($item)->subscription_period; 			// Required

                    // Check if Product contains Subscription Period and assign Product ID
                    if(!empty($s_period) || $s_period != ""){
                        $product_id = $order->get_product_from_item($item)->get_id();
                    }
                }

                // Set Subscription Variables
                $period			= WC_Subscriptions_Product::get_period($product_id);
                $length			= WC_Subscriptions_Product::get_length($product_id);
                $expiration		= WC_Subscriptions_Product::get_expiration_date($product_id);

                // Set the Nets Frequence Number
                if(!empty($period) || $period != 0){
                    switch($period) :
                        case 'day' :
                        $subPeriod = "0";
                        break;
                        case 'week' :
                        $subPeriod = "7";
                        break;
                        case 'month' :
                        $subPeriod = "28";
                        break;
                        case 'year' :
                        $subPeriod = "364";
                        break;
                    endswitch;
                }

                // Set expiration date
                if(!empty($expiration) || $expiration != 0){
                    $expDate = date('Ymd', strtotime($expiration));
                }
                else{
                    $expDate = (date('Y') + 50) . date('md');
                }

                if(!empty($length) || $length != 0){
                    $subLenght = $length;
                }
            }


            $data['recurringType'] = "R";
            $data['recurringFrequency'] = $subPeriod;
            $data['recurringExpiryDate'] = $expDate;


            $this->debug("Register using recurringpayment");
            $this->debug('Testing Period: ' . WC_Subscriptions_Product::get_period($product_id) . ' Correct Period: ' . $subPeriod);
            $this->debug('Testing Lenght: ' . WC_Subscriptions_Product::get_length($product_id) . ' Correct Length: ' . $subLenght);
            $this->debug('Testing Expiration: ' . WC_Subscriptions_Product::get_expiration_date($product_id) . ' Correct Format: ' . $expDate);
            $this->debug('Testing Paragraph: ' . WC_Subscriptions_Product::get_price_string($product_id));

        }


        // Call API
        try {
            $req = new  NetsRegisterRequest($data);

            $res = $this->api->send_request($req);

            $this->debug("Register request response: " . var_export($res, true));

            return $res;
        }
        catch (Exception $e) {
            $msg = __('Unable to authenticate merchant.','woocommerce-gateway-nets');

            if(isset($msg)) wc_add_notice( $msg, 'error' );
            $this->debug('Register request error: '.$e);

            return false;
        }
    }

    public function register_subscription_order_with_nets($order,$amount) {
        $this->debug("register_subscription_order_with_nets() - called for order ".$order->get_id());

        global $woocommerce;

        // Find panhash from order
        $stored_pan_hash = get_post_meta((int)$order->user_id, 'nets_panhash', true);

        // Get goods list
        $goods = $this->get_goods_from_order($order);


        $data = [
            # AUTH
            "merchantId" => $this->merchant_id,
            "token" => $this->token,

            # REQUEST
            "serviceType" => "C",
            "transactionId" => null,
            "description" => null,

            # ORDER, R
            "orderNumber" => ltrim( $order->get_order_number(), '#'),
            "currencyCode" => $this->selected_currency,
            "amount" => $order->get_total() * 100,
            "goods" => $goods,

            # ENVIRONMENT, O
            "environmentLanguage" => "PHP5",

            # TERMINAL, O
            "orderDescription" => null,
            "language" => $this->language,
            "redirectUrl" => $order->get_checkout_payment_url() . "&" . http_build_query(["method" => $this->id]),
            "terminalSinglePage" => false,

            # CUSTOMER, O, only in nets admin
            "customerNumber" => $order->get_customer_id(),
            "customerFirstName" => $order->get_billing_first_name(),
            "customerLastName" => $order->get_billing_last_name(),
            "customerAddress1" => $order->get_billing_address_1(),
            "customerPostcode" => $order->get_billing_postcode(),
            "customerTown" => $order->get_billing_city(),
            "customerCountry" =>strtoupper($order->get_billing_country()),

            # RECURRING
            "recurringType" => "R",
            "panHash" => $stored_pan_hash
        ];


        // Call API
        try {
            $req = new  NetsRegisterRequest($data);
            $res = $this->api->send_request($req);

            $this->debug("Register request response: " . var_export($res, true));

            return $res;
        }
        catch (Exception $e) {
            $order->add_order_note( sprintf(__('Error when processing recurring payment: %s.', 'woocommerce-gateway-nets'), $e) );

            return false;
        }

    }






    // FINISH PAYMENTS (Nets AUTH/SALE) ====================================================================================================================================


    public function process_order_with_nets($order, $transactionId, $operation) {
        $this->debug("process_order_with_nets() - called for order ".$order->get_id());

        try {
            $res = $this->api->send_request(new  NetsProcessRequest([
                "merchantId" => $this->merchant_id,
                "token" => $this->token,
                "operation" => $operation,
                "transactionId" => $transactionId,
                "description" => $operation." for order",
                "transactionAmount" => $order->get_total() * 100,
            ]));

            $this->debug("Process(".$operation.") request response: " . var_export($res, true));

            if($res->ResponseCode == "OK") return true;
            else return false;
        }
        catch (Exception $e) {
            $msg = __('Unable to process transaction through Nets.','woocommerce-gateway-nets');
            if(isset($msg)) wc_add_notice( $msg, 'error' );

            $this->debug("Process(".$operation.") request error: " . $e);
            return false; //Stop since process didn't work
        }

    }


    public function process_subscription_order_with_nets($order,$transactionId) {
        $this->debug("process_subscription_order_with_nets() - called for order ".$order->get_id());

        // Run query to obtain info on transaction
        try {
            $query_data = $this->api->send_request(new  NetsQueryRequest([
                "merchantId" => $this->merchant_id,
                "token" => $this->token,
                "transactionId" => $transactionId,
            ]));

            $this->debug("Query request response: " . var_export($query_data, true));
        }
        catch (Exception $e) {
            $msg = __('Unable to query nets.','woocommerce-gateway-nets');

            $this->debug("Query request error: " . $e);

            return; //Stop since query didn't work
        }


        $order = wc_get_order( $order_id );

        $method = $query_data->CardInformation->PaymentMethod;

        if ( in_array($method,$this->special_invoice_methods) ) {
            $order->add_order_note( sprintf(__('Nets transaction ID: %s.', 'woocommerce-gateway-nets'), $transactionId) );
            update_post_meta( $order->get_id(), 'nets_transaction_id', $transactionId);

            return true;
        }



        try {
            $res = $this->api->send_request(new  NetsProcessRequest([
                "merchantId" => $this->merchant_id,
                "token" => $this->token,
                "operation" => "SALE",
                "transactionId" => $transactionId,
                "description" => $operation." for order",
                "transactionAmount" => $order->get_total() * 100,
            ]));

            $this->debug("Process(SALE) request response: " . var_export($res, true));

            if($res->ResponseCode == "OK") {
                $order->add_order_note( sprintf(__('Nets transaction ID: %s.', 'woocommerce-gateway-nets'), $transactionId) );
                update_post_meta( $order->get_id(), 'nets_transaction_id', $transactionId);

                return true;
            }
        }
        catch (Exception $e) {
            $order->add_order_note( sprintf(__('Error when processing recurring payment: %s.', 'woocommerce-gateway-nets'), $e) );
            return false;
        }

    }






    // SUCCESFUL ORDER ====================================================================================================================================
    public function successful_order($order, $transactionId) {
        $this->debug("successful_order() - called for order ".$order->get_id());

        // Run query to obtain info on transaction
        try {
            $query_data = $this->api->send_request(new  NetsQueryRequest([
                "merchantId" => $this->merchant_id,
                "token" => $this->token,
                "transactionId" => $transactionId,
            ]));

            $this->debug("Query request response: " . var_export($query_data, true));

        }
        catch (Exception $e) {
            $msg = __('Unable to return information for order from nets.','woocommerce-gateway-nets');
            if(isset($msg)) wc_add_notice( $msg, 'error' );
            $this->debug("Query request error: " . $e);
            return;
        }

        //$order_id = $this->get_order_id($query_data->OrderInformation->OrderNumber);
        $order_id = $order->get_id();

        // Store TransactionId
        update_post_meta( $order_id, 'nets_transaction_id', $transactionId);
        $this->debug("Stored Nets TransactionId: " . $transactionId);

        // Store Panhash
        if( isset($query_data->CardInformation->PanHash) ){
            $panhash = $query_data->CardInformation->PanHash;
            update_post_meta( $order_id, 'nets_panhash', $panhash);
            $order->add_order_note(__('NETS customer id (panhash): ', 'woocommerce-gateway-nets') . $panhash);
            $this->debug("Stored Nets panhash: " . $panhash);
        }

        // Store payment method
        if( isset($query_data->CardInformation->PaymentMethod) ){
            if (isset($query_data->Wallet)) $method = $wallet = $query_data->Wallet->Issuer."/".$method;
            else $method = $query_data->CardInformation->PaymentMethod;

            $order->add_order_note(sprintf( __('Nets: Payment through %s was a success! ', 'woocommerce-gateway-nets'), $method ));
            update_post_meta( $order_id, 'nets_payment_method_detail', $method);
        }


        //Complete order
        $order->payment_complete();
        $this->debug("payment_complete() called on order:  " . $order_id);

        // Force order completion upon autocapture with nets
        if ($this->capture_type == 'autocapture') {
            $this->debug("Force complete (autocapture on)!");
            $order->update_status('completed');
        }


        // Empty cart
        $cart = new WC_Cart();
        $cart->empty_cart();
        $cart = NULL;


        $this->debug("Sending user to:  " . $this->get_return_url($order));

        header('Location:' . $this->get_return_url($order));
    }



    // COMPLETE ORDER ====================================================================================================================================
    public function complete_order_with_nets($order_id) {
        $this->debug('complete_order_with_nets() - called for: '.$order_id);

        //if ($this->capture_type == 'autocapture' ) return; // Jump out if autocapture through nets is activated

        //Retrieve the transactionId
        $transactionId = get_post_meta($order_id, "nets_transaction_id",true);


        if ($transactionId != '') {
            // Run query to obtain info on transaction
            try {
                $query_data = $this->api->send_request(new  NetsQueryRequest([
                    "merchantId" => $this->merchant_id,
                    "token" => $this->token,
                    "transactionId" => $transactionId,
                ]));

                $this->debug("Query request response: " . var_export($query_data, true));
            }
            catch (Exception $e) {
                $msg = __('Unable to query nets.','woocommerce-gateway-nets');


                $this->debug("Query request error: " . $e);

                return; //Stop since query didn't work
            }



            $order = wc_get_order( $order_id );

            # Return if full amount is already captured
            if ( $query_data->Summary->AmountCaptured >= $order->get_total() * 100 ){
                $this->debug("Amount captured:".$query_data->Summary->AmountCaptured.", OrderTotal: " . $order->get_total());
                return true;
            }

            $method = $query_data->CardInformation->PaymentMethod;
            if ( in_array($method,$this->special_invoice_methods) ) return true;


            try {
                $process_data = $this->api->send_request(new  NetsProcessRequest([
                    "merchantId" => $this->merchant_id,
                    "token" => $this->token,
                    "operation" => "CAPTURE",
                    "transactionId" => $transactionId,
                    "description" => "Manual CAPTURE for order from WC admin",
                    //"transactionAmount" => $order->get_total() * 100,
                ]));

                $this->debug("Process(CAPTURE) request response: " . var_export($process_data, true));

                if($process_data->ResponseCode == "OK") {
                    $order->add_order_note(__('Nets: Payment captured', 'woocommerce-gateway-nets'));
                    return true;
                }
                else return false;
            }
            catch (Exception $e) {
                $order->add_order_note(__('Nets: Failed to capture payment', 'woocommerce-gateway-nets'));
                $this->debug("Process(CAPTURE) request error: " . $e);

                return false; //Stop since query didn't work
            }

        }
        else return false;
    }





    // REFUND ORDER ====================================================================================================================================
    public function refund_order_with_nets($order_id, $amount, $reason) {
        $this->debug("refund_order_with_nets() - called for order ".$order_id);

        $transactionId = get_post_meta($order_id, "nets_transaction_id",true);

        if ($transactionId == "") {
            $this->debug("No transactionId for order ".$order->get_id()." cannot complete refund");
        }

        // Run query to obtain info on transaction
        try {
            $query_data = $this->api->send_request(new  NetsQueryRequest([
                "merchantId" => $this->merchant_id,
                "token" => $this->token,
                "transactionId" => $transactionId,
            ]));

            $this->debug("Query request response: " . var_export($query_data, true));
        }
        catch (Exception $e) {

            $this->debug("Query request error: " . $e);

            return false; //Stop since query didn't work
        }




        // Set up WC Order object and pass for Nets Process call
        $order = wc_get_order($order_id);


        //Figure out if we are doing an ANNUL or a CREDIT
        if ( (int)$query_data->Summary->AmountCaptured == 0 ){
            $operation = "ANNUL";
            // Partial refunds are not accepted
            if ($query_data->OrderInformation->Amount != $amount*100) {
                $this->debug("Cannot do parial voiding of transaction: ".($amount*100)." of ".$query_data->OrderInformation->Amount );
                return false;
            }
        }
        else $operation = "CREDIT";

        $this->debug("Refund operation: " . $operation);

        try {
            $process_data = $this->api->send_request(new  NetsProcessRequest([
                "merchantId" => $this->merchant_id,
                "token" => $this->token,
                "operation" => $operation,
                "transactionId" => $transactionId,
                "description" => $operation." for order",
                "transactionAmount" => $amount * 100,
            ]));

            $this->debug("Process(".$operation.") request response: " . var_export($process_data, true));

            if($process_data->ResponseCode == "OK") {
                if ($operation == "ANNUL"){
                    $order->add_order_note(__('Nets: Payment cancelled', 'woocommerce-gateway-nets'));
                    $order->update_status('cancelled'); // Cancel order if voided
                }
                else {
                    $order->add_order_note(sprintf(__('Nets: %d refunded', 'woocommerce-gateway-nets'),$amount));
                }



                return true;
            }
        }
        catch (Exception $e) {
            $this->debug("Process(".$operation.") request error: " . $e);

            return false; //Stop since process didn't work
        }
    }



















    // HELPERS ====================================================================================================================================

    /**
    * Get the order ID. Check to see if SON and SONP is enabled and
    *
    * @global type $wc_seq_order_number
    * @global type $wc_seq_order_number_pro
    * @param type $order_number
    * @return type
    */
    private function get_order_id( $order_number ) {

        // Get Order ID by order_number() if the Sequential Order Number plugin is installed
        if ( class_exists('WC_Seq_Order_Number') ) {
            global $wc_seq_order_number;

            $order_id = $wc_seq_order_number->find_order_by_order_number( $order_number );
            if ( 0 === $order_id ) {
                $order_id = $order_number;
            }
        }
        // Get Order ID by order_number() if the Sequential Order Number Pro plugin is installed
        elseif ( class_exists('WC_Seq_Order_Number_Pro') ) {
            global $wc_seq_order_number_pro;

            $order_id = $wc_seq_order_number_pro->find_order_by_order_number( $order_number );

            if ( 0 === $order_id ) {
                $order_id = $order_number;
            }
        }
        // Get order ID  from the Woo Custom and Sequential Order Number plugin
        elseif ( class_exists('WCSON_ORDER_NUMBER') ) {
            global $wcson_order_number;

            $order_id = $wcson_order_number->wcson_find_order_by_order_number( $order_number );

            if ( 0 === $order_id ) {
                $order_id = $order_number;
            }
        }
        //Use normal number if neither exists.
        else {
            $order_id = $order_number;
        }

        return $order_id;

    }



    public function get_goods_from_order($order) {
        $this->debug("Items: ".print_r($order->get_items(), true));

        //$this->debug("Order: ".var_export($order,true));

        $goods = [];
        $_tax = new WC_Tax();
        $tax_option = get_option('woocommerce_prices_include_tax') == 'yes' ? true : false;
        $this->debug("Tax option: ".var_export($tax_option,true));
        foreach ($order->get_items() as $item) {
            if ( $item->is_type( 'line_item' ) && ( $product = $item->get_product() ) ) {
                $goods_item = [
                    "quantity" => $item->get_quantity(),
                    "title" => $item->get_name(),
                    "articleNumber" => $item->get_product_id(),
                    "amount" => ($item->get_total() + $item->get_total_tax())/$item->get_quantity()   // ( ($tax_option) ? 0 : $item->get_subtotal_tax() )
                ];
                // Add VAT if appropriate
                if ( $item->get_total_tax() == 0 ? false : true ) {
                    $rates = array_shift($_tax->get_rates( $product->get_tax_class() ));
                    //Take only the item rate and round it.
                    $item_rate = round(array_shift($rates));

                    $goods_item["VAT"] = $item_rate;
                    $goods_item["isVatIncluded"] = true;
                }
                // Add discount if appropriate
                if ( $item->get_subtotal() - $item->get_total() != 0 ) $goods_item["discount"] = round(($item->get_total()-$item->get_subtotal()) / $item->get_subtotal(),2);

                array_push($goods,$goods_item);
            }
        }

        // Add shipping if shipping for order is nonzero
        if ($order->get_shipping_total() != 0) {
            $goods_item = [
                "quantity" => 1,
                "title" => "Frakt" . $order->get_shipping_method( ),
                "amount" => $order->get_shipping_total() + $order->get_shipping_tax()
            ];
            // Add VAT if appropriate
            if ( $order->get_shipping_tax() != 0 ) {
                $this->debug("Tax: ".var_export($order->get_shipping_tax(),true));
                $this->debug("Tot: ".var_export($order->get_shipping_total(),true));

                $goods_item["VAT"] = round( $order->get_shipping_tax()/$order->get_shipping_total()*100 );
                $goods_item["isVatIncluded"] = true;
            }



            array_push($goods,$goods_item);

        }

        $this->debug("Generated goods list: ".var_export($goods, true));

        return $goods;
    }



    public function check_callback() {
        $this->debug("check_callback() - called");

        $transactionId = isset($_GET['transactionId']) ? $_GET['transactionId'] : false;
        $responseCode = isset($_GET['responseCode']) ? $_GET['responseCode'] : false;
        $method = isset($_GET['method']) ? $_GET['method'] : false;

        if ($transactionId && $responseCode && $method == $this->id) {
            $this->debug("Callback called with values: ".var_export($_GET,true));
            $this->nets_callback($_GET);
        }
    }

    public function info( $string ) {
        if($this->debug_mode) $this->log->info($string,["source"=>$this->id]);
    }
    public function debug( $string ) {
        if($this->debug_mode) $this->log->debug($string,["source"=>$this->id]);
    }








    // HOOKS & CALLBACKS ====================================================================================================================================



    //Called when a subscription is due for payment
    function scheduled_subscription_payment_hook($amount, $order, $product_id ) {
        $this->debug("scheduled_subscription_payment_hook() - called for order ".$order->get_id()."with amount: ". $amount);

        $result = $this->process_subscription_payment($order, $amount);

        if (  $result == false ) {
            $this->debug('Scheduled subscription payment failed for order ' . $order->get_id() );
            WC_Subscriptions_Manager::process_subscription_payment_failure_on_order($order, $product_id);
        }
        else {
            $this->debug('Scheduled subscription payment succeeded for order ' . $order->get_id() );
            WC_Subscriptions_Manager::process_subscription_payments_on_order($order);

        }
    }

    function cancelled_subscription_hook( $order, $product_id ) {
        //TODO: Cancel with nets
    }



    // Called when a user corrects the payment details for a previously attempted subscription payment.
    // The purpose is to update the payment info used for subsequent/future payments
    function update_failing_payment_method_hook($original_order, $renewal_order){
        update_post_meta($original_order->get_id(), 'nets_panhash', get_post_meta($renewal_order->get_id(), 'nets_panhash', true)); //@?
    }



    // Called when nets comes back from the terminal ------------------------------------------------------------------------
    public function nets_callback($data) {
        $this->debug('nets_callback() - called with data: ' . var_export($data,true));

        $transactionId = isset($data['transactionId']) ? $data['transactionId'] : false;
        $responseCode = isset($data['responseCode']) ? $data['responseCode'] : false;


        if ($transactionId && $responseCode == 'OK') { // Terminal phase was successfull
            global $woocommerce;

            $this->debug('Nets Callback function called for transactionId: '.$transactionId); // Add to the nets log

            // Run query to obtain info on transaction
            try {
                $query_data = $this->api->send_request(new  NetsQueryRequest([
                    "merchantId" => $this->merchant_id,
                    "token" => $this->token,
                    "transactionId" => $transactionId,
                ]));

                $this->debug("Query request response: " . var_export($query_data, true));
            }
            catch (Exception $e) {
                $msg = __('Unable to query nets.','woocommerce-gateway-nets');
                if(isset($msg)) wc_add_notice( $msg, 'error' );

                $this->debug("Query request error: " . $e);

                return; //Stop since query didn't work
            }

            // Set up WC Order object and pass for Nets Process call
            $order_id = $this->get_order_id($query_data->OrderInformation->OrderNumber);
            $order = wc_get_order($order_id);


            # Return if full amount is already captured
            if ( $query_data->Summary->AmountCaptured >= $order->get_total() * 100 ){
                $this->debug("Amount captured:".$query_data->Summary->AmountCaptured.", OrderTotal: " . $order->get_total());
                return ;
            }

            $method = $query_data->CardInformation->PaymentMethod->Issuer;
            $wallet = $query_data->Wallet->Issuer;

            if ( $wallet == "Vipps" ) {
                if ( $query_data->Summary->Authorized == false ) { # Check if Authorized (nets does this automatically)
                    $msg = __('Vipps transaction not authorized.','woocommerce-gateway-nets');
                    if(isset($msg)) wc_add_notice( $msg, 'error' );

                    $this->debug("Vipps payment error: " . $e);

                    return; //Stop since query didn't work
                }

                // In the case of vipps the choice is between CAPTURE or not doing CAPTURE
                // depending on the autocapture

                if ( $this->capture_type == "internalcapture" ) {
                    $this->successful_order($order,$transactionId); // Successful Request!
                }
                else {
                    $this->debug("Transaction needs auth for transaction: ".$transactionId);
                    $processed = $this->process_order_with_nets($order, $transactionId, "CAPTURE");

                    if ($processed) $this->successful_order($order,$transactionId); // Successful Request!
                }
            }
            else if ( in_array($method,$this->special_invoice_methods) ) {
                $this->successful_order($order,$transactionId); // Successful Request!
            }
            else {
                if ( $query_data->Summary->Authorized == false ) { // Transaction not authorized, so authorize it; send PROCESS(AUTH) to nets
                    $this->debug("Transaction needs auth for transaction: ".$transactionId);

                    //Figure out if we are doing an AUTH or a SALE
                    $operation = ($this->capture_type == "internalcapture") ? "AUTH" : "SALE";
                    $processed = $this->process_order_with_nets($order, $transactionId, $operation);

                    if ($processed) $this->successful_order($order,$transactionId); // Successful Request!
                }

            }

            /*
            if ( ($query_data->Summary->Authorized == false) && !in_array($method,$this->special_invoice_methods) ) { // Transaction not authorized, so authorize it; send PROCESS(AUTH) to nets
                $this->debug("Transaction needs auth for transaction: ".$transactionId);
                $processed = $this->process_order_with_nets($order,$transactionId);

                if ($processed) $this->successful_order($order,$transactionId); // Successful Request!

            }
            else {
                $this->successful_order($order,$transactionId); // Successful Request!
            }
            */

        }
        else {
            return;
        }


    }

































    // LOGOS ====================================================================================================================================

    function  get_icon_strin() {
        $icons = "";
        foreach ($this->methods as $method => $activated) {
            if ($activated) $icons .= '<div style="display:inline-block;padding-right: 2px;" class="nets-method '.$method.'"></div>';
        }

        return $icons;
    }
    function  get_icon_string() {
        $icons = "";
        foreach ($this->methods as $method => $activated) {
            if ($method == "nets") continue;
            $path = NETS_PLUGIN_DIR_URL."images/".$method.".png";
            if ($activated) $icons .= '<img style="margin-right: 5px;" src="'.$path.'" alt="'.$method.'"/>';
        }

        return $icons;
    }

    function nets_icon_modify( $icon ) {
        if($this->title_logo) {
            return $icon . $this->get_icon_string();
        }
        return $icon;
    }
    // Show icons in checkout description
    public function payment_fields() {
        $description = '';
        if($this->description){
            $description.= wpautop(wptexturize($this->description));
        }
        if($this->description_logo){
            $description.= $this->get_icon_string();
        }
        echo $description;
    }



    // ADMIN ====================================================================================================================================


    /**
    * Admin Panel Options
    * - Options for bits like 'title' and availability on a country-by-country basis
    *
    * @since 1.0.0
    */
    public function admin_options() {
        ?>
        <h3><?php _e('Nets Payment Gateway', 'woocommerce-gateway-nets'); ?></h3>
        <p><?php _e('Nets Payment Gateway works by redirecting user to Nets payment interface to complete the transaction.', 'woocommerce-gateway-nets'); ?></p>

        <?php if( is_ssl() ): ?>
            <!--<div style="display: block;" class="update-nag notice">
                <p>
                    <?php _e('Please add the following url to your Nets Admin page (epayment.nets.eu) by going to Options -> Callback settings:', 'woocommerce-gateway-nets'); ?>
                    <strong><?php echo site_url('nets-callback-url');?></strong>
                </p>
            </div>-->
        <?php else: ?>
            <div class="update-nag notice">
                <p>
                    <?php _e('Nets Payment Gateway highly recommends having an SSL Certificate (https) installed on the website.', 'woocommerce-gateway-nets'); ?>
                </p>
            </div>
        <?php endif;?>




        <?php //if ( get_option( WC_Nets_Payment_Gateway_Class::plugin_name() . '_activated' ) != 'Activated' ) : ?>
            <?php
            //<h3 style="color:red;">
                //printf( __('The Nets Payment Gatewway API License Key has not been activated! %sClick here%s to activate the license key and the plugin.', 'woocommerce-gateway-nets'), '<a href="' . esc_url( admin_url( 'options-general.php?page=' . WC_Nets_Payment_Gateway_Class::plugin_name() . '_dashboard' ) ) . '">', '</a>' )
            //</h3>
            ?>
        <?php //endif; ?>

        <table class="form-table">
            <?php
            if ( $this->is_valid_for_use() ){
                $this->generate_settings_html();
            }
            else { ?>
                <tr valign="top">
                    <th scope="row" class="titledesc"><?php _e('NETS disabled', 'woocommerce-gateway-nets'); ?></th>
                    <td class="forminp">
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('NETS disabled', 'woocommerce-gateway-nets'); ?></span></legend>
                            <?php _e('NETS does not support your store currency.', 'woocommerce-gateway-nets'); ?><br>
                        </fieldset>
                    </td>
                </tr>
            <?php
            }
            ?>
        </table>
        <?php
    }





}
