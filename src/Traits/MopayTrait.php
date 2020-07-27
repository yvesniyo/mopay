<?php

namespace Yves\Mopay\Traits;

use Yves\Mopay\Models\Payment;

trait MopayTrait{

    public function pay(float $amount,$phone=null): Payment{
        $primaryKey = $this->primaryKey;
        $context_model_id = $this->{$primaryKey};
        $context_model = get_class($this);
        $phone = $phone ?? $this->phone;
        $client_name = $this->name;
        $email = $this->email ?? null;

        $payment = Payment::request($amount,$phone,
        $client_name,null,null,$email,$context_model,$context_model_id);

        if($payment){
            return $payment;
        }
        return false;
    }


    public function payments()
    {
        $primaryKey = $this->primaryKey;
        $context_model_id = $this->{$primaryKey};
        $context_model = get_class($this);

        return Payment::where([
            "context_model_id"=> $context_model_id,
            "context_model"=> $context_model,
        ]);
    }

    public function pendingPayments()
    {
        $primaryKey = $this->primaryKey;
        $context_model_id = $this->{$primaryKey};
        $context_model = get_class($this);

        return Payment::where([
            "context_model_id"=> $context_model_id,
            "context_model"=> $context_model,
            "status"=> Payment::STATUS_PENDING,
        ]);
    }

    public function initiatedPayments()
    {
        $primaryKey = $this->primaryKey;
        $context_model_id = $this->{$primaryKey};
        $context_model = get_class($this);

        return Payment::where([
            "context_model_id"=> $context_model_id,
            "context_model"=> $context_model,
            "status"=> Payment::STATUS_INITIATED,
        ]);
    }

    public function completedPayments()
    {
        $primaryKey = $this->primaryKey;
        $context_model_id = $this->{$primaryKey};
        $context_model = get_class($this);

        return Payment::where([
            "context_model_id"=> $context_model_id,
            "context_model"=> $context_model,
            "status"=> Payment::STATUS_COMPLETE,
        ]);
    }

    public function failedPayments()
    {
        $primaryKey = $this->primaryKey;
        $context_model_id = $this->{$primaryKey};
        $context_model = get_class($this);

        return Payment::where([
            "context_model_id"=> $context_model_id,
            "context_model"=> $context_model,
            "status"=> Payment::STATUS_FAIL,
        ]);
    }

    public function createdPayments()
    {
        $primaryKey = $this->primaryKey;
        $context_model_id = $this->{$primaryKey};
        $context_model = get_class($this);

        return Payment::where([
            "context_model_id"=> $context_model_id,
            "context_model"=> $context_model,
            "status"=> Payment::STATUS_CREATED,
        ]);
    }
}