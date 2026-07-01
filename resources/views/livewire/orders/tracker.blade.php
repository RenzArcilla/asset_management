<div>
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">My Requests</h1>
        <p class="mt-2 text-sm text-gray-500">Track the status of your submitted requests.</p>
    </div>

    {{-- Filter tabs --}}
    <div class="inline-flex bg-gray-100/80 rounded-xl p-1 mb-6">
        @foreach (['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
            <button
                wire:click="$set('statusFilter', '{{ $value }}')"
                class="rounded-lg px-4 py-2 text-sm font-semibold transition-all duration-200
                    {{ $statusFilter === $value ? 'bg-white text-indigo-700 shadow ring-1 ring-black/5' : 'text-gray-600 hover:text-gray-900' }}"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Order list --}}
    @if ($orders->isEmpty())
        <div class="rounded-2xl border-2 border-dashed border-gray-200 bg-white/50 py-20 px-6 text-center shadow-sm">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-4 text-sm font-semibold text-gray-900">No requests yet.</h3>
            <p class="mt-1 text-sm text-gray-500">
                <a href="{{ route('catalog') }}" wire:navigate class="text-indigo-600 hover:text-indigo-800 font-medium">Browse the catalog</a>
                to submit your first request.
            </p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($orders as $order)
                <button
                    wire:click="viewOrder({{ $order->id }})"
                    wire:key="order-{{ $order->id }}"
                    class="w-full text-left bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 hover:shadow-md hover:ring-indigo-100 transition-all duration-200 p-5 flex items-center justify-between gap-4"
                >
                    <div>
                        <div class="flex items-center gap-3">
                            <p class="font-semibold text-gray-900">Order #{{ $order->id }}</p>

                            @if ($order->isPending())
                                <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700">Pending</span>
                            @elseif ($order->isApproved())
                                <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700">Approved</span>
                            @elseif ($order->isRejected())
                                <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700">Rejected</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">{{ ucfirst($order->status) }}</span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $order->orderItems->count() }} item(s) · submitted {{ $order->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <svg class="h-5 w-5 text-gray-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif

    {{-- Order Detail Modal --}}
    @if ($viewingOrder)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-trap.noscroll="true">
            <div class="fixed inset-0 bg-black/40" wire:click="closeOrderView"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
                    <div class="flex items-start justify-between mb-5">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Order #{{ $viewingOrder->id }}</h2>
                            <p class="text-sm text-gray-500">Submitted {{ $viewingOrder->created_at->format('M j, Y g:i A') }}</p>
                        </div>

                        @if ($viewingOrder->isPending())
                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700">Pending</span>
                        @elseif ($viewingOrder->isApproved())
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700">Approved</span>
                        @elseif ($viewingOrder->isRejected())
                            <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700">Rejected</span>
                        @endif
                    </div>

                    {{-- Status timeline --}}
                    <div class="flex items-center gap-2 mb-6">
                        <div class="flex items-center gap-2">
                            <div class="h-2.5 w-2.5 rounded-full bg-indigo-600"></div>
                            <span class="text-xs font-medium text-gray-700">Submitted</span>
                        </div>
                        <div class="flex-1 h-0.5 {{ $viewingOrder->isPending() ? 'bg-gray-200' : 'bg-indigo-600' }}"></div>
                        <div class="flex items-center gap-2">
                            <div class="h-2.5 w-2.5 rounded-full {{ $viewingOrder->isPending() ? 'bg-gray-200' : ($viewingOrder->isRejected() ? 'bg-red-500' : 'bg-indigo-600') }}"></div>
                            <span class="text-xs font-medium {{ $viewingOrder->isPending() ? 'text-gray-400' : 'text-gray-700' }}">
                                {{ $viewingOrder->isRejected() ? 'Rejected' : 'Reviewed' }}
                            </span>
                        </div>
                    </div>

                    {{-- Line items --}}
                    <div class="divide-y divide-gray-100 border-y border-gray-100">
                        @foreach ($viewingOrder->orderItems as $orderItem)
                            <div class="flex items-center justify-between py-3">
                                <p class="text-sm font-medium text-gray-900">{{ $orderItem->item->name }}</p>
                                <p class="text-sm text-gray-500">Qty: {{ $orderItem->quantity }}</p>
                            </div>
                        @endforeach
                    </div>

                    @if ($viewingOrder->notes)
                        <div class="mt-4">
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Notes</p>
                            <p class="text-sm text-gray-600 whitespace-pre-line">{{ $viewingOrder->notes }}</p>
                        </div>
                    @endif

                    @if ($viewingOrder->reviewer)
                        <p class="mt-4 text-xs text-gray-400">
                            Reviewed by {{ $viewingOrder->reviewer->name }} on {{ $viewingOrder->reviewed_at->format('M j, Y g:i A') }}
                        </p>
                    @endif

                    <div class="flex items-center justify-end pt-5 mt-5 border-t border-gray-100">
                        <button wire:click="closeOrderView" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 transition">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>