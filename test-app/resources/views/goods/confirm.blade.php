@extends('layouts.main')

@section('title'){{ $title }}@endsection

@section('contents')
    id:&ensp;{{ request('id') }}<br>
    名称:&ensp;{{ request('name') }}<br>
    説明:&ensp;{{ request('explanation') }}<br>
    <form method="post" action="{{ route('goods.input') }}">
        @foreach (request()->input() as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <button type="submit" formaction="route('goods.input')">戻る</button><br>
        <button type="submit" formaction="route('goods.finish')">登録</button>
    </form>
@endsection