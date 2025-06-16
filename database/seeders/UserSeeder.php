<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleQuery = Role::query();
        $userQuery = User::query();

        $adminRole = $roleQuery->firstOrCreate(['name' => RoleEnum::ADMIN->value]);
        $userRole = $roleQuery->firstOrCreate(['name' => RoleEnum::USER->value]);

        $admin = $userQuery->firstOrCreate([
            'name' => 'Admin User',
            'email' => 'admin@mail.ru',
            'password' => Hash::make('password12345'),
        ]);
        $admin->assignRole($adminRole);

        $user = $userQuery->firstOrCreate([
            'name' => 'Ahmad',
            'email' => 'ahmad@mail.ru',
            'password' => Hash::make('password12345'),
        ]);
        $user->assignRole($userRole);
    }
}
