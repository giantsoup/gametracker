<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <x-card aspect-ratio="aspect-video">
                {{-- Card content here --}}
                <x-placeholder-pattern
                    class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20"/>
            </x-card>

            <x-card aspect-ratio="aspect-video">
                {{-- Card content here --}}
                <x-placeholder-pattern
                    class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20"/>
            </x-card>

            <x-card aspect-ratio="aspect-video">
                {{-- Card content here --}}
                <x-placeholder-pattern
                    class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20"/>
            </x-card>
        </div>

        <x-card class="h-full flex-1">
            {{-- Card content here --}}
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20"/>
        </x-card>
    </div>
</x-layouts.app>
