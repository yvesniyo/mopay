<?php

namespace Yves\Mopay\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Yves\Mopay\Events\PaymentCompletedEvent;
use Yves\Mopay\Events\PaymentCreatedEvent;
use Yves\Mopay\Events\PaymentFailedEvent;
use Yves\Mopay\Events\PaymentInitializedEvent;
use Yves\Mopay\Events\PaymentPendingEvent;

class Payment extends Model{

    protected $table = "mopay_payments";

    protected $guarded = [];

    public static $STATUS_CREATED = -1;
    public static $STATUS_INITIATED = 0;
    public static $STATUS_PENDING = 1;
    public static $STATUS_COMPLETE = 2;
    public static $STATUS_FAIL = 3;    


    public static function request(
        $amount=0,
        $msisdn,
        $client_name,
        $post_back_url=null,
        $token = null,
        $email=null,
        $context_model= null,
        $context_model_id=null
        ): Payment{
        if($amount == 0){
            return null;
        }
        if($token == null){
            $token = Config::get("mopay.MOPAY_API_TOKEN");
        }
        if($post_back_url == null){
            $post_back_url = Config::get("mopay.MOPAY_WEBHOOK");
        }
        $msisdn = str_replace("+","", $msisdn);        
        $payment = Payment::create([
            "status"=> 5,
            "token"=> $token,
            "amount"=> $amount,
            "msisdn"=> $msisdn,
            "external_id"=> 0,
            "email"=> $email,
            "message"=> "Created",
            "post_back_url"=> $post_back_url,
            "client_name"=> $client_name,
            "context_model"=> $context_model,
            "context_model_id"=> $context_model_id,
        ]);
        return $payment;
    }


    public static function booted(){
        parent::updated(function($payment){
            switch ($payment->status) {
                case self::$STATUS_INITIATED:
                    event(new PaymentInitializedEvent($payment));
                    break;
                case self::$STATUS_PENDING:
                    event(new PaymentPendingEvent($payment));
                    break;
                case self::$STATUS_COMPLETE:
                    event(new PaymentCompletedEvent($payment));
                    break;
                case self::$STATUS_FAIL:
                    event(new PaymentFailedEvent($payment));
                    break;
                default:
                    break;
            }

        });

        parent::created(function($payment){
            event(new PaymentCreatedEvent($payment));
        });
    }




}