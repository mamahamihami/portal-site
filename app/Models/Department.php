<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    // 論理削除カラム(deleted_at)が日付(Datetime型)であることを宣言
    protected $dates = ['deleted_at'];


    // 中間テーブル　多対多のリーレーションシップ設定
    public function dpm_users()
    {
        return $this->belongsToMany(User::class, 'department_user',  'department_id', 'user_id')->withTimestamps();
        // ->withTimestamps() を書くこと。これにより中間テーブルのタイムスタンプを更新することが出来る。
    }

    // 1つの部署名はは複数の投稿を作成できる
    public function boards()
    {
        return $this->hasMany(Board::class);
    }
}
