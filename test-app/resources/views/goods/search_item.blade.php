@foreach ($goods as $item)
    @if ($loop->first)
        <tbody id="goods">
    @endif
    <tr>
        <td>{{ $item->id }}</td>
        <td>{{ $item->name }}</td>
        <td>{{ $item->explanation }}</td>
        <td><a href="{{ route('goods.input', ['id' => $item->id]) }}">編集</a>&ensp;<a name="delete" data-id="{{ $item->id }}" href="javascript:;">削除</a></td>
    </tr>
    @if ($loop->last)
        </tbody>
    @endif
@endforeach
