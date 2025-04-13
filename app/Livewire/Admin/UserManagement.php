<?php

namespace App\Livewire\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public $name = '';

    public $email = '';

    public $role = 'user';

    public $searchTerm = '';

    public $editingUserId = null;

    public $confirmingUserDeletion = false;

    public $userIdToDelete = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'role' => 'required|string',
    ];

    public function mount()
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function createUser()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make(Str::random(24)),
            'role' => $this->role,
        ]);

        $this->reset(['name', 'email', 'role']);
        $this->dispatch('user-created');
    }

    public function editUser($userId)
    {
        $this->editingUserId = $userId;
        $user = User::findOrFail($userId);
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role->value;

        // Reset validation errors
        $this->resetValidation();

        // Update the rules for editing to exclude the current user's email
        $this->rules['email'] = 'required|email|unique:users,email,'.$userId;
    }

    public function updateUser()
    {
        $this->validate();

        $user = User::findOrFail($this->editingUserId);
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ]);

        $this->reset(['name', 'email', 'role', 'editingUserId']);
        $this->dispatch('user-updated');
    }

    public function confirmUserDeletion($userId)
    {
        // Prevent admins from deleting themselves
        if (auth()->id() == $userId) {
            $this->dispatch('error', 'You cannot delete your own account.');

            return;
        }

        $this->confirmingUserDeletion = true;
        $this->userIdToDelete = $userId;
    }

    public function deleteUser()
    {
        User::findOrFail($this->userIdToDelete)->delete();
        $this->confirmingUserDeletion = false;
        $this->userIdToDelete = null;
        $this->dispatch('user-deleted');
    }

    public function cancelDelete()
    {
        $this->confirmingUserDeletion = false;
        $this->userIdToDelete = null;
    }

    public function cancelEdit()
    {
        $this->reset(['name', 'email', 'role', 'editingUserId']);
        $this->resetValidation();
    }

    public function render()
    {
        $users = User::when($this->searchTerm, function ($query) {
            return $query->where('name', 'like', '%'.$this->searchTerm.'%')
                ->orWhere('email', 'like', '%'.$this->searchTerm.'%');
        })->paginate(10);

        $roles = UserRole::getSelectOptions();

        return view('livewire.admin.user-management', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }
}
