<?php

namespace Yves\Mopay\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Yves\Mopay\Events\PaymentCompletedEvent;
use Yves\Mopay\Events\PaymentCreatedEvent;
use Yves\Mopay\Events\PaymentFailedEvent;
use Yves\Mopay\Events\PaymentInitializedEvent;
use Yves\Mopay\Events\PaymentPendingEvent;

class Payment extends Model
{

    protected $table = "mopay_payments";

    protected $guarded = [];

    public const STATUS_CREATED = -1;
    public const STATUS_INITIATED = 0;
    public const STATUS_PENDING = 1;
    public const STATUS_COMPLETE = 2;
    public const STATUS_FAIL = 3;


    public static function request(
        $amount = 0,
        $msisdn,
        $client_name,
        $post_back_url = null,
        $token = null,
        $email = null,
        $context_model = null,
        $context_model_id = null,
        $externel_id  = null,
        $credit_number = null,
        $mopay_url = null
    ): Payment {
        if ($amount == 0) {
            return null;
        }
        if ($token == null) {
            $token = Config::get("mopay.MOPAY_API_TOKEN");
        }
        if ($post_back_url == null) {
            $post_back_url = Config::get("mopay.MOPAY_WEBHOOK");
        }
        $msisdn = str_replace("+", "", $msisdn);
        if ($externel_id == null) {
            $externel_id = random_int(1, 9999999999999);
        }

        $payment = Payment::create([
            "status"           => 5,
            "token"            => $token,
            "amount"           => $amount,
            "msisdn"           => $msisdn,
            "external_id"      => $externel_id,
            "mopay_url"        => $mopay_url,
            "email"            => $email,
            "message"          => "Created",
            "post_back_url"    => $post_back_url,
            "client_name"      => $client_name,
            "context_model"    => $context_model,
            "credit_number"    => $credit_number,
            "context_model_id" => $context_model_id,
        ]);
        return $payment;
    }


    public static function booted()
    {
        parent::updated(function ($payment) {
            switch ($payment->status) {
                case self::STATUS_INITIATED:
                    event(new PaymentInitializedEvent($payment));
                    break;
                case self::STATUS_PENDING:
                    event(new PaymentPendingEvent($payment));
                    break;
                case self::STATUS_COMPLETE:
                    event(new PaymentCompletedEvent($payment));
                    break;
                case self::STATUS_FAIL:
                    event(new PaymentFailedEvent($payment));
                    break;
                default:
                    break;
            }
        });

        parent::created(function ($payment) {
            event(new PaymentCreatedEvent($payment));
        });
    }
}
