@extends('layouts.main')

@section('title'){{ $title }}@endsection

@section('contents')
    <h1>メニュー</h1>
    <a href="{{ route('logout') }}">ログアウト</a>
@endsection