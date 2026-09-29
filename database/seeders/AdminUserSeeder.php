<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Creates the admin login. CHANGE THE PASSWORD after first login,
     * or edit the values below before seeding, then update env-based
     * credentials for production.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gurucrackers.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('ChangeThisPassword123'),
            ]
        );
    }
}
