Laravel Mopay
==============

Package which can simplify developers work on their app which requires MTN Mobile Money payments in Rwanda, within the laravel application


## Installation

To get the latest version, simply require the project using [Composer](https://getcomposer.org).

```bash
$ composer require yves/mopay
```

Once installed, if you are not using automatic package discovery, then you need to register the `Yves\Mopay\Providers\MopayServiceProvider` service provider in your `config/app.php`.



## Configurations

Mopay requires connection configuration.

To get started, you'll need to publish all vendor assets:

```bash
$ php artisan mopay:install
```
This will create a `config\mopay.php` file in your app that you can modify to set your configuration. Also, make sure you check for changes to the original config file in this package between releases.

There are six config options:

##### MOPAY_API_URL

This by default there is mopay api v1  [Ishema Api](http://api.ishema.rw/api/v1/debit).And it is where payment requests will pass through as gateway.

##### MOPAY_API_TOKEN

Make sure you set this for the token you get from [Ishema Web Dashboard](http://api.ishema.rw) dashboard on profile part.

##### MOPAY_WEBHOOK

This is the postback url or known as webhook for your project which will receive the result of the payments that you requested.
so put real url, by default there is `http://app_url:port/mopay/payments/webhook"`.

##### MAIL_USERNAME

We did not forget those who wants to notify users view email for when there is status changes in their payments. To access it set your gmail or any email of your choosing here but also make sure that you configured mail, I mean like passwords and drivers of smtp. In development we used gmail and it did work great.

##### APP_NAME_ON_EMAILS

If you are using emails this values will be the one on emails as the sender.


##### USE_NEW_EMAIL_IN_CONTEXT_MODEL

Set this to `true` if you want to send emails from updated state in your current model, or `false` just to use the email that was used during the creation of payment.
default is `false`


##### config\mopay.php

```php
return [
    "MOPAY_API_URL" => env("MOPAY_API_URL","http://api.ishema.rw/api/v1/debit"),
    "MOPAY_API_TOKEN" => env("MOPAY_API_TOKEN"),
    "MOPAY_WEBHOOK" => env("MOPAY_WEBHOOK",env("APP_URL")."/mopay/payments/webhook"),
    "APP_NAME_ON_EMAILS"=>  env("APP_NAME", "MOPAY RWANDA"),
    "MAIL_USERNAME"=> env("MAIL_USERNAME"),
    "USE_NEW_EMAIL_IN_CONTEXT_MODEL"=> env("USE_NEW_EMAIL_IN_CONTEXT_MODEL", false),
];
```

## Initiate database table for payments

After installing publishables you need to migrate only one migrating of payments

```bash
$ php artisan migrate
```





## Usage

This package can be used as a library.

### Example: using the library

#### Initialize model for payable

if you wish to use it on your models just make sure that you have these columns:
1. `phone`.
2. `email` (OPTIONAL) only if you wish to send them emails.
3. `name` this will be taken as client name.

Include `Yves\Mopay\Traits\MopayTrait` trait in your model. like down below:
```php
<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Yves\Mopay\Traits\MopayTrait; // include this trait in your model you wish to be payable

class Passengers extends Model
{
    use MopayTrait;// use the trait

}

```

#### Access model in controller and initiate payments

Actually this is straight forward cause you just only need to call your model and call pay function.
like in this example we are going to initiate payment for 1,000Rwf in our controller for the passengers.

Note: Payments requires that you have queue worker or listener in background, so before you start
call this command in project directory:
```bash 
$ php artisan queue:work
```

```php
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Passenger;

class PassengerPaymentController extends Controller
{

    public function initiatePayment(Passenger $passenger){
        $payment = $passenger->pay(1000);// 1000 is the amount to be requested from this passenger
        if($payment){
            return "payment successfuly initiated";
        }
    }
}
```


#### Or you could show web interface to your clients for payment

For now only `phoneNumber` is capable of being changed on front view by

```php

<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Passenger;
use Yves\Mopay\Utils\PaymentCart;
use Yves\Mopay\Utils\PaymentForm;
use Yves\Mopay\Utils\PaymentFormItem;
use Yves\Mopay\Utils\PaymentProductCart;

class PassengerPaymentController extends Controller
{

    // show user a form to fill in phone number
    public function showPaymentForm(Request $request){

        //initialize payment form
        $paymentForm = new PaymentForm();

        // adding items
        $paymentForm->addItem(new PaymentFormItem(PaymentFormItem::AMOUNT,"1000"));
        $paymentForm->addItem(new PaymentFormItem(PaymentFormItem::CURRENCY,"Rwf"));
        $paymentForm->addItem(new PaymentFormItem(PaymentFormItem::MSISDN,"250783588655",false));
        $paymentForm->addItem(new PaymentFormItem(PaymentFormItem::CLIENT_NAME,"Mukunzi Joshua"));
        $paymentForm->addItem(new PaymentFormItem(PaymentFormItem::EMAIL,"mukunzi.joshua@gmail.com"));

        $paymentCart = new PaymentCart();// if you wish to show cart on the sibar if template  view
        $paymentCart->addProduct(new PaymentProductCart("Ticket",1000));// adding product to cart
        $paymentForm->setCart($paymentCart);// set payment cart

        return $paymentForm->view();// this will return view for the payment
    }

}

```

        

