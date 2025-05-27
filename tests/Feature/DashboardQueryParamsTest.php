<?php

use App\Livewire\Dashboard;
use Livewire\Livewire;

test('dashboard handles layout query parameter', function () {
    // Test with layout=1
    $response = $this->get('/?layout=1');
    $response->assertStatus(200);
    $response->assertSee('GameTracker');

    // Test with layout=2
    $response = $this->get('/?layout=2');
    $response->assertStatus(200);
    $response->assertSee('GameTracker');

    // Test with layout=3
    $response = $this->get('/?layout=3');
    $response->assertStatus(200);
    $response->assertSee('GameTracker');

    // Test with invalid layout (should default to 1)
    $response = $this->get('/?layout=999');
    $response->assertStatus(200);
    $response->assertSee('GameTracker');
});

test('dashboard handles display query parameter', function () {
    // Test with display=default
    $response = $this->get('/?display=default');
    $response->assertStatus(200);
    $response->assertSee('GameTracker');

    // Test with display=projection
    $response = $this->get('/?display=projection');
    $response->assertStatus(200);
    $response->assertSee('GameTracker');

    // Test with display=mobile
    $response = $this->get('/?display=mobile');
    $response->assertStatus(200);
    $response->assertSee('GameTracker');

    // Test with invalid display (should default to 'default')
    $response = $this->get('/?display=invalid');
    $response->assertStatus(200);
    $response->assertSee('GameTracker');
});

test('dashboard handles both layout and display query parameters', function () {
    // Test with layout=2 and display=projection
    $response = $this->get('/?layout=2&display=projection');
    $response->assertStatus(200);
    $response->assertSee('GameTracker');

    // Test with layout=3 and display=mobile
    $response = $this->get('/?layout=3&display=mobile');
    $response->assertStatus(200);
    $response->assertSee('GameTracker');
});

test('dashboard component sets properties based on query parameters', function () {
    // Test with layout=2
    Livewire::test(Dashboard::class, ['request' => request()->create('/?layout=2')])
        ->assertSet('activeLayout', 2)
        ->assertSet('displayType', 'default');

    // Test with display=projection
    Livewire::test(Dashboard::class, ['request' => request()->create('/?display=projection')])
        ->assertSet('displayType', 'projection')
        ->assertSet('activeLayout', 1); // Should default to layout 1 for projection

    // Test with display=mobile
    Livewire::test(Dashboard::class, ['request' => request()->create('/?display=mobile')])
        ->assertSet('displayType', 'mobile')
        ->assertSet('activeLayout', 3); // Should default to layout 3 for mobile

    // Test with both layout and display
    Livewire::test(Dashboard::class, ['request' => request()->create('/?layout=2&display=projection')])
        ->assertSet('activeLayout', 2)
        ->assertSet('displayType', 'projection');
});
