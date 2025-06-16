<?php

namespace App\Services\WithdrawalService;

use App\Data\WithdrawalRequestData;
use App\Data\WithdrawalResponseData;
use App\Enums\TransactionStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Exceptions\AccountNotFoundException;
use App\Exceptions\DailyLimitExceededException;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\InsufficientDenominationsException;
use App\Repositories\AccountRepository\AccountRepositoryInterface;
use App\Repositories\CurrencyRepository\CurrencyRepositoryInterface;
use App\Repositories\DenominationRepository\DenominationRepositoryInterface;
use App\Repositories\TransactionRepository\TransactionRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class WithdrawalService extends BaseService implements WithdrawalServiceInterface
{
    public function __construct(
        protected AccountRepositoryInterface $accountRepository,
        protected TransactionRepositoryInterface $transactionRepository,
        protected DenominationRepositoryInterface $denominationRepository,
        protected CurrencyRepositoryInterface $currencyRepository,
    ) {}

    public function processWithdrawal(WithdrawalRequestData $data): WithdrawalResponseData
    {
        $startTime = microtime(true);

        return DB::transaction(function () use ($data, $startTime) {
            $account = throw_if_null(
                $this->accountRepository->findByAccountNumber($data->account_number),
                AccountNotFoundException::class
            );

            $account->resetDailyLimitIfNeeded();

            if (! $account->canWithdraw($data->amount)) {
                throw_if(
                    $account->balance < $data->amount,
                    InsufficientBalanceException::class
                );

                throw_if(
                    true,
                    DailyLimitExceededException::class,
                );
            }

            $denominationBreakdown = $this->calculateOptimalDenominations(
                $data->amount,
                $data->currency_code
            );

            throw_if(
                empty($denominationBreakdown),
                InsufficientDenominationsException::class,
                'Cannot dispense requested amount'
            );

            $balanceBefore = $account->balance;
            $balanceAfter = $balanceBefore - $data->amount;

            $transaction = $this->transactionRepository->create([
                'account_id' => $account->id,
                'type' => TransactionTypeEnum::WITHDRAWAL->value,
                'amount' => $data->amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'status' => TransactionStatusEnum::PENDING->value,
                'denomination_breakdown' => $denominationBreakdown,
                'description' => 'Withdrawal description...',
            ]);

            $this->accountRepository->updateBalance($account->id, $balanceAfter);
            $this->accountRepository->updateDailyWithdrawn($account->id, $data->amount);

            $this->denominationRepository->updateCounts(
                collect($denominationBreakdown)->pluck('count', 'denomination_id')->toArray()
            );

            $this->transactionRepository->updateStatus($transaction->id, TransactionStatusEnum::COMPLETED->value);

            $endTime = microtime(true);
            $latency = round(($endTime - $startTime) * 1000, 2);

            logger()->info('Withdrawal processed', [
                'transaction_id' => $transaction->transaction_id,
                'amount' => $data->amount,
                'latency_ms' => $latency,
            ]);

            return new WithdrawalResponseData(
                transaction_id: $transaction->transaction_id,
                amount: $data->amount,
                balance_before: $balanceBefore,
                balance_after: $balanceAfter,
                denomination_breakdown: $denominationBreakdown,
                status: TransactionStatusEnum::COMPLETED,
                processed_at: now()->toISOString(),
            );
        });
    }

    public function calculateOptimalDenominations(float $amount, string $currencyCode): array
    {
        $currency = $this->currencyRepository->findByCode($currencyCode);

        if (!$currency) {
            return [];
        }

        $denominations = $this->denominationRepository->getAvailableByCurrency($currency->id);
        $result = [];
        $remainingAmount = $amount;

        foreach ($denominations as $denomination) {
            if ($remainingAmount <= 0) {
                break;
            }

            $neededCount = intval($remainingAmount / $denomination->value);
            $availableCount = min($neededCount, $denomination->count);

            if ($availableCount > 0) {
                $result[] = [
                    'denomination_id' => $denomination->id,
                    'value' => $denomination->value,
                    'count' => $availableCount,
                    'total' => $denomination->value * $availableCount,
                ];

                $remainingAmount -= $denomination->value * $availableCount;
            }
        }

        if ($remainingAmount > 0.01) {
            return [];
        }

        return $result;
    }
}
