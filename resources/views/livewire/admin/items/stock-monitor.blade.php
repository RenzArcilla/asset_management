<div>
    {{-- Header --}}
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Stock Level Monitoring</h1>
            <p class="mt-1.5 text-sm text-gray-500">Track and update current inventory quantities.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a
                href="{{ route('admin.items') }}"
                wire:navigate
                class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors"
            >
                View Item Masterlist &rarr;
            </a>
        </div>
    </div>

    {{-- Toast --}}
    <div
        x-data="{ show: false, message: '' }"
        x-on:stock-adjusted.window="message = $event.detail.message; show = true; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="mb-6 flex items-center gap-3 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800 shadow-sm"
        style="display: none;"
    >
        <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span x-text="message" class="font-medium"></span>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-6">
        <div class="relative w-full max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </div>
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search by name or SKU..."
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 shadow-sm focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all duration-200"
            >
        </div>

        <label class="inline-flex items-center gap-2.5 text-sm font-medium text-gray-700 whitespace-nowrap cursor-pointer select-none">
            <input
                wire:model.live="filterLowStock"
                type="checkbox"
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-600"
            >
            Low stock only (&le; {{ $lowStockThreshold }})
        </label>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-black/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Item</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">SKU</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Current Stock</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($items as $item)
                        <tr wire:key="stock-{{ $item->id }}" class="transition-colors duration-150 hover:bg-gray-50 {{ $item->stock_quantity <= $lowStockThreshold ? 'bg-amber-50/30' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ $item->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                                {{ $item->sku ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-lg font-bold tracking-tight {{ $item->stock_quantity <= $lowStockThreshold ? 'text-amber-600' : 'text-gray-900' }}">
                                    {{ $item->stock_quantity }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($item->stock_quantity === 0)
                                    <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">
                                        Out of Stock
                                    </span>
                                @elseif ($item->stock_quantity <= $lowStockThreshold)
                                    <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-800 ring-1 ring-inset ring-amber-600/20">
                                        Low Stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                        In Stock
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <button
                                    wire:click="openAdjustModal({{ $item->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all duration-200"
                                >
                                    <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Adjust Stock
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-semibold text-gray-900">No items found</h3>
                                <p class="mt-1 text-sm text-gray-500">No items match your filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $items->links() }}
    </div>

    {{-- Adjust Stock Modal --}}
    @if ($adjustingItemId)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-trap.noscroll="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" wire:click="closeAdjustModal"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <h2 class="text-xl font-bold text-gray-900">Adjust Stock</h2>
                        <p class="mt-1.5 text-sm text-gray-500">
                            {{ $adjustingItemName }} &mdash; current: <span class="font-bold text-gray-900">{{ $currentStock }}</span>
                        </p>

                        <form wire:submit="applyAdjustment" class="space-y-5 mt-6">
                            {{-- Adjustment type --}}
                            <div class="grid grid-cols-3 gap-3">
                                <label class="flex items-center justify-center gap-2 rounded-lg border px-3 py-2.5 text-sm font-semibold cursor-pointer transition-all duration-200 
                                    {{ $adjustmentType === 'add' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 ring-1 ring-indigo-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                                    <input type="radio" wire:model.live="adjustmentType" value="add" class="hidden">
                                    Add
                                </label>
                                <label class="flex items-center justify-center gap-2 rounded-lg border px-3 py-2.5 text-sm font-semibold cursor-pointer transition-all duration-200 
                                    {{ $adjustmentType === 'remove' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 ring-1 ring-indigo-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                                    <input type="radio" wire:model.live="adjustmentType" value="remove" class="hidden">
                                    Remove
                                </label>
                                <label class="flex items-center justify-center gap-2 rounded-lg border px-3 py-2.5 text-sm font-semibold cursor-pointer transition-all duration-200 
                                    {{ $adjustmentType === 'set' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 ring-1 ring-indigo-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                                    <input type="radio" wire:model.live="adjustmentType" value="set" class="hidden">
                                    Set to
                                </label>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    {{ $adjustmentType === 'set' ? 'New quantity' : 'Amount' }}
                                </label>
                                <input
                                    wire:model="adjustmentAmount"
                                    type="number"
                                    min="0"
                                    class="block w-full rounded-lg border-gray-300 py-2 px-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('adjustmentAmount') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror"
                                >
                                @error('adjustmentAmount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Reason (optional)</label>
                                <input
                                    wire:model="reason"
                                    type="text"
                                    placeholder="e.g. Restock delivery, damaged units, manual correction"
                                    class="block w-full rounded-lg border-gray-300 py-2 px-3 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                            </div>

                            {{-- Preview --}}
                            <div class="rounded-lg bg-gray-50 ring-1 ring-inset ring-gray-500/10 px-4 py-3 text-sm text-gray-600 flex items-center justify-between">
                                <span>Preview:</span>
                                <div>
                                    @if ($adjustmentType === 'add')
                                        New stock will be: <span class="font-bold text-gray-900 ml-1">{{ $currentStock + $adjustmentAmount }}</span>
                                    @elseif ($adjustmentType === 'remove')
                                        New stock will be: <span class="font-bold text-gray-900 ml-1">{{ max(0, $currentStock - $adjustmentAmount) }}</span>
                                    @else
                                        New stock will be: <span class="font-bold text-gray-900 ml-1">{{ $adjustmentAmount }}</span>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                        <button
                            type="submit"
                            wire:click="applyAdjustment"
                            wire:loading.attr="disabled"
                            wire:target="applyAdjustment"
                            class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto disabled:opacity-70 transition-all duration-200"
                        >
                            <span wire:loading.remove wire:target="applyAdjustment">Apply Adjustment</span>
                            <span wire:loading wire:target="applyAdjustment" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Saving...
                            </span>
                        </button>
                        <button
                            type="button"
                            wire:click="closeAdjustModal"
                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto transition-all duration-200"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>