<?php

use App\Livewire\Dashboard;
use Livewire\Livewire;

test('dashboard component renders correctly', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSeeLivewire('dashboard');
});

test('dashboard component renders view data', function () {
    Livewire::test(Dashboard::class)
        ->assertStatus(200)
        ->assertViewHas('activeEvent')
        ->assertViewHas('currentGame')
        ->assertViewHas('finishedGames')
        ->assertViewHas('upcomingGames');
});
