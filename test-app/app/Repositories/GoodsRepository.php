<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

use App\Repositories\BaseRepository;

use DateTime;
use Throwable;

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
     * 商品検索
     *
     * @param array $searchParam
     * @return Illuminate\Database\Eloquent\Collection|null
     */
    public function getGoodsList(array $searchParam)
    {
        $query = $this->builder();
        foreach ($searchParam as $key => $value) {
            if (!$value) {
                continue;
            }
            switch ($key) {
                case 'id':
                    $query->where($key, $value);
                    break;
                case 'searchword':
                    // column1 = 'xxx' and (column2 = 'yyy' or column3 = 'zzz')
                    // などの条件は以下のようにクロージャを使用して書く(カッコの中の部分)
                    // https://readouble.com/laravel/10.x/ja/queries.html
                    $query->where(function ($queryWhere) use ($value) {
                        $queryWhere->where('name', 'like', '%' . $value . '%')
                            ->orWhere('explanation', 'like', '%' . $value . '%');
                    });
                    break;
                default:
                    break;
            }
        }
        $query->orderBy('id');
        return $query->get();
    }

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

    /**
     * データ新規作成、更新処理
     *
     * @return boolean
     */
    public function save(): bool
    {
        DB::beginTransaction();
        try {
            $data = [
                'name' => request()->input('name'),
                'explanation' => request()->input('explanation'),
                'updated_at' => new DateTime(),
            ];

            $query = $this->builder();
            if (session('goods.id')) {
                $query->where('id', session('goods.id'))->update($data);
            } else {
                $data['id'] = request()->input('id');
                $data['created_at'] = new DateTime();
                $query->insert($data);
            }

            DB::commit();

            return true;
        } catch (Throwable $th) {
            DB::rollback();

            // 本来はここで例外処理を行ったりする

            return false;
        }
    }

    /**
     * 削除処理
     *
     * @param string $id
     * @return boolean
     */
    public function delete(string $id): bool
    {
        $success = false;
        DB::beginTransaction();
        try {
            $query = $this->builder();
            $query->where('id', $id)->delete();
            DB::commit();
            $success = true;
        } catch (Throwable $th) {
            DB::rollback();

            // 例外処理など
        }

        return $success;
    }
}
