@extends('layouts.main')

@section('title'){{ $title }}@endsection

@section('contents')
    <h1>{{ $title }}</h1>
    <form action="" method="post">
        @csrf
        名前：&ensp;<input type="text" id="name" name="name" value="{{ old('name') }}"><br>
        @if ($errors->has('name'))
            @foreach ($errors->get('name') as $message)
                {{ $message }}<br><br>
            @endforeach
        @endif
        メールアドレス：&ensp;<input type="text" id="email" name="email" value="{{ old('email') }}"><br>
        @if ($errors->has('email'))
            @foreach ($errors->get('email') as $message)
                {{ $message }}<br><br>
            @endforeach
        @endif
        パスワード：&ensp;<input type="password" id="password" name="password"><br>
        @if ($errors->has('password'))
            @foreach ($errors->get('password') as $message)
                {{ $message }}<br><br>
            @endforeach
        @endif
        <button type="submit">送信</button>
    </form>
    <a href="{{ route('login') }}">ログイン画面</a>へ<br>
@endsection
