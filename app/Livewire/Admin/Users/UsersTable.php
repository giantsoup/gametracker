<?php

namespace App\Livewire\Admin\Users;

use App\Enums\UserRole;
use App\Livewire\CrudTable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Computed;

class UsersTable extends CrudTable
{
    public string $modelName = User::class;

    public string $modelRouteBase = 'admin.users';

    public array $columns = [
        'name' => 'Name',
        'email' => 'Email',
        'role' => 'Role',
        'created_at' => 'Created At',
    ];

    public array $searchFields = ['name', 'email', 'nickname'];

    // Define form properties - these will be used by the form renderer
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $role = '';

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

    /**
     * Define the form configuration for creating/editing users
     */
    public function getFormConfig(): array
    {
        return [
            [
                'name' => 'name',
                'label' => 'Name',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'email',
                'label' => 'Email',
                'type' => 'email',
                'required' => true,
            ],
            [
                'name' => 'password',
                'label' => 'Password',
                'type' => 'password',
                'required' => true,
            ],
            [
                'name' => 'role',
                'label' => 'Role',
                'type' => 'select',
                'required' => true,
                'options' => $this->roles(),
                'description' => 'Select the user\'s role to define their permissions',
            ],
        ];
    }

    #[Computed]
    public function roles(): array
    {
        return UserRole::getSelectOptions();
    }

    public function hasCreatePermission(?Model $model = null): bool
    {
        return auth()->user()->role === UserRole::ADMIN;
    }

    public function deleteModel(Model $model): void
    {
        if (! $this->hasDeletePermission($model)) {
            $this->dispatch('error', 'You do not have permission to delete this '.class_basename($model));

            return;
        }

        if ($model->id === 1) {
            $this->dispatch('error', 'You cannot delete the system user');

            return;
        }

        $model->delete();
        $this->dispatch('success', class_basename($model).' deleted successfully');
    }

    /**
     * @param  User  $model
     */
    public function hasDeletePermission(Model $model): bool
    {
        // Don't allow users to delete themselves
        return auth()->id() !== $model->id;
    }

    /**
     * Create a new user
     */
    public function createModel(Model $model): void
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
        ]);

        $this->toggleCreateForm();
        $this->dispatch('success', 'User created successfully');
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
            'role' => ['required', 'string'],
        ];
    }
}
