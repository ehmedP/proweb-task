<?php

namespace App\Exceptions;

use Exception;

class MaxAccountLimitExceededException extends Exception
{
    public function __construct(string $message = 'Maximum account limit exceeded')
    {
        parent::__construct($message);
    }
}
