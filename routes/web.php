<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
    return redirect()->route('products.index');
});
Route::controller(ProductController::class)->group(function () {
    Route::get('/', 'index')->name('products.index');
    Route::post('save', 'save')->name('products.save');
    Route::post('update', 'update')->name('products.update');
});
