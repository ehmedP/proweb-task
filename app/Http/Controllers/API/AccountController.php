<?php

namespace App\Http\Controllers\API;

use App\Exceptions\AccountAlreadyExistsException;
use App\Exceptions\AccountNotFoundException;
use App\Exceptions\AccountPermissionDeniedException;
use App\Exceptions\CurrencyNotFoundException;
use App\Exceptions\MaxAccountLimitExceededException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAccountRequest;
use App\Services\AccountService\AccountServiceInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    public function __construct(
        protected AccountServiceInterface $accountService,
    ) {}

    public function index(): JsonResponse
    {
        try {
            $userId = auth()->id();
            $accountsData = $this->accountService->getUserAccounts($userId);

            return $this->success($accountsData);
        } catch (AccountNotFoundException $e) {
            return $this->notFound(
                $e->getMessage()
            );
        } catch (\Throwable $e) {
            return $this->error(
                'An error occurred while fetching accounts',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function show(string $accountNumber): JsonResponse
    {
        try {
            $userId = auth()->id();
            $accountData = $this->accountService->getAccountByNumber($accountNumber, $userId);

            return $this->success($accountData);
        } catch (AccountPermissionDeniedException $e) {
            return $this->forbidden(
                $e->getMessage()
            );
        } catch (AccountNotFoundException $e) {
            return $this->notFound(
                $e->getMessage()
            );
        } catch (\Throwable $e) {
            return $this->error(
                'An error occurred while fetching account details',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function store(CreateAccountRequest $request): JsonResponse
    {
        try {
            $accountData = $request->validated();
            $accountData['user_id'] = auth()->id();

            $result = $this->accountService->createAccount($accountData);

            return $this->created(
                $result,
                'Account created successfully'
            );
        } catch (AccountAlreadyExistsException $e) {
            return $this->error(
                $e->getMessage(),
                Response::HTTP_CONFLICT
            );
        } catch (CurrencyNotFoundException $e) {
            return $this->notFound(
                $e->getMessage()
            );
        } catch (MaxAccountLimitExceededException $e) {
            return $this->error(
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\Throwable $e) {
            return $this->error(
                'An error occurred while creating account',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

}
