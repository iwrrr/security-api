<?php

namespace App\Helpers;

use Illuminate\Http\Request;

/**
 * Image Helper.
 */
class Image
{
    /**
     * Store image.
     */
    public static function store(Request $request, string $folder)
    {
        $file = $request->file('image');
        $now = date('Y/m/d H:i:s', time());
        $out = substr(hash('md5', $now), 0, 12);
        $fileName = $out . '.' . $file->getClientOriginalExtension();
        $file->move('uploads/' . $folder, $fileName);

        return ['file_name' => $fileName];
    }
}
