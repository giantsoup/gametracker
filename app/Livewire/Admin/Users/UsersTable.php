<?php

namespace App\Livewire\Admin\Users;

use App\Enums\UserRole;
use App\Livewire\ResourceTable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UsersTable extends ResourceTable
{
    public string $model = User::class;

    public ?string $resource = 'admin.users';

    public array $columns = [
        'name' => 'Name',
        'email' => 'Email',
        'role' => 'Role',
        'created_at' => 'Created At',
    ];

    public array $searchFields = ['name', 'email', 'nickname'];

    /**
     * Map role values to their corresponding badge colors
     */
    public function getRoleBadge($role)
    {
        $colors = [
            UserRole::ADMIN->value => 'red',
            UserRole::USER->value => 'blue',
            // Add more roles and their colors here as needed
        ];

        $roleValue = $role->value;
        $color = $colors[$roleValue] ?? 'zinc';

        return [
            'color' => $color,
            'text' => ucfirst(strtolower($roleValue)),
        ];
    }

    public function hasDeletePermission(Model $model): bool
    {
        // Don't allow users to delete themselves
        return auth()->id() !== $model->id;
    }

    public function deleteUser($user): void
    {
        // Convert ID to User model if an integer is provided
        if (is_numeric($user)) {
            $user = User::find($user);

            if (! $user) {
                $this->dispatch('error', 'User not found');

                return;
            }
        }

        if (auth()->user()->id === $user->id) {
            $this->dispatch('error', 'You cannot delete yourself');

            return;
        }

        if ($user->id === 1) {
            $this->dispatch('error', 'You cannot delete the system user');

            return;
        }

        $user->delete();
        $this->dispatch('success', 'User deleted successfully');
    }
}
