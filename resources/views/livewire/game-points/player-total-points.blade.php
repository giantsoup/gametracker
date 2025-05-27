<div class="w-full">
    <div class="bg-white dark:bg-zinc-800 shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-zinc-900 dark:text-zinc-100">
                Player Points Summary
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-zinc-500 dark:text-zinc-400">
                {{ $user->name }}
            </p>
        </div>
        <div class="border-t border-zinc-200 dark:border-zinc-700">
            <dl>
                <div class="bg-zinc-50 dark:bg-zinc-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                        Total Points
                    </dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100 sm:mt-0 sm:col-span-2">
                        <span class="text-2xl font-bold">{{ $totalPoints }}</span>
                    </dd>
                </div>
                <div class="bg-white dark:bg-zinc-900 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                        Placement Statistics
                    </dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100 sm:mt-0 sm:col-span-2">
                        <div class="flex space-x-4">
                            <div class="flex flex-col items-center">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300">
                                    {{ $placementStats['first'] }}
                                </span>
                                <span class="text-xs mt-1">1st Place</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300">
                                    {{ $placementStats['second'] }}
                                </span>
                                <span class="text-xs mt-1">2nd Place</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-300">
                                    {{ $placementStats['third'] }}
                                </span>
                                <span class="text-xs mt-1">3rd Place</span>
                            </div>
                        </div>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    @if($showBreakdown && $pointsBreakdown)
        <div class="mt-6 bg-white dark:bg-zinc-800 shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg leading-6 font-medium text-zinc-900 dark:text-zinc-100">
                    Points Breakdown
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Game
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Placement
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Points
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach($pointsBreakdown as $point)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        <a href="{{ route('games.show', $point['game']) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            {{ $point['game']->name }}
                                        </a>
                                    </div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $point['game']->event->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        @if($point['placement'])
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">
                                                {{ $point['placement'] }}{{ __('st') }}
                                            </span>
                                        @else
                                            <span class="text-zinc-500 dark:text-zinc-400">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $point['points'] }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="mt-4 flex justify-end">
        <flux:button
            wire:click="toggleBreakdown"
            variant="outline"
        >
            {{ $showBreakdown ? __('Hide Breakdown') : __('Show Breakdown') }}
        </flux:button>
    </div>
</div>
