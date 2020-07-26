<?php

namespace Yves\Mopay\Events;

use Illuminate\Queue\SerializesModels;
use Yves\Mopay\Models\Payment;

class PaymentFailedEvent{

    use SerializesModels;

    public $payment = null;


    public function __construct(Payment $payment) {
        $this->payment = $payment;
    }
}