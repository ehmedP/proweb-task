<?php

namespace App\Providers;

use App\Repositories\AccountRepository\AccountRepository;
use App\Repositories\AccountRepository\AccountRepositoryInterface;
use App\Repositories\CurrencyRepository\CurrencyRepository;
use App\Repositories\CurrencyRepository\CurrencyRepositoryInterface;
use App\Repositories\DenominationRepository\DenominationRepository;
use App\Repositories\DenominationRepository\DenominationRepositoryInterface;
use App\Repositories\TransactionRepository\TransactionRepository;
use App\Repositories\TransactionRepository\TransactionRepositoryInterface;
use App\Repositories\UserRepository\UserRepository;
use App\Repositories\UserRepository\UserRepositoryInterface;
use App\Services\AccountService\AccountService;
use App\Services\AccountService\AccountServiceInterface;
use App\Services\AuthService\AuthService;
use App\Services\AuthService\AuthServiceInterface;
use App\Services\TransactionService\TransactionService;
use App\Services\TransactionService\TransactionServiceInterface;
use App\Services\WithdrawalService\WithdrawalService;
use App\Services\WithdrawalService\WithdrawalServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // repositories
        $this->app->singleton(AccountRepositoryInterface::class, AccountRepository::class);
        $this->app->singleton(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->singleton(DenominationRepositoryInterface::class, DenominationRepository::class);
        $this->app->singleton(CurrencyRepositoryInterface::class, CurrencyRepository::class);
        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);

        // services
        $this->app->bind(AccountServiceInterface::class, AccountService::class);
        $this->app->bind(WithdrawalServiceInterface::class, WithdrawalService::class);
        $this->app->bind(TransactionServiceInterface::class, TransactionService::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
