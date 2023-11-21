<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // 追加
use DateTime; // 追加

class GoodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 実際にはarrayの中身は工夫して作成してください
        // 固定のデータを入れるだけなら下記のような方法でも構いませんが…
        DB::table('goods')->insert([
            'id' => 'G0000001',
            'name' => 'テスト商品01',
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),
        ]);
    }
}
