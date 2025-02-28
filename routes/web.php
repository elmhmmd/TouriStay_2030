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
    Route::get('/admin/dashboard', [App\Http\Controllers\AnnonceController::class, 'index'])->name('admin.dashboard');
    Route::get('/tourist/dashboard', fn () => view('tourist.dashboard'))->name('tourist.dashboard');
    Route::get('/proprietaire/dashboard', [App\Http\Controllers\AnnonceController::class, 'index'])->name('proprietaire.dashboard');
    Route::resource('annonces', App\Http\Controllers\AnnonceController::class)->only(['store', 'edit', 'update', 'destroy']);
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/types', [App\Http\Controllers\TypeDeLogementController::class, 'store'])->name('types.store');
    Route::delete('/types/{id}', [App\Http\Controllers\TypeDeLogementController::class, 'destroy'])->name('types.destroy');

    Route::get('/tourist/listings', [App\Http\Controllers\TouristController::class, 'index'])->name('tourist.listings');
    Route::get('/tourist/book/{id}', [App\Http\Controllers\TouristController::class, 'book'])->name('tourist.book');
    Route::post('/tourist/book', [App\Http\Controllers\TouristController::class, 'storeBooking'])->name('tourist.book');
    Route::delete('/tourist/bookings/{id}', [App\Http\Controllers\TouristController::class, 'cancelBooking'])->name('tourist.bookings.cancel');
});

require __DIR__.'/auth.php';
