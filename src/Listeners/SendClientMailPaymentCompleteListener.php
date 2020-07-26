<?php

namespace Yves\Mopay\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Yves\Mopay\Events\PaymentCompletedEvent;
use Yves\Mopay\Exceptions\NoReceiverEmailFoundException;

class SendClientMailPaymentCompleteListener implements ShouldQueue {

    public function handle(PaymentCompletedEvent $event) 
    {
        $payment = $event->payment;
        $use_new_email = Config::get("mopay.USE_NEW_EMAIL_IN_CONTEXT_MODEL");
        $name  = $payment->client_name;
        $email = null;
        if($use_new_email){
            if($payment->context_model != null){
                $model = ($payment->context_model)::find($payment->context_model_id);
                $email = $model->email;
            }
        }else{
            $email = $payment->email ?? null;
        }
        
        if($email == null){
            throw new NoReceiverEmailFoundException("There is no receiver's email address");
        }

        $phone = $payment->msisdn;
        $amount = $payment->amount ." Rwf";
        $date = $payment->created_at->format("l, d/m/Y h:i A");
        $subject = "Mopay Payment Completed";

        $from_email = Config::get("mopay.MAIL_USERNAME");
        $from_name = Config::get("mopay.APP_NAME_ON_EMAILS");

        Mail::send(
            'mopay::email.payment_complete',
            ['name' => $name, 'phone' => $phone,"amount"=> $amount,"date"=> $date],
            function ($mail) use ($from_email,$from_name,$email, $name, $subject) {
                $mail->from($from_email, $from_name);
                $mail->to($email, $name);
                $mail->subject($subject);
            }
        );

    }

}