<?php

namespace App\Enums;

enum TransactionTypeEnum: int
{
    case WITHDRAWAL = 1;
    case DEPOSIT = 2;
    case TRANSFER = 3;

    public function label(): string
    {
        return match($this) {
            self::WITHDRAWAL => 'Nağdlaşdırma',
            self::DEPOSIT => 'Depozit',
            self::TRANSFER => 'Transfer.',
        };
    }
}
