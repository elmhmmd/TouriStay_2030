<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', fn () => view('admin.dashboard'))->name('admin.dashboard');
    Route::get('/tourist/dashboard', fn () => view('tourist.dashboard'))->name('tourist.dashboard');
    Route::get('/proprietaire/dashboard', fn () => view('proprietaire.dashboard'))->name('proprietaire.dashboard');
});

require __DIR__.'/auth.php';
