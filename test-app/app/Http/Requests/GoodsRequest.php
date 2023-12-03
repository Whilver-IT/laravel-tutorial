<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\GoodsId;

class GoodsRequest extends FormRequest
{
    /**
     * ユーザーがこのリクエストの権限を持っているかを判断する
     * 本サンプルでは認可状態等のチェックは行わないので、常にtrueを返しておく
     *
     * @return boolean
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルールを指定
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // idの判定を難しくしているので、独自に
            // {Laravelインストールディレクトリ}/app/Rules/GoodsId.php
            // で定義
            // のコンストラクタでコンストラクタの引数に
            // App\Services\GoodsService
            // があり、もちろんこのクラス中に引数として、
            // $goodsService = new GoodsService();
            // new GoodsId($goodsService)
            // のように渡してもよいですが、そのようなバケツリレーをしなくてもlaravelの
            // resolveヘルパがよしなにやってくれます
            'id' => resolve(GoodsId::class),
            'name' => 'required',
            'explanation' => 'nullable',
        ];
    }

    /**
     * バリデーションエラーのメッセージ
     * 
     * idのメッセージは、
     * {Laravelインストールディレクトリ}/app/Rules/GoodsId.php
     * 側で設定
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => '名称が入力されていません',
        ];
    }
}
