<?php

namespace App\Exceptions;

use Exception;

class InsufficientDenominationsException extends Exception
{
    public function __construct(string $message = 'Insufficient denominations available.')
    {
        parent::__construct($message);
    }
}
