@extends('layouts.main')

@section('title'){{ $title }}@endsection

@section('contents')
    <h1>{{ $title }}</h1>
    <a href="{{ route('login') }}">ログイン画面</a>へ
@endsection