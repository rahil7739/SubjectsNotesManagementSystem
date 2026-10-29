<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = config('auth.admin_seed');
        $credentials = [$admin['name'], $admin['email'], $admin['password']];
        $providedCredentials = array_filter($credentials, fn (?string $value): bool => filled($value));

        if ($providedCredentials !== [] && count($providedCredentials) !== count($credentials)) {
            throw new RuntimeException('Set ADMIN_NAME, ADMIN_EMAIL, and ADMIN_PASSWORD together to seed an admin account.');
        }

        if ($providedCredentials === []) {
            return;
        }

        $user = User::firstOrNew(['email' => $admin['email']]);
        $user->name = $admin['name'];
        $user->password = $admin['password'];
        $user->role = RoleEnum::Admin;
        $user->save();
    }
}
