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
                                <x-placement-badge :placement="1" class="h-8 w-8 justify-center px-0 text-sm">
                                    {{ $placementStats['first'] }}
                                </x-placement-badge>
                                <x-placement-badge :placement="1" suffix="Place" class="mt-1" />
                            </div>
                            <div class="flex flex-col items-center">
                                <x-placement-badge :placement="2" class="h-8 w-8 justify-center px-0 text-sm">
                                    {{ $placementStats['second'] }}
                                </x-placement-badge>
                                <x-placement-badge :placement="2" suffix="Place" class="mt-1" />
                            </div>
                            <div class="flex flex-col items-center">
                                <x-placement-badge :placement="3" class="h-8 w-8 justify-center px-0 text-sm">
                                    {{ $placementStats['third'] }}
                                </x-placement-badge>
                                <x-placement-badge :placement="3" suffix="Place" class="mt-1" />
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
                                            <x-placement-badge :placement="$point['placement']" />
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
