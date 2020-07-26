<?php

namespace Yves\Mopay\Events;

use Illuminate\Queue\SerializesModels;
use Yves\Mopay\Models\Payment;

class PaymentPendingEvent{

    use SerializesModels;

    public $payment = null;


    public function __construct(Payment $payment) {
        $this->payment = $payment;
    }
}