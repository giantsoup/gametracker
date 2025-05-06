<?php

namespace App\Livewire;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
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

    public bool $includePaginationFunctionality = true;

    public bool $includeSearchFunctionality = true;

    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    public string $modelName;

    public string $modelRouteBase;

    public array $columns;

    public array $searchFields;

    public bool $showCreateForm = false;

    public array $formData = [];

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
     * Get the singular form of the resource name
     * This can be overridden by child classes to implement custom resource naming
     */
    public function getSingularResourceName(): string
    {
        return Str::singular(Str::afterLast($this->getResourceName(), '.'));
    }

    /**
     * Check if a column is a date or datetime type based on casting
     *
     * @param  string  $column  Column name
     * @return string|null Returns 'date', 'datetime', or null if not a date type
     */
    public function getColumnDateType(string $column): ?string
    {
        if (! $this->modelInstance) {
            return null;
        }

        // Check if the model has cast definitions for this column
        $casts = $this->modelInstance->getCasts();

        if (isset($casts[$column])) {
            $cast = $casts[$column];

            // Check for date-related cast types
            if ($cast === 'date') {
                return 'date';
            }

            if (in_array($cast, ['datetime', 'timestamp', 'immutable_datetime', 'immutable_date', 'custom_datetime', 'timestamp'])) {
                return 'datetime';
            }

            // Handle custom cast classes that might be date-related
            if (class_exists($cast) && is_subclass_of($cast, CastsAttributes::class)) {
                // If it's a custom cast class, we'll assume it's not a date for safety
                return null;
            }
        }

        // Check if the attribute is a date by examining the model's $dates array (legacy approach)
        if (method_exists($this->modelInstance, 'getDates')) {
            $dates = $this->modelInstance->getDates();
            if (in_array($column, $dates)) {
                return 'datetime';
            }
        }

        // Last resort - check if this is a timestamp attribute
        if ($column === $this->modelInstance->getCreatedAtColumn() ||
            $column === $this->modelInstance->getUpdatedAtColumn()) {
            return 'datetime';
        }

        return null;
    }

    public function getResourceCountSummary(): string
    {
        return sprintf(
            'Total %s: %d',
            $this->getFormattedResourceName(true),
            $this->resources->count()
        );
    }

    /**
     * Get a formatted singular resource name for display in buttons and labels
     *
     * @param  bool  $plural  Whether to return the plural version of the resource name
     */
    public function getFormattedResourceName(bool $plural = false): string
    {
        $routeName = $this->getResourceName();
        $name = Str::replace('-', ' ', Str::afterLast($routeName, '.'));

        if ($plural) {
            return Str::title(Str::plural($name));
        }

        return Str::title(Str::singular($name));
    }
}
