<?php

namespace App\Repositories\DenominationRepository;

use Illuminate\Database\Eloquent\Collection;

interface DenominationRepositoryInterface
{
    public function getAvailableByCurrency(int $currencyId): Collection;

    public function updateCounts(array $denominationCounts): bool;
}
