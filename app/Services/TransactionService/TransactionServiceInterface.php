<?php

namespace App\Services\TransactionService;

use App\Data\TransactionData;
use Illuminate\Pagination\LengthAwarePaginator;

interface TransactionServiceInterface
{
    public function getAccountTransactions(string $accountNumber, int $userId, int $perPage = 15): LengthAwarePaginator;

    public function getTransactionById(string $transactionId, int $userId): TransactionData;

    public function deleteTransaction(string $transactionId): bool;

}
