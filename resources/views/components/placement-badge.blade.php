@props([
    'placement',
    'suffix' => null,
])

@php
    $placement = (int) $placement;
    $label = \Illuminate\Support\Number::ordinal($placement);

    if ($suffix !== null && $suffix !== '') {
        $label .= ' '.$suffix;
    }

    $toneClasses = match ($placement) {
        1 => 'bg-amber-100 text-amber-900 ring-amber-300/80 dark:bg-amber-500/15 dark:text-amber-200 dark:ring-amber-400/30',
        2 => 'bg-slate-100 text-slate-800 ring-slate-300/80 dark:bg-slate-400/15 dark:text-slate-200 dark:ring-slate-400/30',
        3 => 'bg-orange-100 text-orange-900 ring-orange-300/80 dark:bg-orange-500/15 dark:text-orange-200 dark:ring-orange-400/30',
        default => 'bg-blue-100 text-blue-800 ring-blue-300/80 dark:bg-blue-900/20 dark:text-blue-300 dark:ring-blue-400/20',
    };
@endphp

<span {{ $attributes->class([
    'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset',
    $toneClasses,
]) }}>
    {{ $slot->isNotEmpty() ? $slot : $label }}
</span>
