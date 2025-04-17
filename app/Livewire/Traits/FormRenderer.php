<?php

namespace App\Livewire\Traits;

use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Trait FormRenderer
 *
 * This trait provides form rendering capabilities for Livewire components.
 * It helps with creating, validating, and rendering forms in a standardized way.
 */
trait FormRenderer
{
    /**
     * Whether the create form should be displayed
     */
    public bool $showCreateForm = false;

    /**
     * Toggle the visibility of the create form
     */
    public function toggleCreateForm(): void
    {
        $this->showCreateForm = ! $this->showCreateForm;
        $this->resetForm();
    }

    /**
     * Reset the form fields and validation
     */
    public function resetForm(): void
    {
        // Reset all form-related properties that might be defined in the component
        $properties = collect(get_object_vars($this))
            ->filter(fn ($value, $key) => Str::startsWith($key, 'form') ||
                in_array($key, $this->getFormFieldNames())
            )
            ->keys();

        $this->reset($properties->toArray());
        $this->resetValidation();
    }

    /**
     * Get the form field names from the configuration
     */
    public function getFormFieldNames(): array
    {
        return collect($this->getFormConfig())
            ->pluck('name')
            ->toArray();
    }

    /**
     * Get the form field definitions
     *
     * This method should be overridden by implementing classes to define
     * the form fields and their properties.
     *
     * @return array Array of form field definitions
     */
    public function getFormConfig(): array
    {
        // This should be overridden by implementing classes
        return [];
    }

    /**
     * Render the form in the specified view
     */
    public function renderForm(): View
    {
        // Get the custom form view name if it exists in the implementing class
        $customFormView = $this->getCustomFormView();

        if ($customFormView && view()->exists($customFormView)) {
            return view($customFormView, [
                'formConfig' => $this->getFormConfig(),
                'component' => $this,
            ]);
        }

        // Fall back to the default form view
        return view('livewire.partials.default-form', [
            'formConfig' => $this->getFormConfig(),
            'component' => $this,
            'resourceName' => $this->getFormattedResourceName(),
        ]);
    }

    /**
     * Get the custom form view name if defined by the implementing class
     */
    protected function getCustomFormView(): ?string
    {
        return property_exists($this, 'customFormView') ? $this->customFormView : null;
    }
}
