<div class="absolute z-50 mt-2 w-full sm:w-96 bg-white dark:bg-zinc-800 rounded-lg shadow-lg border border-zinc-200 dark:border-zinc-700 p-4">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium">Create New {{ $resourceName }}</h3>
        <button wire:click="toggleCreateForm"
                class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300">
            <flux:icon icon="x-mark" variant="mini" class="h-5 w-5"/>
        </button>
    </div>

    <form wire:submit="createModel" class="space-y-4">
        @foreach($formConfig as $field)
            <div>
                @php
                    $inputType = $field['type'] ?? 'text';
                    $label = $field['label'] ?? Str::title($field['name']);
                    $required = $field['required'] ?? false;
                    $fieldName = $field['name'];
                @endphp
                <div
                        class="absolute z-50 mt-2 w-full sm:w-96 bg-white dark:bg-zinc-800 rounded-lg shadow-lg border border-zinc-200 dark:border-zinc-700 p-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Create {{ $resourceName }}</h3>
                        <button wire:click="toggleCreateForm"
                                class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300">
                            <flux:icon icon="x-mark" variant="mini" class="h-5 w-5"/>
                        </button>
                    </div>

                    <form wire:submit="createModel" class="space-y-4">
                        @foreach($formConfig as $field)
                            <div>
                                <flux:label
                                        for="{{ $field['name'] }}">{{ $field['label'] ?? Str::title($field['name']) }}</flux:label>

                                @if(($field['type'] ?? 'text') === 'select')
                                    <flux:select
                                            wire:model="{{ $field['name'] }}"
                                            id="{{ $field['name'] }}"
                                            class="w-full mt-1"
                                            {{ isset($field['required']) && $field['required'] ? 'required' : '' }}
                                    >
                                        <option value="">
                                            Select {{ $field['label'] ?? Str::title($field['name']) }}</option>
                                        @foreach($field['options'] ?? [] as $value => $label)
                                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                @else
                                    <flux:input
                                            wire:model="{{ $field['name'] }}"
                                            id="{{ $field['name'] }}"
                                            type="{{ $field['type'] ?? 'text' }}"
                                            class="w-full mt-1"
                                            {{ isset($field['required']) && $field['required'] ? 'required' : '' }}
                                    />
                                @endif

                                @error($field['name'])
                                <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror

                                @if(isset($field['description']))
                                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $field['description'] }}</p>
                                @endif
                            </div>
                        @endforeach

                        <div class="flex justify-end">
                            <flux:button
                                    variant="secondary"
                                    size="base"
                                    wire:click.prevent="toggleCreateForm"
                                    class="mr-2"
                            >
                                Cancel
                            </flux:button>
                            <flux:button
                                    variant="primary"
                                    size="base"
                                    type="submit"
                            >
                                Create
                            </flux:button>
                        </div>
                    </form>
                </div>
                <flux:label for="{{ $fieldName }}">{{ $label }}</flux:label>

                @if($inputType === 'select')
                    <flux:select
                            wire:model="{{ $fieldName }}"
                            id="{{ $fieldName }}"
                            class="w-full mt-1"
                            @if($required) required @endif
                    >
                        <option value="">Select {{ $label }}</option>
                        @foreach($field['options'] as $value => $optionLabel)
                            <flux:select.option value="{{ $value }}">{{ $optionLabel }}</flux:select.option>
                        @endforeach
                    </flux:select>
                @else
                    <flux:input
                            wire:model="{{ $fieldName }}"
                            id="{{ $fieldName }}"
                            type="{{ $inputType }}"
                            class="w-full mt-1"
                            @if($required) required @endif
                    />
                @endif

                @error($fieldName) <span class="text-sm text-red-500">{{ $message }}</span> @enderror

                @if(isset($field['description']))
                    <flux:text
                            class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $field['description'] }}</flux:text>
                @endif
            </div>
        @endforeach

        <div class="flex justify-end">
            <flux:button
                    variant="secondary"
                    size="base"
                    wire:click.prevent="toggleCreateForm"
                    class="mr-2"
            >
                Cancel
            </flux:button>
            <flux:button
                    variant="primary"
                    size="base"
                    type="submit"
            >
                Create {{ $resourceName }}
            </flux:button>
        </div>
    </form>
</div>
