<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user model uses soft deletes', function () {
    $user = User::factory()->create();
    $userId = $user->id;

    $user->delete();

    expect(User::find($userId))->toBeNull();
    expect(User::withTrashed()->find($userId))->not->toBeNull();
});

test('user factory creates valid users', function () {
    $user = User::factory()->create();

    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->not->toBeEmpty();
    expect($user->email)->not->toBeEmpty();
    expect($user->password)->not->toBeEmpty();
});

test('user can be restored after soft delete', function () {
    $user = User::factory()->create();
    $userId = $user->id;

    $user->delete();
    expect(User::find($userId))->toBeNull();

    $user->restore();
    expect(User::find($userId))->not->toBeNull();
});

test('user can be force deleted', function () {
    $user = User::factory()->create();
    $userId = $user->id;

    $user->forceDelete();

    expect(User::find($userId))->toBeNull();
    expect(User::withTrashed()->find($userId))->toBeNull();
});
