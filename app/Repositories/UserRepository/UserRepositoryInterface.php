<?php

namespace App\Repositories\UserRepository;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{

    public function findByEmail(string $email): ?User;

    public function findActiveByEmail(string $email): ?User;

    public function create(array $userData): User;

    public function updatePassword(int $userId, string $hashedPassword): bool;

    public function updateProfile(int $userId, array $profileData): bool;

    public function toggleStatus(int $userId, bool $isActive): bool;

    public function getAllUsers(int $perPage = 15): LengthAwarePaginator;

    public function getUsersByRole(string $role): Collection;

}
