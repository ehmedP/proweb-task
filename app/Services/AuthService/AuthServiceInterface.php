<?php

namespace App\Services\AuthService;

use App\Data\UserData;

interface AuthServiceInterface
{

    public function login(array $credentials): array;

    public function register(array $userData): array;

    public function logout(): void;

    public function getUserProfile(): UserData;

    public function refreshToken(): array;

    public function changePassword(array $passwordData): void;

    public function logoutFromAllDevices(): void;

    public function verifyToken(string $token): bool;

}
