<div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-12">
    <x-js-debug />

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h1 class="text-2xl font-bold mb-6">Livewire Button Test</h1>

                <div class="mb-8">
                    <p class="text-lg mb-2">Current counter value: <span class="font-bold">{{ $counter }}</span></p>

                    @if($message)
                        <div class="mt-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-md">
                            {{ $message }}
                        </div>
                    @endif
                </div>

                <div class="flex space-x-4">
                    <button
                        wire:click="increment"
                        dusk="increment-button"
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md"
                    >
                        Increment (+1)
                    </button>

                    <button
                        wire:click="decrement"
                        dusk="decrement-button"
                        class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md"
                    >
                        Decrement (-1)
                    </button>

                    <button
                        wire:click="resetCounter"
                        dusk="reset-button"
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md"
                    >
                        Reset
                    </button>
                </div>

                <div class="mt-8 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                    <h2 class="text-lg font-semibold mb-2">Debug Information</h2>
                    <p>This component is used to test if Livewire buttons are working correctly.</p>
                    <p class="mt-2">If the buttons above work, you should see the counter value change and a message appear.</p>
                </div>
            </div>
        </div>
    </div>
</div>
