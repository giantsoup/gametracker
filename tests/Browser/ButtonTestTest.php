<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ButtonTestTest extends DuskTestCase
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
                ->waitForText('Counter incremented!')
                ->assertSee('Current counter value: 1');
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
                ->waitForText('Counter decremented!')
                ->assertSee('Current counter value: -1');
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
                ->waitForText('Counter incremented!')
                ->assertSee('Current counter value: 1')
                ->click('@reset-button')
                ->waitForText('Counter reset!')
                ->assertSee('Current counter value: 0');
        });
    }
}
