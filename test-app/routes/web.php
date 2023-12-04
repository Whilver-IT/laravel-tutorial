<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;

use App\Http\Controllers\Goods\GoodsController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Laravelのホーム画面
// ここは開発が進めば不要となるでしょう
Route::get('/', function () {
    return view('welcome');
});

// ここからユーザ作成関連のルーティング ※1

// ユーザ作成画面
// http(s)://xxx.xxx.xx/registerにGETでアクセスしたら
// App\Http\Controllers\HomeControllerクラスのshowRegisterメソッドにアクセスするという意味
// name('showRegister')の意味は、redirect()->route('XXXXX')のXXXXXを指定するとそのルーティングを呼ぶということ
// ルーティングでname('XXXXX')を付けておけば、redirect()->route('XXXXX')でそのnameのルーティングを呼び出せる
Route::get('/register', [RegisterController::class, 'showRegister'])->name('showRegister');

// ユーザ作成処理
// http(s)://xxx.xxx.xx/registerにPOSTでアクセスしたら
// App\Http\Controllers\HomeControllerクラスのregisterメソッドにアクセスするという意味
Route::post('/register', [RegisterController::class, 'register']);

// ※1 ここまで


// ここからログイン関連のルーティング ※2

// ログイン画面
// ログイン画面のnameは「login」にした方がよいかもしれません
// ログインに失敗した場合、
// {Laravelインストールディレクトリ}/app/Http/Middleware/Authenticate.php
// redirectToメソッドのreturnで
// return route('login')
// となっているためです
Route::get('/login', [AuthController::class, 'index'])->name('login');

// ログイン処理
Route::post('/login', [AuthController::class, 'login']);

// ログアウト
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// ※2 ここまで

// メニュー画面
// middleware('auth')を指定することで、
// {Laravelインストールディレクトリ}/app/Http/Middleware/Authenticate.php
// のクラス、App\Http\Middleware/Authenticate::class
// を指定しています
// middleware(App\Http\Middleware/Authenticate::class)
// と記述しても問題ありません
// では、なぜ「auth」でも設定できてしまうのかというと、
// {Laravelインストールディレクトリ}/app/Http/Kernel.php
// の
// protected $middlewareAliases
// に
// 'auth' => \App\Http\Middleware\Authenticate::class,
// というエイリアスが設定されているためです
// 個人的にはこの辺がすごくややこしい…
// viewは{Laravelインストールディレクトリ}/resources/viewからのパスをドットで指定するので、てっきりどこかのディレクトリなのかと思いきや
// Middlewareは、{Laravelインストールディレクトリ}/app/Http/Kernel.php
// にあるんかーいってなりますw
// 慣れなのかもしれませんが、覚えることも多いのですよね…(フレームワークって決して楽じゃない…w)
Route::get('/menu', [MenuController::class, 'index'])->name('menu')->middleware('auth');

// ここから商品機能関連のルーティング ※3
// http(s)://xxx.xxx.xx/goods
// 配下をまとめて記述するのにprefixを用いる
// また、groupしたルーティング配下では使用するコントローラもGoodsControllerだけなので、
// controller(GoodsController::class)を付与
// こうしておくと、Route::xxx()の第2引数は、コントローラのメソッド名を指定するだけになる
Route::prefix('goods')->name('goods.')->controller(GoodsController::class)->group(function () {
    
    Route::get('/search', 'search')->name('search');

    // 入力(変更)画面、確認画面、完了画面をまとめる
    Route::prefix('input')->group(function () {
        Route::post('/confirm', 'confirm')->name('confirm');
        Route::post('/finish', 'finish')->name('finish');

        // ここの記述は順番が大事
        // 最初に以下のルーティングを記述してしまうと、上記のconfirmやfinishは{id?}の変数とみなされるため
        // 意図しない動きとなってしまう
        // matchを使用するとmethodの指定ができ、下記はGETとPOST両方GoodsControllerのinputメソッドを呼び出す
        // 「{」、「}」で囲まれた名称(以下の場合、{id})をdetailメソッドの引数で受け取れる
        // 受け取った側(inputメソッド)で変数の検証を行ってもよいが、whereの正規表現で以下のように指定できる
        // 以下はアルファベットと数字の例
        // 変数の後ろに「?」を付けるとidは必須でなくなる
        // idがない場合は新規登録、idが有ったら変更みたいな感じにできる
        // URLパラメータ(http(s)://xxx.xxx.xx/input?id=yyy)みたいにしても可能
        // どちらのやり方を選ぶかはプロジェクト等で検討することになるかと
        // 本サンプルでは前者の方法でやってみることにする
        // ちなみに本サンプルの例はあまりよろしくないです
        // 勘のいい方なら分かると思いますが、confirmやfinishというIDの商品の場合に紛らわしくなります
        Route::match(['get', 'post'], '/{id?}', 'input')->name('input')->where('id', '^[0-9A-z]*$');
    });
});

// 上記は以下と同じ意味だが共通したmiddlewareを指定する際など記述が増えるし、まとまりを表現できない
//Route::match(['get', 'post'], '/goods/search', [GoodsController::class, 'search'])->name('goods.search');
//Route::match(['get', 'post'], '/goods/input/{id?}', [GoodsController::class, 'input'])->name('goods.input');
//Route::post('/goods/input/confirm', [GoodsController::class, 'confirm'])->name('goods.input.confirm');
//Route::post('/goods/input/finish', [GoodsController::class, 'finish'])->name('goods.input.finish');
// ※3 ここまで

// ajax用のルーティングを定義
Route::prefix('ajax')->name('ajax.')->group(function () {
    Route::prefix('goods')->name('goods.')->controller(GoodsController::class)->group(function () {
        Route::post('/delete', 'delete')->name('delete');
    });
});
