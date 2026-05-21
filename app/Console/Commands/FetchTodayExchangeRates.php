<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ExchangeRateService;

class FetchTodayExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange:fetch-today-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch today\'s exchange rates using ExchangeRateService';

    /**
     * Execute the console command logic.
     *
     * @return int
     */
    public function handle(ExchangeRateService $exchangeRateService): int
    {
        $this->info('Fetching today\'s exchange rates...');
        
        try {
            $exchangeRateService->fetchTodayRates();
            $this->info('Exchange rates fetched successfully.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to fetch exchange rates: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}