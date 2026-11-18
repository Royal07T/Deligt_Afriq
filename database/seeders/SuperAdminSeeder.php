<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\Access\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SUPER_ADMIN_EMAIL');
        $password = env('SUPER_ADMIN_PASSWORD');

        if (! $email || ! $password) {
            Log::warning('Super administrator was not seeded because SUPER_ADMIN_EMAIL or SUPER_ADMIN_PASSWORD is missing.');

            return;
        }

        $admin = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => env('SUPER_ADMIN_NAME', 'Delight Afriq Administrator'),
                'password' => Hash::make($password),
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        $admin->syncRoles([Roles::SUPER_ADMIN]);
    }
}
