{{-- 投稿詳細   boards/1 --}}
@extends('layouts.app')

@section('content')

@section('content')
    <div class="container">
        <h1>全社共通通知詳細</h1>
        @if (session('flash_message'))
            <p class="text-success">{{ session('flash_message') }}</p>
        @endif

        
        <a href="{{ session('current_page') ? route('boards.index') . '?page=' . session('current_page') : route('boards.show', ['board' => $board->id]) }}">
            前のページに戻る
        </a>



        <article>
            <div class="card mb-3">
                <div class="card-body">
                    <p>≪題名≫</p>
                    <h2 class="card-title fs-5">{{ $board->title }}</h2>
                    <p>≪詳細≫</p>
                    <h2 class="card-title fs-5">{!! nl2br(e($board->text)) !!}</h2>
                    <br>

                    <p>≪添付ファイル≫</p>
                    @if ($board->images->isNotEmpty())
                        @foreach ($board->images as $image)
                            @php
                                $filePath = asset($image->file_path);
                                $fileName = basename($image->file_path);
                                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                                $openInBrowserExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];
                            @endphp

                            <p style="display: inline-block; margin-right: 3px;">ファイル名:
                                @if (in_array($extension, $openInBrowserExtensions))
                                    <a href="{{ $filePath }}" target="_blank">{{ $fileName }}</a>
                                @else
                                    {{ $fileName }}
                                @endif
                            </p>

                            <a href="{{ $filePath }}" download="{{ $fileName }}" class="btn btn-success btn-sm"
                                style="display: inline-block;">ダウンロード</a>
                        @endforeach
                    @else
                        <p>画像はありません。</p>
                    @endif
                    <br>
                    <h2 class="card-title fs-5">投稿者: {{ $board->user_name }}</h2>
                    <h2 class="card-title fs-5">発信部署:{{ $board->department_name }}</h2>
                    <br>
                    @if ($board->user_id === Auth::id())
                        <div class="d-flex">
                            <a href="{{ route('boards.edit', $board) }}" class="btn btn-success me-1">編集</a>

                            <form action="{{ route('boards.destroy', $board) }}" method="POST"
                                onsubmit="return confirm('本当に削除してもよろしいですか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">削除</button>
                            </form>
                        </div>
                    @endif
                </div>
        </article>
    </div>
@endsection
