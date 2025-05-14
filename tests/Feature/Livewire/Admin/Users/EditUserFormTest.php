<?php

namespace Tests\Feature\Livewire\Admin\Users;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EditUserFormTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function edit_user_form_can_render()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Create a user to edit
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'role' => UserRole::USER->value,
        ]);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that the component renders with the correct user data
        Volt::test('admin.users.edit-user-form', ['user' => $user, 'roles' => UserRole::getSelectOptions()])
            ->assertSet('name', 'Original Name')
            ->assertSet('email', 'original@example.com')
            ->assertSet('role', UserRole::USER->value);
    }

    #[Test]
    public function admin_can_update_user_through_controller()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Create a user to update
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'role' => UserRole::USER->value,
        ]);

        // Act as the admin user
        $this->actingAs($admin);

        // Use the controller to update the user
        $response = $this->put(route('admin.users.update', $user), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => UserRole::ADMIN->value,
        ]);

        // Assert the user was updated in the database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => UserRole::ADMIN->value,
        ]);

        // Assert the user is redirected to the users index page
        $response->assertRedirect(route('admin.users.index'));
    }

    #[Test]
    public function validation_errors_shown_when_updating_with_invalid_data()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Create a user to update
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'role' => UserRole::USER->value,
        ]);

        // Act as the admin user
        $this->actingAs($admin);

        // Use the controller to update the user with invalid data
        $response = $this->put(route('admin.users.update', $user), [
            'name' => '', // Empty name should fail validation
            'email' => 'not-an-email', // Invalid email should fail validation
            'role' => UserRole::USER->value,
        ]);

        // Assert the response has validation errors
        $response->assertSessionHasErrors(['name', 'email']);

        // Assert the user was not updated in the database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);
    }

    #[Test]
    public function email_must_be_unique_except_for_current_user()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Create a user to update
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'role' => UserRole::USER->value,
        ]);

        // Create another user with a specific email
        $anotherUser = User::factory()->create(['email' => 'another@example.com']);

        // Act as the admin user
        $this->actingAs($admin);

        // Use the controller to update the user with an email that's already in use
        $response = $this->put(route('admin.users.update', $user), [
            'name' => 'Updated Name',
            'email' => 'another@example.com', // This email is already in use by another user
            'role' => UserRole::USER->value,
        ]);

        // Assert the response has validation errors
        $response->assertSessionHasErrors(['email']);

        // Assert the user was not updated in the database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);
    }

    #[Test]
    public function user_can_keep_same_email_when_updating()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Create a user to update
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'role' => UserRole::USER->value,
        ]);

        // Act as the admin user
        $this->actingAs($admin);

        // Use the controller to update the user with the same email
        $response = $this->put(route('admin.users.update', $user), [
            'name' => 'Updated Name',
            'email' => 'original@example.com', // Same email as before
            'role' => UserRole::USER->value,
        ]);

        // Assert the user was updated in the database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'original@example.com',
        ]);

        // Assert the user is redirected to the users index page
        $response->assertRedirect(route('admin.users.index'));
    }
}
