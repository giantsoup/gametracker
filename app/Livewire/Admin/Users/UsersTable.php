<?php

namespace App\Livewire\Admin\Users;

use App\Enums\UserRole;
use App\Livewire\CrudTable;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\ComponentAttributeBag;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class UsersTable extends CrudTable
{
    public bool $includePaginationFunctionality = false;

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

    public string $nickname = '';

    public string $email = '';

    public string $password = '';

    public string $role = 'user';

    public array $formData = [];

    public function mount(?string $resource = null): void
    {
        parent::mount($resource);

        $this->formData = $this->getCreateForm();
    }

    /**
     * Return the component name and component data for the create form
     */
    public function getCreateForm(): array
    {
        return [
            'component' => $this->getCreateFormComponent(),
            'componentData' => $this->getCreateComponentData(),
        ];
    }

    public function getCreateFormComponent(): ?string
    {
        return 'admin.users.create-user-form';
    }

    /**
     * Get the component data to pass to the create user form component
     */
    public function getCreateComponentData(): array
    {
        return [
            'roles' => $this->roles(), // Pass the roles array to the child component
        ];
    }

    #[Computed]
    public function roles(): array
    {
        return UserRole::getSelectOptions();
    }

    #[Computed(key: 'resources')]
    public function resources()
    {
        return $this->modelName::query()
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $subQuery) {
                    foreach ($this->searchFields as $field) {
                        $subQuery->orWhere($field, 'like', '%'.$this->search.'%');
                    }
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();
    }

    public function hasCreatePermission(?Model $model = null): bool
    {
        return auth()->user()->role === UserRole::ADMIN;
    }

    public function deleteModel(int $id): void
    {
        $model = $this->modelName::find($id);

        if (! $model) {
            $this->dispatch('error', "{$this->getFormattedResourceName()} not found");

            return;
        }

        if (! $this->hasDeletePermission($model)) {
            $this->dispatch('error', "You do not have permission to delete this {$this->getFormattedResourceName()}");

            return;
        }

        if ($model->id === 1) {
            $this->dispatch('error', 'You cannot delete the system user');

            return;
        }

        $model->delete();
        $this->dispatch('success', "{$this->getFormattedResourceName()} deleted successfully");
        $this->resetPage();
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
     * Listen for the user-created event and refresh the data
     */
    #[On('user-created')]
    public function refreshAfterUserCreated(): void
    {
        // Refresh the resources (users) data
        $this->resetPage();
    }

    /**
     * Render a custom column
     *
     * @param  string  $key  The column key
     * @param  mixed  $resource  The resource being rendered
     * @return mixed The rendered column content
     */
    public function renderCustomColumn($key, $resource)
    {
        if ($key === 'role') {
            $badge = $resource->getRoleBadge();

            return view('flux.badge.index', [
                'variant' => 'pill',
                'color' => $badge['color'],
                'slot' => $badge['text'],
                'attributes' => new ComponentAttributeBag,
            ]);
        }

        return null;
    }
}
