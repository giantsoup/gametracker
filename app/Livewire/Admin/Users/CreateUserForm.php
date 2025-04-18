<?php

namespace App\Livewire\Admin\Users;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Create User')]
class CreateUserForm extends Component
{
    public string $name = '';

    public string $nickname = '';

    public string $email = '';

    public string $role = 'user'; // Default role

    #[Computed]
    public function roles(): array
    {
        return UserRole::getSelectOptions();
    }

    public function create(): void
    {
        $this->validate();

        // Create user with random password
        User::create([
            'name' => $this->name,
            'nickname' => $this->nickname,
            'email' => $this->email,
            'password' => Hash::make(Str::random(24)),
            'role' => $this->role,
        ]);

        // Using Laravel's session flash for a success message
        session()->flash('success', 'User created successfully. They can now use passwordless login.');

        $this->redirect(route('admin.users.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.users.create-user-form');
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role' => ['required', Rule::in(UserRole::getValues())],
        ];
    }
}
