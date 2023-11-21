@extends('layouts.main')

@section('title'){{ $title }}@endsection

@section('contents')
    <form method="post">
        @csrf
        ID: <br>
        @if ($errors->has('id'))
            @foreach ($errors->get('id') as $message)
                {{ $message }}<br>
            @endforeach
        @endif
        名前: <input type="text" name="name" value="{{ old('name') }}"><br>
        @if ($errors->has('name'))
            @foreach ($errors->get('name') as $message)
                {{ $message }}<br>
            @endforeach
        @endif
        説明: <input type="text" name="explanation" value="{{ old('explanation') }}"><br>
        <button type="submit">確認</button>
    </form>
    {{ $id }}<br>
    {{ $method }}
@endsection