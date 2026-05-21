<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\AssetSnapshotService;

class DashboardAsset extends Component
{
    public $totalAsset = 0.00;

    protected $listeners = [
        'bankAccountSaved' => 'refreshAsset',
    ];

    public function mount(AssetSnapshotService $service)
    {
        $this->refreshAsset($service);
    }

    public function refreshAsset(AssetSnapshotService $service)
    {
        if (auth()->check()) {
            $this->totalAsset = $service->getTotalAsset(auth()->id());
        } else {
            $this->totalAsset = 0.00;
        }
    }

    public function render()
    {
        return view('livewire.dashboard-asset');
    }
}