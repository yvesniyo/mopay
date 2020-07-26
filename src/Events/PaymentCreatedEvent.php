<?php
namespace Yves\Mopay\Events;

use Yves\Mopay\Models\Payment;

class PaymentCreatedEvent{

    public $payment = null;

    public function __construct(Payment $payment) {
        $this->payment = $payment;
    }
}