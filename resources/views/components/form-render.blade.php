<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium">Create {{ $resourceName }}</h3>
        <button wire:click="{{ $cancelAction }}"
                class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300">
            <flux:icon icon="x-mark" variant="mini" class="h-5 w-5"/>
        </button>
    </div>
    @dump($model)
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

                @if($inputType === 'text')
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
                variant="outline"
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
