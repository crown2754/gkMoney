<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        $banks = [
            ['code' => 'BOT',      'name' => '中央銀行',         'is_active' => true],
            ['code' => 'CTBC',     'name' => '中國信託商業銀行',   'is_active' => true],
            ['code' => 'CATHAY',   'name' => '國泰世華商業銀行',   'is_active' => true],
            ['code' => 'FUBON',    'name' => '台北富邦商業銀行',   'is_active' => true],
            ['code' => 'TWBANK',   'name' => '台灣銀行',          'is_active' => true],
            ['code' => 'LAND',     'name' => '土地銀行',          'is_active' => true],
            ['code' => 'CHB',      'name' => '合作金庫商業銀行',   'is_active' => true],
            ['code' => 'FIRST',    'name' => '第一商業銀行',      'is_active' => true],
            ['code' => 'HNCB',     'name' => '華南商業銀行',      'is_active' => true],
            ['code' => 'ESUN',     'name' => '玉山商業銀行',      'is_active' => true],
            ['code' => 'KGI',      'name' => '凱基商業銀行',      'is_active' => true],
            ['code' => 'TAISHIN',  'name' => '台新國際商業銀行',   'is_active' => true],
            ['code' => 'ENTIE',    'name' => '永豐商業銀行',      'is_active' => true],
            ['code' => 'DBS',      'name' => '星展銀行（台灣）',   'is_active' => true],
            ['code' => 'HSBC',     'name' => '滙豐（台灣）商業銀行','is_active' => true],
            ['code' => 'CITI',     'name' => '花旗（台灣）商業銀行','is_active' => true],
            ['code' => 'MEGA',     'name' => '兆豐國際商業銀行',   'is_active' => true],
            ['code' => 'SCSB',     'name' => '上海商業儲蓄銀行',   'is_active' => true],
            ['code' => 'TCB',      'name' => '台中商業銀行',      'is_active' => true],
            ['code' => 'YUANTA',   'name' => '元大商業銀行',      'is_active' => true],
        ];

        $now = now();
        foreach ($banks as &$bank) {
            $bank['created_at'] = $now;
            $bank['updated_at'] = $now;
        }

        DB::table('banks')->insert($banks);
    }
}