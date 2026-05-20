<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code' => 'TWD', 'name' => '新台幣', 'symbol' => 'NT$'],
            ['code' => 'USD', 'name' => '美元', 'symbol' => '$'],
            ['code' => 'EUR', 'name' => '歐元', 'symbol' => '€'],
            ['code' => 'JPY', 'name' => '日圓', 'symbol' => '¥'],
            ['code' => 'CNY', 'name' => '人民幣', 'symbol' => '¥'],
            ['code' => 'HKD', 'name' => '港幣', 'symbol' => 'HK$'],
            ['code' => 'SGD', 'name' => '新加坡幣', 'symbol' => 'S$'],
        ];

        foreach ($currencies as $currency) {
            DB::table('currencies')->updateOrInsert(
                ['code' => $currency['code']],
                [
                    'name' => $currency['name'],
                    'symbol' => $currency['symbol'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}