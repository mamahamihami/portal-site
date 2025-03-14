<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LinkUrlController;
use App\Models\Board;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [BoardController::class, 'index'])->middleware('auth')->name('boards.index');

/*Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});*/

require __DIR__ . '/auth.php';

// ログイン関連のルート（LoginControllerを使用）
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');



//グループ化 
Route::middleware('auth')->group(function () {

    Route::resource('boards', BoardController::class);
    Route::delete('/images/delete', [ImageController::class, 'destroy'])->name('images.destroy');
    Route::get('/users/edit_password', [UserController::class, 'edit_password'])->name('edit_password');
    Route::put('/users/edit_password', action: [UserController::class, 'update_password'])->name('update_password');
    Route::post('favorites/{board_id}', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('favorites/{board_id}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
});
