<?php

namespace Tests\Feature\Livewire\Admin\Users;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateUserFormTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function create_user_form_can_render()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that the component renders
        Volt::test('admin.users.create-user-form', ['roles' => UserRole::getSelectOptions()])
            ->assertStatus(200);
    }

    #[Test]
    public function admin_can_create_user_with_valid_data()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that a user can be created with valid data
        Volt::test('admin.users.create-user-form', ['roles' => UserRole::getSelectOptions()])
            ->set('name', 'New Test User')
            ->set('email', 'newuser@example.com')
            ->set('role', UserRole::USER->value)
            ->call('create')
            ->assertDispatched('user-created');

        // Assert the user was created in the database
        $this->assertDatabaseHas('users', [
            'name' => 'New Test User',
            'email' => 'newuser@example.com',
            'role' => UserRole::USER->value,
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
        Volt::test('admin.users.create-user-form', ['roles' => UserRole::getSelectOptions()])
            ->set('name', '') // Empty name should fail validation
            ->set('email', 'not-an-email') // Invalid email should fail validation
            ->call('create')
            ->assertHasErrors(['name', 'email']);
    }

    #[Test]
    public function email_must_be_unique()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Create a user with a specific email
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that validation fails when using an existing email
        Volt::test('admin.users.create-user-form', ['roles' => UserRole::getSelectOptions()])
            ->set('name', 'Another User')
            ->set('email', 'existing@example.com') // This email is already in use
            ->set('role', UserRole::USER->value)
            ->call('create')
            ->assertHasErrors(['email' => 'unique']);
    }

    #[Test]
    public function form_resets_after_successful_submission()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that the form resets after successful submission
        Volt::test('admin.users.create-user-form', ['roles' => UserRole::getSelectOptions()])
            ->set('name', 'New Test User')
            ->set('email', 'newuser@example.com')
            ->set('role', UserRole::USER->value)
            ->call('create')
            ->assertSet('name', '')
            ->assertSet('email', '')
            ->assertSet('role', UserRole::USER->value); // Role might default to USER
    }
}
