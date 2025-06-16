<?php

namespace App\Exceptions;

use Exception;

class TokenExpiredException extends Exception
{
    public function __construct(string $message = 'Token has expired.')
    {
        parent::__construct($message);
    }
}
