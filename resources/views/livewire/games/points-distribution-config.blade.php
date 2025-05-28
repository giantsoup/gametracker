<div class="space-y-6">
    <div class="bg-white dark:bg-zinc-800 shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">Points Distribution Configuration</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="totalPoints" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Total Points to Distribute
                </label>
                <input
                    type="number"
                    id="totalPoints"
                    wire:model.live="totalPoints"
                    min="1"
                    class="mt-1 block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
            </div>

            <div>
                <label for="pointsRecipients" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Number of Players Receiving Points
                </label>
                <input
                    type="number"
                    id="pointsRecipients"
                    wire:model.live="pointsRecipients"
                    min="1"
                    class="mt-1 block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
            </div>
        </div>

        <div class="mt-4">
            <div class="flex items-center">
                <button
                    type="button"
                    wire:click="toggleCustomDistribution"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    {{ $useCustomDistribution ? 'Use Auto Distribution' : 'Customize Distribution' }}
                </button>

                @if(!$isValid)
                    <span class="ml-4 text-red-600 text-sm">
                        Total of distributed points ({{ $pointsSum }}) does not match the configured total ({{ $totalPoints }})
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-800 shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">Points Distribution</h3>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Placement
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Points
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-800">
                    @foreach(range(1, $pointsRecipients) as $placement)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $placement }}{{ $placement === 1 ? 'st' : ($placement === 2 ? 'nd' : ($placement === 3 ? 'rd' : 'th')) }} Place
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($useCustomDistribution)
                                    <input
                                        type="number"
                                        wire:model.live="pointsDistribution.{{ $placement }}"
                                        min="0"
                                        class="w-20 rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    >
                                @else
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $pointsDistribution[$placement] ?? 0 }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
