<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencyQuery = Currency::query();

        $currencies = [
            [
                'code' => 'AZN',
                'name' => 'Manat',
                'symbol' => '₼',
                'exchange_rate' => 1.0000,
                'is_active' => true,
            ],
            [
                'code' => 'USD',
                'name' => 'Dollar',
                'symbol' => '$',
                'exchange_rate' => 1.7000,
                'is_active' => true,
            ],
        ];

        foreach ($currencies as $currency) {
            $currencyQuery->firstOrCreate($currency);
        }
    }
}
