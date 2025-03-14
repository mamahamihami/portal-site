<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    // ログインフォームの表示
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // ログイン処理
    public function login(Request $request)
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'employee_number' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // 社員番号とパスワードで認証
        $credentials = [
            'employee_number' => $request->employee_number,
            'password' => $request->password,
        ];

        $remember = $request->filled('remember'); // チェックボックスの値を取得

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if ($user->new_flag) {
                return redirect()->route('edit_password');
            }

            return redirect()->route('boards.index'); // ログイン後のリダイレクト先
        }

        return redirect()->back()->withErrors(['employee_number' => '社員番号またはパスワードが間違っています。'])->withInput();
    }

    // ログアウト処理
    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
