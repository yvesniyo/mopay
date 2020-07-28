<?php
namespace Yves\Mopay\routes;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::namespace("\Yves\Mopay\Controllers")->group(function(){

    Route::group(["prefix"=>"mopay/payments"], function(){
        Route::get("/","PaymentsController@list")->name("mopay.payments.list");
        Route::get("/form","PaymentsController@form")->name("mopay.payment.form");
        Route::post("/init","PaymentsController@initByForm")->name("mopay.payment.inititalize");
    
    });
    $webhook_url = Config::get("mopay.MOPAY_WEBHOOK","/mopay/payments/webhook");
    Route::get($webhook_url,"PaymentsController@webhook")->name("mopay.payment.webhook");
    
});

