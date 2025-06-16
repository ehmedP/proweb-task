<?php

namespace App\Services\WithdrawalService;

use App\Data\WithdrawalRequestData;
use App\Data\WithdrawalResponseData;

interface WithdrawalServiceInterface
{
    public function processWithdrawal(WithdrawalRequestData $data): WithdrawalResponseData;
    
    public function calculateOptimalDenominations(float $amount, string $currencyCode): array;
}
