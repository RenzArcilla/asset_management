<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Tracker extends Component
{
    use WithPagination;

    public string $statusFilter = 'all'; // 'all' | 'pending' | 'approved' | 'rejected'

    public ?int $viewingOrderId = null;

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

    public function render()
    {
        $orders = Order::query()
            ->forUser(Auth::id())
            ->with(['orderItems.item'])
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(10);

        $viewingOrder = $this->viewingOrderId
            ? Order::query()
                ->forUser(Auth::id())
                ->with(['orderItems.item', 'reviewer'])
                ->find($this->viewingOrderId)
            : null;

        return view('livewire.orders.tracker', [
            'orders' => $orders,
            'viewingOrder' => $viewingOrder,
        ])->layout('layouts.app');
    }
}