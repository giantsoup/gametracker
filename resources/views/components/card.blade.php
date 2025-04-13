@props([
    'title' => null,
    'footer' => null,
    'headerActions' => null,
    'padding' => true,
    'aspectRatio' => null,
])

<div {{ $attributes->merge([
    'class' => 'overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700' .
    ($aspectRatio ? ' relative ' . $aspectRatio : '')
]) }}>
    @if ($title || $headerActions)
        <div class="flex items-center justify-between border-b border-neutral-200 px-4 py-3 dark:border-neutral-700">
            @if ($title)
                <h3 class="font-medium text-neutral-900 dark:text-white">{{ $title }}</h3>
            @endif

            @if ($headerActions)
                <div class="flex items-center space-x-2">
                    {{ $headerActions }}
                </div>
            @endif
        </div>
    @endif

    <div @class([
        'relative',
        'p-4' => $padding && !$slot->isEmpty(),
    ])>
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="border-t border-neutral-200 px-4 py-3 dark:border-neutral-700">
            {{ $footer }}
        </div>
    @endif
</div>
