<?php

namespace App\Services\AccountService;

use App\Data\AccountData;
use Illuminate\Support\Collection;

interface AccountServiceInterface
{

    public function getUserAccounts(int $userId): Collection;

    public function getAccountByNumber(string $accountNumber, int $userId): AccountData;

}
