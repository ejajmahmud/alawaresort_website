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

class WC_Requery_iPay88 extends WC_Payment_Gateway
{
    public function __construct()
    {
        $this->id = 'ipay88';

        // load settings
        $this->init_settings();
        $this->merchantCode =  $this->get_option('MerchantCode');

        $this->init();
    }

    /**
     * query all order which is on pending payment and payment method with ipay88. and execute requery based on condition
     *
     * @return void
     */
    public function init()
    {
        // get all order which in pending
        $args = [
            'status'         => 'pending',
            'return'         => 'objects',
            'payment_method' => 'ipay88',
        ];
        $orders = WC_Compat_iPay88::wc_get_orders($args);

        foreach ($orders as $order) {
            $refNo  = str_replace('#', '', $order->get_order_number());
            $amount = $order->get_total();
            $date   = $order->get_date_created();

            $currentDateTime = new WC_DateTime();

            $trial_1   = DateTimeImmutable::createFromMutable($date->add(new DateInterval('PT10M')));
            $trial_2   = $trial_1->add(new DateInterval('PT10M'));
            $trial_3   = $trial_2->add(new DateInterval('PT10M'));
            $trial_4   = $trial_3->add(new DateInterval('PT10M'));
            $trial_5   = $trial_4->add(new DateInterval('PT10M'));
            $trial_6   = $trial_5->add(new DateInterval('PT10M'));
            $trial_7   = $trial_6->add(new DateInterval('PT10M'));
            $trial_end = $trial_7->add(new DateInterval('PT10M'));

            if ($currentDateTime > $trial_end) {
                WC_iPay88::add_debug_log($refNo . ' failed execution');
                $order->update_status('failed');
                $order->save();
                continue;
            } elseif ($currentDateTime > $trial_7) {
                WC_iPay88::add_debug_log($refNo . ' trial 7 execution');
                $this->checkOrderStatus($order, $refNo, $amount);
            } elseif ($currentDateTime > $trial_6) {
                WC_iPay88::add_debug_log($refNo . ' trial 6 execution');
                $this->checkOrderStatus($order, $refNo, $amount);
            } elseif ($currentDateTime > $trial_5) {
                WC_iPay88::add_debug_log($refNo . ' trial 5 execution');
                $this->checkOrderStatus($order, $refNo, $amount);
            } elseif ($currentDateTime > $trial_4) {
                WC_iPay88::add_debug_log($refNo . ' trial 4 execution');
                $this->checkOrderStatus($order, $refNo, $amount);
            } elseif ($currentDateTime > $trial_3) {
                WC_iPay88::add_debug_log($refNo . ' trial 3 execution');
                $this->checkOrderStatus($order, $refNo, $amount);
            } elseif ($currentDateTime > $trial_2) {
                WC_iPay88::add_debug_log($refNo . ' trial 2 execution');
                $this->checkOrderStatus($order, $refNo, $amount);
            } elseif ($currentDateTime > $trial_1) {
                WC_iPay88::add_debug_log($refNo . ' trial 1 execution');
                $this->checkOrderStatus($order, $refNo, $amount);
            }
        }
    }

    /**
     * requery and update order status
     *
     * @param  [type] $order
     * @param  [type] $refNo
     * @param  [type] $amount
     * @return void
     */
    public function checkOrderStatus($order, $refNo, $amount)
    {
        $requeryObj = $this->requery($refNo, $amount);
        $transId    = (string)$requeryObj->TransId;
        $errorDesc  = (string)$requeryObj->Errdesc;
        $status     = (string)$requeryObj->Status;

        if (strlen($errorDesc) > 1) {
            $order->add_order_note('Requery: ' . $errorDesc);
        }
        $order->set_transaction_id($transId);

        if ((string)$status == '0') {
            $order->update_status('failed');
        } elseif ((string)$status == '1') {
            $order->payment_complete();
        }

        $order->save();
    }

    /**
     * requery from ipay88 to get transaction details
     *
     * @param  [type] $refNo
     * @param  [type] $amount
     * @return void
     */
    public function requery($refNo, $amount)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => Payments::requery_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => 'MerchantCode=' . $this->merchantCode . '&ReferenceNo=' . $refNo . '&Amount=' . $amount . '&Version=4',
            CURLOPT_HTTPHEADER     => [
                'Accept: */*',
                'Cache-Control: no-cache',
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);

        $response = curl_exec($curl);
        $err      = curl_error($curl);

        curl_close($curl);

        error_log('MerchantCode=' . $this->merchantCode . '&ReferenceNo=' . $refNo . '&Amount=' . $amount . '&Version=4');

        if ($err) {
            error_log($err);
            return 'cURL Error #:' . $err;
        } else {
            return simplexml_load_string($response);
        }
    }
}
