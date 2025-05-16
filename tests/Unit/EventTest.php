<?php

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('isActive returns correct status', function () {
    // Create an event with active status set to true
    $event = new Event(['active' => true]);
    expect($event->isActive())->toBeTrue();

    // Create an event with active status set to false
    $event = new Event(['active' => false]);
    expect($event->isActive())->toBeFalse();
});

test('hasStarted returns true when started_at is set', function () {
    // Create an event with started_at set
    $event = new Event(['started_at' => now()]);
    expect($event->hasStarted())->toBeTrue();

    // Create an event with started_at not set
    $event = new Event(['started_at' => null]);
    expect($event->hasStarted())->toBeFalse();
});

test('hasEnded returns true when ended_at is set', function () {
    // Create an event with ended_at set
    $event = new Event(['ended_at' => now()]);
    expect($event->hasEnded())->toBeTrue();

    // Create an event with ended_at not set
    $event = new Event(['ended_at' => null]);
    expect($event->hasEnded())->toBeFalse();
});

test('start method sets active to true and started_at to current time', function () {
    // Create an inactive event
    $event = Event::factory()->create([
        'name' => 'Test Event',
        'active' => false,
        'started_at' => null,
    ]);

    // Start the event
    $event->start();

    // Verify the event is now active and started_at is set
    expect($event->active)->toBeTrue();
    expect($event->started_at)->not->toBeNull();
});

test('end method sets active to false and ended_at to current time', function () {
    // Create an active event
    $event = Event::factory()->create([
        'name' => 'Test Event',
        'active' => true,
        'ended_at' => null,
    ]);

    // End the event
    $event->end();

    // Verify the event is now inactive and ended_at is set
    expect($event->active)->toBeFalse();
    expect($event->ended_at)->not->toBeNull();
});

test('scopeActive returns only active events', function () {
    // Create active and inactive events
    Event::factory()->create([
        'name' => 'Active Event',
        'active' => true,
    ]);

    Event::factory()->create([
        'name' => 'Inactive Event',
        'active' => false,
    ]);

    // Query active events
    $activeEvents = Event::active()->get();

    // Verify only active events are returned
    expect($activeEvents)->toHaveCount(1);
    expect($activeEvents->first()->name)->toBe('Active Event');
});

test('scopeUpcoming returns events with starts_at in the future', function () {
    // Create events with different starts_at dates
    Event::factory()->create([
        'name' => 'Past Event',
        'starts_at' => now()->subDays(1),
    ]);

    Event::factory()->create([
        'name' => 'Future Event',
        'starts_at' => now()->addDays(1),
    ]);

    // Query upcoming events
    $upcomingEvents = Event::upcoming()->get();

    // Verify only future events are returned
    expect($upcomingEvents)->toHaveCount(1);
    expect($upcomingEvents->first()->name)->toBe('Future Event');
});

test('scopePast returns events with ends_at in the past', function () {
    // Create events with different ends_at dates
    Event::factory()->create([
        'name' => 'Past Event',
        'ends_at' => now()->subDays(1),
    ]);

    Event::factory()->create([
        'name' => 'Future Event',
        'ends_at' => now()->addDays(1),
    ]);

    // Query past events
    $pastEvents = Event::past()->get();

    // Verify only past events are returned
    expect($pastEvents)->toHaveCount(1);
    expect($pastEvents->first()->name)->toBe('Past Event');
});

test('scopeOngoing returns events currently in progress', function () {
    // Create events with different date ranges
    Event::factory()->create([
        'name' => 'Past Event',
        'starts_at' => now()->subDays(2),
        'ends_at' => now()->subDays(1),
    ]);

    Event::factory()->create([
        'name' => 'Future Event',
        'starts_at' => now()->addDays(1),
        'ends_at' => now()->addDays(2),
    ]);

    Event::factory()->create([
        'name' => 'Ongoing Event',
        'starts_at' => now()->subDays(1),
        'ends_at' => now()->addDays(1),
    ]);

    Event::factory()->create([
        'name' => 'Ongoing Event No End',
        'starts_at' => now()->subDays(1),
        'ends_at' => null,
    ]);

    // Query ongoing events
    $ongoingEvents = Event::ongoing()->get();

    // Verify only ongoing events are returned
    expect($ongoingEvents)->toHaveCount(2);
    expect($ongoingEvents->pluck('name')->toArray())->toContain('Ongoing Event');
    expect($ongoingEvents->pluck('name')->toArray())->toContain('Ongoing Event No End');
});
