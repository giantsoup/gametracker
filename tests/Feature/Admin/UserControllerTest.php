<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can view users index page', function () {
    // Create an admin user
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);

    // Act as the admin user
    $this->actingAs($admin);

    // Visit the users index page
    $response = $this->get(route('admin.users.index'));

    // Assert the response is successful
    $response->assertStatus(200);

    // Assert the view is correct
    $response->assertViewIs('admin.users.index');
});

test('non-admin cannot view users index page', function () {
    // Create a regular user
    $user = User::factory()->create(['role' => UserRole::USER]);

    // Act as the regular user
    $this->actingAs($user);

    // Visit the users index page
    $response = $this->get(route('admin.users.index'));

    // Assert the user is redirected (forbidden)
    $response->assertStatus(403);
});

test('admin can view create user page', function () {
    // Create an admin user
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);

    // Act as the admin user
    $this->actingAs($admin);

    // Visit the create user page
    $response = $this->get(route('admin.users.create'));

    // Assert the response is successful
    $response->assertStatus(200);

    // Assert the view is correct
    $response->assertViewIs('admin.users.create');
});

test('admin can create a new user', function () {
    // Create an admin user
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);

    // Act as the admin user
    $this->actingAs($admin);

    // Data for the new user
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'role' => UserRole::USER->value,
    ];

    // Submit the form to create a new user
    $response = $this->post(route('admin.users.store'), $userData);

    // Assert the user is redirected to the users index page
    $response->assertRedirect(route('admin.users.index'));

    // Assert the user was created in the database
    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'role' => UserRole::USER->value,
    ]);

    // Assert a success message was flashed to the session
    $response->assertSessionHas('success');
});

test('admin can view a user', function () {
    // Create an admin user
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);

    // Create a user to view
    $user = User::factory()->create();

    // Act as the admin user
    $this->actingAs($admin);

    // Visit the user show page
    $response = $this->get(route('admin.users.show', $user));

    // Assert the response is successful
    $response->assertStatus(200);

    // Assert the view is correct
    $response->assertViewIs('admin.users.show');

    // Assert the view has the user
    $response->assertViewHas('user', $user);
});

test('admin can view edit user page', function () {
    // Create an admin user
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);

    // Create a user to edit
    $user = User::factory()->create();

    // Act as the admin user
    $this->actingAs($admin);

    // Visit the edit user page
    $response = $this->get(route('admin.users.edit', $user));

    // Assert the response is successful
    $response->assertStatus(200);

    // Assert the view is correct
    $response->assertViewIs('admin.users.edit');

    // Assert the view has the user
    $response->assertViewHas('user', $user);
});

test('admin can update a user', function () {
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

    // Data for updating the user
    $updatedData = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'role' => UserRole::ADMIN->value,
    ];

    // Submit the form to update the user
    $response = $this->put(route('admin.users.update', $user), $updatedData);

    // Assert the user is redirected to the users index page
    $response->assertRedirect(route('admin.users.index'));

    // Assert the user was updated in the database
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'role' => UserRole::ADMIN->value,
    ]);

    // Assert a success message was flashed to the session
    $response->assertSessionHas('success');
});

test('admin can delete a user', function () {
    // Create an admin user
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);

    // Create a user to delete
    $user = User::factory()->create();

    // Act as the admin user
    $this->actingAs($admin);

    // Submit the form to delete the user
    $response = $this->delete(route('admin.users.destroy', $user));

    // Assert the user is redirected to the users index page
    $response->assertRedirect(route('admin.users.index'));

    // Assert the user was soft deleted
    $this->assertSoftDeleted('users', [
        'id' => $user->id,
    ]);

    // Assert a success message was flashed to the session
    $response->assertSessionHas('success');
});

test('admin cannot delete their own account', function () {
    // Create an admin user
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);

    // Act as the admin user
    $this->actingAs($admin);

    // Submit the form to delete the admin's own account
    $response = $this->delete(route('admin.users.destroy', $admin));

    // Assert the admin is redirected to the users index page
    $response->assertRedirect(route('admin.users.index'));

    // Assert the admin was not deleted
    $this->assertDatabaseHas('users', [
        'id' => $admin->id,
        'deleted_at' => null,
    ]);

    // Assert an error message was flashed to the session
    $response->assertSessionHas('error');
});

test('validation fails when creating user with invalid data', function () {
    // Create an admin user
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);

    // Act as the admin user
    $this->actingAs($admin);

    // Data with invalid email
    $invalidData = [
        'name' => 'Test User',
        'email' => 'not-an-email',
        'role' => UserRole::USER->value,
    ];

    // Submit the form with invalid data
    $response = $this->post(route('admin.users.store'), $invalidData);

    // Assert the response has validation errors
    $response->assertSessionHasErrors('email');

    // Assert the user was not created
    $this->assertDatabaseMissing('users', [
        'name' => 'Test User',
        'email' => 'not-an-email',
    ]);
});

test('validation fails when updating user with invalid data', function () {
    // Create an admin user
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);

    // Create a user to update
    $user = User::factory()->create();

    // Act as the admin user
    $this->actingAs($admin);

    // Data with invalid email
    $invalidData = [
        'name' => 'Updated Name',
        'email' => 'not-an-email',
        'role' => UserRole::USER->value,
    ];

    // Submit the form with invalid data
    $response = $this->put(route('admin.users.update', $user), $invalidData);

    // Assert the response has validation errors
    $response->assertSessionHasErrors('email');

    // Assert the user was not updated
    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'not-an-email',
    ]);
});
