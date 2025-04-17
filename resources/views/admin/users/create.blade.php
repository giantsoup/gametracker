<x-layouts.app>
    <x-slot:header>
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Create User
        </h2>
    </x-slot:header>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <livewire:admin.users.create-user-form/>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app><?php
