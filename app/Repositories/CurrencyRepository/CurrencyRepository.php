<?php

namespace App\Repositories\CurrencyRepository;

use App\Models\Currency;
use App\Repositories\BaseRepository;

class CurrencyRepository extends BaseRepository implements CurrencyRepositoryInterface
{

    public function __construct(Currency $model)
    {
        parent::__construct($model);
    }

    public function findByCode(string $currencyCode): ?Currency
    {
        return $this->query(useActiveScope: true)
            ->where('code', $currencyCode)
            ->first();
    }

}
