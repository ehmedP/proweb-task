<?php

namespace App\Repositories\CurrencyRepository;

use App\Models\Currency;

interface CurrencyRepositoryInterface
{
    public function findByCode(string $currencyCode): ?Currency;

}
