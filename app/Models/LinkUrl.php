<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkUrl extends Model
{
    use HasFactory;

    protected $fillable = [
        'link_icon_id',
        'name',
        'address',


    ];

    public function icon()
    {
        return $this->belongsTo(LinkIcon::class,'link_icon_id');
    }

}
