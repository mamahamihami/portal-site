<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkIcon extends Model
{
    use HasFactory;
    protected $fillable = [
        'ikon_image',
    ];

    public function links()
    {
        return $this->hasMany(LinkUrl::class,'link_icon_id');
    }
}
