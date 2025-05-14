@php use Carbon\Carbon; @endphp
<div class="w-full">
    @if($includeCreateFunctionality && $canCreate && method_exists($this, 'toggleCreateForm'))
        <div class="overflow-hidden transition-all duration-300 ease-in-out"
             style="{{ $this->showCreateForm ? 'max-height: auto;' : 'max-height: 0;' }}">
            @if($this->showCreateForm)
                <div
                    class="bg-white dark:bg-zinc-800 rounded-lg shadow-lg border border-zinc-200 dark:border-zinc-700 p-4 mb-4">
                    @if(method_exists($this, 'getCreateFormComponent') && $component = $this->getCreateFormComponent())
                        @livewire($formData['component'], $formData['componentData'] ?? [])
                    @endif
                </div>
            @endif
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        @if($includeCreateFunctionality && $canCreate)
            @if(method_exists($this, 'toggleCreateForm'))
                <div>
                    @if(!$this->showCreateForm)
                        <flux:button
                            wire:click="toggleCreateForm"
                            variant="primary"
                            size="base"
                            class="whitespace-nowrap"
                        >
                            Create {{ $this->getFormattedResourceName() }}
                        </flux:button>
                    @endif
                </div>
            @else
                <flux:button
                    href="{{ route($createRoute) }}"
                    variant="primary"
                    size="base"
                    class="whitespace-nowrap"
                >
                    Create {{ $this->getFormattedResourceName() }}
                </flux:button>
            @endif
        @endif

        <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
            @if($includeSearchFunctionality)
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    type="search"
                    placeholder="Search..."
                    icon:trailing="magnifying-glass"
                    class="w-full sm:w-auto"
                />
            @endif

            @if($includePaginationFunctionality)
                <flux:select
                    wire:model.live="perPage"
                    class="w-full sm:w-auto"
                >
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                    <option value="100">100 per page</option>
                </flux:select>
            @endif
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
                @if($includeShowFunctionality || $includeEditFunctionality || $includeDeleteFunctionality)
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                        Actions
                    </th>
                @endif
            </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-900">
            @forelse($this->resources as $resource)
                <tr class="transition-colors">
                    @foreach($columns as $key => $label)
                        <td class="px-6 py-4 whitespace-nowrap text-zinc-700 dark:text-zinc-300">
                            @php
                                $columnContent = $this->renderColumn($key, $resource);
                            @endphp

                            @if($this->columnContainsHtml)
                                {!! $columnContent !!}
                            @else
                                {{ $columnContent }}
                            @endif
                        </td>
                    @endforeach
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <div class="flex justify-end gap-2">
                            @if($includeShowFunctionality && $hasShow)
                                <flux:button
                                    href="{{ route($this->getResourceName() . '.show', $resource) }}"
                                    variant="ghost"
                                    size="xs"
                                    icon:trailing="eye"
                                >
                                    View
                                </flux:button>
                            @endif

                            @if($includeEditFunctionality && $hasEdit && $this->hasEditPermission($resource))
                                <flux:button
                                    href="{{ route($this->getResourceName() . '.edit', $resource) }}"
                                    variant="ghost"
                                    size="xs"
                                    icon:trailing="pencil-square"
                                >
                                    Edit
                                </flux:button>
                            @endif

                            @if($includeDeleteFunctionality && $hasDestroy && $this->hasDeletePermission($resource))
                                <flux:button
                                    wire:click="deleteModel({{ $resource->id }})"
                                    wire:confirm="Are you sure you want to delete this {{ $this->getFormattedResourceName() }}?"
                                    variant="ghost"
                                    size="xs"
                                    icon:trailing="trash"
                                    class="text-red-500/80 hover:text-red-600 hover:bg-red-50 dark:text-red-400/80 dark:hover:text-red-300 dark:hover:bg-red-950/30"
                                >
                                    Delete
                                </flux:button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) + ($includeShowFunctionality || $includeEditFunctionality || $includeDeleteFunctionality ? 1 : 0) }}"
                        class="px-6 py-8 text-center">
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

    @if($includePaginationFunctionality)
        <div class="mt-6">
            {{ $resources->links() }}
        </div>
    @endif

    <div class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
        {{ $this->getResourceCountSummary() }}
    </div>
</div>
