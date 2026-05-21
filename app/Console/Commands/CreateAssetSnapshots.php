<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\AssetSnapshotService;

class CreateAssetSnapshots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asset:snapshot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create asset snapshots for all users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(AssetSnapshotService $assetSnapshotService): int
    {
        $this->info('Creating asset snapshots for all users...');

        try {
            $users = User::all();
            
            foreach ($users as $user) {
                $assetSnapshotService->createSnapshot($user->id);
            }

            $this->info('Asset snapshots created successfully.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to create asset snapshots: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}