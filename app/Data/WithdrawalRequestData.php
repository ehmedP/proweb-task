<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class WithdrawalRequestData extends Data
{
    public function __construct(
        public string $account_number,
        public float $amount,
        public string $currency_code,
    ) {}
}
