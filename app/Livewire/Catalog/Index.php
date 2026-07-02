<?php

namespace App\Livewire\Catalog;

use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $stockFilter = 'all'; // 'all' | 'in_stock' | 'out_of_stock'

    // Cart: [item_id => quantity], session-persisted so it survives navigation/pagination
    public array $cart = [];

    // Review modal state
    public bool $showReviewModal = false;
    public string $notes = '';

    public function mount(): void
    {
        $this->cart = session()->get('catalog_cart', []);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStockFilter(): void
    {
        $this->resetPage();
    }

    public function addToCart(int $itemId): void
    {
        $item = Item::findOrFail($itemId);

        if ($item->stock_quantity < 1) {
            return;
        }

        $current = $this->cart[$itemId] ?? 0;

        if ($current + 1 > $item->stock_quantity) {
            return;
        }

        $this->cart[$itemId] = $current + 1;
        $this->syncCartToSession();
    }

    public function incrementCartItem(int $itemId): void
    {
        $item = Item::findOrFail($itemId);
        $current = $this->cart[$itemId] ?? 0;

        if ($current + 1 > $item->stock_quantity) {
            return;
        }

        $this->cart[$itemId] = $current + 1;
        $this->syncCartToSession();
    }

    public function decrementCartItem(int $itemId): void
    {
        if (! isset($this->cart[$itemId])) {
            return;
        }

        $this->cart[$itemId]--;

        if ($this->cart[$itemId] <= 0) {
            unset($this->cart[$itemId]);
        }

        $this->syncCartToSession();
    }

    public function removeFromCart(int $itemId): void
    {
        unset($this->cart[$itemId]);
        $this->syncCartToSession();
    }

    protected function syncCartToSession(): void
    {
        session()->put('catalog_cart', $this->cart);
    }

    public function getCartCountProperty(): int
    {
        return array_sum($this->cart);
    }

    public function getCartItemsProperty()
    {
        if (empty($this->cart)) {
            return collect();
        }

        return Item::query()
            ->whereIn('id', array_keys($this->cart))
            ->get()
            ->map(function (Item $item) {
                $item->requestedQuantity = $this->cart[$item->id] ?? 0;
                return $item;
            });
    }

    public function openReviewModal(): void
    {
        if (empty($this->cart)) {
            return;
        }

        $this->showReviewModal = true;
    }

    public function closeReviewModal(): void
    {
        $this->showReviewModal = false;
    }

    public function submitRequest(): void
    {
        if (empty($this->cart)) {
            return;
        }

        // Re-validate stock at submission time — cart could be stale if
        // stock changed since items were added (e.g. another customer
        // requested the same item, or an admin adjusted stock).
        $items = Item::query()->whereIn('id', array_keys($this->cart))->get()->keyBy('id');

        foreach ($this->cart as $itemId => $quantity) {
            $item = $items->get($itemId);

            if (! $item || $quantity > $item->stock_quantity) {
                $this->addError('cart', "Not enough stock for \"{$item?->name}\". Please adjust your request.");
                return;
            }
        }

        DB::transaction(function () use ($items) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'status' => Order::STATUS_PENDING,
                'notes' => $this->notes,
            ]);

            foreach ($this->cart as $itemId => $quantity) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $itemId,
                    'quantity' => $quantity,
                ]);
            }

            ActivityLog::record('order.submitted', $order, [
                'items' => collect($this->cart)->map(function ($quantity, $itemId) use ($items) {
                    return [
                        'item_id' => $itemId,
                        'item_name' => $items->get($itemId)?->name,
                        'quantity' => $quantity,
                    ];
                })->values()->all(),
            ]);
        });

        $this->cart = [];
        $this->notes = '';
        session()->forget('catalog_cart');

        $this->showReviewModal = false;

        $this->dispatch('request-submitted', message: 'Your request has been submitted and is pending review.');
    }

    public function render()
    {
        $items = Item::query()
            ->where('is_active', true)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->stockFilter === 'in_stock', function ($query) {
                $query->where('stock_quantity', '>', 0);
            })
            ->when($this->stockFilter === 'out_of_stock', function ($query) {
                $query->where('stock_quantity', '=', 0);
            })
            ->orderBy('name')
            ->paginate(12);

        return view('livewire.catalog.index', [
            'items' => $items,
        ])->layout('layouts.app');
    }
}