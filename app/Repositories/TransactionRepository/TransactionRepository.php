<?php

namespace App\Repositories\TransactionRepository;

use App\Models\Transaction;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TransactionRepository extends BaseRepository implements TransactionRepositoryInterface
{

    public function __construct(Transaction $model) {
        parent::__construct($model);
    }

    public function create(array $data): Transaction
    {
        return $this->query()->create($data);
    }

    public function findById(int $id): ?Transaction
    {
        return $this->query(['account.currency', 'account.user'])
            ->find($id);
    }

    public function findByTransactionId(string $transactionId): ?Transaction
    {
        return $this->query(['account.currency', 'account.user'])
            ->where('transaction_id', $transactionId)
            ->first();
    }

    public function getAccountTransactions(int $accountId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->query(['account.currency'])
            ->where('account_id', $accountId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function updateStatus(int $transactionId, int $status): bool
    {
        return $this->query()
            ->where('id', $transactionId)
            ->update([
                'status' => $status,
                'processed_at' => now(),
            ]);
    }

    public function deleteTransaction(int $transactionId): bool
    {
        return $this->query()
            ->where('id', $transactionId)
            ->delete();
    }
}
