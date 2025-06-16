<?php

namespace App\Services\AccountService;

use App\Data\AccountData;
use App\Exceptions\AccountAlreadyExistsException;
use App\Exceptions\AccountNotFoundException;
use App\Exceptions\AccountPermissionDeniedException;
use App\Exceptions\CurrencyNotFoundException;
use App\Exceptions\MaxAccountLimitExceededException;
use App\Repositories\AccountRepository\AccountRepositoryInterface;
use App\Repositories\CurrencyRepository\CurrencyRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\Collection;

class AccountService extends BaseService implements AccountServiceInterface
{
    public function __construct(
        protected CurrencyRepositoryInterface $currencyRepository,
        protected AccountRepositoryInterface $accountRepository
    ) {}


    public function getUserAccounts(int $userId): Collection
    {
        $accounts = $this->accountRepository->getUserAccounts($userId);

        throw_if(
            $accounts->isEmpty(),
            AccountNotFoundException::class
        );

        return $accounts->map(function ($account) {
            return AccountData::from([
                'id' => $account->id,
                'account_number' => $account->account_number,
                'balance' => $account->balance,
                'currency_code' => $account->currency->code,
                'currency_symbol' => $account->currency->symbol,
                'daily_limit' => $account->daily_limit,
                'daily_withdrawn' => $account->daily_withdrawn,
                'is_active' => $account->is_active,
            ]);
        });
    }

    public function createAccount(array $accountData): AccountData
    {
        $currency = throw_if_null(
            $this->currencyRepository->findByCode($accountData['currency_code']),
            CurrencyNotFoundException::class,
        );

         throw_if_not_null(
            $this->accountRepository->findByUserAndCurrency(
                $accountData['user_id'],
                $currency->id
            ),
            AccountAlreadyExistsException::class
        );

        throw_if(
            $this->accountRepository->getUserAccountsCount($accountData['user_id']) >= 5,
            MaxAccountLimitExceededException::class,
            'Maximum account limit exceeded. You can have maximum 5 accounts.',
        );

        $accountNumber = $this->generateAccountNumber();

        $newAccountData = [
            'user_id' => $accountData['user_id'],
            'currency_id' => $currency->id,
            'account_number' => $accountNumber,
            'balance' => $accountData['initial_balance'] ?? 0,
            'daily_limit' => $accountData['daily_limit'] ?? $currency->default_daily_limit,
            'daily_withdrawn' => 0,
            'is_active' => true,
            'last_withdrawal_date' => null,
        ];

        $account = $this->accountRepository->create($newAccountData);

        return AccountData::from([
            'id' => $account->id,
            'account_number' => $account->account_number,
            'balance' => $account->balance,
            'currency_code' => $currency->code,
            'currency_symbol' => $currency->symbol,
            'daily_limit' => $account->daily_limit,
            'daily_withdrawn' => $account->daily_withdrawn,
            'is_active' => $account->is_active,
        ]);
    }

    public function getAccountByNumber(string $accountNumber, int $userId): AccountData
    {
        $account = throw_if_null(
            $this->accountRepository->findByAccountNumber($accountNumber),
            AccountNotFoundException::class
        );

        throw_if(
            $account->user_id !== $userId,
            AccountPermissionDeniedException::class
        );

        return AccountData::from([
            'id' => $account->id,
            'account_number' => $account->account_number,
            'balance' => $account->balance,
            'currency_code' => $account->currency->code,
            'currency_symbol' => $account->currency->symbol,
            'daily_limit' => $account->daily_limit,
            'daily_withdrawn' => $account->daily_withdrawn,
            'is_active' => $account->is_active,
        ]);
    }

    private function generateAccountNumber(): string
    {
        do {
            $accountNumber = (string) mt_rand(0, 9999999999999999);
        } while ($this->accountRepository->findByAccountNumber($accountNumber));

        return $accountNumber;
    }
}
