<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DashboardLayoutTest extends DuskTestCase
{
    /**
     * Test that the dashboard page loads correctly.
     */
    public function test_dashboard_page_loads()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('GameTracker')
                ->assertPresent('.sticky.top-0'); // Check if the sticky header is present
        });
    }

    /**
     * Test that the layout buttons work correctly.
     */
    public function test_layout_buttons()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                // Check initial state (Layout 1 should be active by default)
                ->assertPresent('button[wire\\:click="switchLayout(1)"].bg-white')

                // Click Layout 2 button
                ->click('button[wire\\:click="switchLayout(2)"]')
                ->pause(500) // Wait for Livewire to update

                // Check if Layout 2 is now active
                ->assertPresent('button[wire\\:click="switchLayout(2)"].bg-white')
                ->assertSee('Coming Up Next') // Text unique to Layout 2

                // Click Layout 3 button
                ->click('button[wire\\:click="switchLayout(3)"]')
                ->pause(500) // Wait for Livewire to update

                // Check if Layout 3 is now active
                ->assertPresent('button[wire\\:click="switchLayout(3)"].bg-white')
                ->assertSee('Game Cards'); // Text unique to Layout 3
        });
    }
}
