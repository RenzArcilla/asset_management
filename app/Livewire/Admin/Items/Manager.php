<?php

namespace App\Livewire\Admin\Items;

use App\Models\Item;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Manager extends Component
{
    use WithPagination;

    // Search / filter
    public string $search = '';

    // Modal state
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingItemId = null;

    // Form fields
    public string $name = '';
    public string $description = '';
    public string $sku = '';
    public int $stock_quantity = 0;
    public bool $is_active = true;

    // Delete confirmation
    public ?int $confirmingDeleteId = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'sku' => [
                'nullable',
                'string',
                'max:100',
                'unique:items,sku,' . $this->editingItemId,
            ],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal(int $itemId): void
    {
        $item = Item::findOrFail($itemId);

        $this->editingItemId = $item->id;
        $this->name = $item->name;
        $this->description = $item->description ?? '';
        $this->sku = $item->sku ?? '';
        $this->stock_quantity = $item->stock_quantity;
        $this->is_active = $item->is_active;

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->isEditing) {
            $item = Item::findOrFail($this->editingItemId);
            $item->update($validated);

            $this->dispatch('item-saved', message: 'Item updated successfully.');
        } else {
            Item::create($validated);

            $this->dispatch('item-saved', message: 'Item created successfully.');
        }

        $this->closeModal();
    }

    public function confirmDelete(int $itemId): void
    {
        $this->confirmingDeleteId = $itemId;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(): void
    {
        if ($this->confirmingDeleteId) {
            Item::findOrFail($this->confirmingDeleteId)->delete();
            $this->confirmingDeleteId = null;

            $this->dispatch('item-saved', message: 'Item deleted.');
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset(['name', 'description', 'sku', 'stock_quantity', 'is_active', 'editingItemId']);
        $this->is_active = true;
        $this->resetErrorBag();
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
            ->latest()
            ->paginate(10);

        return view('livewire.admin.items.manager', [
            'items' => $items,
        ])->layout('layouts.admin');
    }
}