<?php

namespace App\Services;

use App\Models\AssetSnapshot;
use App\Models\BankAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AssetSnapshotService
{
    /**
     * 計算使用者所有活躍帳戶 `balance_twd` 總和，寫入 `asset_snapshots`
     */
    public function createSnapshot(int $userId): AssetSnapshot
    {
        $totalTWD = BankAccount::where('user_id', $userId)
            ->where('is_active', true)
            ->sum('balance_twd');

        return DB::transaction(function () use ($userId, $totalTWD) {
            return AssetSnapshot::create([
                'user_id' => $userId,
                'total_twd' => $totalTWD,
                'snapshot_date' => Carbon::today(),
            ]);
        });
    }

    /**
     * 取得使用者今日總資產
     */
    public function getTotalAsset(int $userId): float
    {
        return BankAccount::where('user_id', $userId)
            ->where('is_active', true)
            ->sum('balance_twd');
    }
}