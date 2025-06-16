<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class AccountData extends Data
{
    public function __construct(
        public int $id,
        public string $account_number,
        public float $balance,
        public string $currency_code,
        public string $currency_symbol,
        public float $daily_limit,
        public float $daily_withdrawn,
        public bool $is_active,
    ) {}
}
