<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class RegisterController extends Controller
{
    /**
     * ユーザ登録画面表示
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showRegister()
    {
        // viewヘルパの第1引数は{Laravelインストールディレクトリ}/resources/views配下のbladeを指定
        // ディレクトリの区切り文字は「.」を使用し、.blade.phpは省略
        // 第2引数はblade内で使用したい変数を配列で渡す
        // 以下の記述で、$titleをblade内の変数として使用することができる
        return view('auth.register', ['title' => '登録画面',]);

        // php 8.0以降なら名前付き引数を用いて以下のようにも書ける
        //return view(data:['title' => '登録画面',], view:'auth.register');
    }

    /**
     * ユーザ登録
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function register(Request $request)
    {
        // ※1
        // 入力データを取得
        // 本来はしなくてよいが、Laravelを触ったことのない方用に
        // $request->XXXXX
        // XXXXXの部分は、$_REQUEST['XXXXX']で取得できる値
        $input = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ];

        // 入力チェック(validation)
        // 第2引数の配列のキーは上と合わせる
        // バリデーションの細かい部分は
        // https://readouble.com/laravel/10.x/ja/validation.html
        // を参照
        $validator = Validator::make($input, [
            // nameは必須で最大255文字
            'name' => 'required|max:255',

            // emailは必須でusersテーブルのemailにおいてユニークで最大255文字でemailのRFC
            'email' => 'required|unique:users,email|max:255|email:rfc',

            // パスワードは必須でascii文字で最大32文字(33文字以上入力可能かどうかは未確認)
            'password' => 'required|ascii|max:32',
        ]);
        // ※1ここまで

        // ※1は$inputを使用せず以下の記述の方がLaravelっぽいかもしれない
        // 以下の方が、$inputを作成しないでよい(がもちろん時と場合による)
        //$validator = Validator::make($request->all(), [
        //    'name' => 'required|max:255',
        //    'email' => 'required|unique:users,email|max:255|email:rfc',
        //    'password' => 'required|ascii|max:32',
        //]);

        // validationエラーチェック
        if ($validator->fails()) {
            // エラーがあったので入力画面に戻る
            return redirect()->route('showRegister')->withErrors($validator)->withInput();
        }

        // ユーザテーブルにデータを追加
        // パスワードはハッシュを使用してハッシュ化する
        User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return view('auth.register-finish', ['title' => '登録完了']);
    }
}
