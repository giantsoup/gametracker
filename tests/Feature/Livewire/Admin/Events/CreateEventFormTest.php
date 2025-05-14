<?php

namespace Tests\Feature\Livewire\Admin\Events;

use App\Enums\UserRole;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateEventFormTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function create_event_form_can_render()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that the component renders
        Volt::test('admin.events.create-event-form')
            ->assertStatus(200);
    }

    #[Test]
    public function admin_can_create_event_with_valid_data()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that an event can be created with valid data
        Volt::test('admin.events.create-event-form')
            ->set('name', 'New Test Event')
            ->set('active', true)
            ->set('starts_at', now()->addDay()->format('Y-m-d\TH:i'))
            ->set('ends_at', now()->addDays(2)->format('Y-m-d\TH:i'))
            ->call('create')
            ->assertDispatched('event-created');

        // Assert the event was created in the database
        $this->assertDatabaseHas('events', [
            'name' => 'New Test Event',
            'active' => true,
        ]);
    }

    #[Test]
    public function validation_errors_shown_for_invalid_data()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that validation errors are shown for invalid data
        Volt::test('admin.events.create-event-form')
            ->set('name', '') // Empty name should fail validation
            ->set('starts_at', now()->addDays(2)->format('Y-m-d\TH:i')) // Set start date
            ->set('ends_at', now()->subDay()->format('Y-m-d\TH:i')) // End date before start date should fail validation
            ->call('create')
            ->assertHasErrors(['name', 'ends_at']);
    }

    #[Test]
    public function form_resets_after_successful_submission()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that the form resets after successful submission
        Volt::test('admin.events.create-event-form')
            ->set('name', 'New Test Event')
            ->set('active', true)
            ->set('starts_at', now()->addDay()->format('Y-m-d\TH:i'))
            ->set('ends_at', now()->addDays(2)->format('Y-m-d\TH:i'))
            ->call('create')
            ->assertSet('name', '')
            ->assertSet('active', false)
            ->assertSet('starts_at', null)
            ->assertSet('ends_at', null);
    }
}
