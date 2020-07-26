<?php
namespace Yves\Mopay\Utils;

class PaymentCart {
    public $products;

    public function __construct() {
        $this->products = collect();
    }
    
    public function addProduct(PaymentProductCart $paymentProductCart) {
        $this->products->add($paymentProductCart);
    }

    public function products(){
        return $this->products;
    }

    public function getTotalPrice(): float{
        return $this->products->sum("price");
    }

    public function getTotalTax(): float{
        return $this->products->sum("price");
    }
}