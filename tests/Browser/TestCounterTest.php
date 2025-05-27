<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TestCounterTest extends DuskTestCase
{
    /**
     * Test that the test counter button works correctly.
     */
    public function test_test_counter_button()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Test Counter: 0')
                ->click('#test-counter-button')
                ->pause(500) // Wait for Livewire to update
                ->assertSee('Test Counter: 1')
                ->click('#test-counter-button')
                ->pause(500) // Wait for Livewire to update
                ->assertSee('Test Counter: 2');
        });
    }
}
