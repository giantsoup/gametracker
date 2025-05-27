<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SystemUserSeeder::class,
            AdminUserSeeder::class,
            // DemoDataSeeder is not called by default to avoid populating
            // the database with demo data during regular seeding
            // It is called explicitly by the setup-demo-data command
        ]);
    }
}
