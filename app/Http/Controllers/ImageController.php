<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    //指定されたファイルを削除
    public function destroy(Request $request)
    {
        $image = Image::findOrFail($request->file_id);

        // ファイルの物理削除
        Storage::delete(str_replace('storage/uploads/', 'public/uploads/', $image->file_path));

        // データベースから削除
        $image->delete();

        return response()->json(['message' => 'ファイルを削除しました。']);
    }
}
