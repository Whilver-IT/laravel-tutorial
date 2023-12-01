@extends('layouts.main')

@section('title'){{ $title }}@endsection

@section('contents')
    <form method="post" action="{{ route('goods.confirm') }}">
        @csrf
        {{-- 
            セッションの値がある場合、valueに値は入れない
            但し表示は行う(編集不可)
        --}}
        ID(必須): <input type="{{ $mode == 'new' ? 'text' : 'hidden' }}" id="id" name="id" value="{{ $mode == 'new' ? old('id') : '' }}">{{ $mode == 'new' ? '' : session('goods.id') }}<br>
        @if ($errors->has('id'))
            @foreach ($errors->get('id') as $message)
                {{ $message }}<br>
            @endforeach
        @endif
        名前(必須): <input type="text" name="name" value="{{ old('name') }}"><br>
        @if ($errors->has('name'))
            @foreach ($errors->get('name') as $message)
                {{ $message }}<br>
            @endforeach
        @endif
        説明: <input type="text" name="explanation" value="{{ old('explanation') }}"><br>
        <button type="submit">確認</button>
    </form>
@endsection