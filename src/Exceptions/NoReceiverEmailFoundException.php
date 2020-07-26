<?php

namespace Yves\Mopay\Exceptions;

use Exception;
use Throwable;

class NoReceiverEmailFoundException extends Exception
{
    public $error = null;
    public function __construct($error) {
        $this->error = $error;
    }
    
    public function report()
    {
        
    }
    
    public function render()
    {
        
    }
}
