<?php

namespace App\Livewire\Admin\Users;

use App\Enums\UserRole;
use App\Livewire\CrudTable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UsersTable extends CrudTable
{
    public string $modelName = User::class;

    public ?string $modelRouteBase = 'admin.users';

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
    public function getRoleBadge($role): array
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

    public function deleteModel($modelID): void
    {
        $model = $this->modelName::findOrFail($modelID);

        if (! $model) {
            $this->dispatch('error', class_basename($this->modelName).' not found');

            return;
        }

        if (auth()->user()->id === $model->id) {
            $this->dispatch('error', 'You cannot delete yourself');

            return;
        }

        if ($model->id === 1) {
            $this->dispatch('error', 'You cannot delete the system user');

            return;
        }

        $model->delete();
        $this->dispatch('success', 'User deleted successfully');
    }
}
