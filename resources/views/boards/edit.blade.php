{{--  投稿編集  boards/1/edit --}}

@extends('layouts.app')

@section('content')

    {{-- バリデーション表示 --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="container">
        <h1>全社共通通知 投稿編集</h1>

        <a
            href="{{ session('previous_board_id') ? route('boards.show', ['board' => session('previous_board_id')]) : route('boards.index') }}">
            前のページに戻る
        </a>



        <form action="{{ route('boards.update', $board) }}" method="POST" enctype="multipart/form-data">
            @csrf
            {{-- @method('PATCH') は、HTMLフォームの送信メソッドを PATCH にするための Blade ディレクティブ です。 --}}
            @method('PATCH')
            <div class="form-group">
                <label for="title">題名</label>
                <input type="text" name="title" id="title" class="form-control"
                    value="{{ old('title', $board->title) }}">
            </div>
            <div class="form-group">
                <label for="FlexTextarea">内容</label>
                <div class="FlexTextarea">
                    <div class="FlexTextarea__dummy" aria-hidden="true"></div>
                    <textarea name="text" id="FlexTextarea" class="form-control FlexTextarea__textarea">{{ old('text', $board->text) }}</textarea>
                </div>
            </div>


            <!-- ファイルをアップロード（PDF/Word/Excel/画像）複数可 -->
            <div class="form-group">
                <label for="file">ファイルをアップロード（PDF/Word/Excel/画像）複数可</label>

                {{-- 既存のファイル一覧を表示 --}}
                <div id="existing-files">
                    @foreach ($board->images as $image)
                        <div class="file-item">
                            <a href="{{ asset($image->file_path) }}" target="_blank">{{ basename($image->file_path) }}</a>
                            <button type="submit" class="btn btn-danger btn-sm remove-file"
                                data-file-id="{{ $image->id }}" onclick="return confirm('本当に削除しますか？')">削除</button>
                        </div>
                    @endforeach
                </div>

                {{-- 新しいファイルの入力を追加する場所 --}}
                <div id="file-inputs">
                    <input type="file" name="file[]" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,image/*"
                        class="form-control">
                </div>

                <button type="button" id="add-file" class="btn btn-success mt-2">ファイルを追加</button>
            </div>

            <div class="form-group">
                <label for="user_name">作成者</label>
                <input type="text" name="user_name" id="user_name" class="form-control" value="{{ Auth::user()->name }}"
                    readonly>
            </div>

            <div class="form-group">
                <label for="department_name">部署※発信部署を選択してください。</label>
                <select name="department_id" id="department_id" class="form-control">
                    {{-- Auth::user()->dpm_departments を使い、現在のログインユーザーが所属している部署を取得 --}}
                    @foreach (Auth::user()->dpm_departments as $department)
                        <option value="{{ $department->id }}"
                            {{ $board->department_id == $department->id ? 'selected' : '' }}>
                            {{ $department->department_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-success">更新</button>
        </form>

        {{-- image削除機能 --}}
        <script>
            $(document).ready(function() {
                $(".remove-file").click(function(event) {
                    event.preventDefault(); // フォームのデフォルト動作を防ぐ

                    if (!confirm("本当に削除しますか？")) {
                        return; // 「いいえ」を押したら処理を中断
                    }

                    let fileId = $(this).data("file-id");
                    let fileItem = $(this).closest(".file-item");

                    $.ajax({
                        url: "{{ route('images.destroy') }}", // ルートを適切に設定
                        type: "POST",
                        data: {
                            _method: "DELETE",
                            _token: "{{ csrf_token() }}",
                            file_id: fileId
                        },
                        success: function(response) {
                            fileItem.remove(); // 削除成功時に要素を削除
                        },
                        error: function(xhr) {
                            alert("ファイル削除に失敗しました。");
                        }
                    });
                });
            });
        </script>
        
        {{--　image追加ボタン --}}
        <script>
            document.getElementById('add-file').addEventListener('click', function() {
                let newInput = document.createElement('input');
                newInput.type = 'file';
                newInput.name = 'file[]';
                newInput.accept = '.pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png';
                newInput.classList.add('form-control', 'mt-2');

                // ファイル入力を追加する場所に追加
                document.getElementById('file-inputs').appendChild(newInput);
            });
        </script>

        {{-- textareaを文字数に合わせて広げる --}}
        <script>
            function flexTextarea(el) {
                const dummy = el.querySelector('.FlexTextarea__dummy');
                const textarea = el.querySelector('.FlexTextarea__textarea');

                // 入力時にdummyを更新
                textarea.addEventListener('input', e => {
                    dummy.textContent = e.target.value + '\u200b';
                });

                // 初期テキストがある場合にdummyを反映
                if (textarea.value.trim() !== '') {
                    dummy.textContent = textarea.value + '\u200b';
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.FlexTextarea').forEach(flexTextarea);
            });
        </script>
    </div>
@endsection
