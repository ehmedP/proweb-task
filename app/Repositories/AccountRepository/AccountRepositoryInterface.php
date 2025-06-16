<?php

namespace App\Repositories\AccountRepository;

use App\Models\Account;
use Illuminate\Database\Eloquent\Collection;

interface AccountRepositoryInterface
{
    public function findByAccountNumber(string $accountNumber): ?Account;

    public function findByUserAndCurrency(int $userId, int $currencyId): ?Account;

    public function getUserAccounts(int $userId): Collection;

    public function updateBalance(int $accountId, float $newBalance): bool;

    public function updateDailyWithdrawn(int $accountId, float $amount): bool;

    public function create(array $accountData): Account;

}
