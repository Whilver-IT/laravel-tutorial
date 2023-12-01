<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

use App\Repositories\GoodsRepository;

class GoodsId implements ValidationRule
{
    /**
     * コンストラクタ
     *
     * @param GoodsRepository $goodsRepo
     */
    public function __construct(
        protected GoodsRepository $goodsRepo
    ) {}

    /**
     * 商品のIDに対する独自ルールを作成
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // セッションのgoods.idキーに値があれば変更、そうでなければ新規とする
        $mode = session()->has('goods.id') ? 'edit' : 'new';

        // 変更ならセッションから、そうでなければ$valueの値をidとする
        $id = $mode == 'edit' ? session('goods.id') : $value;
        $idLen = strlen($id);
        if ($idLen == 0) {
            if ($mode == 'edit') {
                $fail('変更モードですが:attributeの値がありません');
            } else {
                $fail(':attributeが入力されていません');
            }
        } elseif ($idLen > 8) {
            $fail(':attributeは半角8文字以内の数字かアルファベットで入力してください');
        } else {

            if (preg_match('/[^0-9A-z]/', $id) == 1) {
                $fail(':attributeは半角8文字以内の数字かアルファベットで入力してください');
                return;
            }

            // DBから値を取得
            $item = $this->goodsRepo->getById($id);
            if ($mode == 'edit') {
                if (is_null($item)) {
                    $fail('指定された:attibuteは削除されています');
                } else {
                    if (session()->has('goods.updated_at')) {
                        if (session('goods.updated_at') != $item->updated_at) {
                            $fail('データが変更されましたので、やり直してください');
                        }
                    } else {
                        $fail('最終更新日が設定されていません');
                    }
                }
            } else {
                if (!is_null($item)) {
                    $fail(':attributeの値はすでに使用されています');
                }
            }
        }
    }
}
