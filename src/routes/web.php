<?php
namespace Yves\Mopay\routes;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;


Route::namespace("\Yves\Mopay\Controllers")->group(function(){

    Route::group(["prefix"=>"mopay/payments"], function(){

        Route::get("/","PaymentsController@form")->name("mopay.payment.form");
        Route::post("/init","PaymentsController@initByForm")->name("mopay.payment.inititalize");
    
    });


    $webhook_url = Config::get("mopay.MOPAY_WEBHOOK", Config::get("app.url")."/mopay/payments/webhook");
    $webhook_url = Str::replaceFirst(Config::get("app.url"),"", $webhook_url);
    Route::get($webhook_url,"PaymentsController@webhook")->name("mopay.payment.webhook");


});


