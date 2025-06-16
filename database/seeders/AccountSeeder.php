<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accountQuery = Account::query();
        $currencies = Currency::query()->get();
        $user = User::query()->where('email', 'ahmad@mail.ru')->first();

        $aznCurrency = $currencies->where('code', 'AZN')->first();
        $usdCurrency = $currencies->where('code', 'USD')->first();

        $accountQuery->firstOrCreate([
            'user_id' => $user->id,
            'currency_id' => $aznCurrency->id,
            'account_number' => '12345678901234512345',
            'balance' => 5000.00,
            'daily_limit' => 1000.00,
            'is_active' => true,
        ]);

        $accountQuery->firstOrCreate([
            'user_id' => $user->id,
            'currency_id' => $usdCurrency->id,
            'account_number' => '1234567890123457890',
            'balance' => 2500.00,
            'daily_limit' => 500.00,
            'is_active' => true,
        ]);
    }
}
