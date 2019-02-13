=== WOOCOMMERCE NETS GATEWAY ===Based on Nets Gateway by Nettpilot
== DESCRIPTION ==NETS Payment Services is a leading Scandinavian online payment service provider. Woocommerce NETS Gateway is a plugin that extends WooCommerce, allowing you to take payments via NETS.It supports the following currencies:  NOK, SEK, DKK, EUR, USD, GBP, AUD, THB. Please ask if you need any other currency.

== IMPORTANT NOTES ==This plugin extends WooCommerce with a NETS payment gateway. The plugin will only work if WooCommerce is activated.
This version does support autocapture, direct capture via the WooCommerce order view, multicurrencies and recurring payments with Woo Subscriptions. Please go to https://www.nettpilot.no/kontakt/ I you would like it extended to suit your needs.== KNOWN ISSUES ==
If the page does not redirect back after payments has been done (for example closing the tab), the order will not be set to processing in WooCommerce. In order to fix this one must have an SSL Certificate installed on their server (https) and entered the correct callback in the Nets admin options settings
== INSTALLATION	 ==
1. Download and unzip the latest release zip file.
2. If you use the WordPress plugin uploader to install this plugin skip to step 4.
3. Upload the entire plugin directory to your /wp-content/plugins/ directory.
4. Activate the plugin through the 'Plugins' menu in WordPress Administration.
5. Enter the licence key you've received per email and the email address of the person who bought the plugin and the license. It should be sent by mail. If your mail somehow disappears, then go to http://www.nettpilot.no/min-konto/, log in and you will find both plugin and license key.
6. Go WooCommerce Settings --> Payment Gateways and configure your NETS settings.
7. If you are using the module with Woo Subscriptions then add this as the payment gateway in the Woo Subscription plugin settings.
8. Test it or go live right away. Remember that card transactions can be handled in the NETS Admin, or via either instant autocapture og direct capture when ordrer is set to Complete. Please check the NETS settings in WP-admin.
9. For debugging please make sure you have the right merchant ID and Token, and make sure you have set PHP memory limit to at least 64 mb and that the theme is 100% woocommerce compatible and set up right. Make sure that if you want to test it before going live, then you need to contact support at NETS and make sure they have set it to test mode in their end as well.
10. The latest version can be ordered from this page: http://www.nettpilot.no/produkt/nets-betalingsmodul-for-wordpress-woocommerce/
11. If you need help and support from Nettpilot, then use the form at https://www.nettpilot.no/support/