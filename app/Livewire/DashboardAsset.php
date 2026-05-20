<?php

namespace App\Livewire;

use App\Models\BankAccount;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardAsset extends Component
{
    public $totalTwd = 0;

    public function mount()
    {
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->totalTwd = BankAccount::where('user_id', Auth::id())
            ->where('is_active', true)
            ->sum('balance_twd');
    }

    public function render()
    {
        return view('livewire.dashboard-asset');
    }
}