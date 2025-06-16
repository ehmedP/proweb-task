<?php

namespace App\Services\AuthService;

use App\Data\UserData;
use App\Enums\RoleEnum;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UserAlreadyExistsException;
use App\Repositories\UserRepository\UserRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService extends BaseService implements AuthServiceInterface
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function login(array $credentials): array
    {
        throw_unless(
            Auth::attempt($credentials),
            InvalidCredentialsException::class
        );

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => UserData::from([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames()->toArray(),
                'created_at' => $user->created_at,
            ]),
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration')
        ];
    }

    public function register(array $userData): array
    {
        throw_if_not_null(
            $this->userRepository->findByEmail($userData['email']),
            UserAlreadyExistsException::class,
            ['User with this email already exists']
        );

        $userData['password'] = Hash::make($userData['password']);

        $user = $this->userRepository->create($userData);

        $user->assignRole(RoleEnum::USER->value);

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => UserData::from([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames()->toArray(),
                'created_at' => $user->created_at,
            ]),
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration')
        ];
    }

    public function logout(): void
    {
        $user = Auth::user();

        $user->currentAccessToken()->delete();
    }

    public function getUserProfile(): UserData
    {
        $user = Auth::user();

        return UserData::from([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames()->toArray(),
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }

    public function refreshToken(): array
    {
        $user = Auth::user();

        $user->currentAccessToken()->delete();

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => UserData::from([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames()->toArray(),
                'created_at' => $user->created_at,
            ]),
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration')
        ];
    }

    public function changePassword(array $passwordData): void
    {
        $user = Auth::user();

        throw_unless(
            Hash::check($passwordData['current_password'], $user->password),
            InvalidCredentialsException::class,
            'Current password is incorrect'
        );

        $this->userRepository->updatePassword(
            $user->id,
            Hash::make($passwordData['new_password'])
        );
    }

    public function logoutFromAllDevices(): void
    {
        $user = Auth::user();

        $user->tokens()->delete();
    }

    public function verifyToken(string $token): bool
    {
        $accessToken = PersonalAccessToken::findToken($token);

        return $accessToken && !$accessToken->tokenCan('expired');
    }
}
