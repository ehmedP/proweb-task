<?php

namespace App\Exceptions;

use Exception;

class AccountNotFoundException extends Exception
{
    public function __construct(string $message = 'Account not found.')
    {
        parent::__construct($message);
    }
}
