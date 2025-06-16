<?php

namespace App\Repositories\TransactionRepository;

use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TransactionRepositoryInterface
{
    public function create(array $data): Transaction;

    public function findById(int $id): ?Transaction;

    public function findByTransactionId(string $transactionId): ?Transaction;

    public function getAccountTransactions(int $accountId, int $perPage = 15): LengthAwarePaginator;

    public function updateStatus(int $transactionId, int $status): bool;

    public function deleteTransaction(int $transactionId): bool;
}
