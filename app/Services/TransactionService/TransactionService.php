<?php

namespace App\Services\TransactionService;

use App\Data\TransactionData;
use App\Enums\TransactionStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Exceptions\AccountNotFoundException;
use App\Exceptions\AccountPermissionDeniedException;
use App\Exceptions\FailDeleteProcessException;
use App\Exceptions\TransactionNotFoundException;
use App\Repositories\AccountRepository\AccountRepositoryInterface;
use App\Repositories\TransactionRepository\TransactionRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionService extends BaseService implements TransactionServiceInterface
{
    public function __construct(
        protected TransactionRepositoryInterface $transactionRepository,
        protected AccountRepositoryInterface $accountRepository
    ) {}

    public function getAccountTransactions(string $accountNumber, int $userId, int $perPage = 15): LengthAwarePaginator
    {
        $account = throw_if_null(
            $this->accountRepository->findByAccountNumber($accountNumber),
            AccountNotFoundException::class
        );

        throw_if(
            $account->user_id !== $userId,
            AccountPermissionDeniedException::class
        );

        $transactions = $this->transactionRepository->getAccountTransactions($account->id, $perPage);

        $transactionsDto = $transactions->getCollection()->map(fn ($t) => TransactionData::from([
            'transaction_id' => $t->transaction_id,
            'type' => TransactionTypeEnum::from($t->type),
            'amount' => $t->amount,
            'balance_before' => $t->balance_before,
            'balance_after' => $t->balance_after,
            'status' => TransactionStatusEnum::from($t->status),
            'denomination_breakdown' => $t->denomination_breakdown,
            'description' => $t->description,
            'created_at' => $t->created_at->toISOString(),
            'processed_at' => $t->processed_at?->toISOString(),
        ]));

        return $transactions->setCollection($transactionsDto);
    }

    public function getTransactionById(string $transactionId, int $userId): TransactionData
    {
        $transaction = throw_if_null(
            $this->transactionRepository->findByTransactionId($transactionId),
            TransactionNotFoundException::class
        );

        throw_if(
            $transaction->account->user_id !== $userId,
            AccountPermissionDeniedException::class
        );

        return TransactionData::from([
            'transaction_id' => $transaction->transaction_id,
            'type' => TransactionTypeEnum::from($transaction->type),
            'amount' => $transaction->amount,
            'balance_before' => $transaction->balance_before,
            'balance_after' => $transaction->balance_after,
            'status' => TransactionStatusEnum::from($transaction->status),
            'denomination_breakdown' => $transaction->denomination_breakdown,
            'description' => $transaction->description,
            'created_at' => $transaction->created_at->toISOString(),
            'processed_at' => $transaction->processed_at?->toISOString(),
        ]);
    }


    public function deleteTransaction(string $transactionId): bool
    {
        $transaction = throw_if_null(
            $this->transactionRepository->findByTransactionId($transactionId),
            TransactionNotFoundException::class
        );

        $deleted = $this->transactionRepository->deleteTransaction($transaction->id);

        throw_unless($deleted, FailDeleteProcessException::class);

        return true;
    }
}
