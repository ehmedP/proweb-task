<?php

namespace App\Repositories\UserRepository;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->query()
            ->where('email', $email)
            ->first();
    }

    public function findActiveByEmail(string $email): ?User
    {
        return $this->query(useActiveScope: true)
            ->where('email', $email)
            ->first();
    }

    public function create(array $userData): User
    {
        return $this->query()->create($userData);
    }

    public function updatePassword(int $userId, string $hashedPassword): bool
    {
        return $this->query()
            ->where('id', $userId)
            ->update(['password' => $hashedPassword]);
    }

    public function updateProfile(int $userId, array $profileData): bool
    {
        return $this->query()
            ->where('id', $userId)
            ->update($profileData);
    }

    public function toggleStatus(int $userId, bool $isActive): bool
    {
        return $this->query()
            ->where('id', $userId)
            ->update(['is_active' => $isActive]);
    }

    public function getAllUsers(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getUsersByRole(string $role): Collection
    {
        return $this->query(useActiveScope: true)
            ->where('role', $role)
            ->orderBy('name')
            ->get();
    }
}
