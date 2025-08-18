<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{


    public function loadfile($file_path)
    {
        $cacheTime = 60 * 60 * 48; // 2 ngày

        // Ngăn người dùng nhập path kiểu ../../
        $file_path = ltrim($file_path, '/');

        if (!Storage::disk('public')->exists($file_path)) {
            abort(404, 'File not found');
        }
        // Lấy path và mime type từ cache (hoặc tính toán)
        $fileInfo = Cache::remember("file_info:$file_path", $cacheTime, function () use ($file_path) {
            $path = Storage::disk('public')->path($file_path);
            $mime = mime_content_type($path);
            return [
                'path' => $path,
                'mime' => $mime,
            ];
        });

        return response()->file($fileInfo['path'], [
            'Content-Type' => $fileInfo['mime'],
            'Cache-Control' => 'public, max-age=' . $cacheTime . ', immutable',
        ]);


    }
}
