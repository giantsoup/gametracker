<?php

namespace Tests\Feature\Livewire\Admin\Users;

use App\Enums\UserRole;
use App\Livewire\Admin\Users\UsersTable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UsersTableTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function users_table_can_render()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that the component renders
        Livewire::test(UsersTable::class)
            ->assertStatus(200);
    }

    #[Test]
    public function users_table_displays_users()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Create some regular users
        $user1 = User::factory()->create(['name' => 'Test User 1']);
        $user2 = User::factory()->create(['name' => 'Test User 2']);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that the component displays the users
        Livewire::test(UsersTable::class)
            ->assertSee('Test User 1')
            ->assertSee('Test User 2');
    }

    #[Test]
    public function users_table_can_search_users()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Create some regular users
        $user1 = User::factory()->create(['name' => 'John Doe']);
        $user2 = User::factory()->create(['name' => 'Jane Smith']);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that the search functionality works
        Livewire::test(UsersTable::class)
            ->set('search', 'John')
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith');
    }

    #[Test]
    public function users_table_can_sort_users()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Create some regular users with different creation dates
        $user1 = User::factory()->create([
            'name' => 'John Doe',
            'created_at' => now()->subDays(2),
        ]);
        $user2 = User::factory()->create([
            'name' => 'Jane Smith',
            'created_at' => now()->subDay(),
        ]);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that sorting by name works
        Livewire::test(UsersTable::class)
            ->call('sortBy', 'name')
            ->assertSet('sortField', 'name')
            ->assertSet('sortDirection', 'asc');
    }

    #[Test]
    public function admin_can_delete_user_from_table()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Create a user to delete
        $user = User::factory()->create(['name' => 'Delete Me']);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that the delete functionality works
        Livewire::test(UsersTable::class)
            ->call('deleteModel', $user->id)
            ->assertDispatched('success');

        // Assert the user was soft deleted
        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);
    }

    #[Test]
    public function admin_cannot_delete_themselves_from_table()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that the admin cannot delete themselves
        Livewire::test(UsersTable::class)
            ->call('deleteModel', $admin->id)
            ->assertDispatched('error');

        // Assert the admin was not deleted
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'deleted_at' => null,
        ]);
    }

    #[Test]
    public function users_table_renders_role_badges_correctly()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Create a regular user
        $regularUser = User::factory()->create(['role' => UserRole::USER]);

        // Act as the admin user
        $this->actingAs($admin);

        // Get the component instance
        $component = new UsersTable;

        // Test admin badge rendering
        $adminBadge = $component->renderCustomColumn('role', $admin);
        $this->assertNotNull($adminBadge);
        $this->assertStringContainsString('Admin', (string) $adminBadge->render());
        $this->assertStringContainsString('red', (string) $adminBadge->render());

        // Test user badge rendering
        $userBadge = $component->renderCustomColumn('role', $regularUser);
        $this->assertNotNull($userBadge);
        $this->assertStringContainsString('User', (string) $userBadge->render());
        $this->assertStringContainsString('blue', (string) $userBadge->render());
    }
}
