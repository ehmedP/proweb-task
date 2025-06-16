<?php

namespace App\Exceptions;

use Exception;

class AccountPermissionDeniedException extends Exception
{
    public function __construct(string $message = 'Account permission denied.')
    {
        parent::__construct($message);
    }
}
