<?php

use App\Livewire\Dashboard;
use Livewire\Livewire;

test('dashboard page loads successfully', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('GameTracker');
});

test('dashboard page loads livewire scripts', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSeeLivewire('dashboard');
});

test('dashboard livewire component renders', function () {
    Livewire::test(Dashboard::class)
        ->assertStatus(200);
});
