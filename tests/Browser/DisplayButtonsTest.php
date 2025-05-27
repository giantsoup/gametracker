<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DisplayButtonsTest extends DuskTestCase
{
    /**
     * Test that the display type buttons work correctly.
     */
    public function test_display_type_buttons()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                // Check initial state (default display type)
                ->assertSee('GameTracker')

                // Click Projection display type link
                ->clickLink('', ['href' => '/?display=projection'])
                ->waitForReload()

                // Check if URL contains the projection display type
                ->assertUrlContains('display=projection')

                // Click Mobile display type link
                ->clickLink('', ['href' => '/?display=mobile'])
                ->waitForReload()

                // Check if URL contains the mobile display type
                ->assertUrlContains('display=mobile')

                // Click Default display type link
                ->clickLink('', ['href' => '/?display=default'])
                ->waitForReload()

                // Check if URL does not contain display parameter (or contains display=default)
                ->assertUrlContains('display=default');
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
                ->assertSee('GameTracker')

                // Click Layout 2 link
                ->clickLink('', ['href' => '/?layout=2'])
                ->waitForReload()

                // Check if URL contains layout=2
                ->assertUrlContains('layout=2')

                // Click Layout 3 link
                ->clickLink('', ['href' => '/?layout=3'])
                ->waitForReload()

                // Check if URL contains layout=3
                ->assertUrlContains('layout=3')

                // Click Layout 1 link
                ->clickLink('', ['href' => '/?layout=1'])
                ->waitForReload()

                // Check if URL contains layout=1
                ->assertUrlContains('layout=1');
        });
    }

    /**
     * Test that the display type is preserved when switching layouts.
     */
    public function test_display_type_preserved_when_switching_layouts()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/?display=projection')
                // Check initial state (projection display type)
                ->assertUrlContains('display=projection')

                // Click Layout 2 link
                ->clickLink('', ['href' => '/?layout=2&display=projection'])
                ->waitForReload()

                // Check if URL contains both layout=2 and display=projection
                ->assertUrlContains('layout=2')
                ->assertUrlContains('display=projection')

                // Click Layout 3 link
                ->clickLink('', ['href' => '/?layout=3&display=projection'])
                ->waitForReload()

                // Check if URL contains both layout=3 and display=projection
                ->assertUrlContains('layout=3')
                ->assertUrlContains('display=projection')

                // Click Layout 1 link
                ->clickLink('', ['href' => '/?layout=1&display=projection'])
                ->waitForReload()

                // Check if URL contains both layout=1 and display=projection
                ->assertUrlContains('layout=1')
                ->assertUrlContains('display=projection');
        });
    }

    /**
     * Test that the layout is preserved when switching display types.
     */
    public function test_layout_preserved_when_switching_display_types()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/?layout=2')
                // Check initial state (Layout 2)
                ->assertUrlContains('layout=2')

                // Click Projection display type link
                ->clickLink('', ['href' => '/?display=projection&layout=2'])
                ->waitForReload()

                // Check if URL contains both display=projection and layout=2
                ->assertUrlContains('display=projection')
                ->assertUrlContains('layout=2')

                // Click Mobile display type link
                ->clickLink('', ['href' => '/?display=mobile&layout=2'])
                ->waitForReload()

                // Check if URL contains both display=mobile and layout=2
                ->assertUrlContains('display=mobile')
                ->assertUrlContains('layout=2')

                // Click Default display type link
                ->clickLink('', ['href' => '/?display=default&layout=2'])
                ->waitForReload()

                // Check if URL contains layout=2 and display=default
                ->assertUrlContains('layout=2')
                ->assertUrlContains('display=default');
        });
    }
}
