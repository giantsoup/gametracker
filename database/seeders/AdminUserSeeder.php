<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('HI0a69m5hL3muyK86hFhUb4n'),
            'role' => UserRole::ADMIN,
        ]);

        $this->command->info('Admin user created: admin@example.com / HI0a69m5hL3muyK86hFhUb4n');
    }
}
