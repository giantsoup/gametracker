<?php

namespace App\Livewire\Games;

use App\Livewire\CrudTable;
use App\Models\Game;
use Livewire\Attributes\On;

class GamesTable extends CrudTable
{
    public string $modelName = Game::class;

    public string $modelRouteBase = 'games';

    public array $columns = [
        'name' => 'Name',
        'event.name' => 'Event',
        'duration' => 'Duration',
        'created_at' => 'Created At',
    ];

    public array $searchFields = ['name', 'event.name'];

    // Define form properties
    public string $name = '';

    public ?int $event_id = null;

    public int $duration = 60;

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
        return 'games.create-game-form';
    }

    /**
     * Get the component data to pass to the create game form component
     */
    public function getCreateComponentData(): array
    {
        return [];
    }

    /**
     * Listen for the game-created event and refresh the data
     */
    #[On('game-created')]
    public function refreshAfterGameCreated(): void
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
        if ($key === 'duration') {
            return $resource->getDurationForHumans();
        }

        return null;
    }
}
