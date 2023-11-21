<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

/**
 * リポジトリクラスの基となるクラス
 */
abstract class BaseRepository
{
    /**
     * テーブル名
     * 継承先で必ずセットすること
     * (のであれば、本来ここはinterfaceの方がいいかもしれないが本サンプルでは割愛)
     *
     * @var string
     */
    protected $table = '';

    /**
     * 元になるテーブルクエリを取得
     *
     * @return void
     */
    protected function builder()
    {
        return DB::table($this->table);
    }
}
