<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * ログイン画面表示
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        return Auth::check() ? redirect()->intended('menu') : view('auth.login', ['title' => 'ログイン']);
    }

    /**
     * ログイン処理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        if (!Auth::check()) {
            // バリデーションのチェック
            $validator = $request->validate([
                'email' => 'required|email:rfc',
                'password' => 'required|ascii',
            ]);

            // またはこう記述
            //$validator = Validator::validate($request->all(), [
            //    'email' => 'required|email:rfc',
            //    'password' => 'required|ascii',
            //]);
            

            // 認証
            // attemptでバリデーションエラーが起こると、その時点でエラーとなり
            // 以下のelse節を通らないため、else節中のエラーメッセージは返されないので
            // この辺りの処理をどうするのかは各プロジェクトで決めることになるかと
            if (Auth::attempt($validator)) {
                // 認証成功したので、セッションの再生成をする
                $request->session()->regerate();
            } else {
                // 認証失敗なので戻る
                return back()->withErrors(['message' => 'メールアドレスまたはパスワードが正しくありません'])->onlyInput('email');
            }
        }
        return redirect()->intended('menu');
    }

    /**
     * ログアウト
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // ログアウト処理
        Auth::logout();

        // セッションの値をすべて削除してセッションIDを再生成
        $request->session()->invalidate();

        // CSRFのトークンの再生成
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}