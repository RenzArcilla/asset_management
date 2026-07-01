<div>
    {{-- Header --}}
    <div class="mb-8 sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Catalog</h1>
            <p class="mt-2 text-sm text-gray-500">Browse available items and check current stock status.</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5 mb-8">
        {{-- Search Input --}}
        <div class="relative w-full sm:max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
            </div>
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search items..."
                class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 shadow-sm focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all duration-200"
            >
        </div>

        {{-- Segmented Filter Controls --}}
        <div class="inline-flex bg-gray-100/80 rounded-xl p-1 shadow-inner ring-1 ring-black/5 w-full sm:w-auto overflow-x-auto">
            <button
                wire:click="$set('stockFilter', 'all')"
                class="flex-1 sm:flex-none rounded-lg px-4 py-2 text-sm font-semibold transition-all duration-200 whitespace-nowrap
                    {{ $stockFilter === 'all' ? 'bg-white text-indigo-700 shadow shadow-black/5 ring-1 ring-black/5' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50/50' }}"
            >
                All
            </button>
            <button
                wire:click="$set('stockFilter', 'in_stock')"
                class="flex-1 sm:flex-none rounded-lg px-4 py-2 text-sm font-semibold transition-all duration-200 whitespace-nowrap
                    {{ $stockFilter === 'in_stock' ? 'bg-white text-indigo-700 shadow shadow-black/5 ring-1 ring-black/5' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50/50' }}"
            >
                In Stock
            </button>
            <button
                wire:click="$set('stockFilter', 'out_of_stock')"
                class="flex-1 sm:flex-none rounded-lg px-4 py-2 text-sm font-semibold transition-all duration-200 whitespace-nowrap
                    {{ $stockFilter === 'out_of_stock' ? 'bg-white text-indigo-700 shadow shadow-black/5 ring-1 ring-black/5' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50/50' }}"
            >
                Out of Stock
            </button>
        </div>
    </div>

    {{-- Grid --}}
    @if ($items->isEmpty())
        <div class="rounded-2xl border-2 border-dashed border-gray-200 bg-white/50 py-20 px-6 text-center shadow-sm">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <h3 class="mt-4 text-sm font-semibold text-gray-900">No items match your search.</h3>
            <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or search query.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($items as $item)
                <div wire:key="catalog-item-{{ $item->id }}" class="group bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 hover:shadow-md hover:ring-indigo-100 transition-all duration-200 p-6 flex flex-col relative overflow-hidden">
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <h3 class="font-bold text-gray-900 leading-tight group-hover:text-indigo-600 transition-colors">{{ $item->name }}</h3>

                        @if ($item->stock_quantity > 0)
                            <span class="shrink-0 inline-flex items-center rounded-md bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">
                                In Stock
                            </span>
                        @else
                            <span class="shrink-0 inline-flex items-center rounded-md bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                Out of Stock
                            </span>
                        @endif
                    </div>

                    @if ($item->description)
                        <p class="text-sm text-gray-600 flex-1 leading-relaxed">{{ $item->description }}</p>
                    @endif

                    @if ($item->sku)
                        <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">SKU</span>
                            <span class="text-xs font-semibold text-gray-700 bg-gray-50 px-2 py-1 rounded border border-gray-100">{{ $item->sku }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $items->links() }}
        </div>
    @endif
</div>