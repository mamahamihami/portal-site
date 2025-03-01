<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LinkUrl;


class LinkUrlController extends Controller
{
    public function index()
    {
        // LinkUrl のデータを取得し、icon とのリレーションも読み込む。
        $links = LinkUrl::with('icon')->get();

        return view('boards.index', compact('links'));
    }
}
