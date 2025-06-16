<?php

namespace App\Repositories\DenominationRepository;

use App\Models\Denomination;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class DenominationRepository extends BaseRepository implements DenominationRepositoryInterface
{

    public function __construct(Denomination $model)
    {
        parent::__construct($model);
    }

    public function getAvailableByCurrency(int $currencyId): Collection
    {
        return $this->query(useActiveScope: true)
            ->where('currency_id', $currencyId)
            ->available()
            ->orderBy('value', 'desc')
            ->get();
    }

    public function updateCounts(array $denominationCounts): bool
    {
        foreach ($denominationCounts as $denominationId => $count) {
            $this->query()
                ->where('id', $denominationId)
                ->decrement('count', $count);
        }

        return true;
    }
}
