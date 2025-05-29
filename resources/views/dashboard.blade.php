<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <x-card>
            <livewire:logged-in-dashboard />
        </x-card>
    </div>
</x-layouts.app>
