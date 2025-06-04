<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Event;
use App\Models\Game;
use App\Models\GamePoint;
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
        $this->command->info('- Admin: helper@example.com / password');
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
            ['email' => 'helper@example.com'],
            [
                'name' => 'Helper Admin',
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

        // Create or update 14 normal users with realistic American names (7 male, 7 female)
        $maleNames = [
            'James Smith',
            'Michael Johnson',
            'Robert Williams',
            'David Brown',
            'William Jones',
            'Richard Davis',
            'Thomas Miller',
        ];

        $femaleNames = [
            'Jennifer Garcia',
            'Sarah Rodriguez',
            'Jessica Martinez',
            'Emily Wilson',
            'Ashley Anderson',
            'Elizabeth Taylor',
            'Samantha Thomas',
        ];

        // Create male users
        foreach ($maleNames as $index => $name) {
            $emailPrefix = strtolower(explode(' ', $name)[0]);
            User::updateOrCreate(
                ['email' => "{$emailPrefix}@example.com"],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role' => UserRole::USER,
                ]
            );
        }

        // Create female users
        foreach ($femaleNames as $index => $name) {
            $emailPrefix = strtolower(explode(' ', $name)[0]);
            User::updateOrCreate(
                ['email' => "{$emailPrefix}@example.com"],
                [
                    'name' => $name,
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
                // Create different points distributions for variety
                $pointsDistributions = [
                    // Default distribution (5, 3, 1)
                    null,
                    // Custom distribution with more recipients
                    [1 => 7, 2 => 5, 3 => 3, 4 => 1],
                    // Custom distribution with different point values
                    [1 => 10, 2 => 5, 3 => 2],
                    // Custom distribution with equal points
                    [1 => 3, 2 => 3, 3 => 3],
                ];

                // Randomly select a points distribution or use null for default
                $pointsDistribution = $pointsDistributions[array_rand($pointsDistributions)];

                // Calculate total points and recipients based on the distribution
                $totalPoints = $pointsDistribution ? array_sum($pointsDistribution) : 9; // Default is 9 (5+3+1)
                $pointsRecipients = $pointsDistribution ? count($pointsDistribution) : 3; // Default is 3

                // Sample game descriptions and rules
                $gameDescriptions = [
                    'A strategic board game where players compete to build settlements, cities, and roads on the island of Catan.',
                    'A cooperative card game where players work together to defeat the ancient ones before time runs out.',
                    'A deck-building game where players assume the role of a monarch, using their cards to perform actions.',
                    'A tile-placement game where players compete to build the most valuable road networks, cities, and fields.',
                    'A worker placement game where players take on the roles of ancient civilizations building their empires.',
                    'A party game where players create funny and absurd card combinations to win points.',
                ];

                $gameRules = [
                    "1. Setup the board with hexagonal terrain tiles.\n2. Each player starts with 2 settlements and 2 roads.\n3. On your turn, roll dice for resource production, then trade and build.\n4. First player to reach 10 victory points wins.",
                    "1. Choose your investigator and ancient one.\n2. Setup the game board with monsters and gates.\n3. Each round consists of movement, encounters, and mythos phases.\n4. Seal gates or defeat the ancient one to win.",
                    "1. Start with 7 cards in hand and 3 estates and 7 coppers in your deck.\n2. On your turn: play action cards, buy cards, cleanup.\n3. Game ends when Province pile or any 3 piles are empty.\n4. Player with most victory points wins.",
                    "1. Draw and place a tile on your turn.\n2. Place a meeple on the tile if desired.\n3. Score completed features.\n4. Most points at the end wins.",
                    "1. Place workers on the board to gather resources.\n2. Use resources to build structures and advance your civilization.\n3. Feed your population each round.\n4. Most victory points at game end wins.",
                    "1. Each player starts with 10 white cards.\n2. One player is the judge and plays a black card.\n3. Other players submit a white card to complete the phrase.\n4. Judge picks the funniest combination, and that player gets a point.",
                ];

                $game = Game::updateOrCreate(
                    [
                        'name' => "Game $i for $event->name",
                        'event_id' => $event->id,
                    ],
                    [
                        'description' => $gameDescriptions[array_rand($gameDescriptions)],
                        'rules' => $gameRules[array_rand($gameRules)],
                        'duration' => [30, 45, 60][array_rand([30, 45, 60])], // Only 30, 45, or 60 minutes
                        'total_points' => $totalPoints,
                        'points_recipients' => $pointsRecipients,
                        'points_distribution' => $pointsDistribution,
                        'status' => \App\Enums\GameStatus::Ready,
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
                    // Gaming-related nicknames that sound more natural
                    $nicknames = [
                        'DiceRoller',
                        'StrategyMaster',
                        'CardShark',
                        'BoardGameGeek',
                        'MeepleManiac',
                        'VictoryPoint',
                        'TableTop',
                        'GameMaster',
                        'TokenCollector',
                        'RuleKeeper',
                        'FirstPlayer',
                        'LuckyRoll',
                        'ChessPro',
                        'GameWizard',
                    ];

                    $player = Player::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'event_id' => $event->id,
                        ],
                        [
                            'nickname' => rand(0, 1) ? $nicknames[array_rand($nicknames)] : null,
                            'joined_at' => $event->started_at ?? ($event->starts_at < now() ? now() : null),
                            'left_at' => $event->ended_at,
                        ]
                    );
                }
            }

            // Assign owners to games (max 2 owners per game)
            $games = $event->games;
            $eventPlayers = $event->players;

            if ($eventPlayers->isNotEmpty()) {
                foreach ($games as $game) {
                    // Determine number of owners (1 or 2)
                    $numOwners = rand(1, 2);

                    // Get random players for this game
                    $gameOwners = $eventPlayers->shuffle()->take($numOwners);

                    // Clear existing owners and assign new ones
                    $game->owners()->detach();

                    // Assign owners to the game
                    foreach ($gameOwners as $owner) {
                        $game->owners()->attach($owner->id);
                    }

                    // For the current game in the current event, add all players as active players
                    if ($event->name === 'Current Game Night' && $game === $event->games()->latest()->first()) {
                        // Clear existing players
                        $game->players()->detach();

                        // Add all event players to the game
                        foreach ($eventPlayers as $player) {
                            $game->players()->attach($player->id);
                        }
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
                    // Only assign placements to players based on the game's points_recipients
                    $maxRecipients = $game->points_recipients ?? 3; // Default to 3 if not set
                    $placement = ($index < $maxRecipients) ? $index + 1 : null;

                    // Determine points based on placement using the game's points distribution
                    $points = 0;
                    if ($placement !== null) {
                        $points = $game->getPointsForPlacement($placement);
                    }

                    // Create or update game point
                    GamePoint::updateOrCreate(
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
