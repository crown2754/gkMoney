<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code' => 'TWD', 'name' => '新台幣',       'is_active' => true],
            ['code' => 'USD', 'name' => '美元',         'is_active' => true],
            ['code' => 'EUR', 'name' => '歐元',         'is_active' => true],
            ['code' => 'JPY', 'name' => '日圓',         'is_active' => true],
            ['code' => 'CNY', 'name' => '人民幣',       'is_active' => true],
            ['code' => 'HKD', 'name' => '港幣',         'is_active' => true],
            ['code' => 'GBP', 'name' => '英鎊',         'is_active' => true],
        ];

        $now = now();
        foreach ($currencies as &$currency) {
            $currency['created_at'] = $now;
            $currency['updated_at'] = $now;
        }

        DB::table('currencies')->insert($currencies);
    }
}