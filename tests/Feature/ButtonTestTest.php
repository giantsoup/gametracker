<?php

use App\Livewire\ButtonTest;
use Livewire\Livewire;

test('button test component renders correctly', function () {
    $response = $this->get('/button-test');

    $response->assertStatus(200);
    $response->assertSeeLivewire('button-test');
    $response->assertSee('Livewire Button Test');
    $response->assertSee('Current counter value: 0');
});

test('increment button increases counter', function () {
    Livewire::test(ButtonTest::class)
        ->assertSet('counter', 0)
        ->call('increment')
        ->assertSet('counter', 1)
        ->assertSet('message', 'Counter incremented!');
});

test('decrement button decreases counter', function () {
    Livewire::test(ButtonTest::class)
        ->assertSet('counter', 0)
        ->call('decrement')
        ->assertSet('counter', -1)
        ->assertSet('message', 'Counter decremented!');
});

test('reset button sets counter to zero', function () {
    Livewire::test(ButtonTest::class)
        ->assertSet('counter', 0)
        ->call('increment')
        ->assertSet('counter', 1)
        ->call('resetCounter')
        ->assertSet('counter', 0)
        ->assertSet('message', 'Counter reset!');
});
