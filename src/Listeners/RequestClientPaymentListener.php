<?php

namespace Yves\Mopay\Listeners;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Webkul\Sales\Repositories\OrderRepository;
use Yves\Mopay\Events\PaymentCreatedEvent;
use Yves\Mopay\Models\Payment;
use Webkul\Checkout\Facades\Cart;

class RequestClientPaymentListener implements ShouldQueue
{

    /**
     * OrderRepository object
     *
     * @var array
     */
    protected $orderRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Attribute\Repositories\OrderRepository  $orderRepository
     * @return void
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function handle(PaymentCreatedEvent $event)
    {

        $url = Config::get("mopay.MOPAY_API_URL");

        $payment = $event->payment;

        $payment_req_datas = $payment->only([
            "external_id",
            "token",
            "amount",
            "mopay_url",
            "post_back_url",
            "client_name",
            'credit_number',
            "msisdn",
        ]);

        if ($payment_req_datas["mopay_url"] != null) {
            $url = $payment_req_datas["mopay_url"];
        }

        $payment_req_datas["amount"] = "" . intval($payment_req_datas["amount"]);
        $payment_req_datas["callback_url"] = $payment_req_datas["post_back_url"];
        $payment_req_datas["phone_number"] = $payment_req_datas["msisdn"];
        $payment->status = Payment::STATUS_INITIATED;
        $payment->message = "Initialized";
        $payment->save();

        print_r($payment_req_datas);

        try {
            $response = Http::retry(3, 100)->post($url, $payment_req_datas);
            print_r($response->json());
            if ($response->ok()) {
                $json = $response->json();
                $status = $json['status'];
                $message = $json["message"];
                $reason = $json["reason"] ?? null;
                $reference_id = $json["reference_id"] ?? null;
                $payment->message = $message;
                $payment->status = $status;
                if ($status == Payment::STATUS_PENDING) {
                    $payment->reference_id = $reference_id;
                }
                if ($status == Payment::STATUS_FAIL) {
                    $payment->reason = $reason;
                }
                $payment->save();
            } else {
                $payment->status = Payment::STATUS_FAIL;
                if ($response->clientError()) {
                    $payment->message = "Client error " . $response->body();
                } else {
                    $payment->message = "Server error " . $response->body();
                }
                $payment->save();
            }
        } catch (Exception $e) {
            // print_r($e->getTrace());
            echo "\n" . $e->getMessage() . "\n";
            $payment->status = Payment::STATUS_FAIL;
            $payment->message = $e->getMessage();
            $payment->save();
        }
    }
}
