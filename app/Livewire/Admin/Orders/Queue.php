<?php

namespace App\Livewire\Admin\Orders;

use App\Models\ActivityLog;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Queue extends Component
{
    use WithPagination;

    public string $statusFilter = 'pending'; // 'pending' | 'approved' | 'rejected' | 'all'

    // Detail/action modal state
    public ?int $viewingOrderId = null;

    // Reject reason
    public bool $showRejectModal = false;
    public string $rejectReason = '';

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function viewOrder(int $orderId): void
    {
        $this->viewingOrderId = $orderId;
    }

    public function closeOrderView(): void
    {
        $this->viewingOrderId = null;
    }

    public function approve(int $orderId): void
    {
        $order = Order::with('orderItems.item')->findOrFail($orderId);

        if (! $order->isPending()) {
            $this->addError('order', 'This order has already been reviewed.');
            return;
        }

        // Re-check physical stock at the moment of approval — the request
        // may have sat in the queue while stock changed underneath it.
        foreach ($order->orderItems as $orderItem) {
            if ($orderItem->quantity > $orderItem->item->stock_quantity) {
                $this->addError(
                    'order',
                    "Cannot approve: \"{$orderItem->item->name}\" only has {$orderItem->item->stock_quantity} in stock, but {$orderItem->quantity} was requested."
                );
                return;
            }
        }

        DB::transaction(function () use ($order) {
            $deductions = [];

            foreach ($order->orderItems as $orderItem) {
                $stockBefore = $orderItem->item->stock_quantity;
                $orderItem->item->decrement('stock_quantity', $orderItem->quantity);

                $deductions[] = [
                    'item_id' => $orderItem->item_id,
                    'item_name' => $orderItem->item->name,
                    'quantity_deducted' => $orderItem->quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockBefore - $orderItem->quantity,
                ];
            }

            $order->update([
                'status' => Order::STATUS_APPROVED,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            ActivityLog::record('order.approved', $order, [
                'deductions' => $deductions,
            ]);
        });

        $this->viewingOrderId = null;
        $this->dispatch('order-reviewed', message: "Order #{$order->id} approved. Stock has been deducted.");
    }

    public function openRejectModal(int $orderId): void
    {
        $this->viewingOrderId = $orderId;
        $this->rejectReason = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->rejectReason = '';
    }

    public function reject(): void
    {
        $order = Order::findOrFail($this->viewingOrderId);

        if (! $order->isPending()) {
            $this->addError('order', 'This order has already been reviewed.');
            return;
        }

        $order->update([
            'status' => Order::STATUS_REJECTED,
            'notes' => trim(($order->notes ? $order->notes . "\n\n" : '') . ($this->rejectReason ? "Rejection reason: {$this->rejectReason}" : '')),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        ActivityLog::record('order.rejected', $order, [
            'reason' => $this->rejectReason ?: null,
        ]);

        $this->showRejectModal = false;
        $this->viewingOrderId = null;

        $this->dispatch('order-reviewed', message: "Order #{$order->id} rejected.");
    }

    public function render()
    {
        $orders = Order::query()
            ->with(['user', 'orderItems.item', 'reviewer'])
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(10);

        $viewingOrder = $this->viewingOrderId
            ? Order::with(['user', 'orderItems.item', 'reviewer'])->find($this->viewingOrderId)
            : null;

        return view('livewire.admin.orders.queue', [
            'orders' => $orders,
            'viewingOrder' => $viewingOrder,
            'pendingCount' => Order::pending()->count(),
        ])->layout('layouts.admin');
    }
}