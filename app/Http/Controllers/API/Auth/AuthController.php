<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UserAlreadyExistsException;
use App\Exceptions\TokenExpiredException;
use App\Services\AuthService\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(
        protected AuthServiceInterface $authService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();
            $result = $this->authService->login($credentials);

            return $this->success(
                $result,
                'Login successful',
            );
        } catch (InvalidCredentialsException $e) {
            return $this->unauthorized(
                $e->getMessage(),
            );
        } catch (\Throwable $e) {
            return $this->error(
                'An error occurred during login',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $userData = $request->validated();
            $result = $this->authService->register($userData);

            return $this->created(
                $result,
                'Registration successful',
            );
        } catch (UserAlreadyExistsException $e) {
            return $this->error(
                $e->getMessage(),
                Response::HTTP_CONFLICT
            );
        } catch (\Throwable $e) {
            dd($e->getMessage());
            return $this->error(
                'An error occurred during registration',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();

            return $this->success(
                message: 'Logout successful',
            );
        } catch (\Throwable $e) {
            return $this->error(
                'An error occurred during logout',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function profile(): JsonResponse
    {
        try {
            $userProfile = $this->authService->getUserProfile();

            return $this->success($userProfile);
        } catch (\Throwable $e) {
            return $this->error(
                'An error occurred while fetching profile',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function refresh(): JsonResponse
    {
        try {
            $result = $this->authService->refreshToken();

            return $this->success(
                $result,
                'Token refreshed successfully',
            );
        } catch (TokenExpiredException $e) {
            return $this->unauthorized(
                $e->getMessage(),
            );
        } catch (\Throwable $e) {
            return $this->error(
                'An error occurred during token refresh',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $passwordData = $request->validated();
            $this->authService->changePassword($passwordData);

            return $this->success(
                message: 'Password changed successfully',
            );
        } catch (InvalidCredentialsException $e) {
            return $this->unauthorized(
                $e->getMessage(),
            );
        } catch (\Throwable $e) {
            return $this->error(
                'An error occurred while changing password',
            );
        }
    }

    public function logoutAll(): JsonResponse
    {
        try {
            $this->authService->logoutFromAllDevices();

            return $this->success(
                message: 'Logged out from all devices successfully',
            );
        } catch (\Throwable $e) {
            return $this->error(
                'An error occurred during logout from all devices',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
