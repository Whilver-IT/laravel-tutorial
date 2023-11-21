<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

use App\Repositories\BaseRepository;

/**
 * Goodsテーブルの操作を行うクラス
 */
class GoodsRepository extends BaseRepository
{
    /**
     * メインで扱うテーブル
     *
     * @var string
     */
    protected $table = 'goods';

    /**
     * id条件でデータを取得
     * 
     * first()で取得した場合は、最初の1件のみでstdClassで返ってくる
     * goodsテーブルをidカラム(キー)で絞りこんでいるので、結果は1件またはなしか
     * なしの場合はnullで返る
     * https://readouble.com/laravel/10.x/ja/queries.html
     *
     * @param string $id
     * @return stdClass|null
     */
    public function getById(string $id)
    {
        // $this->builder()はDB::table($this->table)と同義
        // BaseRepositoryで定義
        return $this->builder()->where('id', $id)->first();
    }
}
