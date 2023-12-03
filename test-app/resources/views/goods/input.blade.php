@extends('layouts.main')

@section('title'){{ $title }}@endsection

@section('contents')
    <div>
        <a href="{{ route('goods.search') }}">商品検索へ</a>
    </div>
    <form method="post" action="{{ route('goods.confirm') }}">
        @csrf
        @if ($errors->has('warning'))
            @foreach ($errors->get('warning') as $message)
                {{ $message }}<br>
            @endforeach
        @endif
        ID(必須): @if ($mode == 'new')
            <input type="text" id="id" name="id" value="{{ old('id') }}"><br>
        @else
            {{ session('goods.id') }}<br>
        @endif
        @if ($errors->has('id'))
            @foreach ($errors->get('id') as $message)
                {{ $message }}<br>
            @endforeach
        @endif
        名前(必須): <input type="text" name="name" value="{{ old('name', request()->input('name')) }}"><br>
        @if ($errors->has('name'))
            @foreach ($errors->get('name') as $message)
                {{ $message }}<br>
            @endforeach
        @endif
        説明: <input type="text" name="explanation" value="{{ old('explanation', request()->input('explanation')) }}"><br>
        <button type="submit">確認</button>
    </form>
@endsection