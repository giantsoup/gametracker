<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Users extends CrudTable
{
    public string $modelName = User::class;

    public string $modelRouteBase = 'users';

    public array $columns = [
        'name' => 'Name',
        'email' => 'Email',
        'role' => 'Role',
        'created_at' => 'Created At',
    ];

    public array $searchFields = [
        'name',
        'email',
    ];

    // Form fields
    public $name = '';

    public $email = '';

    public $password = '';

    public $role = '';

    /**
     * Setup form validation rules
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Password::defaults()],
            'role' => ['required', 'string', 'in:admin,user'],
        ];
    }

    /**
     * Implementation of getFormConfig from FormConfigProvider
     *
     * Defines the form fields for the user creation form
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
                'options' => $this->getRoles(),
            ],
        ];
    }

    /**
     * Get roles as options for the select dropdown
     */
    public function getRoles()
    {
        return [
            'admin' => 'Administrator',
            'user' => 'Regular User',
        ];
    }

    /**
     * Implementation of createModel from CrudTable
     *
     * Create a new user instance
     */
    public function createModel(Model $model): void
    {
        // Validate form input
        $this->validate();

        // Create the user (we don't use the passed model parameter since we're creating a new one)
        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
        ]);

        // Reset form and show success message
        $this->resetForm();
        $this->showCreateForm = false;
        $this->dispatch('success', 'User created successfully');
    }

    /**
     * Get role badge display properties
     */
    public function getRoleBadge($role)
    {
        return match ($role) {
            'admin' => [
                'color' => 'purple',
                'text' => 'Administrator',
            ],
            'user' => [
                'color' => 'blue',
                'text' => 'User',
            ],
            default => [
                'color' => 'gray',
                'text' => $role,
            ],
        };
    }
}
