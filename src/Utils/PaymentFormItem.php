<?php
namespace Yves\Mopay\Utils;

class PaymentFormItem {
    public $name;
    public $value;
    public $editable;

    public const AMOUNT = "amount";
    public const CURRENCY = "currency";
    public const CLIENT_NAME = "client_name";
    public const MSISDN = "msisdn";
    public const EMAIL = "email";


    public function __construct(string $name,$value=null,bool $editable=false) {
        $this->name = $name;
        $this->value = $value;
        $this->editable = $editable;
    }
}