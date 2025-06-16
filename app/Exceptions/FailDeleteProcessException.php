<?php

namespace App\Exceptions;

use Exception;

class FailDeleteProcessException extends Exception
{
    public function __construct(string $message = 'Delete process failed.')
    {
        parent::__construct($message);
    }
}
