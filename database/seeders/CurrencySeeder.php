<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code' => 'TWD', 'name' => '新台幣'],
            ['code' => 'USD', 'name' => '美元'],
            ['code' => 'EUR', 'name' => '歐元'],
            ['code' => 'JPY', 'name' => '日圓'],
            ['code' => 'CNY', 'name' => '人民幣'],
            ['code' => 'HKD', 'name' => '港幣'],
            ['code' => 'SGD', 'name' => '新加坡幣'],
        ];

        foreach ($currencies as $currency) {
            DB::table('currencies')->insert([
                'code' => $currency['code'],
                'name' => $currency['name'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}