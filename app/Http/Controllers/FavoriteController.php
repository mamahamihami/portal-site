<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function store($board_id)
    {
        Auth::user()->bd_boards()->attach($board_id);

        return back();
    }

    public function destroy($board_id)
    {
        Auth::user()->bd_boards()->detach($board_id);

        return back();
    }
}
