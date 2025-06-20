<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Event;
use App\Models\Game;
use App\Models\Player;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds to create a demo dataset.
     */
    public function run(): void
    {
        $this->command->info('Creating demo data...');

        // Create demo users
        $this->createDemoUsers();

        // Create demo events
        $this->createDemoEvents();

        // Create demo games and players
        $this->createDemoGamesAndPlayers();

        // Create demo game points
        $this->createDemoGamePoints();

        $this->command->info('Demo data created successfully!');
        $this->command->info('You can now log in with:');
        $this->command->info('- Admin: admin@example.com / password');
        $this->command->info('- User: user@example.com / password');
    }

    /**
     * Create demo users.
     */
    private function createDemoUsers(): void
    {
        $this->command->info('Creating demo users...');

        // Create or update a demo admin user
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Demo Admin',
                'password' => Hash::make('password'),
                'role' => UserRole::ADMIN,
            ]
        );

        // Create or update a demo regular user
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
                'role' => UserRole::USER,
            ]
        );

        // Create or update additional demo users
        for ($i = 1; $i <= 5; $i++) {
            User::updateOrCreate(
                ['email' => "player$i@example.com"],
                [
                    'name' => "Player $i",
                    'password' => Hash::make('password'),
                    'role' => UserRole::USER,
                ]
            );
        }
    }

    /**
     * Create demo events.
     */
    private function createDemoEvents(): void
    {
        $this->command->info('Creating demo events...');

        // Create or update a past event
        Event::updateOrCreate(
            ['name' => 'Past Game Night'],
            [
                'active' => false,
                'starts_at' => now()->subDays(10),
                'ends_at' => now()->subDays(10)->addHours(4),
                'started_at' => now()->subDays(10),
                'ended_at' => now()->subDays(10)->addHours(4),
            ]
        );

        // Create or update an ongoing event
        Event::updateOrCreate(
            ['name' => 'Current Game Night'],
            [
                'active' => true,
                'starts_at' => now()->subHours(2),
                'ends_at' => now()->addHours(2),
                'started_at' => now()->subHours(2),
                'ended_at' => null,
            ]
        );

        // Create or update an upcoming event
        Event::updateOrCreate(
            ['name' => 'Future Game Night'],
            [
                'active' => true,
                'starts_at' => now()->addDays(3),
                'ends_at' => now()->addDays(3)->addHours(4),
                'started_at' => null,
                'ended_at' => null,
            ]
        );
    }

    /**
     * Create demo games and players.
     */
    private function createDemoGamesAndPlayers(): void
    {
        $this->command->info('Creating demo games and players...');

        $events = Event::all();
        $users = User::where('id', '>', 1)->get(); // Skip system user

        foreach ($events as $event) {
            // Create or update games for each event
            $gameCount = rand(3, 6);
            for ($i = 1; $i <= $gameCount; $i++) {
                $game = Game::updateOrCreate(
                    [
                        'name' => "Game $i for $event->name",
                        'event_id' => $event->id,
                    ],
                    [
                        'duration' => rand(30, 180), // 30 minutes to 3 hours
                    ]
                );

                // For active events, mark some games as upcoming by setting created_at to null
                if ($event->active && $i > $gameCount / 2) {
                    // Set created_at to null for the second half of games to mark them as upcoming
                    $game->created_at = null;
                    $game->save();
                }
            }

            // Add players to each event
            foreach ($users as $user) {
                // Randomly decide if this user participates in this event
                if (rand(0, 1)) {
                    $player = Player::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'event_id' => $event->id,
                        ],
                        [
                            'nickname' => rand(0, 1) ? 'Gamer '.substr($user->name, -1) : null,
                            'joined_at' => $event->started_at ?? ($event->starts_at < now() ? now() : null),
                            'left_at' => $event->ended_at,
                        ]
                    );

                    // Get games for this event
                    $games = $event->games()->inRandomOrder()->take(rand(1, 3))->get();

                    // For each game, add this player as an owner
                    foreach ($games as $game) {
                        $game->owners()->syncWithoutDetaching([$player->id]);
                    }
                }
            }
        }
    }

    /**
     * Create demo game points.
     */
    private function createDemoGamePoints(): void
    {
        $this->command->info('Creating demo game points...');

        // Get admin user for assigning points
        $admin = User::where('email', 'admin@example.com')->first();

        // Get past and current events (not future events)
        $events = Event::where('starts_at', '<=', now())->get();

        foreach ($events as $event) {
            // Get all games for this event
            $games = $event->games;

            foreach ($games as $game) {
                // Get all players (owners) for this game
                $players = $game->owners;

                // Skip if no players
                if ($players->isEmpty()) {
                    continue;
                }

                // Shuffle players to randomize placements
                $shuffledPlayers = $players->shuffle()->values();

                // Assign points based on placement
                foreach ($shuffledPlayers as $index => $player) {
                    // Only assign placements to the first 3 players
                    $placement = ($index < 3) ? $index + 1 : null;

                    // Determine points based on placement
                    $points = 0;
                    if ($placement === 1) {
                        $points = 5;
                    } elseif ($placement === 2) {
                        $points = 3;
                    } elseif ($placement === 3) {
                        $points = 1;
                    }

                    // Create or update game point
                    \App\Models\GamePoint::updateOrCreate(
                        [
                            'game_id' => $game->id,
                            'player_id' => $player->user_id,
                        ],
                        [
                            'points' => $points,
                            'placement' => $placement,
                            'assigned_by' => $admin->id,
                            'assigned_at' => $event->started_at ?? now(),
                            'last_modified_by' => null,
                            'last_modified_at' => null,
                        ]
                    );
                }
            }
        }
    }
}
