<?php

namespace App\Livewire\Admin\Items;

use App\Models\ActivityLog;
use App\Models\Item;
use Livewire\Component;
use Livewire\WithPagination;

class StockMonitor extends Component
{
    use WithPagination;

    public string $search = '';

    // low stock threshold, adjustable in the UI
    public int $lowStockThreshold = 5;

    public ?bool $filterLowStock = false;

    // Adjustment modal state
    public ?int $adjustingItemId = null;
    public string $adjustingItemName = '';
    public int $currentStock = 0;
    public string $adjustmentType = 'add'; // 'add' | 'remove' | 'set'
    public int $adjustmentAmount = 0;
    public string $reason = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterLowStock(): void
    {
        $this->resetPage();
    }

    public function openAdjustModal(int $itemId): void
    {
        $item = Item::findOrFail($itemId);

        $this->adjustingItemId = $item->id;
        $this->adjustingItemName = $item->name;
        $this->currentStock = $item->stock_quantity;
        $this->adjustmentType = 'add';
        $this->adjustmentAmount = 0;
        $this->reason = '';
        $this->resetErrorBag();
    }

    public function closeAdjustModal(): void
    {
        $this->reset(['adjustingItemId', 'adjustingItemName', 'currentStock', 'adjustmentType', 'adjustmentAmount', 'reason']);
        $this->resetErrorBag();
    }

    protected function rules(): array
    {
        return [
            'adjustmentAmount' => ['required', 'integer', 'min:0'],
            'adjustmentType' => ['required', 'in:add,remove,set'],
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function applyAdjustment(): void
    {
        $this->validate();

        $item = Item::findOrFail($this->adjustingItemId);
        $previousStock = $item->stock_quantity;

        $newStock = match ($this->adjustmentType) {
            'add' => $previousStock + $this->adjustmentAmount,
            'remove' => max(0, $previousStock - $this->adjustmentAmount),
            'set' => $this->adjustmentAmount,
        };

        if ($this->adjustmentType === 'remove' && $this->adjustmentAmount > $previousStock) {
            $this->addError('adjustmentAmount', 'Cannot remove more than the current stock (' . $previousStock . ').');
            return;
        }

        $item->update(['stock_quantity' => $newStock]);

        ActivityLog::record('item.stock_adjusted', $item, [
            'before' => ['stock_quantity' => $previousStock],
            'after' => ['stock_quantity' => $newStock],
            'adjustment_type' => $this->adjustmentType,
            'amount' => $this->adjustmentAmount,
            'reason' => $this->reason ?: null,
        ]);

        $this->dispatch(
            'stock-adjusted',
            message: "{$item->name} stock updated: {$previousStock} → {$newStock}"
        );

        $this->closeAdjustModal();
    }

    public function render()
    {
        $items = Item::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterLowStock, function ($query) {
                $query->where('stock_quantity', '<=', $this->lowStockThreshold);
            })
            ->orderBy('stock_quantity', 'asc')
            ->paginate(10);

        return view('livewire.admin.items.stock-monitor', [
            'items' => $items,
        ])->layout('layouts.admin');
    }
}