<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

     // Image モデルに、保存するカラムが fillable に追加されていないと保存できません。
     protected $fillable = [
        'board_id',
        'file_path',
    ];

    // boardモデルとリレーションシップ
    public function board()
    {
        return $this->belongsTo(Board::class);
    }
}
