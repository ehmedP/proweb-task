<?php

namespace App\Repositories\AccountRepository;

use App\Models\Account;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class AccountRepository extends BaseRepository implements AccountRepositoryInterface
{

    public function __construct(Account $model) {
        parent::__construct($model);
    }

    public function findByAccountNumber(string $accountNumber): ?Account
    {
        return $this->query(['user', 'currency'], true)
            ->where('account_number', $accountNumber)
            ->first();
    }

    public function findByUserAndCurrency(int $userId, int $currencyId): ?Account
    {
        return $this->query(['user', 'currency'], true)
            ->where('user_id', $userId)
            ->where('currency_id', $currencyId)
            ->first();
    }

    public function getUserAccounts(int $userId): Collection
    {
        return $this->query(['user', 'currency'], true)
            ->where('user_id', $userId)
            ->get();
    }

    public function updateBalance(int $accountId, float $newBalance): bool
    {
        return $this->query()
            ->where('id', $accountId)
            ->update(['balance' => $newBalance]);
    }

    public function updateDailyWithdrawn(int $accountId, float $amount): bool
    {
        $account = $this->query()->find($accountId);
        $today = now()->toDateString();

        if ($account->last_withdrawal_date !== $today) {
            return $account->update([
                'daily_withdrawn' => $amount,
                'last_withdrawal_date' => $today,
            ]);
        }

        return $account->increment('daily_withdrawn', $amount);
    }

    public function getUserAccountsCount(int $userId): int
    {
        return $this->query()
            ->where('user_id', $userId)
            ->count();
    }

    public function create(array $accountData): Account
    {
        return $this->query()
            ->create($accountData);
    }

}
