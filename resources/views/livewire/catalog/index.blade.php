<div>
    {{-- Header --}}
    <div class="mb-8 sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Catalog</h1>
            <p class="mt-2 text-sm text-gray-500">Browse available items and check current stock status.</p>
        </div>
    </div>

    {{-- Toast --}}
    <div
        x-data="{ show: false, message: '' }"
        x-on:request-submitted.window="message = $event.detail.message; show = true; setTimeout(() => show = false, 4000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="mb-6 flex items-center gap-3 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800 shadow-sm"
        style="display: none;"
    >
        <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span x-text="message" class="font-medium"></span>
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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 {{ $this->cartCount > 0 ? 'pb-24' : '' }}">
            @foreach ($items as $item)
                @php $qtyInCart = $cart[$item->id] ?? 0; @endphp
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
                            <span class="text-xs font-semibold text-gray-700 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">{{ $item->sku }}</span>
                        </div>
                    @endif

                    {{-- Cart controls --}}
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        @if ($item->stock_quantity < 1)
                            <button disabled class="w-full rounded-xl bg-gray-50 px-4 py-2.5 text-sm font-semibold text-gray-400 border border-gray-100 cursor-not-allowed transition-all">
                                Unavailable
                            </button>
                        @elseif ($qtyInCart === 0)
                            <button
                                wire:click="addToCart({{ $item->id }})"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Add to Request
                            </button>
                        @else
                            <div class="flex items-center justify-between rounded-xl bg-indigo-50/50 ring-1 ring-inset ring-indigo-100 p-1.5">
                                <button
                                    wire:click="decrementCartItem({{ $item->id }})"
                                    class="h-8 w-8 flex items-center justify-center rounded-lg bg-white text-indigo-600 shadow-sm ring-1 ring-black/5 hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                                >
                                    &minus;
                                </button>
                                <span class="text-sm font-bold text-indigo-700">{{ $qtyInCart }} <span class="font-medium text-indigo-500">in request</span></span>
                                <button
                                    wire:click="incrementCartItem({{ $item->id }})"
                                    @disabled($qtyInCart >= $item->stock_quantity)
                                    class="h-8 w-8 flex items-center justify-center rounded-lg bg-white text-indigo-600 shadow-sm ring-1 ring-black/5 hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed"
                                >
                                    +
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $items->links() }}
        </div>
    @endif

    {{-- Floating Review Bar --}}
    @if ($this->cartCount > 0)
        <div class="fixed bottom-0 inset-x-0 z-40 border-t border-gray-200 bg-white/80 backdrop-blur-md shadow-[0_-8px_30px_rgb(0,0,0,0.08)] transition-all duration-300">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
                <p class="text-sm font-medium text-gray-700 flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">{{ $this->cartCount }}</span>
                    item{{ $this->cartCount === 1 ? '' : 's' }} in your request
                </p>
                <button
                    wire:click="openReviewModal"
                    class="rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200"
                >
                    Review Request
                </button>
            </div>
        </div>
    @endif

    {{-- Review & Submit Modal --}}
    @if ($showReviewModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-trap.noscroll="true">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" wire:click="closeReviewModal"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <h2 class="text-xl font-bold text-gray-900">Review Your Request</h2>
                        <p class="mt-1 text-sm text-gray-500">Confirm the items and quantities before submitting.</p>

                        @error('cart')
                            <div class="mt-4 flex items-center gap-2 text-sm text-red-800 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                                <svg class="h-5 w-5 text-red-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="mt-5 bg-gray-50 ring-1 ring-inset ring-gray-200 rounded-xl p-2 max-h-[40vh] overflow-y-auto">
                            @foreach ($this->cartItems as $item)
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm ring-1 ring-gray-100 mb-2 last:mb-0">
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $item->name }}</p>
                                        <p class="text-xs font-medium text-gray-500 mt-0.5">Qty: <span class="text-gray-900">{{ $item->requestedQuantity }}</span></p>
                                    </div>
                                    <button
                                        wire:click="removeFromCart({{ $item->id }})"
                                        class="text-xs font-semibold text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-2.5 py-1.5 rounded-md transition-colors"
                                    >
                                        Remove
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-5">
                            <label class="block text-sm font-semibold text-gray-900 mb-1.5">Notes (optional)</label>
                            <textarea
                                wire:model="notes"
                                rows="3"
                                placeholder="Any additional context for the reviewing admin..."
                                class="block w-full rounded-xl border-gray-300 py-2.5 px-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm placeholder:text-gray-400"
                            ></textarea>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-4 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                        <button
                            wire:click="submitRequest"
                            wire:loading.attr="disabled"
                            wire:target="submitRequest"
                            class="inline-flex w-full justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto disabled:opacity-70 transition-all duration-200"
                        >
                            <span wire:loading.remove wire:target="submitRequest">Submit Request</span>
                            <span wire:loading wire:target="submitRequest" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Submitting...
                            </span>
                        </button>
                        <button
                            wire:click="closeReviewModal"
                            class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto transition-all duration-200"
                        >
                            Keep Browsing
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>