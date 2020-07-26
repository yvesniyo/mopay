<?php
namespace Yves\Mopay\Events;

use Yves\Mopay\Models\Payment;

class PaymentCompletedEvent{

    public $payment = null;
    public $sync_email = false;

    public function __construct(Payment $payment, $sync_email=false) {
        $this->payment = $payment;
        $this->sync_email = $sync_email;
    }
}