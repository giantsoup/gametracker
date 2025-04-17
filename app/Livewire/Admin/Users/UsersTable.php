<?php

namespace App\Livewire\Admin\Users;

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

    public function hasDeletePermission(Model $model): bool
    {
        // Don't allow users to delete themselves
        return auth()->id() !== $model->id;
    }

    public function deleteResource(User|Model $resource): void
    {
        if (auth()->user()->id === $resource->id) {
            $this->dispatch('error', 'You cannot delete yourself');

            return;
        }

        if ($resource->id === 1) {
            $this->dispatch('error', 'You cannot delete the system user');

            return;
        }

        $resource->delete();
        $this->dispatch('success', 'User deleted successfully');
    }
}
