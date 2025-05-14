<?php

namespace App\Livewire\Admin\Events;

use App\Livewire\CrudTable;
use App\Models\Event;
use Illuminate\View\ComponentAttributeBag;
use Livewire\Attributes\On;

class EventsTable extends CrudTable
{
    public string $modelName = Event::class;

    public string $modelRouteBase = 'admin.events';

    public array $columns = [
        'name' => 'Name',
        'active' => 'Status',
        'starts_at' => 'Starts At',
        'ends_at' => 'Ends At',
        'created_at' => 'Created At',
    ];

    public array $searchFields = ['name'];

    // Define form properties
    public string $name = '';

    public bool $active = false;

    public ?string $starts_at = null;

    public ?string $ends_at = null;

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
        return 'admin.events.create-event-form';
    }

    /**
     * Get the component data to pass to the create event form component
     */
    public function getCreateComponentData(): array
    {
        return [];
    }

    /**
     * Listen for the event-created event and refresh the data
     */
    #[On('event-created')]
    public function refreshAfterEventCreated(): void
    {
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
        if ($key === 'active') {
            $color = $resource->isActive() ? 'green' : 'zinc';
            $text = $resource->isActive() ? 'Active' : 'Inactive';

            return view('flux.badge.index', [
                'variant' => 'pill',
                'color' => $color,
                'slot' => $text,
                'attributes' => new ComponentAttributeBag,
            ]);
        }

        return null;
    }
}
