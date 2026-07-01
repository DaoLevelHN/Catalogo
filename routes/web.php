<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogController;

Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/categoria/{category}', [CatalogController::class, 'category'])->name('catalog.category');
Route::get('/categoria/{category}/{subcategory}', [CatalogController::class, 'subcategory'])->name('catalog.subcategory');
Route::get('/producto/{category}/{subcategory}/{product}', [CatalogController::class, 'product'])->name('catalog.product');

// Search API
Route::get('/api/search', [CatalogController::class, 'search'])->name('catalog.search');

// Serve real images from resources/imagenes
Route::get('/serve-image/{path}', function($path) {
    $fullPath = resource_path('imagenes') . '/' . $path;
    if (file_exists($fullPath)) {
        return response()->file($fullPath);
    }
    abort(404);
})->where('path', '.*')->name('serve.image');
