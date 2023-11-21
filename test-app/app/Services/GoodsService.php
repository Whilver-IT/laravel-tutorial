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
     * IDによるデータ取得
     *
     * @param string $id
     * @return stdClass|null
     */
    public function getById(string $id)
    {
        return $this->goodsRepository->getById($id);
    }
}