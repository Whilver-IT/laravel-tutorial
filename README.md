# Laravelをインストールして簡単なアプリケーションを作成するところまで

## 1. 目的

師匠からLaravel教えてw  
って言われたので、マニュアル的なものを作ってみる  

本ドキュメントでは簡単な入力と表示の機能を作成する

## 2. インストール

### 2-1. はじめに

今回は、[eurolinux](https://en.euro-linux.com/)にインストールしていきます  
環境はapacheとPHP、composerが使えればOSは何でもイケると思います  
以下、マニュアル的なものを作りますが、とてもすべてを書ききれるものでもないので、迷ったら  
[インストール 10.x Laravel](https://readouble.com/laravel/10.x/ja/installation.html)  
を参考にするとよいです

### 2-2. 環境

<table>
    <tr>
        <td><strong>OS</strong></td>
        <td>eurolinux 9.2</td>
    </tr>
    <tr>
        <td><strong>ウェブサーバアプリケーション</td>
        <td>apache</td>
    </tr>
    <tr>
        <td><strong>PHPのバージョン</strong></td>
        <td>8.2</td>
    </tr>
    <tr>
        <td><strong>Laravelのバージョン</strong></td>
        <td>10.x
    </tr>
    <tr>
        <td><strong>Database</strong></td>
        <td>PostgreSQL 16</td>
    </tr>
</table>

新しもの好きなので、最新バージョンを入れます(2023-10-31現在)  
PHPはRemiから、PostgreSQL 16はpgdbから入れます  
SELINUXとfirewalldはここでは切っておきます(本番環境等では適切に設定してください)

```console
# cp -p /etc/selinux/config /etc/selinux/config.default
# vi /etc/selinux/config
SELINUX=enforcing
↓
SELINUX=disabled
# reboot
# systemctl disable firewalld
# systemctl stop firewalld
```

### 2-3. PostgreSQL 16のインストール

```console
# dnf install https://download.postgresql.org/pub/repos/yum/16/redhat/rhel-9-x86_64/postgresql16-libs-16.0-1PGDG.rhel9.x86_64.rpm https://download.postgresql.org/pub/repos/yum/16/redhat/rhel-9-x86_64/postgresql16-16.0-1PGDG.rhel9.x86_64.rpm https://download.postgresql.org/pub/repos/yum/16/redhat/rhel-9-x86_64/postgresql16-server-16.0-1PGDG.rhel9.x86_64.rpm
# su - postgres
$ /usr/pgsql-16/bin/initdb --no-locale -E UTF8
$ exit
# systemctl enable postgresql-16
# systemctl start postgresql-16
# createuser -Ps -U postgres {PostgreSQL操作ユーザ}
# createdb -U {PostgreSQL操作ユーザ} {データベース名}
```

### 2-4. apacheのインストール

```console
# dnf install httpd
```

### 2-5. RemiからPHPのインストール

```console
# dnf install https://rpms.remirepo.net/enterprise/remi-release-9.rpm
# dnf install php82-php php82-php-pdo php82-php-pgsql php82-php-mbstring php82-zip unzip
# alternatives --install /usr/bin/php php /usr/bin/php82 1
```

alternativesでphpコマンドでphp82を実行できるようにしておきます  
この後のcomposerの設定、実行で必要になります

### 2-6. Composerのインストール

```console
# php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
# php composer-setup.php
# mv composer.phar /usr/local/bin/composer
```

### 2-7. Laravelのインストール

```console
$ composer create-project --prefer-dist laravel/laravel {app名}
```

今回はapp名はtest-appとした

## 3. apacheの設定

conf設定の例

```console
DocumentRoot {Laravelインストールディレクトリ}/public
<Directory {Laravelインストールディレクトリ}/public>
    Options +FollowSymLinks -Indexes
    AllowOverride All
    Require all granted
</Directory>
```

設定が終わったら、apacheの起動

```console
# systemctl enable httpd
# systemctl start httpd
```

ここまでできたらブラウザでhttp://xxx.xxx.xxx.xxx  
にアクセスしてLaravelが表示できることを確認

{Laravelインストールディレクトリ}/storage  
ディレクトリは、パーミッションを0777にした(権限がなくてlogファイルが書き込めなかったため)

## 4. Laravelの設定

### 4-1. はじめに

ここまではできても、正直何から始めればよいかすごく迷うと思います  
すべては書ききれない旨は最初に記述しましたが、以下の順番にやれば一通り作れるようにはなるのかなと  
分からないとかこういう場合どうしたらいいのとかに出くわしたらまず本家のマニュアル、  
[10.x Laravel](https://readouble.com/laravel/10.x/ja)  
をご覧ください

### 4-2. 本サンプルソースのライセンスなど

LICENSEファイルをご覧ください(MITライセンスです)
このソースを使用して生じたいかなる不具合にも責任を負いません

### 4-3. 本ドキュメントで説明する内容

1. 設定ファイル(.env)への設定
1. migration(マイグレーション)
1. ルーティング
1. 簡単なページ作成

### 4-4. 設定ファイル(.env)への設定

細かいレベルの話は、  
[設定 10.x Laravel](https://readouble.com/laravel/10.x/ja/configuration.html)  
をご覧ください

Laravelではアプリケーションの定数を、PHPのdefineの様に  
{Laravelインストールディレクトリ}/.env  
に書きます  
defineと異なるのは、このファイルはenvvironment(環境)ごとに設定するイメージです  
例えば、次項で説明するようなデータベースの設定値が開発環境、検証環境、本番環境で異なる値を使用するなど、環境によって異なる値を設定するようなケースにこのファイルを用います

初めての場合、ではどういう機能のどの値を設定すればよいか  
というところで悩むと思いますが、  
{Laravelインストールディレクトリ}/config  
中のファイルの中で、

```php
env('XXXXXX')
```

となっている「XXXXX」の値をセットすると思っていただいて最初は構いません

### 4-5. migration(マイグレーション)

migration  
とは、直訳すると<strong>移行</strong>という意味ですが、データベースとの連携ということになるでしょうか  
具体的にはデータベースにおける、CREATE TABLE(テーブルの作成)をやってくれるものという概念で初めは思っていただいて構いません

正直、CREATE TABLEを自分で記述してデータベースに流してとかする方が早いし、本作業は手間に感じるかもしれませんが、Laravelのお作法ということで

#### 4-5-1. .envの設定

先ずは、データベースの設定を.envの設定を行いましょう  
`{Laravelインストールディレクトリ}/.env`を見ると、`{Laravelインストールディレクトリ}/.env.sample`とほぼ同じ内容であることが分かるかと思います  
APP_KEYの値だけ.envの方は入っているかと思いますが、それ以外はまったく同じですので、  
APP_で始まる値とLOG_で始まる行以外は削除します(行頭に「#」を付与するとコメント行になりますのでそれでもよいです)  
(結論からいえばDB_で始まる部分は残して、値を書き換えてもよいです)  
設定がなくなっても、バックアップは`{Laravelインストールディレクトリ}/.env.sample`にあるので  

そうしたら、本ドキュメントではPostgreSQLに接続するので、以下のようにDB_の値を設定してください
```console
DB_CONNECTION=pgsql
DB_HOST={IPアドレスまたはホスト名}
DB_PORT={ポート番号}
DB_DATABASE={データベース名}
DB_USERNAME={データベースユーザ名}
DB_PASSWORD={データベースパスワード}
```

これは、`{Laravelインストールディレクトリ}/config/database.php`の以下の値を設定していることになります  
env()の第2引数は、第1引数の設定値がない場合のデフォルト値です  
他のデータベースを使用の場合は、そのデータベースの設定を.envで設定してください  
以下抜粋
```php
    'default' => env('DB_CONNECTION', 'mysql'),  // .envでDB_CONECTION=pgsqlで以下、connections内のpgsql設定使用を明言
    'connections' => [
        // 〜 省略 〜

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),      // .envのDB_HOST={設定値}で指定した値
            'port' => env('DB_PORT', '5432'),           // .envのDB_PORT={設定値}で設定した値
            'database' => env('DB_DATABASE', 'forge'),  // .envのDB_DATABASE={設定値}で設定した値
            'username' => env('DB_USERNAME', 'forge'),  // .envのDB_USERNAME={設定値}で設定した値
            'password' => env('DB_PASSWORD', ''),       // .envのDB_PASSWORD={設定値}で設定した値
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

        // 〜 省略 〜
    ],
```

余談ですが、`{Laravelインストールディレクトリ}/config/database.php`のそれ以外の値を変更したい場合は、直接database.phpの値を変えてしまうか、.envで設定できるようにするかその辺りはプロジェクトの方針で決めることになると思います  
(基本それ以外の値は変えることは少なかろうということで、.envの設定値とはなっていないとは思います)

#### 4-5-2. migrateの実行

マイグレーションの細かい部分は、本家マニュアル  
[マイグレーション 10.x Laravel](https://readouble.com/laravel/10.x/ja/migrations.html)  
をご覧ください

上記設定ができたら、migrateの実行をします  
本来はartisanの説明が必要かと思いますが、Laravelにおけるシェルコマンドだとここでは思っておいてください  
Laravelのインストールディレクトリで以下のコマンドを実行してください

```console
$ cd {Laravelインストールディレクトリ}
$ php artisan migrate

   INFO  Preparing database.  

  Creating migration table ......................................... 31ms DONE

   INFO  Running migrations.  

  2014_10_12_000000_create_users_table ............................. 46ms DONE
  2014_10_12_100000_create_password_reset_tokens_table ............. 31ms DONE
  2019_08_19_000000_create_failed_jobs_table ....................... 50ms DONE
  2019_12_14_000001_create_personal_access_tokens_table ............ 58ms DONE
```

というメッセージが出ればOK.です  
意味としては、

1. Creatting migration table → 「migration(s)」テーブルを作成
1. 2014_10_12_000000_create_users_table → 「users」テーブルを作成
1. 2014_10_12_100000_create_password_reset_tokens_table → 「password_reset_tokens」テーブルを作成
1. 2019_08_19_000000_create_failed_jobs_table → 「failed_jobs」テーブルを作成
1. 2019_12_14_000001_create_personal_access_tokens_table → 「personal_access_tokens」テーブル作成

となります  
ここで、2014_10_12_000000_create_user_tableとは一体どこのことなのかというと、  
{Laravelインストールディレクトリ}/database/migrations  
配下にあるファイルです  
これ自体は、上記ディレクトリ中にあるファイルを`php artisan migrate`を実行した時点でファイル名の昇順で実行します

それでは、データベースがどうなったのかを一応見ておきましょう

```
$ psql -U {データベースユーザ名} -h {データベースIPアドレスまたはホスト名} [-p {ポート番号(デフォルトは省略可)}] {データベース名}
ユーザー {データベースユーザ名} のパスワード： {データベースパスワード}
{データベース名}# SELECT * FROM pg_tables WHERE tableowner = '{データベースユーザ名}';
 schemaname |       tablename        | tableowner | tablespace | hasindexes | ha
srules | hastriggers | rowsecurity 
------------+------------------------+------------+------------+------------+---
-------+-------------+-------------
 public     | migrations             | laravel    |            | t          | f 
       | f           | f
 public     | users                  | laravel    |            | t          | f 
       | f           | f
 public     | password_reset_tokens  | laravel    |            | t          | f 
       | f           | f
 public     | failed_jobs            | laravel    |            | t          | f 
       | f           | f
 public     | personal_access_tokens | laravel    |            | t          | f 
       | f           | f
(5 行)

{データベース名}# SELECT * FROM migrations;
 id |                       migration                       | batch 
----+-------------------------------------------------------+-------
  1 | 2014_10_12_000000_create_users_table                  |     1
  2 | 2014_10_12_100000_create_password_reset_tokens_table  |     1
  3 | 2019_08_19_000000_create_failed_jobs_table            |     1
  4 | 2019_12_14_000001_create_personal_access_tokens_table |     1
(4 行)

{データベース名}# exit
```

となり、migrationテーブルと上記ディレクトリにあった、YYYY_MM_DD_XXXXXX_create_{テーブル名}.phpのテーブルが作成されていることを確認  
また、migrationsテーブルに、migrationの内容が登録されていることを確認  
(本ドキュメントでは、migrationsテーブルの細かい中身の解説は割愛)

#### 4-5-3. migrateを行うファイルの作成など

本ドキュメントの冒頭で述べましたが、作成する機能用のデータベースのテーブルを作成  
以下のようなテーブル構成にする  

```sql
CREATE TABLE goods (
    id VARCHAR(8) NOT NULL,
    name TEXT NOT NULL,
    created_at TIMESTAMP(6) WITH TIME ZONE NOT NULL,
    updated_at TIMESTAMP(6) WITH TIME ZONE NOT NULL,
    PRIMARY KEY (id)
)
```

Laravelには前節で述べたファイルを作成する機能があるので以下のコマンドを実行  
今回は「goods」テーブルなので、create_「goods」_tableとする

```console
$ cd {Laravelインストールディレクトリ}
$ php artisan make:migration create_goods_table

 INFO  Migration [database/migrations/2023_11_06_130946_create_goods_table.php] created successfully.  

$
```

{Laravelインストールディレクトリ}/database/migrations  
配下に作成されたphpファイルを編集する

#### **`{Laravelインストールディレクトリ}/database/migrations/2023_11_06_130946_create_goods_table.php`**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods');
    }
};
```

これを以下のように変更して保存する

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->string('id', 8);
            $table->string('name');
            $table->timestampTz('created_at', 6);
            $table->timestampTz('updated_at', 6);
            // 以下の記述でもよい
            // timestampsTz(6)でcreated_at、updated_atをタイムゾーン付きで作成
            //$table->timestampsTz(6);

            // プライマリキー
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods');
    }
};
```

変更が終了したら、再度migrationを実行します

```console
$ cd {Laravelインストールディレクトリ}
$ php artisan migrate

   INFO  Running migrations.  

  2023_11_06_130946_create_goods_table ............................. 72ms DONE

$
```

となり、テーブルが追加されました  
念のためテーブルが追加されたことと、カラムを見ておきます

```
$ psql -U {データベースユーザ名} -h {データベースIPアドレスまたはホスト名} -p {ポート番号} {データベース名}
{データベース名}=# SELECT * FROM pg_tables WHERE tableowner = '{データベースユーザ名}';
 schemaname |       tablename        | tableowner | tablespace | hasindexes | ha
srules | hastriggers | rowsecurity 
------------+------------------------+------------+------------+------------+---
-------+-------------+-------------
 public     | migrations             | laravel    |            | t          | f 
       | f           | f
 public     | users                  | laravel    |            | t          | f 
       | f           | f
 public     | password_reset_tokens  | laravel    |            | t          | f 
       | f           | f
 public     | failed_jobs            | laravel    |            | t          | f 
       | f           | f
 public     | personal_access_tokens | laravel    |            | t          | f 
       | f           | f
 public     | goods                  | laravel    |            | f          | f 
       | f           | f
(6 行)
```

PostgreSQLのカラム情報の取得はSQLできちんと出そうとすると大変なので、pg_dumpコマンドで確認してみます

```console
$ pg_dump -U {データベースユーザ名} -h {データベースIPアドレスまたはホスト名} -p {ポート番号} -t {テーブル名} --schema-only {データベース名}

〜 省略 〜

CREATE TABLE public.goods (
    id character varying(8) NOT NULL,
    name text NOT NULL,
    created_at timestamp(6) with time zone NOT NULL,
    updated_at timestamp(6) with time zone NOT NULL
);

〜 省略 〜

ALTER TABLE ONLY public.goods
    ADD CONSTRAINT goods_pkey PRIMARY KEY (id);

〜 省略 〜

```

### 4-6. seed(シード)

元々は種をまくという意味などですが、Laravelなどのフレームワークでは(テスト)データをデータベースのテーブルに入れる目的のものです  
早速さきほど、goodsテーブルを作成したので、データを入れてみましょう

マニュアルでは、  
[データベース：シーディング 10.x Laravel](https://readouble.com/laravel/10.x/ja/seeding.html)  
に詳しくあります

正直シード(シーダ(seeder))を使用して、データを登録する必要もないかもしれませんが、大量のテストデータを作る場合などに使用すると便利なこともあります

#### 4-6-1. seed用ファイルの作成

goodsテーブルなので、「Goods」Seederとします(先頭大文字)  
これが、作成されるファイルのクラス名になります

```console
$ cd {Laravelインストールディレクトリ}
$ php artisan make:seeder GoodsSeeder

   INFO  Seeder [database/seeders/GoodsSeeder.php] created successfully.  

```

#### **`{Laravelインストールディレクトリ}/database/seeders/GoodsSeeder.php`**
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GoodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 
    }
}

```

を以下のようにする

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // 追加
use DateTime; // 追加

class GoodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 実際にはarrayの中身は工夫して作成してください
        // 固定のデータを入れるだけなら下記のような方法でも構いませんが…
        DB::table('goods')->insert([
            'id' => 'G0000001',
            'name' => 'テスト商品01',
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),
        ]);
    }
}
```

#### 4-6-2. seedの実行

4-6-1.で作成したファイルの内容を変更して保存したらseedの実行

```console
$ cd {Laravelインストールディレクトリ}
$ php artisan db:seed --class=GoodsSeeder

   INFO  Seeding database.  

```

今回は、  
--class=GoodsSeeder  
でseedを実行したいクラスを指定しました(といっても1つしかありませんが)  
では、データが入ったかどうか確認してみましょう

```console
$ psql -U {データベースユーザ名} -h {データベースIPアドレスまたはホスト名} -p {ポート番号} {データベース名}
{データベース名}=# SELECT * FROM goods;
    id    |     name     |       created_at       |       updated_at       
----------+--------------+------------------------+------------------------
 G0000001 | テスト商品01 | 2023-11-07 12:05:51+09 | 2023-11-07 12:05:51+09
```

今回は、特にタイムゾーンを設定していなかったので、UTCとして登録されたため上記のような時間になってしまったが、日本時間にしたいのであれば、  
{Laravelインストールディレクトリ}/config/app.php  
のtimezoneをUTCからAsia/Tokyoにしておいてください

### 4-7. テーブル定義の変更など

再度、migrateの話に戻ってしまうが、実際にアプリケーションの稼働が始まったのちに仕様追加等でテーブル定義を変えないといけなくなった場合について以下に記述する

#### 4-7-1. 定義変更用のファイルの作成

goodsテーブルに説明(explanation)のcolumn(nullもOK.)を追加する  
追加する場所は、nameとcreated_atの間にしたい  
結論から言えばPostgreSQLはあるカラムの後ろになどは追加できない…(MySQL(MariaDB)は可能)

```console
$ cd {Laravelインストールディレクトリ}
$ php artisan make:migration goods_add_column_explanation --table=goods

   INFO  Migration [database/migrations/2023_11_07_223749_goods_add_column_explanation.php] created successfully.   
```

#### 4-7-2. ファイル変更

#### **`{Laravelインストールディレクトリ}/database/migrations/2023_11_07_223749_goods_add_column_explanation.php`**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('goods', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods', function (Blueprint $table) {
            //
        });
    }
};
```

これを以下のように変更

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('goods', function (Blueprint $table) {
            $table->text('explanation')->nullable();
            // 以下のように書いても残念ながらPostgreSQLでは不可能(カラム自体は末尾に追加される)
            //$table->after('name', function (BluePrint $table) {
            //    $table->text('explanation')->nullable();
            //});
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods', function (Blueprint $table) {
            // rollbackした際に元の状態に戻るように書く
            $table->dropColumn('explanation');
        });
    }
};
```

変更が済んだらmigrate

```console
$ cd {Laravelインストールディレクトリ}
$ php artisan migrate

  2023_11_07_223749_goods_add_column_explanation ................... 10ms DONE
```

テーブルの構成、データの状態はここでは割愛  
カラムが追加され、データも元のまま消えたりはしていないことを確認

## 5. ルーティング

### 5-1. はじめに

いよいよ画面を作成するところに入っていきます  
ルーティングは一言でいうならURLに対してどのような処理をさせるかということです

2023-11-10現在、今よりも10年以上前のWebアプリケーションでは、フォルダ構成を利用してそこにファイルを置いておいて、URLでそこにアクセスさせるやり方がほとんどだったかと思いますが、今日ではそういう手法はほとんど用いられなくなりました

今回は以下のようなURLで出力する例を作成します
```console
goods
├── input
│   ├── confirm
│   └── finish
└── search
```

これは、
http(s)://xxxxx.xx/goods/search  
→ 商品の一覧(検索)ページ

http(s)://xxxxx.xx/goods/input
→ 商品入力ページ

http(s)://xxxxx.xx/goods/confirm
→ 商品入力確認ページ

http(s)://xxxxx.xx/goods/finish
→ 商品入力完了ページ

とします  
以前ならこれを以下のディレクトリ構成

```console
{DocumentRootのディレクトリ}/goods
├── input
│   ├── index.php
│   ├── confirm.php
│   └── finish.php
└── search.php
```
のようにしていたかと思いますが、このようにはしません  

なぜこのようにしなくなったのかは以下はSymfonyのマニュアルですが、  
[第9章 - リンクとルーティングシステム](https://symfony.com/legacy/doc/gentle-introduction/1_4/ja/09-links-and-the-routing-system)  
を見てみるとよいかもしれません
(静的な)ファイルをあるディレクトリ配下に置いておくと、ブラウザでのアクセスでは気にならないかもしれませんが、wget等のコマンドやツールを用いてその配下にあるコンテンツを丸ごと取得なんてこともできてしまいます  
もちろんURLから取得されるべきものは置いておくべきではあるのですが、意図しないコンテンツやリソースの取得、あるいはツールで根こそぎ取得なんてのは許したくないなどの場合に、きちんとルーティングを設定しておくことは有用です(アップロードしてはいけないものまでアップロードしていたなんて事故は回避できます。そもそもDocumentRoot配下にファイルをアップロードはほとんどしないので)

### 5-2. 認証機能

前項でルーティングの基本的な考え方について述べましたが、認証機能について述べておきます  
詳しくは、  
[認証 10.x Laravel](https://readouble.com/laravel/10.x/ja/authentication.html)  
をご覧ください

認証機能は

* Breeze
* Larave/ui

などで、自動的に実装する方法もありますが、本ドキュメントでは自前で実装していきます  
実際にはこのあとの機能で細かい説明をするので、以下のコマンド等は今は分からなくても大丈夫です

先ずはユーザ作成用のControllerの作成をartisanで行います
実際にはController名は何でもいいですが、本ドキュメントではRegisterControllerという名前で作成します
```console
$ cd {Laravelインストールディレクトリ}
$ php artisan make:controller RegisterController

   INFO  Controller [app/Http/Controllers/RegisterController.php] created successfully. 
```
別にartisanで作成する必要はなく、自分で一からファイルを作成しても問題はないです  
認証部分のソースの全体像は以下のようになります(自分自身で作成したファイルのみ記述)

```console
{Laravelインストールディレクトリ}/
├── app
│   └── Http
│       └── Controllers
│            ├── AuthController.php
│            ├── Controller.php
│            ├── MenuController.php
│            └── RegisterController.php
├── resources
│   └── views
│       ├── auth
│       │   ├── login.blade.php
│       │   ├── register-finish.blade.php
│       │   └── register.blade.php
│       ├── layouts
│       │   └── main.blade.php
│       └── menu.blade.php
└── routes
    └── web.php
```

ソースを見るときは先ず{Laravelインストールディレクトリ}/routes/web.phpを見ると良いかと思います  
routes/web.php内にも記述していますが、
```php
Route::get('/register', [RegisterController::class, 'showRegister'])->name('showRegister');
```
は、GETで  
http(s)://xxx.xxx.xx/register  
にアクセスしたときに、RegisterController({Laravelインストールディレクトリ}/app/Http/Controllers/RegisterController.php)のshowRegisterメソッドを呼ぶという意味になります  
ルーティングとControllerのメソッドの対応を見れば繋がりが分かるかと思います  
あとは、ソースをご覧ください。細かいコメント等や説明をここに書くのはかえって分かりづらいと判断し、各ソースにコメントを付与していますので

## 6. 商品関連機能作成

ここからいよいよ商品関連機能を作成していきます  
しかしながら、前節の認証機能のソースを見ていただければおそらく基本的なアプリケーションの作成のイメージは掴めると思います  
全体のソースは以下になります(商品関連部分)

```console
{Laravelインストールディレクトリ}/
├── app
│   ├── Http
│   │   └── Controllers
│   │        └── Goods
│   │           └── GoodsController.php
│   ├── Repositories
│   │   ├── BaseRepository.php
│   │   └── GoodsRepository.php
│   ├── Rules
│   │   └── GoodsId.php
│   └── Services
│        ├── BaseService.php
│        └── GoodsService.php
├── resources
│   └── views
│       ├── goods
│       │   ├── confirm.blade.php
│       │   ├── finish.blade.php
│       │   ├── input.blade.php
│       │   ├── search_item.blade.php
│       │   └── search.blade.php
│       └── layouts
│           └── main.blade.php(認証で使用した場合と変更なし)
└── routes
    └── web.php
```

## 6-1. はじめに

本サンプルでは、設計としてよくない箇所があります  
しかしそれはバリデーションにFormRequestを使用したり、独自ルールを作成するなど実践でありそうなシチュエーションを加味して敢えて設計はそのような設計にしてあります  
この程度のものを作成するのに普通に作成してしまうと見どころもないですし、実践で使えそうなものとしては少ないかなと感じましたので  
また、本来はモデルを使用した方がスマートに作成できると思いますがクエリビルダの使用例を盛り込むために、敢えてRepositoryパターンとしています  
この辺りは、実際のプロジェクトで開発にあたるうちにどのようにするのがベターか段々と分かってくることでしょう  
それでは、以降より細かい部分でソース中に書ききれなかったことを記述しますが、よいLaravelライフを!!

## 6-2. Bladeテンプレートについて

正直bladeテンプレートについては、ソース中にすべてを記述するのは難しいと思ったので、ここでまとめて記述します  
詳細は、
[Bladeテンプレート 10.x Laravel](https://readouble.com/laravel/10.x/ja/blade.html)  

### 6-2-1. htmlspecialchars

PHPでhtmlspecialcharsを使用するのと同等にするのは、Laravelでは{{ \$変数名 }}とします  
また、htmlspecialcharsを使用しない場合は、{!! \$変数名 !!}とします  
textareaなどの値を、htmlspecialcharsを適用して、改行コードを&lt;br&gt;に変換したいような場合は、  
{!! nl2br(e($変数名)) !!}  
とできます  
eヘルパがhtmlspecialcharsを実行します  
{Laravelインストールディレクトリ}/vendor/laravel/framework/src/Illuminate/Support/helpers.php  
を見てみてください

### 6-2-2. extends

extendsディレクティブを利用して、テンプレートの継承が行えます  
親となるテンプレートの中で、@yield('{keyword}')となっている部分を継承側で
@section('{keyword}')〜@endsection  
の中身に置き換えることになります  

### 6-2-3. include

includeディレクティブを利用して、別のBladeテンプレートをそこに差し込むことができます  
この機能によって、部品化をしやすくなるかもしれません

