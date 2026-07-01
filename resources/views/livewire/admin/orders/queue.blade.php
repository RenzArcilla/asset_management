<div>
    {{-- Header --}}
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Request Queue</h1>
            <p class="mt-2 text-sm text-gray-500">Review and act on customer requests.</p>
        </div>
        @if ($pendingCount > 0)
            <div class="mt-4 sm:mt-0">
                <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1.5 text-sm font-semibold text-amber-700 ring-1 ring-inset ring-amber-600/20 shadow-sm">
                    <span class="mr-2 flex h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                    {{ $pendingCount }} pending
                </span>
            </div>
        @endif
    </div>

    {{-- Toast --}}
    <div
        x-data="{ show: false, message: '' }"
        x-on:order-reviewed.window="message = $event.detail.message; show = true; setTimeout(() => show = false, 4000)"
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

    @error('order')
        <div class="mb-6 flex items-start gap-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800 shadow-sm">
            <svg class="h-5 w-5 text-red-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
            </svg>
            <span class="font-medium">{{ $message }}</span>
        </div>
    @enderror

    {{-- Filter tabs --}}
    <div class="inline-flex bg-gray-100/80 rounded-xl p-1 mb-6 shadow-inner ring-1 ring-black/5 w-full sm:w-auto overflow-x-auto">
        @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'] as $value => $label)
            <button
                wire:click="$set('statusFilter', '{{ $value }}')"
                class="flex-1 sm:flex-none rounded-lg px-5 py-2 text-sm font-semibold transition-all duration-200 whitespace-nowrap
                    {{ $statusFilter === $value ? 'bg-white text-indigo-700 shadow shadow-black/5 ring-1 ring-black/5' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50/50' }}"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-black/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Order</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Items</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Submitted</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($orders as $order)
                        <tr wire:key="order-{{ $order->id }}" class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                #{{ $order->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700">
                                {{ $order->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $order->orderItems->count() }} item(s)
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($order->isPending())
                                    <span class="inline-flex items-center rounded-md bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-600/20">Pending</span>
                                @elseif ($order->isApproved())
                                    <span class="inline-flex items-center rounded-md bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">Approved</span>
                                @elseif ($order->isRejected())
                                    <span class="inline-flex items-center rounded-md bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-600/20">Rejected</span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-600 ring-1 ring-inset ring-gray-500/10">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="viewOrder({{ $order->id }})" class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                    View <span aria-hidden="true">&rarr;</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                <h3 class="mt-4 text-sm font-semibold text-gray-900">No requests found</h3>
                                <p class="mt-1 text-sm text-gray-500">There are no orders matching your current filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $orders->links() }}
    </div>

    {{-- Order Detail / Approve Modal --}}
    @if ($viewingOrder && ! $showRejectModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-trap.noscroll="true">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" wire:click="closeOrderView"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="flex items-start justify-between mb-5 border-b border-gray-100 pb-4">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Order #{{ $viewingOrder->id }}</h2>
                                <p class="text-sm text-gray-500 mt-1">
                                    Requested by <span class="font-medium text-gray-700">{{ $viewingOrder->user->name }}</span> &middot; {{ $viewingOrder->created_at->format('M j, Y g:i A') }}
                                </p>
                            </div>

                            @if ($viewingOrder->isPending())
                                <span class="inline-flex items-center rounded-md bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-600/20">Pending</span>
                            @elseif ($viewingOrder->isApproved())
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">Approved</span>
                            @elseif ($viewingOrder->isRejected())
                                <span class="inline-flex items-center rounded-md bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-600/20">Rejected</span>
                            @endif
                        </div>

                        {{-- Line items --}}
                        <div class="bg-gray-50 ring-1 ring-inset ring-gray-200 rounded-xl p-2 max-h-[50vh] overflow-y-auto">
                            @foreach ($viewingOrder->orderItems as $orderItem)
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm ring-1 ring-gray-100 mb-2 last:mb-0">
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $orderItem->item->name }}</p>
                                        <p class="text-xs font-medium text-gray-500 mt-1">
                                            Requested: <span class="text-gray-900 font-semibold">{{ $orderItem->quantity }}</span>
                                            <span class="mx-1 text-gray-300">&bull;</span>
                                            Current stock: <span class="text-gray-900 font-semibold">{{ $orderItem->item->stock_quantity }}</span>
                                        </p>
                                    </div>

                                    @if ($orderItem->quantity > $orderItem->item->stock_quantity && $viewingOrder->isPending())
                                        <div class="flex items-center gap-1.5 bg-red-50 px-2.5 py-1.5 rounded-md border border-red-100">
                                            <svg class="h-4 w-4 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <span class="text-xs font-semibold text-red-700">Insufficient stock</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @if ($viewingOrder->notes)
                            <div class="mt-5 bg-blue-50/50 rounded-xl p-4 border border-blue-100">
                                <p class="text-xs font-bold text-blue-800 uppercase tracking-wider mb-1.5">Customer Notes</p>
                                <p class="text-sm text-blue-900 whitespace-pre-line leading-relaxed">{{ $viewingOrder->notes }}</p>
                            </div>
                        @endif

                        @if ($viewingOrder->reviewer)
                            <p class="mt-5 text-sm text-gray-500 flex items-center gap-2">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Reviewed by <span class="font-medium text-gray-700">{{ $viewingOrder->reviewer->name }}</span> on {{ $viewingOrder->reviewed_at->format('M j, Y g:i A') }}
                            </p>
                        @endif
                    </div>

                    <div class="bg-gray-50 px-4 py-4 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                        @if ($viewingOrder->isPending())
                            <button
                                wire:click="approve({{ $viewingOrder->id }})"
                                wire:loading.attr="disabled"
                                wire:target="approve({{ $viewingOrder->id }})"
                                class="inline-flex w-full justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto disabled:opacity-70 transition-all duration-200"
                            >
                                <span wire:loading.remove wire:target="approve({{ $viewingOrder->id }})">Approve & Deduct Stock</span>
                                <span wire:loading wire:target="approve({{ $viewingOrder->id }})" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Processing...
                                </span>
                            </button>
                            <button
                                wire:click="openRejectModal({{ $viewingOrder->id }})"
                                class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-red-600 shadow-sm ring-1 ring-inset ring-red-300 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:mt-0 sm:w-auto transition-all duration-200"
                            >
                                Reject
                            </button>
                        @endif
                        
                        <button wire:click="closeOrderView" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:mr-auto transition-all duration-200">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Reject Reason Modal --}}
    @if ($showRejectModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-trap.noscroll="true">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" wire:click="closeRejectModal"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-bold leading-6 text-gray-900">Reject Order #{{ $viewingOrderId }}</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">Optionally let the customer know why this request is being rejected.</p>
                                </div>
                                <div class="mt-4">
                                    <textarea
                                        wire:model="rejectReason"
                                        rows="3"
                                        placeholder="e.g. Insufficient stock, invalid request..."
                                        class="block w-full rounded-xl border-gray-300 py-2.5 px-3 text-gray-900 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm placeholder:text-gray-400"
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-4 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                        <button
                            wire:click="reject"
                            wire:loading.attr="disabled"
                            wire:target="reject"
                            class="inline-flex w-full justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto disabled:opacity-70 transition-all duration-200"
                        >
                            <span wire:loading.remove wire:target="reject">Confirm Rejection</span>
                            <span wire:loading wire:target="reject" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Processing...
                            </span>
                        </button>
                        <button
                            wire:click="closeRejectModal"
                            class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto transition-all duration-200"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>