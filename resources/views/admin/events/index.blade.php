<x-layouts.app :title="__('Events')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <x-card>
            <div class="flex flex-col gap-6">
                <livewire:admin.events.events-table/>
            </div>
        </x-card>
    </div>
</x-layouts.app>
