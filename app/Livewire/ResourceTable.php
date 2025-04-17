<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ResourceTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    // This can be overridden by the parent component
    public string $model;

    public array $columns = [];

    public array $searchFields = ['name', 'email'];

    public ?string $resource = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10],
    ];

    public function mount(?string $resource = null)
    {
        if ($resource) {
            $this->resource = $resource;
        }
    }

    public function sortBy(string $field): void
    {
        $this->sortDirection = $this->sortField === $field
            ? $this->sortDirection === 'asc' ? 'desc' : 'asc'
            : 'asc';

        $this->sortField = $field;
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed(key: 'resources')]
    public function resources()
    {
        return $this->model::query()
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $subQuery) {
                    foreach ($this->searchFields as $field) {
                        $subQuery->orWhere($field, 'like', '%'.$this->search.'%');
                    }
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function hasEditPermission(Model $model): bool
    {
        // This can be overridden by child classes to implement permission checks
        return true;
    }

    public function hasDeletePermission(Model $model): bool
    {
        // This can be overridden by child classes to implement permission checks
        return true;
    }

    public function deleteResource(Model $resource): void
    {
        $resource->delete();
        $this->dispatch('success', class_basename($resource).' deleted successfully');
    }

    public function render(): View
    {
        return view('livewire.resource-table', [
            'resources' => $this->resources,
            'canCreate' => $this->hasCreatePermission() && Route::has($this->getResourceName().'.create'),
            'createRoute' => $this->getResourceName().'.create',
            'hasShow' => Route::has($this->getResourceName().'.show'),
            'hasEdit' => Route::has($this->getResourceName().'.edit'),
            'hasDestroy' => Route::has($this->getResourceName().'.destroy'),
        ]);
    }

    public function hasCreatePermission(): bool
    {
        // This can be overridden by child classes to implement permission checks
        return true;
    }

    public function getResourceName(): string
    {
        if (! $this->resource) {
            // Try to guess from the model class name
            $modelClass = class_basename($this->model);

            return Str::plural(Str::kebab($modelClass));
        }

        return $this->resource;
    }

    /**
     * Get a formatted singular resource name for display in buttons and labels
     */
    public function getFormattedResourceName(): string
    {
        $routeName = $this->getResourceName();

        return Str::singular(Str::title(Str::replace('-', ' ', Str::afterLast($routeName, '.'))));
    }
}
