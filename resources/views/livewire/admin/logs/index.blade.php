<div>
    {{-- Header --}}
    <div class="mb-8 sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Activity Log</h1>
            <p class="mt-2 text-sm text-gray-500">Audit trail of key actions across the system.</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-black/5 p-5 mb-8 flex flex-wrap items-end gap-5">
        <div class="flex-1 min-w-[160px] max-w-xs">
            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">User</label>
            <select wire:model.live="userFilter" class="block w-full rounded-xl border-gray-300 py-2.5 pl-3 pr-10 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200">
                <option value="">All users</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex-1 min-w-[180px] max-w-xs">
            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Action</label>
            <select wire:model.live="actionFilter" class="block w-full rounded-xl border-gray-300 py-2.5 pl-3 pr-10 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200">
                <option value="">All actions</option>
                @foreach ($this->availableActions as $action)
                    <option value="{{ $action }}">{{ ucwords(str_replace(['.', '_'], ' ', $action)) }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex-1 min-w-[150px] max-w-xs">
            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">From</label>
            <input wire:model.live="dateFrom" type="date" class="block w-full rounded-xl border-gray-300 py-2.5 px-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200">
        </div>

        <div class="flex-1 min-w-[150px] max-w-xs">
            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">To</label>
            <input wire:model.live="dateTo" type="date" class="block w-full rounded-xl border-gray-300 py-2.5 px-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200">
        </div>

        @if ($userFilter || $actionFilter || $dateFrom || $dateTo)
            <div class="pb-1.5">
                <button wire:click="resetFilters" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Clear filters
                </button>
            </div>
        @endif
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-black/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Timestamp</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Subject</th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($logs as $log)
                        <tr wire:key="log-{{ $log->id }}" class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $log->created_at->format('M j, Y g:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $log->user->name ?? 'System' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-md bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-700 ring-1 ring-inset ring-gray-500/20">
                                    {{ ucwords(str_replace(['.', '_'], ' ', $log->action)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <span class="font-medium text-gray-900">{{ class_basename($log->subject_type) }}</span> <span class="text-gray-400">#{{ $log->subject_id }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="viewLog({{ $log->id }})" class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                    View <span aria-hidden="true">&rarr;</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zm3.75 11.625a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                                <h3 class="mt-4 text-sm font-semibold text-gray-900">No activity found</h3>
                                <p class="mt-1 text-sm text-gray-500">No log entries match your current filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $logs->links() }}
    </div>

    {{-- Detail Modal --}}
    @if ($viewingLog)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-trap.noscroll="true">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" wire:click="closeLogView"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="flex items-start justify-between mb-5 border-b border-gray-100 pb-4">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">
                                    {{ ucwords(str_replace(['.', '_'], ' ', $viewingLog->action)) }}
                                </h2>
                                <p class="mt-1.5 text-sm text-gray-500 flex items-center gap-2">
                                    <span class="font-medium text-gray-700">{{ $viewingLog->user->name ?? 'System' }}</span> 
                                    <span class="text-gray-300">&bull;</span>
                                    {{ $viewingLog->created_at->format('M j, Y g:i A') }}
                                </p>
                            </div>
                        </div>

                        <div class="rounded-xl bg-gray-50 ring-1 ring-inset ring-gray-200 px-4 py-3 text-sm text-gray-600 mb-6 flex items-center justify-between">
                            <span class="font-semibold text-gray-900">Target Subject</span>
                            <span class="font-mono text-xs bg-white border border-gray-200 rounded-md px-2 py-1 shadow-sm text-gray-700">
                                {{ class_basename($viewingLog->subject_type) }} #{{ $viewingLog->subject_id }}
                            </span>
                        </div>

                        @if (! empty($viewingLog->properties))
                            <div class="space-y-4 max-h-[50vh] overflow-y-auto pr-2">
                                @foreach ($viewingLog->properties as $key => $value)
                                    <div>
                                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                            {{ ucwords(str_replace('_', ' ', $key)) }}
                                        </h4>

                                        @if (is_array($value))
                                            <div class="bg-[#1e1e2e] rounded-xl shadow-inner overflow-hidden">
                                                <pre class="text-xs text-gray-300 p-4 overflow-x-auto font-mono leading-relaxed">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                            </div>
                                        @else
                                            <div class="bg-gray-50 rounded-xl px-4 py-3 ring-1 ring-inset ring-gray-100">
                                                <p class="text-sm text-gray-800">{{ $value ?? '—' }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="py-4 text-center">
                                <p class="text-sm text-gray-500">No additional details recorded for this action.</p>
                            </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 px-4 py-4 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                        <button wire:click="closeLogView" class="inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto transition-all duration-200">
                            Close Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>