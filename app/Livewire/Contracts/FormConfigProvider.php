<?php
<?php

namespace App\Livewire\Contracts;

/**
 * Interface FormConfigProvider
 *
 * This interface defines methods that should be implemented by components
 * that provide form field configurations.
 */
interface FormConfigProvider
{
    /**
     * Get the form field definitions
     *
     * @return array Array of form field definitions, where each definition is an array with:
     *               - name: Field name (required)
     *               - label: Field label (optional)
     *               - type: Input type (text, email, password, select, etc.) (optional, defaults to text)
     *               - required: Whether the field is required (optional, defaults to false)
     *               - options: For select fields, an array of options (optional)
     *               - description: Help text to display below the field (optional)
     */
    public function getFormConfig(): array;
}
namespace App\Livewire\Contracts;

/**
 * Interface FormConfigProvider
 *
 * This interface defines the contract for components that provide form configurations.
 * Implementing this interface ensures that a component can provide form field definitions
 * that can be used by the FormRenderer trait to generate form inputs.
 */
interface FormConfigProvider
{
    /**
     * Get the form field definitions
     *
     * This method should return an array of form field definitions, where each
     * field definition is an associative array with the following keys:
     * - name: The name of the form field (required)
     * - label: The label to display for the form field (optional)
     * - type: The input type (text, email, password, select, etc.) (optional, defaults to text)
     * - required: Whether the field is required (optional, defaults to false)
     * - options: An array of options for select fields (required for select fields)
     * - description: A description to display below the field (optional)
     *
     * @return array Array of form field definitions
     */
    public function getFormConfig(): array;

    /**
     * Create a new model instance using the form data
     */
    public function createModel(): void;
}
