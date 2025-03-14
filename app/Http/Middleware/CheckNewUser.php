<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CheckNewUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // ユーザーが新規ユーザーであり、かつ現在のページがパスワード変更ページでない場合
        if ($user && $user->new_flag && !$request->is('users/edit_password')) {
            // 新規ユーザーの場合、パスワード変更ページにリダイレクト
            return redirect()->route('edit_password');
        }

        return $next($request);
    }
}
