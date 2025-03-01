{{-- 新規投稿 　boards/create --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>全社共通通知 作成画面</h1>

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

        <a href="{{ route('boards.index') }}">TOPへ戻る</a>

        {{-- formの送信先URL指定 --}}
        <form action="{{ route('boards.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="title">題名</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}">
            </div>
            <div class="form-group">
                <label for="FlexTextarea">内容</label>
                <div class="FlexTextarea">
                    <div class="FlexTextarea__dummy" aria-hidden="true"></div>
                    <textarea name="text" id="FlexTextarea" class="form-control FlexTextarea__textarea">{{ old('text') }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="file">ファイルをアップロード（PDF/Word/Excel/画像）複数可</label>
                {{-- ファイルの入力を追加する場所 --}}
                <div id="file-inputs">
                    <input type="file" name="file[]" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,image/*"
                        class="form-control">
                </div>
                <button type="button" id="add-file" class="btn btn-success mt-2">ファイルを追加</button>
            </div>

            <div class="form-group">
                {{-- value="{{ Auth::user()->name }}" 現在のユーザー名を自動入力 readonly を追加して編集不可にする --}}
                <label for="user_name">作成者</label>
                <input type="text" name="user_name" id="user_name" class="form-control" value="{{ Auth::user()->name }}"
                    readonly>
            </div>
            <div class="form-group">
                <label for="department_name">部署※発信部署を選択してください。</label>
                <select name="department_id" id="department_id" class="form-control">
                    {{-- Auth::user()->dpm_departments を使い、現在のログインユーザーが所属している部署を取得 --}}
                    @foreach (Auth::user()->dpm_departments as $department)
                        <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-success">登録</button>
        </form>

    </div>

    {{-- JavaScript を追加 --}}
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

@endsection
