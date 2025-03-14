{{-- 投稿一覧初期画面  boards --}}
@extends('layouts.app')


@section('content')
    @if (session('success'))
        <p class="text-success">
            {{ session('success') }}</p>
    @endif

    @if (session('error_message'))
        <p class="text-danger">{{ session('error_message') }}</p>
    @endif

    @if (session('flash_message'))
        <p class="text-success">{{ session('flash_message') }}</p>
    @endif


    <div class="container mt-4">

        <div class="d-flex">
            <h2>Link</h2>
        </div>

        @if (isset($links) && $links->isNotEmpty())
            <div class="d-flex flex-wrap ms-5">

                @foreach ($links as $link)
                    <div class="m-2">
                        <button class="btn btn-index  me-2">
                            <a class="index-button" href="{{ $link->address }}" target="_blank">
                                <img src="{{ asset('storage/' . ($link->icon ? $link->icon->ikon_image : 'icons/default_1740837631.png')) }}"
                                    alt="Icon" style="width: 30px; height: 30px;">
                                {{ $link->name }}
                            </a>
                        </button>
                    </div>
                @endforeach
            </div>
        @else
            <p>データがありません。</p>
        @endif
        <br>

        <div class="d-flex">
            <h2>全社共通通知</h2>
        </div>
        <!-- ヘッダー部分 -->

        <div class="d-flex justify-content-start align-items-center bg-success text-white p-2 rounded">

            <button class="btn btn-index  me-2">
                <a class="index-button" href="{{ route('boards.create') }}">新規登録</a>
            </button>

            <button class="btn {{ request('createid') ? 'btn-warning' : 'btn-index' }} me-2">
                <a href="{{ route('boards.index', array_merge(request()->except('page'), ['createid' => request('createid') ? null : 1])) }}"
                   class="index-button">作成一覧</a>
            </button>


            <button class="btn  {{ request('favorites') ? 'btn-warning' : 'btn-index' }} me-2">
                <a href="{{ route('boards.index', array_merge(request()->except('page'), ['favorites' => request('favorites') ? null : 1])) }}"
                    class="index-button">
                    <i class="fa-solid fa-star me-1"></i>
                    お気に入り
                </a>
            </button>

            <form method="GET" action="{{ route('boards.index') }}" class="d-flex align-items-center py-2 ms-auto">
                {{-- department の値を保持する --}}
                <input type="hidden" name="department" value="{{ request('department') }}">


                {{-- flex-grow-1 … 入力欄が適切に伸縮 --}}
                <div class="ms-0 me-1 flex-grow-1">
                    <input type="text" name="keyword" class="form-control" placeholder="検索"
                        value="{{ request('keyword') }}">
                </div>

                <div class="d-flex align-items-center  me-1">
                    <label class="index-label me-1">開始日</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}">
                </div>

                <div class="d-flex align-items-center me-1">
                    <label class="index-label me-1">終了日</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}">
                </div>

                <button class="btn btn-index  me-1">検索</button>
                <a href="{{ route('boards.index') }}" class="btn btn-index me-1">Reset</a>
            </form>
        </div>

        <div class="border rounded">
            <div class="row  g-1">
                <!-- サイドメニュー -->
                <div class="col-md-2">
                    {{-- style="max-height: 400px; overflow-y: auto;"　でスクロールバー --}}
                    <div class="list-group" style="max-height: 400px; overflow-y: auto;">
                        <a href="{{ route('boards.index') }}"
                            class="text-center list-group-item list-group-item-success {{ request('department') ? '' : 'active' }}">全部署</a>
                        @foreach ($departments as $department)
                            @if (!empty($department->id))
                                <a href="{{ route('boards.index', ['department' => $department->id]) }}"
                                    class="text-center list-group-item list-group-item-success {{ request('department') == $department->id ? 'active' : '' }}">{{ $department->department_name }}</a>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- テーブル部分 -->
                <div class="col-md-10">
                    <table class="table  table-bordered table-striped  rounded">
                        <thead class="table-success">
                            <tr class="text-center">
                                <th>NEW</th>
                                <th><i class="fa-solid fa-star"></i></th>
                                <th>登録日</th>
                                <th>題名</th>
                                <th>登録者</th>
                                <th>部署名</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($boards as $board)
                                <tr>
                                    <td class="text-danger fw-bold text-center">
                                        {{--  now()->subWeek() で1週間前の日付を取得 --}}
                                        @if ($board->updated_at >= now()->subWeek())
                                            NEW
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $isFavorite = Auth::user()->bd_boards->contains($board->id);
                                        @endphp

                                        @if ($isFavorite)
                                            <a href="#" class="index-tbody text-warning"
                                                onclick="event.preventDefault(); document.getElementById('favorites-destroy-form-{{ $board->id }}').submit();"><i
                                                    class="fa-solid fa-star"></i></a>

                                            <form id="favorites-destroy-form-{{ $board->id }}"
                                                action="{{ route('favorites.destroy', $board->id) }}" method="POST"
                                                class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @else
                                            <a href="#" class="index-tbody text-secondary"
                                                onclick="event.preventDefault(); document.getElementById('favorites-store-form-{{ $board->id }}').submit();"><i
                                                    class="fa-regular fa-star"></i></a>

                                            <form id="favorites-store-form-{{ $board->id }}"
                                                action="{{ route('favorites.store', $board->id) }}" method="POST"
                                                class="d-none">
                                                @csrf
                                            </form>
                                        @endif
                                    </td>

                                    <td class="text-center"><a href="{{ route('boards.show', $board) }}"
                                            class="index-tbody">{{ $board->updated_at->format('Y年m月d日') }}</a>
                                    </td>
                                    <td><a href="{{ route('boards.show', $board) }}"
                                            class="index-tbody">{{ $board->title }}</a></td>
                                    <td class="text-center"><a href="{{ route('boards.show', $board) }}"
                                            class="index-tbody">{{ $board->user_name }}</a></td>
                                    <td class="text-center"><a href="{{ route('boards.show', $board) }}"
                                            class="index-tbody">{{ $board->department_name }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- ページネーション -->
                    <div class="d-flex flex-row-reverse me-2">
                        {{ $boards->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
