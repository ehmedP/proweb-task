<?php

namespace App\Http\Controllers\API;

use App\Data\WithdrawalRequestData;
use App\Exceptions\AccountNotFoundException;
use App\Exceptions\DailyLimitExceededException;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\InsufficientDenominationsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\WithdrawalRequest;
use App\Services\WithdrawalService\WithdrawalServiceInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class WithdrawalController extends Controller
{
    public function __construct(
        protected WithdrawalServiceInterface $withdrawalService
    ) {}

    public function withdraw(WithdrawalRequest $request): JsonResponse
    {
        try {
            $withdrawalData = WithdrawalRequestData::from($request->validated());
            $result = $this->withdrawalService->processWithdrawal($withdrawalData);

            return $this->success(
                $result,
                'Withdrawal completed successfully',
                Response::HTTP_CREATED
            );
        } catch (AccountNotFoundException $e) {
            return $this->notFound(
                $e->getMessage(),
            );
        } catch (
            InsufficientBalanceException |
            DailyLimitExceededException |
            InsufficientDenominationsException $e
        ) {
            return $this->error(
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        } catch (\Throwable $e) {
            return $this->error(
                'An error occurred during withdrawal',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
