<?php
namespace Yves\Mopay\Utils;

class PaymentProductCart {

    public $name, $price,$description;

    public function __construct(string $name,float $price,string $description=null) {
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
    }
}