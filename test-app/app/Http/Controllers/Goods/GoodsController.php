<?php

namespace App\Http\Controllers\Goods;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Controller;
use App\Services\GoodsService;
use App\Http\Requests\GoodsRequest;

class GoodsController extends Controller
{
    /**
     * App\Services\GoodsService格納用変数
     *
     * @var App\Services\GoodsService
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
     * 商品検索
     *
     * @param Request $request
     * @return Illuminate\Contracts\View\View
     */
    public function search(Request $request)
    {
        $title = '商品検索';
        $goods = $this->goodsService->getGoodsList();
        return view('goods.search', compact('title', 'goods'));
    }

    /**
     * 商品入力画面
     *
     * @param Request $request
     * @param string $id
     * @return Illuminate\Contracts\View\View|Illuminate\Http\RedirectResponse
     */
    public function input(Request $request, string $id = '')
    {
        if ($request->isMethod('post')) {
            if ($id) {
                // POSTでidがあるのは不正とみなす
                abort(500);
            } else {
                $request->flash();
            }
        } else {
            // 商品用セッションを一旦クリア
            $request->session()->forget('goods');

            if (old() && !$request->session()->get('errors')) {
                // 入力データが残っている場合は一旦リダイレクト
                // 本来はこんな作りになっているのはよくないw
                return redirect()->route('goods.input', ['id' => $id]);
            }

            // 商品データを取得(idがある場合)
            $goods = strlen($id) ? $this->goodsService->getById($id) : null;
            if ($goods) {
                // 商品データがあったら、セッションとリクエストに格納
                $request->session()->put('goods.id', $goods->id);
                $request->session()->put('goods.updated_at', $goods->updated_at);
                $request->merge([
                    'name' => $goods->name,
                    'explanation' => $goods->explanation,
                ]);
            }
        }
        $mode = $this->goodsService->getEditMode();
        $title = $this->goodsService->getEditTitle();
        return view('goods.input', compact('title', 'mode'));
    }

    /**
     * 確認画面
     * 
     * バリデーションはここではFormRequestを使用しているため、
     * {Laravelインストールディレクトリ}/app/Http/Requests/GoodsRequest.php
     * を参照
     *
     * @param GoodsRequest $request
     * @return Illuminate\Contracts\View\View
     */
    public function confirm(GoodsRequest $request)
    {
        $title = $this->goodsService->getEditTitle();
        return view('goods.confirm', ['title' => $title, ]);
    }

    /**
     * 完了画面
     *
     * @param GoodsRequest $request
     * @return Illuminate\Contracts\View\View|Illuminate\Http\RedirectResponse
     */
    public function finish(GoodsRequest $request)
    {
        $mode = $this->goodsService->getEditModeString();
        $title = $this->goodsService->getEditTitle();
        if ($this->goodsService->save()) {
            session()->forget('goods');
            $request->session()->regenerate();
            return view('goods.finish', compact('title', 'mode'));
        } else {
            return redirect()->route('goods.input')->withErrors(['warning' => 'データの' . $mode . 'に失敗しました'])->withInput();
        }
    }

    /**
     * 削除
     * 
     * ajaxから呼ばれる
     * 成功時は削除後の商品一覧のhtmlを返してそのhtmlを反映させる
     *
     * @param Request $request
     * @return void
     */
    public function delete(Request $request)
    {
        $success = $this->goodsService->delete($request->input('id'));
        $message = $success ? '' : '削除に失敗しました';
        $goods = $this->goodsService->getGoodsList();
        $html = view('goods.search_item', compact('goods'))->render();
        return response()->json(compact('success', 'message', 'html'), Response::HTTP_OK);
    }
}
