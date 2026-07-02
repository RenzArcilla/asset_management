<?php

namespace App\Livewire\Admin\Logs;

use App\Models\ActivityLog;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $userFilter = '';
    public string $actionFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';

    public ?int $viewingLogId = null;

    protected function queryString(): array
    {
        return [
            'userFilter' => ['except' => ''],
            'actionFilter' => ['except' => ''],
            'dateFrom' => ['except' => ''],
            'dateTo' => ['except' => ''],
        ];
    }

    public function updatingUserFilter(): void { $this->resetPage(); }
    public function updatingActionFilter(): void { $this->resetPage(); }
    public function updatingDateFrom(): void { $this->resetPage(); }
    public function updatingDateTo(): void { $this->resetPage(); }

    public function resetFilters(): void
    {
        $this->reset(['userFilter', 'actionFilter', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function viewLog(int $logId): void
    {
        $this->viewingLogId = $logId;
    }

    public function closeLogView(): void
    {
        $this->viewingLogId = null;
    }

    /**
     * Distinct action values currently present in the log, used to
     * populate the action filter dropdown without hardcoding a list
     * that can drift out of sync with what's actually logged.
     */
    public function getAvailableActionsProperty()
    {
        return ActivityLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');
    }

    public function render()
    {
        $logs = ActivityLog::query()
            ->with('user')
            ->when($this->userFilter, function ($query) {
                $query->where('user_id', $this->userFilter);
            })
            ->when($this->actionFilter, function ($query) {
                $query->where('action', $this->actionFilter);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->latest('created_at')
            ->paginate(20);

        $viewingLog = $this->viewingLogId
            ? ActivityLog::with('user')->find($this->viewingLogId)
            : null;

        return view('livewire.admin.logs.index', [
            'logs' => $logs,
            'viewingLog' => $viewingLog,
            'users' => User::orderBy('name')->get(['id', 'name']),
        ])->layout('layouts.admin');
    }
}