<?php

namespace App\Exceptions;

use Exception;

class CurrencyNotFoundException extends Exception
{
    public function __construct(string $message = 'Currency not found')
    {
        parent::__construct($message);
    }
}
