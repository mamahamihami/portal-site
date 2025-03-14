<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Department;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Models\LinkUrl;
use Illuminate\Support\Facades\Auth;
// Storage は Laravel のファイル管理（ファイルの保存・取得・削除など）を扱うためのファサード です。
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PostRequest;



class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // 一覧ページ
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $departmentId = $request->department; // 部署フィルタリング用
        $createId = $request->has('createid'); //  作成者フィルタリング
        $favoritesOnly = $request->has('favorites'); // お気に入りフィルタリング
        $links = LinkUrl::with('icon')->get(); //ハイパーリンク


        // edit.blade.php からの戻り判定用（session の値を取得）
        $boardId = session('previous_board_id');

        // index.blade.php に戻ったらセッションを削除
        session()->forget('previous_board_id');

        // 日付検索：開始日（date_from）と終了日（date_to）が両方とも入力されている場合
        $query = Board::query();

        // 部署フィルタリング（選択されている場合）
        if ($departmentId !== null) {
            $query->where('department_id', $departmentId);
        }

        // キーワード検索：部分一致検索（キーワードがある場合）
        if ($keyword !== null) {
            $query->where(function ($query) use ($keyword) {
                $query->where('user_name', 'like', "%{$keyword}%")
                    ->orWhere('department_name', 'like', "%{$keyword}%")
                    ->orWhere('title', 'like', "%{$keyword}%")
                    ->orWhere('text', 'like', "%{$keyword}%");
            });
        }

        // 日付検索：開始日（date_from）と終了日（date_to）の範囲検索（キーワードがなくても日付検索を行う）
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('updated_at', [$request->date_from, $request->date_to]);
        }

        // 作成者のみ表示フィルター
        if ($createId) {
            $user = Auth::user();
            $query->whereIn('id', $user->boards->pluck('id'));
        }


        // お気に入りのみ表示のフィルター
        if ($favoritesOnly) {
            $user = Auth::user();
            $query->whereIn('id', $user->bd_boards->pluck('id'));
        }


        // 並び替え（updated_atで降順）
        $boards = $query->orderBy('updated_at', 'desc')->sortable()->paginate(10);

        // 選択中の部署情報を取得
        $department = $departmentId ? Department::find($departmentId) : null;

        // 現在のページ番号をセッションに保存
        session(['current_page' => $request->get('page', 1)]);

        // 全部署のリストを取得
        $departments = Department::all();

        // compact() を使い、取得したデータをビュー (boards.index) に渡す
        return view('boards.index', compact('boards', 'department', 'departments', 'keyword', 'links'));
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // 作成ページ
    public function create()
    {
        // でログインユーザーの部署情報をビューに渡す

        $user = Auth::user();
        $departments = $user->dpm_departments; // ユーザーの所属部署を取得

        return view('boards.create', compact('departments'));
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // 作成機能
    public function store(PostRequest $request)
    {

        $user = Auth::user(); // ログインユーザー取得

        // ユーザーが選択した部署のIDを取得
        // dpm_departments() は User モデルのリレーションを利用して、
        // where('id', $request->department_id) を使い、ユーザーが選択した部署（department_id）を絞り込む。
        $department = $user->dpm_departments()->where('departments.id', $request->department_id)->first();



        // データーベースに保存
        $board = Board::create([
            'user_id' => $user->id,
            // ? は「もし $department が存在する（null でない）なら」という意味です。
            // $department->id はその部署の id を取得しています。
            // : の後ろは「もし $department が存在しないなら」という意味で、null を設定しています。
            'department_id' => $department ? $department->id : null, // 部署IDをセット
            'department_name' => $department ? $department->department_name : '未設定', // 部署名をセット
            'user_name' => $user->name, // ログインユーザーの名前をセット
            'title' => $request->title,
            'text' => $request->text,
        ]);

        // ファイルがアップロードされた場合
        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                if ($file->isValid()) { // ファイルが正しくアップロードされたか確認
                    // ファイル名をユニークにする
                    date_default_timezone_set('Asia/Tokyo');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('public/files', $fileName);

                    // `images` テーブルに保存
                    Image::create([
                        'board_id' => $board->id,
                        'file_path' => 'storage/files/' . $fileName,
                    ]);
                }
            }
        }


        return redirect()->route('boards.index')->with('success', '投稿が作成されました！');
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Board  $board
     * @return \Illuminate\Http\Response
     */

    // 詳細ページ
    public function show(Board $board, Request $request)
    {
        if (session()->has('from_edit')) {
            session()->forget('previous_board_id');  // セッションクリア
            session(['from_edit' => true]);          // 編集後のフラグ設定
        }


        // 現在のページ番号を保持
        if ($request->has('page')) {
            session(['current_page' => $request->page]);
        }

        return view('boards.show', compact('board'));
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Board  $board
     * @return \Illuminate\Http\Response
     */
    //  編集ページ
    // Laravel では ルートモデルバインディング により、$board には URL の id に対応する投稿データ が自動的に渡される。
    public function edit(Board $board)
    {
        // user_id が現在のログインユーザーと一致するかチェック
        if ($board->user_id !== Auth::id()) {
            return redirect()->route('boards.index')->with('error_message', '不正なアクセスです。');
        }

        // ログインユーザーを取得
        $user = Auth::user();

        // ユーザーの所属部署を取得
        $departments = $user->dpm_departments;


        // 現在の board ID をセッションに保存　戻るボタン
        session(['previous_board_id' => $board->id]);

        return view('boards.edit', compact('board', 'departments'));
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Board  $board
     * @return \Illuminate\Http\Response
     */
    //  更新機能
    public function update(PostRequest $request, Board $board)
    {
        // user_id が現在のログインユーザーと一致するかチェック
        if ($board->user_id !== Auth::id()) {
            return redirect()->route('boards.index')->with('error_message', '不正なアクセスです。');
        }

        // ログインユーザーを取得
        $user = Auth::user();

        // ユーザーが選択した部署のIDを取得
        $department = $user->dpm_departments()->where('departments.id', $request->department_id)->first();

        // 各フィールドを更新
        $board->title = $request->input('title');
        $board->text = $request->input('text');
        $board->department_id = $department ? $department->id : null;
        $board->department_name = $department ? $department->department_name : '未設定';
        $board->save();

        // ファイルがアップロードされた場合
        if ($request->hasFile('file')) {
            // 新しい画像を保存
            foreach ($request->file('file') as $file) {
                if ($file->isValid()) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('public/files', $fileName);

                    Image::create([
                        'board_id' => $board->id,
                        'file_path' => 'storage/files/' . $fileName,
                    ]);
                }
            }
        }

        return redirect()->route('boards.show', $board)->with('flash_message', '投稿を更新しました！');
    }

    // 削除機能
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function destroy(Board $board)
    {
        if ($board->user_id !== Auth::id()) {
            return redirect()->route('board.index')->with('error_message', '不正なアクセスです。');
        }
        $board->delete();

        return redirect()->route('boards.index', $board)->with('flash_message', '投稿を削除しました。');
    }
}
