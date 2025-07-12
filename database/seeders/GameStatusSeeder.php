<?php

namespace Database\Seeders;

use App\Models\GameStatus;
use Illuminate\Database\Seeder;

class GameStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Not Started',
                'description' => 'Game has not been started yet.',
                'color' => '#6B7280', // Gray
                'is_active' => true,
            ],
            [
                'name' => 'In Progress',
                'description' => 'Game is currently being played.',
                'color' => '#3B82F6', // Blue
                'is_active' => true,
            ],
            [
                'name' => 'Finished',
                'description' => 'Game has been completed successfully.',
                'color' => '#10B981', // Green
                'is_active' => true,
            ],
            [
                'name' => 'Cancelled',
                'description' => 'Game was cancelled and did not complete.',
                'color' => '#EF4444', // Red
                'is_active' => true,
            ],
        ];

        foreach ($statuses as $status) {
            GameStatus::firstOrCreate(
                ['name' => $status['name']],
                $status
            );
        }
    }
}
