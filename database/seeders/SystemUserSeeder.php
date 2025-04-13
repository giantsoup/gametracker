<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Random\RandomException;

class SystemUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws RandomException
     */
    public function run(): void
    {
        User::create([
            'id' => 1,
            'name' => 'System',
            'email' => 'system@gametracker.local',
            'password' => Hash::make(bin2hex(random_bytes(32))),
            'email_verified_at' => now(),
        ]);
    }
}
