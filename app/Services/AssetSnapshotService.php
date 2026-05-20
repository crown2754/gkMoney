<?php
// file: app/Services/AssetSnapshotService.php

namespace App\Services;

use App\Models\AssetSnapshot;
use App\Models\BankAccount;
use Illuminate\Support\Facades\DB;

class AssetSnapshotService
{
    /**
     * Take a snapshot of total TWD balance across all active accounts for each user.
     */
    public function takeSnapshot(): void
    {
        $userTotals = BankAccount::where('is_active', true)
            ->select('user_id', DB::raw('SUM(balance_twd) as total_twd'))
            ->groupBy('user_id')
            ->get();

        foreach ($userTotals as $total) {
            AssetSnapshot::create([
                'user_id' => $total->user_id,
                'total_twd' => $total->total_twd,
                'snapshot_date' => now()->toDateString(),
            ]);
        }
    }
}