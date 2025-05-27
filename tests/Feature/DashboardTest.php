<?php

use App\Livewire\Dashboard;
use Livewire\Livewire;

test('dashboard component renders correctly', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSeeLivewire('dashboard');
});

test('switchLayout method updates activeLayout property', function () {
    Livewire::test(Dashboard::class)
        ->assertSet('activeLayout', 1) // Default layout is 1
        ->call('switchLayout', 2)
        ->assertSet('activeLayout', 2)
        ->call('switchLayout', 3)
        ->assertSet('activeLayout', 3)
        ->call('switchLayout', 1)
        ->assertSet('activeLayout', 1);
});

test('switchDisplayType method updates displayType property', function () {
    Livewire::test(Dashboard::class)
        ->assertSet('displayType', 'default') // Default display type
        ->call('switchDisplayType', 'projection')
        ->assertSet('displayType', 'projection')
        ->assertSet('activeLayout', 1) // Projection should set layout to 1
        ->call('switchDisplayType', 'mobile')
        ->assertSet('displayType', 'mobile')
        ->assertSet('activeLayout', 3) // Mobile should set layout to 3
        ->call('switchDisplayType', 'default')
        ->assertSet('displayType', 'default');
});
