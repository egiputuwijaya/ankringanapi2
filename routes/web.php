<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/file/{folder}/{filename}', function ($folder, $filename) {
    // Cari file di storage/app/public
    $paths = [
        storage_path('app/public/' . $folder . '/' . $filename),
        public_path('storage/' . $folder . '/' . $filename),
        base_path('../public_html/storage/' . $folder . '/' . $filename)
    ];
    
    $foundPath = null;
    foreach($paths as $p) {
        if(file_exists($p)) {
            $foundPath = $p;
            break;
        }
    }
    
    if (!$foundPath) {
        abort(404, 'File tidak ditemukan.');
    }
    
    return response()->file($foundPath, [
        'Content-Type' => \Illuminate\Support\Facades\File::mimeType($foundPath),
        'Cache-Control' => 'public, max-age=86400'
    ]);
});
