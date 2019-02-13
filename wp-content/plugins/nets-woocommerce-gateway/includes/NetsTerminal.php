<?php

define("NETS_TERMINAL_BASE_URL", "https://epayment.nets.eu/Terminal/default.aspx");
define("NETS_MOBILE_TERMINAL_BASE_URL", "https://epayment.nets.eu/terminal/mobile/default.aspx");

define("NETS_TEST_TERMINAL_BASE_URL", "https://test.epayment.nets.eu/Terminal/default.aspx");
define("NETS_TEST_MOBILE_TERMINAL_BASE_URL", "https://test.epayment.nets.eu/terminal/mobile/default.aspx");


/*
 * Abstracts away the terminal handling for the Nets flow
 *
 *
 *
 * DOCS: https://shop.nets.eu/web/partners/terminal
 *
 */



class NetsTerminal  {

    public $url = NETS_TERMINAL_BASE_URL;

    public function __construct($testmode,$mobile,$merchantId, $transactionId) {
        $this->testmode = $testmode;
        $this->mobile = $mobile;

        $this->merchantId = $merchantId;
        $this->transactionId = $transactionId;

        if      ( !$this->testmode && $this->mobile ) $this->url = NETS_MOBILE_TERMINAL_BASE_URL;
        else if ( !$this->testmode && !$this->mobile ) $this->url = NETS_TERMINAL_BASE_URL;
        else if ( $this->testmode && $this->mobile ) $this->url = NETS_TEST_MOBILE_TERMINAL_BASE_URL;
        else if ( $this->testmode && !$this->mobile ) $this->url = NETS_TEST_TERMINAL_BASE_URL;

    }



    public function present() {
        $data = [
            "merchantId" => $this->merchantId,
            "transactionId" => $this->transactionId
        ];
        $query_string = http_build_query($data);

        $url = $this->url . "?" . $query_string;

        //header('Location:' . $url);

        return $url;
    }



}
