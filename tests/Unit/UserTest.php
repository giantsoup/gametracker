<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user initials method returns correct initials', function () {
    // Create a user with a name
    $user = new User(['name' => 'John Doe']);

    // Test that the initials method returns the correct initials
    expect($user->initials())->toBe('JD');

    // Test with a different name
    $user->name = 'Jane Smith';
    expect($user->initials())->toBe('JS');

    // Test with a single name
    $user->name = 'Madonna';
    expect($user->initials())->toBe('M');

    // Test with multiple names
    $user->name = 'John James Doe Smith';
    expect($user->initials())->toBe('JJDS');
});

test('isAdmin returns true for admin users', function () {
    $user = new User(['role' => UserRole::ADMIN]);
    expect($user->isAdmin())->toBeTrue();

    $user->role = UserRole::USER;
    expect($user->isAdmin())->toBeFalse();
});

test('isUser returns true for regular users', function () {
    $user = new User(['role' => UserRole::USER]);
    expect($user->isUser())->toBeTrue();

    $user->role = UserRole::ADMIN;
    expect($user->isUser())->toBeFalse();
});

test('getRoleBadge returns correct badge information', function () {
    $user = new User(['role' => UserRole::ADMIN]);
    $badge = $user->getRoleBadge();

    expect($badge)->toBeArray();
    expect($badge['color'])->toBe('red');
    expect($badge['text'])->toBe('Admin');

    $user->role = UserRole::USER;
    $badge = $user->getRoleBadge();

    expect($badge['color'])->toBe('blue');
    expect($badge['text'])->toBe('User');
});

// Soft delete test moved to feature tests
