<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    // 論理削除カラム(deleted_at)が日付(Datetime型)であることを宣言
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'employee_number',
        'email',
        'password',
        'new_flag',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     * protected $hidden プロパティは、LaravelのEloquentモデルにおいて、JSONレスポンスや配列に変換する際に隠すべき属性（カラム）を指定するためのもの です。
     * この設定によって、password と remember_token の値は、モデルを JSON や配列に変換したときに出力されなくなる 仕組みです。
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     * protected $casts プロパティは、LaravelのEloquentモデル において、カラムのデータ型をキャスト（変換）するためのものです。
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'employee_number_at' => 'datetime',
    ];

    // 中間テーブル　多対多のリーレーションシップ設定
    public function dpm_departments()
    {
        return $this->belongsToMany(Department::class, 'department_user', 'user_id', 'department_id')->withTimestamps();
        // ->withTimestamps() を書くこと。これにより中間テーブルのタイムスタンプを更新することが出来る。
    }

    // dpm_departments のデータを保存する
    // sync() は、多対多のリレーションシップ（belongsToMany）において、関連するレコードの同期を取るために使用されます。これは、
    //中間テーブル（department_user）に保存する必要がある複数のレコードを一度に管理するために使います。sync() は、指定された部門を User に関連付けるために、中間テーブルに追加や削除を行います。
    public function setDpmDepartmentsAttribute($departments)
    {
        if (is_array($departments)) {
            $this->dpm_departments()->sync($departments);
        }
    }

    // 1人のユーザーは複数の投稿を作成できる
    public function boards()
    {
        return $this->hasMany(Board::class);
    }

    // お気に入り多対多のリレーションシップ
    public function bd_boards()
    {
        return $this->belongsToMany(Board::class)->withTimestamps();
    }
}
