<div class="absolute z-50 mt-2 w-full sm:w-96 bg-white dark:bg-zinc-800 rounded-lg shadow-lg border border-zinc-200 dark:border-zinc-700 p-4">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium">Create {{ $resourceName }}</h3>
        <button wire:click="{{ $cancelAction }}"
                class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300">
            <flux:icon icon="x-mark" variant="mini" class="h-5 w-5"/>
        </button>
    </div>

    <form wire:submit="{{ $submitAction }}" class="space-y-4">
        @foreach($config as $field)
            <div>
                @php
                    $inputType = $field['type'] ?? 'text';
                    $label = $field['label'] ?? Str::title($field['name']);
                    $required = $field['required'] ?? false;
                    $modelProperty = $field['name'];
                @endphp

                <flux:label for="{{ $modelProperty }}">{{ $label }}</flux:label>

                @if($inputType === 'select')
                    <flux:select
                        wire:model="{{ $modelProperty }}"
                        id="{{ $modelProperty }}"
                        class="w-full mt-1"
                        @if($required) required @endif
                    >
                        <option value="">Select {{ $label }}</option>
                        @foreach($field['options'] ?? [] as $value => $optionLabel)
                            <flux:select.option value="{{ $value }}">{{ $optionLabel }}</flux:select.option>
                        @endforeach
                    </flux:select>
                @elseif($inputType === 'textarea')
                    <flux:textarea
                        wire:model="{{ $modelProperty }}"
                        id="{{ $modelProperty }}"
                        class="w-full mt-1"
                        @if($required) required @endif
                    ></flux:textarea>
                @elseif($inputType === 'checkbox')
                    <div class="mt-1">
                        <flux:checkbox
                            wire:model="{{ $modelProperty }}"
                            id="{{ $modelProperty }}"
                            @if($required) required @endif
                        />
                    </div>
                @elseif($inputType === 'radio')
                    <div class="mt-1 space-y-2">
                        @foreach($field['options'] ?? [] as $value => $optionLabel)
                            <div class="flex items-center">
                                <flux:radio
                                    wire:model="{{ $modelProperty }}"
                                    id="{{ $modelProperty }}_{{ $value }}"
                                    value="{{ $value }}"
                                    @if($required) required @endif
                                />
                                <flux:label for="{{ $modelProperty }}_{{ $value }}" class="ml-2">
                                    {{ $optionLabel }}
                                </flux:label>
                            </div>
                        @endforeach
                    </div>
                @else
                    <flux:input
                        wire:model="{{ $modelProperty }}"
                        id="{{ $modelProperty }}"
                        type="{{ $inputType }}"
                        class="w-full mt-1"
                        @if($required) required @endif
                    />
                @endif

                @error($modelProperty)
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror

                @if(isset($field['description']))
                    <flux:text class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $field['description'] }}
                    </flux:text>
                @endif
            </div>
        @endforeach

        <div class="flex justify-end">
            <flux:button
                variant="secondary"
                size="base"
                wire:click.prevent="{{ $cancelAction }}"
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
