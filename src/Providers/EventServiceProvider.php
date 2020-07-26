<?php
namespace Yves\Mopay\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Yves\Mopay\Events\PaymentCompletedEvent;
use Yves\Mopay\Events\PaymentCreatedEvent;
use Yves\Mopay\Events\PaymentFailedEvent;
use Yves\Mopay\Events\PaymentInitializedEvent;
use Yves\Mopay\Events\PaymentPendingEvent;
use Yves\Mopay\Listeners\RequestClientPaymentListener;
use Yves\Mopay\Listeners\SendClientMailPaymentCompleteListener;


class EventServiceProvider extends ServiceProvider{
    
    protected $listen = [
        PaymentCreatedEvent::class => [
            RequestClientPaymentListener::class,
        ],
        PaymentInitializedEvent::class => [
            
        ],
        PaymentPendingEvent::class => [

        ],
        PaymentCompletedEvent::class => [
            SendClientMailPaymentCompleteListener::class
        ],
        PaymentFailedEvent::class => [

        ]
    ];

    public function register()
    {
        
    }


    public function boot()
    {
        parent::boot();
    }
}