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
use LogicException;

class CrudTable extends Component
{
    use WithPagination;

    public bool $includeCreateFunctionality = true;

    public bool $includeDeleteFunctionality = true;

    public bool $includeShowFunctionality = true;

    public bool $includeEditFunctionality = true;

    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    public string $modelName;

    public string $modelRouteBase;

    public array $columns;

    public array $searchFields;

    public bool $showCreateForm = false;

    protected ?Model $modelInstance = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10],
    ];

    public function toggleCreateForm(): void
    {
        $this->showCreateForm = ! $this->showCreateForm;
    }

    public function resetForm(): void
    {
        // Reset all public properties that start with "form"
        $properties = collect(get_object_vars($this))
            ->filter(fn ($value, $key) => Str::startsWith($key, 'form'))
            ->keys();

        $this->reset($properties->toArray());
        $this->resetValidation();
    }

    public function mount(?string $resource = null): void
    {
        if ($resource) {
            $this->modelRouteBase = $resource;
        }

        $this->validateRequiredProperties();

        // Initialize the model instance for permission checks
        $this->modelInstance = new $this->modelName;
    }

    /**
     * Validates that all required properties are set for the component to function properly
     *
     * @throws LogicException When required properties are missing
     */
    protected function validateRequiredProperties(): void
    {
        $errors = [];

        if (empty($this->modelName)) {
            $errors[] = '$modelName is required - you must specify the fully qualified model class name';
        } elseif (! class_exists($this->modelName)) {
            $errors[] = "\$modelName '{$this->modelName}' does not exist or could not be loaded";
        }

        if (empty($this->modelRouteBase)) {
            $errors[] = '$modelRouteBase is required - you must specify the base route name for this resource';
        }

        if (empty($this->columns)) {
            $errors[] = '$columns array is required - you must specify the columns to display';
        }

        if (empty($this->searchFields)) {
            $errors[] = '$searchFields array is required - you must specify the fields to search on';
        }

        if (! empty($errors)) {
            $errorMessage = "CrudTable component cannot initialize:\n- ".implode("\n- ", $errors);
            throw new LogicException($errorMessage);
        }
    }

    /**
     * Optionally provide a create form component.
     * Override this in the child class to return a non-null value.
     */
    public function getCreateFormComponent(): ?string
    {
        return null;
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
        return $this->modelName::query()
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

    public function hasStorePermission(Model $model): bool
    {
        // This can be overridden by child classes to implement permission checks
        return true;
    }

    /**
     * Create a new model instance.
     * This method MUST be implemented in child classes.
     */
    public function createModel(Model $model): void
    {
        $className = class_basename($this);
        throw new LogicException("The createModel() method must be implemented in the {$className} class.");
    }

    public function hasEditPermission(Model $model): bool
    {
        // This can be overridden by child classes to implement permission checks
        return true;
    }

    /**
     * Delete a model instance.
     * Default implementation that works with any model.
     * Can be overridden for custom delete logic.
     */
    public function deleteModel(Model $model): void
    {
        if (! $this->hasDeletePermission($model)) {
            $this->dispatch('error', 'You do not have permission to delete this '.class_basename($model));

            return;
        }

        $model->delete();
        $this->dispatch('success', class_basename($model).' deleted successfully');
    }

    public function hasDeletePermission(Model $model): bool
    {
        // This can be overridden by child classes to implement permission checks
        return true;
    }

    public function render(): View
    {
        //        dd(class_exists($this->modelName));
        // Initialize modelInstance if we have a valid model class
        if (class_exists($this->modelName)) {
            $this->modelInstance = new $this->modelName;
        }

        return view('livewire.crud-table', [
            'resources' => $this->resources,
            'canCreate' => $this->hasCreatePermission() && Route::has($this->getResourceName().'.create'),
            'createRoute' => $this->getResourceName().'.create',
            'hasShow' => Route::has($this->getResourceName().'.show'),
            'hasEdit' => Route::has($this->getResourceName().'.edit'),
            'hasDestroy' => Route::has($this->getResourceName().'.destroy'),
        ]);
    }

    /**
     * Check if the user has permission to create a model.
     * Can be overridden by child classes to implement permission checks.
     */
    public function hasCreatePermission(?Model $model = null): bool
    {
        // This can be overridden by child classes to implement permission checks
        return true;
    }

    public function getResourceName(): string
    {
        return $this->modelRouteBase;
    }

    public function getFormConfig(): array
    {
        return [];
    }

    /**
     * Get a formatted singular resource name for display in buttons and labels
     */
    public function getFormattedResourceName(): string
    {
        $routeName = $this->getResourceName();

        return Str::singular(Str::title(Str::replace('-', ' ', Str::afterLast($routeName, '.'))));
    }

    /**
     * Get the singular form of the resource name
     * This can be overridden by child classes to implement custom resource naming
     */
    public function getSingularResourceName(): string
    {
        return Str::singular(Str::afterLast($this->getResourceName(), '.'));
    }
}
