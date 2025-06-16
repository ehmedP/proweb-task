<?php

namespace App\Exceptions;

use Exception;

class AccountAlreadyExistsException extends Exception
{
    public function __construct(string $message = 'Account already exists for this currency')
    {
        parent::__construct($message);
    }
}
