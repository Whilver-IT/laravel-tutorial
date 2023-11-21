<?php

namespace App\Http\Controllers\Goods;

use stdClass;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\GoodsService;



class GoodsController extends Controller
{
    /**
     * App\Services\GoodsService格納用変数
     *
     * @var [type]
     */
    protected $goodsService;

    /**
     * コンストラクタ
     * 
     * コンストラクタで色んなものを受け取れる
     *
     * @param GoodsService $goodsService
     */
    public function __construct(
        GoodsService $goodsService
    ) {
        $this->goodsService = $goodsService;
    }

    /**
     * 商品入力画面
     *
     * @param Request $request
     * @param string|null $id
     * @return \Illuminate\Contracts\View\View
     */
    public function input(Request $request, ?string $id)
    {
        if ($request->isMethod('post')) {
            if ($id) {
                // POSTでidがあるのは不正とみなす
                abort(500);
            } else {
                if ($request->session()->exists('goods.id')) {
                    $id = strlen($request->session()->get(id)) ? $request->session()->get(id) : null;
                    $goods = is_null($id) ? null : $this->goodsService->getById($id);
                } else {
                    
                }
            }
        } else {
            // 商品用セッションを一旦クリア
            $request->session()->forget('goods');

            // 商品データを取得(idがある場合)
            $goods = strlen($id) ? $this->goodsService->getById($id) : null;
            if ($goods) {
                // 商品データがあったら、セッションに格納
                $request->session()->push('goods.id', $goods->id);
                $request->session()->push('goods.updated_at', $goods->updated_at);
            }
        }

        $title = 'aaa';
        $method = $request->method();
        return view('goods.input', compact('title', 'goods'));
    }
}
