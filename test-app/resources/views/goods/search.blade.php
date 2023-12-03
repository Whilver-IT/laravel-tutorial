@extends('layouts.main')

@section('title'){{ $title }}@endsection

@section('before-script')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', (dcl) => {

            let deleteElems

            const EventUtil = {}
            EventUtil.removeEventListener = (event, elements, callback) => {
                return new Promise((resolve, reject) => {
                    elements.forEach((currentValue, currentIndex, obj) => {
                        currentValue.removeEventListener(event, callback)
                    })
                    resolve()
                })
            }
            const deleteElemClick = (e) => {
                EventUtil.removeEventListener('click', deleteElems, deleteElemClick).then(() => {
                    const id = e.target.dataset.id
                    if (confirm("商品コード:" + id + "\nを削除します\nよろしいですか!?")) {
                        axios({
                            'method': 'post',
                            'url': '{!! route('ajax.goods.delete') !!}' + location.search,
                            'data': {'id': id},
                        }).then(res => {
                            if (res.data.success) {
                                const goodsHtml = document.getElementById('goods')
                                if (goodsHtml) {
                                    goodsHtml.innerHTML = res.data.html
                                }
                            } else {
                                alert(res.data.message)
                            }
                        }).finally(() => {
                            addEvent()
                        })
                    }
                }).catch(() => {
                    addEvent()
                })
            }
            const addEvent = () => {
                deleteElems = [...document.getElementsByName('delete')]
                for (let node of deleteElems) {
                    node.addEventListener('click', deleteElemClick)
                }
            }
            addEvent()
        })
    </script>
@endsection

@section('contents')
    <div>
        <a href="{{ route('goods.input') }}">商品登録へ</a>
    </div>
    <form method="get" active="{{ route('goods.search') }}">
        ID:&ensp;<input type="text" id="id" name="id" value="{{ request()->query('id') }}"><br>
        検索ワード:&ensp;<input type="text" id="searchword" name="searchword" value="{{ request()->query('searchword') }}"><br>
        <button type="submit">検索</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>商品ID</th>
                <th>商品名</th>
                <th>商品説明</th>
                <th></th>
            </tr>
        </thead>
        @include('goods.search_item', ['goods' => $goods])
    </table>
@endsection