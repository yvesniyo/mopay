<?php

namespace Yves\Mopay\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Yves\Mopay\Events\PaymentCreatedEvent;
use Yves\Mopay\Models\Payment;

class RequestClientPaymentListener implements ShouldQueue{

    public function handle(PaymentCreatedEvent $event) 
    {

        $url = Config::get("mopay.MOPAY_API_URL");
        $payment = $event->payment;
        $payment->external_id = $payment->id;
        $payment_req_datas = $payment->only([
            "external_id",
            "token",
            "amount",
            "post_back_url",
            "client_name",
            "msisdn",
        ]);
        $payment_req_datas["postback_url"] = $payment_req_datas["post_back_url"]; 
        unset($payment_req_datas["post_back_url"]);
        $payment->status = Payment::$STATUS_INITIATED;
        $payment->message = "Initialized";
        $payment->save();
        $response = Http::retry(3, 100)->post($url, $payment_req_datas);
        if($response->ok()){
            $json = $response->json();
            $status = $json['status'];
            $message = $json["message"];
            $reason = $json["reason"] ?? null;
            $reference_id = $json["reference_id"] ?? null;
            $payment->message = $message;
            $payment->status = $status;
            if($status == Payment::$STATUS_PENDING){
                $payment->reference_id = $reference_id;
            }
            if($status == Payment::$STATUS_FAIL){
                $payment->reason = $reason;
            }
            $payment->save();
        }else{
            $payment->status = Payment::$STATUS_FAIL;
            if($response->clientError()){
                $payment->message = "Client error ". $response->body();
            }else{
                $payment->message = "Server error ". $response->body();
            }
            $payment->save();
        }
    }
}