@extends('layouts.main')

@section('title'){{ $title }}@endsection

@section('contents')
    <h1>ログイン</h1>
    <form method="post">
        @csrf
        メールアドレス： <input type="text" name="email" value="{{ old('email') }}"><br>
        @if ($errors->has('email'))
            @foreach ($errors->get('email') as $message)
                {{ $message }}<br>
            @endforeach
        @endif
        パスワード： <input type="password" name="password"><br>
        @if ($errors->has('password'))
            @foreach ($errors->get('password') as $message)
                {{ $message }}<br>
            @endforeach
        @endif
        <button type="submit">ログイン</button>
        @if ($errors->has('message'))
            @foreach ($errors->get('message') as $message)
                <br>{{ $message }}
            @endforeach
        @endif
    </form>
    <a href="{{ route('showRegister') }}">ユーザ登録画面</a>へ
@endsection