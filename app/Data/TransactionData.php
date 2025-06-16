<?php

namespace App\Data;

use App\Enums\TransactionStatusEnum;
use App\Enums\TransactionTypeEnum;
use Spatie\LaravelData\Data;

class TransactionData extends Data
{
    public function __construct(
        public string $transaction_id,
        public TransactionTypeEnum $type,
        public float $amount,
        public float $balance_before,
        public float $balance_after,
        public TransactionStatusEnum $status,
        public ?array $denomination_breakdown,
        public ?string $description,
        public string $created_at,
        public ?string $processed_at,
    ) {}
}
