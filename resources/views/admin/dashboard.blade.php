<x-layouts.app>
    <x-slot:header>
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Admin Dashboard
        </h2>
    </x-slot:header>

    <div class="">
        <div class="mx-auto max-w-7xl">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="mb-4 text-lg font-semibold">Admin Actions</h3>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <a href="{{ route('admin.users.index') }}"
                           class="p-4 transition-all bg-white border rounded-md shadow-sm hover:bg-gray-50">
                            <h4 class="font-medium">Manage Users</h4>
                            <p class="text-sm text-gray-500">Create, edit and delete users</p>
                        </a>
                        <a href="{{ route('admin.users-management') }}"
                           class="p-4 transition-all bg-white border rounded-md shadow-sm hover:bg-gray-50">
                            <h4 class="font-medium">User Management (Livewire)</h4>
                            <p class="text-sm text-gray-500">Interactive user management</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
