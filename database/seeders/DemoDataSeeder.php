<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Event;
use App\Models\Game;
use App\Models\GamePoint;
use App\Models\Player;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * @var list<array{email: string, name: string, nickname: string|null}>
     */
    private const DEMO_USERS = [
        ['email' => 'user@example.com', 'name' => 'Demo User', 'nickname' => 'Demo'],
        ['email' => 'player1@example.com', 'name' => 'Alex Kim', 'nickname' => 'AK'],
        ['email' => 'player2@example.com', 'name' => 'Jordan Lee', 'nickname' => null],
        ['email' => 'player3@example.com', 'name' => 'Sam Rivera', 'nickname' => 'River'],
        ['email' => 'player4@example.com', 'name' => 'Casey Nguyen', 'nickname' => 'Case'],
        ['email' => 'player5@example.com', 'name' => 'Taylor Brooks', 'nickname' => 'TB'],
        ['email' => 'player6@example.com', 'name' => 'Morgan Patel', 'nickname' => null],
        ['email' => 'player7@example.com', 'name' => 'Riley Gomez', 'nickname' => 'Riles'],
        ['email' => 'player8@example.com', 'name' => 'Cameron Bell', 'nickname' => null],
        ['email' => 'player9@example.com', 'name' => 'Jamie Foster', 'nickname' => null],
        ['email' => 'player10@example.com', 'name' => 'Logan Murphy', 'nickname' => 'LM'],
        ['email' => 'player11@example.com', 'name' => 'Avery Carter', 'nickname' => null],
        ['email' => 'player12@example.com', 'name' => 'Drew Bennett', 'nickname' => null],
        ['email' => 'player13@example.com', 'name' => 'Parker Diaz', 'nickname' => 'Parker'],
        ['email' => 'player14@example.com', 'name' => 'Quinn Adams', 'nickname' => null],
        ['email' => 'player15@example.com', 'name' => 'Reese Cooper', 'nickname' => 'Reese'],
    ];

    /**
     * Run the database seeds to create a realistic demo dataset.
     */
    public function run(): void
    {
        $this->command?->info('Creating demo data...');

        $password = Hash::make('password');
        $admin = $this->upsertUser([
            'email' => 'admin@example.com',
            'name' => 'Demo Admin',
            'nickname' => 'Admin',
            'password' => $password,
            'role' => UserRole::ADMIN,
            'email_verified_at' => now(),
        ]);

        $demoUsers = $this->createDemoUsers($password);

        foreach ($this->eventBlueprints() as $eventBlueprint) {
            $this->seedEvent($eventBlueprint, $demoUsers, $admin);
        }

        $this->command?->info('Demo data created successfully.');
        $this->command?->info('You can now log in with:');
        $this->command?->info('- Admin: admin@example.com / password');
        $this->command?->info('- User: user@example.com / password');
    }

    /**
     * @return Collection<string, User>
     */
    private function createDemoUsers(string $password): Collection
    {
        return collect(self::DEMO_USERS)
            ->mapWithKeys(function (array $demoUser) use ($password): array {
                $user = $this->upsertUser([
                    'email' => $demoUser['email'],
                    'name' => $demoUser['name'],
                    'nickname' => $demoUser['nickname'],
                    'password' => $password,
                    'role' => UserRole::USER,
                    'email_verified_at' => now(),
                ]);

                return [$user->email => $user];
            });
    }

    /**
     * @param array{
     *     email: string,
     *     name: string,
     *     nickname: string|null,
     *     password: string,
     *     role: UserRole,
     *     email_verified_at: Carbon
     * } $attributes
     */
    private function upsertUser(array $attributes): User
    {
        $user = User::withTrashed()->firstOrNew([
            'email' => $attributes['email'],
        ]);

        if ($user->exists && $user->trashed()) {
            $user->restore();
        }

        $user->forceFill($attributes);
        $user->save();

        return $user->refresh();
    }

    /**
     * @return list<array{
     *     name: string,
     *     attributes: array{active: bool, starts_at: CarbonImmutable, ends_at: CarbonImmutable, started_at: CarbonImmutable|null, ended_at: CarbonImmutable|null},
     *     player_emails: list<string>,
     *     games: list<array{name: string, duration: int, state: 'played'|'current'|'upcoming'}>
     * }>
     */
    private function eventBlueprints(): array
    {
        $now = CarbonImmutable::now();

        $pastStartsAt = $now->subDays(14)->setTime(18, 30);
        $currentStartsAt = $now->subHours(3);
        $futureStartsAt = $now->addDays(4)->setTime(18, 30);

        return [
            [
                'name' => 'Past Game Night',
                'attributes' => [
                    'active' => false,
                    'starts_at' => $pastStartsAt,
                    'ends_at' => $pastStartsAt->addHours(5),
                    'started_at' => $pastStartsAt,
                    'ended_at' => $pastStartsAt->addHours(5),
                ],
                'player_emails' => [
                    'user@example.com',
                    'player1@example.com',
                    'player2@example.com',
                    'player3@example.com',
                    'player4@example.com',
                    'player5@example.com',
                    'player6@example.com',
                    'player7@example.com',
                    'player8@example.com',
                    'player9@example.com',
                    'player10@example.com',
                    'player11@example.com',
                    'player12@example.com',
                    'player13@example.com',
                ],
                'games' => [
                    ['name' => 'Ready Set Bet', 'duration' => 45, 'state' => 'played'],
                    ['name' => 'Wits & Wagers', 'duration' => 30, 'state' => 'played'],
                    ['name' => 'Codenames', 'duration' => 45, 'state' => 'played'],
                    ['name' => 'Just One', 'duration' => 30, 'state' => 'played'],
                    ['name' => 'Skull King', 'duration' => 30, 'state' => 'played'],
                    ['name' => 'Telestrations', 'duration' => 45, 'state' => 'played'],
                ],
            ],
            [
                'name' => 'Current Game Night',
                'attributes' => [
                    'active' => true,
                    'starts_at' => $currentStartsAt,
                    'ends_at' => $currentStartsAt->addHours(6),
                    'started_at' => $currentStartsAt,
                    'ended_at' => null,
                ],
                'player_emails' => [
                    'user@example.com',
                    'player1@example.com',
                    'player2@example.com',
                    'player3@example.com',
                    'player4@example.com',
                    'player5@example.com',
                    'player6@example.com',
                    'player7@example.com',
                    'player8@example.com',
                    'player9@example.com',
                    'player10@example.com',
                    'player11@example.com',
                    'player12@example.com',
                    'player13@example.com',
                    'player14@example.com',
                ],
                'games' => [
                    ['name' => 'Wavelength', 'duration' => 30, 'state' => 'played'],
                    ['name' => 'Sushi Go Party!', 'duration' => 45, 'state' => 'played'],
                    ['name' => 'Camel Up', 'duration' => 30, 'state' => 'played'],
                    ['name' => 'For Sale', 'duration' => 45, 'state' => 'current'],
                    ['name' => 'The Chameleon', 'duration' => 30, 'state' => 'upcoming'],
                    ['name' => 'A Fake Artist Goes to New York', 'duration' => 30, 'state' => 'upcoming'],
                ],
            ],
            [
                'name' => 'Future Game Night',
                'attributes' => [
                    'active' => false,
                    'starts_at' => $futureStartsAt,
                    'ends_at' => $futureStartsAt->addHours(5),
                    'started_at' => null,
                    'ended_at' => null,
                ],
                'player_emails' => [
                    'player2@example.com',
                    'player3@example.com',
                    'player4@example.com',
                    'player5@example.com',
                    'player6@example.com',
                    'player7@example.com',
                    'player8@example.com',
                    'player9@example.com',
                    'player10@example.com',
                    'player11@example.com',
                    'player12@example.com',
                    'player13@example.com',
                    'player14@example.com',
                ],
                'games' => [
                    ['name' => 'Decrypto', 'duration' => 45, 'state' => 'upcoming'],
                    ['name' => 'So Clover!', 'duration' => 30, 'state' => 'upcoming'],
                    ['name' => 'Insider', 'duration' => 45, 'state' => 'upcoming'],
                    ['name' => 'Monikers', 'duration' => 30, 'state' => 'upcoming'],
                    ['name' => 'Anomia', 'duration' => 45, 'state' => 'upcoming'],
                    ['name' => 'Dixit', 'duration' => 30, 'state' => 'upcoming'],
                ],
            ],
        ];
    }

    /**
     * @param array{
     *     name: string,
     *     attributes: array{active: bool, starts_at: CarbonImmutable, ends_at: CarbonImmutable, started_at: CarbonImmutable|null, ended_at: CarbonImmutable|null},
     *     player_emails: list<string>,
     *     games: list<array{name: string, duration: int, state: 'played'|'current'|'upcoming'}>
     * } $eventBlueprint
     * @param  Collection<string, User>  $demoUsers
     */
    private function seedEvent(array $eventBlueprint, Collection $demoUsers, User $admin): void
    {
        $event = $this->upsertEvent($eventBlueprint['name'], $eventBlueprint['attributes']);

        $this->resetEventData($event);

        $players = $this->createPlayersForEvent(
            event: $event,
            playerEmails: $eventBlueprint['player_emails'],
            demoUsers: $demoUsers,
        );

        $this->createGamesForEvent(
            event: $event,
            players: $players,
            gameBlueprints: $eventBlueprint['games'],
            admin: $admin,
        );
    }

    /**
     * @param  array{active: bool, starts_at: CarbonImmutable, ends_at: CarbonImmutable, started_at: CarbonImmutable|null, ended_at: CarbonImmutable|null}  $attributes
     */
    private function upsertEvent(string $name, array $attributes): Event
    {
        $event = Event::withTrashed()->firstOrNew([
            'name' => $name,
        ]);

        if ($event->exists && $event->trashed()) {
            $event->restore();
        }

        $event->fill($attributes);
        $event->save();

        return $event->refresh();
    }

    private function resetEventData(Event $event): void
    {
        $event->games()
            ->withTrashed()
            ->get()
            ->each(function (Game $game): void {
                $game->forceDelete();
            });

        $event->players()
            ->withTrashed()
            ->get()
            ->each(function (Player $player): void {
                $player->forceDelete();
            });
    }

    /**
     * @param  list<string>  $playerEmails
     * @param  Collection<string, User>  $demoUsers
     * @return Collection<int, Player>
     */
    private function createPlayersForEvent(Event $event, array $playerEmails, Collection $demoUsers): Collection
    {
        return collect($playerEmails)
            ->values()
            ->map(function (string $email, int $index) use ($demoUsers, $event): Player {
                /** @var User $user */
                $user = $demoUsers->get($email);

                return Player::create([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'nickname' => $user->nickname,
                    'joined_at' => $this->joinedAtForEvent($event, $index),
                    'left_at' => $event->ended_at,
                ]);
            });
    }

    private function joinedAtForEvent(Event $event, int $index): ?CarbonImmutable
    {
        if ($event->started_at === null) {
            return null;
        }

        return CarbonImmutable::instance($event->started_at)->addMinutes(min($index * 4, 48));
    }

    /**
     * @param  Collection<int, Player>  $players
     * @param  list<array{name: string, duration: int, state: 'played'|'current'|'upcoming'}>  $gameBlueprints
     */
    private function createGamesForEvent(Event $event, Collection $players, array $gameBlueprints, User $admin): void
    {
        $corePlayers = $players->slice(0, $players->count() - 3)->values();
        $benchPlayers = $players->slice(-3)->values();
        $eventStartsAt = CarbonImmutable::instance($event->starts_at ?? now());

        foreach ($gameBlueprints as $index => $gameBlueprint) {
            $game = Game::create([
                'event_id' => $event->id,
                'name' => $gameBlueprint['name'],
                'duration' => $gameBlueprint['duration'],
            ]);

            $participants = $this->participantsForGame($corePlayers, $benchPlayers, $index);
            $game->owners()->sync($participants->pluck('id')->all());

            $scheduledAt = $eventStartsAt->addMinutes($index * 45);
            $this->applyGameState($game, $gameBlueprint['state'], $scheduledAt);

            if ($gameBlueprint['state'] === 'played') {
                $this->assignGameResults(
                    game: $game,
                    participants: $participants,
                    admin: $admin,
                    assignedAt: $scheduledAt->addMinutes($game->duration),
                    rotation: $index,
                );
            }
        }
    }

    /**
     * @param  Collection<int, Player>  $corePlayers
     * @param  Collection<int, Player>  $benchPlayers
     * @return Collection<int, Player>
     */
    private function participantsForGame(Collection $corePlayers, Collection $benchPlayers, int $index): Collection
    {
        if ($benchPlayers->isEmpty()) {
            return $corePlayers->values();
        }

        return $corePlayers
            ->concat($this->rotatePlayers($benchPlayers, $index)->take(min(2, $benchPlayers->count())))
            ->values();
    }

    /**
     * @param  Collection<int, Player>  $players
     * @return Collection<int, Player>
     */
    private function rotatePlayers(Collection $players, int $offset): Collection
    {
        $count = $players->count();

        if ($count === 0) {
            return collect();
        }

        $normalizedOffset = $offset % $count;

        return $players
            ->slice($normalizedOffset)
            ->concat($players->take($normalizedOffset))
            ->values();
    }

    private function applyGameState(Game $game, string $state, CarbonImmutable $scheduledAt): void
    {
        $timestamps = match ($state) {
            'upcoming' => ['created_at' => null, 'updated_at' => null],
            'current' => ['created_at' => $scheduledAt, 'updated_at' => $scheduledAt],
            default => [
                'created_at' => $scheduledAt,
                'updated_at' => $scheduledAt->addMinutes($game->duration),
            ],
        };

        $game->timestamps = false;
        $game->forceFill($timestamps)->saveQuietly();
        $game->timestamps = true;
    }

    /**
     * @param  Collection<int, Player>  $participants
     */
    private function assignGameResults(
        Game $game,
        Collection $participants,
        User $admin,
        CarbonImmutable $assignedAt,
        int $rotation,
    ): void {
        $rankedParticipants = $this->rotatePlayers($participants->values(), $rotation);

        foreach ($rankedParticipants as $placement => $player) {
            GamePoint::create([
                'game_id' => $game->id,
                'player_id' => $player->user_id,
                'points' => $this->pointsForPlacement($placement + 1),
                'placement' => $placement + 1,
                'assigned_by' => $admin->id,
                'assigned_at' => $assignedAt,
                'last_modified_by' => null,
                'last_modified_at' => null,
            ]);
        }
    }

    private function pointsForPlacement(int $placement): int
    {
        return match ($placement) {
            1 => 5,
            2 => 3,
            3 => 1,
            default => 0,
        };
    }
}
