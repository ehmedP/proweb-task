<?php

namespace App\Exceptions;

use Exception;

class TransactionNotFoundException extends Exception
{
    public function __construct(string $message = 'Transaction not found.')
    {
        parent::__construct($message);
    }
}
