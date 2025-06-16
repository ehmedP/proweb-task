<?php

namespace App\Enums;

enum TransactionStatusEnum: int
{
    case PENDING = 1;
    case COMPLETED = 2;
    case FAILED = 3;
    case CANCELLED = 4;

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Proses davam edir.',
            self::COMPLETED => 'Proses tamamlandı.',
            self::FAILED => 'Proses zamanı xəta yarandı.',
            self::CANCELLED => 'Proses ləğv edildi.',
        };
    }
}
