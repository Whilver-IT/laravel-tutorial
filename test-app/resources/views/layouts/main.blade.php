<!doctype html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        @if (!empty($useAjax) && $useAjax)
            {{--
                Laravelでaxiosを使用時にPOSTを行う場合のおまじない
                これがないとLaravel側でエラーとなる
                https://readouble.com/laravel/10.x/ja/csrf.html
            --}}
            <meta name="csrf-token" content="{{ csrf_token() }}">
        @endif
        <title>@yield('title')</title>
        @yield('css')
        @yield('before-script')
    </head>
    <body>
        @yield('contents')
    </body>
</html>