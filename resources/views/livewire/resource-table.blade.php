<div class="w-full">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        @if($canCreate)
            <flux:button
                href="{{ route($createRoute) }}"
                variant="primary"
                size="base"
                class="whitespace-nowrap"
            >
                Create {{ $this->getFormattedResourceName() }}
            </flux:button>
        @endif

        <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
            <flux:input
                wire:model.live.debounce.300ms="search"
                type="search"
                placeholder="Search..."
                icon:trailing="magnifying-glass"
                class="w-full sm:w-auto"
            />

            <flux:select
                wire:model.live="perPage"
                class="w-full sm:w-auto"
            >
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
                <option value="100">100 per page</option>
            </flux:select>
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg border border-zinc-200 shadow-sm dark:border-zinc-700">
        <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
            <tr>
                @foreach($columns as $key => $label)
                    <th wire:click="sortBy('{{ $key }}')"
                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 cursor-pointer transition-colors dark:text-zinc-400">
                        <div class="flex items-center space-x-1">
                            <span>{{ $label }}</span>
                            @if($sortField === $key)
                                <span>
                                    @if($sortDirection === 'asc')
                                        <flux:icon icon="chevron-up" variant="micro" class="h-4 w-4"/>
                                    @else
                                        <flux:icon icon="chevron-down" variant="micro" class="h-4 w-4"/>
                                    @endif
                                </span>
                            @endif
                        </div>
                    </th>
                @endforeach
                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                    Actions
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-900">
            @forelse($this->resources as $resource)
                <tr class="transition-colors">
                    @foreach($columns as $key => $label)
                        <td class="px-6 py-4 whitespace-nowrap text-zinc-700 dark:text-zinc-300">
                            @if($key === 'role' && method_exists($resource, 'getRoleBadgeAttribute'))
                                {!! $resource->getRoleBadgeAttribute() !!}
                            @else
                                <flux:text>{{ $resource->{$key} }}</flux:text>
                            @endif
                        </td>
                    @endforeach
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <div class="flex justify-end gap-2">
                            @if($hasShow)
                                <flux:button
                                    href="{{ route($this->getResourceName() . '.show', $resource) }}"
                                    variant="outline"
                                    size="xs"
                                    icon:trailing="eye"
                                >
                                    View
                                </flux:button>
                            @endif

                            @if($hasEdit && $this->hasEditPermission($resource))
                                <flux:button
                                    href="{{ route($this->getResourceName() . '.edit', $resource) }}"
                                    variant="outline"
                                    size="xs"
                                    icon:trailing="pencil-square"
                                >
                                    Edit
                                </flux:button>
                            @endif

                            @if($hasDestroy && $this->hasDeletePermission($resource))
                                <flux:button
                                    wire:click="deleteResource({{ $resource->id }})"
                                    wire:confirm="Are you sure you want to delete this {{ Str::singular(Str::afterLast($this->getResourceName(), '.')) }}?"
                                    variant="danger"
                                    size="xs"
                                    icon:trailing="trash"
                                >
                                    Delete
                                </flux:button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) + 1 }}" class="px-6 py-8 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <flux:icon icon="document-search" variant="mini"
                                       class="h-12 w-12 mb-4 text-zinc-400 dark:text-zinc-600"/>
                            <flux:text class="text-zinc-500 dark:text-zinc-400">
                                No records found.
                            </flux:text>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $resources->links() }}
    </div>
</div>
