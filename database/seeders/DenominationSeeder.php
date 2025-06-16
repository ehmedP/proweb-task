<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Denomination;
use Illuminate\Database\Seeder;

class DenominationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = Currency::query()->get();
        $denominationQuery = Denomination::query();

        $aznCurrency = $currencies->where('code', 'AZN')->first();
        $usdCurrency = $currencies->where('code', 'USD')->first();

        $aznDenominations = [
            ['value' => 200, 'count' => 100],
            ['value' => 100, 'count' => 150],
            ['value' => 50, 'count' => 200],
            ['value' => 20, 'count' => 300],
            ['value' => 10, 'count' => 400],
            ['value' => 5, 'count' => 500],
            ['value' => 1, 'count' => 1000],
        ];

        $usdDenominations = [
            ['value' => 100, 'count' => 50],
            ['value' => 50, 'count' => 100],
            ['value' => 20, 'count' => 150],
            ['value' => 10, 'count' => 200],
            ['value' => 5, 'count' => 300],
            ['value' => 1, 'count' => 500],
        ];

        foreach ($aznDenominations as $denomination) {
            $denominationQuery->firstOrCreate([
                'currency_id' => $aznCurrency->id,
                'value' => $denomination['value'],
                'count' => $denomination['count'],
                'is_active' => true,
            ]);
        }

        foreach ($usdDenominations as $denomination) {
            $denominationQuery->firstOrCreate([
                'currency_id' => $usdCurrency->id,
                'value' => $denomination['value'],
                'count' => $denomination['count'],
                'is_active' => true,
            ]);
        }
    }
}
