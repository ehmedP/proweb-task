<?php

namespace App\Data;

use App\Enums\TransactionStatusEnum;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapOutputName;

class WithdrawalResponseData extends Data
{
    public function __construct(
        public string $transaction_id,
        public float $amount,
        public float $balance_before,
        public float $balance_after,
        public array $denomination_breakdown,
        public TransactionStatusEnum $status,
        public string $processed_at,
    ) {}

    #[MapOutputName('status_label')]
    public function getStatusLabel(): string
    {
        return $this->status->label();
    }
}
