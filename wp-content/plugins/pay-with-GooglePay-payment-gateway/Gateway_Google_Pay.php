<?php

// Make sure WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
    return;

/*
  Plugin Name: Google Pay For Woocommerce
  Description: Provides an Offline Payment Gateway using Google Pay.Display your Google pay details on your website and get payment direcly into your Google Pay account.
  Version: 1.0.0
  Author: Anup Kumar
  License: GPL2
 */

add_action('plugins_loaded', 'wc_offline_gateway_init', 11);

function wc_offline_gateway_init() {

    class WC_Gateway_Google_Pay extends WC_Payment_Gateway {

        /**
         * Init and hook in the integration.
         */
        function __construct() {
            global $woocommerce;
            $this->id = "google-pay";
            $this->has_fields = false;
            $this->method_title = __("Google Pay", 'woocommerce-google-pay');
            $this->method_description = "Google Pay an Offline Payment Gateway using any UPI code and Google Pay Number or its QR code.Display your Google Pay Details  on your website and get payment direcly into your Google Pay Account.";

            //Initialize form methods
            $this->init_form_fields();
            $this->init_settings();

            // Define user set variables.
            $this->title = $this->settings['title'];
            $this->description = $this->settings['description'];
            $this->instructions = $this->settings['instructions'];
            $this->google_pay_url = $this->settings['google_pay_url'];

            if (version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>=')) {
                add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
                add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));
            } else {
                add_action('woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));
                add_action('woocommerce_thankyou', array(&$this, 'thankyou_page'));
            }
            // Customer Emails
            add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);
        }

        // Build the administration fields for this specific Gateway
        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'woocommerce-google-pay'),
                    'type' => 'checkbox',
                    'label' => __('Enable Google Pay', 'woocommerce-google-pay'),
                    'default' => 'no'
                ),
                'title' => array(
                    'title' => __('Title', 'woocommerce-google-pay'),
                    'type' => 'text',
                    'description' => __('This controls the title for the payment method the customer sees during checkout.', 'woocommerce-google-pay'),
                    'default' => __('Pay with Google Pay', 'woocommerce-google-pay'),
                    'desc_tip' => true,
                ),
                'description' => array(
                    'title' => __('Description', 'woocommerce-google-pay'),
                    'type' => 'textarea',
                    'description' => __('Payment method description that the customer will see on your checkout.', 'woocommerce-google-pay'),
                    'default' => __('Make your payment directly using our Google pay number or UPI ID. Please use your Order ID as a payment reference.', 'woocommerce-google-pay'),
                    'desc_tip' => true,
                ),
                'instructions' => array(
                    'title' => __('Instructions', 'woocommerce-google-pay'),
                    'type' => 'textarea',
                    'description' => __('Instructions that will be added to the thank you page and emails.', 'woocommerce-google-pay'),
                    'default' => "Make your payment directly into ourGoogle Pay account by using Google Pay number or UPI ID. Please use your Order ID as the payment reference. Your order won't be shipped until the funds have cleared in our Google Pay Account.",
                    'desc_tip' => true,
                ),
                'google_pay_url' => array(
                    'title' => __('Google Pay Details/Image*', 'woocommerce-google-pay'),
                    'type' => 'textarea',
                    'description' => __('Google Pay Details/Image to your media library and Provide the URL here.', 'woocommerce-google-pay'),
                    'default' => "Google Pay Details/Image, to be displayed on Thank you page!!",
                    'desc_tip' => true,
                ),
            );
        }

        public function validate_google_pay_field($key, $value) {
            if (isset($value)) {
                if (filter_var($value, FILTER_VALIDATE_URL) === FALSE) {
                    WC_Admin_Settings::add_error(esc_html__('Please enter a valid Google Pay Details/Image.This image will be displayed on Thank you page to recieve payments.', 'woocommerce-google-pay'));
                }
            }

            return $value;
        }

        public function process_payment($order_id) {

            $order = wc_get_order($order_id);

            // Mark as on-hold (we're awaiting the payment)
            $order->update_status('on-hold', __('Awaiting payment via Google Pay', 'woocommerce-google-pay'));

            // Reduce stock levels
            $order->reduce_order_stock();

            // Remove cart
            WC()->cart->empty_cart();

            // Return thankyou redirect
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url($order)
            );
        }

        /**
         * Output for the order received page.
         */
        public function thankyou_page() {
            if ($this->instructions) {
                echo wpautop(wptexturize($this->instructions)). PHP_EOL;
            }
            if ($this->google_pay_url) {
                echo "<br/>Please Google Pay Details/UPI ID in your mobile and make payment.<b> Please add order number in 'What is it for?' tab in paytm app while making payment.</b>";
                echo "<div class='qr_image_class'><img src='" . $this->google_pay_url . "' alt='google-pay-details' /></div>";
                echo "<style>.qr_image_class{width:100%;display:block;padding:10px;} .qr_image_class > img{display:block;margin:0 auto;}</style>";
            }
        }

        /**
         * Add content to the WC emails.
         *
         * @access public
         * @param WC_Order $order
         * @param bool $sent_to_admin
         * @param bool $plain_text
         */
        public function email_instructions($order, $sent_to_admin, $plain_text = false) {
            if ($this->instructions && !$sent_to_admin && 'google-pay' === $order->payment_method && $order->has_status('on-hold')) {
                if ($this->instructions) {
                echo wpautop(wptexturize($this->instructions));
            }
            if ($this->google_pay_url) {
                echo "<br/>Please scan this QR code using paytm app in your mobile and make payment.<b> Please add order number in 'What is it for?' tab in paytm app while making payment.</b>". PHP_EOL;
                echo "<div class='qr_image_class'><img src='" . $this->google_pay_url . "' alt='google-pay-details' /></div>". PHP_EOL;
                echo "<style>.qr_image_class{width:100%;display:block;padding:10px;} .qr_image_class > img{display:block;margin:0 auto;}</style>";
            }
            }
        }

    }

    // Now that we have successfully included our class,
    // Lets add it too WooCommerce
    add_filter('woocommerce_payment_gateways', 'add_google_pay');

    function add_google_pay($methods) {
        $methods[] = 'WC_Gateway_Google_Pay';
        return $methods;
    }

}
