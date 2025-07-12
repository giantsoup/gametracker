<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\GameStatus;
use Illuminate\Database\Seeder;

class AssignDefaultGameStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the "Not Started" status
        $notStartedStatus = GameStatus::where('name', 'Not Started')->first();

        if (! $notStartedStatus) {
            $this->command->error('GameStatus "Not Started" not found. Please run GameStatusSeeder first.');

            return;
        }

        // Update all games that don't have a status assigned
        $updatedCount = Game::whereNull('status_id')
            ->update(['status_id' => $notStartedStatus->id]);

        $this->command->info("Assigned 'Not Started' status to {$updatedCount} games.");
    }
}
