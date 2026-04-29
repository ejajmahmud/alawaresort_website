<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WC_Payment_Gateway')) {
    return;
}

if (!class_exists('Payments')) {
    require_once 'payment.php';
}

class WC_Gateway_iPay88 extends WC_Payment_Gateway
{
    public $posted_payment_type;

    public $check_pass;

    public function __construct()
    {
        $this->id           = 'ipay88';
        $this->icon         = apply_filters('woocommerce_ipay88_icon', WC_iPay88::plugin_url() . '/assets/images/ipay88.png');
        $this->paymentsURL  = WC_iPay88::plugin_url() . '/assets/payment.php';
        $this->has_fields   = false;
        $this->method_title = __('iPay88', 'wc_ipay88');

        // get all payment options
        $this->paymentOptions = Payments::payments;
        $this->signatureType  = Payments::signature;

        // prepare payment options for admin site
        $this->paymentTypeOptions = [];
        foreach ($this->paymentOptions as $option) {
            $this->paymentTypeOptions[$option['id']] = __($option['name'], 'wc_ipay88');
        }

        // prepare payment option gategory
        $this->creditCardOptions = [];
        foreach ($this->paymentOptions as $option) {
            if ($option['category'] == 'credit-card') {
                $this->creditCardOptions[] = $option['id'];
            }
        }

        $this->bankOptions = [];
        foreach ($this->paymentOptions as $option) {
            if ($option['category'] == 'bank') {
                $this->bankOptions[] = $option['id'];
            }
        }

        $this->otherOptions = [];
        foreach ($this->paymentOptions as $option) {
            if ($option['category'] == 'other') {
                $this->otherOptions[] = $option['id'];
            }
        }

        $this->ewalletOptions = [];
        foreach ($this->paymentOptions as $option) {
            if ($option['category'] == 'e-wallet') {
                $this->ewalletOptions[] = $option['id'];
            }
        }

        $this->hash_amount      = 0;
        $this->formatted_amount = 0;

        // Load the form fields.
        $this->init_form_fields();

        // Load the settings.
        $this->init_settings();

        // Define user set variables
        $this->title                    = $this->settings['title'];
        $this->description              = $this->settings['description'];
        $this->enabled                  = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'no';
        $this->MerchantCode             = $this->settings['MerchantCode'];
        $this->MerchantKey              = $this->settings['MerchantKey'];
        $this->use_css                  = isset($this->settings['use_css']) ? $this->settings['use_css'] : 'no';
        $this->paymenttype_available    = isset($this->settings['paymenttype_available']) ? $this->settings['paymenttype_available'] : [];
        $this->gateway                  = isset($this->settings['gateway']) ? $this->settings['gateway'] : 'MY';
        $this->sandbox                  = isset($this->settings['sandbox']) ? $this->settings['sandbox'] : 'yes';
        $this->url                      = Payments::url;

        // Actions
        add_action('woocommerce_api_' . strtolower(get_class($this)), [$this, 'check_status_response_ipay88']);
        add_action('woocommerce_receipt_' . $this->id, [$this, 'receipt_page']);

        // Save options
        add_action('woocommerce_update_options_payment_gateways', [$this, 'process_admin_options']);
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);

        if (!$this->is_valid()) {
            $this->enabled = 'no';
        }

        if ('yes' == $this->use_css && !empty($this->paymenttype_available)) {
            add_action('wp_enqueue_scripts', [$this, 'add_ipay88_checkout_styles']);
        } else {
            add_action('wp_enqueue_scripts', [$this, 'add_ipay88_checkout_basic_styles']);
        }
    }

    /**
     * Inject group css to store front checkout page
     */
    public function add_ipay88_checkout_styles()
    {
        if (is_checkout()) {
            wp_register_style('ipay88-checkout-css', WC_iPay88::plugin_url() . '/assets/css/group.css');
            wp_enqueue_style('ipay88-checkout-css');
        }
    }

    /**
     * Inject basic css to store front checkout page
     */
    public function add_ipay88_checkout_basic_styles()
    {
        if (is_checkout()) {
            wp_register_style('ipay88-checkout-basic-css', WC_iPay88::plugin_url() . '/assets/css/basic.css');
            wp_enqueue_style('ipay88-checkout-basic-css');
        }
    }

    /**
     * Check if the currency allows for gateway use.
     *
     * @return bool
     **/

    // TODO : check the currency
    public function is_valid()
    {
        if ('PH' == $this->gateway) {
            $allowed_currency = ['PHP'];
        } else {
            $allowed_currency = [
                'MYR',
                'USD',
                'AUD',
                'CAD',
                'CNY',
                'EUR',
                'GBP',
                'SGD',
                'HKD',
                'IDR',
                'INR',
                'PHP',
                'THB',
                'TWD',
            ];
        }

        if (!in_array(get_woocommerce_currency(), $allowed_currency)) {
            return false;
        }

        return true;
    }

    /**
     * Admin Panel Options.
     **/
    public function admin_options()
    {
        ?>
		<h3><?php _e('iPay88', 'wc_ipay88'); ?></h3>
		<p><?php _e('iPay88 is a payment gateway works by redirecting the customer to iPay88 server to make a payment and then returns the customer back to your "Thank you/Receipt" page.', 'wc_ipay88'); ?></p>

		<table class="form-table">
			<?php $this->generate_settings_html(); ?>
		</table> 
		<?php
    }

    /**
     * Initialise Gateway Settings Form Fields.
     **/
    public function init_form_fields()
    {
        $this->form_fields = [
            'enabled' => [
                'type'    => 'checkbox',
                'label'   => __('Enable iPay88', 'wc_ipay88'),
                'default' => 'no',
            ],
            'gateway' => [
                'title'       => __('iPay88 Gateway In Use', 'wc_ipay88'),
                'type'        => 'select',
                'description' => __('Which iPay88 gateway are you using? ', 'wc_ipay88'),
                'css'         => 'min-width:350px;',
                'class'       => 'chosen_select',
                'default'     => 'MY',
                'options'     => [
                    'MY' => __('Malaysia', 'wc_ipay88'),
                ],
            ],
            'title' => [
                'title'       => __('Method Title', 'wc_ipay88'),
                'type'        => 'text',
                'description' => __('This controls the title which the user sees during checkout.', 'wc_ipay88'),
                'default'     => __('iPay88', 'wc_ipay88'),
            ],
            'description' => [
                'title'       => __('Description', 'wc_ipay88'),
                'type'        => 'textarea',
                'description' => __('This controls the description which the user sees during checkout.', 'wc_ipay88'),
                'default'     => __('Make a payment using your Credit/Debit card.', 'wc_ipay88'),
            ],
            'MerchantCode' => [
                'title'       => __('Merchant Code', 'wc_ipay88'),
                'type'        => 'text',
                'description' => __('The Merchant Code provided by iPay88 and used to uniquely identify the Merchant.', 'wc_ipay88'),
                'default'     => '',
            ],
            'MerchantKey' => [
                'title'       => __('Merchant Key', 'wc_ipay88'),
                'type'        => 'password',
                'description' => __('Provided by iPay88 OPSG and shared between iPay88 and merchant only.', 'wc_ipay88'),
                'default'     => '',
            ],
            'paymenttype_available' => [
                'title'       => __('Available Payment Types', 'wc_ipay88'),
                'type'        => 'multiselect',
                'description' => __('Choose the payment types you can offer to the customers. The Payment types will be presented to the customer to pre-select on the Checkout page. Do not choose any type to use the default selection on the iPay88 payment page.', 'wc_ipay88'),
                'options'     => $this->paymentTypeOptions,
                'css'         => 'min-width:350px;',
                'class'       => 'chosen_select',
                'default'     => '',
            ],
            'use_css' => [
                'type'        => 'checkbox',
                'label'       => __('Group Payment Types', 'wc_ipay88'),
                'description' => __('Check if you want to use the css packed with the plugin. It will group the payment types in three columns.', 'wc_ipay88'),
                'default'     => 'no',
            ],
            'debug' => [
                'type'        => 'checkbox',
                'label'       => __('Debug Log. Recommended: Test Mode only', 'wc_ipay88'),
                'default'     => 'no',
                'description' => __('Debug log will provide you with most of the data and events generated by the payment process. Logged inside <code>woocommerce/logs/ipay88-' . sanitize_file_name(wp_hash('ipay88')) . '.txt</code>.'),
            ],
        ];
    }

    /**
     * Show Description in place of the payment fields.
     **/
    public function payment_fields()
    {
        if ($this->description) {
            echo wpautop(wptexturize($this->description));
        }

        ob_start();
        $this->getAvailablePaymentOptionHtml();
        $html = ob_get_clean();
        echo $html;
    }

    /**
     * Generate available payment option for store front check out
     */
    public function getAvailablePaymentOptionHtml()
    {
        if (!empty($this->paymenttype_available)) {
            ?>
			<p class="form-row">
				<label for="ipay88_payment_type"><?php _e('Payment Type', 'wc_ipay88'); ?>
                    <span class="required">*</span>
                </label>
			</p>
			<?php
            echo '<div class="ipay88_payment_container">';

            if ((bool) array_intersect($this->creditCardOptions, $this->paymenttype_available)) {
                echo '<div class="ipay88_opt_container">';
                if ('yes' == $this->use_css) {
                    echo '<p class="ipay88_title_opt">' . __('Credit/Debit Card', 'wc_ipay88') . '</p>';
                }

                foreach ($this->creditCardOptions as $number) {
                    if (in_array($number, $this->paymenttype_available)) {
                        echo '<div class="ipay88_opt">';
                        echo '<label for="ipay88' . $this->paymentOptions[$number]['id'] . '">';
                        echo '<input type="radio" id="ipay88' . $this->paymentOptions[$number]['id'] . '"';
                        echo 'name="ipay88_payment_type" value="' . $number . '">';
                        echo '<img alt="' . $this->paymentOptions[$number]['name'] . '" src="' . WC_Compat_iPay88::force_https(WC_iPay88::plugin_url()) . '/assets/images/' . $this->paymentOptions[$number]['image'] . '">';
                        echo '</label>';
                        echo '</div>';
                    }
                }
                echo '</div>';
            }

            if ((bool) array_intersect($this->bankOptions, $this->paymenttype_available)) {
                echo '<div class="ipay88_opt_container">';
                if ('yes' == $this->use_css) {
                    echo '<p class="ipay88_title_opt">' . __('Online Banking', 'wc_ipay88') . '</p>';
                }
                foreach ($this->bankOptions as $number) {
                    if (in_array($number, $this->paymenttype_available)) {
                        echo '<div class="ipay88_opt">';
                        echo '<label for="ipay88' . $this->paymentOptions[$number]['id'] . '">';
                        echo '<input type="radio" id="ipay88' . $this->paymentOptions[$number]['id'] . '"';
                        echo 'name="ipay88_payment_type" value="' . $number . '">';
                        echo '<img alt="' . $this->paymentOptions[$number]['name'] . '" src="' . WC_Compat_iPay88::force_https(WC_iPay88::plugin_url()) . '/assets/images/' . $this->paymentOptions[$number]['image'] . '">';
                        echo '</label>';
                        echo '</div>';
                    }
                }
                echo '</div>';
            }

            if ((bool) array_intersect($this->ewalletOptions, $this->paymenttype_available)) {
                echo '<div class="ipay88_opt_container">';
                if ('yes' == $this->use_css) {
                    echo '<p class="ipay88_title_opt">' . __('E-Wallet', 'wc_ipay88') . '</p>';
                }

                foreach ($this->ewalletOptions as $number) {
                    if (in_array($number, $this->paymenttype_available)) {
                        echo '<div class="ipay88_opt">';
                        echo '<label for="ipay88' . $this->paymentOptions[$number]['id'] . '">';
                        echo '<input type="radio" id="ipay88' . $this->paymentOptions[$number]['id'] . '"';
                        echo 'name="ipay88_payment_type" value="' . $number . '">';
                        echo '<img alt="' . $this->paymentOptions[$number]['name'] . '" src="' . WC_Compat_iPay88::force_https(WC_iPay88::plugin_url()) . '/assets/images/' . $this->paymentOptions[$number]['image'] . '">';
                        echo '</label>';
                        echo '</div>';
                    }
                }
                echo '</div>';
            }

            if ((bool) array_intersect($this->otherOptions, $this->paymenttype_available)) {
                echo '<div class="ipay88_opt_container">';
                if ('yes' == $this->use_css) {
                    echo '<p class="ipay88_title_opt">' . __('Other', 'wc_ipay88') . '</p>';
                }

                foreach ($this->otherOptions as $number) {
                    if (in_array($number, $this->paymenttype_available)) {
                        echo '<div class="ipay88_opt">';
                        echo '<label for="ipay88' . $this->paymentOptions[$number]['id'] . '">';
                        echo '<input type="radio" id="ipay88' . $this->paymentOptions[$number]['id'] . '"';
                        echo 'name="ipay88_payment_type" value="' . $number . '">';
                        echo '<img alt="' . $this->paymentOptions[$number]['name'] . '" src="' . WC_Compat_iPay88::force_https(WC_iPay88::plugin_url()) . '/assets/images/' . $this->paymentOptions[$number]['image'] . '">';
                        echo '</label>';
                        echo '</div>';
                    }
                }
                echo '</div>';
            }
            echo '</div>';
        }
    }

    /**
     * Validate payment fields.
     **/
    public function validate_fields()
    {
        $ptype                     = WC_iPay88::get_field('ipay88_payment_type', $_POST);
        $this->posted_payment_type = null !== $ptype ? $ptype : '0';
        $this->check_payment_fields($this->posted_payment_type);

        //Note the credit card fields check was passed
        $this->check_pass = true;

        if (!WC_Compat_iPay88::wc_notice_count('error')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Generate iPay88 form.
     **/
    public function generate_ipay88_form($order_id)
    {
        $order = WC_Compat_iPay88::wc_get_order($order_id);

        //Debug log
        WC_iPay88::add_debug_log('Generating payment form for order #' . $order_id);

        $desc = '';
        if (0 < sizeof($order->get_items())) {
            foreach ($order->get_items() as $item) {
                if ($item['qty']) {
                    $item_name = $item['name'];

                    $item_meta = WC_Compat_iPay88::get_order_item_meta($item);

                    if (WC_Compat_iPay88::is_wc_3_1()) {
                        $name = $item_meta->get_product()->get_name();
                        $item_name .= ' (' . $name . ')';
                    } else {
                        if ($meta = $item_meta->display(true, true)) {
                            $item_name .= ' (' . $meta . ')';
                        }
                    }

                    $desc .= $item['qty'] . ' x ' . $item_name . ', ';
                }
            }
            //Add the description
            $desc = substr($desc, 0, -2);
        }

        $currency = WC_Compat_iPay88::get_order_currency($order);

        // Format the order total
        $this->format_amount($order->get_total());

        $ipay88_args = [
            'MerchantCode' => $this->MerchantCode,
            'RefNo'        => str_replace('#', '', $order->get_order_number()),
            'Amount'       => $this->formatted_amount,
            'Currency'     => $currency,
            'ProdDesc'     => $desc,
            'UserName'     => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'UserEmail'    => $order->get_billing_email(),
            'UserContact'  => $order->get_billing_phone(),
            'ResponseURL'  => WC_Compat_iPay88::api_request_url('WC_Gateway_iPay88'),
            'BackendURL'   => WC_Compat_iPay88::force_https(add_query_arg('iPay88_response', 'backend', WC_Compat_iPay88::api_request_url('WC_Gateway_iPay88'))),
        ];

        $payment_type = WC_iPay88::get_field('ptype', $_GET);
        if (null != $payment_type && 0 != $payment_type) {
            $ipay88_args['PaymentId'] = $payment_type;
        }

        if ($this->signatureType == 'sha1') {
            $ipay88_args['SignatureType'] = 'SHA1';
            $ipay88_args['signature']     = $this->generate_sha1_signature($ipay88_args, false);
        } else {
            $ipay88_args['SignatureType'] = 'SHA256';
            $ipay88_args['signature']     = $this->generate_sha256_signature($ipay88_args, false);
        }

        WC_iPay88::add_debug_log('Order form parameters: ' . print_r($ipay88_args, true));

        $ipay88_form_array = [];
        foreach ($ipay88_args as $key => $value) {
            $ipay88_form_array[] = '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" />';
        }

        WC_Compat_iPay88::wc_include_js(
            'jQuery("body").block({
				message: "<img src=\"' . esc_url(WC_Compat_iPay88::force_https(WC_iPay88::plugin_url())) . '/assets/images/ajax-loader.gif\" alt=\"Redirecting...\" style=\"\" />'
            . '<p>Thank you for your order. We are now redirecting you to iPay88 to make payment.</p>",
				overlayCSS: {
					background: "#fff",
					opacity: 0.9,
				},
				css: {
                    padding:        20,
					textAlign:      "center",
					color:          "#555",
					border:         "3px solid #aaa",
					backgroundColor:"#fff",
					cursor:         "wait",
					lineHeight:		"32px",
					zIndex:         "9999999"
				}
			});
			jQuery("#submit_ipay88_payment_form").click();
			'
        );

        return '<form action="' . esc_url($this->url) . '" method="post" id="ipay88_payment_form" target="_top">
			' . implode('', $ipay88_form_array) . '
			<input type="submit" class="button-alt" id="submit_ipay88_payment_form" value="' . __('Pay via iPay88', 'wc_ipay88') . '" />'
        . '<a class="button cancel" href="' . esc_url($order->get_cancel_order_url()) . '">' . __('Cancel order &amp; restore cart', 'wc_ipay88') . '</a>
			</form>';
    }

    /**
     * Process the payment.
     **/
    public function process_payment($order_id)
    {
        if (!$this->check_pass) {
            $ptype                     = WC_iPay88::get_field('ipay88_payment_type', $_POST);
            $this->posted_payment_type = null !== $ptype ? $ptype : '0';
            $this->check_payment_fields($this->posted_payment_type);
        }

        if (!WC_Compat_iPay88::wc_notice_count('error')) {
            $order = WC_Compat_iPay88::wc_get_order($order_id);

            return [
                'result'   => 'success',
                'redirect' => add_query_arg('ptype', $this->posted_payment_type, $order->get_checkout_payment_url(true)),
            ];
        }
    }

    /**
     * receipt_page.
     **/
    public function receipt_page($order)
    {
        echo '<p>' . __('Thank you for your order, please click the button below to pay with iPay88.', 'wc_ipay88') . '</p>';
        echo $this->generate_ipay88_form($order);
    }

    /**
     * Check and validate the received response.
     **/
    public function validate_response()
    {
        WC_iPay88::add_debug_log('Validating response...');

        $payment_id    = WC_iPay88::get_field('PaymentId', $_POST);

        if ($this->signatureType == 'sha1') {
            $signature  = $this->generate_sha1_signature($_POST);
        } else {
            $signature  = $this->generate_sha256_signature($_POST);
        }

        WC_iPay88::add_debug_log('Generated response signature is: ' . $signature);
        WC_iPay88::add_debug_log('Received post signature is: ' . WC_iPay88::get_field('Signature', $_POST));

        if (WC_iPay88::get_field('Signature', $_POST) == $signature) {
            $order_id = WC_iPay88::get_field('RefNo', $_POST);

            $order = WC_Compat_iPay88::wc_get_order((int) $order_id);

            WC_iPay88::add_debug_log('Signature validation passed.');

            $order_total = number_format($order->get_total(), 2, '', '');
            if (0 == abs($order_total - $this->hash_amount)) {
                WC_iPay88::add_debug_log('Amount validation passed.');
                return true;
            }

            WC_iPay88::add_debug_log('Amount validation failed.');
            return false;
        } else if (WC_iPay88::get_field('Signature', $_POST) == ''){
            $order_id = WC_iPay88::get_field('RefNo', $_POST);
            $order = WC_Compat_iPay88::wc_get_order((int) $order_id);

                if (WC_iPay88::get_field('Status', $_POST) == 0) {
                WC_iPay88::add_debug_log('Signature validation failed.');
                return true;
                }

        } else {
            WC_iPay88::add_debug_log('Signature validation failed.');
            return false;
        }
    }

    /**
     * Check for iPay88 Payment Response.
     * Process Payment based on the Response.
     **/
    public function check_status_response_ipay88()
    {
        $posted                  = stripslashes_deep($_POST);
        $is_backend_notification = ('backend' == WC_iPay88::get_field('iPay88_response', $_GET));
        $received_ok             = 'RECEIVEOK';

        // Backend notification will get the RECEIVEOK response
        if ($is_backend_notification) {
            WC_iPay88::add_debug_log('Backend response.');
        }
        WC_iPay88::add_debug_log('Payment response received. Response is: ' . print_r($_POST, true));

        if ($this->validate_response()) {
            $refno   = WC_iPay88::get_field('RefNo', $_POST);
            $transid = WC_iPay88::get_field('TransId', $_POST);
            $status  = WC_iPay88::get_field('Status', $_POST);
            $errdesc = WC_iPay88::get_field('ErrDesc', $_POST);

            $order_id     = $refno;
            $order        = WC_Compat_iPay88::wc_get_order((int) $order_id);
            $redirect_url = $this->get_return_url($order);

            // Check if the order was already processed
            if ('completed' == WC_Compat_iPay88::get_order_status($order) || 'processing' == WC_Compat_iPay88::get_order_status($order)) {
                WC_iPay88::add_debug_log('Payment already processed. Aborting.');

                // Backend notification will get the OK response
                if ($is_backend_notification) {
                    echo $received_ok;
                } else {
                    // Normal Payment notification need to be redirected to the "Thank You" page.
                    wp_safe_redirect($redirect_url);
                }
                exit;
            }

            // Update order based on the status of payment
            switch ($status) {
                case 1:
                    // Update order
                    $order->set_transaction_id($transid);
                    $order->add_order_note(
                        sprintf(
                             __(
                                'iPay88 Payment Completed. Transaction Reference Number: %s.',
                                'wc_ipay88'
                            ),
                            $transid
                        )
                    );

                    WC_iPay88::add_debug_log('Payment completed.');
                    WC_Compat_iPay88::empty_cart();
                    $order->payment_complete();
                    break;
                case 2:
                default:
                    WC_iPay88::add_debug_log('Payment failed.');
                    $order->set_transaction_id($transid);
                    $order->add_order_note(
                        sprintf(
                            __(
                                'iPay88 Payment Failed. Error Description: %s Transaction Reference Number: %s.',
                                'wc_ipay88'
                            ),
                            $errdesc,
                            $transid
                        )
                    );
                    $order->update_status('failed');

                    // Add error to show the customer and the cancel URL
                    WC_Compat_iPay88::wc_add_notice(
                        __(
                            'Your Payment Failed.
                            Please try again or use another payment option.',
                            'wc_ipay88'
                        ),
                        'error'
                    );
                    break;
                }

            // Backend notification will get the RECEIVEOK response
            if ($is_backend_notification) {
                echo $received_ok;
            } else {
                // Normal Payment notification needs to be redirected to the "Thank You" page.
                wp_safe_redirect($redirect_url);
            }
            exit;
        }
    }

    /**
     * Generate the sha256 control signature. <br/>
     * Used in both the request and the response to validate the authenticity of the message.
     *
     * @param array $params      The request or response parameters
     * @param bool  $is_response Are the parameters from the response message
     *
     * @return string The sha256 generated string
     **/
    private function generate_sha256_signature($params, $is_response = true)
    {
        $string = '';
        if ($is_response) {
            $this->format_amount(str_replace(',', '', $params['Amount']));
            $string = $params['PaymentId'] . $params['RefNo'] . $this->hash_amount . $params['Currency'] . $params['Status'];
        } else {
            $string = $params['RefNo'] . $this->hash_amount . $params['Currency'];
        }
        $string = $this->MerchantKey . $this->MerchantCode . $string;

        WC_iPay88::add_debug_log('Signature SHA256 string is: ' . $string);
        return hash('sha256', $string);
    }

    /**
     * Generate the sha1 control signature. <br/>
     * Used in both the request and the response to validate the authenticity of the message.
     *
     * @global object $woocommerce
     *
     * @param array $params      The request or response parameters
     * @param bool  $is_response Are the parameters from the response message
     *
     * @return string The sha1 generated string
     **/
    private function generate_sha1_signature($params, $is_response = true)
    {
        $string = '';
        if ($is_response) {
            $this->format_amount(str_replace(',', '', $params['Amount']));
            $string = $params['PaymentId'] . $params['RefNo'] . $this->hash_amount . $params['Currency'] . $params['Status'];
        } else {
            $string = $params['RefNo'] . $this->hash_amount . $params['Currency'];
        }
        $string = $this->MerchantKey . $this->MerchantCode . $string;

        WC_iPay88::add_debug_log('Signature SHA1 string is: ' . $string);
        return base64_encode($this->hex2bin(sha1($string)));
    }

    public function hex2bin($hexSource)
    {
        $bin = '';
        for ($i = 0; $i < strlen($hexSource); $i = $i + 2) {
            $bin .= chr(hexdec(substr($hexSource, $i, 2)));
        }

        return $bin;
    }

    /**
     * Check the Payment method is submitted and is valid.
     *
     * @param string $payment_type
     */
    private function check_payment_fields($payment_type = '0')
    {
        if (!empty($this->paymenttype_available)) {
            if ('0' == $payment_type) {
                WC_Compat_iPay88::wc_add_notice(__('Payment type is required.', 'wc_ipay88'), 'error');
                return;
            }

            if (!in_array($payment_type, $this->paymenttype_available)) {
                WC_Compat_iPay88::wc_add_notice(__('Wrong payment type. Please try again.', 'wc_ipay88'), 'error');
                return;
            }
        }
    }

    /**
     * Format the two amounts we need.
     * One for hashing
     * One for request parameter.
     *
     * @param float $amount
     */
    public function format_amount($amount)
    {
        if (is_numeric($amount)) {
            $this->hash_amount      = number_format($amount, 2, '', '');
            $this->formatted_amount = number_format($amount, 2, '.', ',');
        }
    }
}
