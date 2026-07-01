<?php

namespace App\Livewire\Catalog;

use App\Models\Item;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $stockFilter = 'all'; // 'all' | 'in_stock' | 'out_of_stock'

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStockFilter(): void
    {
        $this->resetPage();
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