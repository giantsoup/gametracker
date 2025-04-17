<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FormRender extends Component
{
    /**
     * An array of form field configurations
     */
    public array $config;

    /**
     * The Livewire component that contains the form data
     */
    public object $model;

    /**
     * The name of the resource being created or edited
     */
    public string $resourceName;

    /**
     * The Livewire action to call when the form is submitted
     */
    public string $submitAction;

    /**
     * The Livewire action to call when the form is canceled
     */
    public string $cancelAction;

    /**
     * Create a new component instance.
     *
     * @param  array  $config  The form field configurations
     * @param  object  $model  The Livewire component that contains the form data
     * @param  string  $resourceName  The name of the resource being created or edited
     * @param  string  $submitAction  The Livewire action to call when the form is submitted
     * @param  string  $cancelAction  The Livewire action to call when the form is canceled
     */
    public function __construct(
        array $config,
        object $model,
        string $resourceName,
        string $submitAction = 'createModel',
        string $cancelAction = 'toggleCreateForm'
    ) {
        $this->config = $config;
        $this->model = $model;
        $this->resourceName = $resourceName;
        $this->submitAction = $submitAction;
        $this->cancelAction = $cancelAction;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('components.form-render');
    }
}
