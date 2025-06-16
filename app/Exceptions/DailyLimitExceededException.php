<?php

namespace App\Exceptions;

use Exception;

class DailyLimitExceededException extends Exception
{
    public function __construct(string $message = 'Daily withdrawal limit exceeded.')
    {
        parent::__construct($message);
    }
}
