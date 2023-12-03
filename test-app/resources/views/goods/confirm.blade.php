@extends('layouts.main')

@section('title'){{ $title }}@endsection

@section('contents')
    id:&ensp;{{ request()->input('id', session('goods.id')) }}<br>
    名称:&ensp;{{ request()->input('name') }}<br>
    説明:&ensp;{{ request()->input('explanation') }}<br>
    <form method="post" action="{{ route('goods.input') }}">
        @foreach (request()->input() as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <button type="submit" formaction="{{ route('goods.input') }}">戻る</button>&ensp;<button type="submit" formaction="{{ route('goods.finish') }}">登録</button>
    </form>
@endsection