<?php

use App\Models\Event;
use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('player belongs to a user', function () {
    $player = Player::factory()->create();

    expect($player->user)->toBeInstanceOf(User::class);
});

test('player belongs to an event', function () {
    $player = Player::factory()->create();

    expect($player->event)->toBeInstanceOf(Event::class);
});

test('user can have many players', function () {
    $user = User::factory()->create();
    $players = Player::factory()->count(3)->create(['user_id' => $user->id]);

    expect($user->players)->toHaveCount(3);
    expect($user->players->first())->toBeInstanceOf(Player::class);
});

test('event can have many players', function () {
    $event = Event::factory()->create();
    $players = Player::factory()->count(3)->create(['event_id' => $event->id]);

    expect($event->players)->toHaveCount(3);
    expect($event->players->first())->toBeInstanceOf(Player::class);
});

test('player can join an event', function () {
    $player = Player::factory()->create(['joined_at' => null]);

    expect($player->hasJoined())->toBeFalse();

    $player->join();

    expect($player->hasJoined())->toBeTrue();
    expect($player->joined_at)->not->toBeNull();
});

test('player can leave an event', function () {
    $player = Player::factory()->joined()->create(['left_at' => null]);

    expect($player->hasLeft())->toBeFalse();

    $player->leave();

    expect($player->hasLeft())->toBeTrue();
    expect($player->left_at)->not->toBeNull();
});

test('player can have a nickname', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $player = Player::factory()->create([
        'user_id' => $user->id,
        'nickname' => 'JohnnyD',
    ]);

    expect($player->nickname)->toBe('JohnnyD');
    expect($player->getDisplayName())->toBe('JohnnyD');
});

test('player uses user name when nickname is null', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $player = Player::factory()->create([
        'user_id' => $user->id,
        'nickname' => null,
    ]);

    expect($player->nickname)->toBeNull();
    expect($player->getDisplayName())->toBe('John Doe');
});

test('user can access events through players', function () {
    $user = User::factory()->create();
    $events = Event::factory()->count(3)->create();

    foreach ($events as $event) {
        Player::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);
    }

    expect($user->events)->toHaveCount(3);
    expect($user->events->first())->toBeInstanceOf(Event::class);
});

test('event can access users through players', function () {
    $event = Event::factory()->create();
    $users = User::factory()->count(3)->create();

    foreach ($users as $user) {
        Player::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);
    }

    expect($event->users)->toHaveCount(3);
    expect($event->users->first())->toBeInstanceOf(User::class);
});
