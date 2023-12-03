<?php

namespace App\Services;

use App\Services\BaseService;
use App\Repositories\GoodsRepository;

/**
 * Goods(商品)用サービスクラス
 */
class GoodsService extends BaseService
{
    /**
     * App\Repositories\GoodsRepository格納用変数
     *
     * @var App\Repositories\GoodsRepository
     */
    protected $goodsRepository;

    /**
     * コンストラクタ
     * 
     * コンストラクタで色んなものを受け取れる
     *
     * @param GoodsRepository $goodsRepository
     */
    public function __construct(
        GoodsRepository $goodsRepository
    ) {
        $this->goodsRepository = $goodsRepository;
    }

    /**
     * 編集モード取得
     *
     * @return string
     */
    public function getEditMode(): string
    {
        return session('goods.id') ? 'edit' : 'new';
    }

    /**
     * 編集モード取得(文言)
     *
     * @return string
     */
    public function getEditModeString(): string
    {
        return $this->getEditMode() == 'edit' ? '変更' : '登録';
    }

    /**
     * タイトル取得
     *
     * @return string
     */
    public function getEditTitle(): string
    {
        return '商品情報' . $this->getEditModeString();
    }

    /**
     * 商品一覧取得
     *
     * @return Illuminate\Database\Eloquent\Collection|null
     */
    public function getGoodsList()
    {
        return $this->goodsRepository->getGoodsList(request()->query());
    }

    /**
     * IDによるデータ取得
     *
     * @param string $id
     * @return stdClass|null
     */
    public function getById(string $id)
    {
        return $this->goodsRepository->getById($id);
    }

    /**
     * データ新規作成、更新処理
     *
     * @return boolean
     */
    public function save(): bool
    {
        return $this->goodsRepository->save();
    }

    /**
     * データ削除
     *
     * @param string $id
     * @return boolean
     */
    public function delete(string $id): bool
    {
        return $this->goodsRepository->delete($id);
    }
}