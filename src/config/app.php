<?php


return [
    "MOPAY_API_URL" => env("MOPAY_API_URL","http://api.ishema.rw/api/v1/debit"),
    "MOPAY_API_TOKEN" => env("MOPAY_API_TOKEN","KNxnrqgFxmYzC64XkEjdnX6yV5Gox4"),
    "MOPAY_WEBHOOK" => env("MOPAY_WEBHOOK",env("APP_URL")."/mopay/payments/webhook"),
    "APP_NAME_ON_EMAILS"=>  env("APP_NAME", "MOPAY RWANDA"),
    "MAIL_USERNAME"=> env("MAIL_USERNAME"),
    "USE_NEW_EMAIL_IN_CONTEXT_MODEL"=> env("USE_NEW_EMAIL_IN_CONTEXT_MODEL", false),
    "DEFAULT_PHONE_NUMBER_EDITABLE"=> env("DEFAULT_PHONE_NUMBER_EDITABLE", true),
];