<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    // パスワード変更

    //パスワード変更画面を表示する　edit_passwordアクション 
    public function edit_password()
    {
        return view('users.edit_password');
    }

    public function update_password(Request $request)
    {
        $validatedData = $request->validate([
            // パスワードと確認パスワードが一致すること
            'password' => 'required|confirmed',
        ]);

        $user = Auth::user();

        // 現在のパスワードと新しいパスワードが同じか確認
        if (Hash::check($request->input('password'), $user->password)) {
            // 変更前と同じパスワードの場合
            return back()->withErrors(['password' => '新しいパスワードは現在のパスワードと異なる必要があります。']);
        }

        // 新しいパスワードを設定
        $user->password = bcrypt($request->input('password'));
        $user->update();

        // 成功メッセージを表示してリダイレクト
        return to_route('boards.index')->with('flash_message', 'パスワードを更新しました。');
    }
}
