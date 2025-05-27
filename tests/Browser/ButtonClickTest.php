<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ButtonClickTest extends DuskTestCase
{
    /**
     * Test that the button test page loads correctly.
     */
    public function test_button_test_page_loads()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/button-test')
                ->assertSee('Livewire Button Test')
                ->assertSee('Current counter value: 0');
        });
    }

    /**
     * Test that the increment button works correctly.
     */
    public function test_increment_button()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/button-test')
                ->assertSee('Current counter value: 0')
                ->click('@increment-button')
                ->pause(500) // Wait for Livewire to update
                ->assertSee('Current counter value: 1')
                ->assertSee('Counter incremented!');
        });
    }

    /**
     * Test that the decrement button works correctly.
     */
    public function test_decrement_button()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/button-test')
                ->assertSee('Current counter value: 0')
                ->click('@decrement-button')
                ->pause(500) // Wait for Livewire to update
                ->assertSee('Current counter value: -1')
                ->assertSee('Counter decremented!');
        });
    }

    /**
     * Test that the reset button works correctly.
     */
    public function test_reset_button()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/button-test')
                ->click('@increment-button')
                ->pause(500) // Wait for Livewire to update
                ->assertSee('Current counter value: 1')
                ->click('@reset-button')
                ->pause(500) // Wait for Livewire to update
                ->assertSee('Current counter value: 0')
                ->assertSee('Counter reset!');
        });
    }

    /**
     * Test that the dashboard buttons work correctly.
     */
    public function test_dashboard_buttons()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('GameTracker')
                ->click('.fixed.bottom-4.right-4 button:nth-child(1)') // Click the first layout button
                ->pause(500) // Wait for Livewire to update
                ->assertSee('GameTracker');
        });
    }
}
