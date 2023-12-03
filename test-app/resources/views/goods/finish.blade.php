@extends('layouts.main')

@section('title'){{ $title }}@endsection

@section('contents')
    商品の{{ $mode }}が完了しました<br>
    <a href="{{ route('goods.input') }}">商品登録</a><br>
    <a href="{{ route('goods.search') }}">商品一覧</a>
@endsection