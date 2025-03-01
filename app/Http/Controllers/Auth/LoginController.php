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
            return redirect()->back()->withErrors($validator)->withInput(); //withInput()入力した（employee_number）を保持　パスワードはクリア
        }

        // 社員番号とパスワードで認証
        $user = User::where('employee_number', $request->employee_number)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()->back()->withErrors(['employee_number' => '社員番号またはパスワードが間違っています。'])->withInput();
        }

        // ログイン処理
        Auth::login($user);

        //return view('index'); //test

        return redirect()->route('boards.index'); // ログイン後のリダイレクト先
    }

    // ログアウト処理
    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
