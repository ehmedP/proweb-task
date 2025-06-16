<?php

namespace App\Http\Controllers\API;

use App\Exceptions\AccountNotFoundException;
use App\Exceptions\AccountPermissionDeniedException;
use App\Exceptions\FailDeleteProcessException;
use App\Exceptions\TransactionNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionIndexRequest;
use App\Services\TransactionService\TransactionServiceInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    public function __construct(
        protected TransactionServiceInterface $transactionService,
    ) {}

    public function index(TransactionIndexRequest $request): JsonResponse
    {
        $userId = auth()->id();

        try {
            $transactions = $this->transactionService->getAccountTransactions(
                $request->get('account_number'),
                $userId,
                $request->get('per_page', 15)
            );

            return $this->success($transactions);
        } catch (AccountNotFoundException $e) {
            return $this->notFound(
                $e->getMessage()
            );
        }
        catch (AccountPermissionDeniedException $e) {
            return $this->forbidden(
                $e->getMessage()
            );
        } catch (\Throwable $e) {
            return $this->error(
                'An error occurred while fetching transactions.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function show(string $transactionId): JsonResponse
    {
        $userId = auth()->id();

        try {
            $transactionData = $this->transactionService->getTransactionById($transactionId, $userId);

            return $this->success($transactionData);
        } catch (TransactionNotFoundException $e) {
            $this->notFound(
                $e->getMessage()
            );
        } catch (AccountPermissionDeniedException $e) {
            return $this->forbidden(
                $e->getMessage()
            );
        } catch (\Throwable $e) {
            return $this->error(
                'An error occurred while fetching the transaction.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function destroy(string $transactionId): JsonResponse
    {
        try {
            $this->transactionService->deleteTransaction($transactionId);
        } catch (TransactionNotFoundException $e) {
            return $this->notFound(
                $e->getMessage()
            );
        } catch (FailDeleteProcessException $e) {
            return $this->error(
                $e->getMessage()
            );
        } catch (\Throwable $e) {
            return $this->error(
                'An error occurred during transaction',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->success(message: 'Transaction deleted successfully');
    }
}
