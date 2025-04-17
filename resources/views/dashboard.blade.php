<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-2">
            <x-card>
                <livewire:admin.users-table/>
            </x-card>
            <x-card>
            </x-card>
        </div>

        <x-card class="h-full flex-1">
            <div class="flex flex-col gap-6">
                <livewire:admin.users-table/>
            </div>
        </x-card>
    </div>
</x-layouts.app>
