<?php

namespace Yves\Mopay\Utils;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

class PaymentForm{

    public $items;
    public $cart  = null;

    public function __construct() {
        $this->items = collect([
            new PaymentFormItem(PaymentFormItem::AMOUNT,0),
            new PaymentFormItem(PaymentFormItem::CURRENCY),
            new PaymentFormItem(PaymentFormItem::CLIENT_NAME),
            new PaymentFormItem(PaymentFormItem::MSISDN,null, Config::get("mopay.DEFAULT_PHONE_NUMBER_EDITABLE")),
            new PaymentFormItem(PaymentFormItem::EMAIL)
        ]);
    }

    public function addItem(PaymentFormItem $item){
        $this->items = $this->items->map(function($filter,$key) use ($item){
            if($item->name == $filter->name){
                return $item;
            }
            return $filter;
        });
    }


    public function items()
    {
        return $this->items;
    }

    public function editableItems(){
        return $this->items->where("editable","=", true);
    }


    public function item(string $name){
        return $this->items->where("name","=", $name)->first();
    }


    public function setCart(PaymentCart $paymentCart){
        $this->cart = $paymentCart;
    }


    public function view(){
        return view("mopay::form",[
            "form"=> $this,
            "extra"=> encrypt($this),
        ]);
    }


}