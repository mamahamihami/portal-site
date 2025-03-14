<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// ソート機能追加
use Kyslik\ColumnSortable\Sortable;

class Board extends Model
{
    // Kyslik/column-sortableライブラリー追加して Sortble追加
    use HasFactory, Sortable, SoftDeletes;

    // 論理削除カラム(deleted_at)が日付(Datetime型)であることを宣言
    protected $dates = ['deleted_at'];

    // Board モデルに、保存するカラムが fillable に追加されていないと保存できません。
    protected $fillable = [
        'user_id',
        'department_id',
        'user_name',
        'department_name',
        'title',
        'text',
    ];

    // 1つの投稿はは1人のユーザに属する
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 1つの投稿はは1人の部署に属する
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // imagesテーブルとリレーションシップ
    public function images()
    {
        return $this->hasMany(Image::class);
    }

    // お気に入り　多対多のリレーションシップ設定
    public function bd_users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
